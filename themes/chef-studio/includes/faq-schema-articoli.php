<?php
/**
 * Schema FAQPage sugli articoli che hanno una sezione "Domande frequenti" scritta nel testo
 * (H2 "Domande frequenti ..." seguito da domande in H3 o H4 con la risposta sotto).
 * Le domande vanno al raccoglitore del tema, che stampa il FAQPage nel footer solo se
 * il plugin FAQ SEO non ha già stampato il suo.
 */

if (!defined('ABSPATH')) {
    exit;
}

function chefstudio_faq_da_testo($html) {
    if (!is_string($html) || stripos($html, 'domande frequenti') === false) {
        return [];
    }

    if (!preg_match('/<h2[^>]*>(?:(?!<\/h2>).)*domande frequenti(?:(?!<\/h2>).)*<\/h2>/is', $html, $m, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $sezione = substr($html, $m[0][1] + strlen($m[0][0]));
    $fine = stripos($sezione, '<h2');
    if ($fine !== false) {
        $sezione = substr($sezione, 0, $fine);
    }

    $parti = preg_split('/(<h[34][^>]*>.*?<\/h[34]>)/is', $sezione, -1, PREG_SPLIT_DELIM_CAPTURE);
    $voci  = [];

    for ($i = 1; $i < count($parti); $i += 2) {
        // I tag citati come testo (&lt;h1&gt;) diventano nomi semplici: nello script JSON-LD
        // un "<img>" decodificato verrebbe riscritto dai filtri di lazy load e romperebbe il JSON.
        $parti[$i + 1] = preg_replace('/&lt;\/?([a-z][a-z0-9]*)[^&]*?&gt;/i', '$1', $parti[$i + 1] ?? '');
        $parti[$i]     = preg_replace('/&lt;\/?([a-z][a-z0-9]*)[^&]*?&gt;/i', '$1', $parti[$i]);
        $domanda  = trim(wp_strip_all_tags($parti[$i]));
        $risposta = trim(str_replace(['<', '>'], '', preg_replace('/\s+/', ' ', wp_strip_all_tags(preg_replace('/<\/(p|li|ul|ol|div|table|tr|td|th)>/i', '$0 ', strip_shortcodes($parti[$i + 1] ?? ''))))));
        if ($domanda !== '' && $risposta !== '') {
            $voci[] = ['question' => $domanda, 'answer' => $risposta];
        }
    }

    return $voci;
}

add_action('wp', function () {
    if (is_admin() || !is_singular('post') || !function_exists('chefstudio_schema_collect_faq_items')) {
        return;
    }

    $post = get_post(get_queried_object_id());
    if (!$post) {
        return;
    }

    $voci = chefstudio_faq_da_testo($post->post_content);
    if ($voci) {
        chefstudio_schema_collect_faq_items($voci, 'faq-articolo');
    }
});
