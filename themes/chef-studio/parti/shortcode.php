<?php
get_template_part('parti/layout_open');

$shortcode    = trim((string) get_sub_field('shortcode'));
$nome_sezione = get_sub_field('nome_sezione');
?>

<?php if (!empty($nome_sezione)): ?>
    <div class="nome-sezione">
        <?php echo esc_html($nome_sezione); ?>
    </div>
<?php endif; ?>

<?php
if ($shortcode) {
    echo do_shortcode($shortcode);
}

get_template_part('parti/layout_close');
?>