<?php
/**
 * Plugin Name: Staging privato
 * Description: Solo per staging.chefstudio.it. Mostra il sito solo agli utenti collegati
 *              o a chi usa una password per le applicazioni. Non va mai caricato sul sito vero.
 */

defined('ABSPATH') || exit;

// Sicurezza: se per errore finisce su un altro dominio, non fa nulla.
$staging_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
if ($staging_host !== 'staging.chefstudio.it') {
    return;
}

// Riattiva le password per le applicazioni (chef-security.php le spegne).
add_filter('wp_is_application_passwords_available', '__return_true', 99);

// Accetta la password per le applicazioni anche sulle pagine del sito, non solo sull'API,
// cosi' posso vedere e misurare le pagine come un visitatore.
add_filter('application_password_is_api_request', function ($is_api) {
    return $is_api || !empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['HTTP_AUTHORIZATION']) || !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
});

// Mai indicizzare lo staging.
add_filter('wp_robots', function ($robots) {
    $robots['noindex'] = true;
    $robots['nofollow'] = true;
    return $robots;
}, 99);
add_action('send_headers', function () {
    header('X-Robots-Tag: noindex, nofollow', true);
    header('Cache-Control: private, no-store', true);
});

// Pagine del sito: solo per utenti collegati.
add_action('template_redirect', function () {
    if (!is_user_logged_in()) {
        auth_redirect();
    }
}, 0);

// API REST: solo per utenti autenticati.
add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
        return $result;
    }
    if (!is_user_logged_in()) {
        return new WP_Error('staging_privato', 'Staging privato.', ['status' => 401]);
    }
    return $result;
}, 99);

// Niente feed pubblici.
add_action('do_feed', function () {
    if (!is_user_logged_in()) {
        wp_die('Staging privato.', '', ['response' => 401]);
    }
}, 0);
