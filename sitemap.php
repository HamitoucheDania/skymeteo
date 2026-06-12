<?php
/**
 * Page du plan du site de SKY METEO.
 * Cette page affiche un plan du site sous forme de grille, avec des liens vers toutes les pages principales du site.
 * L'affichage s'adapte en fonction du thème (clair ou sombre) choisi par l'utilisateur.
 * 
 * @version    1.0
 * @author     Imane MOUSSAOUI
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
  <title>Plan du site - SKY METEO</title>
  <link rel="stylesheet" href="css/<?= htmlspecialchars($cssFile) ?>" />
  <link rel="icon" href="images/logo2.png" />
  <style>
    body {
      background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
      background-size: cover;
    }

    .sitemap-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 30px;
      max-width: 1100px;
      margin: 50px auto;
      padding: 0 20px;
    }

    .sitemap-card {
      background-color: rgba(255, 255, 255, 0.85);
      border-radius: 20px;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    body.dark .sitemap-card {
      background-color: rgba(20, 20, 20, 0.85);
      color: #fff;
    }

    .sitemap-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    .sitemap-card img {
      width: 48px;
      height: 48px;
      margin-bottom: 15px;
    }

    .sitemap-card a {
      display: block;
      font-size: 1.2em;
      font-weight: bold;
      text-decoration: none;
      color: inherit;
      margin-bottom: 8px;
    }

    .sitemap-card a:hover {
      color: #2a5298;
    }

    body.dark .sitemap-card a:hover {
      color: #ffd700;
    }

    .sitemap-card p {
      font-size: 0.95em;
      color: #555;
    }

    body.dark .sitemap-card p {
      color: #ccc;
    }
  </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<?php require_once 'include/header.inc.php'; ?>

<section class="hero-title">
  <h1 class="sky-title">PLAN DU SITE</h1>
  <p class="sky-subtitle">Tous les accès directs à nos pages en un coup d’œil</p>
</section>

<div class="sitemap-grid">

  <div class="sitemap-card">
    <img src="images/icons/home.png" alt="Accueil" />
    <a href="index.php?theme=<?= $theme ?>">Accueil</a>
    <p>Présentation générale, météo IP, fond dynamique.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/meteo.png" alt="Météo" />
    <a href="meteo.php?theme=<?= $theme ?>">Météo</a>
    <p>Prévisions locales, départementales et carte interactive.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/news.png" alt="Actualité" />
    <a href="actualite.php?theme=<?= $theme ?>">Actualité</a>
    <p>Flux météo, actus climatiques et alertes mondiales.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/stats.png" alt="Statistiques" />
    <a href="statistique.php?theme=<?= $theme ?>">Statistiques</a>
    <p>Températures, graphiques météo et stats du site.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/tech.png" alt="Technique" />
    <a href="tech.php?theme=<?= $theme ?>">Technique</a>
    <p>Technos, architecture, APIs utilisées dans le projet.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/contact.png" alt="Contact" />
    <a href="contact.php?theme=<?= $theme ?>">Contact</a>
    <p>Nous écrire, nous contacter ou laisser un avis.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/info.png" alt="À propos" />
    <a href="apropos.php?theme=<?= $theme ?>">À propos</a>
    <p>Notre équipe, notre projet, nos objectifs pédagogiques.</p>
  </div>

  <div class="sitemap-card">
    <img src="images/icons/sitemap.png" alt="Plan du site" />
    <a href="sitemap.php?theme=<?= $theme ?>">Plan du site</a>
    <p>Vous êtes ici. Tous les chemins mènent à SKY METEO.</p>
  </div>
  <div class="sitemap-card">
  <img src="images/icons/cookies.png" alt="Cookies" />
  <a href="cookies.php?theme=<?= $theme ?>">Cookies</a>
  <p>Gérer vos préférences de cookies et le consentement utilisateur.</p>
</div>


</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
