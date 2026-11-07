<?php
/*
 Footer
*/
?>

<div class="footer-large">
	<div class="footer-container">
		<div class="footer-flex">
		<div class="footer-informazioni">
			<div class="margin-bot-15px"> 
				<?php 
$logo = get_field('logo', 'option'); 
if ($logo): 
?>
  <div class="margin-bot-15px">
    <img width="200" src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
  </div>
<?php endif; ?>

			</div>
			<div class="margin-bot-15px"><?php echo (get_field('descrizione_azienda', 'option')) ?></div>
			<div class="small-txt">
			<a target="_blank" href="<?php echo esc_url( get_permalink(2393) ); ?>"><?php _e( 'Informativa Privacy', 'chef-studio') ?></a> | <a target="_blank" href="<?php echo esc_url( get_permalink(5285) ); ?>"><?php _e( 'Informativa Cookie', 'chef-studio') ?></a> <!-- |  <a target="_blank" href="<?php echo esc_url( get_permalink(0000) ); ?>"><?php _e( 'Termini e condizioni', 'chef-studio') ?></a> --> | <a class="cmplz-show-banner"><?php _e( 'Gestisci i cookie', 'chef-studio') ?> </a>
				
			
				
				
			</div>
		</div>	
		
		<div class="footer-menu">
			<?php wp_nav_menu( array( 'container_class' => 'menu-footer', 'theme_location' => 'footer-menu' ) ); ?> <!-- Footer Menu -->
		</div>
		
		<div class="footer-contatti">	
			<?php echo (get_field('contatti_azienda', 'option')) ?>
		</div>

			</div>	<!-- flex -->	
		
		<div class="floatstop"></div>		
		<div class="avvertenze-legali">
			<?php echo (get_field('info_legali', 'option')) ?>
		</div>			
		
		
		<div class="credits">
			
			<div class="social-cont txt-centro">        
  <?php echo (get_field('titolo_profili_social', 'option')) ?>
  
  <?php if (have_rows('profili_social', 'option')): ?>
    <ul>
      <?php while (have_rows('profili_social', 'option')): the_row(); 
        $link = get_sub_field('link_social');
        $icona = get_sub_field('icona_social'); // campo immagine
        $nome = get_sub_field('nome_social');
      ?>
        <?php if ($link && $icona): ?>
          <li>
            <a target="_blank" href="<?php echo esc_url($link); ?>">
              <img 
                src="<?php echo esc_url($icona['url']); ?>" 
                class="social-icon" 
                alt="<?php echo esc_attr($nome); ?>"><br/>
            </a>
          </li>
        <?php endif; ?>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
</div>

			
			
			<br/>	
			Designed by<br/>
			<div class="logo-chef-footer">
				<?php
// Recupera la pagina "Credits" tramite WP_Query
$credits_page = new WP_Query(array(
    'post_type'      => 'page',
    'title'          => 'Credits',
    'posts_per_page' => 1,
));

// Verifica se esiste
if ($credits_page->have_posts()) {
    $credits_page->the_post();
    $credits_link = get_permalink();
    wp_reset_postdata();
}
?>

<a href="<?php echo esc_url( $credits_link ?? '#' ); ?>">
    <img src="<?php echo esc_url($logo['url']); ?>" 
         alt="<?php echo esc_attr($logo['alt']); ?>" 
         width="60">
</a>

			</div>		
		</div>		
	
		
		
		
	</div> <!--/fine footer container -->
</div> <!--/fine footer-->
</div> <!--/fine super-wrapper-->
<?php wp_footer(); ?>

<?php if( is_page( 'Contatti' ) || is_page( 'Contacts' ) )  { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/css/gravity-forms-andrea-nuove-regole.css" />
<?php } ?>

<script>	
	window.addEventListener('resize', function() {
  if (window.matchMedia("(min-width: 1250px)").matches) {
    // Script for menu visibility on scroll
    var nav = document.getElementById("menu-visibility"); // Get reference to the nav element
    var prevScrollpos = window.pageYOffset;

    if (!nav) {
      return;
    }

    window.onscroll = function() {
      var currentScrollPos = window.pageYOffset;

      // Controllo aggiuntivo per hover/focus sul menu e sulle tendine desktop
      var isMenuActive = nav.matches(':hover') || nav.querySelector('.chef-desktop-submenu-open, .menu-header > ul > li:hover, .menu-header > ul > li:focus-within');

      if (prevScrollpos > currentScrollPos || isMenuActive) {
        nav.style.top = "0";
        nav.classList.add("scrolled"); // Add the "scrolled" class for yellow background
      } else {
        nav.style.top = "-80px";
        nav.classList.remove("scrolled"); // Remove the class when scrolled back up
      }
      prevScrollpos = currentScrollPos;
    };
	  } else {
	    // Disable script functionality for smaller screens (optional)
	    var mobileNav = document.getElementById("menu-visibility");
	    if (mobileNav) {
	      mobileNav.style.top = "";
	      mobileNav.classList.remove("scrolled");
	    }
	    window.onscroll = null; // Clear any existing scroll event listener
	  }
	});

// Run check on initial load in case the screen size is already above 980px
window.dispatchEvent(new Event('resize'));
</script>
















</body>
</html>
