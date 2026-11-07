<section class="contenitore">
  <div class="contenuto">

    <div class="padding-bot-15px margin-top-30px">
      <h2><?php echo get_sub_field('titolo_a_tutta_pagina'); ?></h2>
    </div>

    <?php if (have_rows('ripetitore_immagini_piccole')): ?>
      <ul class="marchi">

        <?php while (have_rows('ripetitore_immagini_piccole')) : the_row(); ?>
          <?php
          $immagine_piccola = get_sub_field('immagine_piccola');
          ?>

          <li>
            <?php if (get_sub_field('link')): ?>
              <?php $link = get_sub_field('link'); ?>

              <a href="<?php echo esc_url($link); ?>" target="_blank">
                <figure class="contenitore-marchio">
                  <img class="flex" src="<?php echo esc_url($immagine_piccola['sizes']['loghi-piccoli']); ?>" alt="<?php echo esc_attr($immagine_piccola['alt']); ?>" />
                </figure>
              </a>

            <?php else : ?>

              <figure class="contenitore-marchio">
                <img class="flex" src="<?php echo esc_url($immagine_piccola['sizes']['loghi-piccoli']); ?>" alt="<?php echo esc_attr($immagine_piccola['alt']); ?>" />
              </figure>

            <?php endif; ?>
          </li>

        <?php endwhile; ?>

      </ul>

      <hr>
    <?php endif; ?>

  </div>
</section>