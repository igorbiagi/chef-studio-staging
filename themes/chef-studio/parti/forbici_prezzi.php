<?php
/**
 * Layout "Barre di intervalli (forbici di prezzo)". Campi in includes/rsw-layout-acf.php.
 * Le barre sono posizionate in proporzione alla scala; le tacche si generano dal passo.
 */
get_template_part('parti/layout_open');
$scala = max(1, (float) (get_sub_field('scala') ?: 4000));
$passo = max(1, (float) (get_sub_field('passo') ?: 1000));
$unita = trim((string) get_sub_field('unita'));
$righe = get_sub_field('righe') ?: [];
$nota  = (string) get_sub_field('nota');
$divisioni = max(1, (int) round($scala / $passo));
$pct = function ($v) use ($scala) {
  return rtrim(rtrim(number_format(max(0, min(100, $v / $scala * 100)), 2, '.', ''), '0'), '.');
};
$fmt = function ($v) {
  return number_format((float) $v, 0, ',', '.');
};

chef_rsw_apri();
if ($righe) : ?>
<div class="rsw-forbici" style="--fb-div:<?php echo (int) $divisioni; ?>"><?php foreach ($righe as $r) : ?><div class="rsw-fb-r"><div class="rsw-fb-n"><b><?php echo esc_html($r['titolo'] ?? ''); ?></b><span><?php echo esc_html($r['fonte'] ?? ''); ?></span></div><div class="rsw-fb-t"><?php foreach (($r['barre'] ?? []) ?: [] as $b) :
  $da = (float) ($b['da'] ?? 0);
  $a  = (float) ($b['a'] ?? 0);
  $tono = in_array($b['tono'] ?? '', ['chiaro', 'scuro'], true) ? ' ' . $b['tono'] : ''; ?><span class="rsw-fb-b<?php echo esc_attr($tono); ?>" style="left:<?php echo esc_attr($pct($da)); ?>%;width:<?php echo esc_attr($pct($a - $da)); ?>%"><?php echo esc_html($b['etichetta'] ?? ''); ?></span><?php endforeach; ?></div></div><?php endforeach; ?><div class="rsw-fb-asse"><div class="rsw-fb-asse-v"><?php for ($i = 0; $i <= $divisioni; $i++) :
  $v = $i * $passo;
  $txt = $fmt($v);
  if (($i === 0 || $i === $divisioni) && $unita !== '') {
    $txt .= ' ' . $unita;
  } ?><span><?php echo esc_html($txt); ?></span><?php endfor; ?></div></div><?php if ($nota !== '') : ?><p class="rsw-fb-nota"><?php echo esc_html($nota); ?></p><?php endif; ?></div>
<?php endif;
chef_rsw_chiudi();
get_template_part('parti/layout_close');
