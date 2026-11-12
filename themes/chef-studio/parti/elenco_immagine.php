<?php
/**
 * Layout "Elenco con spunte e immagine" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 */
get_template_part('parti/layout_open');
$punti = get_sub_field('punti') ?: [];
$img   = get_sub_field('immagine');
$did   = (string) get_sub_field('didascalia');
$nome  = get_sub_field('nome_sezione');
?>
<div class="testo-largo">
<?php if ($nome) : ?><div class="nome-sezione"><?php echo chef_rsw_t($nome); ?></div><?php endif; ?>
<div class="rsw-woo"><div class="rsw-woo-t"><?php echo chef_rsw_html(get_sub_field('testo_prima')); ?><?php if ($punti) : ?><ul class="rsw-woo-l"><?php foreach ($punti as $p) : ?><li><b><?php echo chef_rsw_t($p['titolo'] ?? ''); ?></b><?php echo wptexturize(wp_kses_post(preg_replace('#^\s*<p>(.*)</p>\s*$#s', '$1', (string) ($p['testo'] ?? '')))); ?></li><?php endforeach; ?></ul><?php endif; ?></div><?php if (is_array($img) && !empty($img['url'])) : ?><figure class="rsw-woo-img"><img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt'] ?? ''); ?>" loading="lazy" width="<?php echo (int) ($img['width'] ?? 0); ?>" height="<?php echo (int) ($img['height'] ?? 0); ?>"><?php if ($did !== '') : ?><figcaption><?php echo chef_rsw_t($did); ?></figcaption><?php endif; ?></figure><?php endif; ?></div>
<?php echo chef_rsw_html(get_sub_field('testo_dopo')); ?>
</div>
<?php get_template_part('parti/layout_close');
