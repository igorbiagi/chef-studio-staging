<?php get_template_part('parti/layout_open'); ?>

<?php
$primo_sottocampo = function (array $nomi_campo) {
  foreach ($nomi_campo as $nome_campo) {
    $valore = get_sub_field($nome_campo);

    if (!empty($valore)) {
      return $valore;
    }
  }

  return null;
};

$nome_sezione = get_sub_field('nome_sezione') ?: 'Come possiamo aiutarti';
$titolo       = get_sub_field('titolo') ?: 'Scegli il risultato.<br>Noi costruiamo il percorso.';
$testo_intro  = $primo_sottocampo(['testo_introduttivo', 'sottotitolo', 'intro']);
$testo_sottocampo = function ($valore, $fallback = '') {
  return is_scalar($valore) && trim((string) $valore) !== '' ? trim((string) $valore) : $fallback;
};
$titolo_percorsi_principali_mobile = $testo_sottocampo($primo_sottocampo(['titolo_percorsi_principali_mobile', 'titolo_percorsi_principali', 'heading_percorsi_principali_mobile', 'label_percorsi_principali_mobile']), 'Percorsi principali');
$titolo_altri_percorsi_mobile = $testo_sottocampo($primo_sottocampo(['titolo_altri_percorsi_mobile', 'titolo_altri_percorsi', 'heading_altri_percorsi_mobile', 'label_altri_percorsi_mobile']), 'Altri percorsi: grafica e fotografia');
$titolo_punti_forza_mobile = $testo_sottocampo($primo_sottocampo(['titolo_punti_forza_mobile', 'titolo_perche_sceglierci_mobile', 'titolo_vantaggi_mobile', 'heading_punti_forza_mobile']), 'Perché sceglierci');
$titolo_percorsi_principali_desktop = $testo_sottocampo($primo_sottocampo(['titolo_percorsi_principali_desktop', 'titolo_percorsi_principali', 'heading_percorsi_principali_desktop', 'label_percorsi_principali_desktop']), $titolo_percorsi_principali_mobile);
$titolo_altri_percorsi_desktop = $testo_sottocampo($primo_sottocampo(['titolo_altri_percorsi_desktop', 'titolo_altri_percorsi', 'heading_altri_percorsi_desktop', 'label_altri_percorsi_desktop']), $titolo_altri_percorsi_mobile);
$titolo_punti_forza_desktop = $testo_sottocampo($primo_sottocampo(['titolo_punti_forza_desktop', 'titolo_punti_forza', 'titolo_perche_sceglierci_desktop', 'titolo_perche_sceglierci', 'titolo_vantaggi_desktop', 'titolo_vantaggi', 'heading_punti_forza_desktop']), $titolo_punti_forza_mobile);
$nomi_campi_servizi = ['servizi', 'lista_servizi', 'card_servizi', 'servizi_card', 'cards_servizi', 'schede_servizi', 'voci_servizi', 'elenco_servizi', 'servizi_principali', 'servizi_home', 'servizi_aziendali', 'servizi_proposta', 'servizi_proposta_aziendale', 'cards_proposta', 'card_proposta', 'proposta_servizi', 'lista_card', 'lista_anteprime', 'anteprime_servizi', 'obiettivi', 'risultati', 'soluzioni', 'cards', 'card', 'schede', 'voci', 'elementi'];
$servizi      = $primo_sottocampo($nomi_campi_servizi);
$punti_forza  = $primo_sottocampo(['punti_di_forza', 'punti_forza', 'lista_punti_forza', 'vantaggi', 'elementi_di_supporto']);
$punti_forza_mobile = $primo_sottocampo(['punti_di_forza_mobile', 'punti_forza_mobile', 'lista_punti_forza_mobile', 'vantaggi_mobile']);

$base_url = trailingslashit(get_stylesheet_directory_uri());
$layout_row = function_exists('get_row') ? get_row(true) : [];
$layout_row = is_array($layout_row) ? $layout_row : [];

if (function_exists('get_field') && function_exists('get_row_index')) {
  $indice_riga_corrente = (int) get_row_index();
  $righe_layout_pagina = $indice_riga_corrente > 0 ? get_field('layout_flessibile_1', get_queried_object_id()) : [];

  if (is_array($righe_layout_pagina) && !empty($righe_layout_pagina[$indice_riga_corrente - 1]) && is_array($righe_layout_pagina[$indice_riga_corrente - 1])) {
    $layout_row = array_merge($righe_layout_pagina[$indice_riga_corrente - 1], $layout_row);
  }
}

$normalizza_immagine = function ($immagine, $fallback_url = '') {
  $data = [
    'url' => $fallback_url,
    'alt' => '',
  ];

  if (is_array($immagine)) {
    $immagine_id = 0;

    if (!empty($immagine['ID'])) {
      $immagine_id = (int) $immagine['ID'];
    } elseif (!empty($immagine['id'])) {
      $immagine_id = (int) $immagine['id'];
    }

    if (!empty($immagine['url'])) {
      $data['url'] = trim((string) $immagine['url']);
    } elseif (!empty($immagine['src'])) {
      $data['url'] = trim((string) $immagine['src']);
    } elseif (!empty($immagine['guid'])) {
      $data['url'] = trim((string) $immagine['guid']);
    } elseif (!empty($immagine['image']) && is_array($immagine['image']) && !empty($immagine['image']['url'])) {
      $data['url'] = trim((string) $immagine['image']['url']);
      $data['alt'] = !empty($immagine['image']['alt']) ? trim((string) $immagine['image']['alt']) : $data['alt'];
    } elseif (!empty($immagine['immagine']) && is_array($immagine['immagine']) && !empty($immagine['immagine']['url'])) {
      $data['url'] = trim((string) $immagine['immagine']['url']);
      $data['alt'] = !empty($immagine['immagine']['alt']) ? trim((string) $immagine['immagine']['alt']) : $data['alt'];
    } elseif (!empty($immagine['sizes']['full'])) {
      $data['url'] = trim((string) $immagine['sizes']['full']);
    } elseif (!empty($immagine['sizes']['medium'])) {
      $data['url'] = trim((string) $immagine['sizes']['medium']);
    } elseif (!empty($immagine['sizes']['thumbnail'])) {
      $data['url'] = trim((string) $immagine['sizes']['thumbnail']);
    } elseif ($immagine_id > 0) {
      $url = wp_get_attachment_image_url($immagine_id, 'thumbnail') ?: wp_get_attachment_url($immagine_id);
      $data['url'] = !empty($url) ? (string) $url : $data['url'];
    }

    if (!empty($immagine['alt'])) {
      $data['alt'] = trim((string) $immagine['alt']);
    } elseif (!empty($immagine['title'])) {
      $data['alt'] = trim((string) $immagine['title']);
    } elseif ($immagine_id > 0) {
      $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
    }
  } elseif (is_object($immagine) && isset($immagine->ID)) {
    $immagine_id = (int) $immagine->ID;
    $url = wp_get_attachment_image_url($immagine_id, 'thumbnail') ?: wp_get_attachment_url($immagine_id);
    $data['url'] = !empty($url) ? (string) $url : $data['url'];
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_object($immagine) && !empty($immagine->url)) {
    $data['url'] = trim((string) $immagine->url);
    $data['alt'] = !empty($immagine->alt) ? trim((string) $immagine->alt) : '';
  } elseif (is_numeric($immagine)) {
    $immagine_id = (int) $immagine;
    $url = wp_get_attachment_image_url($immagine_id, 'thumbnail') ?: wp_get_attachment_url($immagine_id);
    $data['url'] = !empty($url) ? (string) $url : $data['url'];
    $data['alt'] = (string) get_post_meta($immagine_id, '_wp_attachment_image_alt', true);
  } elseif (is_scalar($immagine) && trim((string) $immagine) !== '') {
    $data['url'] = trim((string) $immagine);
  }

  return $data;
};

$normalizza_link = function ($link) {
  $data = [
    'url'    => '',
    'target' => '',
    'title'  => '',
  ];

  if (is_array($link)) {
    if (!empty($link['url'])) {
      $data['url'] = trim((string) $link['url']);
    } elseif (!empty($link['href'])) {
      $data['url'] = trim((string) $link['href']);
    } elseif (!empty($link['link']) && is_array($link['link']) && !empty($link['link']['url'])) {
      $data['url'] = trim((string) $link['link']['url']);
      $data['target'] = isset($link['link']['target']) ? trim((string) $link['link']['target']) : '';
      $data['title'] = isset($link['link']['title']) ? trim((string) $link['link']['title']) : '';
    } elseif (!empty($link['link']) && is_scalar($link['link'])) {
      $data['url'] = trim((string) $link['link']);
    } elseif (!empty($link['permalink'])) {
      $data['url'] = trim((string) $link['permalink']);
    } elseif (!empty($link['ID'])) {
      $url = get_permalink((int) $link['ID']);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['id'])) {
      $url = get_permalink((int) $link['id']);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['post_object']) && is_object($link['post_object']) && isset($link['post_object']->ID)) {
      $url = get_permalink((int) $link['post_object']->ID);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['post_object']) && is_numeric($link['post_object'])) {
      $url = get_permalink((int) $link['post_object']);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['pagina']) && is_object($link['pagina']) && isset($link['pagina']->ID)) {
      $url = get_permalink((int) $link['pagina']->ID);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['pagina']) && is_numeric($link['pagina'])) {
      $url = get_permalink((int) $link['pagina']);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['page']) && is_object($link['page']) && isset($link['page']->ID)) {
      $url = get_permalink((int) $link['page']->ID);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (!empty($link['page']) && is_numeric($link['page'])) {
      $url = get_permalink((int) $link['page']);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (isset($link[0]) && is_array($link[0]) && !empty($link[0]['url'])) {
      $data['url'] = trim((string) $link[0]['url']);
      $data['target'] = isset($link[0]['target']) ? trim((string) $link[0]['target']) : '';
      $data['title'] = isset($link[0]['title']) ? trim((string) $link[0]['title']) : '';
    } elseif (isset($link[0]) && is_object($link[0]) && isset($link[0]->ID)) {
      $url = get_permalink((int) $link[0]->ID);
      $data['url'] = !empty($url) ? (string) $url : '';
    } elseif (isset($link[0]) && is_scalar($link[0])) {
      $data['url'] = trim((string) $link[0]);
    }

    $data['target'] = isset($link['target']) ? trim((string) $link['target']) : $data['target'];
    $data['title']  = isset($link['title']) ? trim((string) $link['title']) : $data['title'];
  } elseif (is_object($link) && isset($link->ID)) {
    $url = get_permalink((int) $link->ID);
    $data['url'] = !empty($url) ? (string) $url : '';
    $data['title'] = !empty($link->post_title) ? trim((string) $link->post_title) : '';
  } elseif (is_numeric($link)) {
    $url = get_permalink((int) $link);
    $data['url'] = !empty($url) ? (string) $url : '';
  } elseif (is_scalar($link)) {
    $url = trim((string) $link);
    $sembra_url = stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0 || strpos($url, '/') === 0 || strpos($url, '#') === 0 || stripos($url, 'mailto:') === 0 || stripos($url, 'tel:') === 0 || (strpos($url, ' ') === false && (strpos($url, '.') !== false || strpos($url, '/') !== false));
    $data['url'] = $sembra_url ? $url : '';
  }

  return $data;
};

$normalizza_testo_inline = function ($testo) {
  if (is_array($testo)) {
    foreach (['title', 'titolo', 'label', 'name', 'nome', 'value'] as $chiave) {
      if (!empty($testo[$chiave]) && is_scalar($testo[$chiave])) {
        return trim((string) $testo[$chiave]);
      }
    }

    return '';
  }

  if (is_object($testo)) {
    if (!empty($testo->post_title)) {
      return trim((string) $testo->post_title);
    }

    if (!empty($testo->name)) {
      return trim((string) $testo->name);
    }

    return '';
  }

  return is_scalar($testo) ? trim((string) $testo) : '';
};

$normalizza_lista = function ($valore, array $chiavi_nestate = []) {
  if (empty($valore) || !is_array($valore)) {
    return [];
  }

  $chiavi = array_keys($valore);
  $is_lista = $chiavi === range(0, count($valore) - 1);

  if ($is_lista) {
    return $valore;
  }

  foreach ($chiavi_nestate as $chiave) {
    if (!empty($valore[$chiave]) && is_array($valore[$chiave])) {
      $chiavi_interne = array_keys($valore[$chiave]);

      if ($chiavi_interne === range(0, count($valore[$chiave]) - 1)) {
        return $valore[$chiave];
      }
    }
  }

  return [];
};

$trova_valore_shallow = function ($valore, array $chiavi_esatte, array $parti_chiave, array $chiavi_da_saltare = []) {
  if (is_object($valore)) {
    $valore = get_object_vars($valore);
  }

  if (!is_array($valore)) {
    return null;
  }

  foreach ($chiavi_esatte as $chiave_esatta) {
    if (!empty($valore[$chiave_esatta])) {
      return $valore[$chiave_esatta];
    }
  }

  foreach ($valore as $chiave => $dato) {
    if (!is_string($chiave) || empty($dato)) {
      continue;
    }

    $chiave_norm = strtolower($chiave);
    $salta = false;

    foreach ($chiavi_da_saltare as $parte_da_saltare) {
      if (strpos($chiave_norm, $parte_da_saltare) !== false) {
        $salta = true;
        break;
      }
    }

    if ($salta) {
      continue;
    }

    foreach ($parti_chiave as $parte_chiave) {
      if (strpos($chiave_norm, $parte_chiave) !== false) {
        return $dato;
      }
    }
  }

  foreach ($valore as $chiave => $dato) {
    if (is_string($chiave)) {
      $chiave_norm = strtolower($chiave);
      $salta = false;

      foreach ($chiavi_da_saltare as $parte_da_saltare) {
        if (strpos($chiave_norm, $parte_da_saltare) !== false) {
          $salta = true;
          break;
        }
      }

      if ($salta) {
        continue;
      }
    }

    if (is_object($dato)) {
      $dato = get_object_vars($dato);
    }

    if (!is_array($dato)) {
      continue;
    }

    foreach ($chiavi_esatte as $chiave_esatta) {
      if (!empty($dato[$chiave_esatta])) {
        return $dato[$chiave_esatta];
      }
    }
  }

  return null;
};

$trova_icona_servizio = function ($servizio) use ($trova_valore_shallow) {
  return $trova_valore_shallow(
    $servizio,
    ['icona', 'icona_card', 'icona_servizio', 'icona_servizi', 'immagine_icona', 'icon', 'icon_card', 'immagine', 'immagine_card', 'svg', 'icona_svg'],
    ['icona', 'icon', 'immagine', 'image', 'svg'],
    ['link', 'pagina', 'url', 'cta', 'bottone', 'collegamento']
  );
};

$trova_icona_punto_forza = function ($punto) use ($trova_valore_shallow) {
  return $trova_valore_shallow(
    $punto,
    ['icona', 'icona_punto', 'icona_benefit', 'icona_punti_forza', 'immagine_icona', 'immagine', 'immagine_punto', 'immagine_benefit', 'icon', 'icon_punto', 'icon_benefit', 'svg', 'icona_svg'],
    ['icona', 'icon', 'immagine', 'image', 'svg'],
    ['link', 'pagina', 'url', 'cta', 'bottone', 'collegamento']
  );
};

$trova_link_servizio = function ($servizio) use ($trova_valore_shallow) {
  return $trova_valore_shallow(
    $servizio,
    ['link', 'link_servizio', 'link_card', 'link_card_servizio', 'link_servizi', 'link_alla_pagina', 'link_pagina', 'pagina', 'pagina_servizio', 'pagina_card', 'pagina_destinazione', 'url', 'url_servizio', 'url_card', 'collegamento', 'destinazione', 'cta_link', 'link_cta', 'bottone_link'],
    ['link', 'pagina', 'url', 'collegamento', 'destinazione', 'cta', 'bottone'],
    ['icona', 'icon', 'immagine', 'image', 'svg', 'foto']
  );
};

$trova_titolo_servizio = function ($servizio) use ($trova_valore_shallow) {
  return $trova_valore_shallow(
    $servizio,
    ['titolo', 'titolo_servizio', 'titolo_card', 'nome', 'nome_servizio', 'servizio', 'headline'],
    ['titolo', 'nome', 'headline'],
    ['link', 'pagina', 'url', 'cta', 'bottone', 'icona', 'icon', 'immagine', 'image', 'svg']
  );
};

$trova_testo_servizio = function ($servizio) use ($trova_valore_shallow) {
  return $trova_valore_shallow(
    $servizio,
    ['testo', 'descrizione', 'testo_servizio', 'descrizione_servizio', 'testo_card', 'descrizione_card', 'sottotitolo', 'copy'],
    ['testo', 'descrizione', 'sottotitolo', 'copy'],
    ['link', 'pagina', 'url', 'cta', 'bottone', 'icona', 'icon', 'immagine', 'image', 'svg']
  );
};

$punteggio_lista_servizi = function ($lista, $nome_campo = '') use ($trova_icona_servizio, $trova_link_servizio) {
  if (empty($lista) || !is_array($lista)) {
    return 0;
  }

  $chiavi = array_keys($lista);
  if ($chiavi !== range(0, count($lista) - 1)) {
    return 0;
  }

  $nome_norm = strtolower((string) $nome_campo);
  $punteggio = 0;

  foreach (['servizi', 'servizio', 'card', 'schede', 'voci', 'elementi', 'anteprime'] as $indizio_nome) {
    if (strpos($nome_norm, $indizio_nome) !== false) {
      $punteggio += 2;
    }
  }

  foreach (['punti', 'forza', 'vantaggi', 'benefit'] as $indizio_negativo) {
    if (strpos($nome_norm, $indizio_negativo) !== false) {
      $punteggio -= 50;
    }
  }

  foreach (array_slice($lista, 0, 5) as $riga) {
    if (is_object($riga)) {
      $riga = get_object_vars($riga);
    }

    if (!is_array($riga)) {
      continue;
    }

    if (!empty($riga['titolo']) || !empty($riga['titolo_servizio']) || !empty($riga['nome']) || !empty($riga['nome_servizio'])) {
      $punteggio += 2;
    }

    if (!empty($riga['testo']) || !empty($riga['descrizione']) || !empty($riga['testo_servizio'])) {
      $punteggio += 1;
    }

    if (!empty($trova_icona_servizio($riga))) {
      $punteggio += 3;
    }

    if (!empty($trova_link_servizio($riga))) {
      $punteggio += 4;
    }
  }

  return $punteggio;
};

$trova_liste_numeriche = function ($origine) {
  $liste = [];
  $stack = [
    [
      'percorso' => 'layout',
      'valore'   => $origine,
      'livello'  => 0,
    ],
  ];

  while (!empty($stack)) {
    $corrente = array_pop($stack);
    $valore = $corrente['valore'];

    if (!is_array($valore) || $corrente['livello'] > 4 || empty($valore)) {
      continue;
    }

    $chiavi = array_keys($valore);
    $is_lista = $chiavi === range(0, count($valore) - 1);
    $prima_voce = $is_lista ? reset($valore) : null;

    if ($is_lista && (is_array($prima_voce) || is_object($prima_voce))) {
      $liste[$corrente['percorso']] = $valore;
      continue;
    }

    foreach ($valore as $chiave => $dato) {
      if (!is_array($dato)) {
        continue;
      }

      $nome_chiave = is_string($chiave) ? $chiave : (string) $chiave;
      $stack[] = [
        'percorso' => $corrente['percorso'] . '.' . $nome_chiave,
        'valore'   => $dato,
        'livello'  => $corrente['livello'] + 1,
      ];
    }
  }

  return $liste;
};

$trova_lista_servizi_layout = function ($riga_layout) use ($punteggio_lista_servizi, $trova_liste_numeriche) {
  if (empty($riga_layout) || !is_array($riga_layout)) {
    return [
      'lista'     => [],
      'percorso'  => '',
      'punteggio' => 0,
    ];
  }

  $migliore_lista = [];
  $migliore_punteggio = 0;
  $migliore_percorso = '';

  foreach ($trova_liste_numeriche($riga_layout) as $percorso => $valore) {
    $punteggio = $punteggio_lista_servizi($valore, (string) $percorso);

    if ($punteggio > $migliore_punteggio) {
      $migliore_punteggio = $punteggio;
      $migliore_lista = $valore;
      $migliore_percorso = (string) $percorso;
    }
  }

  return [
    'lista'     => $migliore_punteggio >= 5 ? $migliore_lista : [],
    'percorso'  => $migliore_punteggio >= 5 ? $migliore_percorso : '',
    'punteggio' => $migliore_punteggio,
  ];
};

$servizio_da_testo = function ($titolo_card, $indice, $totale) {
  $testo = strtolower(wp_strip_all_tags((string) $titolo_card));

  if (strpos($testo, 'seo') !== false || strpos($testo, 'trovare') !== false) {
    return 'seo';
  }

  if (strpos($testo, 'web') !== false || strpos($testo, 'sito') !== false || strpos($testo, 'scegliere') !== false) {
    return 'web';
  }

  if (strpos($testo, 'commerce') !== false || strpos($testo, 'vendere') !== false || strpos($testo, 'online') !== false) {
    return 'ecommerce';
  }

  if (strpos($testo, 'grafic') !== false || strpos($testo, 'comunic') !== false || strpos($testo, 'valore') !== false) {
    return 'grafica';
  }

  if (strpos($testo, 'foto') !== false || strpos($testo, 'credibil') !== false) {
    return 'foto';
  }

  $mappa_quattro = [1 => 'seo', 2 => 'web', 3 => 'grafica', 4 => 'foto'];
  $mappa_cinque  = [1 => 'seo', 2 => 'web', 3 => 'ecommerce', 4 => 'grafica', 5 => 'foto'];
  $mappa = $totale >= 5 ? $mappa_cinque : $mappa_quattro;

  return $mappa[$indice] ?? 'generico';
};

$normalizza_servizio_key = function ($servizio) {
  if (is_array($servizio)) {
    $servizio = $servizio['value'] ?? $servizio['label'] ?? '';
  }

  $servizio = strtolower(trim((string) $servizio));

  if (strpos($servizio, 'seo') !== false) {
    return 'seo';
  }

  if (strpos($servizio, 'web') !== false || strpos($servizio, 'sito') !== false) {
    return 'web';
  }

  if (strpos($servizio, 'commerce') !== false) {
    return 'ecommerce';
  }

  if (strpos($servizio, 'grafic') !== false) {
    return 'grafica';
  }

  if (strpos($servizio, 'foto') !== false) {
    return 'foto';
  }

  return sanitize_html_class($servizio ?: 'generico');
};

$testo_cta = function ($servizio, $titolo_card = '', $testo_link = '') {
  if (!empty($testo_link)) {
    return $testo_link;
  }

  $fallback = [
    'seo'       => 'Scopri la SEO',
    'web'       => 'Scopri i siti web',
    'ecommerce' => "Scopri l'e-commerce",
    'grafica'   => 'Scopri la grafica',
    'foto'      => 'Scopri la fotografia',
  ];

  if (isset($fallback[$servizio])) {
    return $fallback[$servizio];
  }

  $titolo_card = trim(wp_strip_all_tags((string) $titolo_card));

  return $titolo_card !== '' ? 'Scopri ' . $titolo_card : 'Scopri di più';
};

if (empty($testo_intro)) {
  $testo_intro = '<p>Ogni attività ha un obiettivo diverso: più visibilità, più contatti, più vendite. Ti aiutiamo a scegliere la strategia giusta.</p>';
}

$servizi = $normalizza_lista($servizi, ['servizi', 'lista_servizi', 'card_servizi', 'lista_card']);
$servizi_da_layout_info = $trova_lista_servizi_layout($layout_row);
$servizi_da_layout = $servizi_da_layout_info['lista'];
$punteggio_servizi_campo = $punteggio_lista_servizi($servizi, 'campo_scelto');
$punteggio_servizi_layout = (int) $servizi_da_layout_info['punteggio'];
$debug_liste_candidate = [];

foreach ($trova_liste_numeriche($layout_row) as $percorso_lista => $lista_candidata) {
  $debug_liste_candidate[] = $percorso_lista . ':' . $punteggio_lista_servizi($lista_candidata, $percorso_lista) . ':' . count($lista_candidata);

  if (count($debug_liste_candidate) >= 8) {
    break;
  }
}

if (!empty($servizi_da_layout) && $punteggio_servizi_layout > $punteggio_servizi_campo) {
  $servizi = $servizi_da_layout;
}

$punti_forza = $normalizza_lista($punti_forza, ['punti_di_forza', 'punti_forza', 'lista_punti_forza', 'vantaggi']);
$punti_forza_mobile = $normalizza_lista($punti_forza_mobile, ['punti_di_forza_mobile', 'punti_forza_mobile', 'lista_punti_forza_mobile', 'vantaggi_mobile']);
$punti_forza_mobile_personalizzati = !empty($punti_forza_mobile);
$servizi_fallback_attivo = false;

if (empty($servizi)) {
  $servizi_fallback_attivo = true;
  $servizi = [
    [
      'titolo'     => 'Farti trovare',
      'testo'      => 'Intercetta chi sta già cercando prodotti o servizi come i tuoi.',
      'icona'      => $base_url . 'img/svg/search.svg',
      'testo_link' => 'Scopri la SEO',
    ],
    [
      'titolo'     => 'Farti scegliere',
      'testo'      => 'Un sito chiaro, veloce e credibile che trasforma visite in contatti.',
      'icona'      => $base_url . 'img/svg/earth.svg',
      'testo_link' => 'Scopri i siti web',
    ],
    [
      'titolo'     => 'Vendere online',
      'testo'      => 'E-commerce progettati per aumentare ordini, fiducia e fatturato.',
      'icona'      => $base_url . 'img/svg/cart.svg',
      'testo_link' => "Scopri l'e-commerce",
    ],
    [
      'titolo'     => 'Comunicare valore',
      'testo'      => 'Identità visiva e materiali grafici che rendono il brand riconoscibile.',
      'icona'      => $base_url . 'img/svg/share.svg',
      'testo_link' => 'Scopri la grafica',
    ],
    [
      'titolo'     => 'Dare credibilità',
      'testo'      => 'Foto professionali che aumentano fiducia verso prodotti, persone e ambienti.',
      'icona'      => $base_url . 'img/svg/stelline.svg',
      'testo_link' => 'Scopri la fotografia',
    ],
  ];
}

if (empty($punti_forza)) {
  $punti_forza = [
    [
      'titolo' => 'Strategie basate sui dati',
      'testo'  => 'Decisioni misurabili, non ipotesi.',
      'icona'  => $base_url . 'img/svg/filters.svg',
    ],
    [
      'titolo' => 'SEO, siti ed e-commerce integrati',
      'testo'  => 'Un unico metodo per aumentare visibilità, contatti e vendite.',
      'icona'  => $base_url . 'img/svg/cart.svg',
    ],
    [
      'titolo' => 'WordPress custom, non temi pronti',
      'testo'  => 'Siti su misura, più leggeri, gestibili e progettati per crescere.',
      'icona'  => $base_url . 'img/svg/check.svg',
    ],
    [
      'titolo' => 'Un referente diretto',
      'testo'  => 'Parli sempre con chi segue davvero il progetto.',
      'icona'  => $base_url . 'img/svg/account.svg',
    ],
  ];
}

if (!$punti_forza_mobile_personalizzati) {
  $punti_forza_mobile = $punti_forza;
}

$punti_forza = array_values($punti_forza);
$punti_forza_mobile = array_values($punti_forza_mobile);
$mappa_icone_punti_mobile = [0 => 0, 1 => 1, 2 => 3];

if ($punti_forza_mobile_personalizzati) {
  foreach ($punti_forza_mobile as $indice_punto_mobile => &$punto_mobile) {
    if (is_object($punto_mobile)) {
      $punto_mobile = get_object_vars($punto_mobile);
    }

    if (!is_array($punto_mobile)) {
      continue;
    }

    $icona_mobile = $normalizza_immagine($trova_icona_punto_forza($punto_mobile));
    $usa_icona_fallback = !empty($icona_mobile['url']) && strpos($icona_mobile['url'], $base_url . 'img/svg/') === 0;

    if (!empty($icona_mobile['url']) && !$usa_icona_fallback) {
      continue;
    }

    $indice_sorgente = $mappa_icone_punti_mobile[$indice_punto_mobile] ?? $indice_punto_mobile;
    $punto_sorgente = $punti_forza[$indice_sorgente] ?? ($punti_forza[$indice_punto_mobile] ?? null);

    if (is_object($punto_sorgente)) {
      $punto_sorgente = get_object_vars($punto_sorgente);
    }

    if (!is_array($punto_sorgente)) {
      continue;
    }

    $icona_sorgente = $trova_icona_punto_forza($punto_sorgente);
    $icona_sorgente_normalizzata = $normalizza_immagine($icona_sorgente);

    if (!empty($icona_sorgente_normalizzata['url'])) {
      $punto_mobile['icona'] = $icona_sorgente;
    }
  }

  unset($punto_mobile);
}

$totale_servizi = count($servizi);
$debug_proposta_attivo = function_exists('current_user_can') && current_user_can('manage_options') && !empty($servizi[0]) && is_array($servizi[0]);
$debug_primo_servizio = $debug_proposta_attivo ? $servizi[0] : [];
$debug_primo_link = $debug_proposta_attivo ? $normalizza_link($trova_link_servizio($debug_primo_servizio)) : ['url' => ''];
$debug_prima_icona = $debug_proposta_attivo ? $normalizza_immagine($trova_icona_servizio($debug_primo_servizio)) : ['url' => ''];

$render_servizio_card = function ($servizio, $indice_zero) use ($normalizza_testo_inline, $trova_titolo_servizio, $trova_testo_servizio, $trova_link_servizio, $trova_icona_servizio, $normalizza_link, $normalizza_immagine, $normalizza_servizio_key, $servizio_da_testo, $testo_cta, $totale_servizi, $base_url) {
  if (is_object($servizio)) {
    $servizio = get_object_vars($servizio);
  }

  if (!is_array($servizio)) {
    return;
  }

  $indice = $indice_zero + 1;
  $titolo_card = $normalizza_testo_inline($trova_titolo_servizio($servizio));
  $testo_card  = $normalizza_testo_inline($trova_testo_servizio($servizio));
  $link_raw    = $trova_link_servizio($servizio);
  $icona_raw   = $trova_icona_servizio($servizio);
  $link        = $normalizza_link($link_raw);
  $icona       = $normalizza_immagine($icona_raw);
  $servizio_raw = $servizio['servizio'] ?? $servizio['tipo_servizio'] ?? $servizio['colore_servizio'] ?? '';
  $servizio_key = is_array($servizio_raw) ? ($servizio_raw['value'] ?? '') : (string) $servizio_raw;
  $servizio_key = $normalizza_servizio_key($servizio_key ?: $servizio_da_testo($titolo_card, $indice, $totale_servizi));
  $testo_link   = $servizio['testo_link'] ?? $servizio['testo_bottone'] ?? $link['title'] ?? '';
  $testo_link   = $testo_cta($servizio_key, $titolo_card, $testo_link);
  $ha_link      = !empty($link['url']);
  $card_tag     = $ha_link ? 'a' : 'div';
  $icona_fallback = !empty($icona['url']) && strpos($icona['url'], $base_url . 'img/svg/') === 0;
  ?>
  <li class="chef-proposta-card chef-servizio-<?php echo esc_attr($servizio_key); ?>">
    <<?php echo $card_tag; ?> class="chef-proposta-card-inner" <?php if ($ha_link) : ?>href="<?php echo esc_url($link['url']); ?>"<?php endif; ?> <?php if (!empty($link['target'])) : ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?> <?php if ($link['target'] === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
      <?php if (!empty($icona['url'])) : ?>
        <figure class="contenitore-img">
          <img class="<?php echo $icona_fallback ? 'chef-proposta-icon-fallback' : 'chef-proposta-icon-uploaded'; ?>" src="<?php echo esc_url($icona['url']); ?>" alt="<?php echo esc_attr($icona['alt'] ?: wp_strip_all_tags((string) $titolo_card)); ?>">
        </figure>
      <?php endif; ?>

      <div class="testo-anteprima">
        <?php if (!empty($titolo_card)) : ?>
          <div class="titolo"><?php echo esc_html($titolo_card); ?></div>
        <?php endif; ?>

        <?php if (!empty($testo_card)) : ?>
          <div class="testo"><?php echo wp_kses_post($testo_card); ?></div>
        <?php endif; ?>
      </div>

      <?php if ($ha_link || !empty($testo_link)) : ?>
        <div class="anteprima-cta">
          <span><?php echo esc_html($testo_link); ?></span>
          <span class="anteprima-cta-arrow" aria-hidden="true"></span>
        </div>
      <?php endif; ?>
    </<?php echo $card_tag; ?>>
  </li>
  <?php
};
?>

<section class="chef-proposta-aziendale">
  <?php if ($debug_proposta_attivo) : ?>
    <!-- chef-proposta-debug fallback="<?php echo $servizi_fallback_attivo ? 'yes' : 'no'; ?>" layout_keys="<?php echo esc_attr(implode(',', array_keys($layout_row))); ?>" servizi_field_candidates="<?php echo esc_attr(implode(',', $nomi_campi_servizi)); ?>" selected_path="<?php echo esc_attr($servizi_da_layout_info['percorso']); ?>" candidate_lists="<?php echo esc_attr(implode('|', $debug_liste_candidate)); ?>" field_score="<?php echo esc_attr((string) $punteggio_servizi_campo); ?>" layout_score="<?php echo esc_attr((string) $punteggio_servizi_layout); ?>" servizi_count="<?php echo esc_attr((string) count($servizi)); ?>" first_service_keys="<?php echo esc_attr(implode(',', array_keys($servizi[0]))); ?>" first_has_link="<?php echo !empty($debug_primo_link['url']) ? 'yes' : 'no'; ?>" first_has_icon="<?php echo !empty($debug_prima_icona['url']) ? 'yes' : 'no'; ?>" first_icon_url="<?php echo esc_attr($debug_prima_icona['url']); ?>" -->
  <?php endif; ?>

  <div class="chef-proposta-aziendale-grid">
    <div class="chef-proposta-copy">
      <?php if (!empty($nome_sezione)) : ?>
        <div class="nome-sezione"><?php echo esc_html($nome_sezione); ?></div>
      <?php endif; ?>

      <?php if (!empty($titolo)) : ?>
        <div class="titoletto">
          <?php if (preg_match('/<h[1-6][\s>]/i', (string) $titolo)) : ?>
            <?php echo wp_kses_post($titolo); ?>
          <?php else : ?>
            <h2><?php echo wp_kses_post($titolo); ?></h2>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($testo_intro)) : ?>
        <div class="anteprime-sottotitolo"><?php echo wp_kses_post($testo_intro); ?></div>
      <?php endif; ?>
    </div>

    <div class="chef-proposta-aziendale-cards">
      <?php if (!empty($titolo_percorsi_principali_desktop) || (!empty($titolo_altri_percorsi_desktop) && $totale_servizi > 3)) : ?>
        <div class="chef-proposta-desktop-headings">
          <?php if (!empty($titolo_percorsi_principali_desktop)) : ?>
            <div class="chef-proposta-desktop-heading chef-proposta-desktop-heading-main"><?php echo esc_html($titolo_percorsi_principali_desktop); ?></div>
          <?php endif; ?>

          <?php if (!empty($titolo_altri_percorsi_desktop) && $totale_servizi > 3) : ?>
            <div class="chef-proposta-desktop-heading chef-proposta-desktop-heading-secondary"><?php echo esc_html($titolo_altri_percorsi_desktop); ?></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <ul class="chef-proposta-desktop-list">
        <?php foreach ($servizi as $indice_zero => $servizio) : ?>
          <?php $render_servizio_card($servizio, $indice_zero); ?>
        <?php endforeach; ?>
      </ul>

      <div class="chef-proposta-mobile-layout">
        <?php if (!empty($titolo_percorsi_principali_mobile)) : ?>
          <div class="chef-proposta-mobile-heading"><?php echo esc_html($titolo_percorsi_principali_mobile); ?></div>
        <?php endif; ?>

        <div class="chef-proposta-mobile-scroll" data-chef-proposta-scroll>
          <ul class="chef-proposta-mobile-main-list">
            <?php foreach (array_slice($servizi, 0, 3, true) as $indice_zero => $servizio) : ?>
              <?php $render_servizio_card($servizio, $indice_zero); ?>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="chef-proposta-scroll-progress chef-proposta-scroll-progress-main" aria-hidden="true">
          <span data-chef-proposta-progress></span>
        </div>

        <?php if ($totale_servizi > 3) : ?>
          <?php if (!empty($titolo_altri_percorsi_mobile)) : ?>
            <div class="chef-proposta-mobile-heading chef-proposta-mobile-heading-secondary"><?php echo esc_html($titolo_altri_percorsi_mobile); ?></div>
          <?php endif; ?>
          <ul class="chef-proposta-mobile-secondary-list">
            <?php foreach (array_slice($servizi, 3, count($servizi), true) as $indice_zero => $servizio) : ?>
              <?php $render_servizio_card($servizio, $indice_zero); ?>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($punti_forza) && is_array($punti_forza)) : ?>
      <?php if (!empty($titolo_punti_forza_desktop)) : ?>
        <div class="chef-proposta-benefits-heading chef-proposta-benefits-heading-desktop"><?php echo esc_html($titolo_punti_forza_desktop); ?></div>
      <?php endif; ?>
      <div class="chef-proposta-benefits chef-proposta-benefits-desktop">
        <?php foreach (array_slice($punti_forza, 0, 4) as $punto) :
          $titolo_punto = $punto['titolo'] ?? $punto['titolo_punto'] ?? '';
          $testo_punto  = $punto['testo'] ?? $punto['descrizione'] ?? '';
          $icona_punto  = $normalizza_immagine($trova_icona_punto_forza($punto));
        ?>
          <div class="chef-proposta-benefit">
            <?php if (!empty($icona_punto['url'])) : ?>
              <figure class="chef-proposta-benefit-icon">
                <span
                  class="chef-proposta-benefit-icon-mask"
                  style="--chef-proposta-benefit-icon: url('<?php echo esc_url($icona_punto['url']); ?>');"
                  role="img"
                  aria-label="<?php echo esc_attr($icona_punto['alt'] ?: wp_strip_all_tags((string) $titolo_punto)); ?>"
                ></span>
              </figure>
            <?php endif; ?>

            <div class="chef-proposta-benefit-copy">
              <?php if (!empty($titolo_punto)) : ?>
                <strong><?php echo esc_html($titolo_punto); ?></strong>
              <?php endif; ?>

              <?php if (!empty($testo_punto)) : ?>
                <span><?php echo esc_html($testo_punto); ?></span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($punti_forza_mobile) && is_array($punti_forza_mobile)) : ?>
      <?php if (!empty($titolo_punti_forza_mobile)) : ?>
        <div class="chef-proposta-mobile-heading chef-proposta-mobile-heading-benefits"><?php echo esc_html($titolo_punti_forza_mobile); ?></div>
      <?php endif; ?>
      <div class="chef-proposta-benefits-mobile">
        <?php foreach (array_slice($punti_forza_mobile, 0, 3) as $punto) :
          $titolo_punto = $punto['titolo'] ?? $punto['titolo_punto'] ?? '';
          $testo_punto  = $punto['testo'] ?? $punto['descrizione'] ?? '';
          $icona_punto  = $normalizza_immagine($trova_icona_punto_forza($punto));
        ?>
          <div class="chef-proposta-benefit">
            <?php if (!empty($icona_punto['url'])) : ?>
              <figure class="chef-proposta-benefit-icon">
                <span
                  class="chef-proposta-benefit-icon-mask"
                  style="--chef-proposta-benefit-icon: url('<?php echo esc_url($icona_punto['url']); ?>');"
                  role="img"
                  aria-label="<?php echo esc_attr($icona_punto['alt'] ?: wp_strip_all_tags((string) $titolo_punto)); ?>"
                ></span>
              </figure>
            <?php endif; ?>

            <div class="chef-proposta-benefit-copy">
              <?php if (!empty($titolo_punto)) : ?>
                <strong><?php echo esc_html($titolo_punto); ?></strong>
              <?php endif; ?>

              <?php if (!empty($testo_punto)) : ?>
                <span><?php echo esc_html($testo_punto); ?></span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_template_part('parti/layout_close'); ?>
