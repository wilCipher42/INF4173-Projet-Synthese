<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link rel="stylesheet" href="css/style.css">
  
</head>

<body>

  <!-- NAVBAR -->
  <nav>
    <div class='logo'><img src='images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'></div>
    <ul>
      <li><a href="../index.php">Accueil</a></li>
      <li><a href="disponibilites.php">Disponibilités</a></li>
      <li><a href="mes-reservations.php">Mes réservations</a></li>
      <li><a href="#">Connexion / Enregistrement</a></li>
    </ul>
  </nav>

  <!-- CONTENU -->
  <div class="login-container">
    <div class="register-box">
      <h2>Créer un compte</h2>
      <form id="registerForm" action="add_user.php" method="post">
        <input type="text" name="username" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
        <button type="submit">S'enregistrer</button>
      </form>
      <p id="orErrorMsg"></p>
      <!-- <p id="orErrorMsg">Déjà un compte ? <a href="login.html">Connectez-vous</a></p> -->
    </div>
  </div>

  <script src = "js/script.js"></script>
  
  <footer>
    <p>&copy; INF1763-01 - Autonme 2025</p>
  </footer>

</body>

</html>