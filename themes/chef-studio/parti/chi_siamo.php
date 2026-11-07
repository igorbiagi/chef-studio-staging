<?php get_template_part('parti/layout_open'); ?>

<?php
$nome_sezione       = get_sub_field('nome_sezione');
$titolo             = get_sub_field('titolo');
$testo              = get_sub_field('testo');
$punti              = get_sub_field('punti') ?: get_sub_field('metriche') ?: [];
$persone            = get_sub_field('persone') ?: get_sub_field('team') ?: [];
$link_cta           = get_sub_field('link_cta') ?: get_sub_field('link_bottone');
$testo_bottone      = get_sub_field('testo_bottone');
$testo_cta_supporto = get_sub_field('testo_cta_supporto') ?: get_sub_field('testo_supporto_cta');

$normalizza_immagine_chi_siamo = function ($immagine) {
  $data = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($immagine)) {
    $data['url'] = isset($immagine['url']) ? trim((string) $immagine['url']) : '';
    $data['alt'] = isset($immagine['alt']) ? trim((string) $immagine['alt']) : '';
  } elseif (is_numeric($immagine)) {
    $immagine_id = (int) $immagine;
    $data['url'] = (string) wp_get_attachment_image_url($immagine_id, 'large');
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_scalar($immagine)) {
    $data['url'] = trim((string) $immagine);
  }

  return $data;
};

$normalizza_link_chi_siamo = function ($link) {
  $data = [
    'url'    => '',
    'target' => '',
    'title'  => '',
  ];

  if (is_array($link)) {
    $data['url']    = isset($link['url']) ? trim((string) $link['url']) : '';
    $data['target'] = isset($link['target']) ? trim((string) $link['target']) : '';
    $data['title']  = isset($link['title']) ? trim((string) $link['title']) : '';
  } elseif (is_scalar($link)) {
    $data['url'] = trim((string) $link);
  }

  return $data;
};

$normalizza_ancora_schema = function ($ancora, $fallback = '') {
  $ancora = is_scalar($ancora) ? trim((string) $ancora) : '';

  if (empty($ancora) && !empty($fallback)) {
    $ancora = sanitize_title($fallback);
  }

  return sanitize_title($ancora);
};

$normalizza_testo_schema = function ($testo) {
  $testo = wp_strip_all_tags((string) $testo);
  $testo = html_entity_decode($testo, ENT_QUOTES, 'UTF-8');
  $testo = preg_replace('/\s+/', ' ', $testo);

  return trim($testo);
};

$normalizza_lista_schema = function ($valore) use ($normalizza_testo_schema) {
  if (empty($valore)) {
    return [];
  }

  if (is_array($valore)) {
    $lista = [];

    foreach ($valore as $item) {
      if (is_array($item)) {
        $item = $item['testo'] ?? $item['nome'] ?? $item['titolo'] ?? '';
      }

      $item = $normalizza_testo_schema($item);

      if (!empty($item)) {
        $lista[] = $item;
      }
    }

    return array_values(array_unique($lista));
  }

  $valore = str_replace(['<br>', '<br/>', '<br />'], ',', (string) $valore);
  $parti = preg_split('/[,;\n]+/', $valore);

  $lista = [];

  foreach ($parti as $parte) {
    $parte = $normalizza_testo_schema($parte);

    if (!empty($parte)) {
      $lista[] = $parte;
    }
  }

  return array_values(array_unique($lista));
};

$cta = $normalizza_link_chi_siamo($link_cta);

if (empty($testo_bottone) && !empty($cta['title'])) {
  $testo_bottone = $cta['title'];
}
?>

<section class="chi-siamo-home">
  <div class="chi-siamo-home-grid">

    <div class="chi-siamo-home-copy">
      <?php if (!empty($nome_sezione)): ?>
        <div class="nome-sezione"><?php echo esc_html($nome_sezione); ?></div>
      <?php endif; ?>

      <?php if (!empty($titolo)): ?>
        <div class="chi-siamo-home-title">
          <?php if ($titolo === wp_strip_all_tags($titolo)): ?>
            <h2><?php echo esc_html($titolo); ?></h2>
          <?php else: ?>
            <?php echo wp_kses_post($titolo); ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($testo)): ?>
        <div class="chi-siamo-home-text">
          <?php echo wp_kses_post($testo); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($punti) && is_array($punti)): ?>
        <div class="chi-siamo-home-punti">
          <?php foreach ($punti as $punto):
            $icona_punto  = $normalizza_immagine_chi_siamo($punto['icona'] ?? '');
            $titolo_punto = $punto['titolo'] ?? '';
            $testo_punto  = $punto['testo'] ?? $punto['descrizione'] ?? '';
          ?>
            <article class="chi-siamo-home-punto">
              <?php if (!empty($icona_punto['url'])): ?>
                <figure class="chi-siamo-home-punto-icon">
                  <span
                    class="chi-siamo-home-punto-icon-mask"
                    style="--chi-siamo-punto-icon: url('<?php echo esc_url($icona_punto['url']); ?>');"
                    role="img"
                    aria-label="<?php echo esc_attr($icona_punto['alt'] ?: wp_strip_all_tags((string) $titolo_punto)); ?>"
                  ></span>
                </figure>
              <?php endif; ?>

              <div>
                <?php if (!empty($titolo_punto)): ?>
                  <strong><?php echo wp_kses_post($titolo_punto); ?></strong>
                <?php endif; ?>

                <?php if (!empty($testo_punto)): ?>
                  <div class="chi-siamo-home-punto-text"><?php echo wp_kses_post($testo_punto); ?></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($cta['url']) && !empty($testo_bottone)): ?>
        <div class="chi-siamo-home-cta">
          <a
            href="<?php echo esc_url($cta['url']); ?>"
            class="btn btn-super chi-siamo-home-button"
            <?php if (!empty($cta['target'])): ?>target="<?php echo esc_attr($cta['target']); ?>"<?php endif; ?>
            <?php if ($cta['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
          >
            <span><?php echo esc_html($testo_bottone); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>

          <?php if (!empty($testo_cta_supporto)): ?>
            <div class="chi-siamo-home-cta-note">
              <?php echo wp_kses_post($testo_cta_supporto); ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($persone) && is_array($persone)): ?>
      <div class="chi-siamo-home-persone">
        <?php foreach ($persone as $persona):
          $immagine_persona = $normalizza_immagine_chi_siamo($persona['immagine'] ?? $persona['foto'] ?? '');
          $nome_persona     = $persona['nome'] ?? '';
          $ruolo_persona    = $persona['ruolo'] ?? '';
          $testo_persona    = $persona['testo'] ?? $persona['descrizione'] ?? '';
          $persona_schema_slug = function_exists('chefstudio_schema_person_slug_from_text')
            ? chefstudio_schema_person_slug_from_text($nome_persona)
            : '';
          $ancora_persona   = $persona_schema_slug ?: $normalizza_ancora_schema($persona['ancora_schema'] ?? '', $nome_persona);
        ?>
          <article
            class="chi-siamo-home-persona"
            <?php if (!empty($ancora_persona)): ?>id="<?php echo esc_attr($ancora_persona); ?>"<?php endif; ?>
          >
            <?php if (!empty($immagine_persona['url'])): ?>
              <figure class="chi-siamo-home-persona-img">
                <img src="<?php echo esc_url($immagine_persona['url']); ?>" alt="<?php echo esc_attr($immagine_persona['alt'] ?: wp_strip_all_tags((string) $nome_persona)); ?>">
              </figure>
            <?php endif; ?>

            <div class="chi-siamo-home-persona-body">
              <?php if (!empty($nome_persona)): ?>
                <h3><?php echo esc_html($nome_persona); ?></h3>
              <?php endif; ?>

              <?php if (!empty($ruolo_persona)): ?>
                <div class="chi-siamo-home-persona-role"><?php echo wp_kses_post($ruolo_persona); ?></div>
              <?php endif; ?>

              <?php if (!empty($testo_persona)): ?>
                <div class="chi-siamo-home-persona-text"><?php echo wp_kses_post($testo_persona); ?></div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>


<?php
/**
 * Schema Person generato dal repeater ACF "persone".
 * Viene stampato solo in home e solo se ci sono persone compilate.
 */
if (is_front_page() && !empty($persone) && is_array($persone)):

  $persone_schema = [];

  foreach ($persone as $persona) {
    $nome_persona = $persona['nome'] ?? '';

    if (empty($nome_persona)) {
      continue;
    }

    $immagine_persona = $normalizza_immagine_chi_siamo($persona['immagine'] ?? $persona['foto'] ?? '');
    $ruolo_persona    = $persona['ruolo'] ?? '';
    $testo_persona    = $persona['testo'] ?? $persona['descrizione'] ?? '';

    $persona_schema_slug = function_exists('chefstudio_schema_person_slug_from_text')
      ? chefstudio_schema_person_slug_from_text($nome_persona)
      : '';
    $ancora_persona = $persona_schema_slug ?: $normalizza_ancora_schema($persona['ancora_schema'] ?? '', $nome_persona);

    $persona_schema = [];

    if (!empty($persona_schema_slug) && function_exists('chefstudio_schema_person')) {
      $persona_schema = chefstudio_schema_person($persona_schema_slug);
    }

    if (empty($persona_schema)) {
      $persona_schema = [
        '@type' => 'Person',
        '@id'   => home_url('/#' . $ancora_persona),
        'name'  => $normalizza_testo_schema($nome_persona),
        'worksFor' => [
          '@id' => function_exists('chefstudio_schema_organization_id')
            ? chefstudio_schema_organization_id()
            : home_url('/#organization'),
        ],
      ];
    }

    if (!empty($ruolo_persona)) {
      $job_titles = $normalizza_lista_schema($ruolo_persona);

      if (!empty($job_titles)) {
        $persona_schema['jobTitle'] = $job_titles;
      }
    }

    if (!empty($immagine_persona['url'])) {
      $persona_schema['image'] = esc_url_raw($immagine_persona['url']);
    }

    if (!empty($testo_persona)) {
      $persona_schema['description'] = $normalizza_testo_schema($testo_persona);
    }

    /**
     * Campi ACF opzionali consigliati dentro il repeater "persone":
     *
     * - formazione_schema
     * - competenze_schema
     */
    if (!empty($persona['formazione_schema'])) {
      $persona_schema['alumniOf'] = [
        '@type' => 'EducationalOrganization',
        'name'  => $normalizza_testo_schema($persona['formazione_schema']),
      ];
    }

    if (!empty($persona['competenze_schema'])) {
      $competenze = $normalizza_lista_schema($persona['competenze_schema']);

      if (!empty($competenze)) {
        $persona_schema['knowsAbout'] = $competenze;
      }
    }

    $persone_schema[] = $persona_schema;
  }

  if (!empty($persone_schema)) {
    $schema = [
      '@context' => 'https://schema.org',
      '@graph'   => $persone_schema,
    ];

    echo "\n<script type=\"application/ld+json\">" .
      wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
      "</script>\n";
  }

endif;
?>

<?php get_template_part('parti/layout_close'); ?>
