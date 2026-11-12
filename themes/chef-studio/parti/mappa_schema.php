<?php
/**
 * Layout "Mappa a schema" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$parti     = get_sub_field('parti') ?: [];
$etichette = chef_rsw_righe(get_sub_field('etichette'));
$evidenzia = function ($t) {
  return preg_replace('/`([^`]+)`/', '<code>$1</code>', chef_rsw_t((string) $t));
};

chef_rsw_apri();
if ($parti) : ?>
<div class="rsw-mappa"><?php
  $centro = '<div class="rsw-mappa-c"><b>' . chef_rsw_t(get_sub_field('centro_titolo')) . '</b><span>' . chef_rsw_t(get_sub_field('centro_testo')) . '</span>'
    . (get_sub_field('centro_etichetta') ? '<em class="rsw-mappa-et">' . chef_rsw_t(get_sub_field('centro_etichetta')) . '</em>' : '') . '</div>';
  foreach ($parti as $i => $p) {
    $lato = ($p['lato'] ?? 'l') === 'r' ? 'r' : 'l';
    echo '<div class="rsw-mappa-n ' . $lato . '"><h3>' . chef_rsw_t($p['titolo'] ?? '') . '</h3><p>' . $evidenzia($p['testo'] ?? '') . '</p></div>';
    if ($i === 0) {
      echo $centro;
    }
  }
  if ($etichette) {
    echo '<div class="rsw-mappa-giu">';
    foreach ($etichette as $e) {
      echo '<span>' . chef_rsw_t($e) . '</span>';
    }
    echo '</div>';
  }
?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
