<?php get_template_part('parti/layout_open'); ?>

<?php
$immagini_galleria = get_sub_field('galleria');
?>

<section id="container-scroll-buttons">
  <ul class="gallery-scroll" id="content-scroll-buttons">
    <?php if (!empty($immagini_galleria)) : ?>
      <?php foreach ($immagini_galleria as $img_item): ?>
        <li>
          <figure class="anteprima">
            <img 
              src="<?php echo esc_url($img_item['sizes']['medium_large']); ?>" 
              alt="<?php echo esc_attr($img_item['alt']); ?>" 
              title="<?php echo esc_attr($img_item['title']); ?>"
            >
          </figure>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</section>

<?php get_template_part('parti/layout_close'); ?>

<?php global $classi_contenitore_aperti, $classi_contenuto_aperti; ?>

<div class="<?php echo esc_attr(implode(' ', $classi_contenitore_aperti)); ?>">
  <div class="contenuto-standard">
    <div id="dots-container" class="dots"></div>

    <div class="container-arrows">
      <button id="slideLeft" type="button" aria-label="Scorri galleria a sinistra">
        <img 
          src="<?php echo get_template_directory_uri(); ?>/img/svg/arrow-right.svg" 
          alt="freccia destra" 
          aria-hidden="true"
        >
      </button>

      <button id="slideRight" type="button" aria-label="Scorri galleria a destra">
        <img 
          src="<?php echo get_template_directory_uri(); ?>/img/svg/arrow-right.svg" 
          alt="freccia sinistra" 
          aria-hidden="true"
        >
      </button>
    </div>
  </div>
</div>