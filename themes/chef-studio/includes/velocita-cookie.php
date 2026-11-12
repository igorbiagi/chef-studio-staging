<?php
/**
 * Velocità: il banner cookie (Complianz) aggiunge il suo CSS con JavaScript mentre la pagina
 * si disegna. Quel foglio arriva con priorità massima e a volte ritarda il primo disegno.
 * Qui lo mettiamo noi nella pagina, già pronto ma non bloccante; Complianz lo riconosce
 * dall'id e non lo aggiunge di nuovo. Il banner resta identico.
 */

if (!defined('ABSPATH')) {
  exit;
}

add_action('wp_head', function () {
  if (is_admin()) {
    return;
  }
  $up = wp_get_upload_dir();
  $file = trailingslashit($up['basedir']) . 'complianz/css/banner-1-optin.css';
  if (!is_readable($file)) {
    return;
  }
  $url = trailingslashit($up['baseurl']) . 'complianz/css/banner-1-optin.css?v=' . (int) filemtime($file);
  echo '<link rel="stylesheet" id="cmplz-banner-1-optin-css" data-no-optimize="1" href="' . esc_url($url) . '" media="print" onload="this.media=\'all\'">' . "\n";
}, 99);
