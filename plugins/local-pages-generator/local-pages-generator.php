<?php
/**
 * Plugin Name: Local Pages Generator
 * Description: Generatore di pagine locali da dump SQL + ACF (immagini argomento, FAQ repeater) + Yoast.
 * Version: 1.6.2
 */


 
if ( ! defined('ABSPATH') ) exit;
error_log('LPG: file principale caricato con successo');


define('LPG_PATH', plugin_dir_path(__FILE__));
define('LPG_URL', plugin_dir_url(__FILE__));
define('LPG_RS_HTML_DIR', WP_CONTENT_DIR . '/uploads/realizzazione-siti-html');
define('LPG_WA_TERM_SLUG', 'web-agency');



require_once LPG_PATH.'includes/helpers.php';
require_once LPG_PATH.'includes/class-import.php';
require_once LPG_PATH.'includes/web-agency-variants.php';


// Inizializza subito il modulo di import (aggancia gli admin_post_*)
if (class_exists('LPG_Import')) {
    LPG_Import::init();
    error_log('⚙️ LPG_Import::init richiamato direttamente dal plugin main');
}

// 🔧 Wrapper per tutte le azioni admin-post del plugin

// Import da SQL
add_action('admin_post_lpg_import_db', function () {
    error_log('🔥 admin_post_lpg_import_db chiamato');

    if (!class_exists('LPG_Import')) {
        require_once LPG_PATH . 'includes/class-import.php';
    }

    LPG_Import::handle_import();
});

// Rigenerazione immagini per tutte le pagine
add_action('admin_post_lpg_regenerate_images', function () {
    error_log('🔥 admin_post_lpg_regenerate_images chiamato');

    if (!class_exists('LPG_Import')) {
        require_once LPG_PATH . 'includes/class-import.php';
    }

    LPG_Import::regenerate_all_images();
});

// Rigenera immagini selettive (pagina "Rigenera Immagini")
add_action('admin_post_lpg_rigenera_immagini', function () {
    error_log('🔥 admin_post_lpg_rigenera_immagini chiamato');

    if (!class_exists('LPG_Import')) {
        require_once LPG_PATH . 'includes/class-import.php';
    }

    LPG_Import::handle_rigenera_immagini();
});

// Import HTML realizzazione siti
add_action('admin_post_lpg_import_rs_html', function () {
    error_log('🔥 admin_post_lpg_import_rs_html chiamato');

    if (!class_exists('LPG_Import')) {
        require_once LPG_PATH . 'includes/class-import.php';
    }

    LPG_Import::handle_import_rs_html();
});




// Rigenera immagini per Realizzazione Siti
add_action('admin_post_lpg_regenerate_images_rs', function() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');
    require_once LPG_PATH.'includes/class-import.php';
    LPG_Import::regenerate_all_images_rs();
});

// Rigenera immagini selettive per Realizzazione Siti
add_action('admin_post_lpg_rigenera_immagini_rs', function() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');
    require_once LPG_PATH.'includes/class-import.php';
    LPG_Import::handle_rigenera_immagini_rs();
});

// Rigenera immagini per Web Agency
add_action('admin_post_lpg_regenerate_images_wa', function() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');
    require_once LPG_PATH.'includes/class-import.php';
    LPG_Import::regenerate_all_images_wa();
});

// Sincronizza title/description Yoast per Web Agency
add_action('admin_post_lpg_sync_yoast_wa', function() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');
    require_once LPG_PATH.'includes/class-import.php';
    LPG_Import::sync_all_yoast_wa();
});




// Crea il termine Web Agency dentro la tassonomia delle Pagine Locali.
add_action('init', function() {
    if (!taxonomy_exists('argomento_categoria')) {
        return;
    }

    if (!term_exists(LPG_WA_TERM_SLUG, 'argomento_categoria')) {
        wp_insert_term('Web Agency', 'argomento_categoria', ['slug' => LPG_WA_TERM_SLUG]);
    }
}, 15);


// Carica il template Web Agency senza modificare il routing esistente del tema.
add_filter('single_template', function($template) {
    global $post;

    if (!$post || $post->post_type !== 'pagine-locali') {
        return $template;
    }

    $terms = wp_get_post_terms($post->ID, 'argomento_categoria', ['fields' => 'slugs']);
    if (is_wp_error($terms) || !in_array(LPG_WA_TERM_SLUG, $terms, true)) {
        return $template;
    }

    $tpl = get_stylesheet_directory() . '/pagine/template-pagine-web-agency.php';
    return file_exists($tpl) ? $tpl : $template;
}, 30);


// Campi immagine e testi default per la categoria Web Agency. I campi sono sul
// termine; la singola pagina puo solo scegliere quale variante usare.
add_action('acf/init', function() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $image_fields = [
        'wa_immagine_main'          => 'Hero - web-agency-{citta}.jpg',
        'wa_immagine_designer'      => 'Mini profilo - web-designer-{citta}.jpg',
        'wa_immagine_agency'        => 'Sezione agenzia - agenzia-web-{citta}.jpg',
        'wa_immagine_professionale' => 'Sezione professionale - web-agency-professionale-{citta}.png',
        'wa_immagine_finale'        => 'Contatti - web-agency-chef-studio-{citta}.jpg',
        'wa_immagine_sito_1'        => 'Esempio 1 - siti-web-per-artigiani-{citta}.jpg',
        'wa_immagine_sito_2'        => 'Esempio 2 - siti-per-aziende-agricole-{citta}.jpg',
        'wa_immagine_sito_3'        => 'Esempio 3 - siti-per-hotel-case-vacanza-{citta}.jpg',
        'wa_immagine_sito_4'        => 'Esempio 4 - siti-per-aziende-{citta}.jpg',
    ];

    $text_fields = [
        'wa_hero_h1_default'              => ['H1 hero', 'text', 'hero'],
        'wa_h2_hero_default'              => ['Sottotitolo hero', 'text', 'hero'],
        'wa_paragrafo_intro_default'      => ['Testo hero', 'wysiwyg', 'hero'],
        'wa_phone_prompt_default'         => ['Testo telefono hero', 'text', 'hero'],
        'wa_titolo_sezione_1_default'     => ['Titolo sezione Web Agency', 'text', 'agency'],
        'wa_testo_sezione_1_default'      => ['Testo sezione Web Agency', 'wysiwyg', 'agency'],
        'wa_titolo_1_sezione_2_default'   => ['Titolo colonna siti/WordPress', 'text', 'web_seo'],
        'wa_testo_1_sezione_2_default'    => ['Testo colonna siti/WordPress', 'wysiwyg', 'web_seo'],
        'wa_titolo_2_sezione_2_default'   => ['Titolo colonna SEO/marketing', 'text', 'web_seo'],
        'wa_testo_2_sezione_2_default'    => ['Testo colonna SEO/marketing', 'wysiwyg', 'web_seo'],
        'wa_cta_testo_default'            => ['Testo call to action centrale', 'wysiwyg', 'cta'],
        'wa_cta_button_default'           => ['Testo bottone CTA centrale', 'text', 'cta'],
        'wa_examples_heading_default'     => ['Titolo esempi siti', 'text', 'examples_services'],
        'wa_example_site_1_text_default'  => ['Testo esempio sito 1', 'text', 'examples_services'],
        'wa_example_site_2_text_default'  => ['Testo esempio sito 2', 'text', 'examples_services'],
        'wa_example_site_3_text_default'  => ['Testo esempio sito 3', 'text', 'examples_services'],
        'wa_example_site_4_text_default'  => ['Testo esempio sito 4', 'text', 'examples_services'],
        'wa_services_heading_default'     => ['Titolo griglia servizi', 'text', 'examples_services'],
        'wa_intro_servizi_default'        => ['Intro servizi', 'wysiwyg', 'examples_services'],
        'wa_titolo_scelta_default'        => ['Titolo sezione perche scegliere', 'text', 'choice'],
        'wa_testo_scelta_default'         => ['Testo sezione perche scegliere', 'wysiwyg', 'choice'],
        'wa_titolo_faq_default'           => ['Titolo FAQ', 'text', 'faq'],
        'wa_titolo_contatti_default'      => ['Titolo sezione contatti', 'text', 'contacts'],
        'wa_sottotitolo_contatti_default' => ['Sottotitolo sezione contatti', 'text', 'contacts'],
        'wa_testo_form_default'           => ['Testo sopra il form', 'textarea', 'contacts'],
    ];

    $text_groups = [
        'hero'              => 'Hero',
        'agency'            => 'Sezione Web Agency',
        'web_seo'           => 'Web design / SEO',
        'cta'               => 'CTA centrale',
        'examples_services' => 'Esempi e servizi',
        'choice'            => 'Perche scegliere',
        'faq'               => 'FAQ',
        'contacts'          => 'Contatti',
    ];

    $variants = function_exists('lpg_wa_text_variants') ? lpg_wa_text_variants() : [];
    $base_defaults = function_exists('lpg_wa_base_text_defaults') ? lpg_wa_base_text_defaults() : [];
    $variant_choices = [
        'base' => 'Testi base categoria',
    ];

    foreach ($variants as $variant_key => $variant) {
        $variant_choices[$variant_key] = $variant['label'] ?? $variant_key;
    }

    $fields = [];

    $field_key = function($name) {
        return 'field_lpg_' . preg_replace('/[^a-z0-9_]+/', '_', strtolower((string) $name));
    };

    $add_tab = function($key, $label) use (&$fields, $field_key) {
        $fields[] = [
            'key'       => $field_key('wa_tab_' . $key),
            'label'     => $label,
            'name'      => '',
            'type'      => 'tab',
            'placement' => 'top',
        ];
    };

    $add_accordion = function($key, $label, $open = 0) use (&$fields, $field_key) {
        $fields[] = [
            'key'          => $field_key('wa_acc_' . $key),
            'label'        => $label,
            'name'         => '',
            'type'         => 'accordion',
            'open'         => $open,
            'multi_expand' => 0,
            'endpoint'     => 0,
        ];
    };

    $end_accordion = function($key) use (&$fields, $field_key) {
        $fields[] = [
            'key'      => $field_key('wa_acc_end_' . $key),
            'label'    => '',
            'name'     => '',
            'type'     => 'accordion',
            'endpoint' => 1,
        ];
    };

    $add_text_field = function($name, $label, $type, $default = '') use (&$fields, $field_key) {
        $acf_field = [
            'key'           => $field_key($name),
            'label'         => $label,
            'name'          => $name,
            'type'          => $type,
            'instructions'  => 'Placeholder disponibili: {citta}, {città}, {citta_lowercase}, {citta_slug}, {telefono}, {telefono_link}.',
            'default_value' => $default,
        ];

        if ($type === 'wysiwyg') {
            $acf_field['tabs'] = 'all';
            $acf_field['toolbar'] = 'basic';
            $acf_field['media_upload'] = 0;
        }

        $fields[] = $acf_field;
    };

    $add_faq_repeater = function($name, $label) use (&$fields, $field_key) {
        $fields[] = [
            'key'          => $field_key($name),
            'label'        => $label,
            'name'         => $name,
            'type'         => 'repeater',
            'instructions' => 'Se la singola pagina non ha FAQ compilate, il template usa queste. Placeholder disponibili anche qui.',
            'layout'       => 'row',
            'button_label' => 'Aggiungi FAQ',
            'sub_fields'   => [
                [
                    'key'   => $field_key($name . '_titolo'),
                    'label' => 'Domanda',
                    'name'  => 'wa_faq_titolo',
                    'type'  => 'text',
                ],
                [
                    'key'          => $field_key($name . '_testo'),
                    'label'        => 'Risposta',
                    'name'         => 'wa_faq_testo',
                    'type'         => 'wysiwyg',
                    'tabs'         => 'all',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ],
            ],
        ];
    };

    $add_service_repeater = function($name, $label) use (&$fields, $field_key) {
        $fields[] = [
            'key'          => $field_key($name),
            'label'        => $label,
            'name'         => $name,
            'type'         => 'repeater',
            'instructions' => 'Card della griglia servizi. Placeholder disponibili anche qui.',
            'layout'       => 'row',
            'button_label' => 'Aggiungi servizio',
            'sub_fields'   => [
                [
                    'key'   => $field_key($name . '_titolo'),
                    'label' => 'Titolo servizio',
                    'name'  => 'wa_service_titolo',
                    'type'  => 'text',
                ],
                [
                    'key'   => $field_key($name . '_testo'),
                    'label' => 'Testo servizio',
                    'name'  => 'wa_service_testo',
                    'type'  => 'textarea',
                ],
            ],
        ];
    };

    $add_text_variant = function($tab_key, $tab_label, $variant_key, $defaults) use (
        $add_tab,
        $add_accordion,
        $end_accordion,
        $add_text_field,
        $add_faq_repeater,
        $add_service_repeater,
        $text_fields,
        $text_groups
    ) {
        $add_tab($tab_key, $tab_label);

        foreach ($text_groups as $group_key => $group_label) {
            $add_accordion($tab_key . '_' . $group_key, $group_label, $group_key === 'hero' ? 1 : 0);

            foreach ($text_fields as $base_name => $field) {
                if ($field[2] !== $group_key) {
                    continue;
                }

                $field_name = $variant_key && function_exists('lpg_wa_variant_field_name')
                    ? lpg_wa_variant_field_name($base_name, $variant_key)
                    : $base_name;

                $add_text_field($field_name, $field[0], $field[1], $defaults[$base_name] ?? '');
            }

            if ($group_key === 'examples_services') {
                $service_name = $variant_key && function_exists('lpg_wa_variant_field_name')
                    ? lpg_wa_variant_field_name('wa_service_cards_default', $variant_key)
                    : 'wa_service_cards_default';

                $add_service_repeater($service_name, 'Card servizi');
            }

            if ($group_key === 'faq') {
                $faq_name = $variant_key && function_exists('lpg_wa_variant_field_name')
                    ? lpg_wa_variant_field_name('wa_faq_default', $variant_key)
                    : 'wa_faq_default';

                $add_faq_repeater($faq_name, 'FAQ');
            }
        }

        $end_accordion($tab_key);
    };

    $add_tab('impostazioni', 'Impostazioni');
    $fields[] = [
        'key'           => 'field_lpg_wa_default_variant',
        'label'         => 'Variante predefinita',
        'name'          => 'wa_default_variant',
        'type'          => 'select',
        'instructions'  => 'Se una pagina Web Agency non specifica una variante, usera questa.',
        'choices'       => $variant_choices,
        'default_value' => 'variant_1',
        'return_format' => 'value',
        'ui'            => 1,
    ];

    $fields[] = [
        'key'     => 'field_lpg_wa_testi_default_note',
        'label'   => 'Come funzionano i testi',
        'name'    => '',
        'type'    => 'message',
        'message' => 'Precedenza frontend: testo compilato nella pagina singola → variante scelta nella pagina → variante predefinita della categoria → testi base categoria → fallback template. Nei testi puoi usare {citta}, {città}, {citta_lowercase}, {citta_slug}, {telefono} e {telefono_link}.',
    ];

    $add_tab('immagini', 'Immagini');
    foreach ($image_fields as $name => $label) {
        $fields[] = [
            'key'           => 'field_lpg_' . $name,
            'label'         => $label,
            'name'          => $name,
            'type'          => 'image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
            'library'       => 'all',
        ];
    }

    $add_text_variant('testi_base', 'Testi base', '', $base_defaults);

    foreach ($variants as $variant_key => $variant) {
        $add_text_variant(
            $variant_key,
            $variant['label'] ?? $variant_key,
            $variant_key,
            $variant['fields'] ?? []
        );
    }

    acf_add_local_field_group([
        'key'      => 'group_lpg_web_agency_images',
        'title'    => 'Web Agency - categoria',
        'fields'   => $fields,
        'location' => [
            [
                [
                    'param'    => 'taxonomy',
                    'operator' => '==',
                    'value'    => 'argomento_categoria',
                ],
            ],
        ],
        'menu_order'            => 40,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ]);

    $page_variant_choices = [
        'inherit' => 'Usa variante predefinita della categoria',
        'base'    => 'Testi base categoria',
    ];

    foreach ($variants as $variant_key => $variant) {
        $page_variant_choices[$variant_key] = $variant['label'] ?? $variant_key;
    }

    acf_add_local_field_group([
        'key'      => 'group_lpg_web_agency_page_settings',
        'title'    => 'Web Agency - impostazioni pagina',
        'fields'   => [
            [
                'key'           => 'field_lpg_wa_text_variant',
                'label'         => 'Variante testi',
                'name'          => 'wa_text_variant',
                'type'          => 'select',
                'instructions'  => 'Lascia la prima opzione per usare la variante predefinita scelta nella categoria Web Agency. I testi compilati nei campi della pagina hanno comunque la precedenza.',
                'choices'       => $page_variant_choices,
                'default_value' => 'inherit',
                'return_format' => 'value',
                'ui'            => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'pagine-locali',
                ],
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'argomento_categoria:' . LPG_WA_TERM_SLUG,
                ],
            ],
        ],
        'menu_order'            => 41,
        'position'              => 'side',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ]);
});





// Genera i permalink del CPT "pagine-locali" senza prefisso, ma senza toccare le pagine normali
add_filter('post_type_link', function($permalink, $post, $leavename){
    if ($post->post_type === 'pagine-locali') {
        return home_url( user_trailingslashit($post->post_name) );
    }
    return $permalink;
}, 10, 3);

/**
 * ===== Rewrite dinamico =====
 * Registra regole /{slug}/ → pagine-locali
 */
function lpg_register_rewrite_rules() {
    $posts = get_posts([
        'post_type'      => 'pagine-locali',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'fields'         => 'ids',
    ]);

    if ($posts) {
        $slugs = [];
        foreach ($posts as $id) {
            $slug = get_post_field('post_name', $id);
            if ($slug) $slugs[] = $slug;
        }

        if (!empty($slugs)) {
            $escaped = array_map('preg_quote', $slugs);
            $pattern = '^(' . implode('|', $escaped) . ')/?$';
            add_rewrite_rule($pattern, 'index.php?post_type=pagine-locali&name=$matches[1]', 'top');
        }
    }
}
add_action('init', 'lpg_register_rewrite_rules', 20);


// Flush ritardato dopo importazione
add_action('init', function() {
    if (get_option('lpg_needs_flush')) {
        delete_option('lpg_needs_flush');
        if (function_exists('lpg_register_rewrite_rules')) {
            lpg_register_rewrite_rules();
        }
        flush_rewrite_rules(false);
        error_log('✅ Flush rewrite eseguito dopo import');
    }
}, 99);
















/**
 * ===== Template registration =====
 * Permette al CPT di usare i template personalizzati come una pagina.
 */
add_filter('theme_page_templates', function($templates) {
    $templates['template-tutto-integrato.php'] = 'Template Tutto Integrato (Local Pages)';
    return $templates;
});


/**
 * ===== Rewrite Flush =====
 */
register_activation_hook(__FILE__, function(){
    do_action('init');
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function(){
    flush_rewrite_rules();
});


/**
 * ===== Init moduli =====
 */
add_action('plugins_loaded', function(){
    // Inizializza moduli interni solo dopo che WP e ACF sono pronti
    if ( class_exists('LPG_ACF') )  LPG_ACF::init();
    if ( class_exists('LPG_Admin') ) LPG_Admin::init();
});







// 🔄 Aggiorna le rewrite rules ogni volta che viene creata o modificata una pagina locale
add_action('save_post_pagine-locali', function($post_id){
    if (wp_is_post_revision($post_id)) return;
    
    // Evita loop multipli nello stesso salvataggio
    static $done = false;
    if ($done) return;
    $done = true;

    // Rigenera la regola personalizzata del CPT
    if (function_exists('lpg_register_rewrite_rules')) {
        lpg_register_rewrite_rules();
    }

    // Flush parziale (solo CPT)
    flush_rewrite_rules(false);

    error_log('🔁 Rewrite rules aggiornate automaticamente dopo salvataggio pagina locale: ID ' . $post_id);
}, 30);
