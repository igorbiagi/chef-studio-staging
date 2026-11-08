<?php
if (!defined('ABSPATH')) exit;

/*
 * Campi ACF della landing "Bonifica sito hackerato" (pagine/landing-bonifica.php).
 * Gruppo registrato da codice: compare solo sulle pagine con quel template.
 * Finché un campo non è mai stato salvato, l'editor e il template mostrano i testi predefiniti qui sotto.
 */

function chefstudio_lbh_icone() {
    return [
        'redirect' => 'Freccia esterna',
        'search'   => 'Lente',
        'alert'    => 'Avviso',
        'user'     => 'Utente',
        'slow'     => 'Cronometro',
        'mail'     => 'Email',
        'backup'   => 'Backup',
        'clean'    => 'Scudo',
        'lock'     => 'Lucchetto',
        'report'   => 'Report',
        'clock'    => 'Orologio',
        'euro'     => 'Euro',
        'wp'       => 'WordPress',
        'check'    => 'Spunta',
        'phone'    => 'Telefono',
        'refresh'  => 'Aggiornamento',
        'pulse'    => 'Monitoraggio',
        'key'      => 'Chiave',
        'cart'     => 'Carrello',
        'tools'    => 'Attrezzi',
    ];
}

// Icone SVG delle landing (bonifica e Care).
function chefstudio_lbh_icon($name) {
    $paths = [
        'redirect' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/>',
        'alert'    => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'user'     => '<circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4"/><path d="m17 14 4 4"/><path d="m21 14-4 4"/>',
        'slow'     => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><path d="M9 2h6"/>',
        'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'backup'   => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5"/><path d="M3 12c0 1.7 4 3 9 3s9-1.3 9-3"/>',
        'clean'    => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/>',
        'lock'     => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'report'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'euro'     => '<path d="M18 7a7 7 0 1 0 0 10"/><path d="M4 10h10"/><path d="M4 14h10"/>',
        'wp'       => '<circle cx="12" cy="12" r="9"/><path d="M4.5 8.5 9 20"/><path d="m8 7 5 13 2-6"/><path d="M14.5 7 19 18"/>',
        'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'check'    => '<path d="M20 6 9 17l-5-5"/>',
        'x'        => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'refresh'  => '<path d="M21 12a9 9 0 0 1-15.5 6.3L3 16"/><path d="M3 21v-5h5"/><path d="M3 12a9 9 0 0 1 15.5-6.3L21 8"/><path d="M21 3v5h-5"/>',
        'pulse'    => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
        'key'      => '<circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.8-9.8"/><path d="m16 7 3 3"/><path d="m19 4 2 2"/>',
        'cart'     => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L22 7H6"/>',
        'tools'    => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-8 8-3-3 1.3-1.3a4 4 0 0 0-5-5L5 13 3 11l8-8 2 2Z"/>',
        'star'     => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1Z"/>',
    ];
    if ($name === 'whatsapp') {
        return '<svg class="lbh-ico" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.1 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>';
    }
    return '<svg class="lbh-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

// Clic su WhatsApp e telefono delle landing: evento gtag, conversione Google Ads (etichette in includes/google-ads-landing.php) e push per Tag Manager.
function chefstudio_lbh_tracciamento() {
    ?>
<script>
document.addEventListener('click', function (e) {
  var a = e.target.closest && e.target.closest('.lbh-track-wa, .lbh-track-tel');
  if (!a) return;
  var evento = a.classList.contains('lbh-track-wa') ? 'clic_whatsapp' : 'clic_telefono';
  var dati = { pagina: location.pathname };
  if (typeof window.gtag === 'function') {
    window.gtag('event', evento, dati);
    var conv = window.chefstudioAds && window.chefstudioAds[evento];
    if (conv) window.gtag('event', 'conversion', { send_to: conv, value: 1.0, currency: 'EUR' });
  }
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push(Object.assign({ event: evento }, dati));
});
</script>
    <?php
}

function chefstudio_lbh_default($name = null) {
    static $d = null;
    if ($d === null) {
        $d = [
            'lbh_telefono'        => '352 0996 996',
            'lbh_whatsapp_numero' => '393520996996',
            'lbh_whatsapp_testo'  => 'Ciao, il mio sito WordPress è stato hackerato: ',

            'lbh_badge'       => 'Pronto intervento WordPress',
            'lbh_h1'          => 'Sito WordPress hackerato?',
            'lbh_h1_evidenza' => 'Lo ripuliamo in 48 ore.',
            'lbh_lead'        => 'Togliamo il malware, chiudiamo la falla da cui sono entrati e rimettiamo il sito online. Prezzo fisso, tutto da remoto.',
            'lbh_proof'       => [
                ['icona' => 'clock', 'forte' => 'Entro 48 ore', 'testo' => 'lavorative dagli accessi'],
                ['icona' => 'euro', 'forte' => 'Da 590 €', 'testo' => 'prezzo fisso, prima di iniziare'],
                ['icona' => 'wp', 'forte' => 'Solo WordPress', 'testo' => 'da oltre 15 anni'],
            ],
            'lbh_bottone_wa'  => 'Scrivici su WhatsApp',
            'lbh_micro'       => 'Rispondiamo in giornata. Non chiediamo password via email.',

            'lbh_form_kicker' => 'Controllo gratuito dall\'esterno',
            'lbh_form_titolo' => 'Fai controllare il tuo sito',
            'lbh_form_testo'  => 'Lasciaci l\'indirizzo: ti ricontattiamo con prezzo e tempi prima di iniziare.',

            'lbh_trust' => [
                ['forte' => '5 su 5', 'testo' => 'su Google'],
                ['forte' => '15+ anni', 'testo' => 'su siti WordPress'],
                ['forte' => 'Report scritto', 'testo' => 'di cosa abbiamo trovato'],
                ['forte' => 'Pietrasanta (LU)', 'testo' => 'studio italiano, fattura italiana'],
            ],

            'lbh_segnali_eyebrow' => 'I segnali',
            'lbh_segnali_titolo'  => 'Riconosci uno di questi?',
            'lbh_segnali' => [
                ['icona' => 'redirect', 'titolo' => 'Il sito manda i visitatori altrove', 'testo' => 'Da Google o da cellulare si finisce su casinò, farmaci, finti premi o truffe.'],
                ['icona' => 'search', 'titolo' => 'Su Google ci sono pagine non tue', 'testo' => 'Risultati in giapponese, prodotti che non vendi, contenuti per adulti.'],
                ['icona' => 'alert', 'titolo' => 'Chrome o Google ti avvisano', 'testo' => '"Sito ingannevole", "sito compromesso" o un messaggio da Search Console.'],
                ['icona' => 'user', 'titolo' => 'Admin che non conosci', 'testo' => 'Utenti nuovi, plugin installati da nessuno, file strani sull\'hosting.'],
                ['icona' => 'slow', 'titolo' => 'Lentezza, errori, sospensione', 'testo' => 'Schermata bianca, errori critici o l\'hosting che blocca il sito.'],
                ['icona' => 'mail', 'titolo' => 'Email strane dal tuo dominio', 'testo' => 'I clienti ricevono messaggi che non hai mai scritto.'],
            ],
            'lbh_urgente' => '<strong>Ogni giorno in più l\'infezione si allarga e Google ti declassa.</strong> Scrivici adesso: il primo controllo è gratuito.',

            'lbh_metodo_eyebrow' => 'Cosa facciamo',
            'lbh_metodo_titolo'  => "Non cancelliamo solo i file sospetti.\nChiudiamo la porta da cui sono entrati.",
            'lbh_metodo' => [
                ['icona' => 'backup', 'titolo' => 'Backup', 'testo' => 'Salviamo il sito così com\'è, prima di toccare qualsiasi cosa.'],
                ['icona' => 'clean', 'titolo' => 'Bonifica', 'testo' => 'Malware e backdoor fuori da file WordPress, plugin, tema, uploads e database.'],
                ['icona' => 'lock', 'titolo' => 'Chiusura della falla', 'testo' => 'Aggiorniamo o sostituiamo ciò che era vulnerabile. Nuovi utenti, password e chiavi.'],
                ['icona' => 'report', 'titolo' => 'Verifica e report', 'testo' => 'Chiediamo a Google di togliere l\'avviso e ti mandiamo il report scritto.'],
            ],

            'lbh_prezzi_eyebrow' => 'Prezzi',
            'lbh_prezzi_titolo'  => 'Prezzo fisso, deciso prima di iniziare.',
            'lbh_prezzi' => [
                ['titolo' => 'Sito vetrina o blog', 'prezzo' => '590 €', 'nota' => 'WordPress, una installazione', 'stile' => 'normale'],
                ['titolo' => 'Sito complesso o e-commerce', 'prezzo' => '790 €', 'nota' => 'WooCommerce, aree riservate, molti plugin', 'stile' => 'evidenza'],
                ['titolo' => 'Multisite o siti molto grandi', 'prezzo' => 'Su preventivo', 'nota' => 'dopo il controllo gratuito', 'stile' => 'tenue'],
            ],
            'lbh_prezzi_nota' => 'Pagamento: 50% all\'avvio, 50% a sito pulito, con bonifico. Se dopo il primo controllo il caso è più grave del previsto, te lo diciamo prima, non dopo.',
            'lbh_incluso_titolo' => 'Il prezzo comprende',
            'lbh_incluso' => [
                ['testo' => 'una installazione WordPress, fino a circa 5 GB'],
                ['testo' => 'backup iniziale del sito così com\'è'],
                ['testo' => 'rimozione di malware e backdoor da file e database'],
                ['testo' => 'aggiornamento o sostituzione di plugin e tema vulnerabili'],
                ['testo' => 'pulizia utenti, nuove password e chiavi di sicurezza'],
                ['testo' => 'richiesta di revisione a Google e report finale'],
            ],
            'lbh_escluso_titolo' => 'Non comprende',
            'lbh_escluso' => [
                ['testo' => 'nuove infezioni dopo la consegna: dipendono da aggiornamenti e password che restano a te'],
                ['testo' => 'contenuti o dati persi prima dell\'intervento e assenti da ogni backup'],
                ['testo' => 'licenze di plugin o temi a pagamento da riacquistare'],
                ['testo' => 'problemi del server o dell\'hosting, caselle email, altri siti sullo stesso spazio'],
                ['testo' => 'la segnalazione al Garante privacy, che resta al titolare (nel report ti diciamo se serve)'],
            ],

            'lbh_tempi_eyebrow' => 'Come funziona',
            'lbh_tempi_titolo'  => 'Da quando ci scrivi a sito pulito.',
            'lbh_tempi' => [
                ['quando' => 'Subito', 'titolo' => 'Ci scrivi', 'testo' => 'su WhatsApp, al telefono o dal modulo, con l\'indirizzo del sito.'],
                ['quando' => 'In giornata', 'titolo' => 'Controllo gratuito', 'testo' => 'guardiamo il sito dall\'esterno e ti confermiamo prezzo e tempi.'],
                ['quando' => 'All\'avvio', 'titolo' => 'Accessi e acconto', 'testo' => 'ti spieghiamo come darci l\'accesso all\'hosting, senza password via email.'],
                ['quando' => 'Entro 48 ore', 'titolo' => 'Sito pulito e report', 'testo' => 'sito online, falla chiusa, report di cosa abbiamo trovato e fatto.'],
            ],
            'lbh_care_kicker'  => 'Dopo la bonifica',
            'lbh_care_testo'   => 'Per non doverci richiamare: con <strong>Chef Studio Care</strong> aggiorniamo WordPress, plugin e tema, controlliamo le vulnerabilità e facciamo i backup. Da 49 € al mese.',
            'lbh_care_bottone' => 'Scopri Care',
            'lbh_care_link'    => '/manutenzione-wordpress/',

            'lbh_faq_eyebrow' => 'Domande frequenti',
            'lbh_faq_titolo'  => 'Prima di scriverci.',
            'lbh_faq' => [
                ['domanda' => 'Perderò i contenuti?', 'risposta' => 'No. Partiamo sempre da un backup del sito così com\'è, prima di qualsiasi intervento.'],
                ['domanda' => 'Il sito non l\'avete fatto voi. Va bene lo stesso?', 'risposta' => 'Sì, interveniamo su qualsiasi sito WordPress. Ci servono solo gli accessi all\'hosting e al database.'],
                ['domanda' => 'E se il sito non è WordPress?', 'risposta' => 'In quel caso non possiamo aiutarti: lavoriamo solo su WordPress.'],
                ['domanda' => 'Quanto resta offline il sito?', 'risposta' => 'Di solito poche ore, durante la bonifica, e ti avvisiamo prima. Se il sito è già bloccato, il tempo offline si accorcia.'],
                ['domanda' => 'Devo avvisare qualcuno?', 'risposta' => 'Se dal sito sono passati dati personali (moduli, ordini, account clienti), potresti dover segnalare la violazione al Garante privacy entro 72 ore. Nel report ti diciamo se il tuo caso rientra.'],
                ['domanda' => 'Mi avete contattato voi dicendo che il sito è hackerato: è una truffa?', 'risposta' => 'Non chiediamo mai password o pagamenti tramite link in email. Nel dubbio, chiamaci al 352 0996 996.'],
            ],

            'lbh_finale_titolo'  => 'Il tuo sito è stato violato?',
            'lbh_finale_testo'   => 'Scrivici adesso. Controlliamo il sito dall\'esterno e ti diciamo prezzo e tempi prima di iniziare.',
            'lbh_finale_bottone' => 'Compila il modulo',
        ];
    }
    if ($name === null) return $d;
    return $d[$name] ?? null;
}

// Valore del campo, o il testo predefinito se il campo è vuoto o ACF non è attivo.
function chefstudio_lbh($name, $post_id = null) {
    $value = function_exists('get_field') ? get_field($name, $post_id) : null;
    if ($value === null || $value === false || $value === '' || $value === []) {
        return chefstudio_lbh_default($name);
    }
    return $value;
}

// Testi brevi con grassetto, corsivo, link e a capo.
function chefstudio_lbh_kses($text) {
    return wp_kses(nl2br((string) $text, false), [
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [],
        'a' => ['href' => [], 'target' => [], 'rel' => []],
    ]);
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
        return ['key' => 'field_lbh_tab_' . sanitize_title($label), 'label' => $label, 'name' => '', 'type' => 'tab', 'placement' => 'top'];
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

    acf_add_local_field_group([
        'key' => 'group_lbh_landing_bonifica',
        'show_in_rest' => 1,
        'title' => 'Testi landing bonifica',
        'position' => 'acf_after_title',
        'style' => 'default',
        'menu_order' => 0,
        'instruction_placement' => 'label',
        'location' => [[[
            'param' => 'page_template', 'operator' => '==', 'value' => 'pagine/landing-bonifica.php',
        ]]],
        'fields' => [
            $tab('Contatti'),
            $text('lbh_telefono', 'Telefono (come si legge)', ['instructions' => 'Il link per chiamare si crea da solo.']),
            $text('lbh_whatsapp_numero', 'Numero WhatsApp', ['instructions' => 'Solo cifre, con prefisso 39 e senza +.']),
            $text('lbh_whatsapp_testo', 'Messaggio WhatsApp precompilato'),

            $tab('Hero'),
            $text('lbh_badge', 'Etichetta sopra il titolo'),
            $text('lbh_h1', 'Titolo H1 (prima parte, bianca)'),
            $text('lbh_h1_evidenza', 'Titolo H1 (seconda parte, gialla)'),
            $area('lbh_lead', 'Sottotitolo', 3),
            $rep('lbh_proof', 'Punti sotto il sottotitolo', [
                $icona,
                ['name' => 'forte', 'label' => 'In grassetto', 'type' => 'text', 'wrapper' => ['width' => '35']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'text', 'wrapper' => ['width' => '45']],
            ], 'Aggiungi punto'),
            $text('lbh_bottone_wa', 'Testo dei bottoni WhatsApp'),
            $text('lbh_micro', 'Nota sotto i bottoni'),

            $tab('Modulo'),
            ['key' => 'field_lbh_msg_form', 'label' => '', 'name' => '', 'type' => 'message', 'message' => 'Il modulo Gravity si inserisce nel contenuto della pagina con lo shortcode, per esempio [gravityform id="9" title="false" ajax="true"].'],
            $text('lbh_form_kicker', 'Etichetta verde sopra il modulo'),
            $text('lbh_form_titolo', 'Titolo del modulo'),
            $area('lbh_form_testo', 'Testo sotto il titolo', 2),

            $tab('Fiducia'),
            $rep('lbh_trust', 'Fascia sotto la hero (il primo punto mostra le stelle)', [
                ['name' => 'forte', 'label' => 'In grassetto', 'type' => 'text', 'wrapper' => ['width' => '40']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'text', 'wrapper' => ['width' => '60']],
            ], 'Aggiungi punto'),

            $tab('Segnali'),
            $text('lbh_segnali_eyebrow', 'Etichetta'),
            $text('lbh_segnali_titolo', 'Titolo'),
            $rep('lbh_segnali', 'Card dei segnali', [
                $icona,
                ['name' => 'titolo', 'label' => 'Titolo', 'type' => 'text', 'wrapper' => ['width' => '35']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'wrapper' => ['width' => '45']],
            ], 'Aggiungi segnale'),
            $area('lbh_urgente', 'Testo della barra di urgenza', 2, ['instructions' => 'Puoi usare <strong>testo</strong> per il grassetto.']),

            $tab('Metodo'),
            $text('lbh_metodo_eyebrow', 'Etichetta'),
            $area('lbh_metodo_titolo', 'Titolo (a capo con Invio)', 2),
            $rep('lbh_metodo', 'Passaggi', [
                $icona,
                ['name' => 'titolo', 'label' => 'Titolo', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'wrapper' => ['width' => '50']],
            ], 'Aggiungi passaggio'),

            $tab('Prezzi'),
            $text('lbh_prezzi_eyebrow', 'Etichetta'),
            $text('lbh_prezzi_titolo', 'Titolo'),
            $rep('lbh_prezzi', 'Card dei prezzi', [
                ['name' => 'titolo', 'label' => 'Tipo di sito', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'prezzo', 'label' => 'Prezzo', 'type' => 'text', 'wrapper' => ['width' => '15']],
                ['name' => 'nota', 'label' => 'Nota', 'type' => 'text', 'wrapper' => ['width' => '35']],
                ['name' => 'stile', 'label' => 'Stile', 'type' => 'select', 'choices' => ['normale' => 'Normale', 'evidenza' => 'In evidenza', 'tenue' => 'Tenue'], 'wrapper' => ['width' => '20']],
            ], 'Aggiungi prezzo'),
            $area('lbh_prezzi_nota', 'Nota sotto i prezzi', 3),
            $text('lbh_incluso_titolo', 'Titolo colonna "compreso"'),
            $rep('lbh_incluso', 'Cosa comprende', [
                ['name' => 'testo', 'label' => 'Voce', 'type' => 'text'],
            ], 'Aggiungi voce'),
            $text('lbh_escluso_titolo', 'Titolo colonna "escluso"'),
            $rep('lbh_escluso', 'Cosa non comprende', [
                ['name' => 'testo', 'label' => 'Voce', 'type' => 'text'],
            ], 'Aggiungi voce'),

            $tab('Come funziona'),
            $text('lbh_tempi_eyebrow', 'Etichetta'),
            $text('lbh_tempi_titolo', 'Titolo'),
            $rep('lbh_tempi', 'Fasi', [
                ['name' => 'quando', 'label' => 'Quando', 'type' => 'text', 'wrapper' => ['width' => '20']],
                ['name' => 'titolo', 'label' => 'Titolo', 'type' => 'text', 'wrapper' => ['width' => '30']],
                ['name' => 'testo', 'label' => 'Testo', 'type' => 'text', 'wrapper' => ['width' => '50']],
            ], 'Aggiungi fase'),
            $text('lbh_care_kicker', 'Box Care: etichetta'),
            $area('lbh_care_testo', 'Box Care: testo', 3, ['instructions' => 'Puoi usare <strong>testo</strong> per il grassetto.']),
            $text('lbh_care_bottone', 'Box Care: testo del bottone'),
            $text('lbh_care_link', 'Box Care: link', ['instructions' => 'Indirizzo della pagina, per esempio /manutenzione-wordpress/']),

            $tab('FAQ'),
            $text('lbh_faq_eyebrow', 'Etichetta'),
            $text('lbh_faq_titolo', 'Titolo'),
            $rep('lbh_faq', 'Domande', [
                ['name' => 'domanda', 'label' => 'Domanda', 'type' => 'text'],
                ['name' => 'risposta', 'label' => 'Risposta', 'type' => 'textarea', 'rows' => 3, 'new_lines' => ''],
            ], 'Aggiungi domanda'),

            $tab('Chiusura'),
            $text('lbh_finale_titolo', 'Titolo'),
            $area('lbh_finale_testo', 'Testo', 2),
            $text('lbh_finale_bottone', 'Testo del bottone verso il modulo'),
        ],
    ]);
});

// Precompila i campi mai salvati con i testi predefiniti, così nell'editor si vedono e si modificano.
add_filter('acf/load_value', function ($value, $post_id, $field) {
    $name = $field['name'] ?? '';
    if (strpos($name, 'lbh_') !== 0 || !is_numeric($post_id)) {
        return $value;
    }
    $default = chefstudio_lbh_default($name);
    // Toglie la vecchia dicitura sull'IVA anche dai testi già salvati
    if ($name === 'lbh_prezzi_nota' && is_string($value) && strpos($value, 'Prezzi IVA esclusa. ') === 0) {
        return substr($value, strlen('Prezzi IVA esclusa. '));
    }
    if ($default === null || metadata_exists('post', (int) $post_id, $name)) {
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
