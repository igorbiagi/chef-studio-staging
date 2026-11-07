<?php get_template_part('parti/layout_open'); ?>

<?php
$modalita        = get_sub_field('tipo_visualizzazione');
$disattiva_link  = get_sub_field('disattiva_link_elementi');
$tassonomia      = get_sub_field('scegli_tassonomia');
$manuale         = get_sub_field('elementi_manuali');
$cpt             = get_sub_field('scegli_cpt');
$numero          = get_sub_field('numero_elementi');
$titolo          = get_sub_field('titolo');
$selezione_post  = get_sub_field('selezione_post');
?>

<?php if (!empty($titolo)) : ?>
  <div class="titoletto">
    <?php echo wp_kses_post($titolo); ?>
  </div>
<?php endif; ?>

<div class="contenuto contenuto-con-scroll-x flex-scroll-x">
  <div class="categorie-h-scroll">
    <div class="woocommerce columns-4">

      <ul class="products columns-4">

        <?php if ($modalita === 'manuale' && have_rows('elementi_manuali')): ?>

          <?php while (have_rows('elementi_manuali')): the_row(); 

            $img    = get_sub_field('immagine');
            $titolo = get_sub_field('titolo');
            $link   = get_sub_field('link');
          ?>

            <li class="product-category product">

              <?php if (!$disattiva_link && $link): ?>
                <a href="<?php echo esc_url($link); ?>">
              <?php endif; ?>

                <?php if ($img): ?>
                  <figure class="contenitore-img">
                    <?php
                    $img_alt = is_array($img) && !empty($img['alt'])
                      ? $img['alt']
                      : wp_strip_all_tags((string) $titolo);

                    echo chefstudio_responsive_image($img, 'anteprima-scroll-card', [
                      'alt'      => $img_alt,
                      'loading'  => 'lazy',
                      'decoding' => 'async',
                      'sizes'    => '(max-width: 768px) 160px, 244px',
                    ]);
                    ?>
                  </figure>
                <?php endif; ?>

              <?php if (!$disattiva_link && $link): ?>
                </a>
              <?php endif; ?>

              <?php if (!$disattiva_link && $link): ?>
                <a href="<?php echo esc_url($link); ?>">
              <?php endif; ?>

                <?php if ($titolo): ?>
                  <h2 class="woocommerce-loop-category__title">
                    <?php echo wp_kses_post($titolo); ?>
                  </h2>
                <?php endif; ?>

              <?php if (!$disattiva_link && $link): ?>
                </a>
              <?php endif; ?>

            </li>

          <?php endwhile; ?>

        <?php elseif ($modalita === 'cpt' && $cpt): 

          $query = new WP_Query([
            'post_type'      => $cpt,
            'posts_per_page' => $numero ?: -1,
            'post_status'    => 'publish'
          ]);

          if ($query->have_posts()):

            while ($query->have_posts()): $query->the_post();

              $img = get_post_thumbnail_id(get_the_ID());
        ?>

            <li class="product-category product">

              <?php if (!$disattiva_link): ?>
                <a href="<?php the_permalink(); ?>">
              <?php endif; ?>

                <?php if ($img): ?>
                  <figure class="contenitore-img">
                    <?php echo chefstudio_responsive_image($img, 'anteprima-scroll-card', [
                      'alt'      => get_the_title(),
                      'loading'  => 'lazy',
                      'decoding' => 'async',
                      'sizes'    => '(max-width: 768px) 160px, 244px',
                    ]); ?>
                  </figure>
                <?php endif; ?>

              <?php if (!$disattiva_link): ?>
                </a>
              <?php endif; ?>

              <?php if (!$disattiva_link): ?>
                <a href="<?php the_permalink(); ?>">
              <?php endif; ?>

                <h2 class="woocommerce-loop-category__title">
                  <?php the_title(); ?>
                </h2>

              <?php if (!$disattiva_link): ?>
                </a>
              <?php endif; ?>

            </li>

          <?php endwhile; wp_reset_postdata(); endif; ?>

        <?php elseif ($modalita === 'singoli-post' && $selezione_post): ?>

          <?php
          global $post;
          $original_post = $post;

          foreach ($selezione_post as $post):

            setup_postdata($post);

            $post_id = get_the_ID();
            $img     = get_post_thumbnail_id($post_id);
            $titolo  = get_the_title($post_id);
            $link    = get_permalink($post_id);
          ?>

            <li class="product-category product">

              <?php if (!$disattiva_link): ?>
                <a href="<?php echo esc_url($link); ?>">
              <?php endif; ?>

                <?php if ($img): ?>
                  <figure class="contenitore-img">
                    <?php echo chefstudio_responsive_image($img, 'anteprima-scroll-card', [
                      'alt'      => $titolo,
                      'loading'  => 'lazy',
                      'decoding' => 'async',
                      'sizes'    => '(max-width: 768px) 160px, 244px',
                    ]); ?>
                  </figure>
                <?php endif; ?>

              <?php if (!$disattiva_link): ?>
                </a>
              <?php endif; ?>

              <?php if (!$disattiva_link): ?>
                <a href="<?php echo esc_url($link); ?>">
              <?php endif; ?>

                <h2 class="woocommerce-loop-category__title">
                  <?php echo wp_kses_post($titolo); ?>
                </h2>

              <?php if (!$disattiva_link): ?>
                </a>
              <?php endif; ?>

            </li>

          <?php endforeach; wp_reset_postdata(); $post = $original_post; ?>

        <?php elseif ($modalita === 'tassonomia' && $tassonomia): 

          $terms = get_terms([
            'taxonomy'   => $tassonomia,
            'hide_empty' => false
          ]);

          if (!empty($terms)):

            foreach ($terms as $term):

              $img  = get_field('immagine_anteprima', $tassonomia . '_' . $term->term_id);
              $link = get_term_link($term);
        ?>

              <li class="product-category product">

                <?php if (!$disattiva_link): ?>
                  <a href="<?php echo esc_url($link); ?>">
                <?php endif; ?>

                  <?php if (!empty($img)): ?>
                    <figure class="contenitore-img">
                      <?php
                      $img_alt = is_array($img) && !empty($img['alt']) ? $img['alt'] : $term->name;

                      echo chefstudio_responsive_image($img, 'anteprima-scroll-card', [
                        'alt'      => $img_alt,
                        'loading'  => 'lazy',
                        'decoding' => 'async',
                        'sizes'    => '(max-width: 768px) 160px, 244px',
                      ]);
                      ?>
                    </figure>
                  <?php endif; ?>

                <?php if (!$disattiva_link): ?>
                  </a>
                <?php endif; ?>

                <?php if (!$disattiva_link): ?>
                  <a href="<?php echo esc_url($link); ?>">
                <?php endif; ?>

                  <h2 class="woocommerce-loop-category__title">
                    <?php echo esc_html($term->name); ?>
                  </h2>

                <?php if (!$disattiva_link): ?>
                  </a>
                <?php endif; ?>

              </li>

        <?php
            endforeach;
          endif;

        endif;
        ?>

      </ul>

    </div>
  </div>
</div>

<?php get_template_part('parti/layout_close'); ?>
