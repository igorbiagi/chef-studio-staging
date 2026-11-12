<?php
/**
 * Plugin Name: Chef Studio - Lettura campi ACF via REST (solo staging)
 * Description: Permette agli amministratori di leggere la definizione di un campo ACF (con i sottocampi) via API REST. Solo lettura. Serve a progettare nuovi layout senza toccare il database.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('chef/v1', '/acf-campo', [
        'methods'             => 'GET',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'callback'            => function (WP_REST_Request $req) {
            if (!function_exists('acf_get_field')) {
                return new WP_Error('acf_assente', 'ACF non attivo', ['status' => 500]);
            }
            $nome = sanitize_text_field((string) $req->get_param('nome'));
            $campo = acf_get_field($nome);
            if (!$campo) {
                return new WP_Error('campo_assente', 'Campo non trovato', ['status' => 404]);
            }
            $gruppo = null;
            if (!empty($campo['parent'])) {
                $g = acf_get_field_group($campo['parent']);
                if ($g) {
                    $gruppo = ['key' => $g['key'], 'title' => $g['title'], 'location' => $g['location']];
                }
            }
            return ['campo' => $campo, 'gruppo' => $gruppo];
        },
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('chef/v1', '/acf-meta', [
        'methods'             => 'GET',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'callback'            => function (WP_REST_Request $req) {
            $id = (int) $req->get_param('id');
            $filtro = sanitize_text_field((string) $req->get_param('filtro'));
            if ($req->get_param('campo')) {
                $righe = get_field(sanitize_text_field((string) $req->get_param('campo')), $id);
                $riga = (int) $req->get_param('riga');
                $f = acf_get_field('layout_flessibile_1');
                return ['riga' => $righe[$riga] ?? null, 'layouts' => array_map(function ($l) { return $l['name'] . ':' . count($l['sub_fields']); }, array_values($f['layouts'] ?? [])), 'acf' => defined('ACF_VERSION') ? ACF_VERSION : ''];
            }
            $out = [];
            foreach (get_post_meta($id) as $k => $v) {
                if ($filtro === '' || strpos($k, $filtro) !== false) {
                    $out[$k] = maybe_unserialize($v[0]);
                }
            }
            ksort($out);
            return $out;
        },
    ]);
});
