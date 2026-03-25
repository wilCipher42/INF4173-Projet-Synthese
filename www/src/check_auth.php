<?php

//Chargement des librairies
include 'libs/app_fcts.php';
include 'libs/config.php';
include 'libs/User.class.php';
include 'libs/GoogleAuthenticator.class.php';

session_start();

//Recuperation des variables dont j' aurais besoin
$_SESSION["email"] = $_POST["username"] ?? "";
$_SESSION["password"] = $_POST["password"] ?? "";

$courriel = $_SESSION["email"] ?? "";

//Connection a la base de donnees
//$dbConnect = new mysqli('db', 'auth_db_admin', 'N@ruto1995', 'auth_db');
$dbConnect = getDBConnection();
if ($dbConnect->connect_error) {
	die("Erreur de connexion: " . $conn->connect_error);
}

/*
//preparation de la requete sécurisée
$stmt = $dbConnect->prepare("SELECT email, user_id, name, secret, salt, hash FROM users WHERE email=?");

//lier la variable $courriel a notre requete (s = string)
$stmt->bind_param("s", $courriel);

//executer la commande et recuperer le resultat
$stmt->execute();
$result = $stmt->get_result();
*/ 


//recuperation d'enregistrements
$selectRecords = "SELECT email, user_id, name, secret, salt, hash FROM users WHERE email='$courriel'";

//execution des commandes sql et debugging
$result = $dbConnect->query($selectRecords);
if ($result === FALSE) {
	echo "<p>DEBUG ERREUR: " . $dbConnect->error . "</p>";
}


if ($result->num_rows <= 0) {	//si la commande retourne 0 resultats
	echo "<h1 style='text-align:center;'>Utilisateur introuvable / mot de passe incorrect.</h1>";
	echo '<a href="login.php">Veuillez reessayer</a>';

	//ajouter cette tentative dans la journalisation
	addLogEntry("Echec de l'authentification : utilisateur non trouvé", $courriel, "Login fail");
} else {	//sinon si l'utilisateur est trouve

	$row = $result->fetch_assoc();	//mettre les resultats sous forme de tableau
	//convertir les valeurs en chaines de caracteres
	$secret = (string) $row['secret'];
	$salt = (string) $row['salt'];
	$hash = (string) $row['hash'];
	$name = (string) $row['name'];
	$userID = (string) $row['user_id'];
	$email = (string) $row['email'];

	$passDB = $hash;	//on utilise seulement le hash parce que dans la page add_user on a deja ajoute le salt puis ensuite on a hasher le tout. Et donc le mot de passe c'est le hash dans la db

	/* On va recuperer le mot de passe 
		entre par l'utilisateur et y ajouter le salt et le hash qui correspondent
		a l'utilisateur dans la db puis comparer avec les resultats de notre
		requete
		*/
	$passUsr = $_SESSION["password"] . $salt;
	$passUsr = hash('sha256', $passUsr);
	//$passUsr = $_SESSION["password"] . $passUsr;

	/*
		echo $passUsr;
		echo "</br> ";
		echo $passDB;
		*/

	if ($passDB != $passUsr) {	//on compare les deux hash
		echo "<h1 style='text-align:center;'>Ooops ! Utilisateur introuvable / mot de passe incorrect.</h1>";
		echo '<a href="login.php">Veuillez reessayer</a>';

		//ajouter cette tentative dans la journalisation
		addLogEntry("Echec de l'authentification : mot de passe incorrect", $courriel, "Login fail");
	}
	/*
		else {
			echo "<h2 style='color:purple; text-align:center;'>Yay !! Utilisateur identifie !, verification du second facteur en cours...</h2>";


			//recuperation de la cle secrete stockee dans la db
			$ga = new GoogleAuthenticator();

			$current_code = $ga->getCode($secret);

			if ($_SESSION["pin"] != $current_code) {
				echo "<h2 style='color:purple; text-align:center;'>uhm!! La verification a echoue ::) !</h2>";
				echo "</br><a href='login.php'>Veuillez ressayer !</a>";

				//ajouter cette tentative dans la journalisation
				addLogEntry("Echec de l'authentification : second facteur invalide", $courriel, "Login fail");
			} 
			*/ else {
		//ajouter cette connexion dans la journalisation
		addLogEntry("Authentification reussie", $courriel, "Login success");

		//maintenant que nous sommes authentifies, on va initialiser les variables de la session
		$_SESSION["logged"] = 1;
		$_SESSION["name"] = $name;
		$_SESSION["user_id"] = $userID;
		$_SESSION["email"] = $email;

		//redirection vers la page home
		header("Refresh:2; url=../index.php");
		exit();
	}
}

$dbConnect->close();	//fermer la base de donnees
?>

<!DOCTYPE html>
<html>

<head>
	<title>Verification des identifiants</title>
</head>

<body>

</body>

</html>
