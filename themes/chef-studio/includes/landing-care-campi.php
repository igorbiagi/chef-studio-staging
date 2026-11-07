<?php
if (!defined('ABSPATH')) exit;

/*
 * Campi ACF della pagina "Chef Studio Care" (pagine/landing-care.php).
 * Stesso sistema della landing bonifica: icone e grafica condivise da includes/landing-bonifica-campi.php
 * e css/landing-bonifica.css. Finché un campo non è mai stato salvato, si vedono i testi predefiniti qui sotto.
 */

function chefstudio_lcare_default($name = null) {
    static $d = null;
    if ($d === null) {
        $d = [
            'lcare_telefono'        => '352 0996 996',
            'lcare_whatsapp_numero' => '393520996996',
            'lcare_whatsapp_testo'  => 'Ciao, vorrei informazioni sulla manutenzione del mio sito WordPress: ',

            'lcare_badge'       => 'Chef Studio Care',
            'lcare_h1'          => 'Manutenzione WordPress:',
            'lcare_h1_evidenza' => 'il tagliando del tuo sito.',
            'lcare_lead'        => 'Aggiornamenti, sicurezza e backup, fatti da chi conosce il tuo sito. Un prezzo annuale fisso, con ore di assistenza incluse.',
            'lcare_proof'       => [
                ['icona' => 'euro', 'forte' => 'Da 49 € al mese', 'testo' => 'circa 1,62 € al giorno, piano annuale'],
                ['icona' => 'clean', 'forte' => 'Sicurezza inclusa', 'testo' => 'firewall, scansioni anti-malware, 2FA'],
                ['icona' => 'clock', 'forte' => 'Ore incluse', 'testo' => 'da 2 a 4 ore di modifiche all\'anno'],
            ],
            'lcare_bottone_piani' => 'Vedi i piani',
            'lcare_micro'         => 'Funziona anche se il sito non l\'abbiamo fatto noi.',

            'lcare_form_kicker' => 'Controllo iniziale del sito',
            'lcare_form_titolo' => 'Quale piano ti serve?',
            'lcare_form_testo'  => 'Lasciaci l\'indirizzo del sito: lo guardiamo e ti consigliamo il piano giusto.',

            'lcare_trust' => [
                ['forte' => '5 su 5', 'testo' => 'su Google'],
                ['forte' => '15+ anni', 'testo' => 'su siti WordPress'],
                ['forte' => 'Un referente', 'testo' => 'che conosce già il tuo sito'],
                ['forte' => 'Pietrasanta (LU)', 'testo' => 'studio italiano, fattura italiana'],
            ],

            'lcare_cosa_eyebrow' => 'Cosa facciamo',
            'lcare_cosa_titolo'  => 'Tutto quello che un sito WordPress chiede, ogni mese.',
            'lcare_cosa' => [
                ['icona' => 'refresh', 'titolo' => 'Aggiornamenti', 'testo' => 'WordPress, plugin e tema sempre aggiornati, controllando che il sito funzioni dopo ogni aggiornamento.'],
                ['icona' => 'pulse', 'titolo' => 'Monitoraggio', 'testo' => 'Teniamo d\'occhio vulnerabilità note e uptime: se il sito va giù, lo sappiamo noi prima dei tuoi clienti.'],
                ['icona' => 'clean', 'titolo' => 'Firewall e anti-malware', 'testo' => 'Firewall attivo e scansioni periodiche per bloccare gli attacchi più comuni.'],
                ['icona' => 'key', 'titolo' => '2FA e utenti admin', 'testo' => 'Accesso con verifica in due passaggi e controllo degli amministratori.'],
                ['icona' => 'backup', 'titolo' => 'Backup e ripristino', 'testo' => 'Backup regolari e supporto al ripristino se qualcosa va storto.'],
                ['icona' => 'tools', 'titolo' => 'Ore di assistenza', 'testo' => 'Modifiche e piccoli interventi con le ore incluse nel piano, senza preventivi ogni volta.'],
            ],

            'lcare_piani_eyebrow' => 'I piani',
            'lcare_piani_titolo'  => 'Scegli in base a cosa fa il tuo sito.',
            'lcare_piani' => [
                [
                    'nome' => 'Care', 'per_chi' => 'Per piccoli siti WordPress',
                    'prezzo' => '49 €', 'periodo' => 'al mese', 'equivale' => '590 €/anno · circa 1,62 € al giorno',
                    'risparmio' => 'Risparmi 130 €', 'rate' => 'oppure 2 rate semestrali da 360 €', 'ore' => '2 ore di intervento incluse',
                    'voci' => "Aggiornamenti di WordPress, plugin e tema\nMonitoraggio vulnerabilità e uptime\nFirewall e scansioni anti-malware\n2FA e controllo utenti admin\nBackup e supporto al ripristino",
                    'evidenza' => false, 'etichetta' => '', 'bottone' => 'Scegli Care',
                ],
                [
                    'nome' => 'Care Plus', 'per_chi' => 'Per siti aziendali e lead generation',
                    'prezzo' => '74 €', 'periodo' => 'al mese', 'equivale' => '890 €/anno · circa 2,44 € al giorno',
                    'risparmio' => 'Risparmi 190 €', 'rate' => 'oppure 2 rate semestrali da 540 €', 'ore' => '3 ore di intervento incluse',
                    'voci' => "Tutto di Care, più:\nVerifiche periodiche del funzionamento\nControllo di moduli ed email\nAvvisi prioritari\nIntervento più esteso in caso di attacco",
                    'evidenza' => true, 'etichetta' => 'Consigliato', 'bottone' => 'Scegli Care Plus',
                ],
                [
                    'nome' => 'Care Commerce', 'per_chi' => 'Per WooCommerce e piccoli e-commerce',
                    'prezzo' => '99 €', 'periodo' => 'al mese', 'equivale' => '1.190 €/anno · circa 3,26 € al giorno',
                    'risparmio' => 'Risparmi 250 €', 'rate' => 'oppure 2 rate semestrali da 720 €', 'ore' => '4 ore di intervento incluse',
                    'voci' => "Tutto di Care Plus, più:\nControlli WooCommerce\nTest di checkout e ordini\nPriorità più alta",
                    'evidenza' => false, 'etichetta' => '', 'bottone' => 'Scegli Care Commerce',
                ],
            ],
            'lcare_piani_nota' => 'Servizio annuale con pagamento anticipato, oppure in 2 rate semestrali (totale annuo più alto). Ore extra oltre quelle incluse: 120 € l\'ora. Le licenze dei plugin a pagamento le sottoscrivi tu: se servono, ti diciamo quali.',

            'lcare_metodo_eyebrow' => 'Come funziona',
            'lcare_metodo_titolo'  => "Partiamo da un controllo.\nPoi ci pensiamo noi.",
            'lcare_metodo' => [
                ['icona' => 'search', 'titolo' => 'Controllo iniziale', 'testo' => 'Verifichiamo versione di WordPress, plugin, hosting e backup.'],
                ['icona' => 'lock', 'titolo' => 'Messa in sicurezza', 'testo' => 'Aggiornamenti, firewall, 2FA e pulizia degli utenti admin.'],
                ['icona' => 'refresh', 'titolo' => 'Manutenzione', 'testo' => 'Aggiornamenti e controlli regolari, backup e monitoraggio continui.'],
                ['icona' => 'tools', 'titolo' => 'Assistenza', 'testo' => 'Quando ti serve una modifica, scrivi a noi: usi le ore incluse.'],
            ],

            'lcare_bonifica_kicker'  => 'Senza un piano, se il sito viene violato',
            'lcare_bonifica_testo'   => 'La bonifica costa da 590 € (790 € per un e-commerce), <strong>quanto un anno di Care</strong>. Con Care il supporto al ripristino è incluso.',
            'lcare_bonifica_bottone' => 'Vedi la bonifica',
            'lcare_bonifica_link'    => '/bonifica-sito-wordpress-hackerato/',

            'lcare_faq_eyebrow' => 'Domande frequenti',
            'lcare_faq_titolo'  => 'Prima di scegliere.',
            'lcare_faq' => [
                ['domanda' => 'Il sito non l\'avete fatto voi. Posso attivare Care?', 'risposta' => 'Sì. Prima facciamo un controllo iniziale: se il sito ha problemi seri, te lo diciamo prima di partire.'],
                ['domanda' => 'Cosa succede se il sito viene violato lo stesso?', 'risposta' => 'Il supporto al ripristino è incluso. Con Care Plus e Care Commerce l\'intervento è più esteso.'],
                ['domanda' => 'L\'hosting è incluso?', 'risposta' => 'No, Care riguarda il sito. Se vuoi, gestiamo anche l\'hosting a parte.'],
                ['domanda' => 'Perché un piano annuale?', 'risposta' => 'La sicurezza è un lavoro continuo. Con l\'annuale hai il prezzo più basso e nessun periodo scoperto.'],
                ['domanda' => 'Posso pagare in due rate?', 'risposta' => 'Sì: 2 rate semestrali, con un totale annuo più alto. Con il pagamento annuale anticipato risparmi 130, 190 o 250 €.'],
                ['domanda' => 'Le licenze sono incluse?', 'risposta' => 'Le licenze riguardano servizi e plugin già presenti sul sito. Per aggiornarli serve l\'abbonamento al produttore. Se non lo hai già, ti diciamo quali sottoscrivere e puoi farlo anche tu.'],
            ],

            'lcare_finale_titolo'  => 'In che stato è il tuo sito?',
            'lcare_finale_testo'   => 'Lasciaci l\'indirizzo: facciamo un primo controllo e ti consigliamo il piano giusto.',
            'lcare_finale_bottone' => 'Compila il modulo',
        ];
    }
    if ($name === null) return $d;
    return $d[$name] ?? null;
}

function chefstudio_lcare($name, $post_id = null) {
    $value = function_exists('get_field') ? get_field($name, $post_id) : null;
    if ($value === null || $value === false || $value === '' || $value === []) {
        return chefstudio_lcare_default($name);
    }
    return $value;
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $icone = chefstudio_lbh_icone();

    $text = function ($name, $label, $extra = []) {
        return array_merge(['key' => 'field_' . $name, 'name' => $name, 'label' => $label, 'type' => 'text'], $extra);
    };
    $area = function ($name, $label, $rows = 3, $extra = []) {
        return array_merge(['key' => 'field_' . $name, 'name' => $name, 'label' => $label, 'type' => 'textarea', 'rows' => $rows, 'new_lines' => ''], $extra);
    };
    $tab = function ($label) {
        return ['key' => 'field_lcare_tab_' . sanitize_title($label), 'label' => $label, 'name' => '', 'type' => 'tab', 'placement' => 'top'];
    };
    $rep = function ($name, $label, $subs, $button = 'Aggiungi riga') {
        $sub_fields = [];
        foreach ($subs as $sub) {
            $sub['key'] = 'field_' . $name . '__' . $sub['name'];
            $sub_fields[] = $sub;
        }
        return [
            'key' => 'field_' . $name, 'name' => $name, 'label' => $label, 'type' => 'repeater',
            'layout' => 'block', 'button_label' => $button, 'sub_fields' => $sub_fields,
        ];
    };
    $icona = ['name' => 'icona', 'label' => 'Icona', 'type' => 'select', 'choices' => $icone, 'wrapper' => ['width' => '20']];
    $strong = ['instructions' => 'Puoi usare <strong>testo</strong> per il grassetto.'];

    acf_add_local_field_group([
        'key' => 'group_lcare_landing_care',
        'title' => 'Testi pagina Care',
        'position' => 'acf_after_title',
        'style' => 'default',
        'menu_order' => 0,
        'instruction_placement' => 'label',
        'location' => [[[
            'param' => 'page_template', 'operator' => '==', 'value' => 'pagine/landing-care.php',
        ]]],
        'fields' => [
            $tab('Contatti'),
            $text('lcare_telefono', 'Telefono (come si legge)', ['instructions' => 'Il link per chiamare si crea da solo.']),
            $text('lcare_whatsapp_numero', 'Numero WhatsApp', ['instructions' => 'Solo cifre, con prefisso 39 e senza +.']),
            $text('lcare_whatsapp_testo', 'Messaggio WhatsApp precompilato'),

            $tab('Hero'),
            $text('lcare_badge', 'Etichetta sopra il titolo'),
            $text('lcare_h1', 'Titolo H1 (prima parte, bianca)'),
            $text('lcare_h1_evidenza', 'Titolo H1 (seconda parte, gialla)'),
            $area('lcare_lead', 'Sottotitolo', 3),
            $rep('lcare_proof', 'Punti sotto il sottotitolo', [
                $icona,
                ['name' => 'forte', 'label' => 'In grassetto', 'type' => 'text', 'wrapper' => ['width' => '35']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'text', 'wrapper' => ['width' => '45']],
            ], 'Aggiungi punto'),
            $text('lcare_bottone_piani', 'Testo del bottone verso i piani'),
            $text('lcare_micro', 'Nota sotto i bottoni'),

            $tab('Modulo'),
            ['key' => 'field_lcare_msg_form', 'label' => '', 'name' => '', 'type' => 'message', 'message' => 'Il modulo Gravity si inserisce nel contenuto della pagina con lo shortcode, per esempio [gravityform id="10" title="false" ajax="true"]. I bottoni dei piani precompilano il campo "Piano" se nel modulo il campo ha il nome parametro "piano".'],
            $text('lcare_form_kicker', 'Etichetta verde sopra il modulo'),
            $text('lcare_form_titolo', 'Titolo del modulo'),
            $area('lcare_form_testo', 'Testo sotto il titolo', 2),

            $tab('Fiducia'),
            $rep('lcare_trust', 'Fascia sotto la hero (il primo punto mostra le stelle)', [
                ['name' => 'forte', 'label' => 'In grassetto', 'type' => 'text', 'wrapper' => ['width' => '40']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'text', 'wrapper' => ['width' => '60']],
            ], 'Aggiungi punto'),

            $tab('Cosa facciamo'),
            $text('lcare_cosa_eyebrow', 'Etichetta'),
            $text('lcare_cosa_titolo', 'Titolo'),
            $rep('lcare_cosa', 'Card', [
                $icona,
                ['name' => 'titolo', 'label' => 'Titolo', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'wrapper' => ['width' => '50']],
            ], 'Aggiungi card'),

            $tab('Piani'),
            $text('lcare_piani_eyebrow', 'Etichetta'),
            $text('lcare_piani_titolo', 'Titolo'),
            $rep('lcare_piani', 'Piani', [
                ['name' => 'nome', 'label' => 'Nome del piano', 'type' => 'text', 'wrapper' => ['width' => '25'], 'instructions' => 'Va scritto come nella scelta "Piano" del modulo.'],
                ['name' => 'per_chi', 'label' => 'Per chi', 'type' => 'text', 'wrapper' => ['width' => '45']],
                ['name' => 'etichetta', 'label' => 'Etichetta (es. Consigliato)', 'type' => 'text', 'wrapper' => ['width' => '20']],
                ['name' => 'evidenza', 'label' => 'In evidenza', 'type' => 'true_false', 'ui' => 1, 'wrapper' => ['width' => '10']],
                ['name' => 'prezzo', 'label' => 'Prezzo in evidenza (mensile)', 'type' => 'text', 'wrapper' => ['width' => '20']],
                ['name' => 'periodo', 'label' => 'Accanto al prezzo', 'type' => 'text', 'wrapper' => ['width' => '15']],
                ['name' => 'equivale', 'label' => 'Prezzo annuale e al giorno', 'type' => 'text', 'wrapper' => ['width' => '35']],
                ['name' => 'risparmio', 'label' => 'Risparmio', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'rate', 'label' => 'Rate', 'type' => 'text'],
                ['name' => 'ore', 'label' => 'Ore incluse', 'type' => 'text', 'wrapper' => ['width' => '50']],
                ['name' => 'bottone', 'label' => 'Testo del bottone', 'type' => 'text', 'wrapper' => ['width' => '50']],
                ['name' => 'voci', 'label' => 'Cosa comprende (una voce per riga)', 'type' => 'textarea', 'rows' => 6, 'new_lines' => '', 'instructions' => 'Una riga che finisce con i due punti diventa un titoletto.'],
            ], 'Aggiungi piano'),
            $area('lcare_piani_nota', 'Nota sotto i piani', 2),

            $tab('Come funziona'),
            $text('lcare_metodo_eyebrow', 'Etichetta'),
            $area('lcare_metodo_titolo', 'Titolo (a capo con Invio)', 2),
            $rep('lcare_metodo', 'Passaggi', [
                $icona,
                ['name' => 'titolo', 'label' => 'Titolo', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'wrapper' => ['width' => '50']],
            ], 'Aggiungi passaggio'),
            $text('lcare_bonifica_kicker', 'Box bonifica: etichetta'),
            $area('lcare_bonifica_testo', 'Box bonifica: testo', 3, $strong),
            $text('lcare_bonifica_bottone', 'Box bonifica: testo del bottone'),
            $text('lcare_bonifica_link', 'Box bonifica: link'),

            $tab('FAQ'),
            $text('lcare_faq_eyebrow', 'Etichetta'),
            $text('lcare_faq_titolo', 'Titolo'),
            $rep('lcare_faq', 'Domande', [
                ['name' => 'domanda', 'label' => 'Domanda', 'type' => 'text'],
                ['name' => 'risposta', 'label' => 'Risposta', 'type' => 'textarea', 'rows' => 3, 'new_lines' => ''],
            ], 'Aggiungi domanda'),

            $tab('Chiusura'),
            $text('lcare_finale_titolo', 'Titolo'),
            $area('lcare_finale_testo', 'Testo', 2),
            $text('lcare_finale_bottone', 'Testo del bottone verso il modulo'),
        ],
    ]);
});

// Precompila i campi mai salvati con i testi predefiniti, così nell'editor si vedono e si modificano.
// Valori salvati uguali ai vecchi testi predefiniti: si mostrano i testi nuovi finché non vengono modificati.
function chefstudio_lcare_vecchio($name, $post_id) {
    $meta = function ($key) use ($post_id) {
        return (string) get_post_meta($post_id, $key, true);
    };
    switch ($name) {
        case 'lcare_piani':
            return $meta('lcare_piani_0_periodo') === 'all\'anno' && $meta('lcare_piani_0_prezzo') === '590 €';
        case 'lcare_piani_nota':
            return in_array($meta($name), [
                'Prezzi IVA esclusa. Ore extra: 120 € l\'ora. Le licenze dei plugin a pagamento restano intestate a te.',
                'Ore extra: 120 € l\'ora. Le licenze dei plugin a pagamento restano intestate a te.',
            ], true);
        case 'lcare_faq':
            return $meta('lcare_faq') === '4' && $meta('lcare_faq_3_domanda') === 'Come pago?';
    }
    return false;
}

add_filter('acf/load_value', function ($value, $post_id, $field) {
    $name = $field['name'] ?? '';
    if (strpos($name, 'lcare_') !== 0 || !is_numeric($post_id)) {
        return $value;
    }
    $default = chefstudio_lcare_default($name);
    if ($default === null || (metadata_exists('post', (int) $post_id, $name) && !chefstudio_lcare_vecchio($name, (int) $post_id))) {
        return $value;
    }
    if ($field['type'] !== 'repeater') {
        return $default;
    }
    $rows = [];
    foreach ($default as $row) {
        $keyed = [];
        foreach ($row as $sub => $sub_value) {
            $keyed['field_' . $name . '__' . $sub] = $sub_value;
        }
        $rows[] = $keyed;
    }
    return $rows;
}, 20, 3);
