<?php
/**
 * Velocità: il captcha di Cloudflare (Turnstile, plugin Simple Cloudflare Turnstile)
 * pesa circa 1,5 MB e lavora subito all'apertura di ogni pagina con un modulo.
 * Qui lo carichiamo solo quando il visitatore tocca un modulo: il plugin ha già una coda
 * (cfturnstileQueue) che disegna i widget quando lo script arriva, quindi il modulo
 * funziona come prima.
 */

if (!defined('ABSPATH')) {
  exit;
}

add_filter('script_loader_tag', function ($tag, $handle) {
  if ($handle !== 'cfturnstile' || is_admin()) {
    return $tag;
  }
  // Lo script resta nella pagina ma senza src: lo attiva il caricatore qui sotto.
  $tag = preg_replace('/\sdefer(=("|\')defer("|\'))?/', '', $tag);
  return str_replace(' src=', ' data-chef-src=', $tag);
}, 10, 2);

add_action('wp_footer', function () {
  if (is_admin()) {
    return;
  }
  ?>
<script id="chef-turnstile-pigro">
(function () {
  var fatto = false;
  function carica() {
    if (fatto) return;
    var s = document.getElementById('cfturnstile-js');
    if (!s || !s.getAttribute('data-chef-src')) return;
    fatto = true;
    var n = document.createElement('script');
    n.src = s.getAttribute('data-chef-src');
    n.async = true;
    document.head.appendChild(n);
  }
  function dentroModulo(e) {
    var t = e.target;
    return t && t.closest && t.closest('form') && document.querySelector('.cf-turnstile');
  }
  ['focusin', 'pointerdown', 'touchstart', 'keydown'].forEach(function (ev) {
    document.addEventListener(ev, function (e) { if (dentroModulo(e)) carica(); }, { passive: true, capture: true });
  });
})();
</script>
  <?php
}, 99);
