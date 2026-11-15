<?php
/**
 * Plugin Name: Chef Studio - Tracciamento richieste (GA4)
 * Description: Segnali di consenso per Google, eventi GA4 sui contatti (telefono, WhatsApp, email) e sull'invio dei moduli.
 *
 * Analytics 4 resta caricato da Complianz, solo dopo il consenso statistiche: qui non si carica nessun tag.
 * Questo file aggiunge solo istruzioni a dataLayer: se il visitatore non ha dato il consenso,
 * Analytics non c'è e niente viene inviato.
 *
 * 1. Consenso: Analytics parte solo dopo il consenso statistiche, quindi analytics_storage è "granted".
 *    I dati pubblicitari (Google Signals, remarketing) restano "denied" finché non c'è il consenso marketing.
 *    Senza queste righe Google li considera concessi anche a chi ha accettato solo le statistiche.
 * 2. Clic su telefono, WhatsApp ed email in tutto il sito: eventi clic_telefono, clic_whatsapp, clic_email,
 *    con il parametro "posizione" (menu, footer, pulsante WhatsApp fisso, contenuto).
 *    Sulle landing Ads i link con le classi lbh-track-* li traccia già il tema: qui vengono saltati.
 * 3. Pagina "Richiesta inviata" (6576): evento generate_lead con "origine" (sito, bonifica, care)
 *    e "pagina_modulo" (la pagina da cui è partito il modulo). Una volta sola per sessione e per pagina di partenza,
 *    così un aggiornamento della pagina non conta una richiesta in più.
 */

if (!defined('ABSPATH')) {
    exit;
}

function chef_tracciamento_attivo() {
    return !is_admin() && !is_feed() && !wp_doing_ajax() && !(defined('REST_REQUEST') && REST_REQUEST);
}

// Prima di tutto il resto: i segnali di consenso devono essere in dataLayer prima della configurazione di Analytics.
add_action('wp_head', function () {
    if (!chef_tracciamento_attivo()) {
        return;
    }
    ?>
<script id="chef-consenso-google">
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function () { dataLayer.push(arguments); };
(function () {
  function marketing() {
    return /(?:^|;\s*)cmplz_marketing=allow(?:;|$)/.test(document.cookie);
  }
  var ads = marketing() ? 'granted' : 'denied';
  gtag('consent', 'default', { analytics_storage: 'granted', ad_storage: ads, ad_user_data: ads, ad_personalization: ads });
  document.addEventListener('cmplz_fire_categories', function (e) {
    var cat = (e && e.detail && e.detail.categories) || [];
    if (cat.indexOf('marketing') !== -1 || marketing()) {
      gtag('consent', 'update', { ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });
    }
  });
})();
</script>
    <?php
}, 1);

add_action('wp_footer', function () {
    if (!chef_tracciamento_attivo()) {
        return;
    }
    $thank_you = is_page(6576);
    ?>
<script id="chef-tracciamento">
(function () {
  window.dataLayer = window.dataLayer || [];
  window.gtag = window.gtag || function () { dataLayer.push(arguments); };

  function posizione(a) {
    if (a.closest('.chat-wa, .blob-wa')) return 'pulsante_fisso';
    if (a.closest('footer, .footer, #footer')) return 'footer';
    if (a.closest('nav, header, .menu-header, #menu-visibility')) return 'menu';
    if (a.closest('form, .cta-form-card, .local-seo-form-card')) return 'box_contatti';
    return 'contenuto';
  }

  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest && e.target.closest('a[href]');
    if (!a || a.closest('.lbh-track-wa, .lbh-track-tel')) return;
    var href = (a.getAttribute('href') || '').toLowerCase();
    var evento = null;
    if (href.indexOf('tel:') === 0) evento = 'clic_telefono';
    else if (href.indexOf('mailto:') === 0) evento = 'clic_email';
    else if (/(^|\/\/)(wa\.me|api\.whatsapp\.com|web\.whatsapp\.com)/.test(href)) evento = 'clic_whatsapp';
    if (!evento) return;
    gtag('event', evento, { posizione: posizione(a), pagina: location.pathname });
  }, true);
<?php if ($thank_you) : ?>

  // Richiesta inviata: generate_lead, una volta sola, dopo che Analytics è configurato (consenso statistiche).
  var origine = new URLSearchParams(location.search).get('origine') || 'sito';
  var paginaModulo = '(sconosciuta)';
  try {
    var r = new URL(document.referrer);
    if (r.host === location.host) paginaModulo = r.pathname;
  } catch (err) {}
  var chiave = 'chef_lead_' + origine + '_' + paginaModulo;
  try { if (sessionStorage.getItem(chiave)) return; } catch (err) {}

  function analyticsPronto() {
    for (var i = 0; i < dataLayer.length; i++) {
      var x = dataLayer[i];
      if (x && x[0] === 'config' && typeof x[1] === 'string' && x[1].indexOf('G-') === 0) return true;
    }
    return false;
  }
  var tentativi = 0;
  var timer = setInterval(function () {
    tentativi++;
    if (analyticsPronto()) {
      clearInterval(timer);
      gtag('event', 'generate_lead', { origine: origine, pagina_modulo: paginaModulo });
      try { sessionStorage.setItem(chiave, '1'); } catch (err) {}
    } else if (tentativi > 600) {
      clearInterval(timer); // 5 minuti senza consenso statistiche: niente evento
    }
  }, 500);
<?php endif; ?>
})();
</script>
    <?php
}, 99);
