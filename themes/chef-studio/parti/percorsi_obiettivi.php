<?php
/**
 * Layout "Home · Scelta dei percorsi". Campi in includes/home-layout-acf.php.
 * Senza JavaScript i pannelli restano tutti visibili uno sotto l'altro (classe hp-js aggiunta da scripts-cs.js).
 */
get_template_part('parti/layout_open');
$percorsi = get_sub_field('percorsi') ?: [];
$uid = 'hpo' . substr(md5(wp_json_encode($percorsi)), 0, 6);
chef_rsw_apri('testo-largo hp-percorsi');
if ($percorsi) : ?>
<div class="hp-grid">
  <div class="hp-sx">
    <div class="hp-tabs" role="tablist">
      <?php foreach ($percorsi as $i => $pc) : ?>
      <button type="button" class="hp-tab" role="tab" id="<?php echo $uid . '-t' . $i; ?>" aria-controls="<?php echo $uid . '-p' . $i; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
        <?php if (!empty($pc['icona'])) : echo wp_get_attachment_image((int) $pc['icona'], 'thumbnail', false, ['alt' => '', 'class' => 'hp-tab-ic', 'loading' => 'lazy']); endif; ?>
        <span><?php echo chef_rsw_t($pc['titolo'] ?? ''); ?><small><?php echo chef_rsw_t($pc['sotto'] ?? ''); ?></small></span>
      </button>
      <?php endforeach; ?>
    </div>
    <?php if ($nota = get_sub_field('nota')) : ?><div class="hp-nota"><?php echo chef_rsw_html($nota); ?></div><?php endif; ?>
  </div>
  <div class="hp-pannello">
    <?php foreach ($percorsi as $i => $pc) : $bt = $pc['link_bottone'] ?? []; ?>
    <div class="hp-pane<?php echo $i === 0 ? ' hp-on' : ''; ?>" role="tabpanel" id="<?php echo $uid . '-p' . $i; ?>" aria-labelledby="<?php echo $uid . '-t' . $i; ?>">
      <h3><?php echo chef_rsw_t($pc['titolo_pannello'] ?? ''); ?></h3>
      <p><?php echo chef_hp_inline($pc['testo'] ?? ''); ?></p>
      <?php $passi = chef_rsw_righe($pc['passi'] ?? ''); if ($passi) : ?><ol class="hp-strada"><?php foreach ($passi as $ps) : ?><li><?php echo chef_hp_inline($ps); ?></li><?php endforeach; ?></ol><?php endif; ?>
      <div class="hp-due"><div>
        <ul class="hp-servizi"><?php foreach (chef_hp_link_righe($pc['servizi'] ?? '') as [$t, $u]) : ?><li><a href="<?php echo esc_url($u); ?>"><?php echo chef_rsw_t($t); ?></a></li><?php endforeach; ?></ul></div>
        <div class="hp-due-dx"><?php
        $tipo  = $pc['anteprima_tipo'] ?? '';
        $righe = chef_rsw_righe($pc['anteprima_righe'] ?? '');
        if ($tipo === 'immagine' && !empty($pc['anteprima_immagine'])) : ?>
        <figure class="hp-ant hp-ant-img"><?php echo wp_get_attachment_image((int) $pc['anteprima_immagine'], 'medium_large', false, ['loading' => 'lazy', 'sizes' => '(max-width:700px) 90vw, 420px']); ?>
          <?php if (!empty($pc['prova_testo'])) : ?><figcaption><?php if (!empty($pc['prova_numero'])) : ?><b><?php echo chef_rsw_t($pc['prova_numero']); ?></b> <?php endif; echo chef_hp_inline($pc['prova_testo']); ?></figcaption><?php endif; ?></figure>
        <?php elseif ($tipo === 'ricerca' && $righe) : ?>
        <div class="hp-ant hp-ant-serp" aria-hidden="true"><div class="hp-ant-q"><?php echo chef_rsw_t(array_shift($righe)); ?></div>
          <?php if ($righe) : ?><div class="hp-ant-ai"><span>AI</span><?php echo chef_hp_inline(array_shift($righe)); ?></div><?php endif; ?>
          <?php foreach ($righe as $i => $r) : ?><div class="hp-ant-ris<?php echo $i === 0 ? ' hp-ant-tu' : ''; ?>"><?php echo chef_hp_inline($r); ?></div><?php endforeach; ?></div>
        <?php if (!empty($pc['prova_testo'])) : ?><p class="hp-ant-nota"><?php echo chef_hp_inline($pc['prova_testo']); ?></p><?php endif; ?>
        <?php elseif ($tipo === 'chat' && $righe) : ?>
        <div class="hp-ant hp-ant-chat" aria-hidden="true"><div class="hp-ant-chat-top"><span></span>Assistente AI · risponde subito</div>
          <?php foreach ($righe as $i => $r) : ?><div class="hp-bolla <?php echo $i % 2 ? 'hp-bolla-ai' : 'hp-bolla-cli'; ?>" style="--i:<?php echo (int) $i; ?>"><?php echo chef_hp_inline($r); ?></div><?php endforeach; ?></div>
        <?php if (!empty($pc['prova_testo'])) : ?><p class="hp-ant-nota"><?php echo chef_hp_inline($pc['prova_testo']); ?></p><?php endif; ?>
        <?php elseif (!empty($pc['prova_numero']) || !empty($pc['prova_testo'])) : ?><div class="hp-prova"><b><?php echo chef_rsw_t($pc['prova_numero'] ?? ''); ?></b><p><?php echo chef_hp_inline($pc['prova_testo'] ?? ''); ?></p></div><?php endif; ?></div>
      </div>
      <?php if (!empty($bt['url'])) : ?><a class="hp-vai" href="<?php echo esc_url($bt['url']); ?>"><?php echo chef_rsw_t($pc['testo_bottone'] ?? ''); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif;
echo '</div>';
get_template_part('parti/layout_close');
