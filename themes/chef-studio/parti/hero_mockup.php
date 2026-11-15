<?php
/**
 * Layout "Home · Hero con mockup e percorso del cliente". Campi in includes/home-layout-acf.php.
 * A destra: mockup a rotazione (solo il primo nel markup come immagine vera, gli altri li carica lo script
 * poco prima di mostrarli) e quattro schede del percorso. Le schede sono decorative (aria-hidden) e vuote
 * nell'HTML: i testi dei settori di esempio stanno nel JSON data-mockup e li scrive js/scripts-cs.js.
 */
get_template_part('parti/layout_open');
$b1 = get_sub_field('link_bottone_1') ?: [];
$b2 = get_sub_field('link_bottone_2') ?: [];
$prove = get_sub_field('prove') ?: [];
$mk = array_values(array_filter(get_sub_field('mockup') ?: [], function ($m) { return !empty($m['immagine']); }));
$fisse = [];
foreach (chef_rsw_righe((string) get_sub_field('tappe_fisse')) as $r) {
  $x = array_map('trim', explode('|', $r, 2));
  $fisse[] = [$x[0], $x[1] ?? ''];
}
$fisse = array_pad($fisse, 4, ['', '']);
$dati = [];
foreach ($mk as $k => $m) {
  $id = (int) $m['immagine'];
  $dati[] = [
    'src' => wp_get_attachment_image_url($id, 'full'),
    'set' => (string) wp_get_attachment_image_srcset($id, 'full'),
    'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
    'q' => wptexturize($m['ricerca'] ?? ''), 'a' => wptexturize($m['azione'] ?? ''), 'b' => wptexturize($m['terza'] ?? ''),
    'l' => wptexturize($m['leva'] ?? ''), 'i' => $m['icona'] ?? 'auto', 'n' => wptexturize($m['richiesta'] ?? ''),
  ];
}
$IC = [
  'q' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m20 20-4.8-4.8"/></svg>',
  'a' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l7 16 2.5-6.5L20 11z"/></svg>',
  'ai' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg>',
  'auto' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></svg>',
  'cont' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/></svg>',
  'form' => '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M12 18v-6M9 15l3-3 3 3"/></svg>',
];
?>
<div class="hp-hero hm-hero">
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
  <?php if ($dati) : ?>
  <div class="hm-comp" data-mockup="<?php echo esc_attr(wp_json_encode($dati)); ?>" data-icone="<?php echo esc_attr(wp_json_encode(['ai' => $IC['ai'], 'auto' => $IC['auto'], 'cont' => $IC['cont'], 'form' => $IC['form']])); ?>">
    <i class="hm-disco" aria-hidden="true"></i><i class="hm-anello" aria-hidden="true"></i><i class="hm-orb" aria-hidden="true"><b></b></i><i class="hm-suolo" aria-hidden="true"></i>
    <div class="hm-mks">
      <img class="hm-mk hm-on" src="<?php echo esc_url($dati[0]['src']); ?>" srcset="<?php echo esc_attr($dati[0]['set']); ?>" sizes="(max-width:700px) 92vw, 46vw" width="1200" height="829" alt="<?php echo esc_attr($dati[0]['alt']); ?>" fetchpriority="high" decoding="async">
    </div>
    <?php
    $tipi = ['q' => $IC['q'], 'a' => $IC['a'], 'b' => $IC[$dati[0]['i']] ?? $IC['auto'], 'n' => '✓'];
    $k = 0;
    foreach ($tipi as $chiave => $icona) :
      $classe = $chiave === 'b' && $dati[0]['i'] === 'ai' ? ' hm-ai' : ($chiave === 'n' ? ' hm-ok' : ''); ?>
      <div class="hm-chip hm-c<?php echo $k + 1; ?>" aria-hidden="true"><span class="hm-ci<?php echo $classe; ?>"><?php echo $icona; ?></span><span><small><?php echo chef_rsw_t($fisse[$k][0]); ?></small><b class="hm-t-<?php echo $chiave; ?>"></b></span><i class="hm-lev"><?php echo $chiave === 'b' ? '' : chef_rsw_t($fisse[$k][1]); ?></i></div>
    <?php $k++; endforeach; ?>
    <?php if ($d = get_sub_field('didascalia')) : ?><p class="hm-eti"><?php echo chef_rsw_t($d); ?></p><?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php get_template_part('parti/layout_close');
