<?php

function getDBConnection() {
  //return new mysqli('localhost', 'auth_db_admin', 'password123', 'auth_db');
  return new mysqli('db', 'auth_db_admin', 'N@ruto1995', 'auth_db');
}

function addLogEntry($mainLog, $suppLog, $logType) {
  $log_conn = getDBConnection();
  $ip_address = getUserIpAddr();
  $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'UNDEFINED';

  $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : -1;
  $mainLog = str_replace("'", "''", (string)$mainLog);
  $suppLog = str_replace("'", "''", (string)$suppLog);

  $sql = "INSERT INTO logs(ip_address, user_agent, log_dt, user_id, main_log, supp_log, log_type) VALUES ('" .
         $ip_address . "', '" . $user_agent . "', CURRENT_TIMESTAMP, " . $user_id . ", '" . $mainLog . "', '" . $suppLog . "', '" . $logType . "')";

  // echo $sql;   // À utiliser pour voir la commande SQL.

  $log_conn->query($sql);

  $log_conn->close();
}

function getUserIpAddr(){ 
  if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
    $ip = $_SERVER['HTTP_CLIENT_IP']; 
  }
  elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
  }
  else{ 
    $ip = $_SERVER['REMOTE_ADDR']; 
  }
  
  return $ip; 
}

?>
