<?php
/**
 * Layout "Schede con numeri e schermate" (pagine di servizio). Campi in includes/rsw-layout-acf.php.
 * Su computer la schermata si apre grande in una finestra (js/scripts-cs.js, link a.rsw-cwv-img).
 */
get_template_part('parti/layout_open');
$schede = get_sub_field('schede') ?: [];

chef_rsw_apri();
if ($schede) : ?>
<div class="rsw-cwv"><?php foreach ($schede as $s) :
  $img = $s['immagine'] ?? null; ?><div class="rsw-cwv-c"><?php if (!empty($s['etichetta'])) : ?><span class="rsw-cwv-k"><?php echo esc_html($s['etichetta']); ?></span><?php endif; ?><b><?php echo esc_html($s['numero'] ?? ''); ?></b><?php if (!empty($s['barra'])) : ?><i class="rsw-cwv-bar"></i><?php endif; ?><?php if (!empty($s['testo'])) : ?><p><?php echo esc_html($s['testo']); ?></p><?php endif; ?><?php if (!empty($s['nota'])) : ?><small><?php echo esc_html($s['nota']); ?></small><?php endif; ?><?php if (is_array($img) && !empty($img['url'])) : ?><a class="rsw-cwv-img" href="<?php echo esc_url($img['url']); ?>"><img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt'] ?? ''); ?>" loading="lazy" width="<?php echo (int) ($img['width'] ?? 0); ?>" height="<?php echo (int) ($img['height'] ?? 0); ?>"></a><?php endif; ?></div><?php endforeach; ?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
