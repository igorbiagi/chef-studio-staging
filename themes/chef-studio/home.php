<?php
/* Template Name: Blog */
get_header();
$page_id = get_queried_object_id();

$titolo_principale = get_field('titolo_principale', $page_id);
$sottotitolo       = get_field('sottotitolo', $page_id);
?>

<main id="main-content">

  <div class="contenitore ded-testo-largo-header">
    <div class="contenuto-standard chef-bread">
      <?php if (function_exists('yoast_breadcrumb')) : ?>
        <nav aria-label="Breadcrumb">
          <?php yoast_breadcrumb('<p id="breadcrumbs">', '</p>'); ?>
        </nav>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($titolo_principale || $sottotitolo) : ?>
    <div class="contenitore ded-testo-largo-header">
      <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
        <header class="testo-largo">
          <?php if ($titolo_principale) : ?>
            <h1><?php echo wp_kses_post($titolo_principale); ?></h1>
          <?php endif; ?>

          <?php if ($sottotitolo) : ?>
            <p class="archive-subtitle"><?php echo wp_kses_post($sottotitolo); ?></p>
          <?php endif; ?>
        </header>
      </div>
    </div>
  <?php endif; ?>

  <?php
  /* --------------------------
     Query blog
  --------------------------- */
  $paged = max(1, get_query_var('paged'), get_query_var('page'));

  $grid_q = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => (int) get_option('posts_per_page'),
    'paged'               => $paged,
    'orderby'             => [
      'date' => 'DESC',
      'ID'   => 'DESC',
    ],
    'ignore_sticky_posts' => true,
    'chef_blog_grid'      => true,
  ]);
  ?>

  <?php if ($grid_q->have_posts()) : ?>
    <section class="contenitore" aria-labelledby="blog-list-title">
      <div class="contenuto-standard pad-top-small pad-bottom-large pad-x-standard">

        <h2 id="blog-list-title" class="screen-reader-text">Elenco articoli del blog</h2>

        <div class="anteprime-4 anteprime-cpt-post">
          <ul>
            <?php
            if (!isset($GLOBALS['chef_blog_page_seen_cards'])) {
              $GLOBALS['chef_blog_page_seen_cards'] = [];
            }

            $chef_blog_seen_cards = [];
            $chef_blog_debug_cards = [];

            while ($grid_q->have_posts()) :
              $grid_q->the_post();

              $card_id = get_the_ID();
              $card_slug = (string) get_post_field('post_name', $card_id);
              $card_permalink = (string) get_permalink($card_id);
              $card_key = $card_slug ?: $card_permalink ?: (string) $card_id;
              $chef_blog_debug_cards[] = $card_id . ':' . $card_key;

              if (isset($chef_blog_seen_cards[$card_key]) || isset($GLOBALS['chef_blog_page_seen_cards'][$card_key])) {
                continue;
              }

              $chef_blog_seen_cards[$card_key] = true;
              $GLOBALS['chef_blog_page_seen_cards'][$card_key] = true;
              chef_render_anteprima_card($card_id, ['image_size' => 'large']);
            endwhile;

            if (current_user_can('manage_options') && isset($_GET['chef_debug_blog'])) {
              echo "\n<!-- Chef blog grid cards: " . esc_html(implode(' | ', $chef_blog_debug_cards)) . " -->\n";
            }
            ?>
          </ul>
        </div>

      </div>
    </section>
  <?php else : ?>
    <div class="contenitore">
      <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard txt-center">
        <p>Nessun articolo trovato.</p>
      </div>
    </div>
  <?php endif; ?>

  <?php wp_reset_postdata(); ?>

  <div class="contenitore">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard txt-center">
      <?php
      global $wp_query;
      $old_wp_query = $wp_query;
      $wp_query     = $grid_q;
      ?>
      <nav aria-label="Paginazione articoli">
        <?php my_pagination(); ?>
      </nav>
      <?php
      $wp_query = $old_wp_query;
      wp_reset_postdata();
      ?>
    </div>
  </div>

</main>

<?php get_footer(); ?>
