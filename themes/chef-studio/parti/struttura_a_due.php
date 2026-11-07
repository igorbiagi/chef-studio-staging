<?php get_template_part('parti/layout_open'); ?>

<?php
$contenuto_div_1 = get_sub_field('contenuto_div_1');
$contenuto_div_2 = get_sub_field('contenuto_div_2');
$tipo_struttura = get_sub_field('tipo_struttura');
$attributi_struttura = get_sub_field('attributi_struttura');
$nome_sezione = get_sub_field('nome_sezione');

$link_1 = get_sub_field('link_1');
$testo_link_1 = get_sub_field('testo_link_1');

$link_2 = get_sub_field('link_2');
$testo_link_2 = get_sub_field('testo_link_2');
?>

<?php if (!empty($nome_sezione)) : ?>
  <div class="nome-sezione">
    <?php echo esc_html($nome_sezione); ?>
  </div>
<?php endif; ?>

<div class="struttura-container <?php echo esc_attr($tipo_struttura); ?><?php if (!empty($attributi_struttura)) echo ' ' . esc_attr($attributi_struttura); ?>">
  <div class="div-1">
    <?php if (!empty($contenuto_div_1)) {
      echo chefstudio_responsive_editor_content($contenuto_div_1);
    } ?>
    
    <?php if (!empty($link_1)): ?>
      <a class="btn btn-small btn-primary" href="<?php echo esc_url($link_1); ?>">
        <?php echo esc_html($testo_link_1 ?: 'Scopri di più'); ?>
      </a>
    <?php endif; ?>
  </div>

  <div class="div-2">
    <?php if (!empty($contenuto_div_2)) {
      echo chefstudio_responsive_editor_content($contenuto_div_2);
    } ?>

    <?php if (!empty($link_2)): ?>
      <a class="btn btn-small btn-primary" href="<?php echo esc_url($link_2); ?>">
        <?php echo esc_html($testo_link_2 ?: 'Scopri di più'); ?>
      </a>
    <?php endif; ?>
  </div>
</div>

<?php get_template_part('parti/layout_close'); ?>
