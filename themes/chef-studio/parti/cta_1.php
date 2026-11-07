<?php get_template_part('parti/layout_open'); ?>

<?php
$immagine       = get_sub_field('immagine');
$icona_cta      = get_sub_field('icona_cta');
$testo_cta      = get_sub_field('testo_cta');
$link_cta       = get_sub_field('link_cta');
$testo_bottone  = get_sub_field('testo_bottone');
$orari_cta      = get_sub_field('orari_cta');
$telefono       = get_field('telefono', 'option');
$telefono_link  = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';
$cta_url        = '';
$cta_target     = '';
$icona_cta_url  = '';
$icona_cta_alt  = '';

if (is_array($link_cta)) {
  $cta_url = $link_cta['url'] ?? '';

  if (empty($testo_bottone) && !empty($link_cta['title'])) {
    $testo_bottone = $link_cta['title'];
  }

  $cta_target = $link_cta['target'] ?? '';
} elseif (is_string($link_cta)) {
  $cta_url = $link_cta;
}

if (is_array($icona_cta)) {
  $icona_cta_url = $icona_cta['url'] ?? '';
  $icona_cta_alt = $icona_cta['alt'] ?? '';
} elseif (is_numeric($icona_cta)) {
  $icona_cta_id  = (int) $icona_cta;
  $icona_cta_url = (string) wp_get_attachment_image_url($icona_cta_id, 'thumbnail');
  $icona_cta_alt = (string) get_post_meta($icona_cta_id, '_wp_attachment_image_alt', true);
} elseif (is_string($icona_cta)) {
  $icona_cta_url = $icona_cta;
}
?>

<section class="call-to-action call-to-action-desktop cta-premium">

  <div class="cta-premium-inner">

    <?php if ($immagine): ?>
      <figure class="cta-premium-media">
        <?php echo chefstudio_responsive_image($immagine, 'medium', [
          'alt'      => is_array($immagine) ? ($immagine['alt'] ?? '') : '',
          'loading'  => 'lazy',
          'decoding' => 'async',
          'sizes'    => '(max-width: 768px) 180px, 217px',
        ]); ?>
      </figure>
    <?php endif; ?>

    <div class="cta-premium-copy">
      <?php if (!empty($icona_cta_url)): ?>
        <figure class="cta-premium-icon">
          <img src="<?php echo esc_url($icona_cta_url); ?>" alt="<?php echo esc_attr($icona_cta_alt); ?>">
        </figure>
      <?php endif; ?>

      <?php if ($testo_cta): ?>
        <div class="info">
          <?php echo chefstudio_responsive_editor_content($testo_cta); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="cta-premium-actions">

      <?php if (!empty($cta_url) && !empty($testo_bottone)): ?>
        <a
          href="<?php echo esc_url($cta_url); ?>"
          class="btn btn-super neon cta-premium-button"
          <?php if (!empty($cta_target)): ?>target="<?php echo esc_attr($cta_target); ?>"<?php endif; ?>
          <?php if ($cta_target === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
        >
          <span><?php echo esc_html($testo_bottone); ?></span>
          <span class="hero-btn-arrow" aria-hidden="true"></span>
        </a>
      <?php endif; ?>

      <?php if (!empty($telefono)): ?>
        <a class="cta-premium-phone" href="tel:<?php echo esc_attr($telefono_link); ?>">
          <span class="cta-premium-phone-icon" aria-hidden="true">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/phone.svg" alt="icona telefono" aria-hidden="true">
          </span>
          <span>
            <strong><?php echo esc_html($telefono); ?></strong>
            <?php if (!empty($orari_cta)): ?>
              <small><?php echo esc_html($orari_cta); ?></small>
            <?php endif; ?>
          </span>
        </a>
      <?php endif; ?>

    </div>

  </div>

</section>

<?php get_template_part('parti/layout_close'); ?>
