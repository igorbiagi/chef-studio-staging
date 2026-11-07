<?php
// Normalizza eventuali select con Return=Array
$norm = function ($v) { return is_array($v) ? ($v['value'] ?? '') : $v; };
$pick = function (...$values) use ($norm) {
  foreach ($values as $value) {
    $normalized = $norm($value);

    if (is_scalar($normalized)) {
      $normalized = trim((string) $normalized);

      if ($normalized !== '') {
        return $normalized;
      }
    }
  }

  return '';
};

/**
 * Con clone "Seamless" nel layout:
 * - blocco_contenitore  (GROUP)
 * - blocco_contenuto    (GROUP)
 */
$cont  = get_sub_field('blocco_contenitore') ?: [];
$contn = get_sub_field('blocco_contenuto')   ?: [];

/* ----- CONTENITORE ----- */
$colore_contenitore            = $norm($cont['colore_contenitore']             ?? '');
$immagine_sfondo_contenitore   = $norm($cont['immagine_di_sfondo_contenitore'] ?? '');
$classe_aggiuntiva_contenitore = $norm($cont['classe_aggiuntiva_contenitore']  ?? '');
$classi_libere_contenitore     = trim($cont['classi_aggiuntive_libere_contenitore'] ?? '');
$extra1_contenitore            = $norm($cont['classe_extra_1_contenitore']     ?? '');
$extra2_contenitore            = $norm($cont['classe_extra_2_contenitore']     ?? '');

$classi_contenitore = array_filter(array_map('trim', [
  'contenitore', // fissa
  $colore_contenitore,
  $immagine_sfondo_contenitore,
  $classe_aggiuntiva_contenitore,
  $extra1_contenitore,
  $extra2_contenitore,
  $classi_libere_contenitore,
]));

/* ----- CONTENUTO ----- */
$tipo_contenuto          = $pick($contn['tipo_di_contenuto']                 ?? '');
$padding_contenuto       = $pick($contn['padding_contenuto']                 ?? '');
$padding_top_contenuto   = $pick(
  $contn['padding_top_contenuto'] ?? '',
  $contn['padding_top'] ?? ''
);
$padding_bottom_contenuto = $pick(
  $contn['padding_bottom_contenuto'] ?? '',
  $contn['padding_bottom'] ?? ''
);
$padding_laterale_contenuto = $pick(
  $contn['padding_laterale_contenuto'] ?? '',
  $contn['padding_laterale'] ?? '',
  $contn['padding_x_contenuto'] ?? '',
  $contn['padding_x'] ?? ''
);
$altri_attributi_contenuto       = $pick($contn['altri_attributi_contenuto'] ?? '');
$classi_libere_contenuto = trim($contn['classi_aggiuntive_libere_contenuto'] ?? '');
$extra1_contenuto        = $pick($contn['classe_extra_1_contenuto']          ?? '');
$extra2_contenuto        = $pick($contn['classe_extra_2_contenuto']          ?? '');

$classi_padding_default = [
  'pad-top-large',
  'pad-bottom-large',
  'pad-x-standard',
];

$mappa_padding_legacy = [
  'pad-h-top-bot' => ['pad-top-large', 'pad-bottom-large', 'pad-x-standard'],
  'pad-h-bot' => ['pad-top-small', 'pad-bottom-large', 'pad-x-standard'],
  'pad-h-top' => ['pad-top-large', 'pad-bottom-small', 'pad-x-standard'],
  'pad-h-top-bot-small' => ['pad-top-small', 'pad-bottom-small', 'pad-x-standard'],
  'pad-h-zero' => ['pad-top-zero', 'pad-bottom-zero', 'pad-x-standard'],
  'pad-w-zero' => ['pad-x-zero'],
];

$classi_padding_nuove = [
  $padding_top_contenuto,
  $padding_bottom_contenuto,
  $padding_laterale_contenuto,
];

$classi_padding_legacy = $padding_contenuto !== ''
  ? ($mappa_padding_legacy[$padding_contenuto] ?? [$padding_contenuto])
  : [];

$classi_padding_nuove_complete = count(array_filter($classi_padding_nuove)) === count($classi_padding_nuove);
$usa_padding_legacy = $padding_contenuto !== '' && (
  !$classi_padding_nuove_complete
);

if ($usa_padding_legacy) {
  $classi_padding_contenuto = $classi_padding_legacy;
} else {
  $classi_padding_contenuto = array_filter($classi_padding_nuove) ?: $classi_padding_default;
}

$classi_contenuto = array_filter(array_map('trim', array_merge([
  $tipo_contenuto,
], $classi_padding_contenuto, [
  $altri_attributi_contenuto,
  $extra1_contenuto,
  $extra2_contenuto,
  $classi_libere_contenuto,
])));


// Rende le classi accessibili anche fuori da questo file
global $classi_contenitore_aperti, $classi_contenuto_aperti;
$classi_contenitore_aperti = $classi_contenitore;
$classi_contenuto_aperti   = $classi_contenuto;

?>
<div class="<?php echo esc_attr(implode(' ', $classi_contenitore)); ?>">
  <div class="<?php echo esc_attr(implode(' ', $classi_contenuto)); ?>">

	  
