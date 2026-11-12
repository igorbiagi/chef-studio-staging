<?php
/**
 * Layout "Percorso a tappe" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$tappe  = get_sub_field('tappe') ?: [];
$inizio = (string) get_sub_field('inizio');
$fine   = (string) get_sub_field('fine');

chef_rsw_apri();
if ($tappe) : ?>
<div class="rsw-tl"><?php foreach ($tappe as $i => $t) : ?><div class="rsw-tl-s"><div class="rsw-tl-n"><?php echo chef_rsw_t(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></div><?php if (!empty($t['titolo'])) : ?><h3><?php echo chef_rsw_t($t['titolo']); ?></h3><?php endif; ?><?php echo wp_kses_post($t['testo'] ?? ''); ?><?php if (!empty($t['risultato'])) : ?><span class="rsw-tl-out"><?php echo chef_rsw_t($t['risultato']); ?></span><?php endif; ?></div><?php endforeach; ?></div>
<?php if ($inizio !== '' || $fine !== '') : ?><div class="rsw-tl-fine"><span><?php echo chef_rsw_t($inizio); ?></span><b><?php echo chef_rsw_t($fine); ?></b></div><?php endif; ?>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
