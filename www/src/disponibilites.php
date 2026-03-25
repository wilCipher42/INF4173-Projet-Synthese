<?php
session_start();

require __DIR__ . '/libs/vendor/autoload.php';

include 'libs/app_fcts.php';

# Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
if (!isset($_SESSION["logged"]) || $_SESSION["logged"] != 1) {
    addLogEntry("Attempt to get to Disponibilités page without authentication.", NULL, "Bypass attempt");
    header("Refresh: 1.5; url=login.php");
    exit();
} else {
    $name = $_SESSION['name'];
    $name = htmlspecialchars($name);
}

/*
Ici on va juste gérer le token du compte group18.inf1763@gmail.com
On veut que son token n'expire jamais vu qu'on veut afficher son calendrier en tout temps
*/

//Gerer deux tokens. Un pour le Group18 et l'autre pour les users
$clients = [];

for ($i = 0; $i < 2; $i++) {
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/libs/credentials.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);
    $client->setAccessType('offline');

    $clients[$i] = $client;
}

$clientGr18 = $clients[0];
$clientUsr = $clients[1];

//$clientGr18->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callbackGr18.php');  //Pour serveur
$clientGr18->setRedirectUri('http://localhost:8200/src/callbackGr18.php');  //Pour test local
$clientGr18->setPrompt('consent');                                                     // IMPORTANT pour obtenir le refresh_token la première fois

//$clientUsr->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callback.php');   //pour serveur
$clientUsr->setRedirectUri('http://localhost:8200/src/callback.php');    //Pour test local

//Chemin d'acces des tokens
//$group18Token = '../token_Group18.json';            //pour test local
//$group18Token = '/data/token_Group18.json';        //pour serveur
$group18Token = '../data/token_Group18.json';        //pour serveur
//$tokenPath = '../token_' . $name . '.json'; //pour test local
$tokenPath = '../data/token_' . $name . '.json';   //pour serveur

//Acceder au token du group18
if (file_exists($group18Token)) {
    $accessTokenGroup18 = json_decode(file_get_contents($group18Token), true);
    $clientGr18->setAccessToken($accessTokenGroup18);  #On prend le token pour s'authentifié aupres de google

    // Si le token a expiré, on le rafraîchit
    if ($clientGr18->isAccessTokenExpired()) {
        if ($clientGr18->getRefreshToken()) {
            $newAccessToken = $clientGr18->fetchAccessTokenWithRefreshToken($clientGr18->getRefreshToken());
            $accessTokenGroup18 = array_merge($accessTokenGroup18, $newAccessToken);
            file_put_contents($group18Token, json_encode($accessTokenGroup18));
        } else {
            // Aucun refresh token — on doit repasser par l'autorisation manuelle
            header('Location: ' . $clientGr18->createAuthUrl());
            exit;
        }
    }
} else {
    header('Location: ' . $clientGr18->createAuthUrl());
    exit;
}

//Acceder au token des users
if (file_exists($tokenPath)) {
    $accessUsrToken = json_decode(file_get_contents($tokenPath), true);
    $clientUsr->setAccessToken($accessUsrToken);  #On prend le token pour s'authentifié aupres de google

    // Si le token a expiré, on se reconnecte manuellement
    if ($clientUsr->isAccessTokenExpired()) {
        header('Location: ' . $clientUsr->createAuthUrl());
        /*
        if ($client->getRefreshToken()) {
            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $accessTokenGroup18 = array_merge($accessTokenGroup18, $newAccessToken);
            file_put_contents($group18Token, json_encode($accessTokenGroup18));
        } else {
            // Aucun refresh token — on doit repasser par l'autorisation manuelle
            header('Location: ' . $client->createAuthUrl());
            exit;
        }
        */
    }
} else {
    header('Location: ' . $clientUsr->createAuthUrl());
    exit;
}

//Configurer le Google_Service_Calendar pour afficher le calendrier
$service = new Google_Service_Calendar($clientGr18);    # service ou classe qui permet d'interagir avec le calendrier
$calendarId = 'primary';

if (isset($_GET['json'])) {
    // utiliser les paramètres FullCalendar si disponibles
    $timeMin = isset($_GET['start']) ? $_GET['start'] : date('c', strtotime('-1 week'));
    $timeMax = isset($_GET['end']) ? $_GET['end'] : date('c', strtotime('+1 week'));

    $events = $service->events->listEvents($calendarId, [
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'singleEvents' => true,
        'orderBy' => 'startTime'
    ]);

    $data = [];
    foreach ($events->getItems() as $event) {
        $start = $event->start->dateTime ?? $event->start->date;
        $end = $event->end->dateTime ?? $event->end->date;
        $data[] = [
            'id' => $event->getId(),
            'title' => 'Occupé', //$event->getSummary() ?: 'Occupé',
            'start' => $start,
            'end' => $end,
            'color' => '#451570ff'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes disponibilités</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>

<body>
    <nav>
        <div class='logo'><img src='images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'></div>

        <ul>
            <li><a href='../index.php'>Accueil</a></li>
            <li><a href='#'>Disponibilités</a></li>
            <li><a href='mes-reservations.php'>Mes réservations</a></li>
            <li><a href='logout.php'>Déconnexion</a></li>
            <?php echo "<li><a href='#'>😎 Bonjour " . htmlspecialchars($name) . "</a></li>" ?>
        </ul>
    </nav>
    </br>
    <h2 style="text-align:center;">Mes disponibilités</h2>
    <div id="calendar"></div>
    <script src="js/script.js"></script>
</body>

</html>
