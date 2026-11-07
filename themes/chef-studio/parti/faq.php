<?php
$nome_sezione    = get_sub_field('nome_sezione');
$titolo_faq      = get_sub_field('titolo_faq');
$titolo_faq_testo = trim(wp_strip_all_tags((string) $titolo_faq));
$sottotitolo_faq = get_sub_field('sottotitolo_faq');
$testo_faq       = !empty($sottotitolo_faq) ? $sottotitolo_faq : get_sub_field('testo_faq');
$testo_link_faq  = get_sub_field('testo_bottone_faq');
$link_faq        = get_sub_field('link_bottone_faq');
$faq_link_url    = '';
$faq_link_target = '';

if (empty($testo_link_faq)) {
  $testo_link_faq = get_sub_field('testo_link_faq');
}

if (empty($link_faq)) {
  $link_faq = get_sub_field('link_faq');
}

if (is_array($link_faq)) {
  $faq_link_url = $link_faq['url'] ?? '';

  if (empty($testo_link_faq) && !empty($link_faq['title'])) {
    $testo_link_faq = $link_faq['title'];
  }

  $faq_link_target = $link_faq['target'] ?? '';
} elseif (is_string($link_faq)) {
  $faq_link_url = $link_faq;
}
?>

<?php get_template_part('parti/layout_open'); ?>

<section class="faq-content faq-premium faq-standalone-layout">

  <div class="faq-intro">
    <?php if (!empty($nome_sezione)) : ?>
      <div class="nome-sezione">
        <?php echo esc_html($nome_sezione); ?>
      </div>
    <?php endif; ?>

    <?php if ($titolo_faq_testo !== '') : ?>
      <h2><?php echo esc_html($titolo_faq_testo); ?></h2>
    <?php endif; ?>

    <?php if (!empty($testo_faq)) : ?>
      <div class="faq-intro-text">
        <?php echo wp_kses_post($testo_faq); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($faq_link_url) && !empty($testo_link_faq)) : ?>
      <a
        class="btn btn-super faq-intro-button"
        href="<?php echo esc_url($faq_link_url); ?>"
        <?php if (!empty($faq_link_target)) : ?>target="<?php echo esc_attr($faq_link_target); ?>"<?php endif; ?>
        <?php if ($faq_link_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
      >
        <span><?php echo esc_html($testo_link_faq); ?></span>
        <span class="hero-btn-arrow" aria-hidden="true"></span>
      </a>
    <?php endif; ?>
  </div>

  <div class="faq-list">
    <?php if (have_rows('faq')): ?>
      <?php while (have_rows('faq')): the_row();
        $idx       = get_row_index();
        $input_id  = 'faq-title-' . uniqid() . '-' . $idx;

        $domanda_raw  = get_sub_field('titolo_faq');
        $risposta_raw = get_sub_field('contenuto_faq');

        $domanda_txt  = trim(wp_strip_all_tags((string)$domanda_raw));
        $risposta_txt = trim(wp_strip_all_tags((string)$risposta_raw));
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
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/arrow-down.svg" alt="freccia giù" aria-hidden="true">
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

<?php get_template_part('parti/layout_close'); ?>
