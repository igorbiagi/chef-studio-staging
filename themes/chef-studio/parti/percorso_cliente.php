<?php
/**
 * Layout "Home · Percorso del cliente a tappe". Campi in includes/home-layout-acf.php.
 * La linea e le tappe si accendono allo scroll (scripts-cs.js); senza JS sono già accese.
 */
get_template_part('parti/layout_open');
$tappe = get_sub_field('tappe') ?: [];
chef_rsw_apri('testo-largo hp-cliente');
if ($tappe) : ?>
<ol class="hp-vc" style="--hp-n:<?php echo count($tappe); ?>">
  <?php foreach ($tappe as $i => $t) : ?>
  <li><div class="hp-vc-n"><?php echo (int) $i + 1; ?></div><h3><?php echo chef_rsw_t($t['titolo'] ?? ''); ?></h3><p><?php echo chef_hp_inline($t['testo'] ?? ''); ?></p>
    <?php foreach (chef_hp_link_righe($t['etichette'] ?? '') as [$et, $u]) : ?><a class="hp-tag" href="<?php echo esc_url($u); ?>"><?php echo chef_rsw_t($et); ?></a><?php endforeach; ?></li>
  <?php endforeach; ?>
</ol>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
