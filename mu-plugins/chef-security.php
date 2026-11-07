<?php

defined('ABSPATH') || exit;

/**
 * Disabilita le Application Passwords di WordPress.
 */
add_filter('wp_is_application_passwords_available', '__return_false');

/**
 * Disabilita le funzioni XML-RPC autenticate.
 * L'endpoint verrà bloccato anche a livello server e Cloudflare.
 */
add_filter('xmlrpc_enabled', '__return_false');