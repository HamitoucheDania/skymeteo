<?php
/**
 * Page d'actualités météo pour une ville donnée.
 *
 * Cette page récupère et affiche des actualités météo à partir de l'API GNews,
 * en fonction d'une ville spécifiée par l'utilisateur (ou déduite via l'adresse IP).
 * Elle gère également l'affichage selon un thème (clair/sombre) et la langue.
 *
 * @package SkyMeteo
 * @author  Imane MOUSSAOUI
 * @version 2.3
 *
 * @uses getClientIp() pour obtenir l'IP de l'utilisateur
 * @uses getGeoPluginData() pour géolocaliser l'utilisateur
 * @uses getMultipleWeatherNews() pour récupérer des actualités météo
 */

header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

$theme = $_GET['theme'] ?? 'light';
$lang = $_GET['lang'] ?? 'fr';
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';

$gnewsKey = 'a9fd707514f87d540974ebcb4ce60d24'; 

// Récupération ville IP si aucune saisie
$ip = getClientIp();
$geo = getGeoPluginData($ip);
$cityFromIP = $geo['city'] ?? 'Paris';

$city = $_GET['city'] ?? null;
$villeRech = $city ? $city : $cityFromIP;

// Actus liées à la ville (via IP ou recherche)
$weatherNews = getMultipleWeatherNews("météo $villeRech", $gnewsKey, 6);

// Actus générales monde (climat, météo)
$globalNews = getMultipleWeatherNews("actualité météo OR climat OR météo monde", $gnewsKey, 6);

// Alertes extrêmes
$alertNews = getMultipleWeatherNews("alerte météo OR catastrophe naturelle OR cyclone OR tempête", $gnewsKey, 4);

// Fond dynamique selon thème
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';
?>
<?php require_once 'include/header.inc.php'; ?>

<!-- 🟩 Titre principal -->
<section class="hero-title">
  <h1 class="sky-title">ACTUALITÉ</h1>
</section>

<!-- 🔍 Barre de recherche ville -->
<section class="search-zone">
  <h2 class="search-title">Rechercher des actualités météo par ville</h2>
  <form method="get" action="actualite.php" class="search-form">
    <input type="hidden" name="theme" value="<?= $theme ?>">
    <input type="hidden" name="lang" value="<?= $lang ?>">
    <input type="text" name="city" placeholder="Ex : Paris, Lyon, Dakar..." value="<?= htmlspecialchars($city ?? '') ?>" />
    <button type="submit">🔎</button>
  </form>
</section>

<!-- 🏙️ Actus météo pour la ville -->
<h2 class="section-title">🌆 Actualités météo à <?= htmlspecialchars(ucfirst($villeRech)) ?></h2>
<div class="rss-grid">
  <?php if (!empty($weatherNews)) : ?>
    <?php foreach ($weatherNews as $article): ?>
      <div class="rss-item">
        <h3><a href="<?= htmlspecialchars($article['url']) ?>" target="_blank"><?= htmlspecialchars($article['title']) ?></a></h3>
        <?php if (!empty($article['image'])): ?>
          <img src="<?= htmlspecialchars($article['image']) ?>" alt="Image" style="max-width:100%; border-radius:10px; margin-bottom:10px;" />
        <?php endif; ?>
        <p><?= htmlspecialchars($article['description']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center;">📭 Aucune actualité trouvée pour <?= htmlspecialchars($villeRech) ?>.</p>
  <?php endif; ?>
</div>

<!-- 🌍 Actus météo dans le monde -->
<h2 class="section-title">📰 Actualités météo dans le monde</h2>
<div class="rss-grid">
  <?php if (!empty($globalNews)) : ?>
    <?php foreach ($globalNews as $article): ?>
      <div class="rss-item">
        <h3><a href="<?= htmlspecialchars($article['url']) ?>" target="_blank"><?= htmlspecialchars($article['title']) ?></a></h3>
        <?php if (!empty($article['image'])): ?>
          <img src="<?= htmlspecialchars($article['image']) ?>" alt="Image" style="max-width:100%; border-radius:10px; margin-bottom:10px;" />
        <?php endif; ?>
        <p><?= htmlspecialchars($article['description']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center;">🌎 Aucune actualité météo mondiale pour le moment.</p>
  <?php endif; ?>
</div>

<!-- ⚠️ Alertes météo -->
<h2 class="section-title">⚠️ Alertes Météorologiques dans le monde</h2>
<div class="rss-grid">
  <?php if (!empty($alertNews)) : ?>
    <?php foreach ($alertNews as $article): ?>
      <div class="rss-item">
        <h3><a href="<?= htmlspecialchars($article['url']) ?>" target="_blank"><?= htmlspecialchars($article['title']) ?></a></h3>
        <?php if (!empty($article['image'])): ?>
          <img src="<?= htmlspecialchars($article['image']) ?>" alt="Image" style="max-width:100%; border-radius:10px; margin-bottom:10px;" />
        <?php endif; ?>
        <p><?= htmlspecialchars($article['description']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center;">✅ Aucune alerte majeure détectée pour le moment.</p>
  <?php endif; ?>
</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
