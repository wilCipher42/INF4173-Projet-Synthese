<?php
class User {
  /* Check username and password. */
  public function authenticate($email, $password) {

    $db = getDB();
    $hash_password = hash('sha256', $password);
    $stmt = $db -> prepare("SELECT uid, auth_seed_value FROM users WHERE email=:email AND password=:hash_password");  
    $stmt -> bindParam("email", $email, PDO::PARAM_STR) ;
    $stmt -> bindParam("hash_password", $hash_password, PDO::PARAM_STR) ;
    $stmt -> execute();
    $count = $stmt -> rowCount();
    $data = $stmt -> fetch(PDO::FETCH_OBJ);
    $db = null;
  
    if ($count == 1) {
      $_SESSION['uid'] = $data -> uid;
      $_SESSION['secret'] = $data -> auth_seed_value;
      return true;
    }
    else {
      return false;
    }
  }


  /* Sign in user to the application. */
  public function login($email, $password, $secret) {

    $db = getDB();
    $hash_password = hash('sha256', $password);
    $stmt = $db -> prepare("SELECT uid FROM users WHERE email=:email AND password=:hash_password");  
    $stmt -> bindParam("email", $email, PDO::PARAM_STR) ;
    $stmt -> bindParam("hash_password", $hash_password, PDO::PARAM_STR) ;
    $stmt -> execute();
    $count = $stmt -> rowCount();
    $data = $stmt -> fetch(PDO::FETCH_OBJ);
    $db = null;
  
    if ($count) {
      $_SESSION['uid'] = $data -> uid;
      $_SESSION['google_auth_code'] = $google_auth_code;
      return true;
    }
    else {
      return false;
    }
  }

  /* Check if user exists.  */
  public function exists($email) {
    try {
      $db = getDB();
      $st = $db-> prepare("SELECT uid FROM users WHERE email=:email");  
      $st-> bindParam("email", $email, PDO::PARAM_STR);
      $st-> execute();
      $count = $st -> rowCount();
      
      return ($count == 1);
    } 
    catch(PDOException $e) {
      echo '{"error":{"text":' . $e->getMessage() . '}}'; 
    }
  }

  /* User Registration */
  public function register($first_name, $last_name, $email, $password, $auth_seed_value) {
    //try {
      $db = getDB();
      
      $stmt = $db -> prepare("INSERT INTO users(first_name, last_name, email, password, auth_seed_value) " .
                             "VALUES (:first_name, :last_name, :email, :hash_password, :auth_seed_value)");  
      $stmt -> bindParam("first_name", $first_name, PDO::PARAM_STR);
      $stmt -> bindParam("last_name", $last_name, PDO::PARAM_STR) ;
      $hash_password = hash('sha256', $password);
      $stmt -> bindParam("hash_password", $hash_password, PDO::PARAM_STR) ;
      $stmt -> bindParam("email", $email, PDO::PARAM_STR) ;
      $stmt -> bindParam("auth_seed_value", $auth_seed_value, PDO::PARAM_STR) ;
      $stmt -> execute();
      $uid = $db -> lastInsertId();
      $db = null;
      $_SESSION['uid'] = $uid;
    //} 
    //catch(PDOException $e) {
     // echo '{"error":{"text":' . $e->getMessage() . '}}'; 
   // }
  }

  /* User Details */
  public function getDetails($uid) {
    try {
      $db = getDB();
      $stmt = $db->prepare("SELECT first_name, last_name, email, auth_seed_value FROM users WHERE uid=:uid");  
      $stmt->bindParam("uid", $uid,PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_OBJ);
      return $data;
    }
    catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
  }
}
?>
