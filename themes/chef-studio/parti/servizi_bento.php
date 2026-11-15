<?php
/**
 * Layout "Home · Servizi a griglia". Campi in includes/home-layout-acf.php, stili hb- in chef-custom.css.
 * Griglia: 1° servizio grande chiaro (alto due righe), 2° largo scuro, 3° e 4° piccoli.
 */
get_template_part('parti/layout_open');
$bt = get_sub_field('link_bottone') ?: [];
$servizi = get_sub_field('servizi') ?: [];
$classi = ['hb-g', 'hb-m', 'hb-p', 'hb-p'];
$freccia = '<svg viewBox="0 0 34 12" width="30" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 6h31M27 1l5 5-5 5"/></svg>';
$giu = '<svg viewBox="0 0 12 20" width="12" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 1v17M1 13l5 5 5-5"/></svg>';
?>
<div class="hb-servizi">
  <div class="hb-sx">
    <?php if ($n = get_sub_field('nome_sezione')) : ?><div class="nome-sezione"><?php echo esc_html($n); ?></div><?php endif; ?>
    <?php if ($t = get_sub_field('titolo')) : ?><h2><?php echo chef_rsw_t($t); ?></h2><?php endif; ?>
    <?php if ($t = get_sub_field('testo')) : ?><p><?php echo chef_hp_inline($t); ?></p><?php endif; ?>
    <?php if (!empty($bt['url'])) : ?><a class="hb-btn" href="<?php echo esc_url($bt['url']); ?>"><?php echo chef_rsw_t(get_sub_field('testo_bottone')); ?> <?php echo $freccia; ?></a><?php endif; ?>
  </div>
  <div class="hb-dx">
    <div class="hb-grid">
      <?php foreach ($servizi as $k => $s) :
        $l = $s['link'] ?? [];
        $url = $l['url'] ?? '';
        $ancora = strpos($url, '#') === 0;
        $tag = $url ? 'a' : 'div';
        $et = array_filter(array_map('trim', explode('|', (string) ($s['etichette'] ?? '')))); ?>
        <<?php echo $tag; ?> class="hb-cell <?php echo $classi[$k] ?? 'hb-p'; ?><?php echo $ancora ? ' hb-giu' : ''; ?>"<?php echo $url ? ' href="' . esc_url($url) . '"' : ''; ?>>
          <div class="hb-top"><?php echo chef_hp_icona($s['icona'] ?? ''); ?><span class="hb-vai"><?php echo $ancora ? $giu : $freccia; ?></span></div>
          <div>
            <h3><?php echo wp_kses(wptexturize((string) ($s['titolo'] ?? '')), ['mark' => []]); ?></h3>
            <?php if (!empty($s['testo'])) : ?><p><?php echo chef_hp_inline($s['testo']); ?></p><?php endif; ?>
            <?php if ($et) : ?><ul><?php foreach ($et as $e) : ?><li><?php echo esc_html($e); ?></li><?php endforeach; ?></ul><?php endif; ?>
          </div>
        </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
    <?php if ($t = get_sub_field('nota')) : ?><p class="hb-nota"><?php echo chef_hp_inline($t); ?></p><?php endif; ?>
    <?php $sotto = get_sub_field('mostra_sottoservizi') ? chef_hp_link_righe(get_sub_field('sottoservizi')) : [];
    if ($sotto) : ?><div class="hb-sotto"><span><?php echo chef_rsw_t(get_sub_field('titolo_sottoservizi') ?: 'In evidenza'); ?></span><ul><?php foreach ($sotto as [$t, $u]) : ?><li><a href="<?php echo esc_url($u); ?>"><?php echo chef_rsw_t($t); ?></a></li><?php endforeach; ?></ul></div><?php endif; ?>
  </div>
</div>
<?php get_template_part('parti/layout_close');
