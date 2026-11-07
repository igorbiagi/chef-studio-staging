<?php get_template_part('parti/layout_open'); ?>

<?php
$nome_sezione = get_sub_field('nome_sezione');
$titolo = get_sub_field('titolo');
?>

<?php if (!empty($nome_sezione) || !empty($titolo)) : ?>
  <div class="titoletto">
    <?php if (!empty($nome_sezione)) : ?>
      <div class="nome-sezione">
        <?php echo esc_html($nome_sezione); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($titolo)) : ?>
      <h2><?php echo wp_kses_post($titolo); ?></h2>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if (have_rows('numeri')) : ?>
  <div class="numbers-box">
    <ul>
      <?php while (have_rows('numeri')) : the_row();
        $numero = trim((string) get_sub_field('numero'));
        $testo  = get_sub_field('testo');

        if ($numero === '' && empty($testo)) {
          continue;
        }
      ?>
        <li>
          <div class="numbers-contenuto">
            <?php if ($numero !== '') : ?>
              <div class="number">
                <?php echo esc_html($numero); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($testo)) : ?>
              <div class="testo-anteprima">
                <div class="testo"><?php echo wp_kses_post($testo); ?></div>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
<?php endif; ?>

<?php get_template_part('parti/layout_close'); ?>
