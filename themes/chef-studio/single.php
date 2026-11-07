<?php
/**
 * Template Name: Articolo Blog
 * Template Post Type: post
 */

get_header();
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<div class="contenitore">
  <div class="contenuto-standard chef-bread">
    <?php if (function_exists('yoast_breadcrumb')) : ?>
      <nav aria-label="Breadcrumb">
        <?php yoast_breadcrumb('<p id="breadcrumbs">', '</p>'); ?>
      </nav>
    <?php endif; ?>
  </div>
</div>

<div id="et-progress-container" aria-hidden="true">
  <div id="et-progress-bar"></div>
</div>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <main class="contenitore">
    <div class="contenuto-standard pad-top-small pad-bottom-large pad-x-standard">
      <div class="struttura-side struttura-side-right struttura-side-blog">

        <article class="contenuto-side">

          <header>
            <div class="contenitore">
              <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
                <div class="testo-largo">
                  <?php
                  $titolo_h1 = get_field('titolo_h1_separato');

                  if ($titolo_h1) {
                      echo '<h1>' . esc_html($titolo_h1) . '</h1>';
                  } else {
                      echo '<h1>' . get_the_title() . '</h1>';
                  }
                  ?>
                </div>
              </div>
            </div>

            <?php if (has_post_thumbnail()) : ?>
              <div class="contenitore">
                <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
                  <figure class="img-no-shift">
                    <?php the_post_thumbnail('large'); ?>
                  </figure>
                </div>
              </div>
            <?php endif; ?>
          </header>

          <?php // echo do_shortcode('[ez-toc]'); ?>

          <div class="contenitore articolo">
            <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
              <?php the_content(); ?>

              <section class="author-box">
                <?php author_info_box(); ?>
              </section>

              <section class="condivisione-social" aria-label="Condivisione articolo">
                <?php get_template_part('template-parts/condivisione-social'); ?>
              </section>
            </div>
          </div>

        </article>

        <aside class="sidebar sidebar-blog">
          <?php get_template_part('template-parts/sidebar_1'); ?>
        </aside>

      </div>
    </div>
  </main>

  <section class="contenitore single-related-posts" aria-labelledby="articoli-correlati-title">
    <div class="contenuto-standard">
      <hr>
      <h3 id="articoli-correlati-title">Articoli correlati</h3>

      <div class="anteprime-4 anteprime-cpt-post">
        <ul>
          <?php
          $related = new WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'post__not_in'   => [get_the_ID()],
            'orderby'        => 'date',
            'order'          => 'DESC'
          ]);

          if ($related->have_posts()) :
            while ($related->have_posts()) : $related->the_post(); ?>
              <?php chef_render_anteprima_card(get_the_ID(), ['image_size' => 'large']); ?>
            <?php endwhile;
            wp_reset_postdata();
          endif;
          ?>
        </ul>
      </div>
    </div>
  </section>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
