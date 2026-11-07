<?php
/*
Template Name: Pagina Woocommerce
*/

get_header();

// get the current taxonomy term
$term = get_queried_object();



// vars
$image = get_field('immagine_1', $term);
$testo = get_field('testo_1', $term);
$immagine_di_apertura = get_field('immagine_di_apertura');


$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

	
<div class="contenitore">
    <div class="contenuto-no-padding">
        <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
        }
        ?>
    </div>
</div> <!-- fine contenitore -->


<div class="contenitore">
	<div class="contenuto-header">
		
<h1><?php echo get_field('titolo_h1_principale')?></h1>
		  
<?php visualizza_checkout_steps() ?>
		  
	  
		  
	
<?php echo the_content(); ?>

<?php
			// ID of the current item in the WordPress Loop
			$id = get_the_ID();

			// controlla se il contenuto flessibile ha delle righe
			if ( have_rows( 'layout_flessibile_1', $id ) ) :

			// avvia il loop con le singole righe del layout flessibile e ottieni i relativi file dei singoli pezzi (chiamali uguali alle righe di acf)
			while ( have_rows( 'layout_flessibile_1', $id ) ) : the_row();
			echo "<div class='contenitore'>";
			echo "<div class='contenuto'>";
					get_template_part( 'parti/' . get_row_layout() );
			echo "</div>";
			echo "</div>";

			endwhile;

			elseif ( get_the_content() ) :

			// no layouts found

			endif;
			?>

</div><!-- fine contenuto -->		
</div><!-- fine contenitore -->	
	
	
	
	



<?php

get_footer();