<?php
/*
Template Name: Progetto - layout pagina normale
Template Post Type: progetti
*/

get_header();

$page_id = get_queried_object_id();
?>

<main id="main-content">

    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            ?>

            <?php if (trim(get_the_content())) : ?>
                <div class="contenitore">
                    <div class="contenuto pad-top-small pad-bottom-small pad-x-standard">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (have_rows('layout_flessibile_1', $page_id)) :

                while (have_rows('layout_flessibile_1', $page_id)) :
                    the_row();

                    if (get_row_layout() === 'blocco_riutilizzabile') {

                        $blocco_id = get_sub_field('seleziona_blocco_riutilizzabile');

                        if ($blocco_id && have_rows('layout_flessibile_1', $blocco_id)) {
                            while (have_rows('layout_flessibile_1', $blocco_id)) :
                                the_row();
                                get_template_part('parti/' . get_row_layout());
                            endwhile;
                        }

                    } else {
                        get_template_part('parti/' . get_row_layout());
                    }

                endwhile;

            endif;
            ?>

            <?php
        endwhile;
    endif;
    ?>

</main>

<?php get_footer(); ?>
