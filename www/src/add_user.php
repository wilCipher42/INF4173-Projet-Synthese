<?php
session_start();
include 'libs/app_fcts.php';

$_SESSION['name'] = $_POST['username'] ?? "";
$_SESSION['email'] = $_POST['email'] ?? "";
$_SESSION['password'] = $_POST['password'] ?? "";

$secret = "";

// Ajout de l'usager dans la base de données.
// Il faut d'abord calculer certaines valeurs.
// Établissement au hasard d'une valeur "salt" qui sera ajoutée au mot de passe.
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$salt = '';
for ($i = 0; $i < 20; $i++) {
    $salt .= $characters[random_int(0, $charactersLength - 1)];
}
//echo $salt;   //Juste pour vérifier le salt au besoiin

// Calcul du code de hachage.
$hash = hash('sha256', $_SESSION['password'] . $salt);
//echo $hash;  //voir la valeur de hash code.

// Établir la connexion vers MySQL.
//$conn = new mysqli('db', 'auth_db_admin', 'N@ruto1995', 'auth_db');
$conn = getDBConnection();
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

/*
// Preparer la commande pour eviter injection sql
$sql = $conn->prepare("INSERT INTO users (name, email, hash, salt, secret) VALUES (?, ?, ?, ?, ?)");

// Vérifier si la préparation a échoué
if (!$sql) {
    echo "<p>DEBUG ERREUR PREPARE: " . $conn->error . "</p>";
    exit;
}

// lier les differentes variables
$sql->bind_param("sssss", $_SESSION['name'], $_SESSION['email'], $hash, $salt, $secret);

if (!$sql->execute()) {
    echo "<p>DEBUG ERREUR EXECUTE: " . $sql->error . "</p>";
    exit;
}
*/


// Création de la commande SQL pour insérer l'usager dans la base de données.
$sql = "INSERT INTO users (name, email, hash, salt, secret) VALUES ('" . $_SESSION['name'] . "', '" .
       $_SESSION['email'] . "', '" . $hash . "', '" . $salt . "', NULL)";

// Debug : afficher la commande SQL
//echo "<p>DEBUG SQL: $sql</p>";

// Execution de la commande en utilisant la connexion vers MySQL.
if ($conn->query($sql) === FALSE) {
    echo "<p>DEBUG ERREUR: " . $conn->error . "</p>";
    exit;
} 

//sleep(1);
Header("Refresh:2; url=login.php");

echo "<h2>Compte créé avec succès</h2>";
echo "<h3>Veuillez vous connecter avec le nouveau compte créé</h3>";

?>
