<?php
/**
 * Plugin Name: Chef Studio - Impostazioni LiteSpeed Cache (staging)
 * Description: Applica una volta le impostazioni di ottimizzazione di LiteSpeed Cache decise per le
 *              misure di velocità. Cambiando CHEF_LSC_VERSIONE si riapplicano. Solo staging.
 */

defined('ABSPATH') || exit;

$chef_lsc_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
if ($chef_lsc_host !== 'staging.chefstudio.it') {
    return;
}

const CHEF_LSC_VERSIONE = '4';

add_action('init', function () {
    if (get_option('chef_lsc_versione') === CHEF_LSC_VERSIONE) {
        return;
    }
    if (!class_exists('\LiteSpeed\Conf')) {
        return;
    }
    $conf = [
        // CSS: lo gestisce il tema (CSS critico nella pagina, fogli non bloccanti).
        // Se LiteSpeed unisce i CSS rimette tutto in un file che blocca la pagina.
        'optm-css_min'          => false,
        'optm-css_comb'         => false,
        'optm-css_font_display' => true,  // font-display: swap
        'optm-html_min'         => true,
        'optm-qs_rm'            => true,  // niente ?ver= sugli statici
        'optm-emoji_rm'         => true,
        'optm-js_min'           => true,
    ];
    \LiteSpeed\Conf::cls()->update_confs($conf);
    update_option('chef_lsc_versione', CHEF_LSC_VERSIONE, false);
    do_action('litespeed_purge_all', 'chef: nuove impostazioni');
}, 20);
