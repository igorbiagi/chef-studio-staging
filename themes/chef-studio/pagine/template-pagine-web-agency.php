<?php
/*
Template Name: Template pagine Web Agency
*/
get_header();

echo "<!-- TEMPLATE: template-pagine-web-agency.php CARICATO -->";

if (!function_exists('lpg_wa_city_from_string')) {
    function lpg_wa_city_from_string($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $city = mb_strtolower($value, 'UTF-8');
        $city = str_replace(['-', '_', '|'], ' ', $city);

        $remove_phrases = [
            'web agency',
            'agenzia web',
            'web design',
            'web designer',
            'realizzazione siti web',
            'realizzazione sito web',
            'creazione siti web',
            'creazione sito web',
        ];

        foreach ($remove_phrases as $phrase) {
            $city = preg_replace('/\b' . preg_quote($phrase, '/') . '\b/u', ' ', $city);
        }

        $city = preg_replace('/\b(a|in|di|per|su|nel|nella|nei|nelle)\b/u', ' ', $city);
        $city = preg_replace('/\s+/u', ' ', $city);
        $city = trim($city);

        return $city !== '' ? mb_convert_case($city, MB_CASE_TITLE, 'UTF-8') : '';
    }
}

if (!function_exists('lpg_wa_image_url_from_value')) {
    function lpg_wa_image_url_from_value($img) {
        if (empty($img)) {
            return '';
        }

        if (is_array($img) && !empty($img['url'])) {
            return $img['url'];
        }

        if (is_array($img) && !empty($img['ID'])) {
            $url = wp_get_attachment_url((int) $img['ID']);
            return $url ?: '';
        }

        if (is_numeric($img)) {
            $url = wp_get_attachment_url((int) $img);
            return $url ?: '';
        }

        if (is_string($img) && filter_var($img, FILTER_VALIDATE_URL)) {
            return $img;
        }

        return '';
    }
}

$post_id = get_queried_object_id();

$citta = function_exists('get_field') ? trim((string) get_field('citta', $post_id)) : '';
if ($citta === '') {
    $citta = lpg_wa_city_from_string(get_the_title($post_id));
}
if ($citta === '') {
    $citta = lpg_wa_city_from_string(get_post_field('post_name', $post_id));
}

$citta_lowercase = $citta ? mb_strtolower($citta, 'UTF-8') : '';
$citta_slug = $citta ? sanitize_title(remove_accents($citta)) : 'default';

$telefono = function_exists('get_field') ? get_field('telefono', 'option') : '';
if (empty($telefono)) {
    $telefono = '+39 352 0996 996';
}
$telefono_link = preg_replace('/[^0-9+]/', '', $telefono);

$h2_hero             = function_exists('get_field') ? get_field('h2_hero', $post_id) : '';
$paragrafo_intro     = function_exists('get_field') ? get_field('paragrafo_intro', $post_id) : '';
$titolo_sezione_1    = function_exists('get_field') ? get_field('titolo_sezione_1', $post_id) : '';
$testo_sezione_1     = function_exists('get_field') ? get_field('testo_sezione_1', $post_id) : '';
$titolo_1_sezione_2  = function_exists('get_field') ? get_field('titolo_1_sezione_2', $post_id) : '';
$testo_1_sezione_2   = function_exists('get_field') ? get_field('testo_1_sezione_2', $post_id) : '';
$titolo_2_sezione_2  = function_exists('get_field') ? get_field('titolo_2_sezione_2', $post_id) : '';
$testo_2_sezione_2   = function_exists('get_field') ? get_field('testo_2_sezione_2', $post_id) : '';
$intro_servizi       = function_exists('get_field') ? get_field('intro_servizi', $post_id) : '';
$titolo_faq          = function_exists('get_field') ? get_field('titolo_faq', $post_id) : '';

$terms = wp_get_post_terms($post_id, 'argomento_categoria');
$term_ref = '';
if (!is_wp_error($terms) && !empty($terms)) {
    $wa_term_slug = defined('LPG_WA_TERM_SLUG') ? LPG_WA_TERM_SLUG : 'web-agency';
    foreach ($terms as $term) {
        if (!empty($term->slug) && $term->slug === $wa_term_slug) {
            $term_ref = 'argomento_categoria_' . $term->term_id;
            break;
        }
    }

    if ($term_ref === '') {
        $term_ref = 'argomento_categoria_' . $terms[0]->term_id;
    }
}

if (!function_exists('lpg_wa_text_variants')) {
    $wa_variants_file = get_stylesheet_directory() . '/local-pages-generator/includes/web-agency-variants.php';
    if (file_exists($wa_variants_file)) {
        require_once $wa_variants_file;
    }
}

$wa_base_text_defaults = function_exists('lpg_wa_base_text_defaults') ? lpg_wa_base_text_defaults() : [];
$wa_variants = function_exists('lpg_wa_text_variants') ? lpg_wa_text_variants() : [];
$wa_page_variant = function_exists('get_field') ? (string) get_field('wa_text_variant', $post_id) : '';
$wa_default_variant = ($term_ref && function_exists('get_field')) ? (string) get_field('wa_default_variant', $term_ref) : '';

if ($wa_page_variant === '' || $wa_page_variant === 'inherit') {
    $wa_selected_variant = $wa_default_variant ?: 'variant_1';
} else {
    $wa_selected_variant = $wa_page_variant;
}

if ($wa_selected_variant !== 'base' && empty($wa_variants[$wa_selected_variant])) {
    $wa_selected_variant = !empty($wa_variants['variant_1']) ? 'variant_1' : 'base';
}

$wa_replace_placeholders = function($value) use ($citta, $citta_lowercase, $citta_slug, $telefono, $telefono_link) {
    if (!is_string($value) && !is_numeric($value)) {
        return $value;
    }

    return str_replace(
        ['{citta}', '{città}', '{citta_lowercase}', '{citta_slug}', '{telefono}', '{telefono_link}'],
        [$citta, $citta, $citta_lowercase, $citta_slug, $telefono, $telefono_link],
        (string) $value
    );
};

$wa_is_empty_text = function($value) {
    return trim(wp_strip_all_tags((string) $value)) === '';
};

$wa_normalize_text = function($value) use ($wa_replace_placeholders) {
    $value = $wa_replace_placeholders((string) $value);
    $value = wp_strip_all_tags($value);
    $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    $value = str_replace(["\xc2\xa0", '’'], [' ', "'"], $value);
    $value = preg_replace('/\s+/u', ' ', trim($value));

    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
};

$wa_is_defaultish_page_text = function($value, $term_field, $fallback = '') use (
    $wa_is_empty_text,
    $wa_normalize_text,
    $wa_base_text_defaults,
    $wa_variants
) {
    if ($wa_is_empty_text($value)) {
        return true;
    }

    $normalized_value = $wa_normalize_text($value);
    $default_candidates = [];

    if ($fallback !== '') {
        $default_candidates[] = $fallback;
    }

    if ($term_field && isset($wa_base_text_defaults[$term_field])) {
        $default_candidates[] = $wa_base_text_defaults[$term_field];
    }

    if ($term_field) {
        foreach ($wa_variants as $variant) {
            if (!empty($variant['fields'][$term_field])) {
                $default_candidates[] = $variant['fields'][$term_field];
            }
        }
    }

    foreach ($default_candidates as $candidate) {
        if ($normalized_value === $wa_normalize_text($candidate)) {
            return true;
        }
    }

    return false;
};

$get_wa_text = function($page_field, $term_field, $fallback = '') use (
    $post_id,
    $term_ref,
    $wa_replace_placeholders,
    $wa_is_empty_text,
    $wa_is_defaultish_page_text,
    $wa_selected_variant,
    $wa_variants,
    $wa_base_text_defaults
) {
    $value = '';

    if ($page_field && function_exists('get_field')) {
        $value = get_field($page_field, $post_id);
        if ($wa_is_defaultish_page_text($value, $term_field, $fallback)) {
            $value = '';
        }
    }

    if ($wa_is_empty_text($value) && $term_field && $wa_selected_variant !== 'base') {
        $variant_field = function_exists('lpg_wa_variant_field_name')
            ? lpg_wa_variant_field_name($term_field, $wa_selected_variant)
            : '';

        if ($variant_field && $term_ref && function_exists('get_field')) {
            $value = get_field($variant_field, $term_ref);
        }

        if ($wa_is_empty_text($value) && !empty($wa_variants[$wa_selected_variant]['fields'][$term_field])) {
            $value = $wa_variants[$wa_selected_variant]['fields'][$term_field];
        }
    }

    if ($wa_is_empty_text($value) && $term_field && $term_ref && function_exists('get_field')) {
        $value = get_field($term_field, $term_ref);
    }

    if ($wa_is_empty_text($value) && $term_field && isset($wa_base_text_defaults[$term_field])) {
        $value = $wa_base_text_defaults[$term_field];
    }

    if ($wa_is_empty_text($value)) {
        $value = $fallback;
    }

    return $wa_replace_placeholders($value);
};

$hero_h1 = $get_wa_text('', 'wa_hero_h1_default', 'Web Agency {citta}');

$h2_hero = $get_wa_text(
    'h2_hero',
    'wa_h2_hero_default',
    'Siti web, SEO e marketing digitale su misura per aziende e professionisti.'
);

$paragrafo_intro = $get_wa_text(
    'paragrafo_intro',
    'wa_paragrafo_intro_default',
    '<p>Nella nostra web agency a {citta} lavoriamo con analisi, strategia e cura del dettaglio per aiutare aziende e professionisti a crescere online. Studiamo mercato, competitor e ricerche degli utenti per costruire siti web, identita visive e percorsi di marketing pensati per generare contatti reali.</p><p>Lavoriamo da piu di 10 anni nel mondo del web e affianchiamo piccole e medie imprese con progetti su misura, senza pacchetti preconfezionati.</p>'
);

$titolo_sezione_1 = $get_wa_text(
    'titolo_sezione_1',
    'wa_titolo_sezione_1_default',
    'La Web Agency {citta} dinamica'
);

$testo_sezione_1 = $get_wa_text(
    'testo_sezione_1',
    'wa_testo_sezione_1_default',
    '<p>Realizziamo siti web professionali a {citta}, in Italia e all\'estero. Scegliere la giusta web agency e fondamentale per trasformare il sito in uno strumento utile, chiaro e orientato ai risultati.</p><p>Noi di Chef Studio ti aiutiamo a capire le potenzialita dei mezzi digitali e formuliamo una soluzione proporzionata ai tuoi obiettivi, ottimizzando budget, qualita e strategia.</p><p>La maggior parte dei progetti puo essere seguita anche a distanza, con un metodo ordinato che unisce web design, sviluppo, SEO e comunicazione.</p>'
);

$titolo_1_sezione_2 = $get_wa_text(
    'titolo_1_sezione_2',
    'wa_titolo_1_sezione_2_default',
    'Web design, WordPress e siti su misura a {citta}'
);

$testo_1_sezione_2 = $get_wa_text(
    'testo_1_sezione_2',
    'wa_testo_1_sezione_2_default',
    '<p>Il tuo sito web e la prima impressione che offri online: deve rappresentarti bene, caricarsi velocemente e guidare l\'utente verso una richiesta, una vendita o una prenotazione.</p><p>Progettiamo siti responsive e facili da gestire, spesso in WordPress, con template su misura e una struttura pensata per essere chiara sia per le persone sia per Google.</p>'
);

$titolo_2_sezione_2 = $get_wa_text(
    'titolo_2_sezione_2',
    'wa_titolo_2_sezione_2_default',
    'SEO e marketing digitale per farti trovare'
);

$testo_2_sezione_2 = $get_wa_text(
    'testo_2_sezione_2',
    'wa_testo_2_sezione_2_default',
    '<p>Un bel sito non basta se nessuno lo trova. Per questo integriamo strategia SEO, contenuti e analisi delle ricerche fin dalle prime fasi del progetto.</p><p>Possiamo lavorare sul posizionamento locale a {citta}, sulla crescita nazionale, sulle campagne e sulla comunicazione necessaria a rendere il tuo brand piu riconoscibile.</p>'
);

$testo_cta = $get_wa_text(
    '',
    'wa_cta_testo_default',
    '<h2>Vuoi costruire una presenza online piu efficace?</h2><p>Parla con Chef Studio e scopri come possiamo unire sito web, SEO, design e strategia digitale in un progetto concreto per la tua attivita.</p>'
);

$intro_servizi = $get_wa_text(
    'intro_servizi',
    'wa_intro_servizi_default',
    'La nostra Web Agency {citta} supporta imprese e professionisti con servizi digitali coordinati: sito web, SEO, design, contenuti e analisi.'
);

$titolo_scelta = $get_wa_text(
    '',
    'wa_titolo_scelta_default',
    'Perche affidarti alla nostra Web Agency {citta}'
);

$testo_scelta = $get_wa_text(
    '',
    'wa_testo_scelta_default',
    '<p>Ogni progetto digitale deve partire da obiettivi chiari: farsi trovare, spiegare meglio il valore dell\'azienda, vendere, ricevere richieste o consolidare il brand.</p><p>Per questo uniamo competenze diverse in un unico percorso: identita visiva, sito web, contenuti, SEO, marketing e analisi dei risultati. Il sito non resta un elemento isolato, ma diventa parte di un sistema pensato per crescere.</p>'
);

$titolo_faq = $get_wa_text(
    'titolo_faq',
    'wa_titolo_faq_default',
    'FAQ - Domande frequenti sulla Web Agency {citta}'
);

$titolo_contatti = $get_wa_text(
    '',
    'wa_titolo_contatti_default',
    'Contattaci per Web Agency {citta}!'
);

$sottotitolo_contatti = $get_wa_text(
    '',
    'wa_sottotitolo_contatti_default',
    'Abbiamo la soluzione giusta per il tuo progetto.'
);

$testo_form = $get_wa_text(
    '',
    'wa_testo_form_default',
    'Compila il form o chiamaci al <br/><a href="tel:{telefono_link}" class="no-decoration">{telefono}</a> per parlare subito con la nostra Web Agency {citta}.'
);

$hero_primary_button = 'Parla con il nostro esperto';
$phone_prompt = $get_wa_text('', 'wa_phone_prompt_default', 'Preferisci parlarne subito? Chiamaci!');
$examples_heading = $get_wa_text('', 'wa_examples_heading_default', 'Creazione siti internet {citta}: alcuni esempi.');
$example_site_1_text = $get_wa_text('', 'wa_example_site_1_text_default', 'Sito web per prodotti artigianali.');
$example_site_2_text = $get_wa_text('', 'wa_example_site_2_text_default', 'Sito web per azienda agricola e agriturismo.');
$example_site_3_text = $get_wa_text('', 'wa_example_site_3_text_default', 'Sito web per appartamenti e case vacanza.');
$example_site_4_text = $get_wa_text('', 'wa_example_site_4_text_default', 'Sito web per prodotti di design in marmo.');
$services_heading = $get_wa_text('', 'wa_services_heading_default', 'Principali servizi della nostra Web Agency {citta}');
$cta_button = $get_wa_text('', 'wa_cta_button_default', 'Contattaci!');

$wa_hero_chips = [
    [
        'label' => 'Siti web',
        'url'   => get_permalink(2801),
        'icon'  => content_url('/uploads/2025/11/icona-web-agency.svg'),
    ],
    [
        'label' => 'Siti WordPress',
        'url'   => get_permalink(2783),
        'icon'  => content_url('/uploads/2025/11/icona-siti-wordpress.svg'),
    ],
    [
        'label' => 'E-commerce',
        'url'   => get_permalink(2700),
        'icon'  => content_url('/uploads/2025/11/icona-realizzazione-ecommerce.svg'),
    ],
    [
        'label' => 'Siti aziendali',
        'url'   => get_permalink(2865),
        'icon'  => content_url('/uploads/2025/11/icona-realizzazione-siti-web-aziendali.svg'),
    ],
    [
        'label' => 'Siti professionali',
        'url'   => get_permalink(2850),
        'icon'  => content_url('/uploads/2025/11/icona-realizzazione-siti-professionali.svg'),
    ],
    [
        'label' => 'SEO',
        'url'   => get_permalink(3013),
        'icon'  => content_url('/uploads/2025/11/icona-realizzazione-landing-page.svg'),
    ],
];

$normalize_wa_rows = function($rows, $title_key, $text_key) use ($wa_replace_placeholders) {
    $out = [];

    if (!is_array($rows)) {
        return $out;
    }

    foreach ($rows as $row) {
        $titolo = $row[$title_key] ?? '';
        $testo  = $row[$text_key] ?? '';

        if (trim(wp_strip_all_tags((string) $titolo)) === '' && trim(wp_strip_all_tags((string) $testo)) === '') {
            continue;
        }

        $out[] = [
            'titolo' => $wa_replace_placeholders($titolo),
            'testo'  => $wa_replace_placeholders($testo),
        ];
    }

    return $out;
};

$get_wa_repeater_rows = function($base_field, $fallback_rows, $title_key, $text_key, $variant_data_key) use (
    $term_ref,
    $wa_selected_variant,
    $wa_variants,
    $normalize_wa_rows
) {
    $rows = $normalize_wa_rows($fallback_rows, $title_key, $text_key);
    $variant_rows = [];

    if ($base_field && $wa_selected_variant !== 'base') {
        $variant_field = function_exists('lpg_wa_variant_field_name')
            ? lpg_wa_variant_field_name($base_field, $wa_selected_variant)
            : '';

        if ($variant_field && $term_ref && function_exists('get_field')) {
            $variant_rows = $normalize_wa_rows(get_field($variant_field, $term_ref), $title_key, $text_key);
        }

        if (empty($variant_rows) && !empty($wa_variants[$wa_selected_variant][$variant_data_key])) {
            $variant_rows = $normalize_wa_rows($wa_variants[$wa_selected_variant][$variant_data_key], $title_key, $text_key);
        }
    }

    if (!empty($variant_rows)) {
        return $variant_rows;
    }

    if ($base_field && $term_ref && function_exists('get_field')) {
        $base_rows = $normalize_wa_rows(get_field($base_field, $term_ref), $title_key, $text_key);
        if (!empty($base_rows)) {
            return $base_rows;
        }
    }

    return $rows;
};

$fallback_service_rows = function_exists('lpg_wa_default_service_cards') ? lpg_wa_default_service_cards() : [];
$fallback_faq_rows = function_exists('lpg_wa_default_faq_rows') ? lpg_wa_default_faq_rows() : [];

$wa_service_rows = $get_wa_repeater_rows(
    'wa_service_cards_default',
    $fallback_service_rows,
    'wa_service_titolo',
    'wa_service_testo',
    'service_cards'
);

$wa_faq_rows = $get_wa_repeater_rows(
    'wa_faq_default',
    $fallback_faq_rows,
    'wa_faq_titolo',
    'wa_faq_testo',
    'faq'
);

$theme_dir = get_stylesheet_directory_uri();
$theme_wa_img_citta = trailingslashit($theme_dir . '/img/web-agency/citta');

$get_wa_image_url = function($field_base, $fallback_filename = '') use ($post_id, $term_ref, $theme_wa_img_citta) {
    $img = get_post_meta($post_id, '_lpg_' . $field_base, true);

    if (!$img) {
        $img = get_post_meta($post_id, $field_base, true);
    }

    if (!$img && function_exists('get_field')) {
        $img = get_field($field_base, $post_id);
    }

    if (!$img && $term_ref && function_exists('get_field')) {
        $img = get_field($field_base, $term_ref);
    }

    if (!$img && $term_ref && function_exists('get_field')) {
        $img = get_field($field_base . '_default', $term_ref);
    }

    $url = lpg_wa_image_url_from_value($img);
    if ($url) {
        return $url;
    }

    return $fallback_filename ? $theme_wa_img_citta . $fallback_filename : '';
};

$wa_url_main          = $get_wa_image_url('wa_immagine_main', "web-agency-{$citta_slug}.jpg");
$wa_url_designer      = $get_wa_image_url('wa_immagine_designer', "web-designer-{$citta_slug}.jpg");
$wa_url_agency        = $get_wa_image_url('wa_immagine_agency', "agenzia-web-{$citta_slug}.jpg");
$wa_url_professional  = $get_wa_image_url('wa_immagine_professionale', "web-agency-professionale-{$citta_slug}.png");
$wa_url_finale        = $get_wa_image_url('wa_immagine_finale', "web-agency-chef-studio-{$citta_slug}.jpg");
$wa_url_sito_1        = $get_wa_image_url('wa_immagine_sito_1', "siti-web-per-artigiani-{$citta_slug}.jpg");
$wa_url_sito_2        = $get_wa_image_url('wa_immagine_sito_2', "siti-per-aziende-agricole-{$citta_slug}.jpg");
$wa_url_sito_3        = $get_wa_image_url('wa_immagine_sito_3', "siti-per-hotel-case-vacanza-{$citta_slug}.jpg");
$wa_url_sito_4        = $get_wa_image_url('wa_immagine_sito_4', "siti-per-aziende-{$citta_slug}.jpg");
?>

<?php if (current_user_can('manage_options')) : ?>
  <!-- LPG WA debug: term_ref=<?php echo esc_html($term_ref); ?> page_variant=<?php echo esc_html($wa_page_variant ?: 'inherit'); ?> default_variant=<?php echo esc_html($wa_default_variant ?: 'empty'); ?> selected_variant=<?php echo esc_html($wa_selected_variant); ?> variants_loaded=<?php echo !empty($wa_variants) ? 'yes' : 'no'; ?> -->
<?php endif; ?>

<main class="chef-wa-page">
<div class="contenitore back-pattern-chef-web chef-header">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="struttura-container struttura-2-standard centro-verticale hero-header-enhanced">
      <div class="div-1">
        <h1><?php echo esc_html($hero_h1); ?></h1>

        <h2>
          <?php
          echo $h2_hero
              ? esc_html($h2_hero)
              : esc_html('Siti web, SEO e marketing digitale su misura per aziende e professionisti.');
          ?>
        </h2>

        <?php if (!empty($paragrafo_intro)) : ?>
          <?php echo wp_kses_post($paragrafo_intro); ?>
        <?php else : ?>
          <p>
            Nella nostra web agency a <?php echo esc_html($citta); ?> lavoriamo con analisi, strategia e cura del dettaglio per aiutare aziende e professionisti a crescere online.
            Studiamo mercato, competitor e ricerche degli utenti per costruire siti web, identita visive e percorsi di marketing pensati per generare contatti reali.
          </p>
          <p>
            Lavoriamo da piu di 10 anni nel mondo del web e affianchiamo piccole e medie imprese con progetti su misura, senza pacchetti preconfezionati.
          </p>
        <?php endif; ?>

        <div class="button-container hero-actions">
          <a href="#form-anchor" class="btn btn-super hero-action-link hero-action-link-primary">
            <span><?php echo esc_html($hero_primary_button); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        </div>

        <div class="local-container">
          <?php if (!empty($wa_url_designer)) : ?>
            <div class="img-icon">
              <img src="<?php echo esc_url($wa_url_designer); ?>" alt="web designer e consulenti SEO <?php echo esc_attr($citta); ?>">
            </div>
          <?php endif; ?>

          <div>
            <?php echo esc_html($phone_prompt); ?><br/>
            <span class="telefono-icon" aria-hidden="true"></span>
            <a href="tel:<?php echo esc_attr($telefono_link); ?>">
              <?php echo esc_html($telefono); ?>
            </a>
          </div>
        </div>

        <?php if (!empty($wa_hero_chips)) : ?>
          <div class="hero-chip-list" aria-label="Servizi in evidenza">
            <?php foreach ($wa_hero_chips as $chip) :
              $chip_url = !empty($chip['url']) ? $chip['url'] : '';
              if ($chip_url === '') {
                  continue;
              }
            ?>
              <a class="hero-chip" href="<?php echo esc_url($chip_url); ?>">
                <?php if (!empty($chip['icon'])) : ?>
                  <img class="hero-chip-icon" src="<?php echo esc_url($chip['icon']); ?>" alt="<?php echo esc_attr($chip['label']); ?>" aria-hidden="true">
                <?php endif; ?>
                <span class="hero-chip-label"><?php echo esc_html($chip['label']); ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="div-2">
        <?php if (!empty($wa_url_main)) : ?>
          <img class="img-sbordo" src="<?php echo esc_url($wa_url_main); ?>" alt="web agency <?php echo esc_attr($citta); ?>">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="contenitore">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="struttura-container struttura-2-invertito">
      <div class="div-1">
        <h2>
          <span class="testo-arancio">
            <?php echo esc_html($titolo_sezione_1 ?: 'La Web Agency ' . $citta . ' dinamica'); ?>
          </span>
        </h2>

        <?php if (!empty($testo_sezione_1)) : ?>
          <?php echo wp_kses_post($testo_sezione_1); ?>
        <?php else : ?>
          <p>Realizziamo siti web professionali a <?php echo esc_html($citta); ?>, in Italia e all'estero. Scegliere la giusta web agency e fondamentale per trasformare il sito in uno strumento utile, chiaro e orientato ai risultati.</p>
          <p>Noi di Chef Studio ti aiutiamo a capire le potenzialita dei mezzi digitali e formuliamo una soluzione proporzionata ai tuoi obiettivi, ottimizzando budget, qualita e strategia.</p>
          <p>La maggior parte dei progetti puo essere seguita anche a distanza, con un metodo ordinato che unisce web design, sviluppo, SEO e comunicazione.</p>
        <?php endif; ?>
      </div>

      <div class="div-2">
        <?php if (!empty($wa_url_agency)) : ?>
          <img class="flex" src="<?php echo esc_url($wa_url_agency); ?>" alt="agenzia web <?php echo esc_attr($citta_lowercase); ?>">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="contenitore back-bonus-color">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="struttura-container struttura-2-standard">
      <div class="div-1 txt-bianco">
        <h2>
          <span class="testo-arancio">
            <?php echo esc_html($titolo_1_sezione_2 ?: 'Web design, WordPress e siti su misura a ' . $citta); ?>
          </span>
        </h2>

        <?php if (!empty($testo_1_sezione_2)) : ?>
          <?php echo wp_kses_post($testo_1_sezione_2); ?>
        <?php else : ?>
          <p>Il tuo sito web e la prima impressione che offri online: deve rappresentarti bene, caricarsi velocemente e guidare l'utente verso una richiesta, una vendita o una prenotazione.</p>
          <p>Progettiamo siti responsive e facili da gestire, spesso in WordPress, con template su misura e una struttura pensata per essere chiara sia per le persone sia per Google.</p>
        <?php endif; ?>
      </div>

      <div class="div-2 txt-bianco">
        <h2>
          <span class="testo-arancio">
            <?php echo esc_html($titolo_2_sezione_2 ?: 'SEO e marketing digitale per farti trovare'); ?>
          </span>
        </h2>

        <?php if (!empty($testo_2_sezione_2)) : ?>
          <?php echo wp_kses_post($testo_2_sezione_2); ?>
        <?php else : ?>
          <p>Un bel sito non basta se nessuno lo trova. Per questo integriamo strategia SEO, contenuti e analisi delle ricerche fin dalle prime fasi del progetto.</p>
          <p>Possiamo lavorare sul posizionamento locale a <?php echo esc_html($citta); ?>, sulla crescita nazionale, sulle campagne e sulla comunicazione necessaria a rendere il tuo brand piu riconoscibile.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
$immagine_cta_url = home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png');
$immagine_cta_alt = 'Andrea Biagi - web designer Chef Studio';
?>

<div class="contenitore">
  <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
    <section class="call-to-action call-to-action-desktop cta-premium local-page-template-cta">
      <div class="cta-premium-inner">
        <figure class="cta-premium-media">
          <?php echo chefstudio_responsive_image($immagine_cta_url, 'medium', [
            'alt'      => $immagine_cta_alt,
            'loading'  => 'lazy',
            'decoding' => 'async',
            'sizes'    => '(max-width: 768px) 180px, 217px',
          ]); ?>
        </figure>

        <div class="cta-premium-copy">
          <div class="info">
            <?php echo wp_kses_post($testo_cta); ?>
          </div>
        </div>

        <div class="cta-premium-actions">
          <a href="#form-anchor" class="btn btn-super neon cta-premium-button">
            <span><?php echo esc_html($cta_button); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>

          <a class="cta-premium-phone" href="tel:<?php echo esc_attr($telefono_link); ?>">
            <span class="cta-premium-phone-icon" aria-hidden="true">
              <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/phone.svg'); ?>" alt="icona telefono" aria-hidden="true">
            </span>
            <span><strong><?php echo esc_html($telefono); ?></strong></span>
          </a>
        </div>
      </div>
    </section>
  </div>
</div>

<div class="contenitore">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard contenuto-scroll chef-elenco-siti-local">
    <div class="titoletto">
      <h2><?php echo esc_html($examples_heading); ?></h2>
    </div>

    <div class="anteprime-4">
      <ul class="servizi-apertura">
        <?php
        $siti = [
            [
                'url' => 'https://www.chefstudio.it/progetti/woody-lab-design-logo-servizio-fotografico-stampa-biglietti-da-visita-e-pieghevole-brochure/',
                'img' => $wa_url_sito_1,
                'alt' => 'siti web per artigiani ' . $citta_lowercase,
                'site' => 'woodylab.it',
                'text' => $example_site_1_text,
            ],
            [
                'url' => 'https://www.chefstudio.it/progetti/design-logo-e-realizzazione-sito-web-per-agriturismo/',
                'img' => $wa_url_sito_2,
                'alt' => 'siti per aziende agricole ' . $citta_lowercase,
                'site' => 'ortobiosarzana.it',
                'text' => $example_site_2_text,
            ],
            [
                'url' => '#',
                'img' => $wa_url_sito_3,
                'alt' => 'siti per hotel e case vacanza ' . $citta_lowercase,
                'site' => 'sullaspiaggia.com',
                'text' => $example_site_3_text,
            ],
            [
                'url' => 'https://www.chefstudio.it/progetti/realizzazione-sito-web-elegante-per-azienda-dedalo-stone-di-arredamento-in-marmo/',
                'img' => $wa_url_sito_4,
                'alt' => 'siti per aziende ' . $citta_lowercase,
                'site' => 'dedalostone.com',
                'text' => $example_site_4_text,
            ],
        ];

        foreach ($siti as $sito) :
        ?>
          <li>
            <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($sito['url']); ?>">
              <div class="contenitore-img">
                <?php if (!empty($sito['img'])) : ?>
                  <img class="flex" src="<?php echo esc_url($sito['img']); ?>" alt="<?php echo esc_attr($sito['alt']); ?>">
                <?php endif; ?>
              </div>
              <div class="testo-anteprima">
                <div class="titolo"><span class="url"><?php echo esc_html($sito['site']); ?></span></div>
                <div class="testo"><?php echo esc_html($sito['text']); ?></div>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<div class="contenitore chef-anteprime-hover">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="titoletto">
      <h2><span class="testo-arancio"><?php echo esc_html($services_heading); ?></span></h2>
      <?php if (!empty($intro_servizi)) : ?>
        <div class="servizi-4-2-sottotitolo"><?php echo wp_kses_post($intro_servizi); ?></div>
      <?php else : ?>
        <div class="servizi-4-2-sottotitolo">
          La nostra Web Agency <?php echo esc_html($citta); ?> supporta imprese e professionisti con servizi digitali coordinati: sito web, SEO, design, contenuti e analisi.
        </div>
      <?php endif; ?>
    </div>

    <div class="anteprime-4 anteprime-4-2 servizi-4-2-premium">
      <ul>
        <?php
        $servizi = !empty($wa_service_rows) ? $wa_service_rows : [
            ['titolo' => 'Realizzazione siti web', 'testo' => 'Sviluppiamo siti veloci, responsive e ottimizzati per offrire una buona esperienza utente e generare contatti qualificati.'],
            ['titolo' => 'Web design ' . $citta, 'testo' => 'Progettiamo interfacce chiare e coerenti con il tuo brand, dove estetica, usabilita e conversione lavorano insieme.'],
            ['titolo' => 'Creazione siti in WordPress', 'testo' => 'Realizziamo siti dinamici che puoi aggiornare in autonomia, con template su misura e una struttura ordinata.'],
            ['titolo' => 'Consulenza SEO', 'testo' => 'Analizziamo ricerche, competitor e contenuti per migliorare la visibilita organica del tuo sito sui motori di ricerca.'],
            ['titolo' => 'SEM e campagne', 'testo' => 'Supportiamo campagne a pagamento e strategie di acquisizione quando servono traffico, test rapidi e misurazione precisa.'],
            ['titolo' => 'Social media marketing', 'testo' => 'Definiamo contenuti e messaggi per rendere il brand riconoscibile e coerente sui canali piu adatti al tuo pubblico.'],
            ['titolo' => 'Content marketing', 'testo' => 'Produciamo contenuti utili per spiegare, posizionare e valorizzare servizi, prodotti e competenze della tua azienda.'],
            ['titolo' => 'Email marketing', 'testo' => 'Organizziamo comunicazioni e newsletter per coltivare contatti, promuovere offerte e mantenere vivo il rapporto con i clienti.'],
            ['titolo' => 'Web analytics', 'testo' => 'Misuriamo comportamento degli utenti, conversioni e performance per prendere decisioni basate sui dati.'],
            ['titolo' => 'E-commerce ' . $citta, 'testo' => 'Creiamo negozi online chiari, veloci e gestibili, pensati per valorizzare catalogo, prodotti e processo di acquisto.'],
        ];

        foreach ($servizi as $servizio) :
        ?>
          <li class="no-icon">
            <div class="servizi-4-2-card">
              <div class="testo-anteprima">
                <div class="titolo"><?php echo esc_html($servizio['titolo']); ?></div>
                <div class="testo"><?php echo esc_html($servizio['testo']); ?></div>
              </div>
              <div class="servizi-4-2-action">
                <span class="servizi-4-2-related" aria-hidden="true"></span>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<div class="contenitore back-bonus-color">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="struttura-container struttura-2-standard centro-verticale">
      <div class="div-1 txt-bianco">
        <h2><span class="testo-arancio"><?php echo esc_html($titolo_scelta); ?></span></h2>
        <?php echo wp_kses_post($testo_scelta); ?>
      </div>

      <div class="div-2">
        <?php if (!empty($wa_url_professional)) : ?>
          <img class="flex" src="<?php echo esc_url($wa_url_professional); ?>" alt="web agency professionale <?php echo esc_attr($citta_lowercase); ?>">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
$faq_title = $titolo_faq;
$faq_rows = $wa_faq_rows;

$page_faq_rows = function_exists('get_field')
    ? $normalize_wa_rows(get_field('fld_faq', $post_id), 'fld_faq_tit', 'fld_faq_txt')
    : [];

if (!empty($page_faq_rows)) {
    $faq_rows = $page_faq_rows;
}

if (function_exists('chefstudio_schema_collect_faq_items')) {
    chefstudio_schema_collect_faq_items($faq_rows, 'local-web-agency');
}
?>

<div class="contenitore">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <section class="faq-content faq-premium faq-standalone-layout">
      <div class="faq-intro">
        <h2><?php echo esc_html($faq_title); ?></h2>
      </div>

      <div class="faq-list">
        <?php foreach ($faq_rows as $index => $row) :
          $titolo_txt = trim(wp_strip_all_tags((string) ($row['titolo'] ?? '')));
          $testo = $row['testo'] ?? '';
          $testo_txt = trim(wp_strip_all_tags((string) $testo));
          if ($titolo_txt === '' && $testo_txt === '') continue;
          $id = 'faq-title-wa-' . get_queried_object_id() . '-' . ($index + 1);
        ?>
          <div class="faq-accordion">
            <input type="checkbox" class="tgg-title" id="<?php echo esc_attr($id); ?>">

            <div class="faq-accordion-title">
              <label for="<?php echo esc_attr($id); ?>">
                <span class="faq-toggle-icon" aria-hidden="true"></span>
                <span class="faq-question-text"><?php echo esc_html($titolo_txt); ?></span>
                <span class="arrow-icon" aria-hidden="true">
                  <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/arrow-down.svg'); ?>" alt="Apri risposta" aria-hidden="true">
                </span>
              </label>
            </div>

            <?php if ($testo_txt !== '') : ?>
              <div class="faq-accordion-content">
                <div class="faq-answer-inner">
                  <?php echo wp_kses_post($testo); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>

<?php $svg_base_url = get_stylesheet_directory_uri() . '/img/svg/'; ?>

<section class="contenitore local-seo-contact-section chef-wa-form-section" aria-labelledby="wa-form-title">
  <div class="contenuto-standard pad-x-standard local-seo-contact-wrap">
    <span id="form-ancora" class="local-seo-anchor" aria-hidden="true"></span>
    <span id="form-anchor" class="local-seo-anchor" aria-hidden="true"></span>

    <div class="local-seo-contact">
	      <div class="local-seo-contact-copy">
		        <h2 id="wa-form-title">Web Agency <?php echo esc_html($citta); ?></h2>
		        <?php
		        /*
		         * Rimosso dal layout CTA form del 2026-06-06:
		         * <div class="local-seo-contact-intro">Parlaci della tua attività e degli obiettivi che vuoi raggiungere online. Ti aiutiamo a individuare le azioni più efficaci per sviluppare la tua presenza digitale.</div>
		         */
		        ?>

        <div class="local-seo-consultant-card">
          <?php if (!empty($wa_url_finale)) : ?>
            <figure class="local-seo-consultant-photo">
              <img
                src="<?php echo esc_url($wa_url_finale); ?>"
                alt="Web Agency <?php echo esc_attr($citta); ?> - Chef Studio"
              >
            </figure>
          <?php endif; ?>

	          <div class="local-seo-consultant-info">
	            <h3>Andrea Biagi</h3>
	            <p>Web designer</p>

            <ul class="local-seo-benefits">
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-data-driven.svg'); ?>" alt="SEO siti web e comunicazione" aria-hidden="true">
                <span>SEO, siti web e comunicazione coordinati</span>
              </li>
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-referente-unico.svg'); ?>" alt="Referente unico" aria-hidden="true">
                <span>Un unico referente per il progetto</span>
              </li>
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-strategia.svg'); ?>" alt="Esperienza nel digitale" aria-hidden="true">
                <span>Oltre 15 anni di esperienza</span>
              </li>
            </ul>
          </div>
        </div>

		        <?php
		        /*
		         * Rimosso dal layout CTA form del 2026-06-06:
		         * <div class="local-seo-process">
		         *   <h3>Come funziona?</h3>
		         *   <p>Esaminiamo il progetto nel suo insieme e individuiamo le attività che possono generare il maggiore impatto.</p>
		         * </div>
		         */
		        ?>

		        <div class="local-seo-trust-note">
		          <img src="<?php echo esc_url($svg_base_url . 'icona-sicuro.svg'); ?>" alt="Confronto strategico" aria-hidden="true">
		          <span>Valutazione gratuita su misura, poi scegli come procedere.</span>
		        </div>

	        <div class="local-seo-phone-row">
	          <a class="local-seo-whatsapp-inline-icon" href="https://wa.me/<?php echo esc_attr($telefono_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="Scrivici su WhatsApp">
	            <img src="<?php echo esc_url($svg_base_url . 'whatsapp.svg'); ?>" alt="WhatsApp">
	          </a>
	          <span>Chiamaci: <a class="local-seo-phone-link" href="tel:<?php echo esc_attr($telefono_link); ?>"><strong><?php echo esc_html($telefono); ?></strong></a></span>
	        </div>
	      </div>

	      <div class="local-seo-form-card">
	        <div class="local-seo-form-heading">
	          <h3>Richiedi un'analisi preliminare.</h3>
	          <p>Raccontaci il tuo caso, troveremo le migliori opportunità.</p>
	        </div>

        <div class="local-seo-form-response-time">
          <img src="<?php echo esc_url($svg_base_url . 'icona-orologio.svg'); ?>" alt="Risposta entro un giorno lavorativo" aria-hidden="true">
          <span>Rispondiamo normalmente entro un giorno lavorativo.</span>
        </div>

        <div class="contatti local-seo-gravity-form">
	          <?php
		          if (function_exists('gravity_form')) {
		              gravity_form(1, false, false, false, '', true, 0);
		          }
		          ?>
        </div>
      </div>
    </div>
  </div>
</section>
</main>

<?php get_footer(); ?>
