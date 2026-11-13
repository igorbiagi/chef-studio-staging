<?php
/**
 * Layout "Home · Hero con percorso animato". Campi in includes/home-layout-acf.php.
 * Il pannello a destra è decorativo (aria-hidden): il testo della ricerca è nel data-testo e lo scrive scripts-cs.js.
 */
get_template_part('parti/layout_open');
$b1 = get_sub_field('link_bottone_1') ?: [];
$b2 = get_sub_field('link_bottone_2') ?: [];
$prove  = get_sub_field('prove') ?: [];
$numeri = get_sub_field('numeri') ?: [];
$ricerche = array_values(array_filter(get_sub_field('ricerche') ?: [], function ($r) { return !empty($r['ricerca']); }));
$ricerca  = $ricerche ? $ricerche[0]['ricerca'] : (string) get_sub_field('ricerca');
$risposta = $ricerche ? ($ricerche[0]['risposta'] ?? '') : (string) get_sub_field('risposta');
$notifica = $ricerche ? ($ricerche[0]['notifica'] ?? '') : (string) get_sub_field('notifica_titolo');
$dati_ricerche = array_map(function ($r) {
  return ['q' => wptexturize($r['ricerca']), 'r' => chef_hp_inline($r['risposta'] ?? ''), 'n' => wptexturize($r['notifica'] ?? '')];
}, $ricerche);
?>
<div class="hp-hero">
  <div class="hp-hero-testo">
    <?php if ($o = get_sub_field('occhiello')) : ?><p class="hp-occhiello"><?php echo chef_rsw_t($o); ?></p><?php endif; ?>
    <h1><?php echo wp_kses(wptexturize((string) get_sub_field('titolo')), ['em' => [], 'strong' => []]); ?></h1>
    <?php if ($t = get_sub_field('paragrafo')) : ?><p class="hp-lead"><?php echo chef_hp_inline($t); ?></p><?php endif; ?>
    <div class="hp-cta">
      <?php if (!empty($b1['url'])) : ?><a class="hp-b1" href="<?php echo esc_url($b1['url']); ?>"><?php echo chef_rsw_t(get_sub_field('testo_bottone_1')); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
      <?php if (!empty($b2['url'])) : ?><a class="hp-b2" href="<?php echo esc_url($b2['url']); ?>"<?php echo !empty($b2['target']) ? ' target="_blank" rel="noopener"' : ''; ?>><img src="<?php echo esc_url(get_template_directory_uri() . '/img/svg/whatsapp.svg'); ?>" alt="" width="20" height="20"><?php echo chef_rsw_t(get_sub_field('testo_bottone_2')); ?></a><?php endif; ?>
    </div>
    <?php if ($prove) : ?><div class="hp-prove"><?php foreach ($prove as $pr) : ?><div><?php if (!empty($pr['stelle'])) : ?><span class="hp-stelle" aria-label="5 stelle su 5">★★★★★</span><?php endif; ?><strong><?php echo chef_rsw_t($pr['titolo'] ?? ''); ?></strong><?php echo chef_rsw_t($pr['sotto'] ?? ''); ?></div><?php endforeach; ?></div><?php endif; ?>
  </div>
  <div class="hp-viaggio" aria-hidden="true"<?php echo count($dati_ricerche) > 1 ? " data-ricerche='" . esc_attr(wp_json_encode($dati_ricerche)) . "'" : ''; ?>>
    <div class="hp-filo"><i></i></div>
    <div class="hp-tappa hp-on"><div class="hp-et">1 · Il cliente cerca</div><div class="hp-cerca"><span class="hp-q" data-testo="<?php echo esc_attr($ricerca); ?>"><?php echo esc_html($ricerca); ?></span><span class="hp-cur"></span></div>
      <div class="hp-ai"><span class="hp-chip">AI</span><span class="hp-ai-t"><?php echo chef_hp_inline($risposta); ?></span></div></div>
    <div class="hp-tappa"><div class="hp-et">2 · Trova te e si fida</div><div class="hp-sito"><div class="hp-mini"></div><p><?php echo chef_hp_inline(get_sub_field('tappa_2')); ?></p></div></div>
    <div class="hp-tappa"><div class="hp-et">3 · Ti scrive</div><div class="hp-notifica"><div class="hp-ic">✓</div><div><strong class="hp-not-t"><?php echo chef_rsw_t($notifica); ?></strong><span><?php echo chef_rsw_t(get_sub_field('notifica_testo')); ?></span></div></div></div>
    <?php if ($d = get_sub_field('didascalia')) : ?><div class="hp-didascalia"><?php echo chef_rsw_t($d); ?></div><?php endif; ?>
  </div>
</div>
<?php if ($numeri) : ?><div class="hp-numeri"><?php foreach ($numeri as $n) : ?><div><b><?php echo chef_rsw_t($n['numero'] ?? ''); ?></b><span><?php echo chef_rsw_t($n['testo'] ?? ''); ?></span></div><?php endforeach; ?></div><?php endif; ?>
<?php get_template_part('parti/layout_close');
