<?php
/**
 * Pagine di servizio: componenti come layout ACF (niente HTML scritto a mano nei campi testo).
 *
 * Tutto è definito qui, nel tema, con filtri acf/load_field: niente modifiche al database dei campi.
 * - Nuovi layout nel contenuto flessibile layout_flessibile_1 (template in parti/<nome>.php).
 * - Campi aggiunti a layout esistenti: header (punti di forza, schede sull'immagine),
 *   anteprime (numero in evidenza nei casi), come_lavoriamo (usa i campi bottone già presenti).
 * - Tendine "Classe extra 1/2 contenitore" con gli stili delle pagine di servizio.
 * Stili in css/chef-themes/chef-custom.css (prefisso rsw-).
 */

if (!defined('ABSPATH')) {
  exit;
}

/* -----------------------------------------------------------------
 * Tendine con gli stili delle pagine di servizio
 * ----------------------------------------------------------------- */
function chef_rsw_stili_contenitore() {
  return [
    'rsw-hero'                                         => 'Pagine servizio · Hero',
    'hero-header-enhanced rsw-intro'                   => 'Pagine servizio · Introduzione con immagine',
    'anteprime-box anteprime-icone rsw-schede rsw-tipi' => 'Pagine servizio · Tipi di sito (schede cliccabili)',
    'anteprime-box anteprime-icone rsw-schede'          => 'Pagine servizio · Elenco con icone',
    'anteprime-box rsw-schede rsw-numerata'             => 'Pagine servizio · Elenco numerato',
    'rsw-casi'                                         => 'Pagine servizio · Casi con foto e numero',
    'rsw-recensioni'                                   => 'Pagine servizio · Recensioni',
    'rsw-bottone'                                      => 'Pagine servizio · Bottone centrato',
    'box-confronto-pacchetti rsw-confronto'            => 'Pagine servizio · Confronto in due box',
    'rsw-fasi'                                         => 'Pagine servizio · Metodo in 4 fasi',
    'rsw-persone'                                      => 'Pagine servizio · Chi siamo con foto',
  ];
}

add_filter('acf/load_field/name=classe_extra_1_contenitore', function ($field) {
  $field['choices'] = array_merge(is_array($field['choices'] ?? null) ? $field['choices'] : [], chef_rsw_stili_contenitore());
  $field['allow_null'] = 1;
  $field['instructions'] = 'Stile del blocco nelle pagine di servizio.';
  return $field;
});

add_filter('acf/load_field/name=classe_extra_2_contenitore', function ($field) {
  $field['choices'] = array_merge(is_array($field['choices'] ?? null) ? $field['choices'] : [], [
    'rsw-percorso' => 'Casi raccontati a tappe (con frecce)',
  ]);
  $field['allow_null'] = 1;
  return $field;
});

/* -----------------------------------------------------------------
 * Helper per definire i campi
 * ----------------------------------------------------------------- */
function chef_rsw_campo($pref, $nome, $etichetta, $tipo, $extra = []) {
  return array_merge([
    'key'          => 'field_rswc_' . $pref . '_' . $nome,
    'label'        => $etichetta,
    'name'         => $nome,
    'type'         => $tipo,
    'instructions' => '',
    'required'     => 0,
    'wrapper'      => ['width' => '', 'class' => '', 'id' => ''],
  ], $extra);
}

function chef_rsw_wysiwyg($pref, $nome, $etichetta, $istruzioni = '') {
  return chef_rsw_campo($pref, $nome, $etichetta, 'wysiwyg', [
    'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1, 'delay' => 1, 'instructions' => $istruzioni,
  ]);
}

function chef_rsw_ripetitore($pref, $nome, $etichetta, $bottone, $sotto, $extra = []) {
  return chef_rsw_campo($pref, $nome, $etichetta, 'repeater', array_merge([
    'layout' => 'block', 'button_label' => $bottone, 'min' => 0, 'max' => 0, 'sub_fields' => $sotto,
  ], $extra));
}

function chef_rsw_larghezza($w) {
  return ['wrapper' => ['width' => (string) $w, 'class' => '', 'id' => '']];
}

/* -----------------------------------------------------------------
 * Definizione dei nuovi layout
 * ----------------------------------------------------------------- */
function chef_rsw_definizioni_layout() {
  $L = [];

  // 1. Tabella di confronto
  $p = 'tab';
  $L['tabella_confronto'] = ['Tabella di confronto', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima della tabella', 'Titolo e introduzione.'),
    chef_rsw_ripetitore($p, 'colonne', 'Colonne', 'Aggiungi colonna', [
      chef_rsw_campo($p . '_col', 'intestazione', 'Intestazione', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_col', 'nota', 'Nota sotto l\'intestazione (in grassetto)', 'text', chef_rsw_larghezza(50) + ['instructions' => 'Es. "il nostro metodo". Facoltativa.']),
    ], ['layout' => 'table']),
    chef_rsw_ripetitore($p, 'righe', 'Righe', 'Aggiungi riga', [
      chef_rsw_campo($p . '_riga', 'celle', 'Celle della riga', 'textarea', [
        'rows' => 4, 'new_lines' => '',
        'instructions' => 'Una cella per riga, nello stesso ordine delle colonne. Si possono inserire link.',
      ]),
    ]),
    chef_rsw_campo($p, 'opzioni', 'Aspetto', 'checkbox', [
      'choices' => [
        'prima-voce'  => 'Prima colonna come voci (in grassetto)',
        'evidenzia'   => 'Ultima colonna evidenziata in giallo',
        'stretta'     => 'Tabella stretta (non a tutta larghezza)',
      ],
      'layout' => 'vertical', 'return_format' => 'value',
    ]),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo la tabella'),
  ]];

  // 2. Percorso a tappe
  $p = 'tl';
  $L['percorso_tappe'] = ['Percorso a tappe', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima del percorso', 'Titolo e introduzione.'),
    chef_rsw_ripetitore($p, 'tappe', 'Tappe', 'Aggiungi tappa', [
      chef_rsw_campo($p . '_t', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_t', 'risultato', 'Risultato della tappa (etichetta con la spunta)', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_t', 'testo', 'Testo', 'wysiwyg', ['tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0, 'delay' => 1]),
    ]),
    chef_rsw_campo($p, 'inizio', 'Scritta all\'inizio della linea', 'text', chef_rsw_larghezza(50) + ['default_value' => 'Primo contatto']),
    chef_rsw_campo($p, 'fine', 'Scritta alla fine della linea', 'text', chef_rsw_larghezza(50)),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo il percorso'),
  ]];

  // 3. Schede con numeri e schermate
  $p = 'cwv';
  $L['schede_numeri'] = ['Schede con numeri e schermate', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima delle schede'),
    chef_rsw_ripetitore($p, 'schede', 'Schede', 'Aggiungi scheda', [
      chef_rsw_campo($p . '_s', 'etichetta', 'Etichetta in alto', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_s', 'numero', 'Numero grande', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_s', 'barra', 'Barra verde piena sotto il numero', 'true_false', chef_rsw_larghezza(30) + ['ui' => 1]),
      chef_rsw_campo($p . '_s', 'testo', 'Testo', 'textarea', ['rows' => 2, 'new_lines' => '']),
      chef_rsw_campo($p . '_s', 'nota', 'Nota piccola (periodo, fonte)', 'text'),
      chef_rsw_campo($p . '_s', 'immagine', 'Schermata', 'image', ['return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Su computer si apre grande al clic.']),
    ]),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo le schede'),
  ]];

  // 4. Barre con soglie (Core Web Vitals)
  $p = 'bar';
  $L['barre_soglie'] = ['Barre con soglie e valori', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima delle barre'),
    chef_rsw_campo($p, 'legenda', 'Legenda della freccia', 'text', ['instructions' => 'Es. "valori reali di uno dei siti che gestiamo..."']),
    chef_rsw_ripetitore($p, 'metriche', 'Metriche', 'Aggiungi metrica', [
      chef_rsw_campo($p . '_m', 'sigla', 'Sigla', 'text', chef_rsw_larghezza(20)),
      chef_rsw_campo($p . '_m', 'descrizione', 'Descrizione', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_m', 'nota', 'Nota verde sotto la barra', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_m', 'buono', 'Fascia verde', 'text', chef_rsw_larghezza(25)),
      chef_rsw_campo($p . '_m', 'medio', 'Fascia arancione', 'text', chef_rsw_larghezza(25)),
      chef_rsw_campo($p . '_m', 'scarso', 'Fascia rossa', 'text', chef_rsw_larghezza(25)),
      chef_rsw_campo($p . '_m', 'valore', 'Valore sulla freccia', 'text', chef_rsw_larghezza(12)),
      chef_rsw_campo($p . '_m', 'posizione', 'Posizione freccia (%)', 'number', chef_rsw_larghezza(13) + ['min' => 0, 'max' => 100, 'instructions' => '0 = inizio barra']),
    ]),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo le barre'),
  ]];

  // 5. Mappa a schema
  $p = 'mappa';
  $L['mappa_schema'] = ['Mappa a schema (centro e parti collegate)', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima della mappa'),
    chef_rsw_campo($p, 'centro_titolo', 'Centro: titolo', 'text', chef_rsw_larghezza(33)),
    chef_rsw_campo($p, 'centro_testo', 'Centro: testo', 'text', chef_rsw_larghezza(34)),
    chef_rsw_campo($p, 'centro_etichetta', 'Centro: etichetta in giallo', 'text', chef_rsw_larghezza(33)),
    chef_rsw_ripetitore($p, 'parti', 'Parti collegate', 'Aggiungi parte', [
      chef_rsw_campo($p . '_p', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(35)),
      chef_rsw_campo($p . '_p', 'lato', 'Lato', 'select', chef_rsw_larghezza(15) + ['choices' => ['l' => 'Sinistra', 'r' => 'Destra'], 'return_format' => 'value']),
      chef_rsw_campo($p . '_p', 'testo', 'Testo', 'textarea', chef_rsw_larghezza(50) + ['rows' => 2, 'new_lines' => '', 'instructions' => 'Le parole tra `accenti gravi` escono evidenziate (es. `/en/`).']),
    ]),
    chef_rsw_campo($p, 'etichette', 'Etichette sotto la mappa', 'textarea', ['rows' => 4, 'new_lines' => '', 'instructions' => 'Una per riga.']),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo la mappa'),
  ]];

  // 6. Schema redirect
  $p = 'migra';
  $L['schema_redirect'] = ['Schema vecchi e nuovi indirizzi (redirect)', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima dello schema'),
    chef_rsw_campo($p, 'intestazione_vecchio', 'Intestazione colonna sinistra', 'text', chef_rsw_larghezza(50) + ['default_value' => 'Vecchio indirizzo']),
    chef_rsw_campo($p, 'intestazione_nuovo', 'Intestazione colonna destra', 'text', chef_rsw_larghezza(50) + ['default_value' => 'Nuovo indirizzo']),
    chef_rsw_ripetitore($p, 'righe', 'Indirizzi', 'Aggiungi indirizzo', [
      chef_rsw_campo($p . '_r', 'vecchio', 'Vecchio indirizzo', 'text', chef_rsw_larghezza(45)),
      chef_rsw_campo($p . '_r', 'codice', 'Codice', 'text', chef_rsw_larghezza(10) + ['default_value' => '301']),
      chef_rsw_campo($p . '_r', 'nuovo', 'Nuovo indirizzo', 'text', chef_rsw_larghezza(45)),
    ], ['layout' => 'table']),
    chef_rsw_campo($p, 'totale_numero', 'Riquadro scuro: numero', 'text', chef_rsw_larghezza(30)),
    chef_rsw_campo($p, 'totale_testo', 'Riquadro scuro: testo', 'textarea', chef_rsw_larghezza(70) + ['rows' => 2, 'new_lines' => '']),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo lo schema'),
  ]];

  // 7. Forbici di prezzo
  $p = 'fb';
  $L['forbici_prezzi'] = ['Barre di intervalli (forbici di prezzo)', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Testo prima del grafico'),
    chef_rsw_campo($p, 'scala', 'Valore massimo della scala', 'number', chef_rsw_larghezza(33) + ['default_value' => 4000]),
    chef_rsw_campo($p, 'passo', 'Passo delle tacche', 'number', chef_rsw_larghezza(33) + ['default_value' => 1000]),
    chef_rsw_campo($p, 'unita', 'Unità (sulla prima e sull\'ultima tacca)', 'text', chef_rsw_larghezza(34) + ['default_value' => '€']),
    chef_rsw_ripetitore($p, 'righe', 'Righe', 'Aggiungi riga', [
      chef_rsw_campo($p . '_r', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_r', 'fonte', 'Fonte e data', 'text', chef_rsw_larghezza(50)),
      chef_rsw_ripetitore($p . '_r', 'barre', 'Barre', 'Aggiungi barra', [
        chef_rsw_campo($p . '_b', 'da', 'Da', 'number', chef_rsw_larghezza(20)),
        chef_rsw_campo($p . '_b', 'a', 'A', 'number', chef_rsw_larghezza(20)),
        chef_rsw_campo($p . '_b', 'etichetta', 'Scritta', 'text', chef_rsw_larghezza(35)),
        chef_rsw_campo($p . '_b', 'tono', 'Colore', 'select', chef_rsw_larghezza(25) + ['choices' => ['' => 'Bordeaux', 'chiaro' => 'Giallo chiaro', 'scuro' => 'Scuro'], 'return_format' => 'value']),
      ], ['layout' => 'table']),
    ]),
    chef_rsw_campo($p, 'nota', 'Nota sotto il grafico', 'textarea', ['rows' => 2, 'new_lines' => '']),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo il grafico'),
  ]];

  // 8. Elenco con spunte e immagine
  $p = 'woo';
  $L['elenco_immagine'] = ['Elenco con spunte e immagine', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Titolo e introduzione'),
    chef_rsw_ripetitore($p, 'punti', 'Punti', 'Aggiungi punto', [
      chef_rsw_campo($p . '_p', 'titolo', 'Titolo', 'text'),
      chef_rsw_campo($p . '_p', 'testo', 'Testo', 'wysiwyg', ['tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0, 'delay' => 1]),
    ]),
    chef_rsw_campo($p, 'immagine', 'Immagine', 'image', chef_rsw_larghezza(50) + ['return_format' => 'array', 'preview_size' => 'medium']),
    chef_rsw_campo($p, 'didascalia', 'Didascalia', 'text', chef_rsw_larghezza(50)),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo'),
  ]];

  // 9. Schema di scambio tra due sistemi
  $p = 'gest';
  $L['schema_scambio'] = ['Schema di scambio tra due sistemi', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo', 'Testo a sinistra'),
    chef_rsw_campo($p, 'sinistra_titolo', 'Box chiaro: titolo', 'text', chef_rsw_larghezza(25)),
    chef_rsw_campo($p, 'sinistra_sotto', 'Box chiaro: sottotitolo', 'text', chef_rsw_larghezza(25)),
    chef_rsw_campo($p, 'destra_titolo', 'Box scuro: titolo', 'text', chef_rsw_larghezza(25)),
    chef_rsw_campo($p, 'destra_sotto', 'Box scuro: sottotitolo', 'text', chef_rsw_larghezza(25)),
    chef_rsw_ripetitore($p, 'flussi', 'Cosa passa tra i due', 'Aggiungi', [
      chef_rsw_campo($p . '_f', 'testo', 'Testo', 'text', chef_rsw_larghezza(60)),
      chef_rsw_campo($p . '_f', 'verso', 'Verso', 'select', chef_rsw_larghezza(40) + ['choices' => ['dx' => 'Verso il box scuro →', 'sx' => '← Verso il box chiaro'], 'return_format' => 'value']),
    ], ['layout' => 'table']),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo'),
  ]];

  return $L;
}

/* -----------------------------------------------------------------
 * Aggiunge layout e campi al contenuto flessibile
 * ----------------------------------------------------------------- */
add_filter('acf/load_field/name=layout_flessibile_1', function ($field) {
  if (empty($field['layouts']) || !is_array($field['layouts'])) {
    return $field;
  }

  // Cloni contenitore/contenuto presi da "testo" (sfondo, classi, padding come gli altri blocchi)
  $cloni = [];
  foreach ($field['layouts'] as $l) {
    if (($l['name'] ?? '') !== 'testo' || empty($l['sub_fields'])) {
      continue;
    }
    foreach ($l['sub_fields'] as $sf) {
      if (in_array($sf['name'] ?? '', ['blocco_contenitore', 'blocco_contenuto'], true)) {
        $cloni[] = $sf;
      }
    }
  }

  $esistenti = [];
  foreach ($field['layouts'] as $k => $l) {
    $esistenti[$l['name'] ?? ''] = $k;
  }

  foreach (chef_rsw_definizioni_layout() as $nome => $def) {
    if (isset($esistenti[$nome])) {
      continue;
    }
    $key = 'layout_rswc_' . $nome;
    $sotto = [];
    foreach ($cloni as $c) {
      $c['parent_layout'] = $key;
      $sotto[] = $c;
    }
    foreach ($def[1] as $sf) {
      $sf['parent'] = $field['key'] ?? '';
      $sf['parent_layout'] = $key;
      $sotto[] = $sf;
    }
    $field['layouts'][$key] = [
      'key' => $key, 'name' => $nome, 'label' => 'Servizi · ' . $def[0], 'display' => 'block',
      'sub_fields' => $sotto, 'min' => '', 'max' => '',
    ];
  }

  // Campi in più su layout esistenti
  foreach ($field['layouts'] as $k => $l) {
    $nome = $l['name'] ?? '';
    $nomi = array_map(function ($s) { return $s['name'] ?? ''; }, $l['sub_fields'] ?? []);

    if ($nome === 'header' && !in_array('punti_forza', $nomi, true)) {
      $p = 'hero';
      $field['layouts'][$k]['sub_fields'][] = chef_rsw_ripetitore($p, 'punti_forza', 'Punti di forza sotto i bottoni', 'Aggiungi punto', [
        chef_rsw_campo($p . '_pf', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(40)),
        chef_rsw_campo($p . '_pf', 'sotto', 'Riga sotto', 'text', chef_rsw_larghezza(40)),
        chef_rsw_campo($p . '_pf', 'stelle', 'Cinque stelle', 'true_false', chef_rsw_larghezza(20) + ['ui' => 1]),
      ], ['layout' => 'table', 'parent_layout' => $k, 'instructions' => 'Su smartphone si vedono i primi due.']);
      $field['layouts'][$k]['sub_fields'][] = chef_rsw_ripetitore($p, 'schede_immagine', 'Schede bianche sull\'immagine', 'Aggiungi scheda', [
        chef_rsw_campo($p . '_fc', 'posizione', 'Posizione', 'select', chef_rsw_larghezza(20) + ['choices' => ['a' => 'In basso', 'b' => 'A destra (solo computer)'], 'return_format' => 'value']),
        chef_rsw_campo($p . '_fc', 'etichetta', 'Etichetta', 'text', chef_rsw_larghezza(25)),
        chef_rsw_campo($p . '_fc', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(25)),
        chef_rsw_campo($p . '_fc', 'testo', 'Testo', 'text', chef_rsw_larghezza(30)),
      ], ['layout' => 'table', 'parent_layout' => $k, 'max' => 2]);
    }

    if ($nome === 'anteprime') {
      foreach ($field['layouts'][$k]['sub_fields'] as $i => $sf) {
        if (($sf['name'] ?? '') !== 'elementi_manuali_repeater' || empty($sf['sub_fields'])) {
          continue;
        }
        $nomi_r = array_map(function ($s) { return $s['name'] ?? ''; }, $sf['sub_fields']);
        if (in_array('numero', $nomi_r, true)) {
          continue;
        }
        $field['layouts'][$k]['sub_fields'][$i]['sub_fields'][] = chef_rsw_campo('ant', 'numero', 'Numero in evidenza', 'text', chef_rsw_larghezza(40) + ['instructions' => 'Es. "7 giorni". Facoltativo.']);
        $field['layouts'][$k]['sub_fields'][$i]['sub_fields'][] = chef_rsw_campo('ant', 'numero_testo', 'Riga sotto il numero', 'text', chef_rsw_larghezza(60));
      }
    }
  }

  return $field;
}, 30);

/* -----------------------------------------------------------------
 * Funzioni di supporto per i template
 * ----------------------------------------------------------------- */
function chef_rsw_html($wysiwyg) {
  $wysiwyg = (string) $wysiwyg;
  if (trim($wysiwyg) === '') {
    return '';
  }
  return function_exists('chefstudio_responsive_editor_content') ? chefstudio_responsive_editor_content($wysiwyg) : wp_kses_post($wysiwyg);
}

function chef_rsw_righe($testo) {
  return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $testo)), 'strlen'));
}

function chef_rsw_apri($classe = 'testo-largo') {
  $nome = get_sub_field('nome_sezione');
  echo '<div class="' . esc_attr($classe) . '">';
  if ($nome) {
    echo '<div class="nome-sezione">' . esc_html($nome) . '</div>';
  }
  echo chef_rsw_html(get_sub_field('testo_prima'));
}

function chef_rsw_chiudi() {
  echo chef_rsw_html(get_sub_field('testo_dopo'));
  echo '</div>';
}
