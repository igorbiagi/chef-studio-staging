<?php 
get_header(); 
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<div class="contenitore ded-testo-largo-header">
  <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
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

<main class="contenitore">
  <div class="contenuto-large pad-top-small pad-bottom-large pad-x-standard chef-porfolio<?php if (get_field('rimuovi_padding_immagini')) echo ' chef-no-img-padding-portfolio'; ?>">

    <article class="struttura-container struttura-2-standard">

      <div class="div-1">
        <header>
          <div class="contenitore">
            <div class="">
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
            <?php $thumb = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
            <figure>
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
            </figure>
          <?php endif; ?>
        </header>

        <section class="project-content">
          <?php the_content(); ?>
        </section>

        <section class="condivisione-social" aria-label="Condivisione progetto">
          <?php get_template_part('template-parts/condivisione-social'); ?>
        </section>
      </div>

      <section class="div-2" aria-label="Galleria immagini progetto">
        <?php $galleria = get_field('galleria_immagini'); ?>
        <?php if ($galleria) : ?>
          <?php foreach ($galleria as $img) : ?>
            <figure>
              <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
            </figure>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

    </article>

  </div>
</main>

<section class="contenitore" aria-label="Altri progetti">
  <div class="contenuto-large pad-top-large pad-bottom-large pad-x-standard">
    <div class="button-container">
      <a href="<?php echo esc_url(get_permalink(961)); ?>" class="btn btn-primary">
        Guarda gli altri progetti
      </a>
    </div>
  </div>
</section>

<?php get_footer(); ?>