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

const CHEF_LSC_VERSIONE = '9';

add_action('init', function () {
    if (get_option('chef_lsc_versione') === CHEF_LSC_VERSIONE) {
        return;
    }
    if (!class_exists('\LiteSpeed\Conf')) {
        return;
    }
    $conf = [
        // CSS unito e minimizzato (bloccante: il CSS critico in pagina è stato provato e peggiorava)
        'optm-css_min'          => true,
        'optm-css_comb'         => true,
        'optm-css_comb_ext_inl' => false,
        'optm-css_font_display' => true,  // font-display: swap
        'optm-html_min'         => true,
        'optm-qs_rm'            => true,  // niente ?ver= sugli statici
        'optm-emoji_rm'         => true,
        'optm-js_min'           => true,
        // JS dei moduli (Gravity Forms) caricato al primo gesto del visitatore: il modulo è in fondo
        // alla pagina. Restano subito attivi banner cookie, script del tema, recensioni e jQuery.
        'optm-js_defer'         => 2,
        'optm-js_defer_exc'     => ['cookiebanner', 'complianz', 'cmplz', 'scripts-cs', 'google-reviews', 'jquery.min.js', 'jquery-core', 'dom-ready', 'hooks.min.js', 'i18n.min.js', 'a11y', 'chef-turnstile-pigro', 'cfturnstile'],
    ];
    \LiteSpeed\Conf::cls()->update_confs($conf);
    update_option('chef_lsc_versione', CHEF_LSC_VERSIONE, false);
    do_action('litespeed_purge_all', 'chef: nuove impostazioni');
}, 20);

// Dopo ogni caricamento di file del tema (deploy) la cache si svuota da sola:
// confronta la data di modifica più recente dei file del tema con quella salvata.
add_action('init', function () {
    $dir = get_stylesheet_directory();
    $ultimo = 0;
    foreach (array_merge(glob($dir . '/*.php') ?: [], glob($dir . '/{includes,parti}/*.php', GLOB_BRACE) ?: [], glob($dir . '/css/chef-themes/*.css') ?: [], glob($dir . '/js/*.js') ?: []) as $f) {
        $ultimo = max($ultimo, (int) @filemtime($f));
    }
    if ($ultimo && (int) get_option('chef_lsc_tema') !== $ultimo) {
        update_option('chef_lsc_tema', $ultimo, false);
        do_action('litespeed_purge_all', 'chef: file del tema aggiornati');
    }
}, 21);
