<?php
/**
 * Velocità delle pagine servizi: CSS della prima schermata scritto nella pagina,
 * tutti gli altri fogli di stile caricati senza bloccare la visualizzazione.
 * Vale solo per le pagine elencate qui sotto; il resto del sito resta com'è.
 * Il CSS critico si rigenera dalle pagine (script in project-files, revisione-pagine).
 */

if (!defined('ABSPATH')) {
  exit;
}

function chef_critico_pagine() {
  // SPENTO il 12/11/2026: misurato su Web agency mobile, peggiorava (82-87 contro 91),
  // perché con il CSS unito asincrono il browser ricalcola lo stile due volte.
  // Per riaccenderlo togliere la riga qui sotto.
  return [];
  // web agency, realizzazione siti web, aziendali, professionali, wordpress, e-commerce, restyling, quanto costa
  return [2697, 2801, 2865, 2850, 2783, 2700, 90052, 2290];
}

function chef_critico_attivo() {
  static $attivo = null;
  if ($attivo !== null) {
    return $attivo;
  }
  $file = get_stylesheet_directory() . '/css/critical/servizi-critico.css';
  $attivo = !is_admin() && !is_user_logged_in() && is_singular()
    && in_array((int) get_queried_object_id(), chef_critico_pagine(), true)
    && is_readable($file);
  return $attivo;
}

add_action('wp_head', function () {
  if (!chef_critico_attivo()) {
    return;
  }
  $css = (string) file_get_contents(get_stylesheet_directory() . '/css/critical/servizi-critico.css');
  echo '<style id="chef-critico">' . str_ireplace('</style', '<\/style', $css) . "</style>\n";
}, 1);

// I fogli li unisce LiteSpeed Cache in un solo file: qui lo rendiamo non bloccante,
// perché la prima schermata è già disegnata dal CSS critico qui sopra.
// (Un solo file asincrono invece di tanti: meno ricalcoli dello stile mentre arrivano.)
add_filter('litespeed_buffer_after', function ($html) {
  if (!chef_critico_attivo() || strpos($html, 'chef-critico') === false) {
    return $html;
  }
  return preg_replace_callback('#<link data-optimized="?2"? rel="?stylesheet"? href="?([^" >]+/litespeed/css/[^" >]+)"?\s*/?>#', function ($m) {
    $href = $m[1];
    return '<link rel="preload" as="style" href="' . $href . '" onload="this.onload=null;this.rel=\'stylesheet\'"><noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
  }, $html);
}, 50);
