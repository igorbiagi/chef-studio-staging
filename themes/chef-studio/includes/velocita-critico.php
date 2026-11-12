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

add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
  if (!chef_critico_attivo() || strpos($html, "rel='stylesheet'") === false && strpos($html, 'rel="stylesheet"') === false) {
    return $html;
  }
  if (strpos($html, '<noscript>') !== false) {
    return $html;
  }
  $async = preg_replace('/rel=([\'"])stylesheet\1/', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $html, 1);
  return $async . '<noscript>' . trim($html) . "</noscript>\n";
}, 30, 4);
