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

// Misure di velocità (approvato da Igor il 12/11/2026): chi manda l'intestazione
// X-Chef-Misura con il codice giusto vede le pagine come un visitatore, senza barra
// di amministrazione. Nel repository c'è solo l'impronta del codice, non il codice.
// Vale solo per leggere le pagine: API REST e pannello restano chiusi.
function chef_staging_misura() {
    $codice = isset($_SERVER['HTTP_X_CHEF_MISURA']) ? (string) $_SERVER['HTTP_X_CHEF_MISURA'] : '';
    return $codice !== '' && hash_equals('98503b55ab3850f3933b8c75916be52fa2c294286a2df699a72def1a5a2279f4', hash('sha256', $codice));
}

// Pagine del sito: solo per utenti collegati (o per le misure).
add_action('template_redirect', function () {
    if (is_user_logged_in()) {
        return;
    }
    if (chef_staging_misura()) {
        // Mai cache pubblica: la pagina misurata non deve finire a chi non ha il codice.
        // La cache privata di LiteSpeed resta legata a chi ha fatto la richiesta.
        do_action('litespeed_control_set_private', 'staging privato: misura');
        return;
    }
    auth_redirect();
}, 0);

// Rete di sicurezza: un visitatore senza codice non riceve mai una pagina dalla cache pubblica.
add_action('send_headers', function () {
    if (!is_user_logged_in() && !chef_staging_misura()) {
        do_action('litespeed_control_set_nocache', 'staging privato');
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
