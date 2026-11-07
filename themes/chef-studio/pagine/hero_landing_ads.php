<?php
$hero_landing_ads_get = function ($name) {
  return function_exists('get_sub_field') ? get_sub_field($name) : '';
};

$hero_landing_ads_text = function ($value) {
  if (is_array($value)) {
    $value = $value['value'] ?? $value['label'] ?? $value['title'] ?? '';
  }

  return is_scalar($value) ? trim((string) $value) : '';
};

$hero_landing_ads_pick = function (array $names) use ($hero_landing_ads_get, $hero_landing_ads_text) {
  foreach ($names as $name) {
    $value = $hero_landing_ads_text($hero_landing_ads_get($name));

    if ($value !== '') {
      return $value;
    }
  }

  return '';
};

$hero_landing_ads_pick_raw = function (array $names) use ($hero_landing_ads_get) {
  foreach ($names as $name) {
    $value = $hero_landing_ads_get($name);

    if (!empty($value)) {
      return $value;
    }
  }

  return null;
};

$hero_landing_ads_button = function ($link, $label = '') use ($hero_landing_ads_text) {
  $button = [
    'url'    => '',
    'label'  => $hero_landing_ads_text($label),
    'target' => '',
    'rel'    => '',
  ];

  if (is_array($link)) {
    $button['url'] = isset($link['url']) ? trim((string) $link['url']) : '';

    if ($button['label'] === '' && !empty($link['title'])) {
      $button['label'] = trim((string) $link['title']);
    }

    $button['target'] = isset($link['target']) ? trim((string) $link['target']) : '';
  } elseif (is_scalar($link)) {
    $button['url'] = trim((string) $link);
  }

  if ($button['target'] === '_blank') {
    $button['rel'] = 'noopener noreferrer';
  }

  return $button;
};

$hero_landing_ads_image_data = function ($image) {
  $data = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($image)) {
    $data['url'] = isset($image['url']) ? trim((string) $image['url']) : '';
    $data['alt'] = isset($image['alt']) ? trim((string) $image['alt']) : '';
  } elseif (is_numeric($image)) {
    $image_id = (int) $image;
    $data['url'] = function_exists('wp_get_attachment_image_url') ? (string) wp_get_attachment_image_url($image_id, 'full') : '';
    $data['alt'] = function_exists('get_post_meta') ? (string) get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
  } elseif (is_scalar($image)) {
    $data['url'] = trim((string) $image);
  }

  return $data;
};

$hero_landing_ads_icon = function ($type = 'check') {
  $type = sanitize_title((string) $type);
  $icons = [
    'utente' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>',
    'codice' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m8 9-4 3 4 3"/><path d="m16 9 4 3-4 3"/><path d="m14 5-4 14"/></svg>',
    'dati' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/></svg>',
    'telefono' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.09 5.18 2 2 0 0 1 5.11 3h3a2 2 0 0 1 2 1.72c.12.9.32 1.78.6 2.63a2 2 0 0 1-.45 2.11L9 10.7a16 16 0 0 0 4.3 4.3l1.24-1.26a2 2 0 0 1 2.11-.45c.85.28 1.73.48 2.63.6A2 2 0 0 1 22 16.92Z"/></svg>',
    'sicurezza' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m20 6-11 11-5-5"/></svg>',
  ];

  return $icons[$type] ?? $icons['check'];
};

$hero_landing_ads_logo = $hero_landing_ads_pick_raw(['logo', 'logo_header', 'immagine_logo']);
$hero_landing_ads_logo_data = $hero_landing_ads_image_data($hero_landing_ads_logo);

if ($hero_landing_ads_logo_data['url'] === '' && function_exists('get_field')) {
  $hero_landing_ads_theme_logo = get_field('logo', 'option');
  $hero_landing_ads_theme_logo_data = $hero_landing_ads_image_data($hero_landing_ads_theme_logo);

  if ($hero_landing_ads_theme_logo_data['url'] !== '') {
    $hero_landing_ads_logo = $hero_landing_ads_theme_logo;
    $hero_landing_ads_logo_data = $hero_landing_ads_theme_logo_data;
  }
}

$hero_landing_ads_logo_text = $hero_landing_ads_pick(['testo_logo', 'logo_testo', 'brand']);

if ($hero_landing_ads_logo_text === '') {
  $hero_landing_ads_logo_text = function_exists('get_bloginfo') ? trim((string) get_bloginfo('name')) : '';
}

if ($hero_landing_ads_logo_text === '') {
  $hero_landing_ads_logo_text = 'Chef Studio';
}

$hero_landing_ads_eyebrow = $hero_landing_ads_pick(['eyebrow', 'nome_sezione', 'categoria_hero']);
$hero_landing_ads_title = $hero_landing_ads_pick(['titolo_1', 'titolo', 'h1']);
$hero_landing_ads_subtitle = $hero_landing_ads_pick(['sottotitolo_1', 'sottotitolo', 'headline']);
$hero_landing_ads_text_content = $hero_landing_ads_pick(['paragrafo', 'testo', 'descrizione']);

if ($hero_landing_ads_eyebrow === '') {
  $hero_landing_ads_eyebrow = 'Consulenza SEO per aziende ed e-commerce';
}

if ($hero_landing_ads_title === '') {
  $hero_landing_ads_title = 'Consulenza SEO per trasformare la visibilità su Google in business.';
}

if ($hero_landing_ads_text_content === '') {
  $hero_landing_ads_text_content = '<p>Analizzo domanda, concorrenza, struttura e contenuti per capire dove intervenire e cosa ha davvero priorità.</p><p><strong>Dalla strategia agli interventi diretti su WordPress e WooCommerce.</strong></p>';
}

$hero_landing_ads_form_id = absint($hero_landing_ads_pick(['gravity_form_id', 'form_id', 'id_form', 'id_modulo']));

if ($hero_landing_ads_form_id <= 0) {
  $hero_landing_ads_form_id = 5;
}

$hero_landing_ads_form_shortcode = $hero_landing_ads_pick(['shortcode_form', 'form_shortcode', 'shortcode_modulo']);
$hero_landing_ads_has_form = $hero_landing_ads_form_shortcode !== '' || ($hero_landing_ads_form_id > 0 && function_exists('gravity_form'));
$hero_landing_ads_unique_suffix = function_exists('wp_unique_id') ? wp_unique_id() : uniqid('', false);
$hero_landing_ads_form_anchor = 'hero-landing-ads-form-' . $hero_landing_ads_unique_suffix;
$hero_landing_ads_show_form_preview_value = sanitize_title($hero_landing_ads_pick(['mostra_preview_form', 'mostra_form_demo', 'preview_form']));
$hero_landing_ads_show_form_preview = !in_array($hero_landing_ads_show_form_preview_value, ['0', 'no', 'false', 'nascondi'], true);

$hero_landing_ads_dynamic_values = [];

foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid'] as $query_key) {
  if (!isset($_GET[$query_key])) {
    continue;
  }

  $query_value = wp_unslash($_GET[$query_key]);

  if (!is_scalar($query_value)) {
    continue;
  }

  $hero_landing_ads_dynamic_values[$query_key] = sanitize_text_field((string) $query_value);
}

if (function_exists('get_permalink')) {
  $hero_landing_ads_dynamic_values['landing_page_url'] = get_permalink();
}

if (function_exists('get_the_title')) {
  $hero_landing_ads_dynamic_values['landing_page_title'] = get_the_title();
}

$hero_landing_ads_dynamic_values = array_filter($hero_landing_ads_dynamic_values, function ($value) {
  return trim((string) $value) !== '';
});

$hero_landing_ads_field_values = !empty($hero_landing_ads_dynamic_values)
  ? http_build_query($hero_landing_ads_dynamic_values, '', '&')
  : '';

$hero_landing_ads_primary_button = $hero_landing_ads_button(
  $hero_landing_ads_pick_raw(['link_bottone_1', 'link_cta', 'cta_link']),
  $hero_landing_ads_pick(['testo_bottone_1', 'testo_cta', 'cta_label'])
);

if ($hero_landing_ads_primary_button['url'] === '' && $hero_landing_ads_primary_button['label'] !== '' && ($hero_landing_ads_has_form || $hero_landing_ads_show_form_preview)) {
  $hero_landing_ads_primary_button['url'] = '#' . $hero_landing_ads_form_anchor;
}

if ($hero_landing_ads_primary_button['label'] === '') {
  $hero_landing_ads_primary_button['label'] = 'Presenta il progetto';
}

if ($hero_landing_ads_primary_button['url'] === '') {
  $hero_landing_ads_primary_button['url'] = $hero_landing_ads_has_form || $hero_landing_ads_show_form_preview ? '#' . $hero_landing_ads_form_anchor : '#form-ancora';
}

$hero_landing_ads_has_primary_button = $hero_landing_ads_primary_button['url'] !== '' && $hero_landing_ads_primary_button['label'] !== '';
$hero_landing_ads_mobile_button_label = $hero_landing_ads_pick(['testo_bottone_mobile', 'testo_cta_mobile', 'cta_label_mobile']);

if ($hero_landing_ads_mobile_button_label === '') {
  $hero_landing_ads_mobile_button_label = 'Contattaci';
}

$hero_landing_ads_phone = function_exists('get_field') ? trim((string) get_field('telefono', 'option')) : '';
$hero_landing_ads_phone_label = $hero_landing_ads_pick(['testo_chiama', 'testo_telefono', 'telefono_testo']);
$hero_landing_ads_phone_link = $hero_landing_ads_phone !== '' ? preg_replace('/[^0-9+]/', '', $hero_landing_ads_phone) : '';
$hero_landing_ads_trust_text = $hero_landing_ads_pick(['testo_trust', 'trust_text', 'nota_fiducia']);

if ($hero_landing_ads_phone === '') {
  $hero_landing_ads_phone = '+39 352 0996 996';
  $hero_landing_ads_phone_link = preg_replace('/[^0-9+]/', '', $hero_landing_ads_phone);
}

if ($hero_landing_ads_phone_label === '') {
  $hero_landing_ads_phone_label = 'Preferisci parlare?';
}

if ($hero_landing_ads_trust_text === '') {
  $hero_landing_ads_trust_text = 'Niente bot: parli direttamente con noi.';
}

$hero_landing_ads_photo = $hero_landing_ads_pick_raw(['immagine', 'foto_consulente', 'immagine_consulente', 'foto']);
$hero_landing_ads_photo_data = $hero_landing_ads_image_data($hero_landing_ads_photo);

if ($hero_landing_ads_photo_data['url'] === '' && function_exists('get_stylesheet_directory_uri')) {
  $hero_landing_ads_photo = get_stylesheet_directory_uri() . '/img/seo/igor-biagi-consulente-seo-landing-tonda.png';
  $hero_landing_ads_photo_data = [
    'url' => $hero_landing_ads_photo,
    'alt' => 'Igor Biagi consulente SEO',
  ];
}

$hero_landing_ads_photo_alt = $hero_landing_ads_photo_data['alt'] ?: wp_strip_all_tags($hero_landing_ads_title);
$hero_landing_ads_consultant_name = $hero_landing_ads_pick(['nome_consulente', 'nome_autore', 'nome']);
$hero_landing_ads_consultant_role = $hero_landing_ads_pick(['ruolo_consulente', 'ruolo_autore', 'ruolo']);

if ($hero_landing_ads_consultant_name === '') {
  $hero_landing_ads_consultant_name = 'Igor Biagi';
}

if ($hero_landing_ads_consultant_role === '') {
  $hero_landing_ads_consultant_role = 'Consulente SEO - Chef Studio';
}

$hero_landing_ads_benefits_rows = $hero_landing_ads_pick_raw(['benefit', 'benefits', 'punti_di_forza', 'vantaggi']);
$hero_landing_ads_benefits = [];

if (is_array($hero_landing_ads_benefits_rows)) {
  foreach ($hero_landing_ads_benefits_rows as $row) {
    if (!is_array($row)) {
      continue;
    }

    $benefit_title = $hero_landing_ads_text($row['titolo'] ?? $row['titolo_benefit'] ?? $row['title'] ?? '');
    $benefit_text = $hero_landing_ads_text($row['testo'] ?? $row['descrizione'] ?? $row['description'] ?? '');
    $benefit_icon = $hero_landing_ads_image_data($row['icona'] ?? $row['icon'] ?? null);
    $benefit_icon_type = $hero_landing_ads_text($row['icona_tipo'] ?? $row['tipo_icona'] ?? $row['icon_type'] ?? 'check');

    if ($benefit_title === '' && $benefit_text === '') {
      continue;
    }

    $hero_landing_ads_benefits[] = [
      'title' => $benefit_title,
      'text' => $benefit_text,
      'icon_url' => $benefit_icon['url'],
      'icon_alt' => $benefit_icon['alt'],
      'icon_type' => $benefit_icon_type,
    ];
  }
}

if (empty($hero_landing_ads_benefits)) {
  $hero_landing_ads_benefits = [
    [
      'title' => 'Referente diretto',
      'text' => 'Parli con chi segue realmente il progetto.',
      'icon_url' => '',
      'icon_alt' => '',
      'icon_type' => 'utente',
    ],
    [
      'title' => 'SEO + WordPress/WooCommerce',
      'text' => 'Strategia e implementazione possono stare nello stesso progetto.',
      'icon_url' => '',
      'icon_alt' => '',
      'icon_type' => 'codice',
    ],
    [
      'title' => 'Priorità basate sui dati',
      'text' => 'Prima capiamo dove conviene investire, poi interveniamo.',
      'icon_url' => '',
      'icon_alt' => '',
      'icon_type' => 'dati',
    ],
  ];
}

$hero_landing_ads_form_title = $hero_landing_ads_pick(['titolo_form', 'form_titolo', 'titolo_modulo']);
$hero_landing_ads_form_text = $hero_landing_ads_pick(['testo_form', 'form_testo', 'testo_modulo']);
$hero_landing_ads_form_note = $hero_landing_ads_pick(['nota_form', 'form_nota', 'nota_privacy_form']);
$hero_landing_ads_form_step = $hero_landing_ads_pick(['step_form', 'testo_step_form', 'form_step_label']);
$hero_landing_ads_has_form_content = $hero_landing_ads_form_title !== '' || $hero_landing_ads_form_text !== '' || $hero_landing_ads_form_note !== '';
$hero_landing_ads_has_form_card = $hero_landing_ads_has_form || $hero_landing_ads_show_form_preview || $hero_landing_ads_has_form_content;

if ($hero_landing_ads_has_form_card && $hero_landing_ads_form_title === '') {
  $hero_landing_ads_form_title = 'Raccontami il progetto';
}

if ($hero_landing_ads_has_form_card && $hero_landing_ads_form_text === '') {
  $hero_landing_ads_form_text = 'Mi bastano poche informazioni per capire il contesto e dirti se ha senso parlarne. La richiesta viene letta da noi, non da un bot.';
}

if ($hero_landing_ads_has_form_card && $hero_landing_ads_form_note === '') {
  $hero_landing_ads_form_note = 'Nessun report automatico: uso questi dati solo per valutare la richiesta e ricontattarti. Non li condividiamo con terzi.';
}

if ($hero_landing_ads_has_form_card && $hero_landing_ads_form_step === '') {
  $hero_landing_ads_form_step = '1 di 2';
}

$hero_landing_ads_heading_id = $hero_landing_ads_title !== '' ? 'hero-landing-ads-title-' . $hero_landing_ads_unique_suffix : '';
?>

<?php if ($hero_landing_ads_logo_data['url'] !== '' || $hero_landing_ads_logo_text !== '' || $hero_landing_ads_has_primary_button): ?>
  <header class="hero-landing-ads-sitebar hero-header-enhanced" aria-label="Landing page">
    <div class="hero-landing-ads-sitebar-inner">
      <?php if ($hero_landing_ads_logo_data['url'] !== '' || $hero_landing_ads_logo_text !== ''): ?>
        <a class="hero-landing-ads-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($hero_landing_ads_logo_text); ?>">
          <?php if ($hero_landing_ads_logo_data['url'] !== '' && function_exists('chefstudio_responsive_image')): ?>
            <?php echo chefstudio_responsive_image($hero_landing_ads_logo, 'medium', [
              'class' => 'hero-landing-ads-logo',
              'alt' => $hero_landing_ads_logo_data['alt'] ?: $hero_landing_ads_logo_text,
              'loading' => 'eager',
              'decoding' => 'async',
              'sizes' => '200px',
            ]); ?>
          <?php elseif ($hero_landing_ads_logo_data['url'] !== ''): ?>
            <img class="hero-landing-ads-logo" src="<?php echo esc_url($hero_landing_ads_logo_data['url']); ?>" alt="<?php echo esc_attr($hero_landing_ads_logo_data['alt'] ?: $hero_landing_ads_logo_text); ?>" loading="eager" decoding="async">
          <?php elseif ($hero_landing_ads_logo_text !== ''): ?>
            <span><?php echo esc_html($hero_landing_ads_logo_text); ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if ($hero_landing_ads_has_primary_button): ?>
        <div class="hero-landing-ads-top-actions hero-landing-ads-sitebar-actions">
          <a
            href="<?php echo esc_url($hero_landing_ads_primary_button['url']); ?>"
            class="btn btn-super hero-action-link hero-action-link-primary hero-landing-ads-top-cta"
            <?php if ($hero_landing_ads_primary_button['target'] !== ''): ?>target="<?php echo esc_attr($hero_landing_ads_primary_button['target']); ?>"<?php endif; ?>
            <?php if ($hero_landing_ads_primary_button['rel'] !== ''): ?>rel="<?php echo esc_attr($hero_landing_ads_primary_button['rel']); ?>"<?php endif; ?>
          >
            <span class="hero-landing-ads-cta-label-full"><?php echo esc_html($hero_landing_ads_primary_button['label']); ?></span>
            <span class="hero-landing-ads-cta-label-mobile"><?php echo esc_html($hero_landing_ads_mobile_button_label); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </header>
  <?php if ($hero_landing_ads_has_primary_button): ?>
    <script>
      (function () {
        var threshold = 220;

        function syncLandingHeaderCta() {
          document.body.classList.toggle('hero-landing-ads-scrolled', window.scrollY > threshold);
        }

        document.body.classList.add('hero-landing-ads-scroll-ready');
        syncLandingHeaderCta();
        window.addEventListener('scroll', syncLandingHeaderCta, { passive: true });
        window.addEventListener('resize', syncLandingHeaderCta);
      }());
    </script>
  <?php endif; ?>
<?php endif; ?>

<?php get_template_part('parti/layout_open'); ?>

<section class="hero-landing-ads hero-header-enhanced"<?php if ($hero_landing_ads_heading_id !== ''): ?> aria-labelledby="<?php echo esc_attr($hero_landing_ads_heading_id); ?>"<?php endif; ?>>

  <div class="hero-landing-ads-grid<?php echo $hero_landing_ads_photo_data['url'] === '' ? ' hero-landing-ads-grid-no-photo' : ''; ?><?php echo !$hero_landing_ads_has_form_card ? ' hero-landing-ads-grid-no-form' : ''; ?>">
    <div class="hero-landing-ads-copy">
      <?php if ($hero_landing_ads_eyebrow !== ''): ?>
        <p class="hero-landing-ads-eyebrow"><?php echo esc_html($hero_landing_ads_eyebrow); ?></p>
      <?php endif; ?>

      <?php if ($hero_landing_ads_title !== ''): ?>
        <h1 id="<?php echo esc_attr($hero_landing_ads_heading_id); ?>"><?php echo wp_kses_post($hero_landing_ads_title); ?></h1>
      <?php endif; ?>

      <?php if ($hero_landing_ads_subtitle !== ''): ?>
        <div class="hero-landing-ads-subtitle"><?php echo wp_kses_post($hero_landing_ads_subtitle); ?></div>
      <?php endif; ?>

      <?php if ($hero_landing_ads_text_content !== ''): ?>
        <div class="hero-landing-ads-text"><?php echo wp_kses_post($hero_landing_ads_text_content); ?></div>
      <?php endif; ?>

      <?php if (!empty($hero_landing_ads_benefits)): ?>
        <ul class="hero-landing-ads-benefits">
          <?php foreach ($hero_landing_ads_benefits as $benefit): ?>
            <li>
              <span class="hero-landing-ads-benefit-icon">
                <?php if ($benefit['icon_url'] !== ''): ?>
                  <img src="<?php echo esc_url($benefit['icon_url']); ?>" alt="<?php echo esc_attr($benefit['icon_alt']); ?>" loading="lazy" decoding="async">
                <?php else: ?>
                  <?php echo $hero_landing_ads_icon($benefit['icon_type']); ?>
                <?php endif; ?>
              </span>

              <span class="hero-landing-ads-benefit-copy">
                <?php if ($benefit['title'] !== ''): ?>
                  <strong><?php echo esc_html($benefit['title']); ?></strong>
                <?php endif; ?>
                <?php if ($benefit['text'] !== ''): ?>
                  <span><?php echo esc_html($benefit['text']); ?></span>
                <?php endif; ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if ($hero_landing_ads_has_primary_button): ?>
        <div class="button-container hero-actions hero-landing-ads-main-action">
          <a
            href="<?php echo esc_url($hero_landing_ads_primary_button['url']); ?>"
            class="btn btn-super hero-action-link hero-action-link-primary"
            <?php if ($hero_landing_ads_primary_button['target'] !== ''): ?>target="<?php echo esc_attr($hero_landing_ads_primary_button['target']); ?>"<?php endif; ?>
            <?php if ($hero_landing_ads_primary_button['rel'] !== ''): ?>rel="<?php echo esc_attr($hero_landing_ads_primary_button['rel']); ?>"<?php endif; ?>
          >
            <span><?php echo esc_html($hero_landing_ads_primary_button['label']); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        </div>
      <?php endif; ?>

      <?php if ($hero_landing_ads_phone !== '' || $hero_landing_ads_trust_text !== ''): ?>
        <div class="hero-landing-ads-contact-note">
          <?php if ($hero_landing_ads_phone !== ''): ?>
            <a class="hero-landing-ads-phone" href="tel:<?php echo esc_attr($hero_landing_ads_phone_link); ?>">
              <span class="telefono-icon hero-landing-ads-phone-icon" aria-hidden="true"></span>
              <span>
                <?php if ($hero_landing_ads_phone_label !== ''): ?>
                  <span><?php echo esc_html($hero_landing_ads_phone_label); ?></span>
                <?php endif; ?>
                <strong><?php echo esc_html($hero_landing_ads_phone); ?></strong>
              </span>
            </a>
          <?php endif; ?>

          <?php if ($hero_landing_ads_trust_text !== ''): ?>
            <div class="hero-landing-ads-trust-text"><?php echo wp_kses_post($hero_landing_ads_trust_text); ?></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($hero_landing_ads_photo_data['url'] !== ''): ?>
      <figure class="hero-landing-ads-media">
        <span class="hero-landing-ads-photo-frame">
          <?php if (function_exists('chefstudio_responsive_image')): ?>
            <?php echo chefstudio_responsive_image($hero_landing_ads_photo, 'contenuto-articolo-desktop', [
              'class' => 'hero-landing-ads-photo-img no-lazy skip-lazy',
              'alt' => $hero_landing_ads_photo_alt,
              'fetchpriority' => 'high',
              'loading' => 'eager',
              'decoding' => false,
              'sizes' => '(max-width: 980px) 62vw, 460px',
            ]); ?>
          <?php else: ?>
            <img class="hero-landing-ads-photo-img no-lazy skip-lazy" src="<?php echo esc_url($hero_landing_ads_photo_data['url']); ?>" alt="<?php echo esc_attr($hero_landing_ads_photo_alt); ?>" loading="eager">
          <?php endif; ?>
        </span>

        <?php if ($hero_landing_ads_consultant_name !== '' || $hero_landing_ads_consultant_role !== ''): ?>
          <figcaption class="hero-landing-ads-person-card">
            <?php if ($hero_landing_ads_consultant_name !== ''): ?>
              <strong><?php echo esc_html($hero_landing_ads_consultant_name); ?></strong>
            <?php endif; ?>
            <?php if ($hero_landing_ads_consultant_role !== ''): ?>
              <span><?php echo esc_html($hero_landing_ads_consultant_role); ?></span>
            <?php endif; ?>
          </figcaption>
        <?php endif; ?>
      </figure>
    <?php endif; ?>

    <?php if ($hero_landing_ads_has_form_card): ?>
      <aside id="<?php echo esc_attr($hero_landing_ads_form_anchor); ?>" class="hero-landing-ads-form-card local-seo-form-card">
        <?php if ($hero_landing_ads_form_title !== '' || $hero_landing_ads_form_text !== ''): ?>
          <div class="hero-landing-ads-form-heading local-seo-form-heading">
            <div class="hero-landing-ads-form-heading-row">
              <?php if ($hero_landing_ads_form_title !== ''): ?>
                <h2><?php echo wp_kses_post($hero_landing_ads_form_title); ?></h2>
              <?php endif; ?>

              <?php if ($hero_landing_ads_form_step !== ''): ?>
                <span class="hero-landing-ads-form-step" data-hero-landing-ads-form-step><?php echo esc_html($hero_landing_ads_form_step); ?></span>
              <?php endif; ?>
            </div>

            <?php if ($hero_landing_ads_form_text !== ''): ?>
              <p><?php echo wp_kses_post($hero_landing_ads_form_text); ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($hero_landing_ads_has_form): ?>
          <div class="contatti hero-landing-ads-gravity-form local-seo-gravity-form">
            <?php
            if ($hero_landing_ads_form_shortcode !== '') {
              echo do_shortcode($hero_landing_ads_form_shortcode);
            } else {
              gravity_form($hero_landing_ads_form_id, false, false, false, $hero_landing_ads_field_values, true, 0);
            }
            ?>
          </div>
        <?php else: ?>
          <div class="hero-landing-ads-form-preview" aria-hidden="true">
            <div class="hero-landing-ads-preview-progress">
              <span></span>
            </div>

            <div class="hero-landing-ads-preview-field">
              <label>Sito web</label>
              <div class="hero-landing-ads-preview-row">
                <span class="hero-landing-ads-preview-input">https://www...</span>
              <span class="hero-landing-ads-preview-check">Il progetto non è ancora online</span>
              </div>
            </div>

            <div class="hero-landing-ads-preview-field">
              <label>Che tipo di progetto è?</label>
              <div class="hero-landing-ads-preview-card-grid">
                <span>Azienda / B2B</span>
                <span>E-commerce</span>
                <span>Altro</span>
              </div>
            </div>

            <div class="hero-landing-ads-preview-field">
              <label>Qual è il problema principale?</label>
              <div class="hero-landing-ads-preview-pill-grid">
                <span>Poca visibilità su Google</span>
                <span>Devo capire da dove partire</span>
                <span>Pochi contatti o vendite dalla SEO</span>
                <span>Altro</span>
              </div>
            </div>

            <span class="hero-landing-ads-preview-submit">
              <span>Continua</span>
              <span class="hero-btn-arrow" aria-hidden="true"></span>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($hero_landing_ads_form_note !== ''): ?>
          <div class="hero-landing-ads-form-note">
            <span aria-hidden="true"><?php echo $hero_landing_ads_icon('sicurezza'); ?></span>
            <p><?php echo wp_kses_post($hero_landing_ads_form_note); ?></p>
          </div>
        <?php endif; ?>
      </aside>

      <?php if ($hero_landing_ads_has_form && $hero_landing_ads_form_id === 5): ?>
        <script>
          (function () {
            var formId = 5;
            var cardId = <?php echo wp_json_encode($hero_landing_ads_form_anchor); ?>;
            var observer = null;

            function getVisiblePage(form) {
              var pages = Array.prototype.slice.call(form.querySelectorAll('.gform_page'));
              var visiblePage = pages.find(function (page) {
                return window.getComputedStyle(page).display !== 'none';
              });

              if (!visiblePage) {
                return 1;
              }

              var match = visiblePage.id.match(/_(\d+)$/);
              return match ? parseInt(match[1], 10) : 1;
            }

            function syncCompactLabels(card) {
              var offlineLabel = card.querySelector('#label_5_17_1');

              if (offlineLabel && offlineLabel.textContent.trim() === 'Il progetto non è ancora online.') {
                offlineLabel.textContent = 'Non ho ancora il sito online';
              }
            }

            function syncStepUi() {
              var card = document.getElementById(cardId);

              if (!card) {
                return;
              }

              syncCompactLabels(card);

              var form = card.querySelector('#gform_' + formId);

              if (!form) {
                return;
              }

              var currentPage = getVisiblePage(form);
              var pageCountNode = form.querySelector('.gf_step_page_count');
              var totalPages = pageCountNode ? parseInt(pageCountNode.textContent, 10) : form.querySelectorAll('.gform_page').length;

              if (!totalPages || totalPages < currentPage) {
                totalPages = Math.max(currentPage, 2);
              }

              var percent = Math.round((currentPage / totalPages) * 100);
              var stepBadge = card.querySelector('[data-hero-landing-ads-form-step]');
              var progressBar = card.querySelector('.gf_progressbar_percentage');

              if (stepBadge) {
                var stepLabel = currentPage + ' di ' + totalPages;

                if (stepBadge.textContent !== stepLabel) {
                  stepBadge.textContent = stepLabel;
                }
              }

              if (progressBar) {
                var width = percent + '%';
                var className = (progressBar.className.replace(/\bpercentbar_\d+\b/g, '').trim() + ' percentbar_' + percent).trim();

                if (progressBar.style.width !== width) {
                  progressBar.style.width = width;
                }

                if (progressBar.className !== className) {
                  progressBar.className = className;
                }
              }
            }

            function scheduleSync() {
              window.setTimeout(syncStepUi, 30);
              window.setTimeout(syncStepUi, 260);
            }

            function startObserver() {
              var card = document.getElementById(cardId);

              if (!card || typeof MutationObserver === 'undefined') {
                return;
              }

              if (observer) {
                return;
              }

              observer = new MutationObserver(function () {
                scheduleSync();
              });

              observer.observe(card, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: ['class', 'style']
              });
            }

            if (document.readyState === 'loading') {
              document.addEventListener('DOMContentLoaded', function () {
                startObserver();
                scheduleSync();
              });
            } else {
              startObserver();
              scheduleSync();
            }

            document.addEventListener('gform/post_render', function (event) {
              if (!event.detail || parseInt(event.detail.formId, 10) === formId) {
                scheduleSync();
              }
            });

            document.addEventListener('click', function (event) {
              if (event.target.closest('#' + cardId + ' #gform_' + formId + ' button')) {
                scheduleSync();
              }
            }, true);

            if (window.jQuery) {
              window.jQuery(document).on('gform_page_loaded gform_post_render', function (event, loadedFormId) {
                if (parseInt(loadedFormId, 10) === formId) {
                  scheduleSync();
                }
              });
            }
          }());
        </script>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php get_template_part('parti/layout_close'); ?>
