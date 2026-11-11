<?php
/**
 * Articolo (post) mostrato come pagina servizio a blocchi.
 * Si attiva da single.php quando l'articolo ha righe in layout_flessibile_1:
 * l'URL resta quello dell'articolo, il testo originale resta nel database ma non viene mostrato.
 * Per tornare all'articolo classico basta svuotare il layout flessibile.
 */
get_header();
$post_id = get_queried_object_id();
?>
<main id="main-content">
<?php
if (have_rows('layout_flessibile_1', $post_id)) :
  while (have_rows('layout_flessibile_1', $post_id)) :
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
</main>
<?php get_footer(); ?>
