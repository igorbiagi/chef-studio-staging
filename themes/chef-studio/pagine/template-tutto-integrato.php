<?php
/*
Template Name: Template Tutto Integrato (Local Pages)
*/
get_header();
// ===== MAPPATURA ACF -> VARIABILI USATE NEL TEMPLATE =====
// NB: non cambiare il resto del template: qui "traduco" i campi ACF e il repeater FAQ nelle variabili attese.

echo "<!-- TEMPLATE: template-tutto-integrato.php CARICATO -->";

if (function_exists('get_field')) {
    // testo semplice
    $main_keyword = get_field('main_keyword');
    // $h2 = get_field('h2');
    $meta_title = get_field('meta_title');
    $meta_description = get_field('meta_description');
	
	$h2_hero = get_field('h2_hero');
	$paragrafo_hero = get_field('paragrafo_hero');
		
    $titolo_chi_siamo = get_field('titolo_chi_siamo');
    $testo_chi_siamo = get_field('testo_chi_siamo');
    $titolo_elenco_chi_siamo = get_field('titolo_elenco_chi_siamo');
    $elenco_chi_siamo = get_field('elenco_chi_siamo');
    $frase_dopo_elenco = get_field('frase_dopo_elenco');
    $testo_bottone_call_to_action = get_field('testo_bottone_call_to_action');

    // $slogan_titolo_1 = get_field('slogan_titolo_1');
    // $slogan_titolo_2 = get_field('slogan_titolo_2');
    // $slogan_paragrafo = get_field('slogan_paragrafo');

    $titolo_successi = get_field('titolo_successi');
    $sottotitolo_successi = get_field('sottotitolo_successi');
    $paragrafo_successi_1 = get_field('paragrafo_successi_1');
    $titolo_tra_paragrafi_successi = get_field('titolo_tra_paragrafi_successi');
    $paragrafo_successi_2 = get_field('paragrafo_successi_2');

    $titolo_servizi = get_field('titolo_servizi');

    // titoli con città (composti qui)
    $citta = get_field('citta');
    $titolo_servizio_1 = get_field('titolo_servizio_1');
    $titolo_servizio_2 = get_field('titolo_servizio_2');
    $titolo_servizio_3 = get_field('titolo_servizio_3');
	$titolo_servizio_4 = get_field('titolo_servizio_4');
	$testo_servizio_4 = get_field('testo_servizio_4');


    $testo_servizio_1 = get_field('testo_servizio_1');
    $testo_servizio_2 = get_field('testo_servizio_2');
    $testo_servizio_3 = get_field('testo_servizio_3');

    $paragrafi_titolo_1 = get_field('paragrafi_titolo_1');
    $paragrafi_testo_1 = get_field('paragrafi_testo_1');
    $paragrafi_titolo_2 = get_field('paragrafi_titolo_2');
    $paragrafi_testo_2 = get_field('paragrafi_testo_2');
    // $paragrafi_titolo_3 = get_field('paragrafi_titolo_3');
    // $paragrafi_testo_3 = get_field('paragrafi_testo_3');

    $titolo_casi_studio = get_field('titolo_casi_studio');
    $titolo_caso_studio_1 = get_field('titolo_caso_studio_1');
    $testo_caso_studio_1 = get_field('testo_caso_studio_1');
    $titolo_caso_studio_2 = get_field('titolo_caso_studio_2');
    $testo_caso_studio_2 = get_field('testo_caso_studio_2');

    $titolo_risultati = get_field('titolo_risultati');
    $sottotitolo_risultati = get_field('sottotitolo_risultati');
    $testo_risultato_1 = get_field('testo_risultato_1');
    $testo_risultato_2 = get_field('testo_risultato_2');
    $testo_risultato_3 = get_field('testo_risultato_3');
    $testo_risultato_4 = get_field('testo_risultato_4');

    $titolo_dicono_di_noi = get_field('titolo_dicono_di_noi');

    // $titolo_recensioni = get_field('titolo_recensioni');
    // $sottotitolo_recensioni = get_field('sottotitolo_recensioni');
    // $testo_recensioni = get_field('testo_recensioni');

    $titolo_faq = get_field('titolo_faq');

    // Percorsi assoluti per immagini (dinamiche e fisse)
    $uploads = wp_upload_dir();
    $base_img_url       = trailingslashit($uploads['baseurl']) . 'seo-img/';         // 3 immagini generate/import
    $theme_img_url      = get_stylesheet_directory_uri() . '/img/seo/';             // immagini fisse
    $theme_seo_img_url  = trailingslashit($theme_img_url . 'seo');                  // fisse che stavano in img/seo/

 
// Titolo della sezione FAQ (campo singolo)
$titolo_faq = get_field('titolo_faq');

// Inizializza le variabili titolo_faq_1..7 e testo_faq_1..7
for ($i = 1; $i <= 7; $i++) {
    ${"titolo_faq_".$i} = '';
    ${"testo_faq_".$i}  = '';
}

// Legge il repeater corretto (nome = fld_faq)
$faq_rows = get_field('fld_faq'); // array di righe: ['fld_faq_tit', 'fld_faq_txt']

// Popola le variabili usate nel markup
if (is_array($faq_rows) && !empty($faq_rows)) {
    $i = 1;
    foreach ($faq_rows as $row) {
        if ($i > 7) break;

        ${"titolo_faq_".$i} = $row['fld_faq_tit'] ?? '';
        ${"testo_faq_".$i}  = $row['fld_faq_txt'] ?? '';

        $i++;
    }
}


if (is_array($faq_rows)) {
    echo '<!-- FAQ righe: ' . count($faq_rows) . ' -->';
}






    $titolo_form = get_field('titolo_form');
    $sottotitolo_form = get_field('sottotitolo_form');
    $indicazioni_form = get_field('indicazioni_form');
    $proseguimento_indicazioni_form = get_field('proseguimento_indicazioni_form');

    // Telefoni (extra)
    $numero_whatsapp = '+393488587213';
    $numero_form = '+39 352 0996 996';
    $numero_form_per_link = preg_replace('/\s+/', '', $numero_form);

    // ===================== IMMAGINI (LOCALE -> DEFAULT -> FALLBACK) =====================

    // Argomento SEO collegato (per i default globali)
    $arg = get_field('argomento_collegato', get_the_ID());
    $arg_id = 0;
    if (is_object($arg) && isset($arg->ID)) {
        $arg_id = $arg->ID;
    } elseif (is_array($arg) && isset($arg['ID'])) {
        $arg_id = (int) $arg['ID'];
    } elseif (is_numeric($arg)) {
        $arg_id = (int) $arg;
    }

    // Compatibilità con i nomi (stringhe) usati nel template originale
    $slug = get_field('slug');
    $slug_composto_lowercase = 'seo-'.strtolower($citta);

    // nomi file per immagini generate (fallback statico)
    $nuovo_nome_secondary_img = 'consulente-'.$slug_composto_lowercase.'.jpg';
    $nuovo_nome_last_img = 'consulente-seo-'.strtolower($citta).'-igor-biagi-chef-studio.jpg';

    // 1) Prova a usare le immagini specifiche della pagina
    $immagine_main = get_field('immagine_main', get_the_ID());
    $immagine_secondary = get_field('immagine_secondary', get_the_ID());
    $immagine_last = get_field('immagine_last', get_the_ID());

    // 2) Se vuote, usa le default dell'argomento SEO
    if ( ! $immagine_main && $arg_id ) {
        $immagine_main = get_field('immagine_main_default', $arg_id);
    }
    if ( ! $immagine_secondary && $arg_id ) {
        $immagine_secondary = get_field('immagine_secondary_default', $arg_id);
    }
    if ( ! $immagine_last && $arg_id ) {
        $immagine_last = get_field('immagine_last_default', $arg_id);
    }

    // 3) Fallback iniziale: file generati statici
    $url_main      = $base_img_url . $slug_composto_lowercase . '.jpg';
    $url_secondary = $base_img_url . $nuovo_nome_secondary_img;
    $url_last      = $base_img_url . $nuovo_nome_last_img;

    // 4) Se esistono immagini ACF (locali o default), sovrascrivono il fallback
    if ($immagine_main) {
        if (is_array($immagine_main) && isset($immagine_main['url'])) {
            $url_main = $immagine_main['url'];
        } elseif (is_numeric($immagine_main)) {
            $url_main = wp_get_attachment_url((int)$immagine_main);
        }
    }

    if ($immagine_secondary) {
        if (is_array($immagine_secondary) && isset($immagine_secondary['url'])) {
            $url_secondary = $immagine_secondary['url'];
        } elseif (is_numeric($immagine_secondary)) {
            $url_secondary = wp_get_attachment_url((int)$immagine_secondary);
        }
    }

    if ($immagine_last) {
        if (is_array($immagine_last) && isset($immagine_last['url'])) {
            $url_last = $immagine_last['url'];
        } elseif (is_numeric($immagine_last)) {
            $url_last = wp_get_attachment_url((int)$immagine_last);
        }
    }
}
?>





<div class="contenitore back-pattern-chef-seo chef-header">
  <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">
    
    <div class="struttura-container struttura-2-standard centro-verticale hero-header-enhanced">
      
      <div class="div-1">
        <h1><?php echo $main_keyword; ?></h1>
        <h2><?php echo $h2_hero; ?></h2>

        <p><?php echo $paragrafo_hero; ?></p>

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
            <span>Parla con il nostro esperto SEO</span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>

          <a href="<?php echo esc_url(get_permalink(3013)); ?>" class="btn hero-action-link hero-action-link-secondary">
            <span>Guarda tutti i servizi SEO</span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        </div>

        <div class="local-container">

          <div class="img-icon">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/igor-biagi-cta.png" alt="Igor Biagi">
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
        <img
          class="flex img-no-shift-landing-seo-citta"
          src="<?php echo esc_url($url_main); ?>"
          alt="SEO <?php echo esc_attr($citta); ?>"
        >
      </div>
    </div>
	  
  </div>
</div>

































































<div class="contenitore">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">		


<div class="struttura-container struttura-2-standard">
	
	<div class="div-1"> 
<h2 class="txt-center"><?php echo $titolo_successi; ?></h2>
		<h3 class="txt_center"><?php echo $sottotitolo_successi; ?></h3>
		<p><?php echo $paragrafo_successi_1; ?></p>

		<h3 class="txt_center"><?php echo $titolo_tra_paragrafi_successi; ?></h3>
		<p><?php echo $paragrafo_successi_2; ?></p>
		
			
	</div>			 
	
	<div class="div-2">
		
		<img
    src="<?php echo esc_url($url_secondary); ?>"
    alt="Consulente <?php echo esc_attr($main_keyword); ?>"
    title="Consulente <?php echo esc_attr($main_keyword); ?>"
>
	</div>
</div><!-- struttura 2 -->
</div>
	</div>







<?php
$telefono      = get_field('telefono', 'option');
$telefono_link = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';

$cta_url       = '#form-ancora';
$testo_bottone = 'Parla con noi!';
$testo_cta     = '<h2>Vuoi incrementare le vendite del tuo sito?</h2><p>Parla con Chef Studio e scopri come possiamo aiutarti con SEO, sito web e strategia digitale su misura per far crescere il tuo business online.</p>';
$orari_cta     = '';

$icona_cta_url = home_url('/wp-content/uploads/2025/11/icona-realizzazione-landing-page.svg');
$icona_cta_alt = 'Realizzazione landing page';

$immagine_cta_url = get_stylesheet_directory_uri() . '/img/igor-biagi-cta.png';
$immagine_cta_alt = 'Igor Biagi - consulente SEO Chef Studio';
?>

<div class="contenitore">
  <div class="contenuto-standard pad-top-small pad-bottom-small pad-x-standard">

	    <section class="call-to-action call-to-action-desktop cta-premium local-page-template-cta">

      <div class="cta-premium-inner">

        <figure class="cta-premium-media">
          <img
            src="<?php echo esc_url($immagine_cta_url); ?>"
            alt="<?php echo esc_attr($immagine_cta_alt); ?>"
          >
        </figure>

        <div class="cta-premium-copy">
          <figure class="cta-premium-icon">
            <img
              src="<?php echo esc_url($icona_cta_url); ?>"
              alt="<?php echo esc_attr($icona_cta_alt); ?>"
            >
          </figure>

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








<div class="contenitore chef-3-blocchi-local">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">	
		<div class="titoletto">
			<h2><?php echo $titolo_servizi; ?></h2>
		</div>
		
		<div class="anteprime-4">

	<ul class="servizi-apertura">

		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'consulenza-seo.svg'); ?>" alt="consulenza seo" title="Consulenza SEO">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     <?php echo $titolo_servizio_1; ?>
    </div>
    <div class="testo">
      <?php echo $testo_servizio_1; ?>
    </div>
  </div>
</li>

		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'ottimizzazione-siti-web.svg'); ?>" alt="ottimizzazione siti web" title="Ottimizzazione siti web">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     <?php echo $titolo_servizio_2; ?>
    </div>
    <div class="testo">
      <?php echo $testo_servizio_2; ?>
    </div>
  </div>
</li>		
		
		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'siti-web-seo-friendly.svg'); ?>" alt="gestione seo completa" title="Gestione SEO completa">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     <?php echo $titolo_servizio_3; ?>
    </div>
    <div class="testo">
      <?php echo $testo_servizio_3; ?>
    </div>
  </div>
</li>			
		
		
		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'realizzazzione-siti-web-seo-friendly.svg'); ?>" alt="siti web seo friendly" title="Realizzazione siti web SEO friendly">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     <?php echo $titolo_servizio_4; ?>
    </div>
    <div class="testo">
      <?php echo $testo_servizio_4; ?>
    </div>
  </div>
</li>			
		
		
	</ul>	 

		</div>
		
		
	</div></div>




	
	<div class="contenitore back-bonus-color">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">	
	
	<div class="struttura-container struttura-2-standard tre-paragrafi">
	
	<div class="div-1">
		<h2><?php echo $paragrafi_titolo_1; ?></h2>
		<?php echo $paragrafi_testo_1; ?>
	</div>
	
	<div class="div-2">
		<h2><?php echo $paragrafi_titolo_2; ?></h2>
		<?php echo $paragrafi_testo_2; ?>
	</div>
</div>
		
			</div>
</div>


<div class="contenitore">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">	
		<div class="titoletto">
			<h2 class="txt_center"><?php echo $titolo_casi_studio; ?></h2>
		</div>
		

<div class="struttura-container struttura-2-standard">
	
	<div class="div-1">
		<h3><?php echo $titolo_caso_studio_1; ?></h3>
		<!-- immagine fissa (prima era img/seo/...) -->
		<img class="flex" src="<?php echo esc_url($theme_img_url . 'caso-studio-risultati-seo-sito-aziendale-lusso.jpg'); ?>" title="risultati seo" alt="risultati seo"/>
		<?php echo $testo_caso_studio_1; ?>
	</div>			 
   	 
    <div class="div-2">
		<h3><?php echo $titolo_caso_studio_2; ?></h3>
		<!-- immagine fissa -->
		<img class="flex" src="<?php echo esc_url($theme_img_url . 'consulenza-seo.jpg'); ?>" title="risultati consulenza seo" alt="risultati consulenza seo"/>
		<?php echo $testo_caso_studio_2; ?>
	</div>
</div><!-- struttura 2 -->


	</div>	 <!--/contenuto--> 	
		</div>
	
	
	
	
	<div class="contenitore back-bonus-color">
	<div class="contenuto-large pad-top-large pad-bottom-large pad-x-standard">	

		<div class="titoletto">
		<h2><?php echo $titolo_risultati; ?></h2>
		<h3><?php echo $sottotitolo_risultati; ?></h3>
		</div>
		
		
		<div class="struttura-container struttura-2-standard">
			<div class="div-1">
				<div class="struttura-container struttura-2-standard">
					<div class="div-1">
						<!-- immagine fissa -->
						<img  src="<?php echo esc_url($theme_img_url . 'seo-per-case-vacanza.jpg'); ?>" alt="grafica" title="Recensioni SEO Chef Studio" class="flex">		    
					</div>
					<div class="div-2">
						<?php echo $testo_risultato_1; ?>
					</div>
				</div>	
			</div><!-- testo -->
			<div class="div-2">
				<div class="struttura-container struttura-2-standard">
					<div class="div-1">
						<!-- immagine fissa -->
						<img src="<?php echo esc_url($theme_img_url . 'seo-per-ecommerce.jpg'); ?>" alt="grafica" title="Recensioni Chef Studio SEO e marketing" class="flex">
					</div>
					<div class="div-2">
						<?php echo $testo_risultato_2; ?>	
					</div>
				</div>	
			</div>
		</div><!-- struttura 2 -->
		
		<div class="struttura-container struttura-2-standard">
			<div class="div-1">
				<div class="struttura-container struttura-2-standard">
					<div class="div-1">
						<!-- immagine fissa -->
						<img  src="<?php echo esc_url($theme_img_url . 'seo-per-aziende-di-lusso.jpg'); ?>" alt="grafica" title="Realizzazione Logo e Immagine Coordinata" class="flex">		    
					</div>
					<div class="div-2">
						<?php echo $testo_risultato_3; ?>	
					</div>
				</div>	
			</div><!-- testo -->
			<div class="div-2">
				<div class="struttura-container struttura-2-standard">
					<div class="div-1">
						<!-- immagine fissa -->
						<img src="<?php echo esc_url($theme_img_url . 'seo-internazionale-in-inglese-google-usa.jpg'); ?>" alt="Recensioni Chef Studio SEO" class="flex">
					</div>
					<div class="div-2">
						<?php echo $testo_risultato_4; ?>	
					</div>
				</div>	
			</div>
		</div><!-- struttura 2 -->	
				


		</div></div>
	



<div class="contenitore chef-3-recensioni-local">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard contenuto-scroll">	
		<div class="titoletto">
			<h2><?php echo $titolo_dicono_di_noi; ?></h2>
		</div>
		
		<div class="anteprime-4">

	<ul class="servizi-apertura">

		
<li>
  <div class="contenitore-img">
   <img class="img3" src="<?php echo esc_url($theme_img_url . 'elia_devoti.png'); ?>" alt="Elia, cliente soddisfatto di Chef Studio" title="cliente soddisfatto di chef studio">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     Elia Devoti
    </div>
    <div class="testo">
      Ho affidato il mio sito ai ragazzi di Chef Studio per aumentare la mia presenza su Google. Non sapevo come funzionasse la SEO, ma quando hanno iniziato ad arrivare le richieste di preventivo ho capito che era la strada giusta. Gestiscono tutto in autonomia e i risultati sono stati immediatamente concreti.
    </div>
  </div>
</li>

		
<li>
  <div class="contenitore-img">
   <img class="img3" src="<?php echo esc_url($theme_img_url . 'matteo.png'); ?>" alt="Cliente soddisfatto di Chef Studio" title="cliente soddisfatto del corso di grafica pubblicitari online">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     Matteo Grava
    </div>
    <div class="testo">
      Da quando collaboro con i ragazzi di Chef Studio, il mio e-commerce ha fatto un salto enorme. Sono professionali e meticolosi, e i risultati si vedono chiaramente. Lavoriamo insieme da tre anni e seguono anche i nuovi portali che ho aperto da zero.
    </div>
  </div>
</li>		
		
		
<li>
  <div class="contenitore-img">
   <img class="img3" src="<?php echo esc_url($theme_img_url . 'elisa.png'); ?>" alt="cliente soddisfatto di Chef Studio" title="cliente soddisfatto di Chef Studio">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     Elisa Dominici
    </div>
    <div class="testo">
      Chef Studio ha realizzato per me un ottimo sito per gli appartamenti della mia casa vacanze. Il primo anno abbiamo affittato tutto grazie al sito, senza usare portali come Booking. Siamo alla terza stagione e già da aprile avevamo prenotazioni per l’intero anno.
    </div>
  </div>
</li>		
		

<li>
  <div class="contenitore-img">
   <img class="img3" src="<?php echo esc_url($theme_img_url . 'lorenzo.png'); ?>" alt="cliente soddisfatto di Chef Studio" title="cliente soddisfatto di Chef Studio">
  </div>
  <div class="testo-anteprima">
    <div class="titolo">
     Lorenzo Gianfrancfeschi
    </div>
    <div class="testo">
      Mi sono trovato particolarmente bene con i ragazzi di Chef Studio. Massima professionalità, chiarezza e competenza in ogni fase del lavoro. Un’esperienza positiva che consiglio senza esitazioni.
    </div>
  </div>
</li>
		
		
		
		
		
	</ul>	 

		</div>
		
		
	</div></div>
		
		
		
		
<?php
// Forza il recupero sul post corrente anche se il loop non è attivo
$post_id = get_queried_object_id();

// Titolo della sezione FAQ (campo singolo)
$nome_sezione_faq    = get_field('nome_sezione_faq', $post_id);
$titolo_faq          = get_field('titolo_faq', $post_id);
$titolo_faq_testo    = trim(wp_strip_all_tags((string) $titolo_faq));
$sottotitolo_faq     = get_field('sottotitolo_faq', $post_id);
$testo_faq           = !empty($sottotitolo_faq) ? $sottotitolo_faq : get_field('testo_faq', $post_id);
$testo_link_faq      = get_field('testo_bottone_faq', $post_id);
$link_faq            = get_field('link_bottone_faq', $post_id);
$faq_link_url        = '';
$faq_link_target     = '';

if (empty($testo_link_faq)) {
  $testo_link_faq = get_field('testo_link_faq', $post_id);
}

if (empty($link_faq)) {
  $link_faq = get_field('link_faq', $post_id);
}

if (is_array($link_faq)) {
  $faq_link_url = $link_faq['url'] ?? '';

  if (empty($testo_link_faq) && !empty($link_faq['title'])) {
    $testo_link_faq = $link_faq['title'];
  }

  $faq_link_target = $link_faq['target'] ?? '';
} elseif (is_string($link_faq)) {
  $faq_link_url = $link_faq;
}

// Recupera il repeater completo
$faq_rows = get_field('fld_faq', $post_id);

if (!empty($faq_rows) && function_exists('chefstudio_schema_collect_faq_items')) {
  chefstudio_schema_collect_faq_items($faq_rows, 'tutto-integrato-faq');
}
?>

<?php if (!empty($faq_rows)) : ?>
<div class="contenitore">
  <div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
    <section class="faq-content faq-premium faq-standalone-layout">
      <div class="faq-intro">
        <?php if (!empty($nome_sezione_faq)) : ?>
          <div class="nome-sezione">
            <?php echo esc_html($nome_sezione_faq); ?>
          </div>
        <?php endif; ?>

        <?php if ($titolo_faq_testo !== '') : ?>
          <h2><?php echo esc_html($titolo_faq_testo); ?></h2>
        <?php endif; ?>

        <?php if (!empty($testo_faq)) : ?>
          <div class="faq-intro-text">
            <?php echo wp_kses_post($testo_faq); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($faq_link_url) && !empty($testo_link_faq)) : ?>
          <a
            class="btn btn-super faq-intro-button"
            href="<?php echo esc_url($faq_link_url); ?>"
            <?php if (!empty($faq_link_target)) : ?>target="<?php echo esc_attr($faq_link_target); ?>"<?php endif; ?>
            <?php if ($faq_link_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
          >
            <span><?php echo esc_html($testo_link_faq); ?></span>
            <span class="hero-btn-arrow" aria-hidden="true"></span>
          </a>
        <?php endif; ?>
      </div>

      <div class="faq-list">
      <?php foreach ($faq_rows as $index => $row) :
        $titolo = $row['fld_faq_tit'] ?? '';
        $testo  = $row['fld_faq_txt'] ?? '';
        $titolo_txt = trim(wp_strip_all_tags((string)$titolo));
        $testo_txt  = trim(wp_strip_all_tags((string)$testo));
        if ($titolo_txt === '' && $testo_txt === '') continue;
        $id = 'faq-title-' . $post_id . '-' . ($index + 1);
      ?>
        <div class="faq-accordion">
          <input type="checkbox" class="tgg-title" id="<?php echo esc_attr($id); ?>">

          <div class="faq-accordion-title">
            <label for="<?php echo esc_attr($id); ?>">
              <span class="faq-toggle-icon" aria-hidden="true"></span>
              <?php if ($titolo_txt !== '') : ?>
                <span class="faq-question-text"><?php echo wp_kses_post($titolo); ?></span>
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
<?php endif; ?>


<!--
<div class="contenitore chef-3-recensioni-2-local">
	<div class="contenuto-large pad-top-large pad-bottom-large pad-x-standard contenuto-scroll">	
		<div class="titoletto">
			<h2 class=""><?php // echo $titolo_recensioni; ?></h2>
		<h3 class=""><?php // echo $sottotitolo_recensioni; ?></h3>
		</div>
		
		<div class="anteprime-4">

	<ul class="servizi-apertura">

		
<li>
  <div class="contenitore-img">
   <img  src="<?php echo esc_url($theme_img_url . 'recensione-chef-studio-seo-riccardo.jpg'); ?>" alt="grafica" title="Recensioni SEO Chef Studio">		
  </div>
  <div class="testo-anteprima">
	  <img src="<?php echo esc_url($theme_img_url . 'stelline.jpg'); ?>" width="100" alt="punteggio recensioni" title="recensione a cinque stelle">	
    <div class="titolo">
     Riccardo Troiani, fotografo.
    </div>
    <div class="testo">
      Da Google My Business
    </div>
  </div>
</li>

		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'recensione-chef-studio-seo-lorenzo.jpg'); ?>" alt="grafica" title="Recensioni Chef Studio SEO e marketing">
  </div>
  <div class="testo-anteprima">
	  <img src="<?php echo esc_url($theme_img_url . 'stelline.jpg'); ?>" width="100" alt="punteggio recensioni" title="recensione a cinque stelle">	
    <div class="titolo">
     Lorenzo Gianfranceschi
    </div>
    <div class="testo">
      Da Google My Business
    </div>
  </div>
</li>		
		
		
<li>
  <div class="contenitore-img">
   <img  src="<?php echo esc_url($theme_img_url . 'recensione-chef-studio-seo.jpg'); ?>" alt="grafica" title="Realizzazione Logo e Immagine Coordinata" class="flex">		
  </div>
  <div class="testo-anteprima">
	  <img src="<?php echo esc_url($theme_img_url . 'stelline.jpg'); ?>" width="100" alt="punteggio recensioni" title="recensione a cinque stelle">	
    <div class="titolo">
     Artax Lab, Servizi digitali a supporto degli artisti
    </div>
    <div class="testo">
      Da Google My Business
    </div>
  </div>
</li>	
		
		
<li>
  <div class="contenitore-img">
   <img src="<?php echo esc_url($theme_img_url . 'recensione-chef-studio-da-whatsapp.jpg'); ?>" alt="Recensioni Chef Studio SEO" class="flex">
  </div>
  <div class="testo-anteprima">
	  <img src="<?php echo esc_url($theme_img_url . 'stelline.jpg'); ?>" width="100" alt="punteggio recensioni" title="recensione a cinque stelle">	
    <div class="titolo">
     Un nostro cliente.
    </div>
    <div class="testo">
      Da chat privata di Whatsapp.
    </div>
  </div>
</li>			
		
		
		
	</ul>	 

		</div>
		
		
	</div></div>

-->

































	<?php
	$svg_base_url = get_stylesheet_directory_uri() . '/img/svg/';
	?>

	<div class="contenitore local-seo-contact-section">
	  <div class="contenuto-standard pad-x-standard local-seo-contact-wrap">
	    <span id="form-ancora" class="local-seo-anchor" aria-hidden="true"></span>
	    <span id="form-anchor" class="local-seo-anchor" aria-hidden="true"></span>

	    <section class="local-seo-contact" aria-labelledby="local-seo-contact-title">
	      <div class="local-seo-contact-copy">
		        <h2 id="local-seo-contact-title">
		          Consulente SEO <?php echo esc_html($citta); ?>
		        </h2>
		        <?php
		        /*
		         * Rimosso dal layout CTA form del 2026-06-06:
		         * <div class="local-seo-contact-intro">
		         *   Raccontaci il tuo sito, la tua attività o gli obiettivi che vuoi raggiungere. Ti indicheremo le opportunità più concrete per aumentare visibilità e contatti.
		         * </div>
		         */
		        ?>

	        <div class="local-seo-consultant-card">
	          <figure class="local-seo-consultant-photo">
	            <img
	              src="<?php echo esc_url($url_last); ?>"
	              alt="Consulente SEO a <?php echo esc_attr($citta); ?> - Igor Biagi - Chef Studio"
	            >
	          </figure>

	          <div class="local-seo-consultant-info">
	            <h3>Igor Biagi</h3>
	            <p>Consulente SEO<br>e communication designer.</p>

	            <ul class="local-seo-benefits">
	              <li>
		                <img src="<?php echo esc_url($svg_base_url . 'icona-referente-unico.svg'); ?>" alt="Referente unico" aria-hidden="true">
	                <span>Parli direttamente<br>con chi segue il progetto</span>
	              </li>
	              <li>
		                <img src="<?php echo esc_url($svg_base_url . 'icona-data-driven.svg'); ?>" alt="Strategie basate sui dati" aria-hidden="true">
	                <span>Strategie basate sui dati</span>
	              </li>
	              <li>
		                <img src="<?php echo esc_url($svg_base_url . 'icona-strategia.svg'); ?>" alt="Esperienza SEO" aria-hidden="true">
	                <span>Oltre 15 anni di esperienza</span>
	              </li>
	            </ul>
	          </div>
	        </div>

		        <?php
		        /*
		         * Rimosso dal layout CTA form del 2026-06-06:
		         * <div class="local-seo-process">
		         *   <h3>Come funziona?</h3>
		         *   <p>Analizziamo il contesto competitivo, individuiamo le priorità e ti forniamo un primo orientamento strategico.</p>
		         * </div>
		         */
		        ?>

		        <div class="local-seo-trust-note">
		          <img src="<?php echo esc_url($svg_base_url . 'icona-sicuro.svg'); ?>" alt="Valutazione personalizzata" aria-hidden="true">
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
	
	
	
	
	
	
	
	<div class="contenitore">
	<div class="contenuto-standard pad-top-large pad-bottom-large pad-x-standard">
		<div class="titoletto">
		<h3>Creiamo strategie SEO in tutta Italia per posizionare i siti web su Google in Italia e all'estero</h3>
			<a href="<?php echo esc_url( get_permalink(3015) ); ?>"><h4 class="txt_center">Consulente SEO</h4></a>
		
		</div>
	</div>	 <!--/contenuto--> 
		</div> <!--/fine contenitore-->


<?php get_footer(); ?>
