<?php get_template_part('parti/layout_open'); ?>
<?php
$classe_posizione = get_sub_field('posizione_bottone');
$classe_bottone = get_sub_field('stile_bottone');
$link = get_sub_field('link_bottone');

if ($link):
  $url = $link['url'];
  $target = $link['target'] ? $link['target'] : '_self';
  $testo_bottone = get_sub_field('testo_bottone');
?>
  <div class="<?php echo esc_attr($classe_posizione); ?>">
    <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?> class="btn <?php echo esc_attr($classe_bottone); ?>">
      <?php echo esc_html($testo_bottone); ?>
    </a>
  </div>
<?php endif; ?>

<?php get_template_part('parti/layout_close'); ?>
