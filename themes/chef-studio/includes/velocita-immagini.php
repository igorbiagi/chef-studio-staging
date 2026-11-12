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
    // Icone SVG: pesano pochi byte e alcune stanno in elementi fissi (pulsante WhatsApp),
    // dove il caricamento pigro le farebbe comparire in ritardo.
    if (preg_match('/(no-lazy|skip-lazy)/i', $tag) || preg_match('/\ssrc=["\'][^"\']+\.svg/i', $tag)) {
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

/**
 * Immagine delle anteprime (schede) con le misure ridotte di WordPress (srcset):
 * prima usciva il file originale, anche 400 KB per una scheda larga 400 px.
 * Niente width/height: la scheda decide le proporzioni con il CSS, come prima.
 */
function chef_img_scheda($img, $sizes = '(max-width: 700px) 92vw, (max-width: 1200px) 46vw, 560px') {
  if (!is_array($img) || empty($img['url'])) {
    return '';
  }
  $id = (int) ($img['ID'] ?? ($img['id'] ?? 0));
  $src = $img['url'];
  $srcset = '';
  if ($id && wp_attachment_is_image($id) && !preg_match('/\.svg$/i', $src)) {
    $grande = wp_get_attachment_image_src($id, 'large');
    if ($grande) {
      $src = $grande[0];
    }
    $srcset = (string) wp_get_attachment_image_srcset($id, 'large');
  }
  $html = '<img src="' . esc_url($src) . '"';
  if ($srcset !== '') {
    $html .= ' srcset="' . esc_attr($srcset) . '" sizes="' . esc_attr($sizes) . '"';
  }
  return $html . ' alt="' . esc_attr($img['alt'] ?? '') . '">';
}
