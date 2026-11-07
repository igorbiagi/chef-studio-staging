<?php
/*
Template Name: Chef Studio Care (manutenzione)
*/

/*
 * Pagina dei piani di manutenzione Chef Studio Care.
 * Usa la grafica della landing bonifica (css/landing-bonifica.css) più css/landing-care.css per le card dei piani.
 * Il modulo Gravity Forms va inserito nel contenuto della pagina (shortcode), che viene stampato nella card del form.
 * I testi si modificano dai campi ACF registrati in includes/landing-care-campi.php.
 */

add_action('wp_enqueue_scripts', function () {
    foreach (['chef-landing-bonifica' => ['/css/landing-bonifica.css', ['chef-custom']], 'chef-landing-care' => ['/css/landing-care.css', ['chef-landing-bonifica']]] as $handle => [$path, $deps]) {
        wp_enqueue_style(
            $handle,
            get_stylesheet_directory_uri() . $path,
            $deps,
            file_exists(get_stylesheet_directory() . $path) ? filemtime(get_stylesheet_directory() . $path) : null
        );
    }
}, 20);

$lbh_tel_label = chefstudio_lcare('lcare_telefono');
$lbh_tel_link  = (strpos(trim($lbh_tel_label), '+') === 0 ? '+' : '+39') . preg_replace('/\D+/', '', $lbh_tel_label);
$lbh_wa_link   = 'https://wa.me/' . preg_replace('/\D+/', '', chefstudio_lcare('lcare_whatsapp_numero')) . '?text=' . rawurlencode(chefstudio_lcare('lcare_whatsapp_testo'));
$lbh_logo      = function_exists('get_field') ? get_field('logo', 'option') : null;
$lbh_icon      = 'chefstudio_lbh_icon';

// Evita che l'ultima parola resti da sola a capo
$lbh_nowidow = function ($text) {
    return preg_replace('/ (\S+)$/u', '&nbsp;$1', esc_html($text));
};

$lcare_link = function ($link) {
    return strpos($link, 'http') === 0 ? $link : home_url($link);
};

get_header();
?>

<main id="main-content" class="lbh lcare">

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
        <p class="lbh-badge"><span class="lbh-dot lcare-dot" aria-hidden="true"></span><?php echo esc_html(chefstudio_lcare('lcare_badge')); ?></p>
        <h1><?php echo esc_html(chefstudio_lcare('lcare_h1')); ?> <span><?php echo $lbh_nowidow(chefstudio_lcare('lcare_h1_evidenza')); ?></span></h1>
        <p class="lbh-lead"><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_lead')); ?></p>

        <ul class="lbh-proof">
          <?php foreach ((array) chefstudio_lcare('lcare_proof') as $row): ?>
            <li><?php echo $lbh_icon($row['icona'] ?? 'check'); ?><span><strong><?php echo esc_html($row['forte'] ?? ''); ?></strong> <?php echo esc_html($row['testo'] ?? ''); ?></span></li>
          <?php endforeach; ?>
        </ul>

        <div class="lbh-cta-row">
          <a class="lbh-btn lcare-btn-yellow" href="#piani"><span><?php echo esc_html(chefstudio_lcare('lcare_bottone_piani')); ?></span></a>
          <a class="lbh-btn lbh-btn-ghost lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span>Scrivici su WhatsApp</span></a>
        </div>
        <p class="lbh-micro"><?php echo esc_html(chefstudio_lcare('lcare_micro')); ?></p>
      </div>

      <aside class="lbh-form-card" id="lbh-form">
        <p class="lbh-form-kicker"><?php echo esc_html(chefstudio_lcare('lcare_form_kicker')); ?></p>
        <h2><?php echo esc_html(chefstudio_lcare('lcare_form_titolo')); ?></h2>
        <p class="lbh-form-text"><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_form_testo')); ?></p>
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
        <?php foreach ((array) chefstudio_lcare('lcare_trust') as $i => $row): ?>
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
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lcare('lcare_cosa_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lcare('lcare_cosa_titolo')); ?></h2>
      <div class="lbh-signals lcare-features">
        <?php foreach ((array) chefstudio_lcare('lcare_cosa') as $row): ?>
          <article class="lbh-signal">
            <span class="lbh-signal-ico"><?php echo $lbh_icon($row['icona'] ?? 'check'); ?></span>
            <h3><?php echo esc_html($row['titolo'] ?? ''); ?></h3>
            <p><?php echo esc_html($row['testo'] ?? ''); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="lbh-section lbh-section-soft" id="piani">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lcare('lcare_piani_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lcare('lcare_piani_titolo')); ?></h2>
      <div class="lcare-plans">
        <?php foreach ((array) chefstudio_lcare('lcare_piani') as $row):
          $evidenza = !empty($row['evidenza']);
          $nome = $row['nome'] ?? '';
          $voci = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($row['voci'] ?? ''))));
        ?>
          <article class="lcare-plan<?php echo $evidenza ? ' lcare-plan-hl' : ''; ?>">
            <?php if (!empty($row['etichetta'])): ?><p class="lcare-plan-tag"><?php echo esc_html($row['etichetta']); ?></p><?php endif; ?>
            <h3><?php echo esc_html($nome); ?></h3>
            <p class="lcare-plan-who"><?php echo esc_html($row['per_chi'] ?? ''); ?></p>
            <p class="lcare-plan-price"><strong><?php echo esc_html($row['prezzo'] ?? ''); ?></strong> <span><?php echo esc_html($row['periodo'] ?? ''); ?></span></p>
            <p class="lcare-plan-eq"><?php echo esc_html($row['equivale'] ?? ''); ?></p>
            <?php if (!empty($row['risparmio'])): ?><p class="lcare-plan-save"><?php echo esc_html($row['risparmio']); ?></p><?php endif; ?>
            <p class="lcare-plan-rate"><?php echo esc_html($row['rate'] ?? ''); ?></p>
            <?php if (!empty($row['ore'])): ?><p class="lcare-plan-hours"><?php echo $lbh_icon('clock'); ?><span><?php echo esc_html($row['ore']); ?></span></p><?php endif; ?>
            <ul class="lbh-list lbh-list-ok lcare-plan-list">
              <?php foreach ($voci as $voce): ?>
                <?php if (substr($voce, -1) === ':'): ?>
                  <li class="lcare-plan-sub"><?php echo esc_html($voce); ?></li>
                <?php else: ?>
                  <li><?php echo $lbh_icon('check'); ?><span><?php echo esc_html($voce); ?></span></li>
                <?php endif; ?>
              <?php endforeach; ?>
            </ul>
            <a class="lbh-btn <?php echo $evidenza ? 'lcare-btn-yellow' : 'lbh-btn-dark'; ?> lcare-plan-btn" href="<?php echo esc_url(add_query_arg('piano', rawurlencode($nome), get_permalink()) . '#lbh-form'); ?>" data-piano="<?php echo esc_attr($nome); ?>"><span><?php echo esc_html($row['bottone'] ?? 'Scegli'); ?></span></a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="lbh-price-foot"><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_piani_nota')); ?></p>
    </div>
  </section>

  <section class="lbh-section lbh-section-dark">
    <div class="lbh-wrap">
      <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lcare('lcare_metodo_eyebrow')); ?></p>
      <h2 class="lbh-h2"><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_metodo_titolo')); ?></h2>
      <ol class="lbh-steps">
        <?php foreach (array_values((array) chefstudio_lcare('lcare_metodo')) as $i => $row): ?>
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

  <section class="lbh-section">
    <div class="lbh-wrap">
      <aside class="lbh-care lcare-alert">
        <div>
          <p class="lbh-care-kicker"><?php echo esc_html(chefstudio_lcare('lcare_bonifica_kicker')); ?></p>
          <p><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_bonifica_testo')); ?></p>
        </div>
        <a class="lbh-btn lbh-btn-ghost-dark" href="<?php echo esc_url($lcare_link(chefstudio_lcare('lcare_bonifica_link'))); ?>"><span><?php echo esc_html(chefstudio_lcare('lcare_bonifica_bottone')); ?></span></a>
      </aside>
    </div>
  </section>

  <section class="lbh-section lcare-faq-section">
    <div class="lbh-wrap lbh-faq-grid">
      <div>
        <p class="lbh-eyebrow"><?php echo esc_html(chefstudio_lcare('lcare_faq_eyebrow')); ?></p>
        <h2 class="lbh-h2"><?php echo esc_html(chefstudio_lcare('lcare_faq_titolo')); ?></h2>
      </div>
      <div class="lbh-faq">
        <?php foreach (array_values((array) chefstudio_lcare('lcare_faq')) as $i => $row): ?>
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
      <h2><?php echo esc_html(chefstudio_lcare('lcare_finale_titolo')); ?></h2>
      <p><?php echo chefstudio_lbh_kses(chefstudio_lcare('lcare_finale_testo')); ?></p>
      <div class="lbh-cta-row lbh-cta-center">
        <a class="lbh-btn lbh-btn-dark" href="#lbh-form"><span><?php echo esc_html(chefstudio_lcare('lcare_finale_bottone')); ?></span></a>
        <a class="lbh-btn lbh-btn-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span>Scrivici su WhatsApp</span></a>
      </div>
      <p class="lbh-final-tel">oppure chiama il <a class="lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo esc_html($lbh_tel_label); ?></a></p>
    </div>
  </section>

  <nav class="lbh-mobile-bar" aria-label="Contatti rapidi">
    <a class="lbh-track-tel" href="tel:<?php echo esc_attr($lbh_tel_link); ?>"><?php echo $lbh_icon('phone'); ?><span>Chiama</span></a>
    <a class="lbh-mobile-wa lbh-track-wa" href="<?php echo esc_url($lbh_wa_link); ?>" target="_blank" rel="noopener"><?php echo $lbh_icon('whatsapp'); ?><span>WhatsApp</span></a>
  </nav>

</main>

<script>
// I bottoni dei piani scelgono il piano nel modulo senza ricaricare la pagina
document.querySelectorAll('.lcare [data-piano]').forEach(function (btn) {
  btn.addEventListener('click', function (e) {
    var form = document.getElementById('lbh-form');
    var select = form && Array.prototype.find.call(form.querySelectorAll('select'), function (s) {
      return Array.prototype.some.call(s.options, function (o) { return o.value === btn.dataset.piano; });
    });
    if (!select) return;
    e.preventDefault();
    select.value = btn.dataset.piano;
    select.dispatchEvent(new Event('change', { bubbles: true }));
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});
</script>

<?php chefstudio_lbh_tracciamento(); ?>

<?php get_footer(); ?>
