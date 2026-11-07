<?php
/**
 * Google Ads sulle landing Bonifica e Care e sulla thank you page.
 *
 * Analytics 4 NON è qui: lo gestisce Complianz, che lo carica solo dopo il consenso statistiche.
 * Il tag Ads parte solo dopo il consenso marketing dato nel banner di Complianz.
 * Se Complianz non c'è, il tag non parte: meglio perdere un dato che mettere cookie senza consenso.
 * Non parte per gli utenti collegati a WordPress, per non sporcare i dati.
 *
 * Le vecchie landing Paid (SEO, siti, e-commerce) erano tracciate dal mu-plugin chef-google-ads.php,
 * che caricava il tag senza consenso. Qui sotto i suoi hook vengono staccati e quelle pagine
 * passano per lo stesso controllo del consenso. Il mu-plugin si può anche cancellare: non cambia niente.
 *
 * Nota: non rinominare questo file. Il deploy non cancella i file dal server, quindi
 * un file rinominato resterebbe anche col vecchio nome e il sito andrebbe in errore.
 *
 * Per attivare una conversione basta incollare la sua etichetta qui sotto
 * (Google Ads > Obiettivi > Conversioni > azione > Configura il tag > la parte dopo "AW-1008528616/").
 * Se un'etichetta è vuota, quella conversione non parte.
 */

if (!defined('ABSPATH')) {
    exit;
}

function chefstudio_ads_config() {
    return [
        'id'             => 'AW-1008528616',
        'thank_you_page' => 6576,
        'etichette'      => [
            'modulo_bonifica' => 'g7IwCP7OiLAdEOjZ8-AD',
            'modulo_care'     => '',
            'clic_whatsapp'   => 'icmJCOX6iLAdEOjZ8-AD',
            'clic_telefono'   => 'bu1RCO6SibAdEOjZ8-AD',
        ],
        // Vecchie landing Paid: pagina => etichetta della conversione da inviare al caricamento
        // (vuota = solo tag, nessuna conversione).
        'vecchie_landing' => [
            'landing-seo-01'               => '',
            'thank-you-landing-seo'        => 'wQJaCOjoqukcEOjZ8-AD',
            'landing-siti-01'              => '',
            'thank-you-landing-siti'       => 'IzdqCL6UoukcEOjZ8-AD',
            'landing-e-commerce-01'        => '',
            'thank-you-landing-e-commerce' => 'fUCLCIquoukcEOjZ8-AD',
        ],
    ];
}

// Stacca i tag del mu-plugin, che partivano senza consenso (se il mu-plugin non c'è, non fa niente).
add_action('init', function () {
    foreach ([
        'chefstudio_google_ads_base_tag'            => 20,
        'chefstudio_google_ads_conversion_seo'       => 30,
        'chefstudio_google_ads_conversion_siti'      => 30,
        'chefstudio_google_ads_conversion_ecommerce' => 30,
    ] as $funzione => $priorita) {
        remove_action('wp_head', $funzione, $priorita);
    }
});

// Pagine dove serve Google Ads: le landing Bonifica e Care, la thank you page e le vecchie landing.
function chefstudio_ads_pagina_attiva() {
    if (!is_page()) {
        return false;
    }
    $cfg = chefstudio_ads_config();
    return is_page_template(['pagine/landing-bonifica.php', 'pagine/landing-care.php'])
        || is_page($cfg['thank_you_page'])
        || is_page(array_keys($cfg['vecchie_landing']));
}

add_action('wp_head', function () {
    if (is_admin() || is_user_logged_in() || is_feed() || is_preview() || !chefstudio_ads_pagina_attiva()) {
        return;
    }
    $cfg     = chefstudio_ads_config();
    $send_to = [];
    foreach ($cfg['etichette'] as $nome => $etichetta) {
        $etichetta = trim($etichetta);
        if ($etichetta !== '') {
            $send_to[$nome] = $cfg['id'] . '/' . $etichetta;
        }
    }
    $src = 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode($cfg['id']);
    ?>
<!-- Google Ads: parte solo dopo il consenso marketing di Complianz -->
<script>
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function () { dataLayer.push(arguments); };
window.chefstudioAds = <?php echo wp_json_encode((object) $send_to); ?>;
(function () {
  var caricato = false;
  function carica() {
    if (caricato) return;
    caricato = true;
    // Segnali Consent Mode v2 (modalità base): il tag parte solo dopo il consenso marketing,
    // quindi qui lo comunichiamo a Google come concesso. Analytics resta governato da Complianz.
    gtag('consent', 'default', { ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
    gtag('consent', 'update', { ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });
    gtag('js', new Date());
    gtag('config', <?php echo wp_json_encode($cfg['id']); ?>);
    var s = document.createElement('script');
    s.async = true;
    s.src = <?php echo wp_json_encode($src); ?>;
    document.head.appendChild(s);
  }
  function consenso() {
    return typeof window.cmplz_has_consent === 'function' && window.cmplz_has_consent('marketing');
  }
  // Consenso già dato in una visita precedente.
  if (consenso()) carica();
  else {
    document.addEventListener('DOMContentLoaded', function () { if (consenso()) carica(); });
    window.addEventListener('load', function () { if (consenso()) carica(); });
  }
  // Consenso dato adesso, dal banner.
  document.addEventListener('cmplz_fire_categories', function (e) {
    var cat = e && e.detail && e.detail.categories;
    if ((cat && cat.indexOf('marketing') !== -1) || consenso()) carica();
  });
})();
</script>
    <?php
    if (is_page($cfg['thank_you_page'])) {
        // La thank you page è condivisa con gli altri moduli: la conversione parte
        // solo quando l'indirizzo contiene ?origine=bonifica o ?origine=care.
        // Il controllo è in JavaScript così la cache delle pagine non lo falsa.
        // Se il consenso non c'è, l'evento resta in coda e non viene inviato.
        ?>
<script>
(function () {
  var origine = new URLSearchParams(location.search).get('origine');
  var conv = origine && window.chefstudioAds['modulo_' + origine];
  if (conv) gtag('event', 'conversion', { send_to: conv, value: 1.0, currency: 'EUR' });
})();
</script>
        <?php
    }
    foreach ($cfg['vecchie_landing'] as $pagina => $etichetta) {
        if ($etichetta !== '' && is_page($pagina)) {
            ?>
<script>gtag('event', 'conversion', { send_to: <?php echo wp_json_encode($cfg['id'] . '/' . $etichetta); ?>, value: 1.0, currency: 'EUR' });</script>
            <?php
        }
    }
});
