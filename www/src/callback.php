<?php
require __DIR__ . '/libs/vendor/autoload.php';

session_start();

$name = $_SESSION['name'] ?? "Utilisateur";

$client = new Google_Client();
$client->setAuthConfig('libs/credentials.json');
//$client->setRedirectUri('https://willserver.tail36095f.ts.net:4443/src/callback.php'); //Pour serveur
$client->setRedirectUri('http://localhost:8200/src/callback.php');   # Où on envoie le token apres l'autorisation
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');

//$usrToken = '../token_' .$name. '.json';    //pour test local
$usrToken = '../data/token_' .$name. '.json';    //pour serveur

if (!isset($_GET['code'])) {
    header('Location: ' . $client->createAuthUrl());
    exit;
}

if (isset($token['error'])) {
    echo "Erreur lors de la récupération du token : " . htmlspecialchars($token['error_description'] ?? $token['error']);
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
file_put_contents($usrToken, json_encode($token, JSON_PRETTY_PRINT));      

header('Location: ../index.php');
exit;