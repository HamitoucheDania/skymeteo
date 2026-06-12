<?php
/**
 * Page d'affichage des actualités RSS de France TV Info.
 * Cette page charge un flux RSS provenant de France TV Info et affiche les 5 derniers titres d'actualités.
 * En cas d'échec du chargement du flux, un message d'erreur est affiché.
 * 
 * @version    1.8
 * @author     Dania HAMITOUCHE
 */
$rss = simplexml_load_file('https://www.francetvinfo.fr/titres.rss');
if ($rss):
  echo '<ul class="rss-list">';
  $count = 0;
  foreach ($rss->channel->item as $item) {
    if ($count++ >= 5) break;
    echo '<li><a href="'.htmlspecialchars($item->link).'" target="_blank">'.htmlspecialchars($item->title).'</a></li>';
  }
  echo '</ul>';
else:
  echo '<p>Impossible de charger les actualités.</p>';
endif;
?>
