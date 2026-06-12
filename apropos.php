<?php
/**
 * Page "À propos" de SKY METEO.
 * Cette page présente l'équipe, les objectifs, les technologies utilisées, ainsi que des informations pour contacter les développeuses.
 * Le thème (clair/sombre) et la langue de la page peuvent être configurés via des paramètres GET.
 * 
 * @version    1.3
 * @author     Dania HAMITOUCHE
 */
header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

$theme = $_GET['theme'] ?? 'light';
$lang = $_GET['lang'] ?? 'fr';
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang ?>" lang="<?= $lang ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>À propos - SKY METEO</title>
  <link rel="stylesheet" href="css/<?= htmlspecialchars($cssFile) ?>" />
  <link rel="icon" href="images/logo2.png" />
  <style>
    body {
      background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
      background-size: cover;
    }
  </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<?php require_once 'include/header.inc.php'; ?>

<section class="hero-title">
  <h1 class="sky-title">À propos de SKY METEO</h1>
  <p class="sky-subtitle">Notre mission, nos valeurs, notre équipe.</p>
</section>

<!-- 🌍 Présentation -->
<h2 class="section-title"> Qui sommes-nous ?</h2>

<section class="block transparent about-us" style="padding: 40px; max-width: 1100px; margin: auto; border-radius: 20px;">
  <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center; justify-content: center;">
    
    <!-- Image ronde -->
    <div style="flex: 0 0 260px; text-align: center;">
      <img src="images/universite.png" alt="Université Cergy" style="width: 220px; height: 220px; object-fit: cover; border-radius: 50%; box-shadow: 0 4px 16px rgba(0,0,0,0.3); border: 4px solid #1e3c72;" />
      <figcaption style="margin-top: 10px; font-style: italic; color: #777;">CY Cergy Paris Université</figcaption>
    </div>

    <!-- Texte -->
    <div style="flex: 1 1 500px; text-align: left;">
      <h3 style="font-size: 1.5em; margin-bottom: 15px; color: inherit;">Deux développeuses curieuses & passionnées</h3>
      <p style="font-size: 1.1em; line-height: 1.6em;">
        Nous sommes <strong>Dania Hamitouche</strong> et <strong>Imane Moussaoui</strong>, étudiantes en L2 Informatique à CY Cergy Paris Université.
        Ce projet web, nommé <strong>SkyMétéo</strong>, est né de notre passion pour la météo, le design, et le code propre.
      </p>
      <p style="font-size: 1.1em; line-height: 1.6em;">
        Notre objectif ? Proposer une plateforme <strong>100% accessible</strong>, esthétique, dynamique et utile, qui informe sur la météo tout en sensibilisant au climat 🌍
      </p>
      <p style="font-size: 1.1em; line-height: 1.6em;">
        Avec PHP, HTML5, CSS3, et des APIs ouvertes, nous avons conçu ce site en gardant une seule idée en tête : <em>faire simple, efficace et élégant.</em>
      </p>
    </div>

  </div>
</section>


<!-- 🎯 Objectifs -->
<h2 class="section-title"> Nos objectifs</h2>
<div class="main-pages-carousel">
  <div class="page-card">
    <img src="images/icons/meteo.png" alt="Clarté">
    <p>Prévisions météo <br><strong>claires et fiables</strong></p>
  </div>
  <div class="page-card">
    <img src="images/icons/ui.png" alt="Interface">
    <p>Interface fluide </p>
  </div>
  <div class="page-card">
    <img src="images/icons/news.png" alt="Actu">
    <p><strong>Actus climatiques</strong> <br>à la une</p>
  </div>
  <div class="page-card">
    <img src="images/icons/theme.png" alt="Thème">
    <p>Mode <strong>clair/sombre</strong><br>automatique</p>
  </div>
  <div class="page-card">
    <img src="images/icons/stats.png" alt="Stats">
    <p>Cartes & <strong>statistiques<br> dynamiques</strong></p>
  </div>
</div>


<!-- 👩‍💻 Technologies -->
<h2 class="section-title"> Technologies utilisées</h2>
<div class="main-pages-carousel">
  <div class="page-card">
    <img src="images/php.png" alt="PHP">
    <p><strong>PHP 8</strong><br>Serveur dynamique</p>
  </div>
  <div class="page-card">
    <img src="images/html.png" alt="HTML">
    <p><strong>HTML5</strong><br>Structure sémantique</p>
  </div>
  <div class="page-card">
    <img src="images/css.png" alt="CSS">
    <p><strong>CSS3</strong><br>Thèmes & responsive</p>
  </div>
  <div class="page-card">
    <img src="images/api.png" alt="API">
    <p><strong>OpenWeatherMap</strong><br>et autres APIs</p>
  </div>
</div>


<!-- 📬 Contact -->
<h2 class="section-title"> Nous contacter</h2>
<div class="block transparent">
  <p>Une remarque, une idée ou une suggestion ? Nous serons ravies de vous lire !</p>
  <p>Rendez-vous sur notre <a href="contact.php?theme=<?= $theme ?>&lang=<?= $lang ?>">formulaire de contact</a> pour nous écrire ✉️</p>
</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
