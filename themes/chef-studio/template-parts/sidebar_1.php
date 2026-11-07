<?php
$pagina = get_page_by_path('elementi-ricorrenti');
$pagina_id = $pagina ? apply_filters('wpml_object_id', $pagina->ID, 'page', true) : 0;
$sidebar = $pagina_id ? get_field('sidebar_1', $pagina_id) : null;

$normalizza_immagine_sidebar = function ($immagine, $size = 'thumbnail') {
  $data = [
    'url' => '',
    'alt' => '',
  ];

  if (is_array($immagine)) {
    if (!empty($immagine['sizes'][$size])) {
      $data['url'] = (string) $immagine['sizes'][$size];
    } elseif (!empty($immagine['url'])) {
      $data['url'] = (string) $immagine['url'];
    }

    $data['alt'] = !empty($immagine['alt']) ? (string) $immagine['alt'] : '';
  } elseif (is_numeric($immagine)) {
    $immagine_id = (int) $immagine;
    $data['url'] = (string) wp_get_attachment_image_url($immagine_id, $size);
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_scalar($immagine)) {
    $data['url'] = trim((string) $immagine);
  }

  return $data;
};

$normalizza_link_sidebar = function ($link) {
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

$normalizza_testi_servizio_sidebar = function ($blocco) {
  $titolo = '';
  $descrizione = '';

  foreach (['titolo', 'titolo_servizio', 'nome', 'label'] as $campo_titolo) {
    if (!empty($blocco[$campo_titolo])) {
      $titolo = trim(wp_strip_all_tags((string) $blocco[$campo_titolo]));
      break;
    }
  }

  foreach (['descrizione', 'descrizione_servizio', 'sottotitolo', 'testo_breve', 'testo_descrizione'] as $campo_testo) {
    if (!empty($blocco[$campo_testo])) {
      $descrizione = (string) $blocco[$campo_testo];
      break;
    }
  }

  $testo = isset($blocco['testo']) ? (string) $blocco['testo'] : '';

  if ($testo !== '') {
    $testo_con_righe = str_ireplace(['<br />', '<br/>', '<br>'], "\n", $testo);
    $testo_pulito = trim(wp_strip_all_tags($testo_con_righe));
    $righe = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|\|/', $testo_pulito))));

    if ($titolo === '') {
      $titolo = $righe[0] ?? '';

      if ($descrizione === '' && count($righe) > 1) {
        $descrizione = implode(' ', array_slice($righe, 1));
      }
    } elseif ($descrizione === '' && $testo_pulito !== '' && $testo_pulito !== $titolo) {
      $descrizione = $testo;
    }
  }

  if ($descrizione === '' && $titolo !== '') {
    $chiave_titolo = function_exists('sanitize_title') ? sanitize_title($titolo) : strtolower(trim($titolo));
    $descrizioni_predefinite = [
      'seo'         => 'Ranking, traffico organico, strategia.',
      'web'         => 'UX/UI, responsive, sviluppo.',
      'web-design'  => 'UX/UI, responsive, sviluppo.',
      'grafica'     => 'Logo, creatività, comunicazione visiva.',
      'foto'        => 'Immagini professionali, storytelling.',
      'fotografia'  => 'Immagini professionali, storytelling.',
    ];

    if (!empty($descrizioni_predefinite[$chiave_titolo])) {
      $descrizione = $descrizioni_predefinite[$chiave_titolo];
    }
  }

  return [
    'titolo'      => $titolo,
    'descrizione' => $descrizione,
  ];
};

$normalizza_contatti_sidebar = function ($html) {
  $html_con_righe = str_ireplace(['<br />', '<br/>', '<br>'], "\n", (string) $html);
  $charset = function_exists('get_bloginfo') ? get_bloginfo('charset') : 'UTF-8';
  $testo = html_entity_decode(trim(wp_strip_all_tags($html_con_righe)), ENT_QUOTES, $charset);
  $testo = preg_replace('/[ \t]+/', ' ', $testo);

  $email = '';
  if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $testo, $email_match)) {
    $email = $email_match[0];
    $testo = str_replace($email_match[0], '', $testo);
  }

  $telefono = '';
  if (preg_match('/\+?\d[\d\s().\-]{7,}\d/', $testo, $telefono_match)) {
    $telefono = trim($telefono_match[0]);
    $testo = str_replace($telefono_match[0], '', $testo);
  }

  $intro = trim(preg_replace('/\s+/', ' ', $testo));
  $intro = trim($intro, " \t\n\r\0\x0B-|,.;");

  return [
    'intro'    => $intro,
    'email'    => $email,
    'telefono' => $telefono,
  ];
};
?>

<?php if ($sidebar): ?>
  <div class="sticky-container">
    <?php if (!empty($sidebar['elementi_piccoli_sidebar'])): ?>
      <section class="sidebar-card sidebar-servizi-card" aria-labelledby="sidebar-servizi-title">
        <h3 id="sidebar-servizi-title"><?php echo esc_html(!empty($sidebar['titolo_sidebar']) ? $sidebar['titolo_sidebar'] : 'Esplora i servizi'); ?></h3>

        <div class="sidebar-services-list">
          <?php foreach ($sidebar['elementi_piccoli_sidebar'] as $blocco): 
            $link = $normalizza_link_sidebar($blocco['link'] ?? '');
            $icona = $normalizza_immagine_sidebar($blocco['immagine'] ?? $blocco['icona'] ?? '');
            $testi = $normalizza_testi_servizio_sidebar($blocco);
            $ha_link = !empty($link['url']);
            $ha_icona = !empty($icona['url']);
          ?>
            <div class="side-articolo sidebar-service-item <?php echo $ha_link ? 'has-link' : 'no-link'; ?> <?php echo $ha_icona ? 'has-icon' : 'no-icon'; ?>">
              <?php if ($ha_link): ?>
                <a class="sidebar-service-link" href="<?php echo esc_url($link['url']); ?>" <?php if (!empty($link['target'])): ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?> <?php if ($link['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>>
              <?php else: ?>
                <div class="sidebar-service-link">
              <?php endif; ?>

                <?php if ($ha_icona): ?>
                  <figure class="sidebar-service-icon">
                    <img class="img-no-shift-sidebar-square flex" src="<?php echo esc_url($icona['url']); ?>" alt="<?php echo esc_attr($icona['alt'] ?: $testi['titolo']); ?>">
                  </figure>
                <?php endif; ?>

                <div class="sidebar-service-copy">
                  <?php if (!empty($testi['titolo'])): ?>
                    <div class="side-testo sidebar-service-title"><?php echo esc_html($testi['titolo']); ?></div>
                  <?php endif; ?>

                  <?php if (!empty($testi['descrizione'])): ?>
                    <div class="sidebar-service-description"><?php echo wp_kses_post($testi['descrizione']); ?></div>
                  <?php endif; ?>
                </div>

                <span class="sidebar-service-arrow" aria-hidden="true"></span>

              <?php if ($ha_link): ?>
                </a>
              <?php else: ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if (!empty($sidebar['elementi_grandi_sidebar'])): ?>
      <?php foreach ($sidebar['elementi_grandi_sidebar'] as $elemento):
        $link = $normalizza_link_sidebar($elemento['link'] ?? '');
        $immagine = $normalizza_immagine_sidebar($elemento['immagine'] ?? '', 'medium');
        $testo_promo = isset($elemento['testo']) ? trim(wp_strip_all_tags((string) $elemento['testo'])) : '';
        $ha_link = !empty($link['url']);
        $ha_immagine = !empty($immagine['url']);
        $ha_testo = $testo_promo !== '';

        if (!$ha_immagine && !$ha_testo) {
          continue;
        }

        $link_label = $testo_promo ?: $immagine['alt'];

        if ($link_label === '' && !empty($link['title'])) {
          $link_label = $link['title'];
        }

        if ($link_label === '' && $ha_link) {
          $link_path = wp_parse_url($link['url'], PHP_URL_PATH);
          $link_slug = is_string($link_path) ? trim(basename(untrailingslashit($link_path))) : '';
          $link_label = $link_slug !== '' ? 'Leggi l\'approfondimento ' . str_replace('-', ' ', $link_slug) : 'Leggi l\'approfondimento';
        }

        $immagine_alt = $immagine['alt'] ?: ($ha_testo ? '' : $link_label);
      ?>
        <?php if ($ha_link): ?>
          <a href="<?php echo esc_url($link['url']); ?>" <?php if (!empty($link['target'])): ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?> <?php if ($link['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>>
        <?php else: ?>
          <div>
        <?php endif; ?>
          <div class="chef-promo-sidebar">
            <div class="contenitore-img">
              <?php if ($ha_immagine): ?>
                <img class="flex img-no-shift-sidebar"
                  src="<?php echo esc_url($immagine['url']); ?>"
                  alt="<?php echo esc_attr($immagine_alt); ?>">
              <?php endif; ?>
            </div>
            <div class="ded-sfumatura">
              <?php echo esc_html($testo_promo); ?>
            </div>
            <div class="ded-sfumatura-sopra"></div>
          </div>
        <?php if ($ha_link): ?>
          </a>
        <?php else: ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($sidebar['testo_contatti'])): ?>
      <?php
      $contatti = $normalizza_contatti_sidebar($sidebar['testo_contatti']);
      $intro_contatti = !empty($contatti['intro']) ? $contatti['intro'] : 'Parlaci delle tue idee, siamo pronti a trasformarle in risultati.';
      $email_contatto = !empty($contatti['email']) ? $contatti['email'] : 'info@chefstudio.it';
      $telefono_opzione = function_exists('get_field') ? get_field('telefono', 'option') : '';
      $telefono_contatto = !empty($contatti['telefono']) ? $contatti['telefono'] : $telefono_opzione;
      $telefono_link = $telefono_contatto ? preg_replace('/[^0-9+]/', '', $telefono_contatto) : '';
      $link_contatti = $normalizza_link_sidebar($sidebar['link_contatti'] ?? '');
      ?>
      <section class="sidebar-card chef-info-contatti-side" aria-labelledby="sidebar-contatti-title">
        <h3 id="sidebar-contatti-title">Hai un progetto in mente?</h3>
        <div class="sidebar-contact-text"><?php echo esc_html($intro_contatti); ?></div>

        <div class="sidebar-contact-methods">
          <?php if (!empty($email_contatto)): ?>
            <a class="sidebar-contact-method sidebar-contact-email" href="mailto:<?php echo esc_attr($email_contatto); ?>">
              <?php echo esc_html($email_contatto); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($telefono_contatto) && !empty($telefono_link)): ?>
            <a class="sidebar-contact-method sidebar-contact-phone" href="tel:<?php echo esc_attr($telefono_link); ?>">
              <?php echo esc_html($telefono_contatto); ?>
            </a>
          <?php endif; ?>
        </div>

        <?php if (!empty($link_contatti['url']) && !empty($sidebar['testo_bottone_contatti'])): ?>
          <a class="btn btn-outline-primary sidebar-contact-button" href="<?php echo esc_url($link_contatti['url']); ?>" <?php if (!empty($link_contatti['target'])): ?>target="<?php echo esc_attr($link_contatti['target']); ?>"<?php endif; ?> <?php if ($link_contatti['target'] === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>>
            <span><?php echo esc_html($sidebar['testo_bottone_contatti']); ?></span>
            <span class="sidebar-contact-arrow" aria-hidden="true"></span>
          </a>
        <?php endif; ?>
      </section>
    <?php endif; ?>

  </div><!-- /sticky-container -->
<?php endif; ?>
