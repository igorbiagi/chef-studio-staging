<?php
/**
 * Layout "Schema vecchi e nuovi indirizzi" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$righe = get_sub_field('righe') ?: [];
$num   = (string) get_sub_field('totale_numero');
$tot   = (string) get_sub_field('totale_testo');

chef_rsw_apri();
if ($righe) : ?>
<div class="rsw-migra"><span class="rsw-migra-h"><?php echo esc_html(get_sub_field('intestazione_vecchio')); ?></span><span class="rsw-migra-h"></span><span class="rsw-migra-h"><?php echo esc_html(get_sub_field('intestazione_nuovo')); ?></span><?php foreach ($righe as $r) : ?><span class="rsw-migra-u old"><?php echo esc_html($r['vecchio'] ?? ''); ?></span><span class="rsw-migra-a"><?php echo esc_html(($r['codice'] ?? '') !== '' ? $r['codice'] : '301'); ?></span><span class="rsw-migra-u new"><?php echo esc_html($r['nuovo'] ?? ''); ?></span><?php endforeach; ?><?php if ($num !== '' || $tot !== '') : ?><div class="rsw-migra-tot"><b><?php echo esc_html($num); ?></b><span><?php echo esc_html($tot); ?></span></div><?php endif; ?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
