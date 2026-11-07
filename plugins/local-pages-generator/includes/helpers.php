<?php
if ( ! defined('ABSPATH') ) exit;

class LPG_Helpers {

    /**
     * Pulisce il testo in modo NON distruttivo:
     * - trim spazi all'inizio/fine
     * - normalizza CRLF in LF
     * - mantiene gli a capo interni (perché ti servono nei testi)
     */
    public static function clean_text( $v ) {
        if ($v === null) return '';

        // Prima convertiamo eventuali sequenze escape letterali tipo "\r\n" in veri a capo
        // (in caso qualcosa sia scappato da unquote)
        $v = str_replace(
            array('\\r\\n', '\\n', '\\r'),
            "\n",
            $v
        );

        // Poi normalizziamo eventuali CRLF veri in LF
        $v = str_replace(array("\r\n", "\r"), "\n", $v);

        // Trim globale
        $v = trim($v);

        // Se vuoi, puoi fare un "trim" per riga ma senza togliere gli a capo:
        $lines = explode("\n", $v);
        $lines = array_map('trim', $lines);
        $v = implode("\n", $lines);

        return $v;
    }

    /**
     * Converte un valore "quoted" da SQL in stringa PHP pulita:
     * - toglie apici esterni
     * - decodifica sequenze escape (\r\n, \n, \t, \', \\)
     */
    public static function unquote($v){
        if ($v === null) return '';

        $v = trim($v);

        // NULL SQL
        if (strtoupper($v) === 'NULL') {
            return '';
        }

        // Rimuove apici iniziali/finali se presenti
        if ($v !== '' && $v[0] === "'" && substr($v, -1) === "'") {
            $v = substr($v, 1, -1);
        }

        // Decodifica sequenze di escape provenienti dal dump SQL
        $v = str_replace(
            array('\\r\\n', '\\n', '\\r', '\\t', '\\0', '\\Z', '\\\\', "\\'"),
            array("\n",     "\n",  "\n",  "\t",  "\0",  "\x1A","\\",  "'"),
            $v
        );

        // NIENTE str_replace('rn', ...) QUI!
        // Altrimenti "fornendo" diventa "foendo".

        return $v;
    }

    /**
     * Piccolo helper per logging debug
     */
    public static function log($msg){
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('LPG: ' . $msg);
        }
    }
}
