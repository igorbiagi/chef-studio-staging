<?php
/**
 * Layout "Sezione AI con chat": testo a sinistra, telefono con chat animata a destra.
 * Campi definiti in functions.php (filtro su layout_flessibile_1).
 * Stili: .rsw-ai / .rsw-phone in css/chef-themes/chef-custom.css; animazione in js/scripts-cs.js.
 */
get_template_part('parti/layout_open');

$nome_sezione     = get_sub_field('nome_sezione');
$titolo           = get_sub_field('titolo');
$titolo_evidenza  = get_sub_field('titolo_evidenza');
$testo            = get_sub_field('testo');
$chip             = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) get_sub_field('chip'))));
$chat_titolo      = get_sub_field('chat_titolo');
$chat_sottotitolo = get_sub_field('chat_sottotitolo');
$messaggi         = get_sub_field('messaggi') ?: [];
$ok_titolo        = (string) get_sub_field('conferma_titolo');
$ok_testo         = (string) get_sub_field('conferma_testo');
?>

<div class="rsw-ai">
  <?php if (!empty($nome_sezione)) : ?>
    <div class="nome-sezione"><?php echo esc_html($nome_sezione); ?></div>
  <?php endif; ?>

  <div class="struttura-container">
    <div class="div-1">
      <?php if ($titolo || $titolo_evidenza) : ?>
        <h2><?php echo esc_html($titolo); ?><?php if ($titolo_evidenza) : ?> <span class="rsw-grad"><?php echo esc_html($titolo_evidenza); ?></span><?php endif; ?></h2>
      <?php endif; ?>

      <?php if (!empty($testo)) {
        echo chefstudio_responsive_editor_content($testo);
      } ?>

      <?php if ($chip) : ?>
        <div class="hero-chip-list">
          <?php foreach ($chip as $c) : ?>
            <span class="hero-chip"><?php echo esc_html($c); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="div-2">
      <?php if ($messaggi) : ?>
        <div class="rsw-phone" role="img" aria-label="<?php echo esc_attr('Esempio di conversazione: ' . ($chat_titolo ?: 'assistente AI')); ?>"
             data-ok-titolo="<?php echo esc_attr($ok_titolo); ?>" data-ok-testo="<?php echo esc_attr($ok_testo); ?>">
          <div class="rsw-scr">
            <div class="rsw-top">
              <i>AI</i>
              <span><b><?php echo esc_html($chat_titolo); ?></b><?php echo esc_html($chat_sottotitolo); ?></span>
              <em>AI</em>
            </div>
            <div class="rsw-feed">
              <?php foreach ($messaggi as $n => $m) :
                $chi = ($m['chi'] ?? 'cliente') === 'ai' ? 'rsw-bot' : 'rsw-me';
                $prodotti = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($m['prodotti'] ?? ''))));
              ?>
                <span class="rsw-msg <?php echo esc_attr($chi); ?>">
                  <?php echo esc_html($m['testo'] ?? ''); ?>
                  <?php if ($prodotti) : ?>
                    <span class="rsw-prod">
                      <?php foreach ($prodotti as $k => $riga) :
                        $parti = array_map('trim', explode('|', $riga, 2));
                      ?>
                        <span><i class="<?php echo $k % 2 ? 'b' : 'a'; ?>"></i><b><?php echo esc_html($parti[0]); ?></b><?php echo esc_html($parti[1] ?? ''); ?></span>
                      <?php endforeach; ?>
                    </span>
                  <?php endif; ?>
                </span>
              <?php endforeach; ?>
            </div>
            <div class="rsw-inp">Scrivi un messaggio…</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php get_template_part('parti/layout_close'); ?>
