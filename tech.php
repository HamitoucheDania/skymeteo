<?php
/**
 * Page technique pour la visualisation dynamique des données provenant des API.
 *
 * Cette page affiche plusieurs informations, telles que l'image du jour de la NASA (APOD),
 * ainsi que des données de géolocalisation provenant de différentes sources, comme GeoPlugin,
 * ipinfo.io et WhatIsMyIP.
 *
 * @author Imane MOUSSAOUI 
 * @version    1.3
 */
header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

$theme = $_GET['theme'] ?? 'light';
$lang = $_GET['lang'] ?? 'fr';

$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';

$nasaKey = "61SWah0luHGYx1Y18U24RV8NDIA7OPjzkL04Fdt8";
$ipinfoToken = "a2859efd2cbb75";
$whatismyipKey = "e012c24e2f824f246d21b81753ce1562";

$ip = getClientIp();

$apod = getApodDataCurl($nasaKey);


$geo = getGeoPluginData($ip);
$ipinfo = getIpInfoData($ip, $ipinfoToken);
$whats = getGeoDataWithFallback($ip, $whatismyipKey);

?>
  <style>
    body {
      background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    .section-title {
      text-align: center;
      font-size: 2em;
      color: #0a1f44;
      margin: 40px auto 10px;
      letter-spacing: 1px;
    }

    body.dark .section-title {
      color: #fff;
    }

    .tech-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(230,230,230,0.9));
      box-shadow: 0 12px 30px rgba(0,0,0,0.2);
      border-radius: 18px;
      padding: 30px;
      margin: 20px auto 40px;
      opacity: 0;
      transform: translateY(40px) scale(0.97);
      animation: slideFadeIn 0.9s ease-out forwards;
    }

    body.dark .tech-card {
      background: linear-gradient(135deg, rgba(40,40,40,0.95), rgba(20,20,20,0.9));
      color: #fff;
    }

    .location-block {
      width: 1100px;
      max-width: 100%;
      margin-left: auto;
      margin-right: auto;
    }

    .tech-card h3 {
      font-size: 1.4em;
      margin-bottom: 15px;
      color: #113366;
    }

    body.dark .tech-card h3 {
      color: #ffffff;
    }

    .location-table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
    }

    .location-table th, .location-table td {
      padding: 16px 22px;
      text-align: left;
      transition: background 0.3s, color 0.3s;
    }

    .location-table th {
      background-color: #003366;
      color: #fff;
      font-size: 1.1em;
    }

    .location-table tr:hover td {
      background-color: rgba(0, 102, 204, 0.1);
      box-shadow: 0 0 8px rgba(0,102,204,0.3);
    }

    body.dark .location-table th {
      background-color: #111;
    }

    body.dark .location-table td {
      color: #eee;
    }

    body.dark .location-table tr:hover td {
      background-color: rgba(255,255,255,0.05);
      box-shadow: 0 0 8px rgba(255,255,255,0.2);
    }

    @keyframes slideFadeIn {
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .apod-container {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
      max-width: 1100px;
      margin: 40px auto;
    }

    .apod-container .image,
    .apod-container .desc {
      flex: 1 1 420px;
      min-width: 300px;
    }

    .apod-container img,
    .apod-container iframe {
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
  </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<?php require_once 'include/header.inc.php'; ?>

<section class="hero-title">
  <h1 class="sky-title">PAGE TECHNIQUE</h1>
  <p class="sky-subtitle">Visualisation dynamique des données issues des APIs</p>
</section>

<!-- NASA IMAGE + DESC -->
<h2 class="section-title">🌌 Image du jour (NASA APOD)</h2>

<div class="apod-container">
  <div class="tech-card image">
    <?php if (($apod['media_type'] ?? '') === 'image'): ?>
      <img src="<?= htmlspecialchars($apod['url']) ?>" alt="Image NASA" />
    <?php elseif (($apod['media_type'] ?? '') === 'video'): ?>
      <iframe src="<?= htmlspecialchars($apod['url']) ?>" height="315" allowfullscreen></iframe>
    <?php else: ?>
      <p><strong>Erreur :</strong> <?= htmlspecialchars($apod['explanation'] ?? 'Aucune description disponible.') ?></p>
    <?php endif; ?>
  </div>
  <div class="tech-card desc">
    <h3>Description</h3>
    <p><?= htmlspecialchars($apod['explanation'] ?? 'Aucune description disponible.') ?></p>
  </div>
</div>

<!-- GEOPLUGIN -->
<h2 class="section-title">📍 Localisation GeoPlugin (XML)</h2>
<div class="tech-card location-block">
  <h3>🌐 Données GeoPlugin</h3>
  <table class="location-table">
    <tr><th>Pays</th><td><?= htmlspecialchars($geo['country'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Région</th><td><?= htmlspecialchars($geo['region'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Ville</th><td><?= htmlspecialchars($geo['city'] ?? 'Inconnu') ?></td></tr>
  </table>
</div>

<!-- IPINFO -->
<h2 class="section-title">📡 Localisation ipinfo.io (JSON)</h2>
<div class="tech-card location-block">
  <h3>📡 Données ipinfo</h3>
  <table class="location-table">
    <tr><th>IP</th><td><?= htmlspecialchars($ipinfo['ip'] ?? $ip) ?></td></tr>
    <tr><th>Ville</th><td><?= htmlspecialchars($ipinfo['city'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Région</th><td><?= htmlspecialchars($ipinfo['region'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Pays</th><td><?= htmlspecialchars($ipinfo['country'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Coordonnées</th><td><?= htmlspecialchars($ipinfo['loc'] ?? 'Inconnu') ?></td></tr>
  </table>
</div>

<!-- WHATISMYIP -->
<h2 class="section-title">🔍 Localisation WhatIsMyIP (TXT)</h2>
<div class="tech-card location-block">
  <h3>🔍 Données WhatIsMyIP</h3>
  <table class="location-table">
    <tr><th>IP</th><td><?= htmlspecialchars($whats['ip'] ?? $ip) ?></td></tr>
    <tr><th>Ville</th><td><?= htmlspecialchars($whats['city'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Région</th><td><?= htmlspecialchars($whats['region'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Pays</th><td><?= htmlspecialchars($whats['country'] ?? 'Inconnu') ?></td></tr>
    <tr><th>Latitude / Longitude</th><td><?= htmlspecialchars($whats['latitude'] ?? '') ?> / <?= htmlspecialchars($whats['longitude'] ?? '') ?></td></tr>
  </table>
</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
