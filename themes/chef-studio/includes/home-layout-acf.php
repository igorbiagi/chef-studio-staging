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
      chef_rsw_campo($p . '_p', 'prova_numero', 'Prova: numero', 'text', chef_rsw_larghezza(30)),
      chef_rsw_campo($p . '_p', 'prova_testo', 'Prova: testo', 'textarea', chef_rsw_larghezza(70) + ['rows' => 2, 'new_lines' => '', 'instructions' => 'Si possono inserire link.']),
      chef_rsw_campo($p . '_p', 'testo_bottone', 'Bottone: testo', 'text', chef_rsw_larghezza(50)),
      chef_rsw_campo($p . '_p', 'link_bottone', 'Bottone: link', 'link', chef_rsw_larghezza(50) + ['return_format' => 'array']),
    ], ['max' => 5]),
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

  return $L;
});

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
