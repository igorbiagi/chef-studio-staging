<?php
/**
 * Layout "Barre con soglie e valori" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$metriche = get_sub_field('metriche') ?: [];
$legenda  = (string) get_sub_field('legenda');

chef_rsw_apri();
if ($metriche) : ?>
<div class="rsw-bar"><?php if ($legenda !== '') : ?><p class="rsw-bar-leg"><span>▼</span> <?php echo chef_rsw_t($legenda); ?></p><?php endif; ?><?php foreach ($metriche as $m) :
  $pos = max(0, min(100, (float) ($m['posizione'] ?? 0))); ?><div class="rsw-bar-r"><div class="rsw-bar-n"><b><?php echo chef_rsw_t($m['sigla'] ?? ''); ?></b><span><?php echo chef_rsw_t($m['descrizione'] ?? ''); ?></span></div><div class="rsw-bar-g"><div class="rsw-bar-s"><i class="b"><?php echo chef_rsw_t($m['buono'] ?? ''); ?></i><i class="m"><?php echo chef_rsw_t($m['medio'] ?? ''); ?></i><i class="p"><?php echo chef_rsw_t($m['scarso'] ?? ''); ?></i><?php if (($m['valore'] ?? '') !== '') : ?><em class="rsw-bar-m" style="left:<?php echo esc_attr(rtrim(rtrim(number_format($pos, 2, '.', ''), '0'), '.')); ?>%"><?php echo chef_rsw_t($m['valore']); ?></em><?php endif; ?></div><?php if (!empty($m['nota'])) : ?><small><?php echo chef_rsw_t($m['nota']); ?></small><?php endif; ?></div></div><?php endforeach; ?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
