<div class="contenitore <?php echo get_sub_field('colore_di_sfondo'); ?> margin-bot-60px">
	<div class="contenuto-con-scroll-x flex-scroll-x-wrap">
		<h2><?php echo get_sub_field('titolo'); ?></h2>
		<div class="categorie-h-scroll">
			<?php echo do_shortcode('[custom_product_categories hide_empty="0" ids="' . get_sub_field('id_delle_categorie_in_evidenza') . '"]'); ?>
		</div>
	</div>
</div>