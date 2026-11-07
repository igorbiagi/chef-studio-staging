<?php
/* Template Name: Archivio Progetti */
get_header();

// Recupera la pagina "progetti"
$default_page = get_page_by_path('progetti');
if ($default_page) {
  $page_id = $default_page->ID;
} else {
  echo '<p>Pagina "progetti" non trovata.</p>';
  get_footer();
  exit;
}

// Campi ACF
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
            <h1><?php echo esc_html($titolo_principale); ?></h1>
          <?php endif; ?>

          <?php if ($sottotitolo) : ?>
            <p class="archive-subtitle"><?php echo esc_html($sottotitolo); ?></p>
          <?php endif; ?>
        </header>
      </div>
    </div>
  <?php endif; ?>

  <?php
  /* --------------------------
     Impostazioni paginazione
  --------------------------- */
  $paged = max(1, get_query_var('paged'));
  $ppp   = (int) get_option('posts_per_page');

  /* --------------------------
     Featured project (solo page 1)
  --------------------------- */
  $featured_project = null;

  if ($paged === 1) {
    $featured_q = new WP_Query([
      'post_type'      => 'progetti',
      'posts_per_page' => 1,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);

    if ($featured_q->have_posts()) {
      $featured_q->the_post();
      $featured_project = get_the_ID();
      ?>
      <div class="contenitore">
        <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
          <article class="header-promo progetti-anteprima-grande">
            <a href="<?php the_permalink(); ?>">
              <div class="struttura-container struttura-2-preview">

                <div class="div-1">
                  <?php if (has_post_thumbnail()) : ?>
                    <figure class="contenitore-img">
                      <?php the_post_thumbnail('large'); ?>
                    </figure>
                  <?php endif; ?>
                </div>

                <div class="div-2">
                  <div class="testo-anteprima">
                    <h2 class="titolo"><?php the_title(); ?></h2>

                    <?php if ($excerpt = get_the_excerpt()) : ?>
                      <p class="testo"><?php echo wp_trim_words($excerpt, 30, '...'); ?></p>
                    <?php endif; ?>
                  </div>
                </div>

              </div>
            </a>
          </article>
        </div>
      </div>
      <?php
    }
    wp_reset_postdata();
  }

  /* --------------------------
     Query con offset continuo
  --------------------------- */
  $offset = 1 + ($paged - 1) * $ppp;

  $grid_q = new WP_Query([
    'post_type'      => 'progetti',
    'post_status'    => 'publish',
    'posts_per_page' => $ppp,
    'offset'         => $offset,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);

  // Calcolo totale pagine
  $tot_pubb    = (int) wp_count_posts('progetti')->publish;
  $tot_eff     = max(0, $tot_pubb - 1);
  $total_pages = ($ppp > 0) ? (int) ceil($tot_eff / $ppp) : 1;
  ?>

  <?php if ($grid_q->have_posts()) : ?>
    <section class="contenitore" aria-labelledby="progetti-list-title">
      <div class="contenuto-standard pad-top-small pad-bottom-large pad-x-standard">

        <h2 id="progetti-list-title" class="screen-reader-text">Elenco progetti</h2>

        <div class="anteprime-3 anteprime-cpt-progetti">
          <ul>
            <?php while ($grid_q->have_posts()) : $grid_q->the_post(); ?>
              <?php chef_render_anteprima_card(get_the_ID(), ['image_size' => 'large']); ?>
            <?php endwhile; ?>
          </ul>
        </div>

      </div>
    </section>
  <?php endif; wp_reset_postdata(); ?>

  <div class="contenitore">
    <div class="contenuto-standard pad-top-small pad-bottom-large pad-x-standard">
      <nav aria-label="Paginazione progetti">
        <?php my_pagination(); ?>
      </nav>
    </div>
  </div>

</main>

<?php get_footer(); ?>
