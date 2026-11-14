<?php
/**
 * Schema FAQPage sulle landing con template proprio (Care e Bonifica):
 * le loro FAQ sono campi del template (lcare_faq, lbh_faq), non il layout "faq",
 * quindi né il plugin FAQ SEO né il layout flessibile le vedono.
 * Le passa al raccoglitore del tema, che stampa il FAQPage nel footer.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp', function () {
    if (is_admin() || !is_singular('page') || !function_exists('chefstudio_schema_collect_faq_items')) {
        return;
    }

    $mappa = [
        'pagine/landing-care.php'     => ['chefstudio_lcare', 'lcare_faq'],
        'pagine/landing-bonifica.php' => ['chefstudio_lbh', 'lbh_faq'],
    ];

    $template = get_page_template_slug(get_queried_object_id());

    if (!isset($mappa[$template]) || !function_exists($mappa[$template][0])) {
        return;
    }

    [$funzione, $campo] = $mappa[$template];
    $righe = array_values((array) call_user_func($funzione, $campo));
    $voci  = [];

    foreach ($righe as $riga) {
        if (!is_array($riga)) {
            continue;
        }
        $voci[] = [
            'question' => $riga['domanda'] ?? '',
            'answer'   => $riga['risposta'] ?? '',
        ];
    }

    chefstudio_schema_collect_faq_items($voci, $campo);
});
