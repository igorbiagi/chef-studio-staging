<?php
$theme_url = get_stylesheet_directory_uri();
$svg_base_url = $theme_url . '/img/svg/';

$cta_form_text = get_sub_field('testo');
$cta_form_before_form = get_sub_field('testo_prima_del_form');
$cta_form_id_raw = get_sub_field('gravity_form_id') ?: get_sub_field('form_id') ?: get_sub_field('id_form') ?: get_sub_field('id_modulo');

if (is_array($cta_form_id_raw)) {
    $cta_form_id_raw = $cta_form_id_raw['value'] ?? $cta_form_id_raw['ID'] ?? '';
}

$cta_form_id = absint($cta_form_id_raw);

if ($cta_form_id <= 0) {
    $cta_form_id = 1;
}

$cta_form_ajax_raw = get_sub_field('gravity_form_ajax');

if ($cta_form_ajax_raw === null || $cta_form_ajax_raw === '') {
    $cta_form_ajax_raw = get_sub_field('form_ajax');
}

if (is_array($cta_form_ajax_raw)) {
    $cta_form_ajax_raw = $cta_form_ajax_raw['value'] ?? '';
}

$cta_form_landing_ids = [5, 6, 8];
$cta_form_ajax_ids = [1, 6, 8];
$cta_form_ajax = in_array($cta_form_id, $cta_form_ajax_ids, true);

if ($cta_form_ajax_raw !== null && $cta_form_ajax_raw !== '') {
    $cta_form_ajax = is_bool($cta_form_ajax_raw)
        ? $cta_form_ajax_raw
        : in_array(strtolower(trim((string) $cta_form_ajax_raw)), ['1', 'true', 'yes', 'si', 'on'], true);
}

if (in_array($cta_form_id, [6, 8], true)) {
    $cta_form_ajax = true;
}

$cta_form_phone_label = trim(wp_strip_all_tags((string) get_sub_field('testo_chiama')));
$cta_form_phone_label = $cta_form_phone_label !== '' ? $cta_form_phone_label : 'Chiamaci:';

$telefono = get_field('telefono', 'option');
$telefono_link = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';

$normalize_image = function ($image, $size = 'medium') {
    if (empty($image)) {
        return ['url' => '', 'alt' => ''];
    }

    if (is_array($image)) {
        return [
            'url' => $image['sizes'][$size] ?? ($image['url'] ?? ''),
            'alt' => $image['alt'] ?? '',
        ];
    }

    if (is_numeric($image)) {
        return [
            'url' => wp_get_attachment_image_url((int) $image, $size) ?: '',
            'alt' => get_post_meta((int) $image, '_wp_attachment_image_alt', true) ?: '',
        ];
    }

    if (is_string($image)) {
        return ['url' => $image, 'alt' => ''];
    }

    return ['url' => '', 'alt' => ''];
};

$consultant_image_raw = get_sub_field('cta_form_immagine_referente') ?: get_sub_field('immagine_contatti');
$consultant_image = $normalize_image($consultant_image_raw, 'medium');
$consultant_name = trim((string) get_sub_field('cta_form_nome_referente'));
$consultant_role = trim((string) get_sub_field('cta_form_ruolo_referente'));

$show_benefits = get_sub_field('cta_form_mostra_punti_forza');
$show_benefits = $show_benefits === null ? true : (bool) $show_benefits;
$benefits = [];

if ($show_benefits && have_rows('cta_form_punti_forza')) {
    while (have_rows('cta_form_punti_forza')) {
        the_row();

        $benefit_text = trim((string) get_sub_field('testo'));
        $benefit_icon = $normalize_image(get_sub_field('icona'));

        if ($benefit_text === '' && empty($benefit_icon['url'])) {
            continue;
        }

        $benefits[] = [
            'text' => $benefit_text,
            'icon' => $benefit_icon,
        ];
    }
}

$trust_enabled = get_sub_field('cta_form_mostra_box_fiducia');
$trust_enabled = $trust_enabled === null ? true : (bool) $trust_enabled;
$trust_text = trim((string) get_sub_field('cta_form_box_fiducia_testo'));
$trust_text = $trust_text !== '' ? $trust_text : 'Valutazione gratuita su misura, poi scegli come procedere.';
$trust_icon = $normalize_image(get_sub_field('cta_form_box_fiducia_icona'));

if (empty($trust_icon['url'])) {
    $trust_icon = [
        'url' => $svg_base_url . 'icona-sicuro.svg',
        'alt' => 'Valutazione personalizzata',
    ];
}

$response_time_text = trim((string) get_sub_field('cta_form_testo_risposta'));
$response_time_text = $response_time_text !== '' ? $response_time_text : 'Rispondiamo normalmente entro un giorno lavorativo.';

$has_consultant_card = !empty($consultant_image['url']) || $consultant_name !== '' || $consultant_role !== '' || !empty($benefits);
$cta_form_step_label = in_array($cta_form_id, $cta_form_landing_ids, true) ? '1 di 2' : '';
?>

<div class="contenitore local-seo-contact-section chef-cta-form-section">
  <div class="contenuto-standard pad-x-standard local-seo-contact-wrap">
    <span id="form-ancora" class="local-seo-anchor" aria-hidden="true"></span>
    <span id="form-anchor" class="local-seo-anchor" aria-hidden="true"></span>

    <section class="local-seo-contact">
      <div class="local-seo-contact-copy">
        <?php if (!empty($cta_form_text)) : ?>
          <div class="local-seo-contact-text">
            <?php echo wp_kses_post($cta_form_text); ?>
          </div>
        <?php endif; ?>

        <?php if ($has_consultant_card) : ?>
          <div class="local-seo-consultant-card">
            <?php if (!empty($consultant_image['url'])) : ?>
              <figure class="local-seo-consultant-photo">
                <?php echo chefstudio_responsive_image($consultant_image_raw, 'medium', [
                  'alt'      => $consultant_image['alt'] ?: wp_strip_all_tags($consultant_name),
                  'loading'  => 'lazy',
                  'decoding' => 'async',
                  'sizes'    => '(max-width: 768px) 180px, 217px',
                ]); ?>
              </figure>
            <?php endif; ?>

            <div class="local-seo-consultant-info">
              <?php if ($consultant_name !== '') : ?>
                <h3><?php echo esc_html($consultant_name); ?></h3>
              <?php endif; ?>

              <?php if ($consultant_role !== '') : ?>
                <p><?php echo wp_kses_post($consultant_role); ?></p>
              <?php endif; ?>

              <?php if (!empty($benefits)) : ?>
                <ul class="local-seo-benefits">
                  <?php foreach ($benefits as $benefit) : ?>
                    <li<?php echo empty($benefit['icon']['url']) ? ' class="local-seo-benefit-no-icon"' : ''; ?>>
                      <?php if (!empty($benefit['icon']['url'])) : ?>
                        <img
                          src="<?php echo esc_url($benefit['icon']['url']); ?>"
                          alt="<?php echo esc_attr($benefit['icon']['alt'] ?: wp_strip_all_tags($benefit['text'])); ?>"
                          aria-hidden="true"
                        >
                      <?php endif; ?>
                      <?php if ($benefit['text'] !== '') : ?>
                        <span><?php echo wp_kses_post($benefit['text']); ?></span>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($trust_enabled && $trust_text !== '') : ?>
          <div class="local-seo-trust-note">
            <img src="<?php echo esc_url($trust_icon['url']); ?>" alt="<?php echo esc_attr($trust_icon['alt']); ?>" aria-hidden="true">
            <span><?php echo wp_kses_post($trust_text); ?></span>
          </div>
        <?php endif; ?>

        <?php if (!empty($telefono_link)) : ?>
          <div class="local-seo-phone-row">
            <a class="local-seo-whatsapp-inline-icon" href="https://wa.me/<?php echo esc_attr($telefono_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="Scrivici su WhatsApp">
              <img src="<?php echo esc_url($svg_base_url . 'whatsapp.svg'); ?>" alt="WhatsApp">
            </a>
            <span><?php echo esc_html($cta_form_phone_label); ?> <a class="local-seo-phone-link" href="tel:<?php echo esc_attr($telefono_link); ?>"><strong><?php echo esc_html($telefono); ?></strong></a></span>
          </div>
        <?php endif; ?>
      </div>

      <div class="local-seo-form-card cta-form-card cta-form-card-<?php echo esc_attr($cta_form_id); ?>">
        <?php if ($cta_form_step_label !== '') : ?>
          <span class="cta-form-step" data-cta-form-step><?php echo esc_html($cta_form_step_label); ?></span>
        <?php endif; ?>

        <?php if (!empty($cta_form_before_form)) : ?>
          <div class="local-seo-form-heading">
            <?php echo wp_kses_post($cta_form_before_form); ?>
          </div>
        <?php endif; ?>

        <div class="local-seo-form-response-time">
          <img src="<?php echo esc_url($svg_base_url . 'icona-orologio.svg'); ?>" alt="Risposta entro un giorno lavorativo" aria-hidden="true">
          <span><?php echo esc_html($response_time_text); ?></span>
        </div>

	        <div class="contatti local-seo-gravity-form cta-form-gravity-form cta-form-gravity-form-<?php echo esc_attr($cta_form_id); ?>" data-chef-cta-form-id="<?php echo esc_attr($cta_form_id); ?>">
	          <?php gravity_form($cta_form_id, false, false, false, '', $cta_form_ajax, 0); ?>
	        </div>

	        <?php if (in_array($cta_form_id, $cta_form_landing_ids, true)) : ?>
	          <style>
	            .cta-form-gravity-form-<?php echo esc_attr($cta_form_id); ?> #gform_wrapper_<?php echo esc_attr($cta_form_id); ?>{
	              display: block!important;
	            }

	            .cta-form-gravity-form-<?php echo esc_attr($cta_form_id); ?> #gform_wrapper_<?php echo esc_attr($cta_form_id); ?> form{
	              opacity: 1!important;
	            }
	          </style>
	          <script>
	            (function () {
	              var formId = <?php echo (int) $cta_form_id; ?>;
	              var gravityEventsBound = false;

		              function getPageState(root) {
		                var form = root.querySelector('#gform_' + formId);

	                if (!form) {
	                  return null;
	                }

	                var pages = Array.prototype.slice.call(form.querySelectorAll('.gform_page'));
	                var visiblePage = pages.find(function (page) {
	                  return window.getComputedStyle(page).display !== 'none';
	                });
	                var currentPage = 1;

	                if (visiblePage) {
	                  var match = visiblePage.id.match(/_(\d+)$/);
	                  currentPage = match ? parseInt(match[1], 10) : 1;
	                }

	                var pageCountNode = form.querySelector('.gf_step_page_count');
	                var totalPages = pageCountNode ? parseInt(pageCountNode.textContent, 10) : pages.length;

	                if (!totalPages || totalPages < currentPage) {
	                  totalPages = Math.max(currentPage, 2);
	                }

	                if (totalPages > 2) {
	                  totalPages = 2;
	                }

	                currentPage = Math.min(currentPage, totalPages);

	                return {
	                  currentPage: currentPage,
	                  totalPages: totalPages,
		                  percent: Math.round((currentPage / totalPages) * 100)
		                };
		              }

		              function normalizeLabelText(text) {
		                return (text || '').toLowerCase().replace(/\s+/g, ' ').trim().replace(/[’‘]/g, "'");
		              }

		              function syncChoiceLayouts(root) {
		                var fields = root.querySelectorAll('.gfield');

		                fields.forEach(function (field) {
		                  var label = field.querySelector('legend, .gfield_label, .gfield_label_before_complex');
		                  var labelText = normalizeLabelText(label ? label.textContent : '');

		                  if (labelText.indexOf("di cos'hai bisogno") !== -1 || labelText.indexOf('di cosa hai bisogno') !== -1 || labelText.indexOf('che tipo di progetto') !== -1) {
		                    field.classList.add('hero-choice-cards');
		                  }

		                  if (labelText.indexOf('problema principale') !== -1) {
		                    field.classList.add('hero-choice-pills');
		                  }

		                  if (labelText.indexOf('privacy') !== -1) {
		                    field.classList.add('hero-choice-privacy');
		                  }
		                });
		              }

		              function syncCtaForm5() {
		                var roots = document.querySelectorAll('.cta-form-gravity-form-' + formId);

	                roots.forEach(function (root) {
	                  var wrapper = root.querySelector('#gform_wrapper_' + formId);
	                  var form = root.querySelector('#gform_' + formId);
	                  var offlineCheckbox = root.querySelector('#field_' + formId + '_17 input[type="checkbox"]');
	                  var offlineLabel = root.querySelector('#field_' + formId + '_17 .gform-field-label--type-inline');
	                  var offlineFieldLabel = root.querySelector('#field_' + formId + '_17 > legend, #field_' + formId + '_17 > .gfield_label, #field_' + formId + '_17 > .gfield_label_before_complex');
	                  var projectType = root.querySelector('#field_' + formId + '_20, #field_' + formId + '_15');

	                  if (wrapper) {
	                    wrapper.style.display = 'block';
	                  }

	                  if (form) {
	                    if (form.action) {
	                      form.action = form.action.replace(/#.*$/, '');
	                    }

	                    form.style.opacity = '';
	                  }

	                  if (offlineLabel && offlineLabel.textContent.trim() === 'Il progetto non è ancora online.') {
	                    offlineLabel.textContent = 'Non ho ancora il sito online';
	                  }

	                  if (offlineFieldLabel) {
	                    offlineFieldLabel.style.display = 'none';
	                  }

		                  if (offlineCheckbox && projectType) {
		                    projectType.style.display = offlineCheckbox.checked ? '' : 'none';
		                  }

		                  syncChoiceLayouts(root);

		                  var pageState = getPageState(root);

	                  if (pageState) {
	                    var card = root.closest('.local-seo-form-card');
	                    var stepBadge = card ? card.querySelector('[data-cta-form-step]') : null;
	                    var progressBar = root.querySelector('.gf_progressbar_percentage');
	                    var stepLabel = pageState.currentPage + ' di ' + pageState.totalPages;

	                    if (stepBadge && stepBadge.textContent.trim() !== stepLabel) {
	                      stepBadge.textContent = stepLabel;
	                    }

	                    if (progressBar) {
	                      progressBar.style.width = pageState.percent + '%';
	                      progressBar.className = (progressBar.className.replace(/\bpercentbar_\d+\b/g, '').trim() + ' percentbar_' + pageState.percent).trim();
	                    }
	                  }
	                });
	              }

	              function bindGravityEvents() {
	                if (gravityEventsBound || !window.jQuery) {
	                  return;
	                }

	                gravityEventsBound = true;
	                window.jQuery(document).on('gform_post_render gform_page_loaded', syncCtaForm5);
	              }

	              document.addEventListener('change', function (event) {
	                if (event.target && event.target.matches('.cta-form-gravity-form-' + formId + ' #field_' + formId + '_17 input[type="checkbox"]')) {
	                  syncCtaForm5();
	                }
	              });

	              document.addEventListener('gform/post_render', syncCtaForm5);
	              document.addEventListener('gform_post_render', syncCtaForm5);
	              window.addEventListener('load', function () {
	                bindGravityEvents();
	                syncCtaForm5();
	              });

	              syncCtaForm5();

	              if (document.readyState === 'loading') {
	                document.addEventListener('DOMContentLoaded', function () {
	                  bindGravityEvents();
	                  syncCtaForm5();
	                });
	              } else {
	                bindGravityEvents();
	                syncCtaForm5();
	              }

	              window.setTimeout(syncCtaForm5, 300);
	              window.setTimeout(function () {
	                bindGravityEvents();
	                syncCtaForm5();
	              }, 900);
	            })();
	          </script>
	        <?php endif; ?>
	      </div>
	    </section>
	  </div>
	</div>
