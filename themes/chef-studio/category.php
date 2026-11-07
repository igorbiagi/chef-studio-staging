<?php
/*
Template categoria blog
Usa la main query di WordPress per mostrare gli articoli della categoria corrente.
*/

get_header();

$term = get_queried_object();

$term_name        = isset($term->name) ? $term->name : '';
$term_description = term_description();
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

  <div class="contenitore ded-testo-largo-header">
    <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
      <header class="testo-largo">
        <h1>
          <?php echo esc_html($term_name); ?>
        </h1>

        <?php if ($term_description) : ?>
          <div class="archive-subtitle">
            <?php echo wp_kses_post($term_description); ?>
          </div>
        <?php else : ?>
          <p class="archive-subtitle">
            Articoli del blog nella categoria <?php echo esc_html($term_name); ?>.
          </p>
        <?php endif; ?>
      </header>
    </div>
  </div>

  <?php if (have_posts()) : ?>
    <section class="contenitore" aria-labelledby="category-list-title">
      <div class="contenuto-standard pad-top-small pad-bottom-large pad-x-standard">

        <h2 id="category-list-title" class="screen-reader-text">
          Elenco articoli della categoria <?php echo esc_html($term_name); ?>
        </h2>

        <div class="anteprime-4 anteprime-cpt-post">
          <ul>
            <?php
            if (!isset($GLOBALS['chef_blog_page_seen_cards'])) {
              $GLOBALS['chef_blog_page_seen_cards'] = [];
            }

            $chef_blog_seen_cards  = [];
            $chef_blog_debug_cards = [];

            while (have_posts()) :
              the_post();

              $card_id        = get_the_ID();
              $card_slug      = (string) get_post_field('post_name', $card_id);
              $card_permalink = (string) get_permalink($card_id);
              $card_key       = $card_slug ?: $card_permalink ?: (string) $card_id;

              $chef_blog_debug_cards[] = $card_id . ':' . $card_key;

              if (
                isset($chef_blog_seen_cards[$card_key]) ||
                isset($GLOBALS['chef_blog_page_seen_cards'][$card_key])
              ) {
                continue;
              }

              $chef_blog_seen_cards[$card_key] = true;
              $GLOBALS['chef_blog_page_seen_cards'][$card_key] = true;

              chef_render_anteprima_card($card_id, [
                'image_size' => 'large',
              ]);

            endwhile;

            if (current_user_can('manage_options') && isset($_GET['chef_debug_blog'])) {
              echo "\n<!-- Chef category grid cards: " . esc_html(implode(' | ', $chef_blog_debug_cards)) . " -->\n";
            }
            ?>
          </ul>
        </div>

      </div>
    </section>
  <?php else : ?>

    <div class="contenitore">
      <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard txt-center">
        <p>Non sono ancora presenti articoli in questa categoria.</p>
      </div>
    </div>

  <?php endif; ?>

  <div class="contenitore">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard txt-center">
      <nav aria-label="Paginazione articoli della categoria">
        <?php my_pagination(); ?>
      </nav>
    </div>
  </div>

</main>

<?php get_footer(); ?>