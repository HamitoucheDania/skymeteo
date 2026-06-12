<?php
/**
 * Page de contact du site SkyMeteo.
 *
 * Cette page permet aux utilisateurs d’envoyer un message via un formulaire de contact.
 * Les données du formulaire sont traitées en PHP, validées, puis envoyées par mail si tout est conforme.
 * La page adapte également son affichage selon le thème (clair/sombre) et la langue.
 *
 * @package SkyMeteo
 * @author  Imane MOUSSAOUI
 * @version 1.4
 *
 * @uses getClientIp()          Fonction éventuellement utilisée dans functions.inc.php
 * @uses functions.inc.php      Fichier contenant les fonctions utilitaires globales
 */

ob_start(); 
require_once 'include/functions.inc.php';

$theme = $_GET['theme'] ?? 'light';
$lang = $_GET['lang'] ?? 'fr';
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';

$message = '';
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $contenu = trim($_POST['message'] ?? '');
    $rgpd = isset($_POST['rgpd']);

    if ($nom && $email && $contenu && filter_var($email, FILTER_VALIDATE_EMAIL) && $rgpd) {
        $to = 'contact@skymeteo.fr';
        $subject = "Contact - $sujet";
        $headers = "From: $nom <$email>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
        $body = "Nom : $nom\nEmail : $email\n\nMessage :\n$contenu";

        if (mail($to, $subject, $body, $headers)) {
            $message = "✅ Votre message a bien été envoyé.";
            $sent = true;
        } else {
            $message = "❌ Une erreur est survenue. Veuillez réessayer plus tard.";
        }
    } else {
        $message = "⚠️ Merci de remplir tous les champs et de cocher la case RGPD.";
    }
}
?>
<?php require_once 'include/header.inc.php'; ?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <title>Contact - SKY METEO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/<?= htmlspecialchars($cssFile) ?>" />
  <link rel="icon" href="images/logo2.png" />
  <style>
    body {
      background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
      background-size: cover;
    }

    .toast {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #2ecc71;
      color: white;
      padding: 15px 25px;
      border-radius: 12px;
      font-weight: bold;
      z-index: 9999;
      animation: fadeOut 5s forwards;
    }

    .toast.error {
      background-color: #e74c3c;
    }

    @keyframes fadeOut {
      0% { opacity: 1; }
      80% { opacity: 1; }
      100% { opacity: 0; top: 0; }
    }

    .form-container {
      max-width: 800px;
      margin: 40px auto;
    }

    .form-container form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1em;
    }

    .form-container button {
      align-self: center;
      padding: 12px 28px;
      font-size: 1em;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      background-color: #1e3c72;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .form-container button:hover {
      background-color: #2a5298;
    }

    .form-container .rgpd {
      font-size: 0.95em;
    }

    /* Sombre */
    body.dark .block p,
    body.dark .form-container input,
    body.dark .form-container textarea,
    body.dark .form-container label,
    body.dark .form-container .rgpd label {
      color: #fff;
    }

    body.dark .form-container input,
    body.dark .form-container textarea {
      background-color: rgba(30, 30, 30, 0.95);
      border: 1px solid #555;
    }

    body.dark .form-container input::placeholder,
    body.dark .form-container textarea::placeholder {
      color: #aaa;
    }
    .contact-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  margin: 40px auto;
  max-width: 1000px;
}

.contact-card {
  flex: 1 1 280px;
  background-color: rgba(255,255,255,0.85);
  border-radius: 16px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.contact-card:hover {
  transform: translateY(-5px);
}

.contact-card img {
  width: 42px;
  height: 42px;
  margin-bottom: 10px;
}

.contact-card h3 {
  margin: 10px 0;
  font-size: 1.2em;
  color: #1e3c72;
}

.contact-card p,
.contact-card a {
  color: #333;
  font-size: 1em;
  text-decoration: none;
}

body.dark .contact-card {
  background-color: rgba(20, 20, 20, 0.85);
}

body.dark .contact-card h3 {
  color: #ffd700;
}

body.dark .contact-card p,
body.dark .contact-card a {
  color: #ddd;
}

  </style>
</head>
<body class="<?= $theme === 'dark' ? 'dark' : '' ?>">

<?php if ($message): ?>
  <div class="toast <?= $sent ? '' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<section class="hero-title">
  <h1 class="sky-title">CONTACT</h1>
  <p class="sky-subtitle">Besoin de nous écrire ? On vous répond rapidement.</p>
</section>

<!-- Coordonnées -->
<h2 class="section-title"> Nos coordonnées</h2>

<div class="contact-grid">
  <div class="contact-card">
    <img src="images/icons/email.png" alt="Email">
    <h3>Email</h3>
    <p><a href="mailto:contact@skymeteo.fr">skymeteoproo@gmail.com</a></p>
  </div>
  <div class="contact-card">
    <img src="images/icons/phone.png" alt="Téléphone">
    <h3>Téléphone</h3>
    <p>+33 1 23 45 67 89</p>
  </div>
  <div class="contact-card">
    <img src="images/icons/location.png" alt="Adresse">
    <h3>Adresse</h3>
    <p>CY Cergy Université<br />95000 Cergy, France</p>
  </div>
</div>



<!-- Formulaire -->
<h2 class="section-title"> Formulaire de contact</h2>
<div class="block form-container">
  <?php if (!$sent): ?>
  <form method="post" action="contact.php?theme=<?= $theme ?>&lang=<?= $lang ?>">
    <label for="nom">Nom complet :</label>
    <input type="text" name="nom" id="nom" required placeholder="Votre nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" />

    <label for="email">Adresse email :</label>
    <input type="email" name="email" id="email" required placeholder="votre@email.fr" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

    <label for="sujet">Sujet :</label>
    <input type="text" name="sujet" id="sujet" placeholder="Objet du message" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>" />

    <label for="message">Message :</label>
    <textarea name="message" id="message" rows="6" required placeholder="Votre message..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

    <div class="rgpd">
      <input type="checkbox" name="rgpd" id="rgpd" required />
      <label for="rgpd">J’accepte que mes données soient utilisées uniquement pour me recontacter.</label>
    </div>

    <button type="submit">📨 Envoyer le message</button>
  </form>
  <?php endif; ?>
</div>

<?php require_once 'include/footer.inc.php'; ?>
</body>
</html>
<?php ob_end_flush(); ?>
