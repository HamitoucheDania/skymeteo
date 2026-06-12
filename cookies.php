<?php
/**
 * cookies.php
 *
 * Page de gestion du consentement aux cookies pour le site SKY METEO.
 * Permet aux utilisateurs de consulter et de modifier leur préférence liée à l'utilisation de cookies :
 * - Consentement à l'utilisation des cookies
 * - Thème (clair/sombre)
 * - Langue (français/anglais)
 * - Dernière ville météo consultée
 *
 * Actions possibles via POST :
 *  - accept  : L'utilisateur accepte les cookies
 *  - refuse  : L'utilisateur refuse les cookies
 *  - reset   : Réinitialise tous les cookies utilisateurs
 *
 * @author  Dania HAMITOUCHE
 * @version 1.8
 */
header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

$theme = $_GET['theme'] ?? $_COOKIE['theme'] ?? 'light';
$lang = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'fr';
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'accept') {
        setcookie('cookie_consent', 'accept', time() + 365*24*60*60, '/');
    } elseif ($_POST['action'] === 'refuse') {
        setcookie('cookie_consent', 'refuse', time() + 365*24*60*60, '/');
    } elseif ($_POST['action'] === 'reset') {
        foreach (['cookie_consent', 'theme', 'lang', 'last_city'] as $ck) {
            setcookie($ck, '', time() - 3600, '/');
        }
    }
    header("Location: cookies.php?theme=$theme&lang=$lang");
    exit;
}

$consent = $_COOKIE['cookie_consent'] ?? 'Aucun choix';
$ville = $_COOKIE['last_city'] ?? 'Non définie';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <title>Gestion des cookies - SKY METEO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/<?= htmlspecialchars($cssFile) ?>" />
  <link rel="icon" href="images/logo2.png" />
  <style>
    body {
      background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    .hero-title {
      text-align: center;
      padding: 60px 20px 10px;
    }

    .sky-title {
      font-size: 2.8em;
      color: #1e3c72;
      transition: color 0.3s ease;
    }
    .sky-title:hover {
      color: #103f91;
    }
    body.dark .sky-title {
      color: #fff;
    }
    body.dark .sky-title:hover {
      color: #ffd700;
    }

    .sky-subtitle {
      font-size: 1.2em;
      color: #333;
    }
    body.dark .sky-subtitle {
      color: #bbb;
    }

    .section-title {
      text-align: center;
      font-size: 1.8em;
      color: #1e3c72;
      margin: 40px auto 20px;
      transition: color 0.3s ease;
    }
    .section-title:hover {
      color: #103f91;
    }
    body.dark .section-title {
      color: #fff;
    }
    body.dark .section-title:hover {
      color: #ffd700;
    }

    .cookie-card {
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 16px;
      padding: 40px;
      margin: 20px auto 40px;
      max-width: 1000px;
      width: 95%;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    body.dark .cookie-card {
      background-color: rgba(20, 20, 20, 0.85);
      color: #fff;
    }

    table.cookie-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1.1em;
    }

    .cookie-table th, .cookie-table td {
      padding: 16px 20px;
      text-align: left;
      border-bottom: 1px solid #ccc;
      transition: background 0.3s ease;
    }

    .cookie-table tr:hover td {
      background-color: rgba(30, 100, 200, 0.08);
    }

    body.dark .cookie-table tr:hover td {
      background-color: rgba(255,255,255,0.05);
    }

    .cookie-actions {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 40px;
      flex-wrap: wrap;
    }

    .cookie-actions button {
      padding: 14px 26px;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      font-size: 1em;
      cursor: pointer;
      transition: all 0.3s ease;
      background-color: rgba(255,255,255,0.85);
      color: #1e3c72;
    }

    .cookie-actions button:hover {
      background-color: #1e3c72;
      color: #fff;
    }

    body.dark .cookie-actions button {
      background-color: rgba(30,30,30,0.85);
      color: #fff;
    }

    body.dark .cookie-actions button:hover {
      background-color: #ffd700;
      color: #1e3c72;
    }

    .return-block {
      background-color: rgba(255, 255, 255, 0.85);
      max-width: 400px;
      margin: 0 auto 60px;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    body.dark .return-block {
      background-color: rgba(30, 30, 30, 0.85);
    }

    .return-block a {
      display: inline-block;
      padding: 12px 24px;
      font-size: 1em;
      font-weight: bold;
      text-decoration: none;
      border-radius: 10px;
      color: #1e3c72;
      background-color: rgba(255,255,255,0.8);
      transition: all 0.3s ease;
    }

    .return-block a:hover {
      background-color: #1e3c72;
      color: white;
    }

    body.dark .return-block a {
      background-color: rgba(30,30,30,0.9);
      color: #fff;
    }

    body.dark .return-block a:hover {
      background-color: #ffd700;
      color: #1e3c72;
    }
  </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark' : '' ?>">

<?php require_once 'include/header.inc.php'; ?>

<section class="hero-title">
  <h1 class="sky-title">Gestion des cookies</h1>
  <p class="sky-subtitle">Contrôlez les données stockées sur votre navigateur</p>
</section>

<h2 class="section-title">À propos des cookies </h2>
<div class="cookie-card">
  <p style="font-size: 1.1em; line-height: 1.6;">
    Les cookies sont de petits fichiers texte stockés sur votre appareil lors de la navigation sur un site web. 
    Ils permettent au site de mémoriser certaines informations entre vos visites, comme vos préférences d'affichage ou votre langue.
    <br /><br />
    Sur <strong>SKY METEO</strong>, les cookies sont utilisés uniquement pour améliorer votre expérience utilisateur. 
    Ils servent notamment à :
  </p>
  <ul style="text-align: left; max-width: 900px; margin: 20px auto; font-size: 1em;">
    <li>Conserver votre thème préféré (mode clair ou sombre).</li>
    <li>Retenir la langue choisie (français ou anglais).</li>
    <li>Se souvenir de la dernière ville météo consultée.</li>
    <li>Enregistrer votre choix concernant l’utilisation des cookies.</li>
  </ul>
  <p style="font-size: 1.1em; line-height: 1.6;">
    Ces cookies sont anonymes, ne contiennent aucune donnée personnelle, et ne sont pas utilisés à des fins publicitaires ou commerciales.
    Vous pouvez à tout moment <strong>accepter</strong>, <strong>refuser</strong> ou <strong>réinitialiser</strong> votre consentement.
  </p>
</div>


<h2 class="section-title">Informations enregistrées</h2>

<div class="cookie-card">
  <table class="cookie-table">
    <tr><th>Consentement</th><td><?= htmlspecialchars($consent) ?></td></tr>
    <tr><th>Thème</th><td><?= htmlspecialchars($theme) ?></td></tr>
    <tr><th>Langue</th><td><?= htmlspecialchars($lang) ?></td></tr>
    <tr><th>Dernière ville consultée</th><td><?= htmlspecialchars($ville) ?></td></tr>
  </table>
</div>

<div class="cookie-actions">
  <form method="post">
    <button name="action" value="accept">Accepter</button>
    <button name="action" value="refuse">Refuser</button>
    <button name="action" value="reset">Réinitialiser</button>
  </form>
</div>

<div class="return-block">
  <a href="index.php?theme=<?= $theme ?>&lang=<?= $lang ?>">Retour à l'accueil</a>
</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
