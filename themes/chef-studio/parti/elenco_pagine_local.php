<?php get_template_part('parti/layout_open'); ?>
<?php
// Recupero titolo custom
$custom_title = get_sub_field('titolo_blocco');

// Recupero i termini selezionati
$selected_terms = get_sub_field('taxonomy_terms');

if ($selected_terms) :

    if (!is_array($selected_terms)) {
        $selected_terms = array($selected_terms);
    }

    // Normalizzo i term in ID
    $term_ids = array();
    foreach ($selected_terms as $term) {
        $term_ids[] = is_object($term) ? $term->term_id : intval($term);
    }

    /**
     * Estrae solo la città dal titolo SEO (usata solo dentro questo template)
     */
    if ( ! function_exists('chefstudio_city_label_from_title_tpl') ) {
        function chefstudio_city_label_from_title_tpl($title) {
            if (!is_string($title) || $title === '') {
                return '';
            }

            $city = mb_strtolower($title, 'UTF-8');

            // Frasi ricorrenti nei tuoi titoli che NON sono città
            $remove_phrases = array(
                'realizzazione siti web',
                'realizzazione sito web',
                'creazione siti web',
                'creazione sito web',
                'sviluppo siti web',
                'sviluppo sito web',
                'agenzia seo',
                'consulenza seo',
                'consulente seo',
                'seo',
				'web agency',
            );

            // Rimuove queste frasi ovunque compaiano
            $city = preg_replace('/\b(?:' . implode('|', array_map('preg_quote', $remove_phrases)) . ')\b/u', '', $city);

            // Rimuove preposizioni rimaste “orfane”
            $city = preg_replace('/\b(a|in|di|per|su|nel|nella|nei|nelle)\b/u', ' ', $city);

            // Ripulisce separatori e spazi multipli
            $city = str_replace(array('-', '_', '|'), ' ', $city);
            $city = preg_replace('/\s+/u', ' ', $city);
            $city = trim($city);

            // Capitalizzazione “umana” (La Spezia, Reggio Emilia, ecc.)
            $city = mb_convert_case($city, MB_CASE_TITLE, 'UTF-8');

            return $city;
        }
    }
?>
    <div>

        <?php if ($custom_title) : ?>
            <div class="titoletto">
                <?php echo $custom_title; ?>
            </div>
        <?php endif; ?>

        <ul class="elenco-citta-seo">

        <?php
        $args = array(
            'post_type'      => 'pagine-locali',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'argomento_categoria',
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                )
            )
        );

        $local_pages = new WP_Query($args);

        if ($local_pages->have_posts()) :
            while ($local_pages->have_posts()) : $local_pages->the_post();

                $full_title = get_the_title();
                $city_label = chefstudio_city_label_from_title_tpl($full_title);

                echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html($city_label) . '</a></li>';

            endwhile;
        endif;

        wp_reset_postdata();
        ?>

        </ul>
    </div>

<?php endif; ?>

<?php get_template_part('parti/layout_close'); ?>
