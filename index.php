<?php
/**
 * Page d'accueil de SKY METEO.
 *
 * Cette page affiche les informations météo actuelles et les actualités météorologiques
 * pour une ville déterminée par l'utilisateur ou par géolocalisation via l'adresse IP.
 * Elle permet également la gestion du thème (clair/sombre) et des préférences de cookies.
 * 
 *
 * @author     Dania HAMITOUCHE
 * @version    2.5
 */

header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';
require_once 'include/header.inc.php';

// Gestion du thème (clair ou sombre)
$theme = $_GET['theme'] ?? $_COOKIE['theme'] ?? 'light';
setcookie('theme', $theme, time() + 365*24*60*60, '/');

// Gestion du bandeau cookies et redirection après choix
if (isset($_POST['cookie_choice'])) {
    setcookie('cookie_consent', $_POST['cookie_choice'], time() + 365*24*60*60, '/');
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Récupération de l'adresse IP du client
$ip = getClientIp();

// Géolocalisation de l'utilisateur via son IP
$location = getGeoPluginData($ip);

// Détermination de la ville par défaut
$defaultCity = $location['city'] ?? 'Paris';

// Récupération de la ville recherchée (GET ou géoloc)
$searchCity = $_GET['city'] ?? $defaultCity;

/**
 * Récupération des données météo via OpenWeatherMap.
 *
 * @var array|null $weather Données météo actuelles ou null si erreur.
 */
$weather = getWeatherByCity($searchCity, '77e34d22a2f4e7e1b4caa003f1ef18fc');

/**
 * Récupération d'une actualité météo via GNews.
 *
 * @var array|null $newsArticle Article d’actualité ou null si non disponible.
 */
$newsArticle = getWeatherNewsFromGNews($searchCity, 'a9fd707514f87d540974ebcb4ce60d24');

// Nom de la page actuelle (pour la gestion du bandeau cookie)
$currentPage = basename($_SERVER['PHP_SELF']);

// Consentement utilisateur aux cookies
$cookieConsent = $_COOKIE['cookie_consent'] ?? null;
?>

  <style type="text/css">
    .hero {
      text-align: center;
      padding-top: 60px;
    }
    .hero img {
      height: 100px;
    }
    .hero h1 {
      font-size: 2.5em;
      margin-top: 15px;
      color: <?php echo ($theme === 'dark') ? '#fff' : '#1e3c72'; ?>;
    }
    .block.transparent {
      background-color: rgba(255, 255, 255, 0.85);
      color: #000;
      padding: 30px;
      margin: 20px auto;
      max-width: 1000px;
      border-radius: 12px;
      text-align: center;
    }
    body.dark .block.transparent {
      background-color: rgba(20, 20, 20, 0.85);
      color: #fff;
    }
    .search-form {
      text-align: center;
      margin: 30px auto;
    }
    .search-form input[type="text"] {
      padding: 10px;
      font-size: 1em;
      width: 300px;
      border-radius: 10px;
      border: 1px solid #ccc;
    }
    .search-form button {
      padding: 10px 20px;
      border: none;
      background-color: #1e3c72;
      color: white;
      border-radius: 10px;
      font-weight: bold;
      margin-left: 10px;
    }
    .double-columns {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
      margin: 30px auto;
      max-width: 1000px;
    }
    .weather-details, .weather-news {
      flex: 1 1 400px;
      padding: 20px;
      border-radius: 12px;
      background-color: rgba(255,255,255,0.85);
    }
    body.dark .weather-details, body.dark .weather-news {
      background-color: rgba(20,20,20,0.85);
      color: #fff;
    }
    .carousel-links {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding: 20px;
      margin: 20px auto;
      max-width: 1000px;
      scroll-snap-type: x mandatory;
    }
    .carousel-links a {
      flex: 0 0 auto;
      scroll-snap-align: start;
      background-color: rgba(255,255,255,0.85);
      border-radius: 10px;
      padding: 20px;
      min-width: 180px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
      color: #000;
    }
    body.dark .carousel-links a {
      background-color: rgba(20,20,20,0.85);
      color: #fff;
    }
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      padding: 20px;
      max-width: 1000px;
      margin: auto;
    }
    .gallery img {
      width: 100%;
      border-radius: 12px;
    }
    /* Bannière cookies */
    .cookie-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      z-index: 9998;
    }
    .cookie-overlay.active {
      display: block;
    }
    #cookie-banner {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.95);
      border: 2px solid #1e3c72;
      padding: 30px;
      z-index: 9999;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      max-width: 600px;
      width: 90%;
      text-align: center;
      font-family: 'Roboto', sans-serif;
    }
    #cookie-banner p {
      font-size: 16px;
    }
    #cookie-banner button {
      background: #1e3c72;
      color: white;
      border: none;
      padding: 10px 20px;
      margin: 10px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    #cookie-banner button:hover {
      background: #2a5298;
    }
    #cookie-banner a {
      display: inline-block;
      margin-top: 15px;
      color: #1e3c72;
      text-decoration: underline;
      font-size: 14px;
    }
    #cookie-banner a:hover {
      color: #2a5298;
    }
    
    .weather-icon-box {
      display: inline-block;
      background-color: #2a5298; 
      border-radius: 12px;
      padding: 10px;
      margin: 15px 0;
    }
    .body.light .weather-icon-box {
      background-color: #cce5ff;
    }


    body.dark .weather-icon-box {
      background-color: #ffd700;
    }

    .weather-icon {
      height: 60px;
      vertical-align: middle;
    }

  </style>
<main>
<?php if (!$cookieConsent && $currentPage !== 'cookies.php'): ?>
<div class="cookie-overlay active"></div>
<div id="cookie-banner">
  <p><strong>SKY METEO</strong> utilise des cookies pour personnaliser votre expérience (thème, villes consultées). Acceptez-vous leur utilisation ?</p>
  <form method="post">
    <button type="submit" name="cookie_choice" value="accept">Accepter</button>
    <button type="submit" name="cookie_choice" value="refuse">Refuser</button>
  </form>
  <a href="cookies.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">En savoir plus</a>
</div>
<?php endif; ?>

<section class="hero-title">
  <h1 class="sky-title">SKY METEO</h1>
  <p class="sky-subtitle">Votre ciel, nos prévisions !</p>
</section>

<div class="block transparent">
  <p>Bienvenue sur SKY METEO 🌤️<br />Plongez dans l’univers de la météo avec notre plateforme simple et efficace.</p>
</div>

<div class="search-zone">
  <h2 class="search-title">Rechercher une ville</h2>
  <form method="get" action="index.php" class="search-form">
    <input type="hidden" name="theme" value="<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" />
    <input type="text" name="city" placeholder="Ex : Paris, Alger, Tokyo..." value="<?php echo htmlspecialchars($searchCity, ENT_QUOTES, 'UTF-8'); ?>" />
    <button type="submit">🔍</button>
  </form>
</div>

<div class="weather-actus-container">
  <div class="weather-card">
    <?php if ($weather): ?>
      <div class="weather-icon-box">
        <img src="https://openweathermap.org/img/wn/<?php echo htmlspecialchars($weather['icon'], ENT_QUOTES, 'UTF-8'); ?>@2x.png" alt="Icône météo" class="weather-icon" />
      </div>
      <h2><?php echo ucfirst($weather['desc']) . ' à ' . htmlspecialchars($searchCity, ENT_QUOTES, 'UTF-8'); ?></h2>
      <p><?php echo ucfirst($weather['desc']) . ' : ' . $weather['temp'] . ' °C'; ?></p>
      <div class="weather-grid">
        <div class="weather-item"><h3>Température</h3><p><?php echo $weather['temp'] . ' °C'; ?></p></div>
        <div class="weather-item"><h3>Ressenti</h3><p><?php echo ($weather['feels_like'] ?? '?') . ' °C'; ?></p></div>
        <div class="weather-item"><h3>Humidité</h3><p><?php echo ($weather['humidity'] ?? '?') . ' %'; ?></p></div>
        <div class="weather-item"><h3>Vent</h3><p><?php echo ($weather['wind'] ?? '?') . ' km/h'; ?></p></div>
      </div>
      <p style="margin-top: 15px; font-size: 0.9em; color: #666;">Lever du soleil : <?php echo $weather['sunrise'] ?? '?'; ?><br />Coucher du soleil : <?php echo $weather['sunset'] ?? '?'; ?></p>
    <?php else: ?>
      <p>Météo indisponible.</p>
    <?php endif; ?>
  </div>

  <div class="weather-news">
    <?php if ($newsArticle): ?>
      <h2 class="headline-text"><?php echo htmlspecialchars($newsArticle['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
      <img src="<?php echo htmlspecialchars($newsArticle['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Actu" style="max-width: 100%; border-radius: 10px; margin: 10px 0;" />
      <p><?php echo htmlspecialchars($newsArticle['description'], ENT_QUOTES, 'UTF-8'); ?></p>
      <a href="<?php echo htmlspecialchars($newsArticle['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Lire l'article complet</a>
    <?php else: ?>
      <h2 class="headline-text">Aucune actualité météo trouvée pour <?php echo htmlspecialchars($searchCity, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php endif; ?>
  </div>
</div>

<h2 class="section-title">Nos Services SKY METEO</h2>
<div class="main-pages-carousel">
  <a href="meteo.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/meteo.png" alt="Météo" /><p>Météo</p></a>
  <a href="statistiques.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/stats.png" alt="Statistiques" /><p>Statistiques</p></a>
  <a href="contact.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/contact.png" alt="Contact" /><p>Contact</p></a>
  <a href="tech.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/tech.png" alt="Technique" /><p>Technique</p></a>
  <a href="actualite.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/news.png" alt="Actualité" /><p>Actualité</p></a>
  <a href="apropos.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>" class="page-card"><img src="images/icons/info.png" alt="À propos" /><p>À propos</p></a>
</div>

<section class="inspiration-block block transparent">
  <h2>Nos Inspirations</h2>
  <div class="inspiration-gallery">
    <?php
    $images = glob('images/randomimages/*.{jpg,jpeg,png}', GLOB_BRACE);
    shuffle($images);
    foreach (array_slice($images, 0, 3) as $img): ?>
      <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="Inspiration" class="inspiration-img" />
    <?php endforeach; ?>
  </div>
</section>
</main>

<?php require_once 'include/footer.inc.php'; ?>
