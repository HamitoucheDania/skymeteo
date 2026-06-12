<?php
/**
 * Page d'accueil du site SKY METEO.
 *
 * Cette page gère l'affichage dynamique du thème du site (clair ou sombre) et permet de basculer entre les deux thèmes.
 * Le thème est déterminé en priorité par le paramètre GET `theme`, puis par le cookie `theme`, et enfin par une valeur par défaut (clair).
 * La page inclut un en-tête, une barre de navigation et des liens permettant de passer d'une section à l'autre du site (Prévision, Actualité, Statistiques, Contact).
 *
 * @author Dania HAMITOUCHE 
 * @version    1.6
 */
header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

// Gestion du thème avec priorité GET > COOKIE > valeur par défaut
$theme = $_GET['theme'] ?? $_COOKIE['theme'] ?? 'light';

// Mise à jour du cookie si changé
if (isset($_GET['theme'])) {
    setcookie('theme', $theme, time() + 365*24*60*60, '/');
}

$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SKY METEO</title>
  <link rel="stylesheet" type="text/css" href="css/<?php echo htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="icon" type="image/png" href="images/logo2.png" />
  <style type="text/css">
    body {
      background: url('<?php echo htmlspecialchars($bgImage, ENT_QUOTES, 'UTF-8'); ?>') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
    }
    .theme-toggle-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 16px;
      border-radius: 20px;
      text-decoration: none;
      color: #fff;
      font-weight: 500;
      font-size: 0.9em;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      margin: 5px;
    }
    body.dark .theme-toggle-btn {
      background: linear-gradient(90deg, #ffd700, #ffeb3b);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    body:not(.dark) .theme-toggle-btn {
      background: linear-gradient(90deg, #2a5298, #5b86e5);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    .theme-toggle-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    }
    .theme-icon {
      margin-right: 8px;
      font-size: 1.2em;
      transition: transform 0.3s ease;
    }
    .theme-toggle-btn:hover .theme-icon {
      transform: rotate(20deg);
    }
    .header-right .options {
      display: flex;
      align-items: center;
      gap: 10px;
    }
  </style>
</head>
<body class="index-page <?php echo ($theme === 'dark') ? 'dark' : ''; ?>">
<a id="top"></a>
<div class="wrapper">
  <header class="banner">
    <div class="header-left">
    <a href="index.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">
      <img src="images/logo2.png" alt="Logo SKY METEO" />
    </a>
    </div>
    <div class="header-center">
      <h1>SKY METEO</h1>
    </div>
    <div class="header-right">
      <div class="options">
        <a href="<?php echo basename($_SERVER['PHP_SELF']) . '?theme=' . ($theme === 'dark' ? 'light' : 'dark'); ?>" class="theme-toggle-btn">
          <span class="theme-icon"><?php echo ($theme === 'dark') ? '☀️' : '🌙'; ?></span>
        </a>
      </div>
    </div>
  </header>

  <nav class="main-nav">
    <ul>
      <li><a href="index.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">Accueil</a></li>
      <li><a href="meteo.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">Prevision</a></li>
      <li><a href="actualite.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">Actualité</a></li>
      <li><a href="statistiques.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">Statistiques</a></li>
      <li><a href="contact.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">Contact</a></li>
    </ul>
  </nav>