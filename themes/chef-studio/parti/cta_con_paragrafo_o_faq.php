<?php get_template_part('parti/layout_open'); ?>

<?php
$tipo_contenuto = get_sub_field('tipo_contenuto');
$shortcode_cta = trim((string) get_sub_field('shortcode_cta'));
$has_shortcode_cta = $shortcode_cta !== '';
$has_faq_cta_layout = $has_shortcode_cta || in_array($tipo_contenuto, ['FAQ', 'Paragrafo'], true);
$classe_faq_cta = $has_faq_cta_layout ? ' faq-cta-layout' : '';

if ($has_shortcode_cta) {
  $shortcode_cta = preg_replace_callback(
    '/\[(chef_blog_cta|chef_cta_blog|chef_blog_cta_custom|chef_blog_cta_personalizzata)([^\]]*)\]/',
    function ($matches) {
      $tag = $matches[1];
      $attributes = $matches[2] ?? '';

      if (preg_match('/\slayout\s*=/i', $attributes)) {
        return $matches[0];
      }

      if (preg_match('/\/\s*$/', $attributes)) {
        $attributes = preg_replace('/\s*\/\s*$/', ' layout="side" /', $attributes);
      } else {
        $attributes .= ' layout="side"';
      }

      return '[' . $tag . $attributes . ']';
    },
    $shortcode_cta
  );
}

$faq_cta_link        = get_sub_field('link_cta');
$faq_cta_button_text = get_sub_field('testo_bottone');
$faq_cta_url         = '';
$faq_cta_target      = '';

if (is_array($faq_cta_link)) {
  $faq_cta_url = $faq_cta_link['url'] ?? '';

  if (empty($faq_cta_button_text) && !empty($faq_cta_link['title'])) {
    $faq_cta_button_text = $faq_cta_link['title'];
  }

  $faq_cta_target = $faq_cta_link['target'] ?? '';
} elseif (is_string($faq_cta_link)) {
  $faq_cta_url = $faq_cta_link;
}

$telefono = get_field('telefono', 'option');
$telefono_link = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';
?>

<div class="struttura-container struttura-2-preview chef-animazione-tutta-altezza<?php echo esc_attr($classe_faq_cta); ?>">

  <div class="div-1">

    <?php if ($tipo_contenuto === 'FAQ'): ?>

      <section class="faq-content faq-premium faq-side-layout">

        <?php if (get_sub_field('nome_sezione')): ?>
          <div class="nome-sezione">
            <?php echo esc_html(get_sub_field('nome_sezione')); ?>
          </div>
        <?php endif; ?>

        <?php if (get_sub_field('titolo_faq')): ?>
          <h2><?php echo wp_kses_post(get_sub_field('titolo_faq')); ?></h2>
        <?php endif; ?>

        <?php if (get_sub_field('testo_faq')): ?>
          <div class="faq-intro-text">
            <?php echo wp_kses_post(get_sub_field('testo_faq')); ?>
          </div>
        <?php endif; ?>

        <div class="faq-list">

        <?php if (have_rows('faq')): ?>

          <?php while (have_rows('faq')): the_row();

            $idx           = get_row_index();
            $input_id      = 'faq-cta-title-' . uniqid() . '-' . $idx;

            $domanda_raw   = get_sub_field('titolo_faq');
            $risposta_raw  = get_sub_field('contenuto_faq');

            $domanda_txt   = trim(wp_strip_all_tags((string)$domanda_raw));
            $risposta_txt  = trim(wp_strip_all_tags((string)$risposta_raw));

            if ($domanda_txt === '' && $risposta_txt === '') continue;
          ?>

            <div class="faq-accordion">

              <input type="checkbox" class="tgg-title" id="<?php echo esc_attr($input_id); ?>">

              <div class="faq-accordion-title">
                <label for="<?php echo esc_attr($input_id); ?>">

                  <span class="faq-toggle-icon" aria-hidden="true"></span>

                  <?php if ($domanda_txt !== ''): ?>
                    <span class="faq-question-text"><?php echo wp_kses_post($domanda_raw); ?></span>
                  <?php endif; ?>

                  <span class="arrow-icon" aria-hidden="true">
                    <img 
                      src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/arrow-down.svg" 
                      alt="freccia giù"
                      aria-hidden="true"
                    >
                  </span>

                </label>
              </div>

              <?php if ($risposta_txt !== ''): ?>
                <div class="faq-accordion-content">
                  <div class="faq-answer-inner">
                    <?php echo wp_kses_post($risposta_raw); ?>
                  </div>
                </div>
              <?php endif; ?>

            </div>

          <?php endwhile; ?>

        <?php endif; ?>

        </div>

      </section>

    <?php elseif ($tipo_contenuto === 'Paragrafo'): ?>

      <?php $paragrafo = get_sub_field('paragrafo'); ?>

      <?php if (!empty($paragrafo)): ?>
        <?php echo $paragrafo; ?>
      <?php endif; ?>

    <?php endif; ?>

  </div>

  <div class="div-2">

    <?php $immagine = get_sub_field('immagine'); ?>

    <?php if ($has_shortcode_cta): ?>

      <?php echo do_shortcode($shortcode_cta); ?>

    <?php elseif ($has_faq_cta_layout): ?>

      <section class="call-to-action faq-side-cta">

        <div class="faq-side-cta-inner">

          <?php if ($immagine): ?>
            <figure class="faq-side-cta-media">
              <?php echo chefstudio_responsive_image($immagine, 'medium', [
                'alt'      => is_array($immagine) ? ($immagine['alt'] ?? '') : '',
                'loading'  => 'lazy',
                'decoding' => 'async',
                'sizes'    => '(max-width: 768px) 180px, 217px',
              ]); ?>
            </figure>
          <?php endif; ?>

          <div class="faq-side-cta-body">

            <?php if (get_sub_field('titolo_cta') || get_sub_field('sottotitolo_cta')): ?>
              <div class="faq-side-cta-copy">
                <?php if (get_sub_field('titolo_cta')): ?>
                  <h3><?php echo wp_kses_post(get_sub_field('titolo_cta')); ?></h3>
                <?php endif; ?>

                <?php if (get_sub_field('sottotitolo_cta')): ?>
                  <div class="faq-side-cta-subtitle">
                    <?php echo wp_kses_post(get_sub_field('sottotitolo_cta')); ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php elseif (get_sub_field('testo_cta')): ?>
              <div class="faq-side-cta-copy">
                <?php echo chefstudio_responsive_editor_content(get_sub_field('testo_cta')); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($faq_cta_url) && !empty($faq_cta_button_text)): ?>
              <a
                href="<?php echo esc_url($faq_cta_url); ?>"
                class="btn btn-super neon faq-side-cta-button"
                <?php if (!empty($faq_cta_target)): ?>target="<?php echo esc_attr($faq_cta_target); ?>"<?php endif; ?>
                <?php if ($faq_cta_target === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
              >
                <span><?php echo esc_html($faq_cta_button_text); ?></span>
                <span class="hero-btn-arrow" aria-hidden="true"></span>
              </a>
            <?php endif; ?>

            <?php if (!empty($telefono)): ?>
              <a class="faq-side-cta-phone" href="tel:<?php echo esc_attr($telefono_link); ?>">
                <img 
                  class="telefono-icon" 
                  src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/phone.svg" 
                  alt="icona telefono"
                  aria-hidden="true"
                >
                <span><?php echo esc_html($telefono); ?></span>
              </a>
            <?php endif; ?>

          </div>

        </div>

      </section>

    <?php else: ?>

      <section class="call-to-action">

        <div class="media-object">

          <div class="info-call-wrap">

            <div class="testo-call">
              <div class="info">
                <?php if (get_sub_field('titolo_cta') || get_sub_field('sottotitolo_cta')): ?>
                  <?php if (get_sub_field('titolo_cta')): ?>
                    <h3><?php echo wp_kses_post(get_sub_field('titolo_cta')); ?></h3>
                  <?php endif; ?>

                  <?php if (get_sub_field('sottotitolo_cta')): ?>
                    <?php echo wp_kses_post(get_sub_field('sottotitolo_cta')); ?>
                  <?php endif; ?>
                <?php elseif (get_sub_field('testo_cta')): ?>
                  <?php echo chefstudio_responsive_editor_content(get_sub_field('testo_cta')); ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="img-call">
              <div class="img-call-contenuto">
                <?php if ($immagine): ?>
                  <figure>
                    <?php echo chefstudio_responsive_image($immagine, 'medium', [
                      'alt'      => is_array($immagine) ? ($immagine['alt'] ?? '') : '',
                      'loading'  => 'lazy',
                      'decoding' => 'async',
                      'sizes'    => '(max-width: 768px) 180px, 217px',
                    ]); ?>
                  </figure>
                <?php endif; ?>
              </div>
            </div>

            <div class="button-call">
              <div class="media-object-btn">
                <?php if (!empty($faq_cta_url) && !empty($faq_cta_button_text)): ?>
                  <a
                    href="<?php echo esc_url($faq_cta_url); ?>"
                    class="btn btn-super neon"
                    <?php if (!empty($faq_cta_target)): ?>target="<?php echo esc_attr($faq_cta_target); ?>"<?php endif; ?>
                    <?php if ($faq_cta_target === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
                  >
                    <?php echo esc_html($faq_cta_button_text); ?>
                  </a>
                <?php endif; ?>
              </div>
            </div>

          </div>

        </div>

      </section>

    <?php endif; ?>

  </div>

</div>

<?php get_template_part('parti/layout_close'); ?>
