<?php

/* DATABASE CONFIGURATION */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'demo_app_admin');
define('DB_PASSWORD', 'secret_password');
define('DB_DATABASE', 'demoappdb');

function getDB() 
{
  $dbhost = DB_SERVER;
  $dbuser = DB_USERNAME;
  $dbpass = DB_PASSWORD;
  $dbname = DB_DATABASE;
  try {
    $dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
    $dbConnection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
  }
  catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
  }
}
?>
