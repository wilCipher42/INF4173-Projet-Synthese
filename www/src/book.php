<?php
session_start();
header('Content-Type: application/json');

require __DIR__ . '/libs/vendor/autoload.php';
include 'libs/app_fcts.php';

$name = $_SESSION['name'] ?? 'Utilisateur inconnu';

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

//$clientUsr->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callback.php');   //pour serveur
$clientUsr->setRedirectUri('http://localhost:8200/src/callback.php');    //Pour test local

try {

    //$tokenPath = '../token_' . $name . '.json'; //pour test local
    $tokenPath = '../data/token_' . $name . '.json';   //pour serveur

    //$group18Token = '../token_Group18.json';
    $group18Token = '../data/token_Group18.json';    //pour serveur

    if (!file_exists($tokenPath)) {
        http_response_code(401);
        echo json_encode(['error' => 'Token Google manquant']);
        header('Location: ' . $clientUsr->createAuthUrl());
        exit;
    } elseif (!file_exists($group18Token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Token Google manquant']);
        header('Location: ' . $clientUsr->createAuthUrl());
        exit;
    }

    //Access token Users
    $accessTokenUsr = json_decode(file_get_contents($tokenPath), true);
    $clientUsr->setAccessToken($accessTokenUsr);

    //Access token Group18
    $accessTokenGr18 = json_decode(file_get_contents($group18Token), true);
    $clientGr18->setAccessToken($accessTokenGr18);

    // Initialisation des services Google Calendar
    $serviceGr18 = new Google_Service_Calendar($clientGr18);
    $serviceUsr = new Google_Service_Calendar($clientUsr);
    $calendarId = 'primary';

    // Récupération des données du POST JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $start = $data['start'] ?? null;
    $end = $data['end'] ?? null;

    if (!$start || !$end) {
        http_response_code(400);
        echo json_encode(['error' => 'Données de réservation invalides.']);
        exit;
    }

    // Création de l'événement dans les deux calendriers
    //Remarque: On change juste les summary
    $eventGr18 = new Google_Service_Calendar_Event([
        'summary' => 'Rendez-vous avec ' . $name,
        'start' => ['dateTime' => $start, 'timeZone' => 'America/Toronto'],
        'end' => ['dateTime' => $end, 'timeZone' => 'America/Toronto'],
    ]);

    $eventUsr = new Google_Service_Calendar_Event([
        'summary' => 'Rendez-vous avec la coach de vie',
        'start' => ['dateTime' => $start, 'timeZone' => 'America/Toronto'],
        'end' => ['dateTime' => $end, 'timeZone' => 'America/Toronto'],
    ]);

    // Insertion dans les calendriers
    $eventGr18 = $serviceGr18->events->insert($calendarId, $eventGr18);
    $eventUsr = $serviceUsr->events->insert($calendarId, $eventUsr);

    // Récupération de l’ID de l’événement
    $id_gr18 = $eventGr18->getId();
    $_SESSION["id_gr18"] = $id_gr18;

    // Connexion MySQL
    //$conn = new mysqli('db', 'auth_db_admin', 'N@ruto1995', 'auth_db');
    $conn = getDBConnection();
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion MySQL: " . $conn->connect_error);
    }

    /*
    // Préparation SQL sécurisée (évite les injections)
    $stmt = $conn->prepare("INSERT INTO reservations (id_gr18, username, startD, endD) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Erreur de préparation SQL: " . $conn->error);
    }
    $stmt->bind_param("ssss", $id_gr18, $name, $start, $end);

    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'insertion: " . $stmt->error);
    }
    
    $stmt->close();
    */

    $stmt = "INSERT INTO reservations (id_gr18, username, startD, endD) VALUES ('$id_gr18', '$name', '$start', '$end')";

    if ($conn->query($stmt) === false) {
        throw new Exception("Erreur lors de l'insertion: " . $conn->error);
    }

    $conn->close();

    echo json_encode([
        'message' => 'Votre rendez-vous a été ajouté avec succès. Un message de confirmation vous sera envoyé par mail!',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur Google API : ' . $e->getMessage()]);
}


?>
