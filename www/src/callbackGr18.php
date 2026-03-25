<?php
/*
Configuration du callback seulement pour le token du group18
Compte pour lequel on veut afficher le calendrier en permanence
*/
require __DIR__ . '/libs/vendor/autoload.php';

session_start();

$name = $_SESSION['name'] ?? "unkwon";

$client = new Google_Client();
$client->setAuthConfig('libs/credentials.json');
$client->setRedirectUri('http://localhost:8200/src/callbackGr18.php');
//$client->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callbackGr18.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('consent'); // IMPORTANT pour obtenir le refresh_token la première fois

//$group18Token = '../token_Group18.json';    //server local
$group18Token = '../data/token_Group18.json';

if (!isset($_GET['code'])) {
    header('Location: ' . $client->createAuthUrl());
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    echo "Erreur lors de la récupération du token : " . htmlspecialchars($token['error_description'] ?? $token['error']);
    exit;
}

file_put_contents($group18Token, json_encode($token, JSON_PRETTY_PRINT));


header('Location: ../index.php');
exit;

?>
