<?php
/**
 * Layout "Tabella di confronto" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');

$colonne = get_sub_field('colonne') ?: [];
$righe   = get_sub_field('righe') ?: [];
$opzioni = (array) (get_sub_field('opzioni') ?: []);
$prima_voce = in_array('prima-voce', $opzioni, true);

$classi = ['rsw-tab'];
if (in_array('evidenzia', $opzioni, true)) {
  $classi[] = 'rsw-tab-evid';
}
if (in_array('stretta', $opzioni, true)) {
  $classi[] = 'rsw-tab-stretta';
}

chef_rsw_apri();

if ($colonne && $righe) : ?>
<table class="<?php echo esc_attr(implode(' ', $classi)); ?>"><thead><tr><?php foreach ($colonne as $c) : ?><th><?php echo chef_rsw_t($c['intestazione'] ?? ''); ?><?php if (!empty($c['nota'])) : ?><small><strong><?php echo chef_rsw_t($c['nota']); ?></strong></small><?php endif; ?></th><?php endforeach; ?></tr></thead><tbody>
<?php foreach ($righe as $r) :
  $celle = chef_rsw_righe($r['celle'] ?? '');
  if (!$celle) {
    continue;
  }
  echo '<tr>';
  foreach ($celle as $i => $cella) {
    $tag = ($i === 0 && $prima_voce) ? 'th' : 'td';
    echo '<' . $tag . '>' . wptexturize(wp_kses_post($cella)) . '</' . $tag . '>';
  }
  echo '</tr>';
endforeach; ?>
</tbody></table>
<?php endif;

chef_rsw_chiudi();
get_template_part('parti/layout_close');
