<?php
/**
 * Footer et gestion des visites pour la page.
 *
 * Cette page génère le pied de page d'un site, avec des liens vers les sections importantes comme :
 * - À propos
 * - Plan du site
 * - Gestion des cookies
 * - Page technique
 * - Page de contact
 *
 * De plus, elle affiche le nombre total de visites sur la page, récupéré via la fonction `getPageHits()`.
 *
 * @author Imane MOUSSAOUI
 * @version    1.8
 */
$hits = getPageHits();
?>
<a href="#top" class="back-to-top" title="Retour en haut">⬆️</a>
<footer class="banner footer">
  <div class="footer-left">
    <a href="apropos.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">À propos</a><br />
    <i>© Dania HAMITOUCHE &amp; Imane MOUSSAOUI</i><br />
    <i>CY Cergy Université</i>
  </div>
  <div class="footer-center">
    <strong><?php echo htmlspecialchars($hits, ENT_QUOTES, 'UTF-8'); ?> visites</strong><br />
    <a href="sitemap.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>"> Plan du site</a><br />
    <a href="cookies.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>"> Gérer les cookies</a>
  </div>
  <div class="footer-right">
    <a href="tech.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">🔧 Technique</a><br />
    <a href="contact.php?theme=<?php echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8'); ?>">📞 Contact</a>
  </div>
</footer>