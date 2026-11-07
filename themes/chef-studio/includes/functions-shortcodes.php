<?php
if (!defined('ABSPATH')) exit;

function chefstudio_blog_cta_bool($value, $default = true) {
  if ($value === '' || $value === null) {
    return $default;
  }

  if (is_bool($value)) {
    return $value;
  }

  $value = strtolower(trim((string) $value));

  if (in_array($value, ['0', 'false', 'no', 'off'], true)) {
    return false;
  }

  if (in_array($value, ['1', 'true', 'yes', 'on'], true)) {
    return true;
  }

  return $default;
}

function chefstudio_blog_cta_first_value($names, $post_id = null) {
  if (!function_exists('get_field')) {
    return '';
  }

  foreach ((array) $names as $name) {
    $value = $post_id === null ? get_field($name, 'option') : get_field($name, $post_id);

    if (is_array($value) || is_numeric($value)) {
      return $value;
    }

    if (is_string($value) && trim($value) !== '') {
      return $value;
    }
  }

  return '';
}

function chefstudio_blog_cta_media($value, $size = 'medium') {
  $media = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($value)) {
    $media['url'] = $value['sizes'][$size] ?? ($value['url'] ?? '');
    $media['alt'] = $value['alt'] ?? '';
  } elseif (is_numeric($value)) {
    $attachment_id = (int) $value;
    $media['url'] = (string) wp_get_attachment_image_url($attachment_id, $size);
    $media['alt'] = (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
  } elseif (is_string($value)) {
    $media['url'] = trim($value);
  }

  return $media;
}

function chefstudio_blog_cta_link($value) {
  $link = [
    'url' => '',
    'target' => '',
  ];

  if (is_array($value)) {
    $link['url'] = $value['url'] ?? '';
    $link['target'] = $value['target'] ?? '';
  } elseif (is_string($value)) {
    $link['url'] = trim($value);
  }

  return $link;
}

function chefstudio_blog_cta_contact_url() {
  $contact_page = get_page_by_path('contatti');

  if ($contact_page) {
    return get_permalink($contact_page);
  }

  return home_url('/contatti/');
}

function chefstudio_blog_cta_default_url() {
  return '#form-anchor';
}

function chefstudio_blog_cta_resolve_url_alias($value) {
  if (is_array($value) || is_numeric($value)) {
    return $value;
  }

  $url = trim((string) $value);
  $key = sanitize_key($url);

  if (in_array($key, ['contatti', 'contatto', 'contact'], true)) {
    return chefstudio_blog_cta_contact_url();
  }

  if (in_array($key, ['form', 'form-anchor', 'form-ancora', 'ancora', 'anchor'], true)) {
    return chefstudio_blog_cta_default_url();
  }

  return $value;
}

function chefstudio_blog_cta_presets() {
  $default_url = chefstudio_blog_cta_default_url();

  $presets = [
  'default' => [],

  'igor' => [
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - consulente SEO di Chef Studio',
  ],

  'andrea' => [
    'immagine' => home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png'),
    'immagine_alt' => 'Andrea Biagi - web designer di Chef Studio',
  ],

  'web' => [
    'titolo' => 'Il tuo sito convince davvero chi lo visita?',
    'testo' => 'Progettiamo siti su misura che comunicano il valore della tua attività, guidano le persone verso ciò che cercano e rendono più semplice contattarti.',
    'bottone' => 'Parliamo del tuo nuovo sito',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png'),
    'immagine_alt' => 'Andrea Biagi - web designer di Chef Studio',
  ],

  'sito-aziendale' => [
    'titolo' => 'Il tuo sito trasmette davvero competenza e affidabilità?',
    'testo' => 'Realizziamo siti aziendali chiari e credibili, capaci di spiegare bene cosa fai, distinguerti dai concorrenti e facilitare il contatto.',
    'bottone' => 'Parliamo del tuo sito aziendale',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png'),
    'immagine_alt' => 'Andrea Biagi - realizzazione siti web aziendali',
  ],

  'wordpress' => [
    'titolo' => 'Vuoi un sito WordPress veloce, solido e davvero su misura?',
    'testo' => 'Lo relizziamo sulle tue esigenze, con una struttura pulita, semplice da gestire e pronta per la SEO.',
    'bottone' => 'Raccontaci il tuo progetto',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - Web Designer e consulente SEO di Chef Studio',
  ],

  'ecommerce' => [
    'titolo' => 'Vuoi un e-commerce che renda semplice scegliere e acquistare?',
    'testo' => 'Progettiamo catalogo, navigazione e percorso di acquisto intorno ai tuoi prodotti e ai tuoi clienti, senza soluzioni preconfezionate.',
    'bottone' => 'Parliamo del tuo e-commerce',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png'),
    'immagine_alt' => 'Andrea Biagi - realizzazione e-commerce su misura',
  ],

  'seo' => [
    'titolo' => 'Il tuo sito è visibile per le ricerche che contano davvero?',
    'testo' => 'Analizziamo mercato, struttura e contenuti per costruire una strategia SEO concreta e portare sul sito persone realmente interessate a ciò che offri.',
    'bottone' => 'Parliamo della tua SEO',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - consulente SEO di Chef Studio',
  ],

  'consulenza-seo' => [
  'titolo' => 'Il tuo sito non cresce su Google e non riesci a capire perché?',
  'testo' => 'Analizziamo problemi tecnici, contenuti e opportunità per individuare ciò che sta frenando il sito e definire le priorità su cui intervenire.',
  'bottone' => 'Parla con un consulente SEO',
  'url' => $default_url,
  'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
  'immagine_alt' => 'Igor Biagi - consulente SEO di Chef Studio',
],

  'seo-continuativa' => [
    'titolo' => 'La SEO non si risolve con un intervento una tantum.',
    'testo' => 'Seguiamo il progetto nel tempo, scegliendo le priorità, realizzando le ottimizzazioni e misurando i risultati per far evolvere la strategia mese dopo mese.',
    'bottone' => 'Valutiamo un percorso SEO',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - consulenza SEO continuativa',
  ],

  'ottimizzazione-sito' => [
    'titolo' => 'Il tuo sito è lento, poco chiaro o difficile da trovare?',
    'testo' => 'Individuiamo i problemi tecnici, di struttura e di contenuto che ne limitano prestazioni, usabilità e visibilità, intervenendo prima su ciò che conta davvero.',
    'bottone' => 'Fai valutare il tuo sito',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - ottimizzazione siti web',
  ],

  'posizionamento-google' => [
    'titolo' => 'Le pagine più importanti del tuo sito restano indietro su Google?',
    'testo' => 'Studiamo le ricerche, i concorrenti e ciò che oggi limita il posizionamento, per costruire un percorso di crescita realistico sulle query strategiche.',
    'bottone' => 'Analizziamo il posizionamento',
    'url' => $default_url,
    'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
    'immagine_alt' => 'Igor Biagi - posizionamento siti su Google',
  ],

  'seo-ecommerce' => [
  'titolo' => 'I tuoi concorrenti sono prima di te su Google?',
  'testo' => 'Analizziamo categorie, prodotti e struttura del tuo e-commerce per migliorare la visibilità delle pagine che possono portare traffico qualificato e nuovi ordini.',
  'bottone' => 'Scopri cosa limita il tuo e-commerce',
  'url' => $default_url,
  'immagine' => home_url('/wp-content/uploads/2026/02/igor-biagi-cta.png'),
  'immagine_alt' => 'Igor Biagi - consulente SEO per e-commerce',
],
];

  return apply_filters('chefstudio_blog_cta_presets', $presets);
}






function chefstudio_blog_cta_shortcode($atts = [], $content = null) {
  $post_id = get_the_ID();
  $raw_atts = is_array($atts) ? $atts : [];

  $defaults = [
    'preset' => 'default',
    'tipo' => '',
    'variante' => '',
    'titolo' => 'Vuoi trasformare il tuo sito in uno strumento più efficace?',
    'h2' => '',
    'testo' => 'Chef Studio ti aiuta a costruire una strategia digitale più chiara, dal sito web alla SEO, con interventi pensati per valorizzare il tuo progetto e portare risultati misurabili.',
    'bottone' => 'Parla con noi',
    'url' => chefstudio_blog_cta_default_url(),
    'link' => '',
    'target' => '',
    'destinazione' => '',
    'destination' => '',
    'immagine' => '',
    'immagine_alt' => '',
    'icona' => '',
    'icona_alt' => '',
    'telefono' => '1',
    'orari' => '',
    'classe' => '',
    'layout' => '',
  ];

  $option_values = [
    'titolo' => chefstudio_blog_cta_first_value(['chef_blog_cta_titolo', 'blog_cta_titolo']),
    'testo' => chefstudio_blog_cta_first_value(['chef_blog_cta_testo', 'blog_cta_testo']),
    'bottone' => chefstudio_blog_cta_first_value(['chef_blog_cta_bottone', 'blog_cta_bottone']),
    'url' => chefstudio_blog_cta_first_value(['chef_blog_cta_url', 'blog_cta_url', 'chef_blog_cta_link', 'blog_cta_link']),
    'immagine' => chefstudio_blog_cta_first_value(['chef_blog_cta_immagine', 'blog_cta_immagine']),
    'icona' => chefstudio_blog_cta_first_value(['chef_blog_cta_icona', 'blog_cta_icona']),
    'orari' => chefstudio_blog_cta_first_value(['chef_blog_cta_orari', 'blog_cta_orari']),
  ];

  foreach ($option_values as $key => $value) {
    if ($value !== '') {
      $defaults[$key] = $value;
    }
  }

  $preset_key = $raw_atts['preset'] ?? $raw_atts['tipo'] ?? $raw_atts['variante'] ?? '';
  $preset_key = sanitize_key((string) $preset_key);
  $presets = chefstudio_blog_cta_presets();

  if ($preset_key !== '' && isset($presets[$preset_key]) && is_array($presets[$preset_key])) {
    foreach ($presets[$preset_key] as $key => $value) {
      if ($value !== '') {
        $defaults[$key] = $value;
      }
    }
  }

  $post_values = [
    'titolo' => chefstudio_blog_cta_first_value(['chef_blog_cta_titolo', 'blog_cta_titolo'], $post_id),
    'testo' => chefstudio_blog_cta_first_value(['chef_blog_cta_testo', 'blog_cta_testo'], $post_id),
    'bottone' => chefstudio_blog_cta_first_value(['chef_blog_cta_bottone', 'blog_cta_bottone'], $post_id),
    'url' => chefstudio_blog_cta_first_value(['chef_blog_cta_url', 'blog_cta_url', 'chef_blog_cta_link', 'blog_cta_link'], $post_id),
    'immagine' => chefstudio_blog_cta_first_value(['chef_blog_cta_immagine', 'blog_cta_immagine'], $post_id),
    'icona' => chefstudio_blog_cta_first_value(['chef_blog_cta_icona', 'blog_cta_icona'], $post_id),
    'orari' => chefstudio_blog_cta_first_value(['chef_blog_cta_orari', 'blog_cta_orari'], $post_id),
  ];

  foreach ($post_values as $key => $value) {
    if ($value !== '') {
      $defaults[$key] = $value;
    }
  }

  $atts = shortcode_atts($defaults, $atts, 'chef_blog_cta');

  if (!empty($atts['h2'])) {
    $atts['titolo'] = $atts['h2'];
  }

  if (!empty($atts['link'])) {
    $atts['url'] = $atts['link'];
  }

  $destination = sanitize_key((string) (!empty($atts['destinazione']) ? $atts['destinazione'] : $atts['destination']));

  if ($destination !== '') {
    $atts['url'] = $destination;
  }

  $link = chefstudio_blog_cta_link(chefstudio_blog_cta_resolve_url_alias($atts['url']));
  $link['url'] = chefstudio_blog_cta_resolve_url_alias($link['url']);

  if (!empty($atts['target'])) {
    $link['target'] = trim((string) $atts['target']);
  }

  $image = chefstudio_blog_cta_media($atts['immagine'], 'medium');
  if (!empty($atts['immagine_alt'])) {
    $image['alt'] = (string) $atts['immagine_alt'];
  }

  $icon = chefstudio_blog_cta_media($atts['icona'], 'thumbnail');
  if (!empty($atts['icona_alt'])) {
    $icon['alt'] = (string) $atts['icona_alt'];
  }

  $title = trim(wp_strip_all_tags((string) $atts['titolo']));
  $text = $content !== null && trim((string) $content) !== '' ? $content : $atts['testo'];
  $text = trim((string) $text);

  if ($text !== '' && !preg_match('/<(p|ul|ol|h[1-6]|blockquote|div)\b/i', $text)) {
    $text = wpautop($text);
  }

  $button_label = trim(wp_strip_all_tags((string) $atts['bottone']));
  $show_phone = chefstudio_blog_cta_bool($atts['telefono'], true);
  $phone = function_exists('get_field') ? get_field('telefono', 'option') : '';
  $phone_link = $phone ? preg_replace('/[^0-9+]/', '', $phone) : '';
  $layout = sanitize_key((string) $atts['layout']);
  $classes = trim('call-to-action call-to-action-desktop cta-premium chef-blog-cta ' . sanitize_html_class((string) $atts['classe']));

  if (in_array($layout, ['side', 'laterale', 'verticale'], true)) {
    $side_classes = trim('call-to-action faq-side-cta chef-blog-cta-side ' . sanitize_html_class((string) $atts['classe']));

    ob_start();
    ?>
    <section class="<?php echo esc_attr($side_classes); ?>">
      <div class="faq-side-cta-inner">
        <?php if (!empty($image['url'])): ?>
          <figure class="faq-side-cta-media">
            <?php echo chefstudio_responsive_image($atts['immagine'], 'medium', [
              'alt'      => $image['alt'],
              'loading'  => 'lazy',
              'decoding' => 'async',
              'sizes'    => '(max-width: 768px) 180px, 217px',
            ]); ?>
          </figure>
        <?php endif; ?>

        <div class="faq-side-cta-body">
          <?php if (!empty($icon['url']) || $title !== '' || $text !== ''): ?>
            <div class="faq-side-cta-copy">
              <?php if (!empty($icon['url'])): ?>
                <figure class="faq-side-cta-icon">
                  <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>">
                </figure>
              <?php endif; ?>

              <?php if ($title !== ''): ?>
                <h3><?php echo esc_html($title); ?></h3>
              <?php endif; ?>

              <?php if ($text !== ''): ?>
                <div class="faq-side-cta-subtitle">
                  <?php echo wp_kses_post(do_shortcode($text)); ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($link['url']) && $button_label !== ''): ?>
            <a
              href="<?php echo esc_url($link['url']); ?>"
              class="btn btn-super neon faq-side-cta-button"
              <?php if (!empty($link['target'])): ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>
              <?php if ($link['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
            >
              <span><?php echo esc_html($button_label); ?></span>
              <span class="hero-btn-arrow" aria-hidden="true"></span>
            </a>
          <?php endif; ?>

          <?php if ($show_phone && !empty($phone)): ?>
            <a class="faq-side-cta-phone" href="tel:<?php echo esc_attr($phone_link); ?>">
              <img
                class="telefono-icon"
                src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/phone.svg'); ?>"
                alt="icona telefono"
                aria-hidden="true"
              >
              <span><?php echo esc_html($phone); ?></span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
  }

  ob_start();
  ?>
  <section class="<?php echo esc_attr($classes); ?>">
    <div class="cta-premium-inner">
      <?php if (!empty($image['url'])): ?>
        <figure class="cta-premium-media">
          <?php echo chefstudio_responsive_image($atts['immagine'], 'medium', [
            'alt'      => $image['alt'],
            'loading'  => 'lazy',
            'decoding' => 'async',
            'sizes'    => '(max-width: 768px) 180px, 217px',
          ]); ?>
        </figure>
      <?php endif; ?>

      <div class="cta-premium-copy">
        <?php if (!empty($icon['url'])): ?>
          <figure class="cta-premium-icon">
            <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt']); ?>">
          </figure>
        <?php endif; ?>

        <div class="info">
          <?php if ($title !== ''): ?>
            <h2><?php echo esc_html($title); ?></h2>
          <?php endif; ?>

          <?php echo wp_kses_post(do_shortcode($text)); ?>
        </div>
      </div>

      <div class="cta-premium-actions">
        <?php if (!empty($link['url']) && $button_label !== ''): ?>
          <a
            href="<?php echo esc_url($link['url']); ?>"
            class="btn btn-super neon cta-premium-button"
            <?php if (!empty($link['target'])): ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>
            <?php if ($link['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
          >
            <span><?php echo esc_html($button_label); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        <?php endif; ?>

        <?php if ($show_phone && !empty($phone)): ?>
          <a class="cta-premium-phone" href="tel:<?php echo esc_attr($phone_link); ?>">
            <span class="cta-premium-phone-icon" aria-hidden="true">
              <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/phone.svg'); ?>" alt="icona telefono" aria-hidden="true">
            </span>
            <span>
              <strong><?php echo esc_html($phone); ?></strong>
              <?php if (!empty($atts['orari'])): ?>
                <small><?php echo esc_html($atts['orari']); ?></small>
              <?php endif; ?>
            </span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php
  return ob_get_clean();
}
add_shortcode('chef_blog_cta', 'chefstudio_blog_cta_shortcode');
add_shortcode('chef_cta_blog', 'chefstudio_blog_cta_shortcode');
add_shortcode('chef_blog_cta_custom', 'chefstudio_blog_cta_shortcode');
add_shortcode('chef_blog_cta_personalizzata', 'chefstudio_blog_cta_shortcode');
