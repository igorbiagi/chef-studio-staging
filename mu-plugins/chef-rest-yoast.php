<?php
/**
 * Plugin Name: Chef Studio - Yoast via API REST (solo staging)
 * Description: Permette agli amministratori di leggere e scrivere via API REST il title SEO, la meta description e il noindex di Yoast su pagine, articoli e progetti. Serve per la revisione dei testi sullo staging.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    $campi = [
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_meta-robots-noindex',
    ];
    foreach (['page', 'post', 'progetti', 'pagine-locali'] as $tipo) {
        foreach ($campi as $chiave) {
            register_post_meta($tipo, $chiave, [
                'type'          => 'string',
                'single'        => true,
                'show_in_rest'  => true,
                'auth_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]);
        }
    }
});
