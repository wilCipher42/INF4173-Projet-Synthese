<?php
require __DIR__ . '/libs/vendor/autoload.php';

include 'libs/app_fcts.php';

session_start();

if (!isset($_SESSION["logged"]) || $_SESSION["logged"] !== 1) {
    header("Refresh: 1.5; url=login.php");
    exit;
}

$name = $_SESSION['name'] ?? "User";

$client = new Google_Client();
$client->setAuthConfig('libs/credentials.json');    # Identifié l'application (projet google cloud pour l'api)
$client->addScope(Google_Service_Calendar::CALENDAR);   # on veut avoir acces au calendrier
//$client->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callback.php');   # Où on envoie le token apres l'autorisation
$client->setRedirectUri('http://localhost:8200/src/callback.php');
$client->setAccessType('offline');
//$client->setPrompt('consent'); // IMPORTANT pour obtenir le refresh_token la première fois

//$tokenPath = '../token_' . $name . '.json';
$tokenPath = '../data/token_' . $name . '.json';   //pour serveur

try {
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);  #On prend le token pour s'authentifié aupres de google

        // Si le token a expiré, on le renouvelle automatiquement
        if ($client->isAccessTokenExpired()) {
            /*
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      */
            header('Location: ' . $client->createAuthUrl());
            exit;
        }
    } else {    //Si le token n'existe pas
        header('Location: ' . $client->createAuthUrl());
        exit;
    }

    $service = new Google_Service_Calendar($client);    # service ou classe qui permet d'interagir avec le calendrier
    $calendarId = 'primary';
} catch (Exception $e) {
    echo 'Erreur Google Client : ' . $e->getMessage();
    exit;
}

$data = [];

//if (isset($_GET['json'])) {
// utiliser les paramètres FullCalendar si disponibles
$timeMin = '2000-01-01T00:00:00Z';
$timeMax = '2050-01-01T00:00:00Z';

$events = $service->events->listEvents($calendarId, [
    'timeMin' => $timeMin,
    'timeMax' => $timeMax,
    'singleEvents' => true,
    'orderBy' => 'startTime'
]);


foreach ($events->getItems() as $event) {
    $start = $event->start->dateTime ?? $event->start->date;
    $end = $event->end->dateTime ?? $event->end->date;

    /*
    $data[] = [
        'id' => $event->getId() ?: 'Unknown',
        'title' => $event->getSummary() ?: 'Occupé',
        'start' => $start,
        'end' => $end,
        "coach" => "Chelsea",
        "location" => "En ligne"
    ];
    */
    
    // Filtrer seulement les événements qui contiennent "coach" dans le titre
    if (strpos(strtolower($event->getSummary()), 'coach') !== false) {
      $data[] = [
        'id' => $event->getId() ?: 'Unknown',
        'title' => $event->getSummary() ?: 'Occupé',
        'start' => $start,
        'end' => $end,
        "coach" => "Chelsea",
        "location" => "En ligne"
      ];
    }
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes Reservations</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav>
        <div class='logo'><img src='images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'></div>
        <ul>
            <li><a href='../index.php'>Accueil</a></li>
            <li><a href='disponibilites.php'>Disponibilités</a></li>
            <li><a href='#'>Mes réservations</a></li>
            <li><a href='logout.php'>Déconnexion</a></li>
            <?php echo "<li><a href='#'>😎 Bonjour " . htmlspecialchars($name) . "</a></li>" ?>
        </ul>
    </nav>
    </br>
    <h2 style="text-align:center;">Mes Reservations</h2></br>
    <div style="border: 1.0px solid rgb(0,0,0); padding: 10.0px; background-color: #ffbe63e0;">
        <p style="text-align: center; color:black;">Seules les réservations effectuées via notre application web s'afficheront ici.</p>
        <p style="text-align: center;"><strong>Attention: </strong>Si vous voulez modifier une rencontre, veuillez d'abord l'annuler puis faire une nouvelle reservation.</p>
    </div></br>
    <div class="reservations-container">
        <?php foreach ($data as $r): ?>
            <div class="reservation-card">
                <div class="reservation-content">
                    <h3><?= htmlspecialchars($r["title"]) ?></h3>
                    <div >
                        <p><?= "De " .date('d/m/Y - H:i', strtotime($r["start"])) ?> à <?= date('d/m/Y - H:i', strtotime($r["end"])) ?></p>
                        <div class="details-box" style="display: none;">
                            <p>Avec <?= $r["coach"] ?> </p>
                            <p>Lieu: <?= $r["location"] ?></p>
                            <p>Durée: <?= gmdate("H\hi", strtotime($r["end"]) - strtotime($r["start"])) ?></p>
                        </div>
                        
                    </div>
                </div>

                <div class="reservation-actions">
                    <button class="details-btn" onclick="voirDetails(this)">Voir plus de détails</button>
                    <button class="cancel-btn"
                        data-id="<?= $r["id"] ?>"
                        data-start="<?= $r["start"] ?>"
                        data-end="<?= $r["end"] ?>">Annuler
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="js/script.js"></script>
</body>
</html>