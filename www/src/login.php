<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav>
    <div class='logo'><img src='images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'></div>
    <ul>
      <li><a href="../index.php">Accueil</a></li>
      <li><a href="#">Disponibilités</a></li>
      <li><a href="mes-reservations.php">Mes réservations</a></li>
      <li><a href="login.php">Connexion / Enregistrement</a></li>
    </ul>
  </nav>

  <!-- CONTENU -->
  <div class="login-container">
    <div class="login-box">
      <h2>Connexion</h2>
      <?php

      /* 
      Vérifier qu'on a une session active de la page enregistrement
      En gros, après avoir créé son compte, une session s'active et on est redirigé vers 
      la page login. Mais je veux afficher le message "Connectez-vous avec votre nouveau compte créé"
      seulement quand c'est le cas.

      Je ne peux pas utiliser if (isset($_GET['from']) && $_GET['from'] === 'login.php')
      parce que j'utilise déjà Location: login.php?from=login.php dans la page 
      mes-reservations.php
      */

      if ((session_status() === PHP_SESSION_ACTIVE) /*&& ($_SESSION["email"] != "")*/) {
        print($_SESSION["email"]);

        echo "
          <h4>(Connectez-vous avec votre nouveau compte créé)</h4>
          <form action='check_auth.php' method='post'>
            <input type='email' name='username' placeholder='Nom utilisateur/email' required>
            <input type='password' name='password' placeholder='Mot de passe' required>
            <button type='submit'>Se connecter</button>
          </form>
          <p>Problème de connexion ? <a href='#'>Réinitialisez votre compte</a></p>
        ";
      } else {
        if (isset($_GET['from']) && $_GET['from'] === 'login.php') {
          echo "
          <h4>(Connectez-vous pour voir vos reservations)</h4>
          <form action='check_auth.php' method='post'>
            <input type='email' name='username' placeholder='Adresse email' required>
            <input type='password' name='password' placeholder='Mot de passe' required>
            <button type='submit'>Se connecter</button>
            </form>
          <p>Pas encore de compte ? <a href='enregistrement.php'>Enregistrez-vous</a></p>
        ";
        } else {
          echo "
          <form action='check_auth.php' method='post'>
            <input type='email' name='username' placeholder='Adresse email' required>
            <input type='password' name='password' placeholder='Mot de passe' required>
            <button type='submit'>Se connecter</button>
            </form>
          <p>Pas encore de compte ? <a href='enregistrement.php'>Enregistrez-vous</a></p>
        ";
        }
      }

      ?>
    </div>
  </div>

  <footer>
    <p>&copy; INF1763-01 - Autonme 2025</p>
  </footer>

</body>

</html>