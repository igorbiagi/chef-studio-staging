<?php
/**
 * Home: tre layout ACF in più per il contenuto flessibile (template in parti/<nome>.php).
 * - hero_percorso: hero con il percorso animato ricerca -> pagina -> richiesta e la striscia di numeri
 * - percorsi_obiettivi: "Cosa vuoi ottenere?" con schede e pannelli
 * - percorso_cliente: le tappe del cliente, dalla ricerca alla richiesta
 * Stili: hero in css/critical/chef-custom-critical.css (sopra la piega), il resto in chef-custom.css (prefisso hp-).
 * Animazioni in js/scripts-cs.js: solo transform e opacity, ferme con prefers-reduced-motion.
 */

if (!defined('ABSPATH')) {
  exit;
}

add_filter('chef_rsw_definizioni_layout', function ($L) {

  $p = 'hph';
  $L['hero_percorso'] = ['Home · Hero con percorso animato', [
    chef_rsw_campo($p, 'occhiello', 'Occhiello sopra il titolo', 'text'),
    chef_rsw_campo($p, 'titolo', 'Titolo (H1)', 'text', ['instructions' => 'Per evidenziare una parola: <em>parola</em>.']),
    chef_rsw_campo($p, 'paragrafo', 'Paragrafo', 'textarea', ['rows' => 3, 'new_lines' => '']),
    chef_rsw_campo($p, 'testo_bottone_1', 'Bottone principale: testo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'link_bottone_1', 'Bottone principale: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    chef_rsw_campo($p, 'testo_bottone_2', 'Bottone secondario: testo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'link_bottone_2', 'Bottone secondario: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    chef_rsw_ripetitore($p, 'prove', 'Prove sotto i bottoni', 'Aggiungi prova', [
      chef_rsw_campo($p . '_pr', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_pr', 'sotto', 'Riga sotto', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_pr', 'stelle', 'Cinque stelle', 'true_false', chef_rsw_larghezza(20) + ['ui' => 1]),
    ], ['layout' => 'table', 'max' => 3]),
    chef_rsw_campo($p, 'ricerca', 'Tappa 1: ricerca che si scrive da sola', 'text', ['instructions' => 'Cosa chiede un possibile cliente a Google o a un\'AI.']),
    chef_rsw_campo($p, 'risposta', 'Tappa 1: risposta dell\'AI', 'textarea', ['rows' => 2, 'new_lines' => '', 'instructions' => 'Per evidenziare: <b>Chef Studio</b>.']),
    chef_rsw_ripetitore($p, 'ricerche', 'Tappa 1 e 3: ricerche a rotazione', 'Aggiungi ricerca', [
      chef_rsw_campo($p . '_r', 'ricerca', 'Cosa cerca il cliente', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_r', 'risposta', 'Risposta dell\'AI', 'text', chef_rsw_larghezza(45) + ['instructions' => 'Per evidenziare: <b>la tua azienda</b>.']),
      chef_rsw_campo($p . '_r', 'notifica', 'Notifica (titolo)', 'text', chef_rsw_larghezza(25)),
    ], ['layout' => 'table', 'max' => 8, 'instructions' => 'Se compilato sostituisce ricerca, risposta e titolo della notifica qui sopra. Girano in ordine, partendo da una a caso.']),
    chef_rsw_campo($p, 'tappa_2', 'Tappa 2: la pagina', 'textarea', ['rows' => 2, 'new_lines' => '']),
    chef_rsw_campo($p, 'notifica_titolo', 'Tappa 3: notifica, titolo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'notifica_testo', 'Tappa 3: notifica, riga sotto', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'didascalia', 'Didascalia sotto il percorso', 'text'),
    chef_rsw_ripetitore($p, 'numeri', 'Striscia di numeri sotto la hero', 'Aggiungi numero', [
      chef_rsw_campo($p . '_n', 'numero', 'Numero', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_n', 'testo', 'Testo', 'text', chef_rsw_larghezza(70)),
    ], ['layout' => 'table', 'max' => 4]),
  ]];

  $p = 'hpo';
  $L['percorsi_obiettivi'] = ['Home · Scelta dei percorsi (Cosa vuoi ottenere?)', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Titolo e introduzione'),
    chef_rsw_ripetitore($p, 'percorsi', 'Percorsi', 'Aggiungi percorso', [
      chef_rsw_campo($p . '_p', 'icona', 'Icona', 'image', chef_rsw_larghezza(20) + ['return_format' => 'id', 'preview_size' => 'thumbnail']),
      chef_rsw_campo($p . '_p', 'titolo', 'Scheda: titolo', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_p', 'sotto', 'Scheda: riga sotto', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_p', 'titolo_pannello', 'Pannello: titolo (H3)', 'text'),
      chef_rsw_campo($p . '_p', 'testo', 'Pannello: testo', 'textarea', ['rows' => 3, 'new_lines' => '']),
      chef_rsw_campo($p . '_p', 'passi', 'Passi (uno per riga)', 'textarea', ['rows' => 3, 'new_lines' => '', 'instructions' => 'Tre righe. Per il grassetto: <b>parola</b>.']),
      chef_rsw_campo($p . '_p', 'servizi', 'Pagine collegate (una per riga: Testo | /indirizzo/)', 'textarea', ['rows' => 4, 'new_lines' => '']),
      chef_rsw_campo($p . '_p', 'anteprima_tipo', 'Anteprima del lavoro', 'select', chef_rsw_larghezza(25) + ['choices' => ['' => 'Nessuna (usa numero e testo)', 'immagine' => 'Immagine di un progetto', 'ricerca' => 'Risultato Google/AI disegnato', 'chat' => 'Chat AI disegnata'], 'return_format' => 'value']),
      chef_rsw_campo($p . '_p', 'anteprima_immagine', 'Anteprima: immagine', 'image', chef_rsw_larghezza(25) + ['return_format' => 'id', 'preview_size' => 'thumbnail']),
      chef_rsw_campo($p . '_p', 'anteprima_righe', 'Anteprima: righe (ricerca o chat, una per riga)', 'textarea', chef_rsw_larghezza(50) + ['rows' => 4, 'new_lines' => '', 'instructions' => 'Ricerca: 1a riga = cosa si cerca, 2a = risposta AI, poi i risultati. Chat: righe alterne cliente / assistente. Per evidenziare: <b>testo</b>.']),
      chef_rsw_campo($p . '_p', 'prova_numero', 'Prova: numero', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_p', 'prova_testo', 'Prova: testo', 'textarea', chef_rsw_larghezza(70) + ['rows' => 2, 'new_lines' => '', 'instructions' => 'Si possono inserire link.']),
      chef_rsw_campo($p . '_p', 'testo_bottone', 'Bottone: testo', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_p', 'link_bottone', 'Bottone: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    ], ['max' => 5]),
    chef_rsw_campo($p, 'titolo_servizi', 'Titolo sopra le pagine collegate', 'text', chef_rsw_larghezza(50) + ['default_value' => 'I servizi per questo obiettivo']),
    chef_rsw_campo($p, 'etichetta_anteprima', 'Etichetta sopra le anteprime disegnate', 'text', chef_rsw_larghezza(50) + ['default_value' => 'Esempio']),
    chef_rsw_wysiwyg($p, 'nota', 'Nota sotto le schede'),
  ]];

  $p = 'hpc';
  $L['percorso_cliente'] = ['Home · Percorso del cliente a tappe', [
    chef_rsw_campo($p, 'nome_sezione', 'Nome sezione', 'text'),
    chef_rsw_wysiwyg($p, 'testo_prima', 'Titolo e introduzione'),
    chef_rsw_ripetitore($p, 'tappe', 'Tappe', 'Aggiungi tappa', [
      chef_rsw_campo($p . '_t', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_t', 'testo', 'Testo', 'textarea', chef_rsw_larghezza(40) + ['rows' => 2, 'new_lines' => '']),
      chef_rsw_campo($p . '_t', 'etichette', 'Etichette (una per riga: Testo | /indirizzo/)', 'textarea', chef_rsw_larghezza(30) + ['rows' => 2, 'new_lines' => '']),
    ], ['max' => 6]),
    chef_rsw_wysiwyg($p, 'testo_dopo', 'Testo dopo le tappe'),
  ]];

  $p = 'hpm';
  $L['hero_mockup'] = ['Home · Hero con mockup e percorso del cliente', [
    chef_rsw_campo($p, 'occhiello', 'Occhiello sopra il titolo', 'text'),
    chef_rsw_campo($p, 'titolo', 'Titolo (H1)', 'text', ['instructions' => 'Per evidenziare una parola: <em>parola</em>.']),
    chef_rsw_campo($p, 'paragrafo', 'Paragrafo', 'textarea', ['rows' => 3, 'new_lines' => '']),
    chef_rsw_campo($p, 'testo_bottone_1', 'Bottone principale: testo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'link_bottone_1', 'Bottone principale: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    chef_rsw_campo($p, 'testo_bottone_2', 'Bottone secondario: testo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'link_bottone_2', 'Bottone secondario: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    chef_rsw_ripetitore($p, 'prove', 'Prove sotto i bottoni', 'Aggiungi prova', [
      chef_rsw_campo($p . '_pr', 'titolo', 'Titolo', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_pr', 'sotto', 'Riga sotto', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_pr', 'stelle', 'Cinque stelle', 'true_false', chef_rsw_larghezza(20) + ['ui' => 1]),
    ], ['layout' => 'table', 'max' => 3]),
    chef_rsw_ripetitore($p, 'mockup', 'Mockup a rotazione (il primo si carica subito, gli altri dopo)', 'Aggiungi mockup', [
      chef_rsw_campo($p . '_m', 'immagine', 'Immagine (WebP, sfondo trasparente)', 'image', chef_rsw_larghezza(20) + ['return_format' => 'id', 'preview_size' => 'thumbnail']),
      chef_rsw_campo($p . '_m', 'ricerca', '1. Cosa cerca l\'utente', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_m', 'azione', '2. Cosa fa sul sito', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_m', 'terza', '3. Cosa succede poi', 'text', chef_rsw_larghezza(40)),
      chef_rsw_campo($p . '_m', 'leva', '3. Etichetta (es. Automazioni)', 'text', chef_rsw_larghezza(20)),
      chef_rsw_campo($p . '_m', 'icona', '3. Icona', 'select', chef_rsw_larghezza(20) + ['choices' => ['auto' => 'Fulmine (automazioni)', 'ai' => 'Scintilla (AI)', 'cont' => 'Stella (contenuti)', 'form' => 'Documento (form)'], 'return_format' => 'value']),
      chef_rsw_campo($p . '_m', 'richiesta', '4. Cosa ti arriva', 'text', chef_rsw_larghezza(20)),
    ], ['max' => 8, 'instructions' => 'I testi delle tappe non sono contenuto della pagina: li scrive lo script, così le parole dei settori di esempio non pesano sulla SEO della home.']),
    chef_rsw_campo($p, 'tappe_fisse', 'Tappe: righe piccole ed etichette fisse (una per riga: riga piccola | etichetta)', 'textarea', ['rows' => 4, 'new_lines' => '', 'default_value' => "L’utente cerca su Google o nelle AI | SEO\nArriva sul sito: ogni clic è misurato | UX e dati\nPoi | \nTi arriva in email o nel CRM | Email o CRM"]),
    chef_rsw_campo($p, 'didascalia', 'Scritta sotto il mockup', 'text', ['default_value' => 'Esempio: il percorso di un cliente su un sito su misura']),
  ]];

  $p = 'hpb';
  $L['servizi_bento'] = ['Home · Servizi a griglia (2 grandi + 2 piccoli)', [
    chef_rsw_campo($p, 'nome_sezione', 'Titoletto', 'text', chef_rsw_larghezza(30)),
    chef_rsw_campo($p, 'titolo', 'Titolo (H2)', 'text', chef_rsw_larghezza(70)),
    chef_rsw_campo($p, 'testo', 'Testo', 'textarea', ['rows' => 3, 'new_lines' => '']),
    chef_rsw_campo($p, 'testo_bottone', 'Bottone: testo', 'text', chef_rsw_larghezza(50)),
    chef_rsw_campo($p, 'link_bottone', 'Bottone: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    chef_rsw_ripetitore($p, 'servizi', 'Servizi (1° grande chiaro, 2° largo scuro, poi piccoli)', 'Aggiungi servizio', [
      chef_rsw_campo($p . '_s', 'icona', 'Icona', 'select', chef_rsw_larghezza(20) + ['choices' => ['siti' => 'Sito', 'seo' => 'SEO', 'ecom' => 'Carrello', 'ai' => 'AI'], 'return_format' => 'value']),
      chef_rsw_campo($p . '_s', 'titolo', 'Titolo (H3)', 'text', chef_rsw_larghezza(40) + ['instructions' => 'Per evidenziare: <mark>parole</mark>.']),
      chef_rsw_campo($p . '_s', 'link', 'Link', 'link', chef_rsw_larghezza(40) + ['return_format' => 'array', 'instructions' => 'Un link che inizia con # mostra la freccia verso il basso.']),
      chef_rsw_campo($p . '_s', 'testo', 'Testo', 'textarea', chef_rsw_larghezza(60) + ['rows' => 2, 'new_lines' => '']),
      chef_rsw_campo($p . '_s', 'etichette', 'Etichette (separate da |)', 'text', chef_rsw_larghezza(40)),
    ], ['max' => 4]),
    chef_rsw_campo($p, 'nota', 'Riga sotto la griglia', 'textarea', ['rows' => 2, 'new_lines' => '', 'instructions' => 'Si possono inserire link.']),
  ]];

  return $L;
});

/** Icone a linea della home (stroke = currentColor). */
function chef_hp_icona($k) {
  $I = [
    'siti' => '<rect x="5" y="8" width="38" height="30" rx="3"/><path d="M5 15h38"/><path d="m19 22-4 4.5 4 4.5M29 22l4 4.5-4 4.5M26 20.5l-4 12"/>',
    'seo'  => '<circle cx="20" cy="20" r="13"/><path d="m29.5 29.5 11 11"/><path d="m13 24 5-5 3.5 3.5L28 16"/><path d="M24 16h4v4"/>',
    'ecom' => '<path d="M4 8h6l5 22h22l5-15H13"/><circle cx="18" cy="38" r="3"/><circle cx="34" cy="38" r="3"/>',
    'ai'   => '<path d="M20 6l2.6 8.4L31 17l-8.4 2.6L20 28l-2.6-8.4L9 17l8.4-2.6z"/><path d="M35 26l1.6 4.4L41 32l-4.4 1.6L35 38l-1.6-4.4L29 32l4.4-1.6z"/><path d="M11 32l1 3 3 1-3 1-1 3-1-3-3-1 3-1z"/>',
  ];
  return isset($I[$k]) ? '<svg class="hb-ic" viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $I[$k] . '</svg>' : '';
}

/** "Testo | /url/" per riga -> [[testo, url], ...] */
function chef_hp_link_righe($testo) {
  $out = [];
  foreach (chef_rsw_righe($testo) as $riga) {
    $parti = array_map('trim', explode('|', $riga, 2));
    if ($parti[0] !== '') {
      $out[] = [$parti[0], $parti[1] ?? ''];
    }
  }
  return $out;
}

/** Testo breve con i soli tag in linea ammessi. */
function chef_hp_inline($testo) {
  return wp_kses(wptexturize((string) $testo), ['b' => [], 'strong' => [], 'em' => [], 'a' => ['href' => [], 'target' => [], 'rel' => []]]);
}
