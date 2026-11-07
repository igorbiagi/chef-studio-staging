<?php
/*
Template di pagina negozio
*/
get_header();
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$shop_page_id = wc_get_page_id('shop');

$testo_intro = get_field('testo_intro', $shop_page_id);
$testo_fine = get_field('testo_fine', $shop_page_id);
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
	
	<div class="struttura-side struttura-side-left">
		
		<div class="sidebar">
			<div class="<?php echo get_field('tipo_di_sidebar'); ?>">
		      <?php echo do_shortcode('[yith_wcan_filters slug="default-preset"]'); ?>
			</div>
		</div><!--/fine side bar -->
		
		<div class="contenuto-side contenitore-prodotti-4">
		
			<?php echo $testo_intro; ?>
			
	
	
<div class="linea">
	<h2>Monete</h2>
	<h3>Monete Antiche</h3>
	<?php echo do_shortcode('[product_categories number="0" parent="21" hide_empty="false"]'); ?>
	<hr>
	<h3>Monete Moderne</h3>
	<?php echo do_shortcode('[product_categories number="0" parent="81" hide_empty="false"]'); ?>
	
</div>
			<hr>
<div class="linea">
	<h2>Medaglie</h2>
	<?php echo do_shortcode('[products category="19" limit="10" columns="4"]'); ?>
</div>

			
		<div class="testo-largo">
			<?php echo $testo_fine; ?>
		</div>	

		</div><!--/contenuto side -->
		
		
		
			
	
	</div><!--/struttura-side -->

	</div><!--/contenuto -->	
    </div><!--/contenitore-->





	
<?php
get_footer();