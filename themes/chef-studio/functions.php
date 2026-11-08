<?php

add_filter('acf/load_value', function($value, $post_id, $field){
    if ($post_id == 2895 && in_array($field['name'], ['h1','h2','meta_title','meta_description'])) {
        error_log("🔎 ACF sta caricando il campo {$field['name']} con valore: " . print_r($value, true));
    }
    return $value;
}, 10, 3);


// Fix: impedisce ad ACF di caricare valori “iniettati” da hook precedenti o JSON
add_filter('acf/load_value', function($value, $post_id, $field){
    if (get_post_type($post_id) === 'pagine-locali'
        && in_array($field['name'], ['h1','h2','meta_title','meta_description','immagine_main','immagine_secondary','immagine_last'])) {
        // Legge solo il valore nel DB e ignora qualsiasi prefill
        $real = get_post_meta($post_id, $field['name'], true);
        return $real !== '' ? $real : $value;
    }
    return $value;
}, 1, 3);

add_filter('acf/settings/save_json', '__return_false');
add_filter('acf/settings/load_json', '__return_false');














/**
 * Riconosce il layout landing ACF, anche dentro un blocco riutilizzabile.
 * Legge i valori senza avanzare il loop have_rows()/the_row() dei template.
 * Si controllano tutte le righe: la presenza della hero cambia anche l'header.
 */
function chefstudio_has_landing_critical_layout($post_id, $seen = []) {
    $post_id = absint($post_id);

    if (!$post_id || isset($seen[$post_id]) || !function_exists('get_field')) {
        return false;
    }

    $seen[$post_id] = true;
    $rows = get_field('layout_flessibile_1', $post_id);

    foreach ((array) $rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $layout = $row['acf_fc_layout'] ?? '';

        if ($layout === 'hero_landing_ads') {
            return true;
        }

        if ($layout === 'blocco_riutilizzabile') {
            $block = $row['seleziona_blocco_riutilizzabile'] ?? 0;
            $block_id = is_object($block) ? ($block->ID ?? 0) : (is_array($block) ? ($block['ID'] ?? 0) : $block);

            if (chefstudio_has_landing_critical_layout($block_id, $seen)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * CSS principali: samples resta bloccante per le strutture condivise.
 * Il critical di custom resta dopo chef-vars, nella stessa posizione di cascata.
 */
add_action('wp_enqueue_scripts', function () {

    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    $ver = wp_get_theme()->get('Version');

    $critical = [
        'chef-custom-critical' => ['/css/critical/chef-custom-critical.css', ['chef-vars'], 'all'],
    ];

    if (is_singular() && chefstudio_has_landing_critical_layout(get_queried_object_id())) {
        // Le basi della landing precedono i reset comuni a pari specificita
        // (es. .gchoice label). Evita di duplicarli nel supplemento.
        $critical = [
            'chef-landing-critical' => ['/css/critical/chef-landing-critical.css', ['chef-vars'], 'all'],
            'chef-custom-critical' => ['/css/critical/chef-custom-critical.css', ['chef-landing-critical'], 'all'],
        ];
    }

    // Un deploy incompleto deve mantenere il foglio completo render-blocking.
    $critical_ready = true;
    foreach ($critical as [$path]) {
        if (!is_readable($dir . $path)) {
            $critical_ready = false;
        }
    }

    $css = [

        'chef-style'    => ['/style.css', [], 'screen'],
        'chef-samples'  => ['/css/samples.css', ['chef-style'], 'all'],
        'chef-menu'     => ['/css/menu.css', ['chef-samples'], 'all'],
        'chef-vars'     => ['/css/chef-themes/chef-var.css', ['chef-menu'], 'all'],
    ];

    $custom_deps = ['chef-vars'];
    if ($critical_ready) {
        $css = array_merge($css, $critical);
        $custom_deps = [array_key_last($critical)];
    }
    $css['chef-custom'] = ['/css/chef-themes/chef-custom.css', $custom_deps, 'all'];

    foreach ($css as $handle => [$path, $deps, $media]) {
        wp_enqueue_style(
            $handle,
            $path === '/style.css' ? get_stylesheet_uri() : $uri . $path,
            $deps,
            file_exists($dir . $path) ? filemtime($dir . $path) : $ver,
            $media
        );
    }

    wp_style_add_data('chef-custom', 'chef_nonblocking', $critical_ready);

}, 1);


/**
 * Differisce soltanto chef-custom e conserva gli attributi del link WordPress.
 * Il link rimane in posizione: onload cambia solo rel, non la cascata finale.
 * Il filtro Gravity Forms sottostante rimane indipendente e invariato.
 */
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
    if ($handle !== 'chef-custom' || !wp_styles()->get_data($handle, 'chef_nonblocking') || !class_exists('WP_HTML_Tag_Processor')) {
        return $html;
    }

    $link = new WP_HTML_Tag_Processor($html);
    if (!$link->next_tag('LINK') || $link->get_attribute('rel') !== 'stylesheet') {
        return $html;
    }

    $link->set_attribute('rel', 'preload');
    $link->set_attribute('as', 'style');
    $link->set_attribute('onload', "this.onload=null;this.rel='stylesheet'");

    return $link->get_updated_html() . '<noscript>' . $html . "</noscript>\n";
}, 20, 4);

/**
 * CSS custom per il banner cookie.
 * Viene accodato dopo i fogli stile di Complianz, quando presenti.
 */
add_action('wp_enqueue_scripts', function () {

    global $wp_styles;

    $dir  = get_stylesheet_directory();
    $uri  = get_stylesheet_directory_uri();
    $path = '/css/cookie-banner-cs.css';
    $deps = ['chef-custom'];

    if ($wp_styles instanceof WP_Styles) {
        foreach ((array) $wp_styles->queue as $handle) {
            if (preg_match('/(?:cmplz|complianz)/i', (string) $handle)) {
                $deps[] = $handle;
            }
        }
    }

    wp_enqueue_style(
        'chef-cookie-banner',
        $uri . $path,
        array_values(array_unique($deps)),
        file_exists($dir . $path) ? filemtime($dir . $path) : wp_get_theme()->get('Version'),
        'all'
    );

}, 999);




/**
 * Carica i CSS di Gravity Forms in modo non render-blocking.
 * Utile se i form sono quasi sempre in fondo alla pagina.
 */
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {

    $defer_styles = [
        'gforms_reset_css',
        'gforms_formsmain_css',
        'gforms_ready_class_css',
        'gforms_browsers_css',
    ];

    if (!in_array($handle, $defer_styles, true)) {
        return $html;
    }

    $media = $media ?: 'all';

    return sprintf(
        "<link rel='preload' href='%s' as='style' media='%s' onload=\"this.onload=null;this.rel='stylesheet'\">\n" .
        "<noscript><link rel='stylesheet' href='%s' media='%s'></noscript>\n",
        esc_url($href),
        esc_attr($media),
        esc_url($href),
        esc_attr($media)
    );

}, 10, 4);


/**
 * Lascia al browser l'ordine naturale del focus nei form Gravity Forms.
 */
add_filter('gform_tabindex', '__return_false');

/**
 * Evita label vuote sui campi reCAPTCHA di Gravity Forms.
 * Il target del for e' un div, non un controllo di form etichettabile.
 */
add_filter('gform_field_content', function ($field_content, $field, $value, $lead_id, $form_id) {
    if (!is_object($field) || empty($field->type) || $field->type !== 'captcha') {
        return $field_content;
    }

    $field_id = !empty($field->id) ? $field->id : 0;

    if (!$field_id) {
        return $field_content;
    }

    $target_id = 'input_' . absint($form_id) . '_' . absint($field_id);

    return preg_replace(
        '/<label\b[^>]*\bfor=(["\'])' . preg_quote($target_id, '/') . '\1[^>]*>\s*<\/label>/i',
        '',
        $field_content
    );
}, 10, 5);





/**
 * Rimuove jQuery Migrate nel frontend, se non necessario.
 */
add_action('wp_default_scripts', function ($scripts) {

    if (is_admin()) {
        return;
    }

    if (isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        );
    }

});







// =====================================================
// Inclusione file PHP dalla cartella /includes
// =====================================================
foreach (glob(get_template_directory() . '/includes/*.php') as $file) {
    require_once $file;
}

// =====================================================
// Sicurezza base
// =====================================================
if ( ! defined( 'ABSPATH' ) ) exit;

// =====================================================
// Setup del tema
// =====================================================
function tema_setup() {
    // Traduzioni
    load_theme_textdomain( 'chef-studio', get_template_directory() . '/languages' );

    // Supporto WooCommerce
    if ( class_exists( 'WooCommerce' ) ) {
        add_theme_support( 'woocommerce' );
    }
}
add_action( 'after_setup_theme', 'tema_setup' );



// =====================================================
// Carica i miei script
// =====================================================
function tema_enqueue_scripts() {
    $path = '/js/scripts-cs.js';
    $file = get_stylesheet_directory() . $path;

    wp_enqueue_script(
        'scripts-cs',
        get_stylesheet_directory_uri() . $path,
        array(),
        file_exists($file) ? filemtime($file) : wp_get_theme()->get('Version'),
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    if (is_page('Home') || is_page('Home EN')) {
        $parallax_path = '/js/parallax.js';
        $parallax_file = get_stylesheet_directory() . $parallax_path;

        wp_enqueue_script(
            'chef-parallax',
            get_stylesheet_directory_uri() . $parallax_path,
            array('jquery'),
            file_exists($parallax_file) ? filemtime($parallax_file) : wp_get_theme()->get('Version'),
            array(
                'in_footer' => true,
                'strategy'  => 'defer',
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'tema_enqueue_scripts');

// Impedisce a Gravity Forms di spostare la pagina al termine di un cambio
// schermata AJAX o dopo l'invio del modulo.
add_filter('gform_confirmation_anchor', '__return_false');

// Non fa attendere all'utente l'invio delle email di notifica prima di mostrare
// la conferma. Salvataggio della richiesta e controllo Turnstile restano sincroni.
add_filter('gform_is_asynchronous_notifications_enabled_7', '__return_true');
add_filter('gform_is_asynchronous_notifications_enabled_8', '__return_true');



// =====================================================
// Supporto editor classico (no Gutenberg)
// =====================================================
add_filter('use_block_editor_for_post', '__return_false');

// =====================================================
// Supporti nativi WordPress
// =====================================================
add_theme_support('post-thumbnails');
add_theme_support('responsive-embeds');
add_theme_support('wp-block-styles');
add_theme_support('align-wide');
add_theme_support( 'title-tag' ); // Per Yoast


/**
 * Ottimizza il comportamento responsive di tutte le immagini inserite negli editor di testo
 */
add_filter('wp_calculate_image_sizes', function ($sizes, $size, $image_src, $meta, $attachment_id) {
    // Rimuove "auto," e imposta una regola coerente per tutte le immagini
    return '(max-width: 480px) 100vw, (max-width: 768px) 90vw, 630px';
}, 10, 5);




if (!has_filter('the_content', 'wp_filter_content_tags')) {
    add_filter('the_content', 'wp_filter_content_tags', 12);
}

add_filter('wp_kses_allowed_html', function ($tags, $context) {
    if ($context !== 'post' || empty($tags['img']) || !is_array($tags['img'])) {
        return $tags;
    }

    $tags['img']['srcset']        = true;
    $tags['img']['sizes']         = true;
    $tags['img']['loading']       = true;
    $tags['img']['decoding']      = true;
    $tags['img']['fetchpriority'] = true;

    return $tags;
}, 10, 2);

if (!function_exists('chefstudio_maybe_update_missing_image_size')) {
    function chefstudio_maybe_update_missing_image_size($attachment_id, $size) {
        $attachment_id = absint($attachment_id);

        if (!$attachment_id || !is_string($size) || $size === '' || $size === 'full') {
            return;
        }

        static $checked = array();
        $cache_key = $attachment_id . '|' . $size;

        if (isset($checked[$cache_key])) {
            return;
        }

        $checked[$cache_key] = true;
        $metadata = wp_get_attachment_metadata($attachment_id);

        if (!empty($metadata['sizes'][$size]['file'])) {
            return;
        }

        if (!function_exists('wp_get_registered_image_subsizes')) {
            return;
        }

        $registered_sizes = wp_get_registered_image_subsizes();

        if (empty($registered_sizes[$size])) {
            return;
        }

        $mime_type = function_exists('get_post_mime_type') ? (string) get_post_mime_type($attachment_id) : '';

        if ($mime_type && !preg_match('#^image/(?:jpe?g|png|gif|webp|avif)$#i', $mime_type)) {
            return;
        }

        $file = function_exists('get_attached_file') ? get_attached_file($attachment_id) : '';

        if (!$file || !file_exists($file)) {
            return;
        }

        if (!function_exists('wp_update_image_subsizes') && defined('ABSPATH')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        if (function_exists('wp_update_image_subsizes')) {
            wp_update_image_subsizes($attachment_id);
        }
    }
}

if (!function_exists('chefstudio_attachment_id_from_image_value')) {
    function chefstudio_attachment_id_from_image_value($image) {
        if (is_numeric($image)) {
            return absint($image);
        }

        if (is_array($image)) {
            if (!empty($image['ID'])) {
                return absint($image['ID']);
            }

            if (!empty($image['id'])) {
                return absint($image['id']);
            }

            if (!empty($image['url'])) {
                $image = $image['url'];
            } else {
                return 0;
            }
        }

        if (!is_string($image) || trim($image) === '' || !function_exists('attachment_url_to_postid')) {
            return 0;
        }

        $url = trim($image);
        $attachment_id = attachment_url_to_postid($url);

        if ($attachment_id) {
            return absint($attachment_id);
        }

        $candidate = preg_replace('/-\d+x\d+(?=\.(?:jpe?g|png|gif|webp|avif)$)/i', '', $url);
        $candidate = preg_replace('/-scaled(?=\.(?:jpe?g|png|gif|webp|avif)$)/i', '', (string) $candidate);

        if ($candidate && $candidate !== $url) {
            $attachment_id = attachment_url_to_postid($candidate);
        }

        if ($attachment_id) {
            return absint($attachment_id);
        }

        $path = function_exists('wp_parse_url') ? wp_parse_url($candidate ?: $url, PHP_URL_PATH) : parse_url($candidate ?: $url, PHP_URL_PATH);

        if (!is_string($path) || !preg_match('#/(?:wp-content/)?uploads/#', $path)) {
            return 0;
        }

        $filename = $path ? wp_basename($path) : '';

        if ($filename !== '' && function_exists('get_posts')) {
            $attachments = get_posts([
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => [
                    [
                        'key'     => '_wp_attached_file',
                        'value'   => $filename,
                        'compare' => 'LIKE',
                    ],
                ],
            ]);

            if (!empty($attachments[0])) {
                return absint($attachments[0]);
            }
        }

        return 0;
    }
}

if (!function_exists('chefstudio_image_url_from_value')) {
    function chefstudio_image_url_from_value($image, $size = 'medium') {
        if (is_array($image)) {
            if (!empty($image['sizes'][$size])) {
                return (string) $image['sizes'][$size];
            }

            if (!empty($image['url'])) {
                return (string) $image['url'];
            }
        }

        if (is_numeric($image)) {
            return (string) wp_get_attachment_image_url(absint($image), $size);
        }

        if (is_string($image)) {
            return trim($image);
        }

        return '';
    }
}

if (!function_exists('chefstudio_responsive_image')) {
    function chefstudio_responsive_image($image, $size = 'medium', $attr = array()) {
        $attachment_id = chefstudio_attachment_id_from_image_value($image);

        if ($attachment_id) {
            chefstudio_maybe_update_missing_image_size($attachment_id, $size);

            if (!isset($attr['alt'])) {
                $attr['alt'] = (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
            }

            return wp_get_attachment_image($attachment_id, $size, false, $attr);
        }

        $url = chefstudio_image_url_from_value($image, $size);

        if ($url === '') {
            return '';
        }

        $attr = array_merge(array('src' => $url), $attr);
        $html = '<img';

        foreach ($attr as $name => $value) {
            if ($value === null || $value === false || $value === '') {
                continue;
            }

            $html .= ' ' . sanitize_key($name) . '="' . esc_attr($value) . '"';
        }

        $html .= '>';

        return $html;
    }
}

if (!function_exists('chefstudio_editor_image_size_name')) {
    function chefstudio_editor_image_size_name($img_tag = '') {
        if (is_string($img_tag) && false !== strpos($img_tag, 'hero-chip-icon')) {
            return 'thumbnail';
        }

        if (is_string($img_tag) && false !== strpos($img_tag, 'img-icon')) {
            return 'thumbnail';
        }

        return 'contenuto-articolo-desktop';
    }
}

if (!function_exists('chefstudio_editor_image_sizes')) {
    function chefstudio_editor_image_sizes($img_tag = '') {
        if (is_string($img_tag) && false !== strpos($img_tag, 'hero-chip-icon')) {
            return '18px';
        }

        if (is_string($img_tag) && false !== strpos($img_tag, 'img-icon')) {
            return '45px';
        }

        return '(max-width: 480px) 100vw, (max-width: 768px) 90vw, 630px';
    }
}

if (!function_exists('chefstudio_img_tag_set_attr')) {
    function chefstudio_img_tag_set_attr($tag, $name, $value) {
        $name = sanitize_key($name);

        if ($name === '' || !is_string($tag) || stripos($tag, '<img') !== 0) {
            return $tag;
        }

        $pattern = '/\s' . preg_quote($name, '/') . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i';

        if ($value === null || $value === false || $value === '') {
            return preg_replace($pattern, '', $tag, 1);
        }

        $attr = ' ' . $name . '="' . esc_attr($value) . '"';

        if (preg_match($pattern, $tag)) {
            return preg_replace($pattern, $attr, $tag, 1);
        }

        return preg_replace('/^<img\b/i', '<img' . $attr, $tag, 1);
    }
}

if (!function_exists('chefstudio_force_responsive_image_attrs')) {
    function chefstudio_force_responsive_image_attrs($html) {
        if (!is_string($html) || stripos($html, '<img') === false) {
            return $html;
        }

        return preg_replace_callback('/<img\b[^>]*>/i', function ($matches) {
            $tag = $matches[0];

            $attachment_id = 0;

            if (preg_match('/\bwp-image-(\d+)\b/', $tag, $class_match)) {
                $attachment_id = absint($class_match[1]);
            }

            if (!$attachment_id && preg_match('/\bsrc=["\']([^"\']+)["\']/', $tag, $src_match)) {
                $attachment_id = chefstudio_attachment_id_from_image_value(html_entity_decode($src_match[1], ENT_QUOTES, 'UTF-8'));
            }

            if (!$attachment_id) {
                return $tag;
            }

            $size_name = chefstudio_editor_image_size_name($tag);
            chefstudio_maybe_update_missing_image_size($attachment_id, $size_name);

            $image_src = function_exists('wp_get_attachment_image_src') ? wp_get_attachment_image_src($attachment_id, $size_name) : false;

            if (is_array($image_src) && !empty($image_src[0])) {
                $tag = chefstudio_img_tag_set_attr($tag, 'src', $image_src[0]);

                if (!empty($image_src[1]) && !empty($image_src[2])) {
                    $tag = chefstudio_img_tag_set_attr($tag, 'width', absint($image_src[1]));
                    $tag = chefstudio_img_tag_set_attr($tag, 'height', absint($image_src[2]));
                }
            }

            $srcset = function_exists('wp_get_attachment_image_srcset') ? wp_get_attachment_image_srcset($attachment_id, $size_name) : '';

            if (!$srcset) {
                return $tag;
            }

            $attrs = '';

            if (false === stripos($tag, ' srcset=')) {
                $attrs .= ' srcset="' . esc_attr($srcset) . '"';
            }

            if (false === stripos($tag, ' sizes=')) {
                $attrs .= ' sizes="' . esc_attr(chefstudio_editor_image_sizes($tag)) . '"';
            }

            if (false === stripos($tag, ' loading=')) {
                $attrs .= ' loading="lazy"';
            }

            if (false === stripos($tag, ' decoding=')) {
                $attrs .= ' decoding="async"';
            }

            if ($attrs === '') {
                return $tag;
            }

            return preg_replace('/^<img\b/i', '<img' . $attrs, $tag, 1);
        }, $html);
    }
}

if (!function_exists('chefstudio_optimize_editor_images')) {
    function chefstudio_optimize_editor_images($html) {
        if (!is_string($html) || stripos($html, '<img') === false) {
            return $html;
        }

        if (function_exists('wp_filter_content_tags')) {
            $html = wp_filter_content_tags($html, 'chefstudio-flexible-content');
        } elseif (function_exists('wp_make_content_images_responsive')) {
            $html = wp_make_content_images_responsive($html);
        }

        return chefstudio_force_responsive_image_attrs($html);
    }
}

if (!function_exists('chefstudio_responsive_editor_content')) {
    function chefstudio_responsive_editor_content($html) {
        return chefstudio_optimize_editor_images(wp_kses_post($html));
    }
}

add_filter('acf/format_value/type=wysiwyg', function ($value) {
    return chefstudio_optimize_editor_images($value);
}, 20);












/**
 * URL piatti per le sole pagine WordPress figlie.
 *
 * Esempio:
 * /pagina-genitore/pagina-figlia/
 * diventa:
 * /pagina-figlia/
 *
 * Non tocca CPT, articoli, tassonomie o altri post type.
 */


/**
 * Cambia il permalink pubblico SOLO delle pagine figlie normali.
 */
add_filter('page_link', function ($link, $post_id, $sample) {
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'page') {
        return $link;
    }

    // Solo pagine figlie.
    if ((int) $post->post_parent === 0) {
        return $link;
    }

    return home_url(user_trailingslashit($post->post_name));
}, 10, 3);


/**
 * Risolve /slug-figlia/ verso la pagina figlia corretta.
 * Versione più robusta: intercetta sia pagename sia name.
 */
add_filter('request', function ($query_vars) {
    if (is_admin()) {
        return $query_vars;
    }

    $requested_slug = '';

    if (!empty($query_vars['pagename'])) {
        $requested_slug = trim($query_vars['pagename'], '/');
    } elseif (!empty($query_vars['name'])) {
        $requested_slug = trim($query_vars['name'], '/');
    }

    if ($requested_slug === '') {
        return $query_vars;
    }

    // Interviene solo su URL a un livello: /qualcosa/
    // Non tocca /genitore/figlia/, /categoria/post/, CPT con struttura annidata, ecc.
    if (strpos($requested_slug, '/') !== false) {
        return $query_vars;
    }

    // Se esiste una pagina top-level con questo slug, non fare nulla.
    // Esempio: /contatti/ deve restare /contatti/.
    $top_level_page = get_posts([
        'post_type'      => 'page',
        'post_status'    => ['publish', 'private'],
        'name'           => $requested_slug,
        'post_parent'    => 0,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (!empty($top_level_page)) {
        return $query_vars;
    }

    // Cerca pagine figlie normali con quello slug.
    $child_pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => ['publish', 'private'],
        'name'           => $requested_slug,
        'posts_per_page' => 2,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (empty($child_pages)) {
        return $query_vars;
    }

    // Tieni solo vere pagine figlie.
    $child_pages = array_filter($child_pages, function ($page_id) {
        return (int) wp_get_post_parent_id($page_id) !== 0;
    });

    $child_pages = array_values($child_pages);

    // Se ci sono più pagine figlie con lo stesso slug, non rischiare.
    if (count($child_pages) !== 1) {
        return $query_vars;
    }

    // Forza WordPress a caricare quella pagina specifica.
    unset($query_vars['pagename'], $query_vars['name'], $query_vars['post_type']);

    $query_vars['page_id'] = (int) $child_pages[0];

    return $query_vars;
});


/**
 * Evita redirect canonici da /pagina-figlia/
 * verso /pagina-genitore/pagina-figlia/.
 */
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_admin() || !$redirect_url) {
        return $redirect_url;
    }

    $requested_path = trim(parse_url($requested_url, PHP_URL_PATH) ?? '', '/');
    $redirect_path  = trim(parse_url($redirect_url, PHP_URL_PATH) ?? '', '/');

    if (
        $requested_path &&
        $redirect_path &&
        strpos($requested_path, '/') === false &&
        str_ends_with($redirect_path, '/' . $requested_path)
    ) {
        return false;
    }

    return $redirect_url;
}, 10, 2);





/**
 * Blocca gli URL gerarchici delle pagine figlie.
 *
 * Esempio:
 * /pagina-genitore/pagina-figlia/
 *
 * deve dare 404, mentre:
 * /pagina-figlia/
 *
 * deve funzionare.
 *
 * Solo per pagine WordPress normali. Non tocca CPT, articoli o tassonomie.
 */
add_action('template_redirect', function () {
    if (is_admin() || !is_page()) {
        return;
    }

    $page = get_queried_object();

    if (!$page || $page->post_type !== 'page') {
        return;
    }

    // Solo pagine figlie.
    if ((int) $page->post_parent === 0) {
        return;
    }

    $flat_url = get_permalink($page);

    if (!$flat_url) {
        return;
    }

    $current_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
    $flat_path    = trim(parse_url($flat_url, PHP_URL_PATH) ?? '', '/');

    if (!$current_path || !$flat_path) {
        return;
    }

    // Se siamo già sull'URL piatto corretto, tutto ok.
    if ($current_path === $flat_path) {
        return;
    }

    /**
     * Se WordPress ha risolto comunque la pagina figlia tramite URL gerarchico,
     * blocchiamo quella versione e forziamo 404.
     *
     * Esempio:
     * current_path = test/agenzia-seo/consulenza-seo
     * flat_path    = test/consulenza-seo
     */
    if (
        str_ends_with($current_path, '/' . $page->post_name) &&
        $current_path !== $flat_path
    ) {
        global $wp_query;

        $wp_query->set_404();

        status_header(404);
        nocache_headers();

        $template_404 = get_404_template();

        if ($template_404) {
            include $template_404;
        }

        exit;
    }
}, 20);















// =====================================================
// Dimensioni immagini personalizzate
// =====================================================
add_image_size( 'post-preview', 470, 250, true );
add_image_size( 'loghi-piccoli', 200, 0, true );
add_image_size('hero-responsive', 720, 720, false);
add_image_size( 'contenuto-articolo', 800 );
add_image_size( 'hero-responsive-desktop', 630, 630, false );
add_image_size( 'contenuto-articolo-responsive', 686, 0, false );
add_image_size( 'contenuto-articolo-desktop', 630, 0, false );
add_image_size( 'anteprima-scroll-card', 244, 149, true );
add_image_size( 'apertura-piccola', 2000, 280, true );

// Aggiunta al selettore immagini
add_filter( 'image_size_names_choose', 'my_custom_sizes' );
function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'contenuto-articolo' => __('Contenuto Articolo'),
        'contenuto-articolo-responsive' => __('Contenuto Articolo Responsive'),
        'contenuto-articolo-desktop' => __('Contenuto Articolo Desktop'),
        'anteprima-scroll-card' => __('Anteprima Scroll Card')
    ));
}

// Dimensioni extra per articoli
function use_new_image_size() {
    if ( function_exists( 'add_image_size' ) ) {
        add_image_size( 'articolo-800px', 800, 0, true );
        add_image_size( 'articolo-600px', 600, 0, true );
        add_image_size( 'articolo-400px', 400, 0, true );
    }
}
add_action( 'after_setup_theme', 'use_new_image_size' );

// Ottimizza tag img con srcset
function modify_image_html( $html, $attachment_id, $size, $icon ) {
    $srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
    if ( $srcset ) {
        if ( false === strpos( $html, ' srcset=' ) ) {
            $html = str_replace( '<img', '<img srcset="' . esc_attr( $srcset ) . '"', $html );
        }

        if ( false === strpos( $html, ' sizes=' ) ) {
            $sizes = wp_get_attachment_image_sizes( $attachment_id, $size );

            if ( $sizes ) {
                $html = str_replace( '<img', '<img sizes="' . esc_attr( $sizes ) . '"', $html );
            }
        }

        $html = str_replace( '/>', '>', $html );
    }
    return $html;
}
add_filter( 'wp_get_attachment_image', 'modify_image_html', 10, 4 );



















/**
 * Arricchisce il nodo Organization generato da Yoast.
 */
add_filter('wpseo_schema_organization', 'chefstudio_extend_yoast_organization_schema', 11, 2);

function chefstudio_extend_yoast_organization_schema($data, $context) {

    $data['@type'] = ['Organization', 'ProfessionalService'];

    $data['name'] = 'Chef Studio';
    $data['legalName'] = 'Chef Studio';
    $data['url'] = home_url('/');

    $data['telephone'] = '+39 352 0996 996';
    $data['email'] = 'info@chefstudio.it';
    $data['priceRange'] = '€€';

    $data['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Via Fiumetto 17',
        'addressLocality' => 'Pietrasanta',
        'addressRegion' => 'LU',
        'postalCode' => '55045',
        'addressCountry' => 'IT',
    ];

    $data['areaServed'] = [
        [
            '@type' => 'Country',
            'name' => 'Italia',
        ],
    ];

    $data['sameAs'] = chefstudio_schema_same_as();

    $data['brand'] = [
        '@id' => chefstudio_schema_brand_id(),
    ];

    $data['knowsAbout'] = chefstudio_schema_organization_knows_about();

    $data['slogan'] = 'Web marketing, comunicazione visiva, SEO e siti web su misura.';

    if (chefstudio_schema_should_include_organization_rating()) {
        $data['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => '5.0',
            'bestRating'  => '5',
            'worstRating' => '1',
            'ratingCount' => 18,
            'reviewCount' => 18,
        ];
    }

    return $data;
}

function chefstudio_schema_home_id($fragment) {
    return home_url('/#' . ltrim((string) $fragment, '#'));
}

function chefstudio_schema_organization_id() {
    return chefstudio_schema_home_id('organization');
}

function chefstudio_schema_brand_id() {
    return chefstudio_schema_home_id('brand');
}

function chefstudio_schema_person_id($person_slug) {
    return chefstudio_schema_home_id(sanitize_title($person_slug));
}

function chefstudio_schema_should_include_organization_rating() {
    if (!is_singular('pagine-locali')) {
        return false;
    }

    $post_id = get_queried_object_id();

    if (!$post_id || !function_exists('chefstudio_schema_pagina_locale_config')) {
        return false;
    }

    $config = chefstudio_schema_pagina_locale_config($post_id);

    return !empty($config['include_rating']);
}

function chefstudio_schema_collect_faq_items($items, $source_id = '') {
    if (empty($items) || !is_array($items)) {
        return;
    }

    global $chefstudio_schema_faq_items;

    if (!is_array($chefstudio_schema_faq_items ?? null)) {
        $chefstudio_schema_faq_items = [];
    }

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $question_raw = $item['question']
            ?? $item['name']
            ?? $item['titolo']
            ?? $item['titolo_faq']
            ?? $item['testo_domanda']
            ?? $item['fld_faq_tit']
            ?? '';

        $answer_raw = $item['answer']
            ?? $item['text']
            ?? $item['risposta']
            ?? $item['testo']
            ?? $item['contenuto_faq']
            ?? $item['testo_risposta']
            ?? $item['fld_faq_txt']
            ?? '';

        $question_text = chefstudio_pulisci_testo_schema($question_raw);
        $answer_text   = chefstudio_pulisci_testo_schema($answer_raw);

        if ($question_text === '' || $answer_text === '') {
            continue;
        }

        $dedupe_key = sanitize_title($question_text);

        if ($dedupe_key === '') {
            $dedupe_key = md5($question_text);
        }

        if (isset($chefstudio_schema_faq_items[$dedupe_key])) {
            continue;
        }

        $chefstudio_schema_faq_items[$dedupe_key] = [
            'question' => $question_text,
            'answer'   => $answer_text,
            'source'   => sanitize_key($source_id),
        ];
    }
}

add_action('wp_footer', 'chefstudio_schema_output_collected_faq_items', 20);

add_filter('csfaq_schema_data', 'chefstudio_extend_csfaq_schema_data', 20, 3);

function chefstudio_extend_csfaq_schema_data($schema, $post_id, $faq_items) {
    global $chefstudio_schema_csfaq_output;

    $chefstudio_schema_csfaq_output = true;
    $url = get_permalink($post_id);

    if (empty($url)) {
        return $schema;
    }

    $url = trailingslashit($url);

    if (is_array($schema)) {
        $schema['@id'] = $url . '#faq';
        $schema['url'] = $url;
        $schema['isPartOf'] = [
            '@id' => chefstudio_schema_home_id('website'),
        ];

        if (!empty($schema['mainEntity']) && is_array($schema['mainEntity'])) {
            $position = 1;

            foreach ($schema['mainEntity'] as $index => $question) {
                if (!is_array($question)) {
                    continue;
                }

                if (empty($question['@id'])) {
                    $schema['mainEntity'][$index]['@id'] = $url . '#faq-question-' . $position;
                }

                $position++;
            }
        }
    }

    return $schema;
}

function chefstudio_schema_output_collected_faq_items() {
    if (is_admin()) {
        return;
    }

    global $chefstudio_schema_faq_items, $chefstudio_schema_csfaq_output;

    if (empty($chefstudio_schema_faq_items) || !is_array($chefstudio_schema_faq_items)) {
        return;
    }

    if (!empty($chefstudio_schema_csfaq_output)) {
        return;
    }

    $url = is_singular() ? get_permalink(get_queried_object_id()) : home_url(add_query_arg([], $GLOBALS['wp']->request ?? ''));
    $url = trailingslashit($url);

    $main_entity = [];
    $position = 1;

    foreach ($chefstudio_schema_faq_items as $faq_item) {
        $main_entity[] = [
            '@type' => 'Question',
            '@id'   => $url . '#faq-question-' . $position,
            'name'  => $faq_item['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq_item['answer'],
            ],
        ];

        $position++;
    }

    if (empty($main_entity)) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        '@id'      => $url . '#faq',
        'url'      => $url,
        'isPartOf' => [
            '@id' => chefstudio_schema_home_id('website'),
        ],
        'mainEntity' => $main_entity,
    ];

    echo "\n<script type=\"application/ld+json\" class=\"chefstudio-faq-schema\">" .
        wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
        "</script>\n";
}

function chefstudio_schema_logo_url() {
    return home_url('/wp-content/uploads/2026/04/chef-studio-seo-logo.jpg');
}

function chefstudio_schema_same_as() {
    return [
        'https://maps.app.goo.gl/b3eS7jqkT7RrQ7Ey8',
        'https://www.instagram.com/chefstudiodesign/',
        'https://www.facebook.com/chefstudiodesign',
    ];
}

function chefstudio_schema_organization_knows_about() {
    return [
        'SEO',
        'Consulenza SEO',
        'Agenzia SEO',
        'Web design',
        'WordPress',
        'Sviluppo siti web',
        'Siti web accessibili',
        'UX/UI design',
        'Comunicazione visiva',
        'SEO per AI',
        'Intelligenza artificiale per il marketing',
        'Graphic design',
        'Brand identity',
        'Fotografia',
        'Marketing digitale',
        'E-commerce',
        'Google Analytics',
        'Google Search Console',
        'Local SEO',
    ];
}

function chefstudio_schema_brand() {
    return [
        '@type'  => 'Brand',
        '@id'    => chefstudio_schema_brand_id(),
        'name'   => 'Chef Studio',
        'url'    => home_url('/'),
        'logo'   => chefstudio_schema_logo_url(),
        'sameAs' => chefstudio_schema_same_as(),
    ];
}

function chefstudio_schema_person_slug_from_name($name) {
    $slug = sanitize_title((string) $name);

    $known_people = [
        'igor-biagi'   => 'igor-biagi',
        'andrea-biagi' => 'andrea-biagi',
    ];

    return $known_people[$slug] ?? '';
}

function chefstudio_schema_person_slug_from_text($text) {
    $text = chefstudio_pulisci_testo_schema($text);
    $person_slug = chefstudio_schema_person_slug_from_name($text);

    if (empty($person_slug) && preg_match('/\bIgor\b/i', $text)) {
        $person_slug = 'igor-biagi';
    }

    if (empty($person_slug) && preg_match('/\bAndrea\b/i', $text)) {
        $person_slug = 'andrea-biagi';
    }

    return $person_slug;
}

function chefstudio_schema_person_slug_from_current_post_author() {
    if (!is_singular()) {
        return '';
    }

    $post_id = get_queried_object_id();
    $author_id = $post_id ? (int) get_post_field('post_author', $post_id) : 0;

    if (!$author_id) {
        return '';
    }

    $author_names = [
        get_the_author_meta('display_name', $author_id),
        trim(get_the_author_meta('first_name', $author_id) . ' ' . get_the_author_meta('last_name', $author_id)),
        get_the_author_meta('nickname', $author_id),
        get_the_author_meta('user_nicename', $author_id),
    ];

    foreach ($author_names as $author_name) {
        $person_slug = chefstudio_schema_person_slug_from_text($author_name);

        if (!empty($person_slug)) {
            return $person_slug;
        }
    }

    return '';
}

function chefstudio_schema_person($person_slug) {
    $person_slug = sanitize_title($person_slug);
    $organization_id = chefstudio_schema_organization_id();

    $people = [
        'igor-biagi' => [
            '@type'    => 'Person',
            '@id'      => chefstudio_schema_person_id('igor-biagi'),
            'name'     => 'Igor Biagi',
            'jobTitle' => [
                'SEO Manager',
                'Communication Designer',
            ],
            'url'      => home_url('/#igor-biagi'),
            'image'    => get_stylesheet_directory_uri() . '/img/igor-biagi-cta.png',
            'worksFor' => [
                '@id' => $organization_id,
            ],
            'knowsAbout' => [
                'SEO',
                'Consulenza SEO',
                'Agenzia SEO',
                'Local SEO',
                'WordPress',
                'Web design',
                'Google Analytics',
                'Google Search Console',
                'SEO per AI',
            ],
        ],
        'andrea-biagi' => [
            '@type'    => 'Person',
            '@id'      => chefstudio_schema_person_id('andrea-biagi'),
            'name'     => 'Andrea Biagi',
            'jobTitle' => [
                'Web Designer',
                'Graphic Designer',
            ],
            'url'      => home_url('/#andrea-biagi'),
            'image'    => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-andrea-biagi-contatti.png',
            'worksFor' => [
                '@id' => $organization_id,
            ],
            'knowsAbout' => [
                'Web design',
                'WordPress',
                'Sviluppo siti web',
                'UX/UI design',
                'Graphic design',
                'Brand identity',
                'E-commerce',
                'Marketing digitale',
            ],
        ],
    ];

    return $people[$person_slug] ?? [];
}

add_filter('wpseo_schema_person', 'chefstudio_extend_yoast_person_schema', 11, 2);

function chefstudio_extend_yoast_person_schema($data, $context) {
    $name = $data['name'] ?? '';
    $person_slug = chefstudio_schema_person_slug_from_text($name);

    if (empty($person_slug)) {
        return $data;
    }

    return array_merge($data, chefstudio_schema_person($person_slug));
}

add_filter('wpseo_schema_article', 'chefstudio_extend_yoast_article_schema', 11, 2);

function chefstudio_extend_yoast_article_schema($data, $context) {
    $person_slug = '';

    if (!empty($data['author']['name'])) {
        $person_slug = chefstudio_schema_person_slug_from_text($data['author']['name']);
    }

    if (empty($person_slug)) {
        $person_slug = chefstudio_schema_person_slug_from_current_post_author();
    }

    if (empty($person_slug) && !empty($data['author']['@id'])) {
        $author_id = (string) $data['author']['@id'];

        if (false !== stripos($author_id, 'igor')) {
            $person_slug = 'igor-biagi';
        } elseif (false !== stripos($author_id, 'andrea')) {
            $person_slug = 'andrea-biagi';
        }
    }

    if (!empty($person_slug)) {
        $data['author'] = [
            '@id' => chefstudio_schema_person_id($person_slug),
        ];
    }

    if (empty($data['publisher'])) {
        $data['publisher'] = [
            '@id' => chefstudio_schema_organization_id(),
        ];
    }

    if (is_singular('post')) {
        $post_id = get_queried_object_id();
        $topic_terms = wp_get_post_terms($post_id, ['category', 'post_tag'], [
            'fields' => 'names',
        ]);

        $topics = [];
        $ignored_topics = [
            'senza categoria',
            'uncategorized',
        ];

        if (!is_wp_error($topic_terms) && !empty($topic_terms)) {
            foreach ($topic_terms as $topic_term) {
                $topic = chefstudio_pulisci_testo_schema($topic_term);
                $topic_key = function_exists('mb_strtolower')
                    ? mb_strtolower($topic, 'UTF-8')
                    : strtolower($topic);

                if ($topic !== '' && !in_array($topic_key, $ignored_topics, true)) {
                    $topics[] = $topic;
                }
            }
        }

        $topics = array_values(array_unique($topics));

        if (!empty($topics)) {
            $data['keywords'] = implode(', ', $topics);

            $data['about'] = array_map(function ($topic) {
                return [
                    '@type' => 'Thing',
                    'name'  => $topic,
                ];
            }, array_slice($topics, 0, 12));
        } else {
            $fallback_topics = chefstudio_schema_article_fallback_topics($post_id);

            if (!empty($fallback_topics)) {
                $data['keywords'] = implode(', ', $fallback_topics);
                $data['about'] = array_map(function ($topic) {
                    return [
                        '@type' => 'Thing',
                        'name'  => $topic,
                    ];
                }, array_slice($fallback_topics, 0, 12));
            } else {
                unset($data['keywords'], $data['about']);
            }
        }
    }

    return $data;
}

function chefstudio_schema_article_fallback_topics($post_id) {
    $slug = get_post_field('post_name', $post_id);

    $fallback_topics = [
        'css' => [
            'CSS',
            'HTML',
            'Sviluppo web',
            'Web design',
            'Frontend development',
        ],
    ];

    return $fallback_topics[$slug] ?? [];
}



















/**
 * Schema Service per le pagine locali.
 *
 * Yoast mantiene WebPage, BreadcrumbList, WebSite e Organization.
 * Qui aggiungiamo solo le entità di servizio collegate allo stesso
 * Organization ID e ai referenti Chef Studio coerenti nel sito.
 */
add_action('wp_head', 'chefstudio_schema_pagine_locali_servizi', 40);

function chefstudio_schema_pagine_locali_servizi() {
    if (!is_singular('pagine-locali')) {
        return;
    }

    $post_id = get_the_ID();

    $url   = get_permalink($post_id);
    $slug  = get_post_field('post_name', $post_id);
    $title = get_the_title($post_id);

    $config = chefstudio_schema_pagina_locale_config($post_id);

    if (empty($config)) {
        return;
    }

    $citta = '';

    if (function_exists('get_field')) {
        $citta = get_field('citta', $post_id);
    }

    if (empty($citta)) {
        $citta = chefstudio_ricava_citta_da_pagina_locale($title, $slug, $config['city_prefixes']);
    }

    $citta = chefstudio_formatta_nome_citta_schema($citta);

    if (!empty($citta)) {
        $nome_servizio = $config['name_prefix'] . ' ' . $citta;
    } else {
        $nome_servizio = $config['name_prefix'];
    }

    $description = chefstudio_schema_pagina_locale_description($post_id, $citta, $config);
    $image       = chefstudio_schema_pagina_locale_image($post_id, $slug, $citta, $config);

    $organization_id = chefstudio_schema_organization_id();
    $brand_id        = chefstudio_schema_brand_id();
    $person_id       = chefstudio_schema_person_id($config['person']);

    $product_id = trailingslashit($url) . '#product';
    $service_id = trailingslashit($url) . '#service';
    $offer_id   = trailingslashit($url) . '#offer';

    $service = [
        '@type'       => 'Service',
        '@id'         => $service_id,
        'name'        => $nome_servizio,
        'serviceType' => $config['service_type'],
        'description' => $description,
        'url'         => $url,
        'image'       => esc_url_raw($image),
        'brand'       => [
            '@id' => $brand_id,
        ],
        'provider'    => [
            [
                '@id' => $organization_id,
            ],
            [
                '@id' => $person_id,
            ],
        ],
        'offers' => [
            '@id' => $offer_id,
        ],
        'mainEntityOfPage' => $url,
    ];

    if (!empty($citta)) {
        $service['areaServed'] = [
            '@type' => 'Place',
            'name'  => $citta,
        ];
    }

    $offer = [
        '@type'         => 'Offer',
        '@id'           => $offer_id,
        'url'           => $url,
        'availability'  => 'https://schema.org/InStock',
        'itemCondition' => 'https://schema.org/NewCondition',
        'seller' => [
            '@id' => $organization_id,
        ],
        'itemOffered' => [
            '@id' => $service_id,
        ],
    ];

    if (!empty($config['price'])) {
        $offer['priceCurrency'] = 'EUR';
        $offer['price'] = $config['price'];
        $offer['priceValidUntil'] = chefstudio_schema_price_valid_until();
    }

    $graph = [
        chefstudio_schema_brand(),
        $service,
        $offer,
        chefstudio_schema_person($config['person']),
    ];

    if (!empty($config['include_product'])) {
        $product = [
            '@type'       => 'Product',
            '@id'         => $product_id,
            'name'        => $nome_servizio,
            'image'       => esc_url_raw($image),
            'description' => $description,
            'sku'         => $slug,
            'category'    => $config['category'],
            'brand'       => [
                '@id' => $brand_id,
            ],
            'isRelatedTo' => [
                '@id' => $service_id,
            ],
            'offers' => [
                '@id' => $offer_id,
            ],
        ];

        if (!empty($config['include_rating'])) {
            $product['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => 5,
                'bestRating'  => 5,
                'worstRating' => 1,
                'ratingCount' => 18,
                'reviewCount' => 18,
            ];
        }

        $service['subjectOf'] = [
            '@id' => $product_id,
        ];

        $graph = [
            chefstudio_schema_brand(),
            $product,
            $service,
            $offer,
            chefstudio_schema_person($config['person']),
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => $graph,
    ];

    echo "\n<script type=\"application/ld+json\">" .
        wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
        "</script>\n";
}

add_action('wp_head', 'chefstudio_schema_pagine_servizio_standard', 42);

function chefstudio_schema_pagine_servizio_standard() {
    if (!is_page()) {
        return;
    }

    $post_id = get_queried_object_id();
    $slug = get_post_field('post_name', $post_id);

    $configs = [
        'agenzia-seo' => [
            'name'        => 'Agenzia SEO',
            'serviceType' => 'Agenzia SEO',
            'category'    => 'SEO',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/consulenza-seo.jpg',
            'description' => 'Servizi SEO strategici per migliorare visibilita, contenuti, struttura tecnica e risultati organici sui motori di ricerca.',
        ],
        'consulente-seo' => [
            'name'        => 'Consulente SEO',
            'serviceType' => 'Consulenza SEO',
            'category'    => 'SEO',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/consulente-seo-pisa-igor-biagi-chef-studio.jpg',
            'description' => 'Consulenza SEO per analizzare siti web, individuare opportunita di crescita e costruire strategie organiche sostenibili.',
        ],
        'consulenza-seo' => [
            'name'        => 'Consulenza SEO',
            'serviceType' => 'Consulenza SEO',
            'category'    => 'SEO',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/consulenza-seo.jpg',
            'description' => 'Consulenza SEO tecnica, strategica e contenutistica per rendere il sito piu chiaro, competitivo e visibile.',
        ],
        'ottimizzazione-siti-web' => [
            'name'        => 'Ottimizzazione siti web',
            'serviceType' => 'Ottimizzazione SEO siti web',
            'category'    => 'SEO',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/ottimizzazione-siti-web.svg',
            'description' => 'Ottimizzazione tecnica, contenutistica e strutturale di siti web per migliorare performance, usabilita e posizionamento.',
        ],
        'posizionamento-siti-su-google' => [
            'name'        => 'Posizionamento siti su Google',
            'serviceType' => 'Posizionamento SEO',
            'category'    => 'SEO',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/seo-pisa.jpg',
            'description' => 'Strategie SEO per migliorare il posizionamento su Google attraverso analisi, contenuti, tecnica e continuita.',
        ],
        'seo-per-ecommerce' => [
            'name'        => 'SEO per e-commerce',
            'serviceType' => 'SEO per e-commerce',
            'category'    => 'SEO e-commerce',
            'person'      => 'igor-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/seo/seo-per-ecommerce.jpg',
            'description' => 'SEO per e-commerce orientata a categorie, schede prodotto, struttura informativa e traffico qualificato.',
        ],
        'web-agency' => [
            'name'        => 'Web Agency',
            'serviceType' => 'Web agency',
            'category'    => 'Web design e marketing digitale',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-consulenza-seo-web-agency-siti.svg',
            'description' => 'Web agency per siti web, identita visiva, SEO, contenuti e strategie digitali su misura.',
        ],
        'realizzazione-siti-web' => [
            'name'        => 'Realizzazione siti web',
            'serviceType' => 'Realizzazione siti web',
            'category'    => 'Sviluppo siti web',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-consulenza-seo-web-agency-siti.svg',
            'description' => 'Realizzazione siti web su misura, responsive e ottimizzati per comunicare meglio e generare contatti.',
        ],
        'realizzazione-siti-wordpress' => [
            'name'        => 'Realizzazione siti WordPress',
            'serviceType' => 'Realizzazione siti WordPress',
            'category'    => 'Sviluppo siti web',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-consulenza-seo-web-agency-siti.svg',
            'description' => 'Realizzazione siti WordPress personalizzati, gestibili e progettati per performance, contenuti e SEO.',
        ],
        'realizzazione-ecommerce' => [
            'name'        => 'Realizzazione E-Commerce',
            'serviceType' => 'Realizzazione e-commerce',
            'category'    => 'Sviluppo e-commerce',
            'person'      => 'andrea-biagi',
            'image'       => home_url('/wp-content/uploads/2025/11/realizzazione-e-commerce.png'),
            'description' => 'Realizzazione ecommerce veloci e sicuri per aumentare le vendite. Soluzioni personalizzate per brand che vogliono crescere online.',
        ],
        'realizzazione-e-commerce' => [
            'name'        => 'Realizzazione E-Commerce',
            'serviceType' => 'Realizzazione e-commerce',
            'category'    => 'Sviluppo e-commerce',
            'person'      => 'andrea-biagi',
            'image'       => home_url('/wp-content/uploads/2025/11/realizzazione-e-commerce.png'),
            'description' => 'Realizzazione ecommerce veloci e sicuri per aumentare le vendite. Soluzioni personalizzate per brand che vogliono crescere online.',
        ],
        'realizzazione-siti-web-aziendali' => [
            'name'        => 'Realizzazione siti web aziendali',
            'serviceType' => 'Realizzazione siti web aziendali',
            'category'    => 'Sviluppo siti web',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-consulenza-seo-web-agency-siti.svg',
            'description' => 'Realizzazione siti web aziendali pensati per presentare servizi, posizionare il brand e generare richieste qualificate.',
        ],
        'realizzazione-siti-web-professionali' => [
            'name'        => 'Realizzazione siti web professionali',
            'serviceType' => 'Realizzazione siti web professionali',
            'category'    => 'Sviluppo siti web',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/realizzazione-siti-web/chef-studio-consulenza-seo-web-agency-siti.svg',
            'description' => 'Realizzazione siti web professionali per freelance, studi e aziende che vogliono comunicare con chiarezza e autorevolezza.',
        ],
        'studio-grafico' => [
            'name'        => 'Studio grafico',
            'serviceType' => 'Graphic design',
            'category'    => 'Grafica e comunicazione visiva',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Studio grafico per identita visiva, impaginati, materiali promozionali e comunicazione coordinata.',
        ],
        'realizzazione-logo' => [
            'name'        => 'Realizzazione logo',
            'serviceType' => 'Logo design',
            'category'    => 'Brand identity',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Realizzazione loghi e identita visive progettate per rendere il brand riconoscibile, coerente e professionale.',
        ],
        'impaginazione-catalogo' => [
            'name'        => 'Impaginazione cataloghi',
            'serviceType' => 'Impaginazione cataloghi',
            'category'    => 'Graphic design',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Impaginazione cataloghi, brochure e materiali editoriali con cura grafica, gerarchia visiva e coerenza di brand.',
        ],
        'realizzazione-brochure' => [
            'name'        => 'Realizzazione brochure',
            'serviceType' => 'Realizzazione brochure',
            'category'    => 'Graphic design',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Realizzazione brochure aziendali e materiali di comunicazione progettati per presentare servizi, prodotti e valori del brand.',
        ],
        'corso-di-grafica' => [
            'name'        => 'Corso di grafica',
            'serviceType' => 'Formazione grafica',
            'category'    => 'Formazione',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Corso di grafica per imparare principi visivi, strumenti e metodo progettuale applicati alla comunicazione.',
        ],
        'fotografia' => [
            'name'        => 'Fotografia',
            'serviceType' => 'Servizi fotografici',
            'category'    => 'Fotografia',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Servizi fotografici per comunicazione, prodotti, ambienti e contenuti visivi professionali.',
        ],
        'fotografia-di-prodotto' => [
            'name'        => 'Fotografia di prodotto',
            'serviceType' => 'Fotografia di prodotto',
            'category'    => 'Fotografia',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Fotografia di prodotto per e-commerce, cataloghi, campagne e contenuti digitali.',
        ],
        'fotografia-di-interni' => [
            'name'        => 'Fotografia di interni',
            'serviceType' => 'Fotografia di interni',
            'category'    => 'Fotografia',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Fotografia di interni per strutture ricettive, aziende, spazi commerciali e progetti di comunicazione.',
        ],
        'google-street-view' => [
            'name'        => 'Google Street View',
            'serviceType' => 'Virtual tour Google Street View',
            'category'    => 'Fotografia e presenza locale',
            'person'      => 'andrea-biagi',
            'image'       => get_stylesheet_directory_uri() . '/img/logo-chef-studio-grafica-design-fotografia-web-comunicazione.png',
            'description' => 'Servizi Google Street View e virtual tour per migliorare presenza locale, fiducia e visibilita online.',
        ],
    ];

    $config = $configs[$slug] ?? chefstudio_schema_pagina_servizio_standard_config_from_ancestors($post_id, $configs);

    if (empty($config)) {
        return;
    }

    $url = get_permalink($post_id);
    $service_id = trailingslashit($url) . '#service';
    $offer_id = trailingslashit($url) . '#offer';
    $organization_id = chefstudio_schema_organization_id();
    $brand_id = chefstudio_schema_brand_id();
    $person_id = chefstudio_schema_person_id($config['person']);

    $description = '';

    if (class_exists('WPSEO_Meta')) {
        $description = WPSEO_Meta::get_value('metadesc', $post_id);
    }

    if (empty($description)) {
        $description = get_the_excerpt($post_id);
    }

    if (empty($description)) {
        $description = $config['description'];
    }

    $image = has_post_thumbnail($post_id)
        ? get_the_post_thumbnail_url($post_id, 'full')
        : $config['image'];

    $service = [
        '@type'            => 'Service',
        '@id'              => $service_id,
        'name'             => $config['name'],
        'serviceType'      => $config['serviceType'],
        'category'         => $config['category'],
        'description'      => chefstudio_pulisci_testo_schema($description),
        'url'              => $url,
        'image'            => esc_url_raw($image),
        'brand'            => [
            '@id' => $brand_id,
        ],
        'provider'         => [
            [
                '@id' => $organization_id,
            ],
            [
                '@id' => $person_id,
            ],
        ],
        'offers'           => [
            '@id' => $offer_id,
        ],
        'mainEntityOfPage' => $url,
        'areaServed'       => [
            '@type' => 'Country',
            'name'  => 'Italia',
        ],
    ];

    if (!empty($config['parentServiceId'])) {
        $service['isRelatedTo'] = [
            '@id' => $config['parentServiceId'],
        ];
    }

    $offer = [
        '@type'         => 'Offer',
        '@id'           => $offer_id,
        'url'           => $url,
        'availability'  => 'https://schema.org/InStock',
        'itemCondition' => 'https://schema.org/NewCondition',
        'seller'        => [
            '@id' => $organization_id,
        ],
        'itemOffered'   => [
            '@id' => $service_id,
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            chefstudio_schema_brand(),
            $service,
            $offer,
            chefstudio_schema_person($config['person']),
        ],
    ];

    echo "\n<script type=\"application/ld+json\">" .
        wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
        "</script>\n";
}

function chefstudio_schema_pagina_servizio_standard_config_from_ancestors($post_id, $configs) {
    $ancestor_ids = get_post_ancestors($post_id);

    if (empty($ancestor_ids)) {
        return [];
    }

    foreach ($ancestor_ids as $ancestor_id) {
        $ancestor_slug = get_post_field('post_name', $ancestor_id);

        if (empty($ancestor_slug) || empty($configs[$ancestor_slug])) {
            continue;
        }

        $parent_config = $configs[$ancestor_slug];
        $page_title = chefstudio_pulisci_testo_schema(get_the_title($post_id));

        if ($page_title === '') {
            $page_title = $parent_config['name'];
        }

        return [
            'name'            => $page_title,
            'serviceType'     => $page_title,
            'category'        => $parent_config['category'] ?? '',
            'person'          => $parent_config['person'] ?? 'andrea-biagi',
            'image'           => $parent_config['image'] ?? chefstudio_schema_logo_url(),
            'description'     => $parent_config['description'] ?? '',
            'parentServiceId' => trailingslashit(get_permalink($ancestor_id)) . '#service',
        ];
    }

    return [];
}

function chefstudio_schema_pagina_locale_config($post_id) {
    $configs = [
        [
            'term'           => 'seo',
            'name_prefix'    => 'Consulenza SEO',
            'service_type'   => 'Consulenza SEO',
            'category'       => 'Consulenza SEO',
            'person'         => 'igor-biagi',
            'price'          => '450',
            // Niente Product e stelle: Google non ammette recensioni autoreferenziali sui servizi.
            'include_product' => false,
            'include_rating'  => false,
            'city_prefixes'  => [
                'SEO',
                'Consulente SEO',
                'Agenzia SEO',
                'Servizi SEO',
            ],
            'image_fields'   => [
                'immagine_main',
            ],
            'image_pattern'  => 'seo-%s.jpg',
            'fallback_description_city' => 'Consulenza SEO a %s per aziende, professionisti ed e-commerce.',
            'fallback_description'      => 'Servizio di consulenza SEO per aziende, professionisti ed e-commerce.',
        ],
        [
            'term'           => 'realizzazione-siti',
            'name_prefix'    => 'Realizzazione siti web',
            'service_type'   => 'Realizzazione siti web',
            'category'       => 'Sviluppo siti web',
            'person'         => 'andrea-biagi',
            'price'          => '',
            'include_product' => false,
            'include_rating'  => false,
            'city_prefixes'  => [
                'Realizzazione siti web',
                'Realizzazione siti internet',
                'Creazione siti web',
                'Sviluppo siti web',
                'Web design',
            ],
            'image_fields'   => [
                'rs_immagine_main',
                'immagine_main',
            ],
            'image_pattern'  => 'realizzazione-siti-web-%s.jpg',
            'fallback_description_city' => 'Realizzazione siti web a %s per aziende e professionisti, con design, sviluppo WordPress, UX e SEO integrati.',
            'fallback_description'      => 'Servizio di realizzazione siti web per aziende e professionisti, con design, sviluppo WordPress, UX e SEO integrati.',
        ],
        [
            'term'           => defined('LPG_WA_TERM_SLUG') ? LPG_WA_TERM_SLUG : 'web-agency',
            'name_prefix'    => 'Web Agency',
            'service_type'   => 'Web agency e marketing digitale',
            'category'       => 'Web agency',
            'person'         => 'andrea-biagi',
            'price'          => '',
            'include_product' => false,
            'include_rating'  => false,
            'city_prefixes'  => [
                'Web Agency',
                'Agenzia web',
                'Web design',
                'Web designer',
            ],
            'image_fields'   => [
                'wa_immagine_main',
                'immagine_main',
            ],
            'image_pattern'  => 'web-agency-%s.jpg',
            'fallback_description_city' => 'Web Agency a %s per siti web, SEO, comunicazione visiva, contenuti e marketing digitale.',
            'fallback_description'      => 'Web agency per siti web, SEO, comunicazione visiva, contenuti e marketing digitale.',
        ],
    ];

    foreach ($configs as $config) {
        if (has_term($config['term'], 'argomento_categoria', $post_id)) {
            return $config;
        }
    }

    return [];
}

function chefstudio_schema_price_valid_until() {
    $timestamp = current_time('timestamp');
    $year = (int) wp_date('Y', $timestamp);
    $valid_until = $year . '-12-31';

    if (strtotime($valid_until . ' 23:59:59') < $timestamp) {
        $valid_until = ($year + 1) . '-12-31';
    }

    return $valid_until;
}

function chefstudio_schema_pagina_locale_description($post_id, $citta, array $config) {
    $description = '';

    if (function_exists('get_field')) {
        $description = get_field('meta_description', $post_id);
    }

    if (empty($description) && class_exists('WPSEO_Meta')) {
        $description = WPSEO_Meta::get_value('metadesc', $post_id);
    }

    if (empty($description)) {
        $description = get_the_excerpt($post_id);
    }

    if (empty($description)) {
        if (!empty($citta)) {
            $description = sprintf($config['fallback_description_city'], $citta);
        } else {
            $description = $config['fallback_description'];
        }
    }

    return chefstudio_pulisci_testo_schema($description);
}

function chefstudio_schema_pagina_locale_image($post_id, $slug, $citta, array $config) {
    $image = '';

    if (has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'full');
    }

    if (empty($image)) {
        $image = chefstudio_schema_image_from_fields($post_id, $config['image_fields']);
    }

    if (empty($image)) {
        $uploads = wp_upload_dir();
        $image_slug = !empty($citta) ? sanitize_title(remove_accents($citta)) : sanitize_title($slug);

        $image = trailingslashit($uploads['baseurl']) . 'seo-img/' . sprintf($config['image_pattern'], $image_slug);
    }

    if (empty($image)) {
        $image = chefstudio_schema_logo_url();
    }

    return esc_url_raw($image);
}

function chefstudio_schema_image_from_fields($post_id, array $field_names) {
    foreach ($field_names as $field_name) {
        $image = '';

        if (function_exists('get_field')) {
            $image = get_field($field_name, $post_id);
        }

        $url = chefstudio_schema_image_url_from_value($image);

        if (!empty($url)) {
            return $url;
        }

        $image = get_post_meta($post_id, $field_name, true);
        $url = chefstudio_schema_image_url_from_value($image);

        if (!empty($url)) {
            return $url;
        }
    }

    return '';
}

function chefstudio_schema_image_url_from_value($image) {
    if (empty($image)) {
        return '';
    }

    if (is_array($image) && !empty($image['url'])) {
        return trim((string) $image['url']);
    }

    if (is_array($image) && !empty($image['ID'])) {
        $url = wp_get_attachment_url((int) $image['ID']);
        return $url ?: '';
    }

    if (is_numeric($image)) {
        $url = wp_get_attachment_url((int) $image);
        return $url ?: '';
    }

    if (is_string($image) && filter_var(trim($image), FILTER_VALIDATE_URL)) {
        return trim($image);
    }

    return '';
}


/**
 * Ricava la città da titolo o slug di una pagina locale.
 */
function chefstudio_ricava_citta_da_pagina_locale($title, $slug, array $prefixes) {
    $title_clean = chefstudio_pulisci_testo_schema($title);

    foreach ($prefixes as $prefix) {
        $prefix = chefstudio_pulisci_testo_schema($prefix);

        if (empty($prefix)) {
            continue;
        }

        $pattern = '/^' . preg_quote($prefix, '/') . '\s+(.+?)(?:\s*[-–|]\s*|$)/iu';

        if (preg_match($pattern, $title_clean, $match)) {
            return chefstudio_formatta_nome_citta_schema($match[1]);
        }
    }

    $slug_clean = sanitize_title($slug);

    foreach ($prefixes as $prefix) {
        $prefix_slug = sanitize_title($prefix);

        if (empty($prefix_slug)) {
            continue;
        }

        $pattern = '/^' . preg_quote($prefix_slug, '/') . '-/';

        if (preg_match($pattern, $slug_clean)) {
            $city_slug = preg_replace($pattern, '', $slug_clean);
            $city_slug = str_replace('-', ' ', $city_slug);

            return chefstudio_formatta_nome_citta_schema($city_slug);
        }
    }

    return '';
}


/**
 * Ricava la città da titolo o slug di una pagina locale SEO.
 *
 * Esempi gestiti:
 * - "SEO Lucca - Consulente SEO Esperto"
 * - "SEO Arezzo"
 * - "Consulente SEO Lucca"
 * - "Agenzia SEO Lucca"
 * - slug: seo-lucca
 * - slug: consulente-seo-lucca
 */
function chefstudio_ricava_citta_da_pagina_locale_seo($title, $slug) {
    return chefstudio_ricava_citta_da_pagina_locale($title, $slug, [
        'SEO',
        'Consulente SEO',
        'Agenzia SEO',
        'Servizi SEO',
    ]);
}


/**
 * Normalizza il nome città per lo Schema.
 */
function chefstudio_formatta_nome_citta_schema($citta) {
    $citta = chefstudio_pulisci_testo_schema($citta);

    if (empty($citta)) {
        return '';
    }

    return mb_convert_case($citta, MB_CASE_TITLE, 'UTF-8');
}


/**
 * Pulisce testi per JSON-LD.
 */
function chefstudio_pulisci_testo_schema($testo) {
    $testo = wp_strip_all_tags((string) $testo);
    $testo = html_entity_decode($testo, ENT_QUOTES, 'UTF-8');
    $testo = preg_replace('/\s+/', ' ', $testo);

    return trim($testo);
}









// =====================================================
// Pagination personalizzata
// =====================================================
function my_pagination() {
    global $wp_query;

    $total_pages = $wp_query->max_num_pages;

    if ($total_pages > 1) {
        $current_page = max(1, get_query_var('paged'));

        $big = 999999999;

        $add_args = array();

        if (isset($_GET['orderby']) && $_GET['orderby'] !== 'default') {
            $add_args['orderby'] = sanitize_text_field(wp_unslash($_GET['orderby']));
        }

        echo '<nav class="page_nav" aria-label="Paginazione">';

        echo paginate_links(array(
            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    => '',
            'current'   => $current_page,
            'total'     => $total_pages,
            'prev_text' => 'Indietro',
            'next_text' => 'Avanti',
            'add_args'  => $add_args,
        ));

        echo '</nav>';
    }
}



// =====================================================
// Menu: registrazione + classi padre
// =====================================================
if ( function_exists( 'register_nav_menus' ) ) {
    register_nav_menus(array(
        'menu-chef'   => 'Menu principale',
        'footer-menu' => 'Menu footer',
    ));
}

function add_menu_parent_class( $items ) {
    $parents = array();
    foreach ( $items as $item ) {
        if ( $item->menu_item_parent ) {
            $parents[] = $item->menu_item_parent;
        }
    }
    foreach ( $items as $item ) {
        if ( in_array( $item->ID, $parents ) ) {
            $item->classes[] = 'has-children';
        }
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'add_menu_parent_class' );

// =====================================================
// ACF Opzioni Tema
// =====================================================

if( function_exists('acf_add_options_page') ) {
acf_add_options_page(array(
'page_title' => 'Impostazioni Tema',
'menu_title'	=> 'Impostazioni Tema',
'menu_slug' => 'impostazioni-del-tema',
'capability'	=> 'edit_posts',
'redirect'	=> false
));
acf_add_options_sub_page(array(
'page_title' => 'Footer',
'menu_title'	=> 'Footer',
'parent_slug'	=> 'impostazioni-del-tema',
));
acf_add_options_sub_page(array(
        'page_title' => 'Info Aziendali',
        'menu_title' => 'Info Aziendali',
        'parent_slug'=> 'impostazioni-del-tema',
    ));
acf_add_options_sub_page(array(
        'page_title' => 'Menu Mobile',
        'menu_title' => 'Menu Mobile',
        'parent_slug'=> 'impostazioni-del-tema',
    ));
}

// =====================================================
// Box autore solo nei post, con foto personalizzata
// =====================================================
function author_info_box() {
    global $post;

    if ( ! is_single() || ! isset( $post->post_type ) || $post->post_type !== 'post' || ! isset( $post->post_author ) ) {
        return;
    }

    $author_id        = $post->post_author;
    $display_name     = get_the_author_meta( 'display_name', $author_id );

    if ( empty( $display_name ) ) {
        $display_name = get_the_author_meta( 'nickname', $author_id );
    }

    $user_description = get_the_author_meta( 'user_description', $author_id );
    $user_website     = get_the_author_meta( 'url', $author_id );
    $custom_photo_url = get_user_meta( $author_id, 'author_photo', true );

    if ( ! empty( $custom_photo_url ) ) {
        $avatar_url = $custom_photo_url;
    } else {
        $avatar_url = get_avatar_url(
            get_the_author_meta( 'user_email', $author_id ),
            array( 'size' => 90 )
        );
    }

    echo '<hr>';
    echo '<footer class="author_bio_section">';

    if ( ! empty( $avatar_url ) ) {
        echo '<div class="author_avatar">';
        echo '<img src="' . esc_url( $avatar_url ) . '" alt="' . esc_attr( $display_name ) . '">';
        echo '</div>';
    }

    if ( ! empty( $user_description ) ) {
        echo '<p class="author_details">' . nl2br( esc_html( $user_description ) ) . '</p>';
    }

    echo '<p class="author_links">Post di ' . esc_html( $display_name );

    if ( ! empty( $user_website ) ) {
        echo ' | <a href="' . esc_url( $user_website ) . '" target="_blank" rel="nofollow noopener">Sito web</a>';
    }

    echo '</p>';
    echo '</footer>';
}






// =====================================================
// Abilita Media Uploader nel profilo utente
// =====================================================
function cs_enqueue_user_media_uploader( $hook ) {
    // Carica solo nella pagina del profilo utente
    if ( in_array( $hook, array( 'profile.php', 'user-edit.php' ) ) ) {
        wp_enqueue_media();
        wp_enqueue_script( 'jquery' );
    }
}
add_action( 'admin_enqueue_scripts', 'cs_enqueue_user_media_uploader' );





// =====================================================
// Campo personalizzato: Foto autore nel profilo utente
// =====================================================
function cs_add_author_photo_field( $user ) {
    ?>
    <h3>Foto autore (per il box nei post)</h3>
    <table class="form-table">
        <tr>
            <th><label for="author_photo">Immagine autore</label></th>
            <td>
                <?php
                $author_photo = get_user_meta( $user->ID, 'author_photo', true );
                ?>
                <input type="hidden" name="author_photo" id="author_photo" value="<?php echo esc_attr( $author_photo ); ?>" />
                <div id="author_photo_preview" style="margin-bottom:10px;">
                    <?php if ( $author_photo ) : ?>
                        <img src="<?php echo esc_url( $author_photo ); ?>" style="max-width:100px;height:auto;">
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="author_photo_upload">Carica immagine</button>
                <button type="button" class="button" id="author_photo_remove" style="display:<?php echo $author_photo ? 'inline-block' : 'none'; ?>">Rimuovi</button>

                <script>
                    jQuery(document).ready(function($){
                        var frame;
                        $('#author_photo_upload').on('click', function(e){
                            e.preventDefault();
                            if (frame) { frame.open(); return; }
                            frame = wp.media({
                                title: 'Seleziona o carica una foto',
                                button: { text: 'Usa questa immagine' },
                                multiple: false
                            });
                            frame.on('select', function(){
                                var attachment = frame.state().get('selection').first().toJSON();
                                $('#author_photo').val(attachment.url);
                                $('#author_photo_preview').html('<img src="'+attachment.url+'" style="max-width:100px;height:auto;">');
                                $('#author_photo_remove').show();
                            });
                            frame.open();
                        });
                        $('#author_photo_remove').on('click', function(){
                            $('#author_photo').val('');
                            $('#author_photo_preview').html('');
                            $(this).hide();
                        });
                    });
                </script>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'cs_add_author_photo_field' );
add_action( 'edit_user_profile', 'cs_add_author_photo_field' );

function cs_save_author_photo_field( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['author_photo'] ) ) {
        update_user_meta( $user_id, 'author_photo', esc_url_raw( $_POST['author_photo'] ) );
    }
}
add_action( 'personal_options_update', 'cs_save_author_photo_field' );
add_action( 'edit_user_profile_update', 'cs_save_author_photo_field' );





// =====================================================
// Link per aprire banner cookie Complianz
// =====================================================

function cmplz_show_banner_on_click() {
	?>
	<style>
        .cmplz-show-banner {
            cursor: pointer;
        }
	</style>
	<script>
        function addEvent(event, selector, callback, context) {
            document.addEventListener(event, e => {
                if ( e.target.closest(selector) ) {
                    callback(e);
                }
            });
        }
        addEvent('click', '.cmplz-show-banner', function(){
            document.querySelectorAll('.cmplz-manage-consent').forEach(obj => {
                obj.click();
            });
        });
	</script>
	<?php
}
add_action( 'wp_footer', 'cmplz_show_banner_on_click' );







// =====================================================
// Crea il CPT Progetti
// =====================================================
function create_progetti_cpt() {
    $labels = array(
        'name'                  => _x('Progetti', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Progetto', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Progetti', 'textdomain'),
        'name_admin_bar'        => __('Progetto', 'textdomain'),
        'archives'              => __('Archivio Progetti', 'textdomain'),
        'attributes'            => __('Attributi del progetto', 'textdomain'),
        'parent_item_colon'     => __('Progetto padre:', 'textdomain'),
        'all_items'             => __('Tutti i progetti', 'textdomain'),
        'add_new_item'          => __('Aggiungi nuovo progetto', 'textdomain'),
        'add_new'               => __('Aggiungi nuovo', 'textdomain'),
        'new_item'              => __('Nuovo progetto', 'textdomain'),
        'edit_item'             => __('Modifica progetto', 'textdomain'),
        'update_item'           => __('Aggiorna progetto', 'textdomain'),
        'view_item'             => __('Visualizza progetto', 'textdomain'),
        'view_items'            => __('Visualizza progetti', 'textdomain'),
        'search_items'          => __('Cerca progetto', 'textdomain'),
        'not_found'             => __('Nessun progetto trovato', 'textdomain'),
        'not_found_in_trash'    => __('Nessun progetto trovato nel cestino', 'textdomain'),
        'featured_image'        => __('Immagine in evidenza', 'textdomain'),
        'set_featured_image'    => __('Imposta immagine in evidenza', 'textdomain'),
        'remove_featured_image' => __('Rimuovi immagine in evidenza', 'textdomain'),
        'use_featured_image'    => __('Usa come immagine in evidenza', 'textdomain'),
        'insert_into_item'      => __('Inserisci nel progetto', 'textdomain'),
        'uploaded_to_this_item' => __('Caricato in questo progetto', 'textdomain'),
        'items_list'            => __('Elenco progetti', 'textdomain'),
        'items_list_navigation' => __('Navigazione elenco progetti', 'textdomain'),
        'filter_items_list'     => __('Filtra elenco progetti', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Progetti', 'textdomain'),
        'description'           => __('Custom post type per i progetti', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array('slug' => 'progetti', 'with_front' => false),
        'show_in_rest'          => true, // Abilita Gutenberg
    );

    register_post_type('progetti', $args);
}
add_action('init', 'create_progetti_cpt');

// Crea la tassonomia personalizzata "Categorie Progetti"
function create_progetti_taxonomy() {
    $labels = array(
        'name'              => _x('Categorie Progetti', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Categoria Progetto', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Cerca categoria', 'textdomain'),
        'all_items'         => __('Tutte le categorie', 'textdomain'),
        'parent_item'       => __('Categoria padre', 'textdomain'),
        'parent_item_colon' => __('Categoria padre:', 'textdomain'),
        'edit_item'         => __('Modifica categoria', 'textdomain'),
        'update_item'       => __('Aggiorna categoria', 'textdomain'),
        'add_new_item'      => __('Aggiungi nuova categoria', 'textdomain'),
        'new_item_name'     => __('Nome nuova categoria', 'textdomain'),
        'menu_name'         => __('Categorie Progetti', 'textdomain'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'categoria-progetto'),
        'show_in_rest'      => true, // visibile in Gutenberg
    );

    register_taxonomy('categorie_progetti', array('progetti'), $args);
}
add_action('init', 'create_progetti_taxonomy');













function crea_cpt_blocchi_riutilizzabili() {
    $labels = array(
        'name'                  => _x('Blocchi', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Blocco', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Blocchi', 'textdomain'),
        'name_admin_bar'        => __('Blocco', 'textdomain'),
        'add_new'               => __('Aggiungi Blocco', 'textdomain'),
		'add_new_item'          => __('Aggiungi nuovo blocco', 'textdomain'),
        'new_item'              => __('Nuovo Blocco', 'textdomain'),
        'edit_item'             => __('Modifica Blocco', 'textdomain'),
        'view_item'             => __('Visualizza Blocco', 'textdomain'),
        'all_items'             => __('Tutti i Blocchi', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Blocchi', 'textdomain'),
        'description'           => __('Blocchi riutilizzabili per pagine', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20, // Cambia posizione nel menu
        'menu_icon'             => 'dashicons-screenoptions',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => true,  // Supporto per Gutenberg
    );

    register_post_type('blocchi', $args);
}
add_action('init', 'crea_cpt_blocchi_riutilizzabili');


















// =====================================================
// Popola il campo dei CPT per farli scegliere da backend come nella struttura delle anteprime
// =====================================================
add_filter('acf/load_field/name=scegli_cpt', function ($field) {
  // Ottieni CPT pubblici (inclusi quelli built-in utili come 'post')
  $post_types = get_post_types(array(
    'public' => true
  ), 'objects');

  // Popola il campo select con nome e label
  $choices = [];

  foreach ($post_types as $post_type) {
    // Escludi CPT indesiderati (opzionale)
    if (in_array($post_type->name, ['attachment', 'revision', 'nav_menu_item', 'acf-field', 'acf-field-group'])) {
      continue;
    }
    $choices[$post_type->name] = $post_type->labels->singular_name;
  }

  $field['choices'] = $choices;

  return $field;
});







// =====================================================
// Popola il campo delle tassonomie per farli scegliere da backend
// =====================================================

add_filter('acf/load_field/name=scegli_tassonomia', function ($field) {
  $taxonomies = get_taxonomies(['public' => true], 'objects');
  $choices = [];
  foreach ($taxonomies as $tax) {
    if (in_array($tax->name, ['post_format', 'nav_menu'])) continue;
    $choices[$tax->name] = $tax->labels->singular_name;
  }
  $field['choices'] = $choices;
  return $field;
});

function estratto_custom($content, $limit = 30) {
  // Rimuove shortcode, HTML, normalizza spazi
  $clean = wp_strip_all_tags(strip_shortcodes($content));
  $clean = html_entity_decode($clean, ENT_QUOTES, 'UTF-8');
  $clean = preg_replace('/\s+/', ' ', $clean); // rimuove spazi doppi/tab/newline
  $clean = trim($clean);

  // Se vuoto, restituisce stringa vuota
  if (empty($clean)) {
    return '';
  }

  $words = explode(' ', $clean);

  if (count($words) > $limit) {
    $words = array_slice($words, 0, $limit);
    return implode(' ', $words) . '...';
  }

  return implode(' ', $words);
}



// =====================================================
// Ottieni dati dei CPT
// =====================================================

function chef_get_card_categoria($post_id) {
  $post_type = get_post_type($post_id);

  if (!$post_type) {
    return null;
  }

  $taxonomies = get_object_taxonomies($post_type, 'objects');

  if (empty($taxonomies)) {
    return null;
  }

  $preferred_taxonomies = ['category', 'product_cat', 'categorie_progetti', 'argomento_categoria'];
  $excluded_taxonomies  = ['post_tag', 'product_tag', 'post_format', 'nav_menu', 'link_category'];

  uasort($taxonomies, function ($a, $b) use ($preferred_taxonomies) {
    $a_preferred = array_search($a->name, $preferred_taxonomies, true);
    $b_preferred = array_search($b->name, $preferred_taxonomies, true);

    $a_rank = ($a_preferred === false) ? 100 : $a_preferred;
    $b_rank = ($b_preferred === false) ? 100 : $b_preferred;

    if ($a_rank !== $b_rank) {
      return $a_rank <=> $b_rank;
    }

    if (!empty($a->hierarchical) !== !empty($b->hierarchical)) {
      return !empty($a->hierarchical) ? -1 : 1;
    }

    return strcmp($a->name, $b->name);
  });

  foreach ($taxonomies as $taxonomy) {
    if (in_array($taxonomy->name, $excluded_taxonomies, true)) {
      continue;
    }

    if (empty($taxonomy->public) && empty($taxonomy->show_ui)) {
      continue;
    }

    $terms = get_the_terms($post_id, $taxonomy->name);

    if (is_wp_error($terms) || empty($terms)) {
      continue;
    }

    $terms = array_values($terms);
    $primary_term_id = (int) get_post_meta($post_id, '_yoast_wpseo_primary_' . $taxonomy->name, true);

    if ($primary_term_id > 0) {
      foreach ($terms as $term) {
        if ((int) $term->term_id === $primary_term_id) {
          $terms = [$term];
          break;
        }
      }
    }

    $term = $terms[0];
    $link = get_term_link($term);

    return [
      'nome'       => $term->name,
      'slug'       => $term->slug,
      'tassonomia' => $taxonomy->name,
      'link'       => is_wp_error($link) ? '' : $link,
    ];
  }

  return null;
}

function get_contenuto_card($post_id) {
  $immagine = has_post_thumbnail($post_id) ? [
    'url' => get_the_post_thumbnail_url($post_id, 'large'),
    'alt' => get_the_title($post_id)
  ] : null;

  $titolo = get_the_title($post_id);

  $contenuto = get_post_field('post_content', $post_id);
  $testo = estratto_custom($contenuto, 30);
  $categoria = chef_get_card_categoria($post_id);

  return compact('immagine', 'titolo', 'testo', 'categoria');
}

function chef_render_anteprima_card($post_id = null, $args = []) {
  $post_id = $post_id ?: get_the_ID();

  if (!$post_id) {
    return;
  }

  $post_type = get_post_type($post_id);

  $args = wp_parse_args($args, [
    'image_size'   => 'large',
    'show_excerpt' => false,
    'excerpt_words' => 24,
    'li_class'     => '',
    'article_class'=> '',
    'title_tag'    => 'h3',
  ]);

  $allowed_title_tags = ['h2', 'h3', 'h4', 'div'];
  $title_tag = in_array($args['title_tag'], $allowed_title_tags, true) ? $args['title_tag'] : 'h3';
  $permalink = get_permalink($post_id);
  $title = get_the_title($post_id);
  $post_slug = get_post_field('post_name', $post_id);
  $li_class = trim('anteprima-card-item ' . sanitize_html_class((string) $args['li_class']));
  $article_class = trim('anteprima-card anteprima-card-' . sanitize_html_class($post_type ?: 'post') . ' ' . sanitize_html_class((string) $args['article_class']));
  ?>

  <li class="<?php echo esc_attr($li_class); ?>" data-post-id="<?php echo esc_attr($post_id); ?>" data-post-slug="<?php echo esc_attr($post_slug); ?>">
    <article class="<?php echo esc_attr($article_class); ?>">
      <a href="<?php echo esc_url($permalink); ?>">
        <?php if (has_post_thumbnail($post_id)) : ?>
          <figure class="contenitore-img">
            <?php echo get_the_post_thumbnail($post_id, $args['image_size'], ['alt' => esc_attr($title)]); ?>
          </figure>
        <?php endif; ?>

        <div class="testo-anteprima">
          <<?php echo esc_attr($title_tag); ?> class="titolo"><?php echo esc_html($title); ?></<?php echo esc_attr($title_tag); ?>>

          <?php if (!empty($args['show_excerpt'])) :
            $excerpt = get_the_excerpt($post_id);
            if ($excerpt === '') {
              $excerpt = get_post_field('post_content', $post_id);
            }
          ?>
            <p class="testo"><?php echo esc_html(wp_trim_words($excerpt, (int) $args['excerpt_words'], '...')); ?></p>
          <?php endif; ?>

          <div class="freccia"></div>
        </div>
      </a>
    </article>
  </li>

  <?php
}

function chef_distinct_blog_grid_query($distinct, $query) {
  if (!is_admin() && $query->get('chef_blog_grid')) {
    return 'DISTINCT';
  }

  return $distinct;
}
add_filter('posts_distinct', 'chef_distinct_blog_grid_query', 10, 2);



// =====================================================
// Disattiva l'immagine -scaled oltre una certa grandezza
// =====================================================
add_filter('big_image_size_threshold', '__return_false');




// =====================================================
// AGGIUNGE CLASSE BODY "cs-parentid-###"
// SU PAGINE PADRI E FIGLIE
// =====================================================
function cs_aggiungi_classe_id_pagina_padre( $classes ) {
    if ( is_page() ) {
        global $post;

        // Usa l'ID del genitore se esiste, altrimenti quello della pagina stessa
        $parent_id = $post->post_parent ? $post->post_parent : $post->ID;

        // Aggiunge la classe personalizzata legata al genitore
        $classes[] = 'cs-parent-id-' . $parent_id;
    }

    return $classes;
}
add_filter( 'body_class', 'cs_aggiungi_classe_id_pagina_padre' );















/**
 * === CPT + Tassonomia: Pagine Locali (SEO + Realizzazione Siti)
 */
add_action('init', function() {

    /**
     * === CPT: Pagine Locali ===
     */
    $labels = [
        'name'               => 'Pagine Locali',
        'singular_name'      => 'Pagina Locale',
        'menu_name'          => 'Pagine Locali',
        'name_admin_bar'     => 'Pagina Locale',
        'add_new'            => 'Aggiungi nuova',
        'add_new_item'       => 'Aggiungi nuova Pagina Locale',
        'new_item'           => 'Nuova Pagina Locale',
        'edit_item'          => 'Modifica Pagina Locale',
        'view_item'          => 'Visualizza Pagina Locale',
        'all_items'          => 'Tutte le Pagine Locali',
        'search_items'       => 'Cerca Pagine Locali',
        'not_found'          => 'Nessuna pagina trovata',
        'not_found_in_trash' => 'Nessuna pagina nel cestino',
    ];

    register_post_type('pagine-locali', [
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-admin-site-alt3',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'rewrite'            => ['slug' => false, 'with_front' => false],
        'has_archive'        => false,
        'publicly_queryable' => true,
        'show_in_rest'       => true, // importante per compatibilità ACF e Gutenberg
    ]);


    /**
     * === Tassonomia: Argomento Categoria (SEO / Realizzazione Siti)
     */
    $labels_tax = [
        'name'              => 'Categorie Pagine Locali',
        'singular_name'     => 'Categoria Pagina Locale',
        'search_items'      => 'Cerca Categorie',
        'all_items'         => 'Tutte le Categorie',
        'edit_item'         => 'Modifica Categoria',
        'update_item'       => 'Aggiorna Categoria',
        'add_new_item'      => 'Aggiungi nuova Categoria',
        'new_item_name'     => 'Nuovo nome Categoria',
        'menu_name'         => 'Categorie Pagine Locali',
    ];

    register_taxonomy('argomento_categoria', ['pagine-locali'], [
        'hierarchical'      => false,
        'labels'            => $labels_tax,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true, //  aggiunto: necessario per ACF 6+
        'query_var'         => true,
        'rewrite'           => false,
    ]);

    //  Crea automaticamente i termini base se non esistono
    if (!term_exists('seo', 'argomento_categoria')) {
        wp_insert_term('SEO', 'argomento_categoria', ['slug' => 'seo']);
    }
    if (!term_exists('realizzazione-siti', 'argomento_categoria')) {
        wp_insert_term('Realizzazione Siti', 'argomento_categoria', ['slug' => 'realizzazione-siti']);
    }

}, 5);










add_action('after_switch_theme', function(){
    flush_rewrite_rules();
});


add_filter('single_template', function($template) {
    global $post;

    if ($post->post_type === 'pagine-locali') {
        $terms = wp_get_post_terms($post->ID, 'argomento_categoria', ['fields' => 'slugs']);

        if (!empty($terms)) {
            if (in_array('realizzazione-siti', $terms)) {
                $tpl = get_stylesheet_directory() . '/pagine/template-pagine-realizzazione-siti.php';
                if (file_exists($tpl)) return $tpl;
            }

            if (in_array('seo', $terms)) {
                $tpl = get_stylesheet_directory() . '/pagine/template-tutto-integrato.php';
                if (file_exists($tpl)) return $tpl;
            }
        }

        // fallback in caso non ci sia termine
        $default_tpl = get_stylesheet_directory() . '/pagine/template-tutto-integrato.php';
        if (file_exists($default_tpl)) return $default_tpl;
    }

    return $template;
});



add_action('acf/save_post', function($post_id) {
    if (get_post_type($post_id) === 'pagine-locali') {
        error_log("🟢 ACF sta tentando di salvare i campi per post_id=$post_id");
    }
}, 5);



add_action('acf/save_post', function($post_id) {
    if (get_post_type($post_id) === 'pagine-locali') {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        $called_from = array_map(fn($t) => ($t['file'] ?? '') . ':' . ($t['line'] ?? ''), $trace);
        error_log("🚨 acf/save_post chiamato per {$post_id}. Stack:\n" . implode("\n", $called_from));
    }
}, 999);



add_filter('update_post_metadata', function($null, $object_id, $meta_key, $meta_value){
    if ($object_id == 2895 && in_array($meta_key, ['h1','h2','meta_title','meta_description'])) {
        error_log("💾 update_post_meta intercettato: {$meta_key} = " . substr(strip_tags($meta_value), 0, 60));
        error_log("Stack: " . print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6), true));
    }
    return $null; // Lascia proseguire normalmente
}, 10, 4);
