<?php

defined('ABSPATH') || exit;

/**
 * Application Passwords: consentite solo all'utente "Claude" (modifiche ai contenuti via REST), spente per tutti gli altri.
 */
add_filter('wp_is_application_passwords_available_for_user', function ($ok, $user) {
    return $ok && $user instanceof WP_User && $user->user_login === 'Claude';
}, 10, 2);

/**
 * Disabilita le funzioni XML-RPC autenticate.
 * L'endpoint verrà bloccato anche a livello server e Cloudflare.
 */
add_filter('xmlrpc_enabled', '__return_false');