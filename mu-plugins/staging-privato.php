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

// Tutto cio' che WordPress serve prima di template_redirect (sitemap e XSL di Yoast, ecc.):
// solo per utenti collegati. Restano liberi robots.txt (deve dire Disallow a tutti) e l'API REST
// (che ha gia' il suo 401 qui sopra).
add_action('parse_request', function ($wp) {
    if (is_user_logged_in()) {
        return;
    }
    $qv = $wp->query_vars;
    if (!empty($qv['rest_route']) || !empty($qv['robots']) || !empty($qv['favicon'])) {
        return;
    }
    auth_redirect();
}, 0);

// Login: dopo 5 tentativi sbagliati dallo stesso IP in 15 minuti, blocco per 15 minuti.
// (Lo staging e' senza proxy Cloudflare, quindi senza il WAF che protegge wp-login sul sito vero.)
function chef_staging_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? preg_replace('/[^0-9a-f:.]/i', '', $_SERVER['REMOTE_ADDR']) : '';
}
add_action('wp_login_failed', function () {
    $k = 'chef_lf_' . md5(chef_staging_ip());
    $n = (int) get_transient($k);
    set_transient($k, $n + 1, 15 * MINUTE_IN_SECONDS);
});
add_filter('authenticate', function ($user) {
    if ((int) get_transient('chef_lf_' . md5(chef_staging_ip())) >= 5) {
        return new WP_Error('chef_bloccato', 'Troppi tentativi. Riprova tra 15 minuti.');
    }
    return $user;
}, 99);
