<?php
/**
 * Plugin Name: Chef Studio - Impostazioni per ambiente
 * Description: Rende il push dello staging sul sito vero (Softaculous) sicuro senza passaggi a mano.
 *              Sul sito vero forza "visibile ai motori di ricerca" e spegne i plugin che servono solo
 *              allo staging, anche se il database copiato dallo staging dice il contrario.
 *              Sullo staging non cambia nulla (lì il noindex e l'accesso privato li gestisce staging-privato.php).
 * Version: 1.0 (14/11/2026)
 */

if (!defined('ABSPATH')) {
    exit;
}

$chef_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
// Da riga di comando o cron senza host: si decide dall'indirizzo del sito salvato nel database.
if ($chef_host === '' && defined('WP_HOME')) {
    $chef_host = strtolower((string) parse_url(WP_HOME, PHP_URL_HOST));
}
$chef_e_live = in_array($chef_host, array('www.chefstudio.it', 'chefstudio.it'), true);

if (!$chef_e_live) {
    return;
}

// 1. Il sito vero è sempre indicizzabile, anche se il database arriva dallo staging (blog_public = 0).
add_filter('pre_option_blog_public', function () {
    return '1';
}, 99);

// 2. Plugin che servono solo a lavorare sullo staging: sul sito vero restano spenti.
add_filter('option_active_plugins', function ($plugins) {
    if (!is_array($plugins)) {
        return $plugins;
    }
    $solo_staging = array(
        'query-monitor/query-monitor.php',
        'what-the-file/what-the-file.php',
        'wordpress-importer/wordpress-importer.php',
    );
    return array_values(array_diff($plugins, $solo_staging));
}, 99);
