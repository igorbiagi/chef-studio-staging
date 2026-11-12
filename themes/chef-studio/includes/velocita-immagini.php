<?php
/**
 * Velocità: le immagini sotto la prima schermata si caricano solo quando servono.
 * Molte immagini del tema (anteprime, recensioni, icone) escono senza loading="lazy"
 * e all'apertura della pagina si contendono la banda con il CSS e l'immagine principale.
 *
 * Regola prudente: si toccano solo le immagini che vengono DOPO l'immagine principale
 * (quella con fetchpriority="high" nella hero). Se la pagina non ne ha una, non si tocca nulla.
 * Le immagini con classe no-lazy / skip-lazy restano come sono.
 */

if (!defined('ABSPATH')) {
  exit;
}

function chef_immagini_pigre($html) {
  if (!is_string($html) || $html === '' || stripos($html, '<img') === false) {
    return $html;
  }
  if (!preg_match('/<img[^>]*fetchpriority=["\']?high[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
    return $html;
  }
  $inizio = $m[0][1] + strlen($m[0][0]);
  $prima = substr($html, 0, $inizio);
  $dopo = preg_replace_callback('/<img\b[^>]*>/i', function ($t) {
    $tag = $t[0];
    if (preg_match('/(no-lazy|skip-lazy)/i', $tag)) {
      return $tag;
    }
    // Un'immagine pigra non deve chiedere priorità alta (WordPress a volte mette entrambe).
    if (preg_match('/\sloading=["\']?lazy/i', $tag)) {
      return preg_replace('/\sfetchpriority=["\']?high["\']?/i', '', $tag);
    }
    if (preg_match('/\sloading=/i', $tag)) {
      return $tag;
    }
    $agg = ' loading="lazy"';
    if (!preg_match('/\sdecoding=/i', $tag)) {
      $agg .= ' decoding="async"';
    }
    return preg_replace('/^<img\b/i', '<img' . $agg, $tag);
  }, substr($html, $inizio));
  return $prima . $dopo;
}

add_action('template_redirect', function () {
  if (is_admin() || wp_doing_ajax() || is_feed() || (defined('REST_REQUEST') && REST_REQUEST)) {
    return;
  }
  ob_start('chef_immagini_pigre');
}, 99);
