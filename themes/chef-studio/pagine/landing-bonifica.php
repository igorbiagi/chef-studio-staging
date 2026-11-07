<?php
/*
Template Name: Landing bonifica sito hackerato
*/

/*
 * Landing per Google Ads "sito WordPress hackerato".
 * File autonomo: usa solo css/landing-bonifica.css, caricato soltanto su questo template.
 * Il modulo Gravity Forms va inserito nel contenuto della pagina (shortcode), che viene stampato nella card del form.
 * I testi si modificano dai campi ACF registrati in includes/landing-bonifica-campi.php.
 */

add_action('wp_enqueue_scripts', function () {
    $path = '/css/landing-bonifica.css';
    wp_enqueue_style(
        'chef-landing-bonifica',
        get_stylesheet_directory_uri() . $path,
        ['chef-custom'],
        file_exists(get_stylesheet_directory() . $path) ? filemtime(get_stylesheet_directory() . $path) : null
    );
}, 20);

$lbh_tel_label = chefstudio_lbh('lbh_telefono');
$lbh_tel_link  = (strpos(trim($lbh_tel_label), '+') === 0 ? '+' : '+39') . preg_replace('/\D+/', '', $lbh_tel_label);
$lbh_wa_link   = 'https://wa.me/' . preg_replace('/\D+/', '', chefstudio_lbh('lbh_whatsapp_numero')) . '?text=' . rawurlencode(chefstudio_lbh('lbh_whatsapp_testo'));
$lbh_wa_label  = chefstudio_lbh('lbh_bottone_wa');
$lbh_logo      = function_exists('get_field') ? get_field('logo', 'option') : null;

// Evita che l'ultima parola resti da sola a capo
$lbh_nowidow = function ($text) {
    return preg_replace('/ (\S+)$/u', '&nbsp;$1', esc_html($text));
};

$lbh_icon = 'chefstudio_lbh_icon';

get_header();
?>

<main id="main-content" class="lbh">

  <header class="lbh-bar">
    <div class="lbh-wrap lbh-bar-inner">
      <a class="lbh-brand" href="<?php echo esc_url(home_url('/')); ?>">
        <?php if (!empty($lbh_logo['url'])): ?>
          <img src="<?php echo esc_url($lbh_logo['url']); ?>" alt="<?php echo esc_attr($lbh_logo['alt'] ?: 'Chef Studio'); ?>">
        <?php else: ?>
          <span>Chef Studio</span>
        <?php endif; ?>
      </a>
      <div class="lbh-bar-actions">
        <a class="lbh-bar-tel lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo $lbh_icon('phone'); ?><span><?php echo esc_html($lbh_tel_label); ?></span></a>
        <a class="lbh-btn lbh-btn-wa lbh-btn-sm lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span>WhatsApp</span></a>
      </div>
    </div>
  </header>

  <section class="lbh-hero">
    <div class="lbh-wrap lbh-hero-grid">
      <div class="lbh-hero-copy">
        <p class="lbh-badge"><span class="lbh-dot" aria-hidden="true"></span><?php echo esc_html(chefstudio_lbh('lbh_badge')); ?></p>
        <h1><?php echo esc_html(chefstudio_lbh('lbh_h1')); ?> <span><?php echo $lbh_nowidow(chefstudio_lbh('lbh_h1_evidenza')); ?></span></h1>
        <p class="lbh-lead"><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_lead')); ?></p>

        <ul class="lbh-proof">
          <?php foreach ((array) chefstudio_lbh('lbh_proof') as $row): ?>
            <li><?php echo $lbh_icon($row['icona'] ?? 'check'); ?><span><strong><?php echo esc_html($row['forte'] ?? ''); ?></strong> <?php echo esc_html($row['testo'] ?? ''); ?></span></li>
          <?php endforeach; ?>
        </ul>

        <div class="lbh-cta-row">
          <a class="lbh-btn lbh-btn-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span><?php echo esc_html($lbh_wa_label); ?></span></a>
          <a class="lbh-btn lbh-btn-ghost lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo $lbh_icon('phone'); ?><span>Chiama <?php echo esc_html($lbh_tel_label); ?></span></a>
        </div>
        <p class="lbh-micro"><?php echo esc_html(chefstudio_lbh('lbh_micro')); ?></p>
      </div>

      <aside class="lbh-form-card" id="lbh-form">
        <p class="lbh-form-kicker"><?php echo esc_html(chefstudio_lbh('lbh_form_kicker')); ?></p>
        <h2><?php echo esc_html(chefstudio_lbh('lbh_form_titolo')); ?></h2>
        <p class="lbh-form-text"><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_form_testo')); ?></p>
        <div class="lbh-form-body">
          <?php
          if (have_posts()) {
              while (have_posts()) {
                  the_post();
                  the_content();
              }
          }
          ?>
        </div>
      </aside>
    </div>

    <div class="lbh-wrap">
      <ul class="lbh-trust">
        <?php foreach ((array) chefstudio_lbh('lbh_trust') as $i => $row): ?>
          <li>
            <?php if ($i === 0): ?><span class="lbh-stars" aria-hidden="true"><?php echo str_repeat($lbh_icon('star'), 5); ?></span><?php endif; ?>
            <strong><?php echo esc_html($row['forte'] ?? ''); ?></strong><span><?php echo esc_html($row['testo'] ?? ''); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="lbh-section">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lbh('lbh_segnali_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lbh('lbh_segnali_titolo')); ?></h2>
      <div class="lbh-signals">
        <?php foreach ((array) chefstudio_lbh('lbh_segnali') as $row): ?>
          <article class="lbh-signal">
            <span class="lbh-signal-ico"><?php echo $lbh_icon($row['icona'] ?? 'alert'); ?></span>
            <h3><?php echo esc_html($row['titolo'] ?? ''); ?></h3>
            <p><?php echo esc_html($row['testo'] ?? ''); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="lbh-urgent">
        <p><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_urgente')); ?></p>
        <a class="lbh-btn lbh-btn-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span><?php echo esc_html($lbh_wa_label); ?></span></a>
      </div>
    </div>
  </section>

  <section class="lbh-section lbh-section-dark">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lbh('lbh_metodo_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_metodo_titolo')); ?></h2>
      <ol class="lbh-steps">
        <?php foreach (array_values((array) chefstudio_lbh('lbh_metodo')) as $i => $row): ?>
          <li class="lbh-step">
            <span class="lbh-step-num"><?php echo sprintf('%02d', $i + 1); ?></span>
            <span class="lbh-step-ico"><?php echo $lbh_icon($row['icona'] ?? 'check'); ?></span>
            <h3><?php echo esc_html($row['titolo'] ?? ''); ?></h3>
            <p><?php echo esc_html($row['testo'] ?? ''); ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section class="lbh-section" id="lbh-prezzi">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lbh('lbh_prezzi_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lbh('lbh_prezzi_titolo')); ?></h2>
      <div class="lbh-prices">
        <?php foreach ((array) chefstudio_lbh('lbh_prezzi') as $row):
          $stile = $row['stile'] ?? 'normale';
          $classe = $stile === 'evidenza' ? ' lbh-price-hl' : ($stile === 'tenue' ? ' lbh-price-soft' : '');
          $prezzo = $row['prezzo'] ?? '';
          $prezzo_lungo = !preg_match('/\d/', $prezzo);
        ?>
          <article class="lbh-price<?php echo $classe; ?>">
            <h3><?php echo esc_html($row['titolo'] ?? ''); ?></h3>
            <p class="lbh-price-num<?php echo $prezzo_lungo ? ' lbh-price-num-sm' : ''; ?>"><?php echo esc_html($prezzo); ?></p>
            <p class="lbh-price-note"><?php echo esc_html($row['nota'] ?? ''); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="lbh-price-foot"><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_prezzi_nota')); ?></p>

      <div class="lbh-scope">
        <div class="lbh-scope-col">
          <h3><?php echo esc_html(chefstudio_lbh('lbh_incluso_titolo')); ?></h3>
          <ul class="lbh-list lbh-list-ok">
            <?php foreach ((array) chefstudio_lbh('lbh_incluso') as $row): ?><li><?php echo $lbh_icon('check'); ?><span><?php echo esc_html($row['testo'] ?? ''); ?></span></li><?php endforeach; ?>
          </ul>
        </div>
        <div class="lbh-scope-col">
          <h3><?php echo esc_html(chefstudio_lbh('lbh_escluso_titolo')); ?></h3>
          <ul class="lbh-list lbh-list-no">
            <?php foreach ((array) chefstudio_lbh('lbh_escluso') as $row): ?><li><?php echo $lbh_icon('x'); ?><span><?php echo esc_html($row['testo'] ?? ''); ?></span></li><?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="lbh-section lbh-section-soft">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lbh('lbh_tempi_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lbh('lbh_tempi_titolo')); ?></h2>
      <ol class="lbh-timeline">
        <?php foreach ((array) chefstudio_lbh('lbh_tempi') as $row): ?>
          <li><span class="lbh-when"><?php echo esc_html($row['quando'] ?? ''); ?></span><strong><?php echo esc_html($row['titolo'] ?? ''); ?></strong><span><?php echo esc_html($row['testo'] ?? ''); ?></span></li>
        <?php endforeach; ?>
      </ol>
      <aside class="lbh-care">
        <div>
          <p class="lbh-care-kicker"><?php echo esc_html(chefstudio_lbh('lbh_care_kicker')); ?></p>
          <p><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_care_testo')); ?></p>
        </div>
        <?php $care_link = chefstudio_lbh('lbh_care_link'); ?>
        <a class="lbh-btn lbh-btn-ghost-dark" href="<?php echo esc_url(strpos($care_link, 'http') === 0 ? $care_link : home_url($care_link)); ?>"><span><?php echo esc_html(chefstudio_lbh('lbh_care_bottone')); ?></span></a>
      </aside>
    </div>
  </section>

  <section class="lbh-section">
    <div class="lbh-wrap lbh-faq-grid">
      <div>
        <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lbh('lbh_faq_eyebrow')); ?></p>
        <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lbh('lbh_faq_titolo')); ?></h2>
      </div>
      <div class="lbh-faq">
        <?php foreach (array_values((array) chefstudio_lbh('lbh_faq')) as $i => $row): ?>
          <details<?php echo $i === 0 ? ' open' : ''; ?>>
            <summary><?php echo esc_html($row['domanda'] ?? ''); ?></summary>
            <p><?php echo chefstudio_lbh_kses($row['risposta'] ?? ''); ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="lbh-final">
    <div class="lbh-wrap lbh-final-inner">
      <h2><?php echo esc_html(chefstudio_lbh('lbh_finale_titolo')); ?></h2>
      <p><?php echo chefstudio_lbh_kses(chefstudio_lbh('lbh_finale_testo')); ?></p>
      <div class="lbh-cta-row lbh-cta-center">
        <a class="lbh-btn lbh-btn-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span><?php echo esc_html($lbh_wa_label); ?></span></a>
        <a class="lbh-btn lbh-btn-dark" href="#lbh-form"><span><?php echo esc_html(chefstudio_lbh('lbh_finale_bottone')); ?></span></a>
      </div>
      <p class="lbh-final-tel">oppure chiama il <a class="lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo esc_html($lbh_tel_label); ?></a></p>
    </div>
  </section>

  <nav class="lbh-mobile-bar" aria-label="Contatti rapidi">
    <a class="lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo $lbh_icon('phone'); ?><span>Chiama</span></a>
    <a class="lbh-mobile-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span>WhatsApp</span></a>
  </nav>

</main>

<?php chefstudio_lbh_tracciamento(); ?>

<?php get_footer(); ?>
