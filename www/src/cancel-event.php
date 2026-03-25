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

$clientGr18->setRedirectUri('http://localhost:8888/src/callbackGr18.php');  //Pour test local

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
    $eventId = $data['id'] ?? null;

    if (!$eventId || !$start || !$end) {
        http_response_code(400);
        echo json_encode(['error' => 'Données de réservation invalides.']);
        exit;
    }

    // Établir la connexion vers MySQL.
    //$conn = new mysqli('db', 'auth_db_admin', 'N@ruto1995', 'auth_db');
    $conn = getDBConnection();
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // Requête préparée
    $stmt = $conn->prepare("SELECT id_gr18 FROM reservations WHERE username = ? AND startD = ? AND endD = ?" );
    $stmt->bind_param("sss", $name, $start, $end);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $id_gr18 = $row['id_gr18'] ?? null;

    $stmt->close();
    $conn->close();

    // Annulation de l'événement dans les deux calendriers
    $serviceGr18->events->delete($calendarId, $id_gr18);
    $serviceUsr->events->delete($calendarId, $eventId);

    echo json_encode([
        'message' => 'Votre rendez-vous a été annulé avec succès. Un message de confirmation vous sera envoyé par mail!',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur Google API : ' . $e->getMessage()]);
}


?>
