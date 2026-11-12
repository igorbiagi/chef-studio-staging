<?php get_template_part('parti/layout_open'); ?>

<?php
// Normalizza eventuali select con Return=Array
$norm = function ($v) { return is_array($v) ? ($v['value'] ?? '') : $v; };

$modalita        = $norm(get_sub_field('tipo_visualizzazione'));
$cpt             = $norm(get_sub_field('scegli_cpt'));
$mostra_img      = get_sub_field('mostra_immagine');
$mostra_titolo   = get_sub_field('mostra_titolo');
$mostra_testo    = get_sub_field('mostra_testo');
$mostra_categoria = get_sub_field('mostra_categoria');
if ($mostra_categoria === null) {
  $mostra_categoria = get_sub_field('mostra_categorie');
}
$colonne_layout  = get_sub_field('colonne_layout');
$titolo          = get_sub_field('titolo');
$sottotitolo     = get_sub_field('sottotitolo') ?: get_sub_field('testo_introduttivo');
$nome_sezione    = get_sub_field('nome_sezione');
$disattiva_link  = get_sub_field('disattiva_link_elementi');

// Normalizza valore per sicurezza
$modalita = str_replace('_', '-', (string) $modalita);

$is_truthy = function ($value) use ($norm) {
  $value = $norm($value);

  if (is_bool($value)) {
    return $value;
  }

  return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'si', 'on'], true);
};

$mostra_categoria = $is_truthy($mostra_categoria);
$posts_scelti = ($modalita === 'manuale') ? get_sub_field('selezione_post') : null;
$anteprime_post_type = $cpt;

if (empty($anteprime_post_type) && is_array($posts_scelti) && !empty($posts_scelti)) {
  $primo_post = $posts_scelti[0];
  $anteprime_post_type = is_object($primo_post) && isset($primo_post->ID)
    ? get_post_type($primo_post->ID)
    : get_post_type($primo_post);
}

$label_categoria = function ($categoria) {
  if (is_object($categoria) && isset($categoria->name)) {
    return (string) $categoria->name;
  }

  if (is_array($categoria)) {
    if (isset($categoria['name'])) {
      return (string) $categoria['name'];
    }

    if (isset($categoria['label'])) {
      return (string) $categoria['label'];
    }

    if (isset($categoria[0])) {
      return is_object($categoria[0]) && isset($categoria[0]->name) ? (string) $categoria[0]->name : '';
    }

    return '';
  }

  return is_scalar($categoria) ? (string) $categoria : '';
};

$classe_cpt = is_string($anteprime_post_type) && $anteprime_post_type !== '' ? 'anteprime-cpt-' . sanitize_html_class($anteprime_post_type) : '';

global $classi_contenuto_aperti;
$classi_contenuto_corrente = is_array($classi_contenuto_aperti) ? implode(' ', $classi_contenuto_aperti) : '';
$ha_scroll_anteprime = preg_match('/(^|\s)contenuto-scroll(\s|$)/', $classi_contenuto_corrente) === 1;

$classi_anteprime = array_filter([
  $colonne_layout,
  'anteprime-align',
  'anteprime-sezione',
  $classe_cpt,
  $mostra_categoria ? 'anteprime-con-categoria' : '',
  $ha_scroll_anteprime ? 'anteprime-scroll-unificato' : '',
]);

$ha_elementi_anteprime = false;

?>

<?php if (!empty($nome_sezione)) : ?>
  <div class="nome-sezione">
    <?php echo esc_html($nome_sezione); ?>
  </div>
<?php endif; ?>

<?php if (!empty($titolo)) : ?>
  <div class="titoletto">
    <?php echo wp_kses_post($titolo); ?>
  </div>
<?php endif; ?>

<?php if (!empty($sottotitolo)) : ?>
  <div class="anteprime-sottotitolo">
    <?php echo wp_kses_post($sottotitolo); ?>
  </div>
<?php endif; ?>

<div class="<?php echo esc_attr(implode(' ', $classi_anteprime)); ?>">
  <ul<?php if ($ha_scroll_anteprime): ?> data-chef-proposta-scroll<?php endif; ?>>

    <?php
    // =========================
    // MODALITÀ AUTOMATICA (CPT)
    // =========================
    if ($modalita === 'automatica') {

      $numero = get_sub_field('numero_elementi');

      $anteprime_query = new WP_Query([
        'post_type'      => $cpt,
        'posts_per_page' => $numero,
        'post_status'    => 'publish',
      ]);

      if ($anteprime_query->have_posts()) :

        while ($anteprime_query->have_posts()) :
          $anteprime_query->the_post();

          $card_id = get_the_ID();
          $data = get_contenuto_card($card_id);
          $link = get_permalink($card_id);
          $ha_elementi_anteprime = true;
    ?>

      <li>
        <article>

          <?php if (!$disattiva_link): ?>
            <a href="<?php echo esc_url($link); ?>">
          <?php endif; ?>

            <?php if ($mostra_img && !empty($data['immagine'])): ?>
              <figure class="contenitore-img">
                <img src="<?php echo esc_url($data['immagine']['url']); ?>" alt="<?php echo esc_attr($data['immagine']['alt']); ?>">
              </figure>
            <?php endif; ?>

            <div class="testo-anteprima">

              <?php if ($mostra_categoria && !empty($data['categoria']['nome'])): ?>
                <div class="anteprima-categoria">
                  <?php echo esc_html($data['categoria']['nome']); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_titolo && !empty($data['titolo'])): ?>
                <div class="titolo">
                  <?php echo esc_html($data['titolo']); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_testo && !empty($data['testo'])): ?>
                <div class="testo">
                  <?php echo esc_html($data['testo']); ?>
                </div>
              <?php endif; ?>

              <div class="freccia"></div>

            </div>

          <?php if (!$disattiva_link): ?>
            </a>
          <?php endif; ?>

        </article>
      </li>

    <?php
        endwhile;

        wp_reset_postdata();

      endif;
    }

    // ====================================
    // MODALITÀ MANUALE (post selezionati)
    // ====================================
    elseif ($modalita === 'manuale') {

      if ($posts_scelti && is_array($posts_scelti)) :

        foreach ($posts_scelti as $singolo_post) :

          $post_id = is_object($singolo_post) && isset($singolo_post->ID) ? $singolo_post->ID : (int) $singolo_post;
          $data = get_contenuto_card($post_id);
          $link = get_permalink($post_id);
          $ha_elementi_anteprime = true;
    ?>

      <li>
        <article>

          <?php if (!$disattiva_link): ?>
            <a href="<?php echo esc_url($link); ?>">
          <?php endif; ?>

            <?php if ($mostra_img && !empty($data['immagine'])): ?>
              <figure class="contenitore-img">
                <img src="<?php echo esc_url($data['immagine']['url']); ?>" alt="<?php echo esc_attr($data['immagine']['alt']); ?>">
              </figure>
            <?php endif; ?>

            <div class="testo-anteprima">

              <?php if ($mostra_categoria && !empty($data['categoria']['nome'])): ?>
                <div class="anteprima-categoria">
                  <?php echo esc_html($data['categoria']['nome']); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_titolo && !empty($data['titolo'])): ?>
                <div class="titolo">
                  <?php echo esc_html($data['titolo']); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_testo && !empty($data['testo'])): ?>
                <div class="testo">
                  <?php echo esc_html($data['testo']); ?>
                </div>
              <?php endif; ?>

              <div class="freccia"></div>

            </div>

          <?php if (!$disattiva_link): ?>
            </a>
          <?php endif; ?>

        </article>
      </li>

    <?php
        endforeach;

      endif;
    }

    // ======================================
    // MODALITÀ MANUALE-REPEATER
    // ======================================
    elseif ($modalita === 'manuale-repeater') {

      $righe = get_sub_field('elementi_manuali_repeater');

      if (!empty($righe) && is_array($righe)) :

        foreach ($righe as $riga) :

          $img  = $riga['immagine'] ?? null;
          $tit  = $riga['titolo'] ?? '';
          $txt  = $riga['testo'] ?? '';
          $cat  = $riga['categoria'] ?? $riga['etichetta'] ?? '';
          $link = $riga['link'] ?? null;
          $num_ev  = trim((string) ($riga['numero'] ?? ''));
          $num_txt = trim((string) ($riga['numero_testo'] ?? ''));

          $href = $link['url'] ?? '';
          $tgt  = !empty($link['target']) ? $link['target'] : '';
          $cat_label = $label_categoria($cat);
          $ha_elementi_anteprime = true;
    ?>

      <li>
        <article>

          <?php if (!$disattiva_link && $href): ?>
  <a href="<?php echo esc_url($href); ?>"<?php echo $tgt ? ' target="' . esc_attr($tgt) . '"' : ''; ?>>
<?php endif; ?>

            <?php if ($mostra_img && !empty($img)): ?>
              <figure class="contenitore-img">
                <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
              </figure>
            <?php endif; ?>

            <div class="testo-anteprima">

              <?php if ($mostra_categoria && $cat_label !== ''): ?>
                <div class="anteprima-categoria">
                  <?php echo esc_html($cat_label); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_titolo && !empty($tit)): ?>
                <div class="titolo">
                  <?php echo esc_html($tit); ?>
                </div>
              <?php endif; ?>

              <?php if ($mostra_testo && (!empty($txt) || $num_ev !== '')): ?>
                <div class="testo">
                  <?php if ($num_ev !== ''): ?><p class="rsw-num"><?php echo esc_html($num_ev); ?><?php if ($num_txt !== ''): ?><span><?php echo esc_html($num_txt); ?></span><?php endif; ?></p><?php endif; ?>
                  <?php echo wp_kses_post($txt); ?>
                </div>
              <?php endif; ?>

              <div class="freccia"></div>

            </div>

          <?php if (!$disattiva_link && $href): ?>
            </a>
          <?php endif; ?>

        </article>
      </li>

    <?php
        endforeach;

      endif;
    }
    ?>

  </ul>

  <?php if ($ha_scroll_anteprime && $ha_elementi_anteprime): ?>
    <div class="chef-proposta-scroll-progress chef-proposta-scroll-progress-main servizi-4-2-scroll-progress anteprime-scroll-progress is-scroll-intent" aria-hidden="true">
      <span data-chef-proposta-progress style="--chef-proposta-progress: 22.00%; width: 22%;"></span>
    </div>
  <?php endif; ?>
</div>

<?php get_template_part('parti/layout_close'); ?>
