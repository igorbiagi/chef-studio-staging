<?php
// =====================================================
// IMPORTA FILE HTML COME CPT "Progetti"
// Evita duplicati completi (progetto + media)
// Logica thumb: 
// 1. immagine dentro itemprop="description"
// 2. immagine tra itemprop="description" e <div class="espositore-img">
// 3. prima immagine in <div class="espositore-img">
// =====================================================

require_once( dirname( __FILE__, 4 ) . '/wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );

set_time_limit(0);

$base_url = 'https://www.chefstudio.it/';
$dir = get_stylesheet_directory() . '/import/';
$redirect_file = $dir . 'redirects.txt';
$files = glob( $dir . '*.html' );
$redirects = [];

foreach ($files as $file_path) {

    $html = file_get_contents($file_path);
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // ===== META TITLE & DESCRIPTION =====
    $meta_title = '';
    $meta_desc  = '';
    $title_tag = $dom->getElementsByTagName('title')->item(0);
    if ($title_tag) $meta_title = trim($title_tag->textContent);

    foreach ($dom->getElementsByTagName('meta') as $meta) {
        if ($meta->getAttribute('name') === 'description') {
            $meta_desc = $meta->getAttribute('content');
            break;
        }
    }

    // ===== H1 come titolo =====
    $h1_node = $xpath->query('//h1')->item(0);
    $post_title = $h1_node ? trim($h1_node->textContent) : '(senza titolo)';

    // ===== EVITA DUPLICATI SUBITO (PRIMA DI QUALSIASI IMPORT MEDIA) =====
    $existing = get_page_by_title($post_title, OBJECT, 'progetti');
    if ($existing) {
        echo "⏭️ Già importato: $post_title<br>";
        continue;
    }

    // ===== Contenuto principale =====
    $desc_node = $xpath->query('//p[@itemprop="description"]')->item(0);
    if (!$desc_node) {
        echo "⚠️ Nessun <p itemprop='description'> trovato in $file_path<br>";
        continue;
    }
    $content_raw = $dom->saveHTML($desc_node);
    $content_raw = preg_replace('/^<p[^>]*>|<\/p>$/i', '', $content_raw);
    $content = preg_replace('/(<br\s*\/?>\s*){2,}/i', "</p><p>", $content_raw);
    $content = '<p>' . trim($content) . '</p>';

    // ===== 1. Immagine dentro itemprop="description" =====
    $thumb_src = null;
    $desc_imgs = $xpath->query('//p[@itemprop="description"]//img');
    if ($desc_imgs->length > 0) {
        $thumb_src = $desc_imgs->item(0)->getAttribute('src');
    }

    // ===== 2. Se non trovata, immagine dopo <p itemprop="description"> ma prima di <div class="espositore-img"> =====
    if (!$thumb_src) {
        $candidate_imgs = $xpath->query('//p[@itemprop="description"]/following::img');
        if ($candidate_imgs->length > 0) {
            foreach ($candidate_imgs as $img) {
                $parent = $img->parentNode;
                $found_expositor = false;
                while ($parent) {
                    if ($parent->nodeType === XML_ELEMENT_NODE && stripos($parent->getAttribute('class'), 'espositore-img') !== false) {
                        $found_expositor = true;
                        break;
                    }
                    $parent = $parent->parentNode;
                }
                if (!$found_expositor) {
                    $thumb_src = $img->getAttribute('src');
                    break;
                } else {
                    break;
                }
            }
        }
    }

    // ===== GESTIONE IMMAGINI (solo dentro <div class="espositore-img">) =====
    $first_image_id = null;
    $gallery_ids = [];
    $img_nodes = $xpath->query('//div[contains(@class,"espositore-img")]//img');

    // Funzione per importare immagine
    $import_image = function($src) use ($base_url, &$redirects, &$slug) {
        if (!$src) return null;
        $old_src = $src;
        if (!preg_match('#^https?://#i', $src)) {
            $src = rtrim($base_url, '/') . '/' . ltrim($src, '/');
        }

        // Aggiunge prefisso slug per evitare conflitti di nomi simili
        $filename = sanitize_title($slug) . '-' . basename($src);

        // Import con controllo errori
        $attachment = get_page_by_title($filename, OBJECT, 'attachment');
        if ($attachment) {
            $new_url = wp_get_attachment_url($attachment->ID);
            $id = $attachment->ID;
        } else {
            $tmp = download_url($src);
            if (is_wp_error($tmp)) return null;
            $file_array = ['name' => $filename, 'tmp_name' => $tmp];
            $id = media_handle_sideload($file_array, 0);
            if (is_wp_error($id)) {
                @unlink($tmp);
                return null;
            }
            $new_url = wp_get_attachment_url($id);

            // pulizia del titolo e alt (minuscolo senza trattini)
            $title_clean = preg_replace('/[-_]+/', ' ', pathinfo($filename, PATHINFO_FILENAME));
            $title_clean = trim(strtolower($title_clean));
            wp_update_post(['ID' => $id, 'post_title' => $title_clean]);
            update_post_meta($id, '_wp_attachment_image_alt', $title_clean);
        }

        if (!preg_match('#^https?://#i', $old_src)) {
            $old_url = rtrim($base_url, '/') . '/' . ltrim($old_src, '/');
            $redirects[] = "Redirect 301 $old_url $new_url";
        }
        return $id;
    };

    // Import thumb (1° o 2° regola)
    if ($thumb_src) {
        $first_image_id = $import_image($thumb_src);
    }

    // Import immagini del div
    if ($img_nodes->length > 0) {
        foreach ($img_nodes as $index => $img) {
            $id = $import_image($img->getAttribute('src'));
            if (!$id) continue;
            if (!$first_image_id && $index === 0) {
                $first_image_id = $id;
            } else {
                $gallery_ids[] = $id;
            }
        }
    }

    // ===== SLUG =====
    $slug = basename($file_path, '.html');

    // ===== CREA POST =====
    $post_id = wp_insert_post([
        'post_title'   => wp_strip_all_tags($post_title),
        'post_name'    => sanitize_title($slug),
        'post_content' => $content,
        'post_status'  => 'draft',
        'post_type'    => 'progetti'
    ]);

    if ($post_id && !is_wp_error($post_id)) {
        // ===== Yoast SEO =====
        $clean_title = preg_replace('/\s*[-|–|—]?\s*Chef\s*Studio\s*$/i', '', $meta_title);
        $clean_title = trim($clean_title);
        $yoast_title_final = $clean_title . ' %%sep%% %%sitename%%';
        update_post_meta($post_id, '_yoast_wpseo_title', $yoast_title_final);
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_desc);

        // Thumb
        if ($first_image_id) set_post_thumbnail($post_id, $first_image_id);
        // Galleria
        if (!empty($gallery_ids)) update_field('galleria_immagini', $gallery_ids, $post_id);

        echo "✅ Importato: $post_title (slug: $slug) → ID $post_id<br>";
    } else {
        echo "❌ Errore su $file_path<br>";
    }
}

// ===== REDIRECT FILE =====
if (!empty($redirects)) {
    $existing_redirects = file_exists($redirect_file) ? file($redirect_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $merged = array_unique(array_merge($existing_redirects, $redirects));
    sort($merged);
    file_put_contents($redirect_file, implode("\n", $merged) . "\n");
    echo "<br>📁 Redirect aggiornati in: $redirect_file<br>";
}

echo "<hr>🎯 Import completato.";
