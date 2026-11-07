<div class="contenitore <?php echo get_sub_field('colore_di_sfondo'); ?>">
	<div class="contenuto">
		<h2><?php echo get_sub_field('titolo'); ?></h2>
		<div class="contenitore-prodotti-<?php echo get_sub_field('numero_colonne'); ?>">
			<div class="woocommerce columns-<?php echo get_sub_field('numero_colonne'); ?> ">
 				<?php echo do_shortcode('[featured_products columns="4"]'); ?>
			</div>
		</div>
	</div>
</div>