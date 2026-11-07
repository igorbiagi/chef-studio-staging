<?php get_template_part('parti/layout_open'); ?>

<?php if (function_exists('yoast_breadcrumb')) : ?>
  <nav aria-label="Breadcrumb">
    <?php yoast_breadcrumb('<p id="breadcrumbs">', '</p>'); ?>
  </nav>
<?php endif; ?>

<?php get_template_part('parti/layout_close'); ?>