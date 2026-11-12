<?php get_template_part('parti/layout_open'); ?>

<?php
$nome_sezione = trim((string) get_sub_field('nome_sezione'));
$titolo       = trim((string) get_sub_field('titolo'));
$testo        = trim((string) get_sub_field('testo'));
$steps        = get_sub_field('step') ?: get_sub_field('steps') ?: get_sub_field('lista_step');

$normalizza_immagine_come_lavoriamo = function ($immagine) {
  $data = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($immagine)) {
    $data['url'] = isset($immagine['url']) ? trim((string) $immagine['url']) : '';
    $data['alt'] = isset($immagine['alt']) ? trim((string) $immagine['alt']) : '';
  } elseif (is_numeric($immagine)) {
    $immagine_id = (int) $immagine;
    $data['url'] = (string) wp_get_attachment_image_url($immagine_id, 'thumbnail');
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_scalar($immagine)) {
    $data['url'] = trim((string) $immagine);
  }

  return $data;
};

$normalizza_select_come_lavoriamo = function ($valore) {
  if (is_array($valore)) {
    return (string) ($valore['value'] ?? $valore['label'] ?? '');
  }

  return is_scalar($valore) ? (string) $valore : '';
};

$normalizza_colore_come_lavoriamo = function ($valore, $fallback = '') {
  $chiave = sanitize_title((string) $valore);
  $fallback = sanitize_title((string) $fallback);
  $mappa_colori = [
    'primary' => 'primary',
    'bordeaux' => 'primary',
    'analisi' => 'primary',
    'web' => 'web',
    'verde' => 'web',
    'strategia' => 'web',
    'grafica' => 'grafica',
    'rosso' => 'grafica',
    'design' => 'grafica',
    'design-sviluppo' => 'grafica',
    'foto' => 'foto',
    'viola' => 'foto',
    'seo' => 'foto',
    'misurazione' => 'foto',
    'seo-misurazione' => 'foto',
  ];

  if (isset($mappa_colori[$chiave])) {
    return $mappa_colori[$chiave];
  }

  if (isset($mappa_colori[$fallback])) {
    return $mappa_colori[$fallback];
  }

  return $chiave ?: $fallback;
};

$icone_come_lavoriamo = [
  'analisi' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="27" cy="27" r="16"/><path d="M39 39l13 13"/><path d="M22 22c2-3 6-5 10-4"/></svg>',
  'strategia' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="32" cy="32" r="22"/><circle cx="32" cy="32" r="13"/><circle cx="32" cy="32" r="5"/><path d="M39 25l12-12"/><path d="M44 13h7v7"/></svg>',
  'design' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><rect x="12" y="14" width="40" height="32" rx="3"/><path d="M12 22h40"/><path d="M25 31l-6 5 6 5"/><path d="M39 31l6 5-6 5"/><path d="M34 30l-4 13"/><path d="M26 52h12"/><path d="M32 46v6"/></svg>',
  'misurazione' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><rect x="12" y="37" width="8" height="15"/><rect x="28" y="27" width="8" height="25"/><rect x="44" y="13" width="8" height="39"/><path d="M10 54h44"/></svg>',
  'contatto' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="9"/><circle cx="44" cy="22" r="8"/><path d="M10 52c2-11 10-17 20-17s18 6 20 17"/><path d="M34 35c7 1 13 7 15 17"/></svg>',
  'esperienza' => '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M32 8l7 16 17 2-13 11 4 17-15-9-15 9 4-17L8 26l17-2z"/></svg>',
];

$render_icona_come_lavoriamo = function ($icona, $fallback, $alt = '') use ($normalizza_immagine_come_lavoriamo, $icone_come_lavoriamo) {
  $immagine = $normalizza_immagine_come_lavoriamo($icona);

  if (!empty($immagine['url'])) {
    return '<span class="come-lavoriamo-step-icon-mask" style="--come-lavoriamo-step-icon: url(\'' . esc_url($immagine['url']) . '\');" role="img" aria-label="' . esc_attr($immagine['alt'] ?: $alt) . '"></span>';
  }

  $chiave = is_string($fallback) && isset($icone_come_lavoriamo[$fallback]) ? $fallback : 'analisi';
  return $icone_come_lavoriamo[$chiave];
};

if (empty($steps) || !is_array($steps)) {
  $steps = [
    ['numero' => '01', 'titolo' => 'Analisi', 'testo' => 'Studiamo obiettivi, mercato e opportunita.', 'icona_tipo' => 'analisi', 'colore' => 'primary'],
    ['numero' => '02', 'titolo' => 'Strategia', 'testo' => 'Definiamo priorita, messaggi e struttura.', 'icona_tipo' => 'strategia', 'colore' => 'web'],
    ['numero' => '03', 'titolo' => 'Design & Sviluppo', 'testo' => 'Realizziamo siti e materiali chiari, veloci e coerenti.', 'icona_tipo' => 'design', 'colore' => 'grafica'],
    ['numero' => '04', 'titolo' => 'SEO & Misurazione', 'testo' => 'Ottimizziamo, monitoriamo e miglioriamo le performance.', 'icona_tipo' => 'misurazione', 'colore' => 'foto'],
  ];
}

?>

<?php if (!empty($nome_sezione)): ?>
  <div class="nome-sezione"><?php echo esc_html($nome_sezione); ?></div>
<?php endif; ?>

<section class="come-lavoriamo">
  <div class="come-lavoriamo-metodo">
    <div class="come-lavoriamo-copy">
      <?php if (!empty($titolo)): ?>
        <h2><?php echo wp_kses_post($titolo); ?></h2>
      <?php endif; ?>

      <?php if (!empty($testo)): ?>
        <div class="come-lavoriamo-intro"><?php echo wp_kses_post($testo); ?></div>
      <?php endif; ?>

      <?php
      $cl_link  = get_sub_field('link_cta');
      $cl_testo = trim((string) get_sub_field('testo_bottone'));
      $cl_url   = is_array($cl_link) ? ($cl_link['url'] ?? '') : (string) $cl_link;
      if ($cl_url !== '' && $cl_testo !== ''):
        $cl_tgt = is_array($cl_link) && !empty($cl_link['target']) ? $cl_link['target'] : '';
      ?>
        <p class="rsw-cl-cta"><a class="btn btn-super" href="<?php echo esc_url($cl_url); ?>"<?php echo $cl_tgt ? ' target="' . esc_attr($cl_tgt) . '" rel="noopener"' : ''; ?>><?php echo esc_html($cl_testo); ?></a></p>
      <?php endif; ?>
    </div>

    <div class="come-lavoriamo-steps">
      <?php foreach ($steps as $index => $step):
        $numero = $step['numero'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
        $titolo_step = $step['titolo'] ?? '';
        $testo_step = $step['testo'] ?? $step['descrizione'] ?? '';
        $icona_step = $step['icona'] ?? '';
        $icona_tipo = $normalizza_select_come_lavoriamo($step['icona_tipo'] ?? $step['tipo_icona'] ?? '');
        $colore = $normalizza_select_come_lavoriamo($step['colore'] ?? $step['tema'] ?? '');
        $classe_colore = sanitize_html_class($normalizza_colore_come_lavoriamo($colore, $icona_tipo) ?: 'step-' . ($index + 1));
      ?>
        <article class="come-lavoriamo-step come-lavoriamo-<?php echo esc_attr($classe_colore); ?>">
          <figure class="come-lavoriamo-step-icon">
            <?php echo $render_icona_come_lavoriamo($icona_step, $icona_tipo ?: 'analisi', wp_strip_all_tags((string) $titolo_step)); ?>
          </figure>

          <div class="come-lavoriamo-step-body">
            <span class="come-lavoriamo-step-number"><?php echo esc_html($numero); ?></span>
            <?php if (!empty($titolo_step)): ?>
              <h3><?php echo wp_kses_post($titolo_step); ?></h3>
            <?php endif; ?>
            <?php if (!empty($testo_step)): ?>
              <div class="come-lavoriamo-step-text"><?php echo wp_kses_post($testo_step); ?></div>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php get_template_part('parti/layout_close'); ?>
