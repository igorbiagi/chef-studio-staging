<?php
/**
 * Layout "Schema vecchi e nuovi indirizzi" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$righe = get_sub_field('righe') ?: [];
$num   = (string) get_sub_field('totale_numero');
$tot   = (string) get_sub_field('totale_testo');
$casi  = get_sub_field('casi') ?: [];

chef_rsw_apri();
if ($righe) : ?>
<div class="rsw-migra">
<div class="rsw-migra-r rsw-migra-testa"><span class="rsw-migra-h"><?php echo chef_rsw_t(get_sub_field('intestazione_vecchio')); ?></span><span></span><span class="rsw-migra-h"><?php echo chef_rsw_t(get_sub_field('intestazione_nuovo')); ?></span></div>
<?php foreach ($righe as $r) :
  $cod = trim((string) ($r['codice'] ?? ''));
  $cod = $cod === '' ? '301' : $cod;
  $cod = ctype_digit($cod) ? 'redirect ' . $cod : $cod; ?>
<div class="rsw-migra-r"><span class="rsw-migra-u old"><?php echo chef_rsw_t($r['vecchio'] ?? ''); ?></span><span class="rsw-migra-a"><?php echo chef_rsw_t($cod); ?></span><span class="rsw-migra-u new"><?php echo chef_rsw_t($r['nuovo'] ?? ''); ?></span></div>
<?php endforeach; ?>
<?php if ($num !== '' || $tot !== '') : ?><div class="rsw-migra-tot"><b><?php echo chef_rsw_t($num); ?></b><span><?php echo chef_rsw_t($tot); ?></span></div><?php endif; ?>
</div>
<?php endif;
if ($casi) : ?>
<div class="rsw-migra-casi"><?php foreach ($casi as $c) :
  $punti = chef_rsw_righe($c['punti'] ?? ''); ?>
<div class="rsw-migra-caso<?php echo !empty($c['scuro']) ? ' scuro' : ''; ?>">
<?php if (!empty($c['etichetta'])) : ?><span class="rsw-migra-et"><?php echo chef_rsw_t($c['etichetta']); ?></span><?php endif; ?>
<?php if (!empty($c['numero'])) : ?><b class="rsw-migra-num"><?php echo chef_rsw_t($c['numero']); ?></b><?php if (!empty($c['barra'])) : ?><span class="rsw-migra-barra" aria-hidden="true"><i></i></span><?php endif; ?><?php endif; ?>
<?php if (!empty($c['titolo'])) : ?><h3><?php echo chef_rsw_t($c['titolo']); ?></h3><?php endif; ?>
<?php if (!empty($c['testo'])) : ?><p><?php echo chef_rsw_t($c['testo']); ?></p><?php endif; ?>
<?php if ($punti) : ?><ul><?php foreach ($punti as $pt) : ?><li><?php echo chef_rsw_t($pt); ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
<?php endforeach; ?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
