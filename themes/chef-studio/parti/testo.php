<?php get_template_part('parti/layout_open'); ?>
<div class="<?php echo get_sub_field('larghezza_testo'); ?>">
	<?php if (get_sub_field('nome_sezione')): ?>
		<div class="nome-sezione">
			<?php echo esc_html(get_sub_field('nome_sezione')); ?>
		</div>
	<?php endif; ?>
	<?php echo chefstudio_responsive_editor_content(get_sub_field('testo')); ?>
</div>
<?php get_template_part('parti/layout_close'); ?>
