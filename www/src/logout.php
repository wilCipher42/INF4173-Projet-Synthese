<?php
session_start();

if (isset($_SESSION['name'])) {
	$name = $_SESSION['name'];
	$tokenUsr = "../data/token_" .$name. ".json";

	if (file_exists($tokenUsr))
		unlink($tokenUsr);
}

session_unset();
session_destroy();

header("Refresh:1.5; url=../index.php");
exit;
?>
