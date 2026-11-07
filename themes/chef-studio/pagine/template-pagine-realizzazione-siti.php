<?php
/*
Template Name: Template pagine realizzazione siti
*/
get_header();

echo "<!-- TEMPLATE: template-pagine-realizzazione-siti.php CARICATO -->";

if (function_exists('get_field')) {
    $post_id = get_queried_object_id();

    // ================== TESTI BASE ==================
    $main_keyword        = get_field('main_keyword', $post_id);
    $paragrafo_intro     = get_field('paragrafo_intro', $post_id);

    $titolo_sezione_1    = get_field('titolo_sezione_1', $post_id);
    $testo_sezione_1     = get_field('testo_sezione_1', $post_id);

    $titolo_1_sezione_2  = get_field('titolo_1_sezione_2', $post_id);
    $testo_1_sezione_2   = get_field('testo_1_sezione_2', $post_id);

    $titolo_2_sezione_2  = get_field('titolo_2_sezione_2', $post_id);
    $testo_2_sezione_2   = get_field('testo_2_sezione_2', $post_id);

    $intro_servizi       = get_field('intro_servizi', $post_id);

    // Città
    $citta = get_field('citta', $post_id);
    $citta_lowercase = $citta ? mb_strtolower($citta) : '';
    $citta_lowercase_nospace = $citta
        ? mb_strtolower(preg_replace('/\s+/', '', remove_accents($citta)))
        : '';

    // Numero telefono form (come nel template SEO)
    $numero_form = '+39 352 0996 996';
    $numero_form_per_link = preg_replace('/\s+/', '', $numero_form);

    // ================== PATH IMMAGINI TEMA ==================
    $theme_dir            = get_stylesheet_directory_uri();
    $theme_rs_img_url     = trailingslashit($theme_dir . '/img/realizzazione-siti-web');
    $theme_rs_img_citta   = trailingslashit($theme_rs_img_url . 'citta');
    $theme_common_img_url = trailingslashit($theme_dir . '/img');

    // ================== ARGOMENTO / DEFAULT DI CATEGORIA ==================
    $terms = wp_get_post_terms($post_id, 'argomento_categoria');
    $term_ref = '';
    if (!is_wp_error($terms) && !empty($terms)) {
        $term_ref = 'argomento_categoria_' . $terms[0]->term_id;
    }

       // ================== HELPER PER IMMAGINI RS ==================
    /**
     * Restituisce l'URL di un'immagine:
     * 1. Campo pagina: rs_immagine_*
     * 2. Default categoria: stesso nome campo sul termine (rs_immagine_*)
     * 3. Back-compat: versione *_default (rs_immagine_*_default o immagine_*_default)
     * 4. Fallback nel tema: /img/realizzazione-siti-web/citta/{fallback_filename}
     */
    $get_rs_image_url = function($field_base, $fallback_filename = '') use ($post_id, $term_ref, $theme_rs_img_citta) {

        // 1) Campo sulla pagina
        $img = get_field($field_base, $post_id);

        // 2) Campo sul termine con LO STESSO NOME (rs_immagine_main)
        if (!$img && $term_ref) {
            $img = get_field($field_base, $term_ref);
        }

        // 3) Back-compat: *_default (rs_immagine_main_default o immagine_main_default)
        if (!$img && $term_ref) {
            // stessa base + _default
            $img = get_field($field_base . '_default', $term_ref);

            // se inizia per rs_, prova anche senza rs_ + _default
            if (!$img && strpos($field_base, 'rs_') === 0) {
                $no_rs = substr($field_base, 3); // rs_immagine_main -> immagine_main
                $img = get_field($no_rs . '_default', $term_ref);
            }
        }

        // Se abbiamo qualcosa (ID o array ACF)
        if ($img) {
            if (is_array($img) && !empty($img['url'])) {
                return $img['url'];
            } elseif (is_numeric($img)) {
                $url = wp_get_attachment_url((int) $img);
                if ($url) return $url;
            }
        }

        // 4) Fallback al tema, se indicato
        if ($fallback_filename) {
            return $theme_rs_img_citta . $fallback_filename;
        }

        return '';
    };


   
    $slug_citta_ns = $citta_lowercase_nospace ?: 'default';

    // ================== URL IMMAGINI DINAMICHE ==================
    $rs_url_designer      = $get_rs_image_url('rs_immagine_designer',      "andrea-biagi-web-designer-{$slug_citta_ns}.png");
    $rs_url_main          = $get_rs_image_url('rs_immagine_main',          "realizzazione-siti-web-{$slug_citta_ns}.jpg");
    $rs_url_secondary     = $get_rs_image_url('rs_immagine_secondary',     "realizzazione-siti-internet-{$slug_citta_ns}.jpg");

    $rs_url_sito_1        = $get_rs_image_url('rs_immagine_sito_1',        "siti-web-per-artigiani-{$slug_citta_ns}.jpg");
    $rs_url_sito_2        = $get_rs_image_url('rs_immagine_sito_2',        "siti-per-aziende-agricole-{$slug_citta_ns}.jpg");
    $rs_url_sito_3        = $get_rs_image_url('rs_immagine_sito_3',        "siti-per-hotel-case-vacanza-{$slug_citta_ns}.jpg");
    $rs_url_sito_4        = $get_rs_image_url('rs_immagine_sito_4',        "siti-per-agenzia-immobiliare-{$slug_citta_ns}.jpg");
    $rs_url_sito_5        = $get_rs_image_url('rs_immagine_sito_5',        "sito-per-aziende-{$slug_citta_ns}.jpg");
    $rs_url_sito_6        = $get_rs_image_url('rs_immagine_sito_6',        "realizzazione-e-commerce-{$slug_citta_ns}.jpg");
    $rs_url_sito_7        = $get_rs_image_url('rs_immagine_sito_7',        "siti-per-architetti-{$slug_citta_ns}.jpg");
    $rs_url_sito_8        = $get_rs_image_url('rs_immagine_sito_8',        "siti-per-stabilimenti-balneari-{$slug_citta_ns}.jpg");

    $rs_url_seo_friendly  = $get_rs_image_url('rs_immagine_seo_friendly',  "siti-seo-friendly-{$slug_citta_ns}.jpg");
    $rs_url_creazione     = $get_rs_image_url('rs_immagine_creazione',     "creazione-siti-web-{$slug_citta_ns}.jpg");
}
?>












	<div class="contenitore back-pattern-chef-web chef-header">
<div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
<div class="struttura-container struttura-2-standard centro-verticale hero-header-enhanced">
  <div class="div-1">


      <h1>Realizzazione siti web <?php echo $citta; ?></h1>


    <?php if (get_sub_field('sottotitolo_1_header')): ?>
      <h2><?php echo esc_html(get_sub_field('sottotitolo_1_header')); ?></h2>
    <?php endif; ?>

   <?php
$paragrafo_intro = get_field('paragrafo_intro');

if ( ! empty($paragrafo_intro) ) : ?>

	<?php echo wp_kses_post($paragrafo_intro); ?>

<?php else : ?>

	<p>
		Siamo l’agenzia specializzata nella realizzazione di siti web a <?php echo $citta; ?> pensati per attrarre clienti, performare e posizionarsi su Google. Vuoi un sito veloce, moderno e progettato per convertire?<br/><br/>
		Studiamo mercato, competitor e comportamento degli utenti per creare siti internet strategici, dove UX, UI, grafica, performance e SEO lavorano insieme per far crescere il tuo business.
	</p>

<?php endif; ?>


	  
	  
	  
	   <div class="button-container hero-actions">
          <?php if (get_sub_field('link_bottone_1_header') && get_sub_field('testo_bottone_1_header')): ?>
            <a
              href="<?php echo esc_url(get_sub_field('link_bottone_1_header')); ?>"
              class="btn btn-super hero-action-link hero-action-link-primary"
            >
              <span><?php echo esc_html(get_sub_field('testo_bottone_1_header')); ?></span>
              <span class="hero-btn-arrow" aria-hidden="true"></span>
            </a>
          <?php endif; ?>

          <a href="#form-ancora" class="btn btn-super hero-action-link hero-action-link-primary">
            <span>Parla col nostro esperto web</span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>

          <a href="<?php echo esc_url(get_permalink(2697)); ?>" class="btn hero-action-link hero-action-link-secondary">
            <span>Guarda tutti i servizi web</span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        </div>

        <div class="local-container">

          <div class="img-icon">
            <div class="img-icon"><img src="<?php echo esc_url($rs_url_designer); ?>" alt="Andrea Biagi web designer <?php echo esc_attr($citta); ?>"/></div>
          </div>

          <div>
            Preferisci parlarne subito? Chiamaci!<br/>
            <span class="telefono-icon" aria-hidden="true"></span>
            <?php 
              $telefono = get_field('telefono', 'option');
              if ($telefono): 
                $telefono_link = preg_replace('/[^0-9+]/', '', $telefono);
            ?>
              <a href="tel:<?php echo esc_attr($telefono_link); ?>">
                <?php echo esc_html($telefono); ?>
              </a>
            <?php endif; ?>
          </div>

        </div>
	  
	  


  </div>

  <div class="div-2">
    <?php $immagine = get_sub_field('immagine'); ?>
    <?php if ($immagine): ?>
      <img src="<?php echo esc_url($immagine['url']); ?>" alt="<?php echo esc_attr($immagine['alt']); ?>">
    <?php endif; ?>
	 <img class="img-sbordo"
                             src="<?php echo esc_url($rs_url_main); ?>"
                             alt="realizzazione siti web <?php echo esc_attr($citta); ?>"/>
	  
  </div>
</div>
		</div></div>






<div class="contenitore">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">

    <div class="struttura-container struttura-2-invertito">

        <div class="div-1">

            <h2>
  <span class="testo-arancio">
    <?php if (!empty($titolo_sezione_1)) : ?>
      <?php echo esc_html($titolo_sezione_1); ?>
    <?php else : ?>
      <?php echo esc_html('Realizzazione siti internet ' . $citta); ?>
    <?php endif; ?>
  </span>
</h2>

            <?php if ( ! empty($testo_sezione_1) ) : ?>

    <?php echo $testo_sezione_1; ?>

<?php else : ?>

    <!-- BLOCCO: Siti web WordPress -->
    <p>Per la <strong>realizzazione di siti web a <?php echo $citta; ?></strong> di alto livello scegliamo spesso <strong>WordPress</strong>, il CMS più usato al mondo: una piattaforma solida, flessibile e perfetta per far crescere il tuo progetto nel tempo. Il vantaggio? Un sito professionale che puoi gestire in autonomia, in modo semplice e veloce.</p>

    <p>Se lo desideri, ti consegniamo un sito pensato per renderti indipendente: potrai aggiornare testi e immagini, creare nuove pagine, pubblicare articoli e organizzare i contenuti in modo ordinato. Inoltre, WordPress permette di integrare funzionalità avanzate tramite plug-in, rendendolo adatto a tantissimi progetti: <strong>siti aziendali</strong>, <strong>e-commerce</strong>, <strong>blog</strong>, <strong>portfolio</strong>, <strong>landing page</strong> e molto altro.</p>

    <p>Anche se esistono temi già pronti, il nostro approccio è diverso: preferiamo sviluppare <strong>template su misura</strong>, progettati intorno alla tua identità e ai tuoi obiettivi, per ottenere un sito più veloce, più chiaro da navigare e più efficace nel trasformare visite in contatti.</p>

    <p>Per esigenze particolari, oltre a WordPress realizziamo anche <strong>siti con CMS sviluppati ad hoc</strong>, quando serve una soluzione completamente personalizzata.</p>

<?php endif; ?>


        </div>

        <div class="div-2">
            <?php if (!empty($rs_url_secondary)) : ?>
                <img class="flex"
                     src="<?php echo esc_url($rs_url_secondary); ?>"
                     alt="realizzazione siti internet <?php echo esc_attr($citta_lowercase); ?>"/>
            <?php endif; ?>
        </div>
    </div><!-- struttura 2 -->

</div><!-- / contenuto-->
	</div>


<div class="contenitore back-bonus-color">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
        <div class="struttura-container struttura-2-standard">

            <div class="div-1 txt-bianco">

                <h2>
  <span class="testo-arancio">
    <?php if (!empty($titolo_1_sezione_2)) : ?>
      <?php echo esc_html($titolo_1_sezione_2); ?>
    <?php else : ?>
      <?php echo esc_html('Realizzazione siti internet ' . $citta . ' in Wordpress'); ?>
    <?php endif; ?>
  </span>
</h2>

				
               <?php if ( ! empty($testo_sezione_2) ) : ?>

    <?php echo $testo_sezione_2; ?>

<?php else : ?>

    <!-- BLOCCO: Siti web WordPress -->
    <p>Per la <strong>realizzazione di siti web a <?php echo $citta; ?></strong> di alto livello scegliamo spesso <strong>WordPress</strong>, il CMS più usato al mondo: una piattaforma solida, flessibile e perfetta per far crescere il tuo progetto nel tempo. Il vantaggio? Un sito professionale che puoi gestire in autonomia, in modo semplice e veloce.</p>

    <p>Se lo desideri, ti consegniamo un sito pensato per renderti indipendente: potrai aggiornare testi e immagini, creare nuove pagine, pubblicare articoli e organizzare i contenuti in modo ordinato. Inoltre, WordPress permette di integrare funzionalità avanzate tramite plug-in, rendendolo adatto a tantissimi progetti: <strong>siti aziendali</strong>, <strong>e-commerce</strong>, <strong>blog</strong>, <strong>portfolio</strong>, <strong>landing page</strong> e molto altro.</p>

    <p>Anche se esistono temi già pronti, il nostro approccio è diverso: preferiamo sviluppare <strong>template su misura</strong>, progettati intorno alla tua identità e ai tuoi obiettivi, per ottenere un sito più veloce, più chiaro da navigare e più efficace nel trasformare visite in contatti.</p>

    <p>Per esigenze particolari, oltre a WordPress realizziamo anche <strong>siti con CMS sviluppati ad hoc</strong>, quando serve una soluzione completamente personalizzata.</p>

<?php endif; ?>



            </div>

            <div class="div-2 txt-bianco">

                <h2>
  <span class="testo-arancio">
    <?php if (!empty($titolo_2_sezione_2)) : ?>
      <?php echo esc_html($titolo_2_sezione_2); ?>
    <?php else : ?>
      <?php echo esc_html('Creazione siti web ' . $citta . ' ed e-commerce'); ?>
    <?php endif; ?>
  </span>
</h2>

                <?php if ( ! empty($testo_1_sezione_2) ) : ?>

    <?php echo $testo_1_sezione_2; ?>

<?php else : ?>

    <!-- BLOCCO: E-commerce -->
    <p>La <strong>realizzazione di un e-commerce a <?php echo $citta; ?></strong> non significa solo “mettere online dei prodotti”: significa costruire un sistema di vendita che funzioni davvero. Per questo curiamo ogni dettaglio che incide sulle conversioni, dall’esperienza utente alla grafica, fino alla struttura delle pagine prodotto e del checkout.</p>

    <p>Un e-commerce efficace deve essere <strong>veloce</strong>, <strong>chiaro</strong> e <strong>credibile</strong>: deve comunicare in pochi secondi cosa vendi, perché conviene acquistare da te e cosa rende la tua attività diversa dalle altre. Quando il sito trasmette fiducia, le persone comprano con più facilità.</p>

    <p>Supportiamo aziende di ogni dimensione con soluzioni <strong>su misura</strong>, pensate in base al tuo catalogo, al tuo pubblico e ai tuoi obiettivi. Ti consegniamo una piattaforma professionale che puoi gestire in autonomia (prodotti, immagini, prezzi, contenuti e promozioni) e ti affianchiamo anche dopo la pubblicazione, con <strong>supporto tecnico</strong> e <strong>consulenza SEO</strong>, per aiutarti a migliorare la visibilità e far crescere le vendite nel tempo.</p>

<?php endif; ?>


            </div>
        </div><!-- contenitore -->
    </div><!-- struttura 2 -->
</div>

<?php
$telefono      = get_field('telefono', 'option');
$telefono_link = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';

$cta_url       = '#form-ancora';
$testo_bottone = 'Contattaci!';
$testo_cta     = '<h2>Vuoi un sito web che lavori davvero per la tua attività?</h2><p>Parla con Chef Studio e scopri come possiamo progettare un sito su misura, veloce, chiaro e pensato per trasformare visite in contatti, richieste e nuove opportunità.</p>';
$orari_cta     = '';

$immagine_cta_url = home_url('/wp-content/uploads/2025/11/call-to-action-andrea.png');
$immagine_cta_alt = 'Andrea Biagi - web designer Chef Studio';
?>

<div class="contenitore">
  <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">

	    <section class="call-to-action call-to-action-desktop cta-premium local-page-template-cta">

      <div class="cta-premium-inner">

        <figure class="cta-premium-media">
          <?php echo chefstudio_responsive_image($immagine_cta_url, 'medium', [
            'alt'      => $immagine_cta_alt,
            'loading'  => 'lazy',
            'decoding' => 'async',
            'sizes'    => '(max-width: 768px) 180px, 217px',
          ]); ?>
        </figure>

        <div class="cta-premium-copy">
          <div class="info">
            <?php echo wp_kses_post($testo_cta); ?>
          </div>
        </div>

        <div class="cta-premium-actions">

          <a
            href="<?php echo esc_url($cta_url); ?>"
            class="btn btn-super neon cta-premium-button"
          >
            <span><?php echo esc_html($testo_bottone); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>

          <?php if (!empty($telefono)): ?>
            <a class="cta-premium-phone" href="tel:<?php echo esc_attr($telefono_link); ?>">
	              <span class="cta-premium-phone-icon" aria-hidden="true">
	                <img
	                  src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/phone.svg'); ?>"
	                  alt="icona telefono"
	                  aria-hidden="true"
	                >
	              </span>
              <span>
                <strong><?php echo esc_html($telefono); ?></strong>
                <?php if (!empty($orari_cta)): ?>
                  <small><?php echo esc_html($orari_cta); ?></small>
                <?php endif; ?>
              </span>
            </a>
          <?php endif; ?>

        </div>

      </div>

    </section>

  </div>
</div>

<?php
$blocco_id = 2717;

// H2 replacement
$replacement_h2 = '<h2><span class="testo-arancio">Principali servizi per Realizzazione Siti Web ' . esc_html($citta) . '</span></h2>';

// 1) Prova a leggere dal blocco
$intro_servizi = get_field('intro_servizi', $blocco_id);

// 2) Se vuoto, prova dalla pagina corrente
if (empty(trim((string) $intro_servizi))) {
  $intro_servizi = get_field('intro_servizi'); // usa il post "corrente"
}

// Fallback testo
if (!empty(trim((string) $intro_servizi))) {
  // Se è un WYSIWYG spesso contiene già <p> ecc. Quindi NON wrappare per forza in <p>.
  $replacement_p = '<div class="intro-servizi">' . wp_kses_post($intro_servizi) . '</div>';
} else {
  $replacement_p = '<p>Chef Studio realizza siti web per attività e professionisti di ' . esc_html($citta) . ' e in tutta Italia da oltre 10 anni, unendo sviluppo, UX e strategia SEO. L’obiettivo è creare un sito utile, efficace, bello da vedere, veloce e pronto a generare contatti. Scopri i nostri servizi principali.</p>';
}

// H2 + intro insieme
$replacement_block = $replacement_h2 . "\n" . $replacement_p;

if (have_rows('layout_flessibile_1', $blocco_id)) {
  while (have_rows('layout_flessibile_1', $blocco_id)) {
    the_row();

    ob_start();

    get_template_part('parti/' . get_row_layout(), null, [
      'blocco_id' => $blocco_id,
    ]);

    $html = ob_get_clean();

    // sostituisci SOLO il primo H2 che trovi e aggiungi subito dopo il testo intro
    $html = preg_replace('~<h2\b[^>]*>.*?</h2>~si', $replacement_block, $html, 1);

    echo $html;
  }
}
?>



<div class="contenitore">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard contenuto-scroll chef-elenco-siti-local">
		<div class="titoletto">
			    <h2>Creazione siti internet <?php echo $citta; ?>: alcuni esempi.</h2>
		</div>


		<div class="anteprime-4">
    <ul class="servizi-apertura">

        <!-- 1 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_1)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_1); ?>"
                             alt="siti web per artigiani <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">woodylab.it</span>
                    </div>
                    <div class="testo">
                        Sito web per prodotti artigianali.
                    </div>
                </div>

            
        </li>


        <!-- 2 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_2)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_2); ?>"
                             alt="siti per aziende agricole <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">ortobiosarzana.it</span>
                    </div>
                    <div class="testo">
                        Sito Web azienda agricola e agriturismo.
                    </div>
                </div>

            
        </li>


        <!-- 3 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_3)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_3); ?>"
                             alt="siti per hotel e case vacanza <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">sullaspiaggia.com</span>
                    </div>
                    <div class="testo">
                        Sito Web per appartamenti e case vacanza.
                    </div>
                </div>

            
        </li>


        <!-- 4 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_4)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_4); ?>"
                             alt="siti per agenzia immobiliare <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">tuscanyinstyle.com</span>
                    </div>
                    <div class="testo">
                        Sito Internet per agenzia immobiliare.
                    </div>
                </div>

            
        </li>


        <!-- 5 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_5)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_5); ?>"
                             alt="siti per aziende <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">dedalostone.com</span>
                    </div>
                    <div class="testo">
                        Sito web per prodotti di design in marmo.
                    </div>
                </div>

            
        </li>


        <!-- 6 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_6)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_6); ?>"
                             alt="realizzazione e-commerce <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">www.termogo.com</span>
                    </div>
                    <div class="testo">
                        E-commerce per rivenditore di ricambi per caldaie.
                    </div>
                </div>

            
        </li>


        <!-- 7 -->
        <li>
            

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_7)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_7); ?>"
                             alt="siti per architetti <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">www.bonfigliarchitect.com</span>
                    </div>
                    <div class="testo">
                        Sito internet per architetto.
                    </div>
                </div>

            
        </li>


        <!-- 8 -->
        <li>
           

                <div class="contenitore-img">
                    <?php if (!empty($rs_url_sito_8)) : ?>
                        <img class="flex"
                             src="<?php echo esc_url($rs_url_sito_8); ?>"
                             alt="siti per stabilimenti balneari <?php echo esc_attr($citta_lowercase); ?>"/>
                    <?php endif; ?>
                </div>

                <div class="testo-anteprima">
                    <div class="titolo">
                        <span class="url">bagnovenezia.eu</span>
                    </div>
                    <div class="testo">
                        Sito internet per stabilimento balneare.
                    </div>
                </div>

            
        </li>

    </ul>
</div>
</div></div>


<div class="contenitore">
    <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <div class="struttura-container struttura-2-standard padding-bot-60px">

        <div class="div-1">

            <h2>Siti SEO friendly a <?php echo $citta; ?></h2>
            <p>Il posizionamento sui motori di ricerca rappresenta un elemento chiave per un sito web
                <?php echo $citta; ?>. Raggiungere le prime pagine di Google significa garantirsi una visibilità
                superiore rispetto ai concorrenti. Google offre linee guida specifiche per raggiungere questi obiettivi,
                ed è cruciale considerarle sin dalle fasi iniziali di progettazione.</p>
            <p>Progettare un sito web con un focus sul posizionamento, sia a livello locale a <?php echo $citta; ?> che
                nazionale, contribuisce all'espansione del tuo business nel mercato target. Le strategie da adottare
                sono numerose, ma con una pianificazione accurata e il supporto della nostra Agenzia SEO esperta, è
                possibile raggiungere risultati tangibili e duraturi.</p>

        </div>

        <div class="div-2">
            <?php if (!empty($rs_url_seo_friendly)) : ?>
                <img class="flex"
                     src="<?php echo esc_url($rs_url_seo_friendly); ?>"
                     alt="realizzazione siti internet SEO friendly <?php echo esc_attr($citta_lowercase); ?>"/>
            <?php endif; ?>
        </div>
    </div><!-- struttura 2 -->
		</div></div>


<?php
$citta_faq_testo = (string) $citta;
$citta_faq_html = esc_html($citta);
$web_faq_title = 'FAQ - Domande frequenti sulla realizzazione dei siti internet ' . $citta_faq_testo;
$web_faq_rows = [
  [
    'titolo' => 'A cosa serve un sito web?',
    'testo'  => '<p>A vendere, a farti conoscere, a monetizzare. Un sito web a ' . $citta_faq_html . ' è il tuo biglietto da visita digitale. Puoi mostrare al mondo prodotti e servizi, presentare il tuo brand, generare traffico dai motori di ricerca e tramite pubblicità su social network. Per un brand o un professionista è essenziale avere delle pagine web ben curate e esplicative del lavoro che viene svolto.</p>',
  ],
  [
    'titolo' => 'Come posso trovare clienti vicini alla mia attività a ' . $citta_faq_testo . '?',
    'testo'  => '<p>Per le attività che beneficiano di una ricerca locale sui motori di ricerca possiamo attuare diverse strategie. È utile anche aggiornare la scheda Google My Business con orari e specifiche informazioni su prodotti o servizi per comparire sulle mappe di Google.</p>',
  ],
  [
    'titolo' => 'Il sito è ottimizzato e mobile friendly?',
    'testo'  => '<p>Sì, i siti ' . $citta_faq_html . ' che realizziamo sono responsive. Il layout si adatta agli schermi degli smartphone e su tutti gli altri dispositivi.</p>',
  ],
  [
    'titolo' => 'Il sito web ha un costo annuale?',
    'testo'  => '<p>I siti hanno bisogno di essere caricati su un hosting. I costi di questi servizi variano in base alle esigenze e partono da 50/100 € l’anno circa. Alcune piattaforme professionali hanno costi molto più alti. Costi di aggiornamento e manutenzione possono variare da poche centinaia di euro annui a costi più cospicui.</p>',
  ],
  [
    'titolo' => 'Posso aggiornare in autonomia il sito?',
    'testo'  => '<p>Sì, diamo la possibilità al cliente di fare alcune operazioni sul sito come aggiornare i prodotti o scrivere articoli. Se non hai tempo da dedicare al progetto possiamo pensare a tutto noi.</p><p>Lavoriamo sia in modo continuativo, che è la modalità per ottenere risultati ottimali, sia fornendo anche consulenze di qualche ora nel caso il cliente sia già esperto in fatto di SEO e abbia bisogno di consigli sulle direzioni da prendere. Forniamo anche consulenze SEO all’interno delle aziende di ' . $citta_faq_html . ' e formazione di personale per la gestione di alcuni aspetti in autonomia.</p><p>Forniamo consulenze anche a web agency che non hanno la minima idea di cosa sia la SEO e hanno clienti che vorrebbero ottenere ottimi risultati su Google.</p>',
  ],
  [
    'titolo' => 'Il mio sito apparirà sui motori di ricerca?',
    'testo'  => '<p>La nostra agenzia di web marketing è specializzata in posizionamento di siti ' . $citta_faq_html . ' su Google e realizzazione siti web SEO friendly. Possiamo creare un progetto al fine di posizionarti per le parole chiave strategiche che potranno generare contatti e vendite.</p>',
  ],
];

if (function_exists('chefstudio_schema_collect_faq_items')) {
  chefstudio_schema_collect_faq_items($web_faq_rows, 'local-realizzazione-siti');
}
?>

<div class="contenitore">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <section class="faq-content faq-premium faq-standalone-layout">
      <div class="faq-intro">
        <h2><?php echo esc_html($web_faq_title); ?></h2>
      </div>

      <div class="faq-list">
        <?php foreach ($web_faq_rows as $index => $row) :
          $titolo = $row['titolo'] ?? '';
          $testo  = $row['testo'] ?? '';
          $titolo_txt = trim(wp_strip_all_tags((string) $titolo));
          $testo_txt  = trim(wp_strip_all_tags((string) $testo));
          if ($titolo_txt === '' && $testo_txt === '') continue;
          $id = 'faq-title-web-' . get_queried_object_id() . '-' . ($index + 1);
        ?>
          <div class="faq-accordion">
            <input type="checkbox" class="tgg-title" id="<?php echo esc_attr($id); ?>">

            <div class="faq-accordion-title">
              <label for="<?php echo esc_attr($id); ?>">
                <span class="faq-toggle-icon" aria-hidden="true"></span>
                <?php if ($titolo_txt !== '') : ?>
                  <span class="faq-question-text"><?php echo esc_html($titolo_txt); ?></span>
                <?php endif; ?>
                <span class="arrow-icon" aria-hidden="true">
                  <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/arrow-down.svg" alt="Apri risposta" aria-hidden="true">
                </span>
              </label>
            </div>

            <?php if ($testo_txt !== '') : ?>
              <div class="faq-accordion-content">
                <div class="faq-answer-inner">
                  <?php echo wp_kses_post($testo); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>














<?php $svg_base_url = get_stylesheet_directory_uri() . '/img/svg/'; ?>

<div class="contenitore local-seo-contact-section">
  <div class="contenuto-standard pad-x-standard local-seo-contact-wrap">
    <span id="form-ancora" class="local-seo-anchor" aria-hidden="true"></span>
    <span id="form-anchor" class="local-seo-anchor" aria-hidden="true"></span>

    <section class="local-seo-contact" aria-labelledby="local-rs-contact-title">
      <div class="local-seo-contact-copy">
	        <h2 id="local-rs-contact-title">Web design <?php echo esc_html($citta); ?></h2>
	        <?php
	        /*
	         * Rimosso dal layout CTA form del 2026-06-06:
	         * <div class="local-seo-contact-intro">
	         *   Raccontaci il progetto o l'attività che vuoi presentare online. Ti aiutiamo a definire la soluzione più adatta ai tuoi obiettivi.
	         * </div>
	         */
	        ?>

        <div class="local-seo-consultant-card">
          <?php if (!empty($rs_url_creazione)) : ?>
            <figure class="local-seo-consultant-photo">
              <img
                src="<?php echo esc_url($rs_url_creazione); ?>"
                alt="Realizzazione siti web <?php echo esc_attr($citta); ?> - Andrea Biagi - Chef Studio"
              >
            </figure>
          <?php endif; ?>

	          <div class="local-seo-consultant-info">
	            <h3>Andrea Biagi</h3>
	            <p>Web designer</p>

            <ul class="local-seo-benefits">
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-strategia.svg'); ?>" alt="Sito web su misura" aria-hidden="true">
                <span>Siti web progettati su misura</span>
              </li>
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-data-driven.svg'); ?>" alt="Design e strategia" aria-hidden="true">
                <span>Design e strategia coordinati</span>
              </li>
              <li>
                <img src="<?php echo esc_url($svg_base_url . 'icona-referente-unico.svg'); ?>" alt="Supporto diretto" aria-hidden="true">
                <span>Supporto diretto durante tutto il progetto</span>
              </li>
            </ul>
          </div>
        </div>

	        <?php
	        /*
	         * Rimosso dal layout CTA form del 2026-06-06:
	         * <div class="local-seo-process">
	         *   <h3>Come funziona?</h3>
	         *   <p>Valutiamo contenuti, funzionalità e priorità del progetto per capire quale struttura può offrire i risultati migliori.</p>
	         * </div>
	         */
	        ?>

	        <div class="local-seo-trust-note">
	          <img src="<?php echo esc_url($svg_base_url . 'icona-sicuro.svg'); ?>" alt="Soluzione su misura" aria-hidden="true">
	          <span>Valutazione gratuita su misura, poi scegli come procedere.</span>
	        </div>

	        <div class="local-seo-phone-row">
	          <a class="local-seo-whatsapp-inline-icon" href="https://wa.me/<?php echo esc_attr($numero_form_per_link); ?>" target="_blank" rel="noopener noreferrer" aria-label="Scrivici su WhatsApp">
	            <img src="<?php echo esc_url($svg_base_url . 'whatsapp.svg'); ?>" alt="WhatsApp">
	          </a>
	          <span>Chiamaci: <a class="local-seo-phone-link" href="tel:<?php echo esc_attr($numero_form_per_link); ?>"><strong><?php echo esc_html($numero_form); ?></strong></a></span>
	        </div>
	      </div>

	      <div class="local-seo-form-card">
	        <div class="local-seo-form-heading">
	          <h3>Richiedi un'analisi preliminare.</h3>
	          <p>Raccontaci il tuo caso, troveremo le migliori opportunità.</p>
	        </div>

        <div class="local-seo-form-response-time">
          <img src="<?php echo esc_url($svg_base_url . 'icona-orologio.svg'); ?>" alt="Risposta entro un giorno lavorativo" aria-hidden="true">
          <span>Rispondiamo normalmente entro un giorno lavorativo.</span>
        </div>

	        <div class="contatti local-seo-gravity-form">
	          <?php gravity_form(1, false, false, false, '', true, 0); ?>
	        </div>
      </div>
    </section>
  </div>
</div>





<?php get_footer(); ?>
