<?php


if ( ! defined('ABSPATH') ) exit;

class LPG_Import {
	
	
	    /**
     * Normalizza un valore immagine restituendo sempre l’ID attachment.
     * Accetta ID, array (da ACF) o URL.
     */
    private static function normalize_attachment_id($value) {
        if (empty($value)) return 0;

        // Se è già un ID numerico
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Se è un array ACF con chiave 'ID'
        if (is_array($value) && !empty($value['ID'])) {
            return (int) $value['ID'];
        }

        // Se è un URL → risale all’ID dell’allegato
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            global $wpdb;
            $upload_dir = wp_upload_dir();
            $url = esc_url_raw($value);
            $baseurl = trailingslashit($upload_dir['baseurl']);
            if (strpos($url, $baseurl) === 0) {
                $path = str_replace($baseurl, '', $url);
                $attachment_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT ID FROM $wpdb->posts WHERE guid LIKE %s AND post_type = 'attachment' LIMIT 1",
                    '%' . $wpdb->esc_like($path) . '%'
                ));
                return $attachment_id ? (int) $attachment_id : 0;
            }
        }

        return 0;
    }


   public static function init() {

    // ✅ Registra il menu admin
    add_action('admin_menu', [__CLASS__, 'menu']);
    add_action('save_post_pagine-locali', [__CLASS__, 'maybe_set_web_agency_slug'], 50);
    add_action('acf/save_post', [__CLASS__, 'maybe_set_web_agency_slug'], 30);
    add_action('save_post_pagine-locali', [__CLASS__, 'maybe_update_web_agency_yoast'], 70);
    add_action('acf/save_post', [__CLASS__, 'maybe_update_web_agency_yoast'], 50);
    add_filter('wpseo_title', [__CLASS__, 'filter_web_agency_yoast_title']);
    add_filter('wpseo_metadesc', [__CLASS__, 'filter_web_agency_yoast_metadesc']);
    add_filter('wpseo_metadesc', [__CLASS__, 'filter_seo_pages_yoast_metadesc']);

    // ❌ NIENTE admin_post QUI DENTRO
    // Li gestiamo tutti dal file principale del plugin

    error_log('✅ LPG_Import::init registrato con successo');
}




    public static function menu(){
        add_submenu_page(
            'edit.php?post_type=pagine-locali',
            'Importa da Database',
            'Importa da Database',
            'manage_options',
            'lpg-import',
            [__CLASS__, 'render_import_page']
        );

        add_submenu_page(
            'edit.php?post_type=pagine-locali',
            'Rigenera Immagini SEO',
            'Rigenera Immagini SEO',
            'manage_options',
            'lpg-rigenera-immagini',
            [__CLASS__, 'render_rigenera_immagini_page']
        );

        add_submenu_page(
            'edit.php?post_type=pagine-locali',
            'Rigenera Immagini Siti Web',
            'Rigenera Immagini Siti Web',
            'manage_options',
            'lpg-rigenera-immagini-rs',
            [__CLASS__, 'render_rigenera_immagini_rs_page']
        );
    }

    public static function render_import_page(){
        if( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');

        $args = [
            'post_type'      => 'argomento-seo',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => ['publish', 'private'],
        ];
        $arg_posts = get_posts($args);
        ?>
        <div class="wrap">
            <h1>Importa da Dump SQL (tabella: pagine_seo)</h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="lpg_import_db">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label>File .sql</label></th>
                        <td><input type="file" name="dump" accept=".sql" required></td>
                    </tr>

                    <tr>
    <th scope="row"><label>Argomento (tassonomia “argomento_categoria”)</label></th>
    <td>
        <select name="argomento_slug" required>
            <option value="">— Seleziona una categoria —</option>
            <?php
            $terms = get_terms([
                'taxonomy'   => 'argomento_categoria',
                'hide_empty' => false,
            ]);
            foreach ($terms as $term) :
            ?>
                <option value="<?php echo esc_attr($term->slug); ?>">
                    <?php echo esc_html($term->name); ?> (slug: <?php echo $term->slug; ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">Scegli la categoria da cui ereditare le immagini predefinite (es. SEO o Realizzazione Siti).</p>
    </td>
</tr>


                    <tr>
                        <th scope="row"><label>Città (facoltativa)</label></th>
                        <td>
                            <input type="text" name="filtro_citta" placeholder="Es: Pisa">
                            <p class="description">Lascia vuoto per importare tutte le righe del file SQL.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label>Opzioni</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_update" value="1">
                                Sovrascrivi le pagine già esistenti (attenzione: eventuali modifiche locali verranno perse)
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Importa'); ?>
            </form>

            <hr>

            <h2>Rigenerazione Immagini</h2>
            <p>Questo comando rigenera le 3 immagini (principale, secondaria, finale) per tutte le pagine già pubblicate, basandosi sulle immagini predefinite dell'argomento.</p>
            <a class="button button-secondary" href="<?php echo admin_url('admin-post.php?action=lpg_regenerate_images'); ?>">Rigenera immagini per tutte le pagine</a>
            <a class="button button-secondary" href="<?php echo admin_url('edit.php?post_type=pagine-locali&page=lpg-rigenera-immagini'); ?>">Rigenera immagini SEO per pagine selezionate</a>
			
			<hr>
<h2>Rigenerazione Immagini (Realizzazione Siti)</h2>
<p>Rigenera tutte le immagini delle pagine locali collegate all’argomento <strong>realizzazione-siti</strong>.</p>
<a class="button button-secondary" href="<?php echo admin_url('admin-post.php?action=lpg_regenerate_images_rs'); ?>">
    Rigenera immagini Realizzazione Siti
</a>
<a class="button button-secondary" href="<?php echo admin_url('edit.php?post_type=pagine-locali&page=lpg-rigenera-immagini-rs'); ?>">
    Rigenera immagini Realizzazione Siti per pagine selezionate
</a>

			<hr>
<h2>Rigenerazione Immagini (Web Agency)</h2>
<p>Rigenera tutte le immagini delle pagine locali collegate all’argomento <strong>web-agency</strong>.</p>
<a class="button button-secondary" href="<?php echo admin_url('admin-post.php?action=lpg_regenerate_images_wa'); ?>">
    Rigenera immagini Web Agency
</a>

            <hr>
<h2>SEO Yoast (Web Agency)</h2>
<p>Compila o aggiorna meta title e meta description Yoast delle pagine <strong>web-agency</strong>, usando la città e la variante testi scelta. I meta Yoast modificati a mano non vengono sovrascritti.</p>
<a class="button button-secondary" href="<?php echo admin_url('admin-post.php?action=lpg_sync_yoast_wa'); ?>">
    Sincronizza SEO Yoast Web Agency
</a>

        </div>
        <?php
    }

    public static function handle_import(){
    static $already_run = false;
    if ($already_run) {
        error_log('⏭️ handle_import già eseguito, salto seconda chiamata');
        return;
    }
    $already_run = true;

    error_log("⚠️ HANDLE_IMPORT richiamato manualmente? URI = " . $_SERVER['REQUEST_URI']);

    if( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');
    if( empty($_FILES['dump']['tmp_name']) ) wp_die('Nessun file caricato.');


    // Recupera il termine selezionato (SEO o Realizzazione Siti)
    $argomento_slug = sanitize_title($_POST['argomento_slug'] ?? 'seo');
    $term = get_term_by('slug', $argomento_slug, 'argomento_categoria');
    $term_ref = $term ? 'argomento_categoria_' . $term->term_id : '';

    $allow_update = !empty($_POST['allow_update']);

    $path = $_FILES['dump']['tmp_name'];
    $rows = self::parse_sql($path, 'pagine_seo');

    // Eventuale filtro città
    $filtro_citta = trim($_POST['filtro_citta'] ?? '');
    if ($filtro_citta !== '') {
        $rows = array_filter($rows, fn($r) => strtolower(trim($r['citta'])) === strtolower($filtro_citta));
    }
    if (empty($rows)) wp_die('Nessuna riga importabile trovata.');

    // Immagini di default prese dal termine della tassonomia
    $defaults = [
        'immagine_main_default'      => self::normalize_attachment_id(get_field('immagine_main_default', $term_ref)),
        'immagine_secondary_default' => self::normalize_attachment_id(get_field('immagine_secondary_default', $term_ref)),
        'immagine_last_default'      => self::normalize_attachment_id(get_field('immagine_last_default', $term_ref)),
    ];

    error_log("LPG IMPORT: immagini default dal termine {$term_ref}");
    foreach ($defaults as $k => $v) {
        error_log(" - {$k} = {$v}");
    }

    $count = 0;

    foreach ($rows as $row) {

        $mk  = trim($row['main_keyword'] ?? '');
        $cit = trim($row['citta'] ?? '');

        if ($mk !== '' && stripos(mb_strtolower($mk), mb_strtolower($cit)) !== false) {
            $title = $mk;
        } elseif ($mk !== '') {
            $title = $mk . ' ' . $cit;
        } else {
            $title = $cit ?: 'Pagina Locale';
        }

        $slug = sanitize_title($row['slug'] ?? $title);

        // Se esiste già e non vogliamo sovrascrivere → salta
        $existing = get_page_by_path($slug, OBJECT, 'pagine-locali');
        if ($existing && !$allow_update) continue;

        // Crea o aggiorna la pagina
        $post_id = self::upsert_post($title, $slug);
        wp_set_object_terms($post_id, $argomento_slug, 'argomento_categoria', false);

        // Aggiorna i campi normali
        foreach (self::field_map() as $db => $acf) {
            update_field($acf, \LPG_Helpers::clean_text($row[$db] ?? ''), $post_id);
        }
        update_field('citta', $cit, $post_id);
        update_field('slug',  $slug, $post_id);

        // ===== FAQ =====
        $faq_rows = [];
        for ($i = 1; $i <= 7; $i++) {
            $tit = \LPG_Helpers::clean_text($row["titolo_faq_$i"] ?? '');
            $txt = \LPG_Helpers::clean_text($row["testo_faq_$i"] ?? '');
            if ($tit || $txt) {
                $faq_rows[] = [
                    'fld_faq_tit' => $tit,
                    'fld_faq_txt' => $txt,
                ];
            }
        }
        if (!empty($faq_rows)) {
            update_field('fld_faq', $faq_rows, $post_id);
            error_log("✅ FAQ salvate correttamente per ID {$post_id}: " . print_r($faq_rows, true));
        } else {
            error_log("⚠️ Nessuna FAQ trovata per ID {$post_id}");
        }

        // ===== YOAST =====
        if (!empty($row['meta_title'])) {
            $yoast_title = trim(wp_strip_all_tags($row['meta_title'])) . ' %%sep%% %%sitename%%';
            update_post_meta($post_id, '_yoast_wpseo_title', $yoast_title);
        }
        if (!empty($row['meta_description'])) {
            $yoast_desc = trim(wp_strip_all_tags($row['meta_description']));
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $yoast_desc);
        }

        // ===== IMMAGINI (solo se i campi sono vuoti) =====
        self::ensure_images($post_id, $defaults, $cit);

        $count++;
    }

		// Imposta un flag per rigenerare i permalink alla prossima richiesta
update_option('lpg_needs_flush', 1);

wp_redirect(admin_url("edit.php?post_type=pagine-locali&import_ok=$count"));
exit;

}




    private static function upsert_post($title, $slug){
        $existing = get_page_by_path($slug, OBJECT, 'pagine-locali');
        if ($existing) {
            wp_update_post(['ID'=>$existing->ID, 'post_title'=>$title, 'post_status'=>'publish']);
            return $existing->ID;
        }
        return wp_insert_post([
            'post_type'   => 'pagine-locali',
            'post_title'  => $title,
            'post_name'   => $slug,
            'post_status' => 'publish'
        ]);
    }

    private static function attachment_is_used_by_other_pages($attachment_id, $post_id, $meta_keys) {
        if (!$attachment_id || empty($meta_keys)) {
            return false;
        }

        global $wpdb;

        $placeholders = implode(',', array_fill(0, count($meta_keys), '%s'));
        $sql = $wpdb->prepare(
            "SELECT COUNT(DISTINCT post_id)
             FROM {$wpdb->postmeta}
             WHERE meta_value = %s
             AND post_id <> %d
             AND meta_key IN ($placeholders)",
            array_merge([(string) $attachment_id, (int) $post_id], $meta_keys)
        );

        return (int) $wpdb->get_var($sql) > 0;
    }


    private static function ensure_images($post_id, $defaults, $citta, $only_fields = []){
    $citta_lc  = strtolower(trim($citta));
    $slug_comp = 'seo-' . $citta_lc;

    // nomi dei file generati
    $patterns = [
        'immagine_main'      => "{$slug_comp}.jpg",
        'immagine_secondary' => "consulente-seo-{$citta_lc}.jpg",
        'immagine_last'      => "consulente-seo-{$citta_lc}-igor-biagi-chef-studio.jpg",
    ];

    $meta_keys = array_keys($patterns);

    foreach ($patterns as $field => $filename) {
        if (!empty($only_fields) && !in_array($field, $only_fields, true)) {
            continue;
        }

        $default_id = $defaults[$field . '_default'] ?? 0;
        if (!$default_id) {
            continue;
        }

        // 🔧 Sovrascrive sempre le immagini con le default
$existing_field = get_field($field, $post_id);
if ($existing_field) {
    // elimina l'immagine precedente se esiste
    $old_id = is_array($existing_field) && isset($existing_field['ID'])
        ? $existing_field['ID']
        : (int) $existing_field;
    if ($old_id) {
        if (!self::attachment_is_used_by_other_pages($old_id, $post_id, $meta_keys)) {
            wp_delete_attachment($old_id, true);
        }
    }
}


        $new_id = self::duplicate_attachment_with_name($default_id, $filename, $post_id);
        if ($new_id) {
            // nel plugin salvavi l'ID semplice, facciamo uguale
            update_field($field, $new_id, $post_id);
        }
    }
}



	
	
	
	
	
	
	
	
	
	
    private static function get_realizzazione_siti_image_fields() {
        return [
            'rs_immagine_main',
            'rs_immagine_secondary',
            'rs_immagine_designer',
            'rs_immagine_sito_1',
            'rs_immagine_sito_2',
            'rs_immagine_sito_3',
            'rs_immagine_sito_4',
            'rs_immagine_sito_5',
            'rs_immagine_sito_6',
            'rs_immagine_sito_7',
            'rs_immagine_sito_8',
            'rs_immagine_seo_friendly',
            'rs_immagine_creazione',
        ];
    }


    private static function get_realizzazione_siti_image_labels() {
        return [
            'rs_immagine_main'         => 'Immagine principale',
            'rs_immagine_secondary'    => 'Immagine secondaria',
            'rs_immagine_designer'     => 'Immagine designer',
            'rs_immagine_sito_1'       => 'Esempio sito 1',
            'rs_immagine_sito_2'       => 'Esempio sito 2',
            'rs_immagine_sito_3'       => 'Esempio sito 3',
            'rs_immagine_sito_4'       => 'Esempio sito 4',
            'rs_immagine_sito_5'       => 'Esempio sito 5',
            'rs_immagine_sito_6'       => 'Esempio sito 6',
            'rs_immagine_sito_7'       => 'Esempio sito 7',
            'rs_immagine_sito_8'       => 'Esempio sito 8',
            'rs_immagine_seo_friendly' => 'Immagine SEO friendly',
            'rs_immagine_creazione'    => 'Immagine creazione siti',
        ];
    }


    private static function get_realizzazione_siti_defaults($term_ref, $fields = []) {
        $defaults = [];
        $fields = !empty($fields) ? $fields : self::get_realizzazione_siti_image_fields();

        foreach ($fields as $field) {
            $val = get_field($field, $term_ref);

            if (!$val) {
                $val = get_field($field . '_default', $term_ref);
            }

            $defaults[$field] = self::normalize_attachment_id($val);
        }

        return $defaults;
    }


	private static function ensure_images_rs($post_id, $defaults, $citta, $only_fields = []){
    if (!$citta) return;

    // slug città "pulito" come usi nel template (senza accenti, senza spazi)
    $citta_slug = strtolower(trim(remove_accents($citta)));
    $citta_slug = preg_replace('/\s+/', '-', $citta_slug);

    // pattern file → campo pagina
    $patterns = [
        'rs_immagine_main'         => "realizzazione-siti-web-{$citta_slug}.jpg",
        'rs_immagine_secondary'    => "realizzazione-siti-internet-{$citta_slug}.jpg",
        'rs_immagine_designer'     => "andrea-biagi-web-designer-{$citta_slug}.png",
        'rs_immagine_sito_1'       => "siti-web-per-artigiani-{$citta_slug}.jpg",
        'rs_immagine_sito_2'       => "siti-per-aziende-agricole-{$citta_slug}.jpg",
        'rs_immagine_sito_3'       => "siti-per-hotel-case-vacanza-{$citta_slug}.jpg",
        'rs_immagine_sito_4'       => "siti-per-agenzia-immobiliare-{$citta_slug}.jpg",
        'rs_immagine_sito_5'       => "sito-per-aziende-{$citta_slug}.jpg",
        'rs_immagine_sito_6'       => "realizzazione-e-commerce-{$citta_slug}.jpg",
        'rs_immagine_sito_7'       => "siti-per-architetti-{$citta_slug}.jpg",
        'rs_immagine_sito_8'       => "siti-per-stabilimenti-balneari-{$citta_slug}.jpg",
        'rs_immagine_seo_friendly' => "siti-seo-friendly-{$citta_slug}.jpg",
        'rs_immagine_creazione'    => "creazione-siti-web-{$citta_slug}.jpg",
    ];

    foreach ($patterns as $field => $filename) {
        if (!empty($only_fields) && !in_array($field, $only_fields, true)) {
            continue;
        }

        $default_id = $defaults[$field] ?? 0;
        if (!$default_id) continue;

        // Se esiste già un'immagine sulla pagina, la elimino per rigenerare pulito
        $existing = get_field($field, $post_id);
        if ($existing) {
            $old_id = is_array($existing) && isset($existing['ID'])
                ? (int) $existing['ID']
                : (int) $existing;
            if ($old_id) {
                if (!self::attachment_is_used_by_other_pages($old_id, $post_id, array_keys($patterns))) {
                    wp_delete_attachment($old_id, true);
                }
            }
        }

        $new_id = self::duplicate_attachment_with_name($default_id, $filename, $post_id);
        if ($new_id) {
            update_field($field, $new_id, $post_id);
        }
    }
}


	private static function get_web_agency_image_patterns($citta){
    if (!$citta) return [];

    $citta_slug = strtolower(trim(remove_accents($citta)));
    $citta_slug = preg_replace('/\s+/', '-', $citta_slug);

    return [
        'wa_immagine_main'          => "web-agency-{$citta_slug}.jpg",
        'wa_immagine_designer'      => "web-designer-{$citta_slug}.jpg",
        'wa_immagine_agency'        => "agenzia-web-{$citta_slug}.jpg",
        'wa_immagine_professionale' => "web-agency-professionale-{$citta_slug}.png",
        'wa_immagine_finale'        => "web-agency-chef-studio-{$citta_slug}.jpg",
        'wa_immagine_sito_1'        => "siti-web-per-artigiani-{$citta_slug}.jpg",
        'wa_immagine_sito_2'        => "siti-per-aziende-agricole-{$citta_slug}.jpg",
        'wa_immagine_sito_3'        => "siti-per-hotel-case-vacanza-{$citta_slug}.jpg",
        'wa_immagine_sito_4'        => "siti-per-aziende-{$citta_slug}.jpg",
    ];
}


	private static function get_web_agency_city_for_post($post_id){
    $citta = function_exists('get_field') ? trim((string) get_field('citta', $post_id)) : '';
    if ($citta !== '') {
        return $citta;
    }

    $value = get_the_title($post_id);
    if (!$value) {
        $value = get_post_field('post_name', $post_id);
    }

    $city = mb_strtolower(trim((string) $value), 'UTF-8');
    $city = str_replace(['-', '_', '|'], ' ', $city);

    $remove_phrases = [
        'web agency',
        'agenzia web',
        'web design',
        'web designer',
        'realizzazione siti web',
        'realizzazione sito web',
        'creazione siti web',
        'creazione sito web',
    ];

    foreach ($remove_phrases as $phrase) {
        $city = preg_replace('/\b' . preg_quote($phrase, '/') . '\b/u', ' ', $city);
    }

    $city = preg_replace('/\b(a|in|di|per|su|nel|nella|nei|nelle)\b/u', ' ', $city);
    $city = preg_replace('/\s+/u', ' ', $city);
    $city = trim($city);

    return $city !== '' ? mb_convert_case($city, MB_CASE_TITLE, 'UTF-8') : '';
}


	private static function ensure_images_wa($post_id, $defaults, $citta){
    $patterns = self::get_web_agency_image_patterns($citta);
    if (empty($patterns)) return;

    foreach ($patterns as $field => $filename) {
        $default_id = $defaults[$field] ?? 0;
        if (!$default_id) continue;

        $hidden_meta_key = '_lpg_' . $field;
        $existing = get_post_meta($post_id, $hidden_meta_key, true);
        if (!$existing) {
            $existing = get_post_meta($post_id, $field, true);
        }
        if (!$existing && function_exists('get_field')) {
            $existing = get_field($field, $post_id);
        }

        if ($existing) {
            $old_id = is_array($existing) && isset($existing['ID'])
                ? (int) $existing['ID']
                : (int) $existing;
            if ($old_id) {
                wp_delete_attachment($old_id, true);
            }
        }

        $new_id = self::duplicate_attachment_with_name($default_id, $filename, $post_id);
        if ($new_id) {
            update_post_meta($post_id, $hidden_meta_key, $new_id);
        }
    }
}




	
	
	


    private static function duplicate_attachment_with_name($attachment_id, $new_filename, $post_id){
        $src = get_attached_file($attachment_id);
        if (!$src || !file_exists($src)) return 0;

        $uploads = wp_upload_dir();
        $subdir = trailingslashit($uploads['basedir']).'seo-img';
        if (!file_exists($subdir)) wp_mkdir_p($subdir);

        $dest = trailingslashit($subdir) . sanitize_file_name($new_filename);
        $dest_url = trailingslashit($uploads['baseurl']).'seo-img/'.basename($dest);

        // Se il file esiste già, non lo ricrea
        if (!file_exists($dest)) {
            copy($src, $dest);
        }

        // Se esiste già un allegato per quell'URL, riusalo
        $existing_attach_id = attachment_url_to_postid($dest_url);
        if ($existing_attach_id) {
            return $existing_attach_id;
        }

        $filetype = wp_check_filetype(basename($dest), null);
        $attachment = [
            'guid'           => $dest_url,
            'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
            'post_title'     => pathinfo($new_filename, PATHINFO_FILENAME),
            'post_status'    => 'inherit'
        ];
        $new_id = wp_insert_attachment($attachment, $dest, $post_id);
        require_once ABSPATH.'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($new_id, $dest);
        wp_update_attachment_metadata($new_id, $attach_data);
        return $new_id;
    }

	
	
   public static function regenerate_all_images() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');

    $posts = get_posts([
        'post_type'      => 'pagine-locali',
        'numberposts'    => -1,
        'post_status'    => ['publish', 'draft'],
    ]);

    $count = 0;

    foreach ($posts as $p) {
        $post_id = $p->ID;
        $cit = get_field('citta', $post_id);
        if (!$cit) continue;

        // prendi il primo termine di argomento_categoria (seo / realizzazione-siti)
        $terms   = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'ids']);
        $term_id = $terms[0] ?? 0;
        if (!$term_id) continue;

        $term_ref = 'argomento_categoria_' . $term_id;

        $defaults = [
            'immagine_main_default'      => self::normalize_attachment_id( get_field('immagine_main_default', $term_ref) ),
            'immagine_secondary_default' => self::normalize_attachment_id( get_field('immagine_secondary_default', $term_ref) ),
            'immagine_last_default'      => self::normalize_attachment_id( get_field('immagine_last_default', $term_ref) ),
        ];

		
		error_log("🎯 Termine collegato: {$term_ref}");
error_log("📦 Default images per {$cit}: " . print_r($defaults, true));

		
		
        self::ensure_images($post_id, $defaults, $cit);
        $count++;
    }

    wp_redirect(admin_url('edit.php?post_type=pagine-locali&regen_ok=' . $count));
    exit;
}

	
	
	
	
	
	
	
public static function regenerate_all_images_rs() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');

    // Recupera solo le pagine collegate all’argomento “realizzazione-siti”
    $posts = get_posts([
        'post_type'   => 'pagine-locali',
        'numberposts' => -1,
        'post_status' => ['publish', 'draft'],
        'tax_query'   => [[
            'taxonomy' => 'argomento_categoria',
            'field'    => 'slug',
            'terms'    => ['realizzazione-siti'],
        ]],
    ]);

    $count = 0;

    foreach ($posts as $p) {
        $post_id = $p->ID;
        $cit     = get_field('citta', $post_id);
        if (!$cit) continue;

        $terms   = wp_get_post_terms($post_id, 'argomento_categoria', ['fields'=>'ids']);
        $term_id = $terms[0] ?? 0;
        if (!$term_id) continue;

        // riferimento ACF al termine
        $term_ref = 'argomento_categoria_' . $term_id;

        $defaults = self::get_realizzazione_siti_defaults($term_ref);

        self::ensure_images_rs($post_id, $defaults, $cit);
        $count++;
    }

    wp_redirect(admin_url('edit.php?post_type=pagine-locali&regen_rs_ok=' . $count));
    exit;
}


public static function render_rigenera_immagini_rs_page() {
    if ( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');

    $field_labels = self::get_realizzazione_siti_image_labels();
    $pagine = get_posts([
        'post_type'      => 'pagine-locali',
        'post_status'    => ['publish', 'draft', 'pending', 'private', 'future'],
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'argomento_categoria',
                'field'    => 'slug',
                'terms'    => ['realizzazione-siti'],
            ],
        ],
    ]);
    ?>
    <div class="wrap">
        <h1>Rigenera Immagini Realizzazione Siti</h1>
        <?php if (isset($_GET['rigenerate'])) :
            $updated = isset($_GET['count']) ? intval($_GET['count']) : 0;
            $notice_class = $updated > 0 ? 'notice-success' : 'notice-warning';
        ?>
            <div class="notice <?php echo esc_attr($notice_class); ?> is-dismissible">
                <p><?php echo $updated > 0 ? 'Rigenerazione completata: ' . esc_html($updated) . ' pagine aggiornate.' : 'Nessuna immagine rigenerata. Controlla che la pagina abbia il campo Città e che la categoria Realizzazione Siti abbia le immagini default.'; ?></p>
            </div>
        <?php endif; ?>

        <p>Seleziona una o più pagine <strong>realizzazione-siti</strong>: le immagini vengono prese dalla categoria e rinominate con il campo ACF <strong>Città</strong>.</p>

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('lpg_rigenera_immagini_rs', 'lpg_rigenera_immagini_rs_nonce'); ?>
            <input type="hidden" name="action" value="lpg_rigenera_immagini_rs">

            <h2>1. Quali immagini vuoi rigenerare?</h2>
            <p>
                <label><input type="checkbox" id="lpg-select-all-rs-images" checked> Seleziona tutte le immagini</label>
            </p>
            <div style="columns:2; max-width:760px;">
                <?php foreach ($field_labels as $field => $label) : ?>
                    <label style="display:block; margin-bottom:6px;">
                        <input type="checkbox" name="images[]" value="<?php echo esc_attr($field); ?>" checked>
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <h2>2. Pagine Locali</h2>
            <?php if ( empty($pagine) ) : ?>
                <p>Nessuna pagina Realizzazione Siti trovata.</p>
            <?php else : ?>
                <p>
                    <label><input type="checkbox" id="lpg-select-all-rs-pagine"> Seleziona tutte</label>
                </p>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="lpg-select-all-rs-pagine-top"></th>
                            <th>Titolo</th>
                            <th>Città</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagine as $page) :
                            $citta = function_exists('get_field') ? get_field('citta', $page->ID) : '';
                        ?>
                            <tr>
                                <td><input type="checkbox" name="pagine[]" value="<?php echo esc_attr($page->ID); ?>"></td>
                                <td><strong><?php echo esc_html($page->post_title); ?></strong></td>
                                <td><?php echo esc_html($citta ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <p class="description">Se l'immagine vecchia è condivisa con un'altra pagina locale, non viene eliminata dai Media.</p>
            <p><button type="submit" class="button button-primary">Rigenera immagini selezionate</button></p>
        </form>
    </div>

    <script>
    (function(){
        const allImages = document.getElementById('lpg-select-all-rs-images');
        if (allImages) {
            allImages.addEventListener('change', e => {
                document.querySelectorAll('input[name="images[]"]').forEach(cb => cb.checked = e.target.checked);
            });
        }

        const top = document.getElementById('lpg-select-all-rs-pagine-top');
        const main = document.getElementById('lpg-select-all-rs-pagine');
        if(!top || !main) return;
        function toggleAll(checked) {
            document.querySelectorAll('input[name="pagine[]"]').forEach(cb => cb.checked = checked);
        }
        top.addEventListener('change', e => toggleAll(e.target.checked));
        main.addEventListener('change', e => toggleAll(e.target.checked));
    })();
    </script>
    <?php
}


public static function handle_rigenera_immagini_rs() {
    if ( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');

    if ( ! isset($_POST['lpg_rigenera_immagini_rs_nonce']) ||
         ! wp_verify_nonce($_POST['lpg_rigenera_immagini_rs_nonce'], 'lpg_rigenera_immagini_rs') ) {
        wp_die('Nonce non valido.');
    }

    $pagine = isset($_POST['pagine']) ? (array) $_POST['pagine'] : [];
    $images = isset($_POST['images']) ? (array) $_POST['images'] : [];
    $redirect_url = admin_url('edit.php?post_type=pagine-locali&page=lpg-rigenera-immagini-rs');

    $allowed_fields = self::get_realizzazione_siti_image_fields();
    $only_fields = array_values(array_intersect($allowed_fields, $images));

    if ( empty($pagine) || empty($only_fields) ) {
        wp_redirect( add_query_arg(['rigenerate' => 0], $redirect_url) );
        exit;
    }

    $count = 0;

    foreach ($pagine as $page_id) {
        $page_id = (int) $page_id;
        if (!$page_id || get_post_type($page_id) !== 'pagine-locali') continue;

        $terms = wp_get_post_terms($page_id, 'argomento_categoria');
        if (is_wp_error($terms) || empty($terms)) continue;

        $rs_term = null;
        foreach ($terms as $term) {
            if (!empty($term->slug) && $term->slug === 'realizzazione-siti') {
                $rs_term = $term;
                break;
            }
        }
        if (!$rs_term) continue;

        $cit = function_exists('get_field') ? get_field('citta', $page_id) : '';
        if (!$cit) continue;

        $term_ref = 'argomento_categoria_' . $rs_term->term_id;
        $defaults = self::get_realizzazione_siti_defaults($term_ref, $only_fields);

        $has_default = false;
        foreach ($only_fields as $field) {
            if (!empty($defaults[$field])) {
                $has_default = true;
                break;
            }
        }

        if (!$has_default) continue;

        self::ensure_images_rs($page_id, $defaults, $cit, $only_fields);
        $count++;
    }

    wp_redirect( add_query_arg(['rigenerate' => 1, 'count' => $count], $redirect_url) );
    exit;
}


public static function regenerate_all_images_wa() {
    if (!current_user_can('manage_options')) wp_die('Permessi insufficienti.');

    $posts = get_posts([
        'post_type'   => 'pagine-locali',
        'numberposts' => -1,
        'post_status' => ['publish', 'draft'],
        'tax_query'   => [[
            'taxonomy' => 'argomento_categoria',
            'field'    => 'slug',
            'terms'    => ['web-agency'],
        ]],
    ]);

    $fields = array_keys(self::get_web_agency_image_patterns('citta'));
    $count = 0;

    foreach ($posts as $p) {
        $post_id = $p->ID;
        $cit     = self::get_web_agency_city_for_post($post_id);
        if (!$cit) continue;

        $terms   = wp_get_post_terms($post_id, 'argomento_categoria', ['fields'=>'ids']);
        $term_id = $terms[0] ?? 0;
        if (!$term_id) continue;

        $term_ref = 'argomento_categoria_' . $term_id;
        $defaults = [];

        foreach ($fields as $field) {
            $val = get_field($field, $term_ref);
            if (!$val) {
                $val = get_field($field . '_default', $term_ref);
            }

            $defaults[$field] = self::normalize_attachment_id($val);
        }

        self::ensure_images_wa($post_id, $defaults, $cit);
        $count++;
    }

    wp_redirect(admin_url('edit.php?post_type=pagine-locali&regen_wa_ok=' . $count));
    exit;
}


public static function maybe_set_web_agency_slug($post_id) {
    static $running = false;

    if ($running || !is_numeric($post_id)) {
        return;
    }

    $post_id = (int) $post_id;
    if (!$post_id || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'pagine-locali') {
        return;
    }

    $terms = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'slugs']);
    if (is_wp_error($terms) || !in_array('web-agency', $terms, true)) {
        return;
    }

    $citta = self::get_web_agency_city_for_post($post_id);
    if (!$citta) {
        return;
    }

    $target_slug = sanitize_title('web-agency-' . $citta);
    if (!$target_slug) {
        return;
    }

    $current_slug = (string) get_post_field('post_name', $post_id);
    $previous_auto_slug = (string) get_post_meta($post_id, '_lpg_wa_auto_slug', true);

    $title_slug = sanitize_title(get_the_title($post_id));
    $city_slug  = sanitize_title($citta);
    $auto_candidates = array_filter(array_unique([
        $target_slug,
        $title_slug,
        $city_slug,
        sanitize_title('realizzazione-siti-web-' . $citta),
        sanitize_title('realizzazione-sito-web-' . $citta),
        sanitize_title('web-agency-' . $citta),
    ]));

    $should_update = $current_slug === ''
        || ($previous_auto_slug !== '' && $current_slug === $previous_auto_slug)
        || ($previous_auto_slug === '' && in_array($current_slug, $auto_candidates, true));

    if (!$should_update) {
        return;
    }

    if ($current_slug === $target_slug) {
        update_post_meta($post_id, '_lpg_wa_auto_slug', $target_slug);
        self::refresh_rewrite_rules_after_web_agency_slug_change();
        return;
    }

    $running = true;
    wp_update_post([
        'ID'        => $post_id,
        'post_name' => $target_slug,
    ]);
    update_post_meta($post_id, '_lpg_wa_auto_slug', $target_slug);
    self::refresh_rewrite_rules_after_web_agency_slug_change();
    $running = false;
}


private static function refresh_rewrite_rules_after_web_agency_slug_change() {
    update_option('lpg_needs_flush', 1);

    if (function_exists('lpg_register_rewrite_rules')) {
        lpg_register_rewrite_rules();
    }

    flush_rewrite_rules(false);
}


private static function is_web_agency_post($post_id) {
    if (!$post_id || get_post_type($post_id) !== 'pagine-locali') {
        return false;
    }

    $terms = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'slugs']);
    return !is_wp_error($terms) && in_array('web-agency', $terms, true);
}


private static function is_seo_page_post($post_id) {
    if (!$post_id || get_post_type($post_id) !== 'pagine-locali') {
        return false;
    }

    $terms = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'slugs']);
    return !is_wp_error($terms) && in_array('seo', $terms, true);
}


private static function is_web_agency_term_save($acf_post_id) {
    if (!is_string($acf_post_id)) {
        return false;
    }

    if (!preg_match('/(?:argomento_categoria|term)_(\d+)$/', $acf_post_id, $matches)) {
        return false;
    }

    $term = get_term((int) $matches[1], 'argomento_categoria');
    return $term && !is_wp_error($term) && $term->slug === 'web-agency';
}


private static function render_yoast_replace_vars($value, $post_id) {
    $value = trim((string) $value);

    if ($value !== '' && function_exists('wpseo_replace_vars')) {
        return wpseo_replace_vars($value, get_post($post_id));
    }

    return $value;
}


private static function get_seo_page_metadesc_value($post_id) {
    $yoast_desc = (string) get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
    if (trim($yoast_desc) !== '') {
        return self::render_yoast_replace_vars($yoast_desc, $post_id);
    }

    if (class_exists('WPSEO_Meta')) {
        $yoast_desc = (string) WPSEO_Meta::get_value('metadesc', $post_id);
        if (trim($yoast_desc) !== '') {
            return self::render_yoast_replace_vars($yoast_desc, $post_id);
        }
    }

    $acf_desc = function_exists('get_field') ? (string) get_field('meta_description', $post_id) : '';
    if (trim($acf_desc) !== '') {
        return trim(wp_strip_all_tags($acf_desc));
    }

    $db_desc = (string) get_post_meta($post_id, 'description', true);
    if (trim($db_desc) !== '') {
        return trim(wp_strip_all_tags($db_desc));
    }

    return '';
}


public static function filter_seo_pages_yoast_metadesc($description) {
    if (!is_singular('pagine-locali')) {
        return $description;
    }

    $post_id = get_queried_object_id();
    if (!$post_id || !self::is_seo_page_post($post_id)) {
        return $description;
    }

    $seo_description = self::get_seo_page_metadesc_value($post_id);
    return $seo_description !== '' ? $seo_description : $description;
}


private static function get_web_agency_term_ref_for_post($post_id) {
    $terms = wp_get_post_terms($post_id, 'argomento_categoria');
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    foreach ($terms as $term) {
        if (!empty($term->slug) && $term->slug === 'web-agency') {
            return 'argomento_categoria_' . $term->term_id;
        }
    }

    return '';
}


private static function get_web_agency_selected_variant_for_post($post_id) {
    $variants = function_exists('lpg_wa_text_variants') ? lpg_wa_text_variants() : [];
    $page_variant = function_exists('get_field') ? (string) get_field('wa_text_variant', $post_id) : '';
    $term_ref = self::get_web_agency_term_ref_for_post($post_id);
    $default_variant = ($term_ref && function_exists('get_field')) ? (string) get_field('wa_default_variant', $term_ref) : '';

    $selected = ($page_variant === '' || $page_variant === 'inherit')
        ? ($default_variant ?: 'variant_1')
        : $page_variant;

    if ($selected === 'base') {
        return 'variant_1';
    }

    if (!empty($variants) && empty($variants[$selected])) {
        return !empty($variants['variant_1']) ? 'variant_1' : $selected;
    }

    return $selected ?: 'variant_1';
}


private static function get_web_agency_yoast_description_template($variant_key) {
    $templates = function_exists('lpg_wa_yoast_description_templates')
        ? lpg_wa_yoast_description_templates()
        : [];

    if (empty($templates)) {
        $templates = [
            'variant_1' => 'Web Agency {citta}: siti web custom, SEO e marketing digitale per migliorare presenza online, contatti e crescita del tuo progetto.',
        ];
    }

    return $templates[$variant_key] ?? $templates['variant_1'];
}


private static function get_web_agency_yoast_values($post_id) {
    $citta = self::get_web_agency_city_for_post($post_id);
    if (!$citta) {
        return [];
    }

    $variant_key = self::get_web_agency_selected_variant_for_post($post_id);

    return [
        'title'   => 'Web Agency ' . $citta . ' %%sep%% %%sitename%%',
        'desc'    => self::replace_web_agency_yoast_placeholders(
            self::get_web_agency_yoast_description_template($variant_key),
            $citta
        ),
        'variant' => $variant_key,
    ];
}


private static function render_web_agency_yoast_title($title, $post_id) {
    if (function_exists('wpseo_replace_vars')) {
        return wpseo_replace_vars($title, get_post($post_id));
    }

    return str_replace(
        ['%%sep%%', '%%sitename%%'],
        [apply_filters('wpseo_separator', '|'), get_bloginfo('name')],
        $title
    );
}


private static function replace_web_agency_yoast_placeholders($value, $citta) {
    $citta_slug = sanitize_title(remove_accents($citta));
    $citta_lowercase = function_exists('mb_strtolower') ? mb_strtolower($citta, 'UTF-8') : strtolower($citta);

    return str_replace(
        ['{citta}', '{città}', '{citta_lowercase}', '{citta_slug}'],
        [$citta, $citta, $citta_lowercase, $citta_slug],
        (string) $value
    );
}


private static function maybe_update_auto_yoast_meta($post_id, $meta_key, $auto_meta_key, $new_value) {
    $current = (string) get_post_meta($post_id, $meta_key, true);
    $previous_auto = (string) get_post_meta($post_id, $auto_meta_key, true);

    $can_update = trim($current) === ''
        || ($previous_auto !== '' && $current === $previous_auto)
        || $current === $new_value;

    if (!$can_update) {
        return false;
    }

    update_post_meta($post_id, $meta_key, $new_value);
    update_post_meta($post_id, $auto_meta_key, $new_value);

    return true;
}


public static function maybe_update_web_agency_yoast($post_id) {
    if (!is_numeric($post_id)) {
        if (self::is_web_agency_term_save($post_id)) {
            self::sync_web_agency_yoast_posts();
        }

        return false;
    }

    $post_id = (int) $post_id;
    if (!$post_id || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return false;
    }

    if (!self::is_web_agency_post($post_id)) {
        return false;
    }

    $values = self::get_web_agency_yoast_values($post_id);
    if (empty($values)) {
        return false;
    }

    $updated_title = self::maybe_update_auto_yoast_meta(
        $post_id,
        '_yoast_wpseo_title',
        '_lpg_wa_auto_yoast_title',
        $values['title']
    );

    $updated_desc = self::maybe_update_auto_yoast_meta(
        $post_id,
        '_yoast_wpseo_metadesc',
        '_lpg_wa_auto_yoast_desc',
        $values['desc']
    );

    update_post_meta($post_id, '_lpg_wa_auto_yoast_variant', $values['variant']);

    return $updated_title || $updated_desc;
}


private static function should_use_auto_yoast_meta($post_id, $meta_key, $auto_meta_key, $auto_value) {
    $current = (string) get_post_meta($post_id, $meta_key, true);
    $previous_auto = (string) get_post_meta($post_id, $auto_meta_key, true);

    return trim($current) === ''
        || ($previous_auto !== '' && $current === $previous_auto)
        || $current === $auto_value;
}


private static function get_queried_web_agency_post_id() {
    if (!is_singular('pagine-locali')) {
        return 0;
    }

    $post_id = get_queried_object_id();
    return $post_id && self::is_web_agency_post($post_id) ? (int) $post_id : 0;
}


public static function filter_web_agency_yoast_title($title) {
    $post_id = self::get_queried_web_agency_post_id();
    if (!$post_id) {
        return $title;
    }

    $values = self::get_web_agency_yoast_values($post_id);
    if (empty($values['title'])) {
        return $title;
    }

    return self::should_use_auto_yoast_meta($post_id, '_yoast_wpseo_title', '_lpg_wa_auto_yoast_title', $values['title'])
        ? self::render_web_agency_yoast_title($values['title'], $post_id)
        : $title;
}


public static function filter_web_agency_yoast_metadesc($description) {
    $post_id = self::get_queried_web_agency_post_id();
    if (!$post_id) {
        return $description;
    }

    $values = self::get_web_agency_yoast_values($post_id);
    if (empty($values['desc'])) {
        return $description;
    }

    return self::should_use_auto_yoast_meta($post_id, '_yoast_wpseo_metadesc', '_lpg_wa_auto_yoast_desc', $values['desc'])
        ? $values['desc']
        : $description;
}


private static function sync_web_agency_yoast_posts() {
    $posts = get_posts([
        'post_type'      => 'pagine-locali',
        'post_status'    => ['publish', 'draft', 'pending', 'private', 'future'],
        'numberposts'    => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'argomento_categoria',
                'field'    => 'slug',
                'terms'    => ['web-agency'],
            ],
        ],
    ]);

    $updated = 0;
    foreach ($posts as $post_id) {
        if (self::maybe_update_web_agency_yoast($post_id)) {
            $updated++;
        }
    }

    return $updated;
}


public static function sync_all_yoast_wa() {
    $updated = self::sync_web_agency_yoast_posts();

    wp_redirect(admin_url('edit.php?post_type=pagine-locali&sync_yoast_wa_ok=' . $updated));
    exit;
}


	
	
	
	
	
	
	
	
	


    private static function parse_sql($path, $table){
        $sql = file_get_contents($path);
        if (!$sql) return [];
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = preg_replace('/--.*?\n/', '', $sql);
        $sql = preg_replace('/LOCK TABLES.+?;/is', '', $sql);
        $sql = preg_replace('/UNLOCK TABLES;/is', '', $sql);
        $blocks = preg_split('/(?=INSERT\s+INTO\s+)/i', $sql);
        $rows = [];
        foreach ($blocks as $block) {
            if (stripos($block,'INSERT INTO')===false) continue;
            if (!preg_match('/INSERT\s+INTO\s+`?([a-zA-Z0-9_\-]+)`?/i',$block,$m)) continue;
            $table_found=trim($m[1]);
            if($table&&$table_found!==$table)continue;
            if(!preg_match('/\(([^)]+)\)\s*VALUES/i',$block,$cm))continue;
            $cols=array_map(fn($c)=>strtolower(trim(str_replace(['`','"'],'',$c))),explode(',',$cm[1]));
            $after=preg_replace('/.*?VALUES\s*/is','',$block);
            $after=trim($after,'; ');
            $tuples=preg_split('/\),\s*\(/',$after);
            foreach($tuples as $tuple){
                $tuple=trim($tuple,'() ');
                $vals=self::split_values($tuple);
                if(count($vals)!=count($cols))continue;
                $r=[]; foreach($cols as $i=>$c){$r[$c]=\LPG_Helpers::unquote($vals[$i]);}
                if(!empty($r['citta']))$rows[]=$r;
            }
        }
        return $rows;
    }

    private static function split_values($s){
        $vals=[];$buf='';$in=false;$len=strlen($s);
        for($i=0;$i<$len;$i++){
            $ch=$s[$i];
            if($ch==="'"&&($i==0||$s[$i-1]!=='\\'))$in=!$in;
            if(!$in&&$ch===','){ $vals[]=trim($buf);$buf='';continue;}
            $buf.=$ch;
        }
        if(trim($buf)!=='')$vals[]=trim($buf);
        return $vals;
    }

    private static function field_map(){
        $keys = [
            'main_keyword','meta_title','meta_description','h1','h2_hero', 'paragrafo_hero',
            'titolo_chi_siamo','testo_chi_siamo','titolo_elenco_chi_siamo','elenco_chi_siamo','frase_dopo_elenco',
            'testo_bottone_call_to_action',
            'titolo_successi','sottotitolo_successi','paragrafo_successi_1','titolo_tra_paragrafi_successi','paragrafo_successi_2',
            'titolo_servizi','titolo_servizio_1','testo_servizio_1','titolo_servizio_2','testo_servizio_2','titolo_servizio_3','testo_servizio_3','titolo_servizio_4','testo_servizio_4',
            'paragrafi_titolo_1','paragrafi_testo_1','paragrafi_titolo_2','paragrafi_testo_2',
            'titolo_casi_studio','titolo_caso_studio_1','testo_caso_studio_1','titolo_caso_studio_2','testo_caso_studio_2',
            'titolo_risultati','sottotitolo_risultati','testo_risultato_1','testo_risultato_2','testo_risultato_3','testo_risultato_4',
            'titolo_dicono_di_noi',
            'titolo_faq','titolo_form','sottotitolo_form','indicazioni_form','proseguimento_indicazioni_form'
        ];
        $out=[]; foreach($keys as $k){ $out[$k]=$k; } return $out;
    }
	
	
	public static function render_rigenera_immagini_page() {
    if ( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');

    // Recupera le pagine SEO/generiche. Realizzazione Siti e Web Agency hanno
    // pulsanti dedicati, quindi qui le escludiamo per evitare sovrascritture.
    $pagine = get_posts([
        'post_type'      => 'pagine-locali',
        'post_status'    => ['publish', 'draft', 'pending', 'private', 'future'],
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'argomento_categoria',
                'field'    => 'slug',
                'terms'    => ['realizzazione-siti', 'web-agency'],
                'operator' => 'NOT IN',
            ],
        ],
    ]);
    ?>
    <div class="wrap">
        <h1>Rigenera Immagini SEO</h1>
        <?php if (isset($_GET['rigenerate'])) :
            $updated = isset($_GET['count']) ? intval($_GET['count']) : 0;
            $notice_class = $updated > 0 ? 'notice-success' : 'notice-warning';
        ?>
            <div class="notice <?php echo esc_attr($notice_class); ?> is-dismissible">
                <p><?php echo $updated > 0 ? 'Rigenerazione completata: ' . esc_html($updated) . ' pagine aggiornate.' : 'Nessuna immagine rigenerata. Controlla che la pagina abbia categoria, campo Città e immagini default nella categoria.'; ?></p>
            </div>
        <?php endif; ?>
        <p>Seleziona una o più pagine: le immagini vengono prese dalla categoria della pagina e rinominate con il campo ACF <strong>Città</strong>.</p>

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('lpg_rigenera_immagini', 'lpg_rigenera_immagini_nonce'); ?>
            <input type="hidden" name="action" value="lpg_rigenera_immagini">

            <h2>1. Quali immagini vuoi rigenerare?</h2>
            <p>
                <label><input type="checkbox" name="images[]" value="main" checked> Immagine principale</label><br>
                <label><input type="checkbox" name="images[]" value="secondary" checked> Immagine secondaria</label><br>
                <label><input type="checkbox" name="images[]" value="last" checked> Immagine finale</label>
            </p>

            <h2>2. Pagine Locali</h2>
            <?php if ( empty($pagine) ) : ?>
                <p>Nessuna pagina trovata.</p>
            <?php else : ?>
                <p>
                    <label><input type="checkbox" id="lpg-select-all-pagine"> Seleziona tutte</label>
                </p>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="lpg-select-all-pagine-top"></th>
                            <th>Titolo</th>
                            <th>Città</th>
                            <th>Argomento attuale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagine as $page) : 
                            $citta = function_exists('get_field') ? get_field('citta', $page->ID) : '';
                            $terms = wp_get_post_terms($page->ID, 'argomento_categoria');
                            $term_names = (!is_wp_error($terms) && !empty($terms))
                                ? implode(', ', wp_list_pluck($terms, 'name'))
                                : '-';
                        ?>
                            <tr>
                                <td><input type="checkbox" name="pagine[]" value="<?php echo esc_attr($page->ID); ?>"></td>
                                <td><strong><?php echo esc_html($page->post_title); ?></strong></td>
                                <td><?php echo esc_html($citta ?: '-'); ?></td>
                                <td><?php echo esc_html($term_names); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <p class="description">Le immagini selezionate vengono sostituite nei campi della pagina. Se l'immagine vecchia è condivisa con un'altra pagina locale, non viene eliminata dai Media.</p>

            <p><button type="submit" class="button button-primary">Rigenera immagini selezionate</button></p>
        </form>
    </div>

    <script>
    (function(){
        const top = document.getElementById('lpg-select-all-pagine-top');
        const main = document.getElementById('lpg-select-all-pagine');
        if(!top || !main) return;
        function toggleAll(checked) {
            document.querySelectorAll('input[name="pagine[]"]').forEach(cb => cb.checked = checked);
        }
        top.addEventListener('change', e => toggleAll(e.target.checked));
        main.addEventListener('change', e => toggleAll(e.target.checked));
    })();
    </script>
    <?php
}



	
	
	public static function handle_rigenera_immagini() {
    if ( ! current_user_can('manage_options') ) wp_die('Permessi insufficienti.');

    if ( ! isset($_POST['lpg_rigenera_immagini_nonce']) ||
         ! wp_verify_nonce($_POST['lpg_rigenera_immagini_nonce'], 'lpg_rigenera_immagini') ) {
        wp_die('Nonce non valido.');
    }

    $pagine = isset($_POST['pagine']) ? (array) $_POST['pagine'] : [];
    $images = isset($_POST['images']) ? (array) $_POST['images'] : [];
    $redirect_url = admin_url('edit.php?post_type=pagine-locali&page=lpg-rigenera-immagini');

    if ( empty($pagine) || empty($images) ) {
        wp_redirect( add_query_arg(['rigenerate' => 0], $redirect_url) );
        exit;
    }

    $field_map = [
        'main'      => 'immagine_main',
        'secondary' => 'immagine_secondary',
        'last'      => 'immagine_last',
    ];

    $only_fields = [];
    foreach ($images as $key) {
        if (isset($field_map[$key])) {
            $only_fields[] = $field_map[$key];
        }
    }

    if (empty($only_fields)) {
        wp_redirect( add_query_arg(['rigenerate' => 0], $redirect_url) );
        exit;
    }

    $conteggio = 0;
    $excluded_slugs = ['realizzazione-siti', 'web-agency'];

    foreach ( $pagine as $page_id ) {
        $page_id = (int) $page_id;
        if ( ! $page_id ) continue;
        if (get_post_type($page_id) !== 'pagine-locali') continue;

        $terms = wp_get_post_terms($page_id, 'argomento_categoria');
        if (is_wp_error($terms) || empty($terms)) continue;

        $term_slugs = wp_list_pluck($terms, 'slug');
        if (array_intersect($excluded_slugs, $term_slugs)) continue;

        $term_ref = 'argomento_categoria_' . $terms[0]->term_id;
        $cit = function_exists('get_field') ? get_field('citta', $page_id) : '';
        if (!$cit) continue;

        $defaults = [
            'immagine_main_default'      => self::normalize_attachment_id( get_field('immagine_main_default', $term_ref) ),
            'immagine_secondary_default' => self::normalize_attachment_id( get_field('immagine_secondary_default', $term_ref) ),
            'immagine_last_default'      => self::normalize_attachment_id( get_field('immagine_last_default', $term_ref) ),
        ];

        $has_default = false;
        foreach ($only_fields as $field) {
            if (!empty($defaults[$field . '_default'])) {
                $has_default = true;
                break;
            }
        }

        if (!$has_default) continue;

        self::ensure_images($page_id, $defaults, $cit, $only_fields);
        $conteggio++;
    }

    wp_redirect( add_query_arg(['rigenerate' => 1, 'count' => $conteggio], $redirect_url) );
    exit;
}



	
	
	
	
	
	
	
	
	
	public static function render_import_rs_html_page() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('Permessi insufficienti.');
    }

    $dir = defined('LPG_RS_HTML_DIR') ? LPG_RS_HTML_DIR : '';
    $files = [];
    if ( $dir && is_dir($dir) ) {
        foreach (glob($dir . '/*.html') as $file) {
            $files[] = basename($file);
        }
    }
    ?>
    <div class="wrap">
        <h1>Importa HTML – Realizzazione Siti Web</h1>

        <p>Metti tutti i file <code>.html</code> in questa cartella sul server:</p>
        <pre><?php echo esc_html( WP_CONTENT_DIR . '/uploads/realizzazione-siti-html' ); ?></pre>

        <?php if ( empty($files) ) : ?>
            <p><strong>Non è stato trovato nessun file HTML.</strong></p>
        <?php else : ?>
            <h2>File trovati (<?php echo count($files); ?>):</h2>
            <ul>
                <?php foreach ($files as $f) : ?>
                    <li><?php echo esc_html($f); ?></li>
                <?php endforeach; ?>
            </ul>

            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                <?php wp_nonce_field('lpg_import_rs_html', 'lpg_import_rs_html_nonce'); ?>
                <input type="hidden" name="action" value="lpg_import_rs_html">

                <p>
                    <label>
                        Argomento "Realizzazione Siti":
                        <input type="text" name="argomento_title" value="Realizzazione Siti" style="width:250px;">
                        <br><small>Deve corrispondere al titolo dell'Argomento (post type <code>argomento-seo</code>).</small>
                    </label>
                </p>

                <p>
                    <button type="submit" class="button button-primary">
                        📥 Importa tutti gli HTML come Pagine Locali
                    </button>
                </p>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

	
	
	
	
	
	


	
	
	
	
	


	
	
}




// 🔧 Forza automaticamente il template per le pagine della categoria "Realizzazione Siti"
add_action('save_post_pagine-locali', function($post_id){
    if (wp_is_post_revision($post_id)) return;

    $terms = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'slugs']);
    if (in_array('realizzazione-siti', $terms, true)) {
        update_post_meta($post_id, '_wp_page_template', 'pagine/template-pagine-realizzazione-siti.php');

    }
}, 20);


// Forza automaticamente il template per le pagine della categoria "Web Agency".
add_action('save_post_pagine-locali', function($post_id){
    if (wp_is_post_revision($post_id)) return;

    $terms = wp_get_post_terms($post_id, 'argomento_categoria', ['fields' => 'slugs']);
    if (in_array('web-agency', $terms, true)) {
        update_post_meta($post_id, '_wp_page_template', 'pagine/template-pagine-web-agency.php');
    }
}, 20);









add_action('admin_notices', function() {
    // Mostra messaggio dopo la rigenerazione immagini
    if ( isset($_GET['regen_ok']) && is_admin() ) {
        $count = intval($_GET['regen_ok']);
        if ($count > 0) {
            echo '<div class="notice notice-success is-dismissible">
                <p>✅ Rigenerazione completata: ' . $count . ' pagine aggiornate con successo.</p>
            </div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible">
                <p>⚠️ Nessuna immagine rigenerata.</p>
            </div>';
        }
    }
});












add_action('admin_notices', function() {
    if ( isset($_GET['page']) && $_GET['page'] === 'lpg-import-rs-html' && isset($_GET['importati']) ) {
        $n = intval($_GET['importati']);
        if ($n > 0) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Import completato: ' . $n . ' pagine create/aggiornate.</p></div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible"><p>Nessuna pagina importata. Controlla i file HTML e la cartella.</p></div>';
        }
    }
});







add_action('admin_notices', function() {
    if ( isset($_GET['regen_rs_ok']) && is_admin() ) {
        $count = intval($_GET['regen_rs_ok']);
        echo '<div class="notice notice-success is-dismissible">
            <p>✅ Rigenerazione immagini Realizzazione Siti completata: ' . $count . ' pagine aggiornate.</p>
        </div>';
    }
});


add_action('admin_notices', function() {
    if ( isset($_GET['regen_wa_ok']) && is_admin() ) {
        $count = intval($_GET['regen_wa_ok']);
        echo '<div class="notice notice-success is-dismissible">
            <p>✅ Rigenerazione immagini Web Agency completata: ' . $count . ' pagine aggiornate.</p>
        </div>';
    }

    if ( isset($_GET['sync_yoast_wa_ok']) && is_admin() ) {
        $count = intval($_GET['sync_yoast_wa_ok']);
        echo '<div class="notice notice-success is-dismissible">
            <p>✅ SEO Yoast Web Agency sincronizzata: ' . $count . ' pagine aggiornate.</p>
        </div>';
    }
});







add_action('acf/save_post', function($post_id){
    $post_type = get_post_type($post_id);
    if ($post_type === 'pagine-locali') {
        error_log("🧩 acf/save_post attivo per $post_id (tipo: {$post_type})");
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $found = false;
        foreach ($trace as $t) {
            if (!empty($t['file']) && str_contains($t['file'], 'lpg')) {
                $found = true;
                error_log("   ↳ chiamato da " . $t['file'] . ' : ' . ($t['line'] ?? ''));
            }
        }
        if (!$found) {
            error_log("   ⚠️ nessun riferimento a file del plugin LPG nella trace");
        }
    }
}, 1);








add_action('init', function() {
    global $wp_filter;
    if (empty($wp_filter['save_post'])) {
        error_log('🚨 Nessun hook save_post attivo! Qualcosa li ha rimossi.');
    } else {
        error_log('✅ save_post attivo con ' . count($wp_filter['save_post']->callbacks, COUNT_RECURSIVE) . ' callback');
    }
}, 100);
