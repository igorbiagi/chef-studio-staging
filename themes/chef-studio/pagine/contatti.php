<?php
/*
Template Name: Contatti
*/

get_header();

// get the current taxonomy term
$term = get_queried_object();

$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$apertura_piccola = get_field('apertura_piccola');
$titolo_recapiti = get_field('titolo_recapiti');
$recapiti = get_field('recapiti');
$titolo_form = get_field('titolo_form');
$campi_obbligatori = get_field('campi_obbligatori');
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

<div class="struttura-container struttura-2-standard contatti">
    <div class="div-1">
		<h1><?php echo get_field('titolo_h1_principale')?></h1>
        <h2><?php echo (get_field('tirolo_recapiti')) ?></h2>
			<p class="small-txt-mobile">
		  		<?php echo (get_field('recapiti')) ?>
		  </p>
    </div>
    <div class="div-2">
		<div class="contatti">
			<h2>
				<?php echo (get_field('titolo_form')) ?>
			</h2>
			<!--<h3 class="small-txt padding-bot-15px">
				<?php echo (get_field('campi_obbligatori')) ?>
			</h3>-->
			
			<?php gravity_form(1, false, false, false, '', true, 0); ?>
		</div>
	</div>
</div>



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

	
<!--
<div class="mappa">
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3345.627638049613!2d10.225357545737868!3d43.950250604074185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12d50a9440f18571%3A0xf20785bf1f51f64b!2sFarmacia%20Comunale%20Pietrasanta!5e0!3m2!1sit!2sit!4v1607164050072!5m2!1sit!2sit" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
</div>
-->	
	

	</div> <!-- fine contenuto -->		
</div> <!-- fine contenitore -->
<?php
get_footer();
?>

