<?php get_template_part('parti/layout_open'); ?>
<?php
$titolo = get_sub_field('titolo');
$converti_icone_editor = function ($html) {
  $html = wp_kses_post($html);

  return preg_replace_callback('/<img\b[^>]*>/i', function ($matches) {
    $tag = $matches[0];

    if (!preg_match('/\bclass=(["\'])(.*?)\1/i', $tag, $class_match)) {
      return $tag;
    }

    $class_attr = html_entity_decode($class_match[2], ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    if (!preg_match('/(^|\s)(icona-primary|icona-secondary)(\s|$)/', $class_attr, $color_match)) {
      return $tag;
    }

    if (!preg_match('/\bsrc=(["\'])(.*?)\1/i', $tag, $src_match)) {
      return $tag;
    }

    $src = html_entity_decode($src_match[2], ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    if (!preg_match('/\.svg(\?|#|$)/i', $src)) {
      return $tag;
    }

    $alt = '';
    if (preg_match('/\balt=(["\'])(.*?)\1/i', $tag, $alt_match)) {
      $alt = trim(wp_strip_all_tags(html_entity_decode($alt_match[2], ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8')));
    }

    $classi_originali = preg_split('/\s+/', trim($class_attr));
    $classi_utili = array_filter($classi_originali, function ($classe) {
      return in_array($classe, ['icona-primary', 'icona-secondary', 'alignnone', 'alignleft', 'alignright', 'aligncenter'], true);
    });
    array_unshift($classi_utili, 'editor-icon-mask');
    $classi_utili = array_unique($classi_utili);

    $attributi_accessibilita = $alt !== ''
      ? ' role="img" aria-label="' . esc_attr($alt) . '"'
      : ' aria-hidden="true"';

    return '<span class="' . esc_attr(implode(' ', $classi_utili)) . '" style="--editor-icon-mask: url(\'' . esc_url($src) . '\');"' . $attributi_accessibilita . '></span>';
  }, $html);
};

if (!empty($titolo)): ?>
<div class="titoletto">
  <h2><?php echo wp_kses_post($titolo); ?></h2>
</div>
<?php endif; ?>
<?php if (have_rows('blocchi_flex')): ?>
  <div class="flex-container-3-colonne">
    <?php while (have_rows('blocchi_flex')): the_row(); 
      $colore_icona = get_sub_field('colore_icona'); // select primaria / secondaria
      $icona        = get_sub_field('icona'); // campo immagine (array)
      $testo        = get_sub_field('testo');
      $icona_alt    = !empty($icona['alt']) ? $icona['alt'] : wp_strip_all_tags((string) $testo);
    ?>
      <div>
        <?php if ($icona): ?>
          <span
            class="flex-container-3-colonne-icon-mask <?php echo esc_attr($colore_icona); ?>"
            style="--flex-container-3-colonne-icon: url('<?php echo esc_url($icona['url']); ?>');"
            role="img"
            aria-label="<?php echo esc_attr($icona_alt); ?>"
          ></span>
        <?php endif; ?>

        <?php if ($testo): ?>
          <?php echo $converti_icone_editor($testo); ?>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<?php get_template_part('parti/layout_close'); ?>
