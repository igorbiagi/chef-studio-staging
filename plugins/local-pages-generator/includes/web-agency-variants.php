<?php

if (!defined('ABSPATH')) exit;

if (!function_exists('lpg_wa_variant_field_name')) {
    function lpg_wa_variant_field_name($base_field, $variant_key) {
        $prefixes = [
            'variant_1' => 'wa_v1_',
            'variant_2' => 'wa_v2_',
            'variant_3' => 'wa_v3_',
        ];

        if (empty($prefixes[$variant_key])) {
            return '';
        }

        $base_field = (string) $base_field;
        if (strpos($base_field, 'wa_') === 0) {
            $base_field = substr($base_field, 3);
        }

        return $prefixes[$variant_key] . $base_field;
    }
}

if (!function_exists('lpg_wa_base_text_defaults')) {
    function lpg_wa_base_text_defaults() {
        return [
            'wa_hero_h1_default'              => 'Web Agency {citta}',
            'wa_h2_hero_default'              => 'Siti web, SEO e marketing digitale su misura per aziende e professionisti.',
            'wa_paragrafo_intro_default'      => '<p>Nella nostra web agency a {citta} lavoriamo con analisi, strategia e cura del dettaglio per aiutare aziende e professionisti a crescere online. Studiamo mercato, competitor e ricerche degli utenti per costruire siti web, identita visive e percorsi di marketing pensati per generare contatti reali.</p><p>Lavoriamo da piu di 10 anni nel mondo del web e affianchiamo piccole e medie imprese con progetti su misura, senza pacchetti preconfezionati.</p>',
            'wa_hero_primary_button_default'  => 'Parla con la nostra web agency',
            'wa_hero_secondary_button_default'=> 'Guarda tutti i servizi web',
            'wa_phone_prompt_default'         => 'Preferisci parlarne subito? Chiamaci!',
            'wa_titolo_sezione_1_default'     => 'La Web Agency {citta} dinamica',
            'wa_testo_sezione_1_default'      => '<p>Realizziamo siti web professionali a {citta}, in Italia e all\'estero. Scegliere la giusta web agency e fondamentale per trasformare il sito in uno strumento utile, chiaro e orientato ai risultati.</p><p>Noi di Chef Studio ti aiutiamo a capire le potenzialita dei mezzi digitali e formuliamo una soluzione proporzionata ai tuoi obiettivi, ottimizzando budget, qualita e strategia.</p><p>La maggior parte dei progetti puo essere seguita anche a distanza, con un metodo ordinato che unisce web design, sviluppo, SEO e comunicazione.</p>',
            'wa_titolo_1_sezione_2_default'   => 'Web design, WordPress e siti su misura a {citta}',
            'wa_testo_1_sezione_2_default'    => '<p>Il tuo sito web e la prima impressione che offri online: deve rappresentarti bene, caricarsi velocemente e guidare l\'utente verso una richiesta, una vendita o una prenotazione.</p><p>Progettiamo siti responsive e facili da gestire, spesso in WordPress, con template su misura e una struttura pensata per essere chiara sia per le persone sia per Google.</p>',
            'wa_titolo_2_sezione_2_default'   => 'SEO e marketing digitale per farti trovare',
            'wa_testo_2_sezione_2_default'    => '<p>Un bel sito non basta se nessuno lo trova. Per questo integriamo strategia SEO, contenuti e analisi delle ricerche fin dalle prime fasi del progetto.</p><p>Possiamo lavorare sul posizionamento locale a {citta}, sulla crescita nazionale, sulle campagne e sulla comunicazione necessaria a rendere il tuo brand piu riconoscibile.</p>',
            'wa_cta_testo_default'            => '<h2>Vuoi costruire una presenza online piu efficace?</h2><p>Parla con Chef Studio e scopri come possiamo unire sito web, SEO, design e strategia digitale in un progetto concreto per la tua attivita.</p>',
            'wa_cta_button_default'           => 'Contattaci!',
            'wa_examples_heading_default'     => 'Creazione siti internet {citta}: alcuni esempi.',
            'wa_example_site_1_text_default'  => 'Sito web per prodotti artigianali.',
            'wa_example_site_2_text_default'  => 'Sito web per azienda agricola e agriturismo.',
            'wa_example_site_3_text_default'  => 'Sito web per appartamenti e case vacanza.',
            'wa_example_site_4_text_default'  => 'Sito web per prodotti di design in marmo.',
            'wa_services_heading_default'     => 'Principali servizi della nostra Web Agency {citta}',
            'wa_intro_servizi_default'        => 'La nostra Web Agency {citta} supporta imprese e professionisti con servizi digitali coordinati: sito web, SEO, design, contenuti e analisi.',
            'wa_titolo_scelta_default'        => 'Perche affidarti alla nostra Web Agency {citta}',
            'wa_testo_scelta_default'         => '<p>Ogni progetto digitale deve partire da obiettivi chiari: farsi trovare, spiegare meglio il valore dell\'azienda, vendere, ricevere richieste o consolidare il brand.</p><p>Per questo uniamo competenze diverse in un unico percorso: identita visiva, sito web, contenuti, SEO, marketing e analisi dei risultati. Il sito non resta un elemento isolato, ma diventa parte di un sistema pensato per crescere.</p>',
            'wa_titolo_faq_default'           => 'FAQ - Domande frequenti sulla Web Agency {citta}',
            'wa_titolo_contatti_default'      => 'Contattaci per Web Agency {citta}!',
            'wa_sottotitolo_contatti_default' => 'Abbiamo la soluzione giusta per il tuo progetto.',
            'wa_testo_form_default'           => 'Compila il form o chiamaci al <br/><a href="tel:{telefono_link}" class="no-decoration">{telefono}</a> per parlare subito con la nostra Web Agency {citta}.',
        ];
    }
}

if (!function_exists('lpg_wa_default_service_cards')) {
    function lpg_wa_default_service_cards() {
        return [
            ['wa_service_titolo' => 'Realizzazione siti web', 'wa_service_testo' => 'Sviluppiamo siti veloci, responsive e ottimizzati per offrire una buona esperienza utente e generare contatti qualificati.'],
            ['wa_service_titolo' => 'Web design {citta}', 'wa_service_testo' => 'Progettiamo interfacce chiare e coerenti con il tuo brand, dove estetica, usabilita e conversione lavorano insieme.'],
            ['wa_service_titolo' => 'Creazione siti in WordPress', 'wa_service_testo' => 'Realizziamo siti dinamici che puoi aggiornare in autonomia, con template su misura e una struttura ordinata.'],
            ['wa_service_titolo' => 'Consulenza SEO', 'wa_service_testo' => 'Analizziamo ricerche, competitor e contenuti per migliorare la visibilita organica del tuo sito sui motori di ricerca.'],
            ['wa_service_titolo' => 'SEM e campagne', 'wa_service_testo' => 'Supportiamo campagne a pagamento e strategie di acquisizione quando servono traffico, test rapidi e misurazione precisa.'],
            ['wa_service_titolo' => 'Social media marketing', 'wa_service_testo' => 'Definiamo contenuti e messaggi per rendere il brand riconoscibile e coerente sui canali piu adatti al tuo pubblico.'],
            ['wa_service_titolo' => 'Content marketing', 'wa_service_testo' => 'Produciamo contenuti utili per spiegare, posizionare e valorizzare servizi, prodotti e competenze della tua azienda.'],
            ['wa_service_titolo' => 'Email marketing', 'wa_service_testo' => 'Organizziamo comunicazioni e newsletter per coltivare contatti, promuovere offerte e mantenere vivo il rapporto con i clienti.'],
            ['wa_service_titolo' => 'Web analytics', 'wa_service_testo' => 'Misuriamo comportamento degli utenti, conversioni e performance per prendere decisioni basate sui dati.'],
            ['wa_service_titolo' => 'E-commerce {citta}', 'wa_service_testo' => 'Creiamo negozi online chiari, veloci e gestibili, pensati per valorizzare catalogo, prodotti e processo di acquisto.'],
        ];
    }
}

if (!function_exists('lpg_wa_default_faq_rows')) {
    function lpg_wa_default_faq_rows() {
        return [
            ['wa_faq_titolo' => 'Perche realizzare un sito web?', 'wa_faq_testo' => '<p>Realizzare un sito web a {citta} e fondamentale per presentare prodotti, servizi e identita del brand. Un sito ben strutturato aumenta la credibilita, aiuta le persone a capire cosa fai e puo generare richieste tramite motori di ricerca, campagne e canali social.</p>'],
            ['wa_faq_titolo' => 'Perche rivolgersi a una Web Agency a {citta}?', 'wa_faq_testo' => '<p>Una web agency ti aiuta a mettere ordine tra strategia, design, sviluppo, SEO e marketing. Se il sito e uno strumento di lavoro, affidarsi a professionisti permette di evitare improvvisazioni e costruire un progetto piu solido, misurabile e adatto ai tuoi obiettivi.</p>'],
            ['wa_faq_titolo' => 'Posso creare un sito gratis?', 'wa_faq_testo' => '<p>Esistono piattaforme gratuite o economiche, ma spesso impongono limiti su prestazioni, personalizzazione, SEO e proprieta del progetto. Se il sito serve al tuo business, e meglio progettare una base professionale che possa crescere nel tempo.</p>'],
            ['wa_faq_titolo' => 'I vostri siti sono mobile friendly?', 'wa_faq_testo' => '<p>Si, realizziamo siti responsive che si adattano a smartphone, tablet e desktop. La progettazione mobile e parte del lavoro fin dall\'inizio, perche gran parte delle visite arriva da dispositivi mobili.</p>'],
            ['wa_faq_titolo' => 'Il sito web ha un costo annuale?', 'wa_faq_testo' => '<p>Ogni sito richiede hosting, dominio e spesso manutenzione. I costi variano in base alle esigenze: da soluzioni semplici fino a progetti piu complessi con servizi, aggiornamenti e supporto continuativo.</p>'],
            ['wa_faq_titolo' => 'Posso aggiornare da solo il sito?', 'wa_faq_testo' => '<p>Si, quando il progetto lo prevede consegniamo un sito gestibile in autonomia per aggiornare testi, immagini, pagine, articoli o prodotti. Possiamo anche affiancarti con formazione o occuparci noi degli aggiornamenti.</p>'],
            ['wa_faq_titolo' => 'Il mio sito apparira sui motori di ricerca?', 'wa_faq_testo' => '<p>Un sito puo essere indicizzato da Google, ma per ottenere visibilita reale serve una strategia SEO. Possiamo lavorare su struttura, contenuti, performance e posizionamento per aumentare le possibilita di intercettare ricerche utili.</p>'],
        ];
    }
}

if (!function_exists('lpg_wa_text_variants')) {
    function lpg_wa_text_variants() {
        return [
            'variant_1' => [
                'label' => 'Variante 1 - Standard',
                'fields' => [
                    'wa_h2_hero_default' => 'Siti web, SEO e marketing digitale per progetti online più chiari, solidi e misurabili.',
                    'wa_paragrafo_intro_default' => '<p>Cerchi una Web Agency {citta}? Chef Studio progetta siti web professionali, strategie SEO e percorsi di marketing digitale pensati per trasformare la presenza online in uno strumento concreto di acquisizione clienti.</p><p>Analizziamo obiettivi, pubblico, ricerche e contenuti per costruire progetti su misura, senza pacchetti standard e senza soluzioni generiche.</p>',
                    'wa_titolo_sezione_1_default' => 'Web Agency {citta}: strategia, sito e crescita digitale',
                    'wa_testo_sezione_1_default' => '<p>Per chi cerca una Web Agency {citta}, la scelta non riguarda solo la grafica del sito, ma il modo in cui il progetto digitale comunica valore, genera fiducia e porta contatti qualificati.</p><p>Chef Studio unisce analisi, web design, sviluppo WordPress, SEO e comunicazione per creare una presenza online coerente con gli obiettivi dell’attività e con le ricerche del pubblico.</p><p>Ogni decisione viene costruita attorno a priorità reali: chiarezza del messaggio, performance, struttura dei contenuti, posizionamento e conversione.</p>',
                    'wa_titolo_1_sezione_2_default' => 'Siti web e WordPress per chi cerca una Web Agency {citta}',
                    'wa_testo_1_sezione_2_default' => '<p>Un sito professionale deve essere bello da vedere, semplice da usare e progettato per guidare l’utente verso un’azione: richiesta di informazioni, contatto, acquisto o prenotazione.</p><p>Realizziamo siti responsive in WordPress, con template su misura, contenuti ordinati e una struttura pensata per essere comprensibile sia dalle persone sia dai motori di ricerca.</p>',
                    'wa_titolo_2_sezione_2_default' => 'SEO e marketing digitale orientati ai risultati',
                    'wa_testo_2_sezione_2_default' => '<p>La visibilità online nasce da un sistema: analisi delle ricerche, contenuti utili, architettura chiara, performance tecniche e monitoraggio dei risultati.</p><p>Possiamo lavorare su SEO locale, nazionale o internazionale in base agli obiettivi del progetto, integrando strategia organica, campagne, contenuti e analisi dei dati.</p>',
                    'wa_cta_testo_default' => '<h2>Vuoi rendere più efficace la tua presenza online?</h2><p>Raccontaci il progetto: ti aiuteremo a capire come unire sito web, SEO, design e strategia digitale in un percorso chiaro e misurabile.</p>',
                    'wa_intro_servizi_default' => 'Servizi web e marketing per chi cerca una Web Agency {citta}: strategia, sito, SEO, contenuti, design e analisi dei risultati.',
                    'wa_titolo_scelta_default' => 'Perché scegliere Chef Studio come Web Agency {citta}',
                    'wa_testo_scelta_default' => '<p>Un progetto digitale funziona quando parte da obiettivi precisi: migliorare la presenza online, aumentare la visibilità, spiegare meglio il valore dell’azienda e generare contatti più qualificati.</p><p>Per questo integriamo competenze diverse in un unico metodo: identità visiva, sito web, contenuti, SEO, marketing e web analytics. Il sito non resta un elemento isolato, ma diventa parte di una strategia più ampia.</p>',
                    'wa_titolo_faq_default' => 'FAQ sulla Web Agency {citta}',
                    'wa_titolo_contatti_default' => 'Parliamo del tuo progetto digitale',
                    'wa_sottotitolo_contatti_default' => 'Una consulenza chiara per capire obiettivi, priorità e prossimi passi.',
                    'wa_testo_form_default' => 'Compila il form o chiamaci al <br><a href="tel:{telefono_link}" class="no-decoration">{telefono}</a> per richiedere informazioni su servizi web, SEO e marketing digitale.',
                    'wa_hero_h1_default' => 'Web Agency {citta}',
                    'wa_hero_primary_button_default' => 'Richiedi una consulenza',
                    'wa_hero_secondary_button_default' => 'Scopri i servizi digitali',
                    'wa_phone_prompt_default' => 'Preferisci parlarne ora? Chiamaci.',
                    'wa_examples_heading_default' => 'Progetti digitali e siti professionali: alcuni esempi',
                    'wa_example_site_1_text_default' => 'Sito web per prodotti artigianali.',
                    'wa_example_site_2_text_default' => 'Sito web per azienda agricola e agriturismo.',
                    'wa_example_site_3_text_default' => 'Sito web per appartamenti e case vacanza.',
                    'wa_example_site_4_text_default' => 'Sito web per prodotti di design in marmo.',
                    'wa_services_heading_default' => 'Servizi per chi cerca una Web Agency {citta}',
                    'wa_cta_button_default' => 'Contattaci',
                ],
                'faq' => [
                    ['wa_faq_titolo' => 'Quanto costa una Web Agency?', 'wa_faq_testo' => '<p>Il costo dipende da obiettivi, complessità del sito, contenuti, SEO, integrazioni e livello di supporto richiesto. Un progetto semplice ha un investimento diverso da una strategia completa con sito, posizionamento organico, analytics e marketing continuativo.</p>'],
                    ['wa_faq_titolo' => 'Come scegliere una Web Agency {citta}?', 'wa_faq_testo' => '<p>Valuta metodo, competenze SEO, qualità dei siti realizzati, chiarezza del preventivo e capacità di collegare design, contenuti e obiettivi commerciali. Una buona web agency non vende solo pagine, ma costruisce un sistema digitale misurabile.</p>'],
                    ['wa_faq_titolo' => 'Una Web Agency si occupa anche di SEO?', 'wa_faq_testo' => '<p>Sì, una web agency strutturata dovrebbe considerare la SEO fin dall’inizio. Architettura del sito, contenuti, performance, metadata e struttura semantica influenzano la visibilità su Google e la comprensione da parte delle AI.</p>'],
                    ['wa_faq_titolo' => 'È meglio usare WordPress per un sito aziendale?', 'wa_faq_testo' => '<p>WordPress è una scelta valida quando serve un sito flessibile, aggiornabile e scalabile. La qualità però dipende da progetto, tema, codice, contenuti e manutenzione: non basta installare un CMS per ottenere un sito efficace.</p>'],
                    ['wa_faq_titolo' => 'Posso rifare un sito esistente senza perdere visibilità?', 'wa_faq_testo' => '<p>Sì, ma serve una migrazione ordinata. Prima di rifare un sito è importante analizzare pagine, traffico, keyword, URL, contenuti e redirect, così da proteggere il lavoro SEO già acquisito e migliorare ciò che non funziona.</p>'],
                    ['wa_faq_titolo' => 'Il sito sarà aggiornabile in autonomia?', 'wa_faq_testo' => '<p>Quando il progetto lo prevede, realizziamo siti gestibili in autonomia per modificare testi, immagini, pagine, articoli o prodotti. Possiamo anche affiancarti con formazione, manutenzione e aggiornamenti continuativi.</p>'],
                    ['wa_faq_titolo' => 'Quanto tempo serve per vedere risultati SEO?', 'wa_faq_testo' => '<p>I tempi dipendono da concorrenza, stato del sito, qualità dei contenuti e autorevolezza del dominio. La SEO richiede metodo e continuità, ma permette di costruire visibilità più stabile rispetto ad azioni isolate o solo pubblicitarie.</p>'],
                ],
                'service_cards' => [
                    ['wa_service_titolo' => 'Siti web professionali', 'wa_service_testo' => 'Progettiamo siti veloci, responsive e orientati alla conversione, con contenuti chiari e percorsi pensati per generare contatti.'],
                    ['wa_service_titolo' => 'Web design {citta}', 'wa_service_testo' => 'Disegniamo interfacce coerenti con il brand, dove estetica, usabilità e chiarezza aiutano l’utente a capire e scegliere.'],
                    ['wa_service_titolo' => 'Siti WordPress su misura', 'wa_service_testo' => 'Sviluppiamo siti gestibili, scalabili e ordinati, con template personalizzati e una struttura semplice da aggiornare.'],
                    ['wa_service_titolo' => 'Consulenza SEO', 'wa_service_testo' => 'Analizziamo keyword, contenuti, competitor e struttura del sito per migliorare la visibilità organica e intercettare ricerche utili.'],
                    ['wa_service_titolo' => 'Campagne e acquisizione', 'wa_service_testo' => 'Supportiamo strategie a pagamento quando servono traffico qualificato, test rapidi, landing page e misurazione precisa.'],
                    ['wa_service_titolo' => 'Strategia social', 'wa_service_testo' => 'Definiamo messaggi, contenuti e linee editoriali per rendere il brand più riconoscibile sui canali adatti al pubblico.'],
                    ['wa_service_titolo' => 'Content marketing', 'wa_service_testo' => 'Creiamo contenuti utili per spiegare servizi, valorizzare competenze e costruire fiducia lungo il percorso di scelta.'],
                    ['wa_service_titolo' => 'Email marketing', 'wa_service_testo' => 'Organizziamo newsletter e automazioni per coltivare contatti, promuovere offerte e mantenere attiva la relazione con i clienti.'],
                    ['wa_service_titolo' => 'Web analytics', 'wa_service_testo' => 'Misuriamo traffico, conversioni e comportamento degli utenti per prendere decisioni basate su dati reali.'],
                    ['wa_service_titolo' => 'E-commerce', 'wa_service_testo' => 'Realizziamo negozi online chiari e gestibili, progettati per valorizzare prodotti, catalogo e processo di acquisto.'],
                ],
            ],
            'variant_2' => [
                'label' => 'Variante 2 - Consulenziale',
                'fields' => [
                    'wa_h2_hero_default' => 'Strategia digitale, siti WordPress e SEO per trasformare la presenza online in richieste concrete.',
                    'wa_paragrafo_intro_default' => '<p>Per chi cerca una Web Agency {citta}, Chef Studio offre un approccio integrato: analisi, sito web, SEO, contenuti e marketing digitale lavorano insieme per rendere il progetto più chiaro, credibile e orientato ai risultati.</p><p>Partiamo dagli obiettivi e dal modo in cui le persone cercano informazioni online, così ogni scelta di design, contenuto e struttura ha una funzione precisa.</p>',
                    'wa_titolo_sezione_1_default' => 'Una Web Agency {citta} per progetti digitali più efficaci',
                    'wa_testo_sezione_1_default' => '<p>La ricerca di una Web Agency {citta} spesso nasce da un’esigenza concreta: migliorare il sito, aumentare la visibilità, presentare meglio l’azienda o ottenere più richieste qualificate.</p><p>Chef Studio lavora su questi obiettivi con un metodo consulenziale che collega comunicazione, esperienza utente, sviluppo tecnico, SEO e misurazione delle performance.</p><p>Il risultato è un progetto digitale costruito con logica, non una semplice somma di grafica, testi e funzioni.</p>',
                    'wa_titolo_1_sezione_2_default' => 'Web design, UX e WordPress per Web Agency {citta}',
                    'wa_testo_1_sezione_2_default' => '<p>Un sito efficace deve comunicare subito chi sei, cosa offri e perché l’utente dovrebbe contattarti. Per questo curiamo struttura, gerarchie visive, contenuti e velocità di caricamento.</p><p>Realizziamo siti WordPress con layout su misura, gestione semplificata e una base tecnica pensata per evolvere nel tempo senza perdere ordine e coerenza.</p>',
                    'wa_titolo_2_sezione_2_default' => 'SEO, contenuti e dati per crescere online',
                    'wa_testo_2_sezione_2_default' => '<p>La SEO non è un’aggiunta finale, ma una parte del progetto. Analisi delle query, architettura informativa, contenuti e performance aiutano il sito a essere trovato e compreso meglio.</p><p>In base agli obiettivi, la strategia può lavorare su ricerche locali, nazionali o internazionali, integrando organico, campagne, contenuti e monitoraggio.</p>',
                    'wa_cta_testo_default' => '<h2>Hai un progetto digitale da sviluppare?</h2><p>Parliamone in modo concreto: analizziamo obiettivi, priorità e possibili interventi per rendere sito, SEO e comunicazione più efficaci.</p>',
                    'wa_intro_servizi_default' => 'Una selezione di servizi per chi cerca una Web Agency {citta}: sito web, SEO, WordPress, contenuti, campagne e analisi.',
                    'wa_titolo_scelta_default' => 'Cosa valutare in una Web Agency {citta}',
                    'wa_testo_scelta_default' => '<p>Scegliere una web agency significa affidare la presenza online a un metodo. Non bastano estetica e tecnologia: servono strategia, contenuti chiari, attenzione alla SEO e capacità di leggere i dati.</p><p>Chef Studio segue ogni progetto con una visione integrata, mettendo in relazione identità del brand, sito web, acquisizione clienti e miglioramento continuo.</p>',
                    'wa_titolo_faq_default' => 'Domande frequenti su Web Agency {citta}',
                    'wa_titolo_contatti_default' => 'Richiedi un confronto sul tuo progetto',
                    'wa_sottotitolo_contatti_default' => 'Ti aiutiamo a capire quali interventi possono generare più valore.',
                    'wa_testo_form_default' => 'Compila il form o chiamaci al <br><a href="tel:{telefono_link}" class="no-decoration">{telefono}</a> per ricevere un primo confronto su sito, SEO e strategia digitale.',
                    'wa_hero_h1_default' => 'Web Agency {citta}',
                    'wa_hero_primary_button_default' => 'Parliamo del progetto',
                    'wa_hero_secondary_button_default' => 'Vedi i servizi web',
                    'wa_phone_prompt_default' => 'Vuoi parlarne direttamente? Chiamaci.',
                    'wa_examples_heading_default' => 'Esempi di siti e progetti digitali',
                    'wa_example_site_1_text_default' => 'Sito per prodotti artigianali e brand di nicchia.',
                    'wa_example_site_2_text_default' => 'Sito per azienda agricola, ospitalità e territorio.',
                    'wa_example_site_3_text_default' => 'Sito per strutture ricettive e case vacanza.',
                    'wa_example_site_4_text_default' => 'Sito per design, marmo e prodotti premium.',
                    'wa_services_heading_default' => 'Cosa può fare una Web Agency {citta}',
                    'wa_cta_button_default' => 'Richiedi informazioni',
                ],
                'faq' => [
                    ['wa_faq_titolo' => 'Quanto costa rivolgersi a una Web Agency?', 'wa_faq_testo' => '<p>Il prezzo varia in base a obiettivi, dimensione del sito, complessità tecnica, contenuti, SEO e attività di marketing. Per questo è utile partire da una diagnosi: evita preventivi generici e aiuta a definire priorità reali.</p>'],
                    ['wa_faq_titolo' => 'Quali servizi offre una Web Agency {citta}?', 'wa_faq_testo' => '<p>Una web agency può occuparsi di sito web, WordPress, SEO, contenuti, design, campagne, analytics e strategia digitale. La differenza sta nella capacità di collegare questi elementi in un progetto coerente e misurabile.</p>'],
                    ['wa_faq_titolo' => 'Come capisco se il mio sito va rifatto?', 'wa_faq_testo' => '<p>Conviene valutare traffico, conversioni, velocità, chiarezza dei contenuti, posizionamento SEO e facilità di gestione. Se il sito non genera fiducia o contatti, può essere utile ripensarlo in modo strategico.</p>'],
                    ['wa_faq_titolo' => 'Una Web Agency può migliorare il posizionamento su Google?', 'wa_faq_testo' => '<p>Sì, se lavora sulla SEO in modo strutturato. Servono analisi delle ricerche, contenuti adeguati, struttura tecnica pulita, performance, link interni e monitoraggio costante dei risultati.</p>'],
                    ['wa_faq_titolo' => 'WordPress è adatto a un sito professionale?', 'wa_faq_testo' => '<p>Sì, WordPress può essere molto efficace per siti aziendali, landing page, blog ed e-commerce semplici o evoluti. La qualità dipende però da progetto, sviluppo, sicurezza, manutenzione e attenzione alla SEO.</p>'],
                    ['wa_faq_titolo' => 'Quanto conta la velocità del sito?', 'wa_faq_testo' => '<p>La velocità influisce su esperienza utente, conversioni e qualità percepita. Un sito lento può far perdere richieste, soprattutto da mobile. Per questo performance e struttura tecnica vanno considerate fin dall’inizio.</p>'],
                    ['wa_faq_titolo' => 'Le AI possono capire meglio un sito ben strutturato?', 'wa_faq_testo' => '<p>Sì, contenuti chiari, gerarchie corrette, dati strutturati e informazioni non ambigue aiutano motori di ricerca e sistemi AI a interpretare meglio servizi, competenze e contesto del progetto.</p>'],
                ],
                'service_cards' => [
                    ['wa_service_titolo' => 'Siti aziendali', 'wa_service_testo' => 'Creiamo siti professionali pensati per presentare servizi, valorizzare il brand e trasformare le visite in richieste.'],
                    ['wa_service_titolo' => 'Web design {citta}', 'wa_service_testo' => 'Progettiamo layout chiari, moderni e coerenti, con attenzione a leggibilità, percorsi utente e qualità percepita.'],
                    ['wa_service_titolo' => 'WordPress personalizzato', 'wa_service_testo' => 'Sviluppiamo siti WordPress su misura, facili da aggiornare e costruiti con una struttura ordinata e scalabile.'],
                    ['wa_service_titolo' => 'SEO strategy', 'wa_service_testo' => 'Definiamo keyword, architettura, contenuti e priorità per aumentare la visibilità organica su ricerche rilevanti.'],
                    ['wa_service_titolo' => 'SEM e landing page', 'wa_service_testo' => 'Affianchiamo campagne e pagine di destinazione quando servono traffico qualificato, test e acquisizione misurabile.'],
                    ['wa_service_titolo' => 'Comunicazione social', 'wa_service_testo' => 'Costruiamo messaggi e contenuti coerenti per rafforzare identità, riconoscibilità e relazione con il pubblico.'],
                    ['wa_service_titolo' => 'Contenuti SEO', 'wa_service_testo' => 'Scriviamo testi utili, leggibili e orientati alla ricerca, capaci di spiegare valore, servizi e differenze competitive.'],
                    ['wa_service_titolo' => 'Email e newsletter', 'wa_service_testo' => 'Progettiamo comunicazioni per mantenere il contatto con clienti e prospect, promuovendo contenuti, offerte e novità.'],
                    ['wa_service_titolo' => 'Analytics e report', 'wa_service_testo' => 'Configuriamo misurazioni e report per capire cosa funziona, dove intervenire e quali canali generano risultati.'],
                    ['wa_service_titolo' => 'E-commerce', 'wa_service_testo' => 'Realizziamo e ottimizziamo negozi online con attenzione a catalogo, schede prodotto, UX e percorso di acquisto.'],
                ],
            ],
            'variant_3' => [
                'label' => 'Variante 3 - SEO e acquisizione',
                'fields' => [
                    'wa_h2_hero_default' => 'Web design, SEO e marketing digitale per costruire una presenza online più autorevole.',
                    'wa_paragrafo_intro_default' => '<p>Servizi web e marketing per chi cerca una Web Agency {citta}: Chef Studio aiuta aziende e professionisti a progettare siti, contenuti e strategie digitali più efficaci.</p><p>Lavoriamo su ciò che conta davvero: messaggio, struttura, SEO, performance, identità visiva e capacità del sito di generare contatti qualificati.</p>',
                    'wa_titolo_sezione_1_default' => 'Web Agency {citta}: metodo, contenuti e risultati',
                    'wa_testo_sezione_1_default' => '<p>Una Web Agency {citta} deve aiutarti a trasformare il digitale in uno strumento di lavoro: non solo un sito da pubblicare, ma un sistema capace di comunicare, farsi trovare e generare opportunità.</p><p>Chef Studio parte da analisi e obiettivi per definire una soluzione proporzionata al progetto, integrando sito web, SEO, contenuti, design e marketing.</p><p>Il focus è costruire una presenza online credibile, comprensibile e sostenibile, evitando interventi scollegati tra loro.</p>',
                    'wa_titolo_1_sezione_2_default' => 'Siti professionali, WordPress e UX',
                    'wa_testo_1_sezione_2_default' => '<p>Il sito è spesso il primo punto di contatto tra brand e cliente. Deve caricarsi velocemente, spiegare bene l’offerta e accompagnare l’utente verso una scelta chiara.</p><p>Progettiamo siti WordPress responsive, con struttura personalizzata, gestione semplice e contenuti organizzati per migliorare esperienza utente, fiducia e conversioni.</p>',
                    'wa_titolo_2_sezione_2_default' => 'Web Agency {citta}: SEO e strategie di acquisizione',
                    'wa_testo_2_sezione_2_default' => '<p>La SEO aiuta il sito a intercettare ricerche utili e a rispondere meglio alle domande degli utenti. Per questo lavoriamo su architettura, testi, intenti di ricerca, tecnica e misurazione.</p><p>La strategia può concentrarsi su visibilità locale, nazionale o internazionale, secondo priorità, mercato, risorse disponibili e obiettivi commerciali.</p>',
                    'wa_cta_testo_default' => '<h2>Vuoi capire da dove partire?</h2><p>Raccontaci il tuo progetto e analizziamo insieme come migliorare sito, SEO, contenuti e acquisizione clienti con un percorso concreto.</p>',
                    'wa_intro_servizi_default' => 'Chef Studio supporta chi cerca una Web Agency {citta} con servizi coordinati: siti WordPress, SEO, design, contenuti, marketing e dati.',
                    'wa_titolo_scelta_default' => 'Perché affidare il progetto a una Web Agency {citta}',
                    'wa_testo_scelta_default' => '<p>Un sito può essere tecnicamente corretto ma poco efficace se non nasce da una strategia. Servono messaggi chiari, percorsi utente intuitivi, contenuti utili e obiettivi misurabili.</p><p>Chef Studio lavora per collegare identità, tecnologia e marketing: dal posizionamento del brand alla struttura del sito, dalla SEO ai dati che permettono di migliorare nel tempo.</p>',
                    'wa_titolo_faq_default' => 'FAQ per chi cerca una Web Agency {citta}',
                    'wa_titolo_contatti_default' => 'Inizia con un confronto strategico',
                    'wa_sottotitolo_contatti_default' => 'Valutiamo insieme sito, obiettivi e opportunità di crescita online.',
                    'wa_testo_form_default' => 'Compila il form o chiamaci al <br><a href="tel:{telefono_link}" class="no-decoration">{telefono}</a> per parlare del tuo progetto web e capire come possiamo aiutarti.',
                    'wa_hero_h1_default' => 'Web Agency {citta}',
                    'wa_hero_primary_button_default' => 'Richiedi un confronto',
                    'wa_hero_secondary_button_default' => 'Esplora i servizi',
                    'wa_phone_prompt_default' => 'Hai già un progetto? Chiamaci.',
                    'wa_examples_heading_default' => 'Alcuni esempi di progetti web',
                    'wa_example_site_1_text_default' => 'Sito per prodotti artigianali e vendita specializzata.',
                    'wa_example_site_2_text_default' => 'Sito per agriturismo, produzione agricola e ospitalità.',
                    'wa_example_site_3_text_default' => 'Sito per case vacanza, appartamenti e prenotazioni.',
                    'wa_example_site_4_text_default' => 'Sito per design, materiali pregiati e prodotto su misura.',
                    'wa_services_heading_default' => 'Servizi digitali della Web Agency {citta}',
                    'wa_cta_button_default' => 'Scrivici',
                ],
                'faq' => [
                    ['wa_faq_titolo' => 'Quando serve una Web Agency?', 'wa_faq_testo' => '<p>Serve quando il sito e la comunicazione digitale devono diventare strumenti di business. Una web agency può aiutarti a chiarire il messaggio, migliorare la visibilità, ottimizzare il sito e rendere misurabili i risultati.</p>'],
                    ['wa_faq_titolo' => 'Quanto costa una Web Agency {citta}?', 'wa_faq_testo' => '<p>Non esiste un prezzo unico: dipende da sito, SEO, contenuti, design, funzionalità e supporto richiesto. Un preventivo serio dovrebbe spiegare attività, obiettivi, priorità e livello di complessità del progetto.</p>'],
                    ['wa_faq_titolo' => 'Che differenza c’è tra sito web e strategia digitale?', 'wa_faq_testo' => '<p>Il sito è una parte della strategia. La strategia digitale definisce pubblico, obiettivi, messaggi, canali, SEO, contenuti e misurazione. Senza questa visione, il sito rischia di essere bello ma poco utile.</p>'],
                    ['wa_faq_titolo' => 'Una Web Agency può seguire anche la SEO locale?', 'wa_faq_testo' => '<p>Sì, se la SEO locale è coerente con gli obiettivi del progetto. Può lavorare su pagine, contenuti, Google Business Profile, dati strutturati e ricerche geografiche, senza limitare la strategia al solo territorio.</p>'],
                    ['wa_faq_titolo' => 'Meglio un sito custom o un template pronto?', 'wa_faq_testo' => '<p>Dipende dal progetto. Un template può essere sufficiente per esigenze semplici, ma un sito su misura offre più controllo su struttura, performance, identità, SEO e percorsi di conversione.</p>'],
                    ['wa_faq_titolo' => 'Posso migliorare il sito senza rifarlo da zero?', 'wa_faq_testo' => '<p>Sì, a volte bastano interventi mirati su contenuti, struttura, velocità, SEO e call to action. Prima di rifare tutto conviene analizzare cosa funziona, cosa limita i risultati e quali pagine hanno valore.</p>'],
                    ['wa_faq_titolo' => 'Perché i contenuti sono importanti per Google e AI?', 'wa_faq_testo' => '<p>Contenuti chiari e specifici aiutano utenti, motori di ricerca e sistemi AI a capire cosa offri, per chi è utile il servizio e quale valore distingue il progetto. La qualità semantica oggi è decisiva.</p>'],
                ],
                'service_cards' => [
                    ['wa_service_titolo' => 'Progettazione siti web', 'wa_service_testo' => 'Costruiamo siti chiari, veloci e responsive, progettati per comunicare valore e favorire contatti qualificati.'],
                    ['wa_service_titolo' => 'Web design {citta}', 'wa_service_testo' => 'Creiamo interfacce curate e funzionali, con attenzione a identità visiva, leggibilità e percorsi di conversione.'],
                    ['wa_service_titolo' => 'WordPress', 'wa_service_testo' => 'Realizziamo siti WordPress personalizzati, aggiornabili e ordinati, con gestione semplice dei contenuti.'],
                    ['wa_service_titolo' => 'SEO', 'wa_service_testo' => 'Lavoriamo su ricerca keyword, struttura, contenuti, tecnica e performance per migliorare la visibilità organica.'],
                    ['wa_service_titolo' => 'Campagne digitali', 'wa_service_testo' => 'Supportiamo campagne e landing page quando servono traffico qualificato, test rapidi e dati affidabili.'],
                    ['wa_service_titolo' => 'Social media marketing', 'wa_service_testo' => 'Aiutiamo a definire messaggi e contenuti coerenti per rafforzare riconoscibilità e relazione con il pubblico.'],
                    ['wa_service_titolo' => 'Copywriting e contenuti', 'wa_service_testo' => 'Scriviamo testi chiari, persuasivi e ottimizzati per spiegare servizi, prodotti e differenze competitive.'],
                    ['wa_service_titolo' => 'Email marketing', 'wa_service_testo' => 'Organizziamo comunicazioni, newsletter e sequenze per coltivare contatti e mantenere vivo il rapporto con i clienti.'],
                    ['wa_service_titolo' => 'Analisi dati', 'wa_service_testo' => 'Monitoriamo traffico, conversioni e comportamento degli utenti per individuare opportunità e priorità di intervento.'],
                    ['wa_service_titolo' => 'E-commerce', 'wa_service_testo' => 'Progettiamo negozi online gestibili, con attenzione a catalogo, schede prodotto, UX e processo di acquisto.'],
                ],
            ],
        ];
    }
}

if (!function_exists('lpg_wa_yoast_description_templates')) {
    function lpg_wa_yoast_description_templates() {
        return [
            'variant_1' => 'Web Agency {citta}: siti web custom, SEO e marketing digitale per migliorare presenza online, contatti e crescita del tuo progetto.',
            'variant_2' => 'Web Agency {citta}: strategie digitali, SEO e siti web professionali per valorizzare il tuo brand e acquisire nuovi clienti.',
            'variant_3' => 'Web Agency {citta}: sviluppiamo progetti digitali orientati a visibilità, autorevolezza e acquisizione di contatti qualificati.',
        ];
    }
}

if (!function_exists('lpg_wa_repeater_default_for_field')) {
    function lpg_wa_repeater_default_for_field($field_name) {
        if ($field_name === 'wa_faq_default') {
            return lpg_wa_default_faq_rows();
        }

        if ($field_name === 'wa_service_cards_default') {
            return lpg_wa_default_service_cards();
        }

        foreach (lpg_wa_text_variants() as $variant_key => $variant) {
            if ($field_name === lpg_wa_variant_field_name('wa_faq_default', $variant_key)) {
                return $variant['faq'] ?? [];
            }

            if ($field_name === lpg_wa_variant_field_name('wa_service_cards_default', $variant_key)) {
                return $variant['service_cards'] ?? [];
            }
        }

        return [];
    }
}

if (function_exists('add_filter')) {
    $lpg_wa_repeater_fields = ['wa_faq_default', 'wa_service_cards_default'];

    foreach (array_keys(lpg_wa_text_variants()) as $lpg_wa_variant_key) {
        $lpg_wa_repeater_fields[] = lpg_wa_variant_field_name('wa_faq_default', $lpg_wa_variant_key);
        $lpg_wa_repeater_fields[] = lpg_wa_variant_field_name('wa_service_cards_default', $lpg_wa_variant_key);
    }

    foreach ($lpg_wa_repeater_fields as $lpg_wa_repeater_field) {
        add_filter('acf/load_value/name=' . $lpg_wa_repeater_field, function($value) use ($lpg_wa_repeater_field) {
            if (!empty($value)) {
                return $value;
            }

            return lpg_wa_repeater_default_for_field($lpg_wa_repeater_field);
        }, 10, 1);
    }
}
