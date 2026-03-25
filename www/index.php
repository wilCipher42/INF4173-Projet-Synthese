<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="src/css/style.css">
  <title>Coach de vie</title>
</head>

<body>
  <!-- NAVBAR <div class='logo'>Groupe 18</div> -->
  <?php
  if (isset($_SESSION["logged"]) && $_SESSION["logged"] == 1) {
    $name = $_SESSION["name"];
    $userID = $_SESSION["user_id"];
    $email = $_SESSION["email"];
    echo "
    <nav>
      <div class='logo'>
         <img src='src/images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'>
      </div>
      <ul>
        <li><a href='#' class='active'>Accueil</a></li>
        <li><a href='src/disponibilites.php'>Disponibilités</a></li>
        <li><a href='src/mes-reservations.php'>Mes réservations</a></li>
        <li><a href='src/logout.php'>Déconnexion</a></li>
        <li><a href='#'>😎 Bonjour " . $name . "</a></li>
      </ul>
    </nav>
    ";
  } else {
    echo "
    <nav>
      <div class='logo'><img src='src/images/group18Logo.webp' alt='Logo Groupe 18' style='height:30px; width:auto;'></div>
      <ul>
        <li><a href='#' class='active'>Accueil</a></li>
        <li><a href='src/disponibilites.php'>Disponibilités</a></li>
        <li><a href='src/mes-reservations.php'>Mes réservations</a></li>
        <li><a href='src/login.php'>Connexion / Enregistrement</a></li>
      </ul>
    </nav>
    ";
  }
  ?>

  <!-- HERO -->
  <div class="hero-wrapper">
    <div class="container">
      <!-- Image -->
      <div class="left"></div>

      <!-- Texte et contenu -->
      <div class="right">
        <p class="hero-eyebrow">Coach de vie personnel et de carrière</p>
        <h1 class="hero-title">Je suis Chelsea</h1>
        <p class="hero-subtitle">
          Je vous aide à trouver et à forger votre propre chemin.
        </p>

        <div class="hero-rating">
          <span class="hero-stars">★★★★★</span>
          <span class="hero-rating-text">4.9/5 · 32 avis de clients</span>
        </div>

        <a href="src/disponibilites.php" class="btn-main">Prenez rdv</a>

        <div class="cards">
          <div class="card">
            <h3>Coach de vie personnel</h3>
            <p>
              Un accompagnement bienveillant pour vous aider à mieux vous connaître,
              surmonter vos blocages et atteindre un meilleur équilibre dans votre vie quotidienne.
            </p>
            <a href="src/disponibilites.php" class="btn-card">Commençons dès aujourd’hui !</a>
          </div>

          <div class="card">
            <h3>Développement de carrière</h3>
            <p>
              Un soutien personnalisé pour définir vos objectifs professionnels,
              renforcer vos compétences et bâtir une carrière épanouissante et alignée à vos valeurs.
            </p>
            <a href="src/disponibilites.php" class="btn-card">Commençons dès aujourd’hui !</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== SECTION AVIS ===== -->
  <section class="reviews">
    <div class="reviews-inner">
      <header class="reviews-header">
        <h2 class="reviews-title">Ce que les clients disent de Chelsea</h2>
        <p class="reviews-subtitle">
          Quelques témoignages de personnes qui ont suivi un accompagnement.
        </p>
      </header>

      <div class="reviews-grid">
        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar">AM</div>
            <div>
              <p class="review-name">Amélie M.</p>
              <p class="review-meta">Étudiante en informatique</p>
            </div>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">
            Chelsea m'a aidée à clarifier mes objectifs de carrière et à préparer mes
            entretiens de stage. J'ai enfin décroché un poste qui me motive.
          </p>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar">YS</div>
            <div>
              <p class="review-name">Yassine S.</p>
              <p class="review-meta">Jeune diplômé</p>
            </div>
          </div>
          <div class="review-stars">★★★★☆</div>
          <p class="review-text">
            On a revu mon CV, mon LinkedIn et ma façon de me présenter. J'étais
            beaucoup plus à l'aise pendant les entrevues.
          </p>
        </article>

        <article class="review-card">
          <div class="review-header">
            <div class="review-avatar">CL</div>
            <div>
              <p class="review-name">Camille L.</p>
              <p class="review-meta">Reconversion de carrière</p>
            </div>
          </div>
          <div class="review-stars">★★★★★</div>
          <p class="review-text">
            J'étais perdue entre plusieurs options. Les séances avec Chelsea m'ont
            permis de choisir un plan réaliste étape par étape.
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- ===== SECTION SERVICES ===== -->
  <section class="services">
    <div class="service">
      <div class="icon">🎯</div>
      <h3>Définition d’objectifs</h3>
      <p>Un accompagnement pour définir vos objectifs et créer un plan clair pour les atteindre.</p>
    </div>

    <div class="service">
      <div class="icon">👥</div>
      <h3>Coaching relationnel</h3>
      <p>Améliorez vos relations personnelles et développez des liens plus sains et équilibrés.</p>
    </div>

    <div class="service">
      <div class="icon">💙</div>
      <h3>Gestion du stress</h3>
      <p>Des stratégies concrètes pour réduire le stress et retrouver un équilibre de vie.</p>
    </div>

    <div class="service">
      <div class="icon">💼</div>
      <h3>Conseils de carrière</h3>
      <p>Des conseils pratiques pour faire progresser votre carrière et atteindre vos ambitions.</p>
    </div>
  </section>

  <!-- ===== SECTION PACKAGES ===== -->
  <section class="packages">
    <h2>Formules de coaching</h2>
    <div class="package-cards">
      <div class="package">
        <h3>coaching 2 semaines</h3>
        <p>Un suivi intensif de 2 semaines pour un objectif précis.</p>
        <p class="price">$800</p>
      </div>

      <div class="package">
        <h3>coaching 4 semaines</h3>
        <p>Un accompagnement approfondi pour atteindre des résultats durables.</p>
        <p class="price">$1200</p>
      </div>

      <div class="package">
        <h3>coaching 8 semaines</h3>
        <p>Un programme complet pour transformer votre quotidien.</p>
        <p class="price">$2200</p>
      </div>
    </div>
  </section>

  <!-- ===== FOOTER ===== -->
  <footer>
    <p>&copy; INF1763-01 - Automne 2025</p>
  </footer>
</body>
</html>
