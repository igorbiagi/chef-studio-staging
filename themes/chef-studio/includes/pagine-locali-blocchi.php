<?php
/**
 * Pagine locali (CPT pagine-locali) costruite con il layout flessibile.
 *
 * - Il gruppo ACF "Layout Flessibile" (layout_flessibile_1) vale anche per le pagine locali.
 * - Se una pagina locale ha righe nel layout flessibile, viene mostrata a blocchi
 *   (pagine/articolo-a-blocchi.php). Le altre restano sui template di prima
 *   (realizzazione siti, web agency, tutto integrato), quindi si può passare una città alla volta.
 * - Per tornare alla versione vecchia di una pagina basta svuotare il layout flessibile.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('acf/load_field_group', function ($group) {
    if (empty($group['key']) || $group['key'] !== 'group_5e8e0b4331def') {
        return $group;
    }
    $location = isset($group['location']) && is_array($group['location']) ? $group['location'] : [];
    foreach ($location as $regole) {
        foreach ((array) $regole as $r) {
            if (($r['param'] ?? '') === 'post_type' && ($r['value'] ?? '') === 'pagine-locali') {
                return $group;
            }
        }
    }
    $location[] = [[
        'param'    => 'post_type',
        'operator' => '==',
        'value'    => 'pagine-locali',
    ]];
    $group['location'] = $location;
    return $group;
});

function chefstudio_pagina_locale_a_blocchi($post_id) {
    return get_post_type($post_id) === 'pagine-locali'
        && !empty(get_post_meta($post_id, 'layout_flessibile_1', true));
}

add_filter('single_template', function ($template) {
    $post = get_queried_object();
    if ($post instanceof WP_Post && chefstudio_pagina_locale_a_blocchi($post->ID)) {
        $tpl = get_stylesheet_directory() . '/pagine/articolo-a-blocchi.php';
        if (file_exists($tpl)) {
            return $tpl;
        }
    }
    return $template;
}, 20);
