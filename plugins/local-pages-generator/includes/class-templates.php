<?php
if (!defined('ABSPATH')) exit;

class LPG_Templates {
    static function init(){
        add_filter('single_template',[__CLASS__,'load']);
        add_action('wp','LPG_Templates::ensure_admin_bar');
    }

    static function load($tpl){
        global $post;
        if ($post && $post->post_type === 'local-page') {
            $t = LPG_PATH.'templates/single-local-page.php';
            if (file_exists($t)) return $t;
        }
        return $tpl;
    }

    // Se il tema non chiama wp_footer(), la barra admin può sparire. Qui ci assicuriamo che sia abilitata.
    static function ensure_admin_bar(){
        if (is_singular('local-page') && current_user_can('edit_post', get_the_ID())) {
            show_admin_bar(true);
        }
    }
}
