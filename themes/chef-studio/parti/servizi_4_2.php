<?php get_template_part('parti/layout_open'); ?>

<?php
$nome_sezione   = get_sub_field('nome_sezione');
$titolo_sezione = get_sub_field('titolo');
$sottotitolo    = get_sub_field('sottotitolo') ?: get_sub_field('testo_sottotitolo') ?: get_sub_field('intro');
$servizi        = get_sub_field('lista_servizi');
global $classi_contenuto_aperti;
$classi_contenuto_corrente = is_array($classi_contenuto_aperti) ? implode(' ', $classi_contenuto_aperti) : '';
$ha_scroll_servizi         = preg_match('/(^|\s)contenuto-scroll(\s|$)/', $classi_contenuto_corrente) === 1;

$normalizza_immagine_servizio = function ($immagine) {
  $data = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($immagine)) {
    $data['url'] = isset($immagine['url']) ? trim((string) $immagine['url']) : '';
    $data['alt'] = isset($immagine['alt']) ? trim((string) $immagine['alt']) : '';
  } elseif (is_numeric($immagine)) {
    $immagine_id = (int) $immagine;
    $data['url'] = (string) wp_get_attachment_image_url($immagine_id, 'thumbnail');
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_scalar($immagine)) {
    $data['url'] = trim((string) $immagine);
  }

  return $data;
};

$normalizza_link_servizio = function ($link) {
  $data = [
    'url'    => '',
    'target' => '',
    'title'  => '',
  ];

  if (is_array($link)) {
    $data['url']    = isset($link['url']) ? trim((string) $link['url']) : '';
    $data['target'] = isset($link['target']) ? trim((string) $link['target']) : '';
    $data['title']  = isset($link['title']) ? trim((string) $link['title']) : '';
  } elseif (is_scalar($link)) {
    $data['url'] = trim((string) $link);
  }

  return $data;
};
?>

<div class="servizi-4-2-section">

<?php if (!empty($nome_sezione)): ?>
  <div class="nome-sezione">
    <?php echo esc_html($nome_sezione); ?>
  </div>
<?php endif; ?>

<?php if (!empty($titolo_sezione) || !empty($sottotitolo)): ?>
<div class="titoletto servizi-4-2-heading">
  <?php if (!empty($titolo_sezione)): ?>
    <h2><?php echo wp_kses_post($titolo_sezione); ?></h2>
  <?php endif; ?>

  <?php if (!empty($sottotitolo)): ?>
    <div class="servizi-4-2-sottotitolo"><?php echo wp_kses_post($sottotitolo); ?></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="anteprime-4 anteprime-4-2 servizi-4-2-premium">
  <ul<?php if ($ha_scroll_servizi): ?> data-chef-proposta-scroll<?php endif; ?>>
    <?php if ($servizi): ?>
      <?php foreach ($servizi as $servizio): 
        $titolo_servizio = $servizio['titolo'] ?? '';
        $testo           = $servizio['testo'] ?? '';
        $icona           = $normalizza_immagine_servizio($servizio['icona'] ?? $servizio['icona_servizio'] ?? '');
        $link            = $normalizza_link_servizio($servizio['link'] ?? '');
        $testo_link      = $servizio['testo_link'] ?? $servizio['testo_bottone'] ?? 'Scopri il servizio';
        $ha_link         = !empty($link['url']);
      ?>
        <li class="<?php echo !empty($icona['url']) ? 'has-icon' : 'no-icon'; ?>">
          <?php if ($ha_link): ?>
            <a class="servizi-4-2-card" href="<?php echo esc_url($link['url']); ?>" <?php if (!empty($link['target'])): ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?> <?php if ($link['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>>
          <?php else: ?>
            <div class="servizi-4-2-card">
            <?php endif; ?>
            <?php if (!empty($icona['url'])): ?>
              <figure class="servizi-4-2-icon">
                <span
                  class="servizi-4-2-icon-mask"
                  style="--servizi-4-2-icon: url('<?php echo esc_url($icona['url']); ?>');"
                  role="img"
                  aria-label="<?php echo esc_attr($icona['alt'] ?: wp_strip_all_tags((string) $titolo_servizio)); ?>"
                ></span>
              </figure>
            <?php endif; ?>

            <div class="testo-anteprima">
              <?php if ($titolo_servizio): ?>
                <div class="titolo"><?php echo wp_kses_post($titolo_servizio); ?></div>
              <?php endif; ?>
              <?php if ($testo): ?>
                <div class="testo"><?php echo wp_kses_post($testo); ?></div>
              <?php endif; ?>
            </div>

            <div class="servizi-4-2-action">
              <?php if ($ha_link): ?>
                <span><?php echo esc_html($testo_link); ?></span>
                <span class="servizi-4-2-arrow" aria-hidden="true"></span>
              <?php else: ?>
                <span class="servizi-4-2-related" aria-hidden="true"></span>
              <?php endif; ?>
            </div>

          <?php if ($ha_link): ?>
            </a>
          <?php else: ?>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
  <?php if ($ha_scroll_servizi && !empty($servizi)): ?>
    <div class="chef-proposta-scroll-progress chef-proposta-scroll-progress-main servizi-4-2-scroll-progress is-scroll-intent" aria-hidden="true">
      <span data-chef-proposta-progress style="--chef-proposta-progress: 22.00%; width: 22%;"></span>
    </div>
  <?php endif; ?>
</div>

</div>

<?php get_template_part('parti/layout_close'); ?>
