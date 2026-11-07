<?php get_template_part('parti/layout_open'); ?>

<?php
$chef_header_button = function ($link, $label = '') {
  $button = [
    'url'    => '',
    'label'  => is_scalar($label) ? trim((string) $label) : '',
    'target' => '',
    'rel'    => '',
  ];

  if (is_array($link)) {
    $button['url'] = isset($link['url']) ? trim((string) $link['url']) : '';

    if ($button['label'] === '' && !empty($link['title'])) {
      $button['label'] = trim((string) $link['title']);
    }

    $button['target'] = isset($link['target']) ? trim((string) $link['target']) : '';
  } elseif (is_scalar($link)) {
    $button['url'] = trim((string) $link);
  }

  if ($button['target'] === '_blank') {
    $button['rel'] = 'noopener noreferrer';
  }

  return $button;
};

$chef_header_primary_button   = $chef_header_button(get_sub_field('link_bottone_1'), get_sub_field('testo_bottone_1'));
$chef_header_secondary_button = $chef_header_button(get_sub_field('link_bottone_2'), get_sub_field('testo_bottone_2'));

$chef_header_has_primary_button   = $chef_header_primary_button['url'] !== '' && $chef_header_primary_button['label'] !== '';
$chef_header_has_secondary_button = $chef_header_secondary_button['url'] !== '' && $chef_header_secondary_button['label'] !== '';

$chef_header_show_phone = true;
$chef_header_show_phone_field = function_exists('get_sub_field_object') ? get_sub_field_object('mostra_telefono') : null;

if (is_array($chef_header_show_phone_field) && array_key_exists('value', $chef_header_show_phone_field)) {
  $chef_header_show_phone_value = $chef_header_show_phone_field['value'];
  $chef_header_show_phone = !in_array($chef_header_show_phone_value, [false, 0, '0'], true);
}

$chef_header_chip_rows = get_sub_field('tastini_header');
$chef_header_chips     = [];

if (is_array($chef_header_chip_rows)) {
  foreach ($chef_header_chip_rows as $chef_header_chip_row) {
    if (!is_array($chef_header_chip_row)) {
      continue;
    }

    $chef_header_chip_label_raw = $chef_header_chip_row['testo_tastino']
      ?? $chef_header_chip_row['testo']
      ?? $chef_header_chip_row['label']
      ?? $chef_header_chip_row['titolo_tastino']
      ?? '';

    $chef_header_chip_label = trim(wp_strip_all_tags((string) $chef_header_chip_label_raw));

    if ($chef_header_chip_label === '') {
      continue;
    }

    $chef_header_chip_link = $chef_header_button(
      $chef_header_chip_row['link_tastino'] ?? $chef_header_chip_row['link'] ?? '',
      ''
    );

    $chef_header_chip_icon     = $chef_header_chip_row['icona_tastino']
      ?? $chef_header_chip_row['icona']
      ?? $chef_header_chip_row['immagine_icon_tastino']
      ?? null;
    $chef_header_chip_icon_url = '';
    $chef_header_chip_icon_alt = '';

    if (is_array($chef_header_chip_icon)) {
      $chef_header_chip_icon_url = isset($chef_header_chip_icon['url']) ? trim((string) $chef_header_chip_icon['url']) : '';
      $chef_header_chip_icon_alt = isset($chef_header_chip_icon['alt']) ? trim((string) $chef_header_chip_icon['alt']) : '';
    } elseif (is_numeric($chef_header_chip_icon)) {
      if (function_exists('wp_get_attachment_image_url')) {
        $chef_header_chip_icon_id  = (int) $chef_header_chip_icon;
        $chef_header_chip_icon_url = (string) wp_get_attachment_image_url($chef_header_chip_icon_id, 'thumbnail');
        $chef_header_chip_icon_alt = (string) get_post_meta($chef_header_chip_icon_id, '_wp_attachment_image_alt', true);
      }
    } elseif (is_scalar($chef_header_chip_icon)) {
      $chef_header_chip_icon_url = trim((string) $chef_header_chip_icon);
    }

    $chef_header_chips[] = [
      'label'    => $chef_header_chip_label,
      'url'      => $chef_header_chip_link['url'],
      'target'   => $chef_header_chip_link['target'],
      'rel'      => $chef_header_chip_link['rel'],
      'icon'     => $chef_header_chip_icon,
      'icon_url' => $chef_header_chip_icon_url,
      'icon_alt' => $chef_header_chip_icon_alt,
    ];
  }
}

$chef_header_is_enhanced = $chef_header_has_primary_button || $chef_header_has_secondary_button || !empty($chef_header_chips);
?>

<header class="struttura-container struttura-2-standard centro-verticale<?php echo $chef_header_is_enhanced ? ' hero-header-enhanced' : ''; ?>">

  <div class="div-1">

    <?php if (get_sub_field('titolo_1')): ?>
      <h1><?php echo esc_html(get_sub_field('titolo_1')); ?></h1>
    <?php endif; ?>

    <?php if (get_sub_field('sottotitolo_1')): ?>
      <span class="hero-subtitle">
        <?php echo get_sub_field('sottotitolo_1'); ?>
      </span>
    <?php endif; ?>

    <?php if (get_sub_field('paragrafo')): ?>
      <div class="hero-text">
        <?php echo get_sub_field('paragrafo'); ?>
      </div>
    <?php endif; ?>

    <?php if ($chef_header_has_primary_button || $chef_header_has_secondary_button): ?>
      <div class="button-container hero-actions">
        <?php if ($chef_header_has_primary_button): ?>
          <a
            href="<?php echo esc_url($chef_header_primary_button['url']); ?>"
            class="btn btn-super hero-action-link hero-action-link-primary"
            <?php if ($chef_header_primary_button['target'] !== ''): ?>target="<?php echo esc_attr($chef_header_primary_button['target']); ?>"<?php endif; ?>
            <?php if ($chef_header_primary_button['rel'] !== ''): ?>rel="<?php echo esc_attr($chef_header_primary_button['rel']); ?>"<?php endif; ?>
          >
            <span><?php echo esc_html($chef_header_primary_button['label']); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        <?php endif; ?>

        <?php if ($chef_header_has_secondary_button): ?>
          <a
            href="<?php echo esc_url($chef_header_secondary_button['url']); ?>"
            class="btn hero-action-link hero-action-link-secondary"
            <?php if ($chef_header_secondary_button['target'] !== ''): ?>target="<?php echo esc_attr($chef_header_secondary_button['target']); ?>"<?php endif; ?>
            <?php if ($chef_header_secondary_button['rel'] !== ''): ?>rel="<?php echo esc_attr($chef_header_secondary_button['rel']); ?>"<?php endif; ?>
          >
            <span><?php echo esc_html($chef_header_secondary_button['label']); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($chef_header_show_phone): ?>
      <div class="local-container">

        <?php 
        $immagine_icon_chiama = get_sub_field('immagine_icon_chiama');
        if ($immagine_icon_chiama): ?>
          <figure class="img-icon">
            <?php
            $immagine_icon_chiama_alt = is_array($immagine_icon_chiama) && isset($immagine_icon_chiama['alt'])
              ? $immagine_icon_chiama['alt']
              : '';

            echo chefstudio_responsive_image($immagine_icon_chiama, 'thumbnail', [
              'alt'      => $immagine_icon_chiama_alt,
              'loading'  => 'lazy',
              'decoding' => 'async',
              'sizes'    => '45px',
            ]);
            ?>
          </figure>
        <?php endif; ?>

        <div>

          <?php if (get_sub_field('testo_chiama')): ?>
            <p>
              <?php echo esc_html(get_sub_field('testo_chiama')); ?>
            </p>
          <?php endif; ?>

          <span class="telefono-icon" aria-hidden="true"></span>

          <?php 
          $telefono = get_field('telefono', 'option');

          if ($telefono): 
            $telefono_link = preg_replace('/[^0-9+]/', '', $telefono);
          ?>
            <a href="tel:<?php echo esc_attr($telefono_link); ?>">
              <?php echo esc_html($telefono); ?>
            </a>
          <?php endif; ?>

        </div>

      </div>
    <?php endif; ?>

    <?php if (!empty($chef_header_chips)): ?>
      <div class="hero-chip-list">
        <?php foreach ($chef_header_chips as $chef_header_chip): ?>
          <?php if ($chef_header_chip['url'] !== ''): ?>
            <a
              class="hero-chip"
              href="<?php echo esc_url($chef_header_chip['url']); ?>"
              <?php if ($chef_header_chip['target'] !== ''): ?>target="<?php echo esc_attr($chef_header_chip['target']); ?>"<?php endif; ?>
              <?php if ($chef_header_chip['rel'] !== ''): ?>rel="<?php echo esc_attr($chef_header_chip['rel']); ?>"<?php endif; ?>
            >
              <?php if ($chef_header_chip['icon_url'] !== ''): ?>
                <?php echo chefstudio_responsive_image($chef_header_chip['icon'], 'thumbnail', [
                  'class'    => 'hero-chip-icon',
                  'alt'      => $chef_header_chip['icon_alt'],
                  'loading'  => 'lazy',
                  'decoding' => 'async',
                ]); ?>
              <?php endif; ?>
              <span class="hero-chip-label"><?php echo esc_html($chef_header_chip['label']); ?></span>
            </a>
          <?php else: ?>
            <span class="hero-chip">
              <?php if ($chef_header_chip['icon_url'] !== ''): ?>
                <?php echo chefstudio_responsive_image($chef_header_chip['icon'], 'thumbnail', [
                  'class'    => 'hero-chip-icon',
                  'alt'      => $chef_header_chip['icon_alt'],
                  'loading'  => 'lazy',
                  'decoding' => 'async',
                ]); ?>
              <?php endif; ?>
              <span class="hero-chip-label"><?php echo esc_html($chef_header_chip['label']); ?></span>
            </span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

  <div class="div-2">

  <?php $immagine = get_sub_field('immagine'); ?>

  <?php if (!empty($immagine)): ?>
    <?php
    $image_alt = is_array($immagine) && !empty($immagine['alt']) ? $immagine['alt'] : '';
    $hero_img = chefstudio_responsive_image($immagine, 'hero-responsive-desktop', [
      'class'         => 'hero-img no-lazy skip-lazy',
      'alt'           => $image_alt,
      'fetchpriority' => 'high',
      'loading'       => 'eager',
      'decoding' => false,
      'sizes'         => '(max-width: 768px) 90vw, (max-width: 1200px) 50vw, 630px',
    ]);
    ?>

    <?php if ($hero_img !== ''): ?>
      <figure class="hero-img">
        <?php echo $hero_img; ?>
      </figure>
    <?php endif; ?>

  <?php endif; ?>

</div>

</header>

<?php get_template_part('parti/layout_close'); ?>
