<?php
/**
 * Layout "Schema di scambio tra due sistemi" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$flussi = get_sub_field('flussi') ?: [];
$nome   = get_sub_field('nome_sezione');
?>
<div class="testo-largo">
<?php if ($nome) : ?><div class="nome-sezione"><?php echo esc_html($nome); ?></div><?php endif; ?>
<div class="rsw-gest"><div class="rsw-gest-t"><?php echo chef_rsw_html(get_sub_field('testo')); ?></div><div class="rsw-gest-s" aria-hidden="true"><div class="rsw-gest-box"><?php echo esc_html(get_sub_field('sinistra_titolo')); ?><small><?php echo esc_html(get_sub_field('sinistra_sotto')); ?></small></div><div class="rsw-gest-f"><?php foreach ($flussi as $f) :
  $sx = ($f['verso'] ?? 'dx') === 'sx'; ?><span<?php echo $sx ? ' class="sx"' : ''; ?>><?php echo $sx ? '← ' . esc_html($f['testo'] ?? '') : esc_html($f['testo'] ?? '') . ' →'; ?></span><?php endforeach; ?></div><div class="rsw-gest-box scuro"><?php echo esc_html(get_sub_field('destra_titolo')); ?><small><?php echo esc_html(get_sub_field('destra_sotto')); ?></small></div></div></div>
<?php echo chef_rsw_html(get_sub_field('testo_dopo')); ?>
</div>
<?php get_template_part('parti/layout_close');
