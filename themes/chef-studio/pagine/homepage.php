<?php
/*
Template Name: Homepage
*/
get_header();
?>

		
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
	
<?php 
get_footer();
?>