<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
	
<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri() ?>/img/favicon.ico" />
	
	<link rel="preload" href="<?php echo get_template_directory_uri() ?>/fonts/bricolage-grotesque-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>

	
<?php if( is_page( 'Privacy Policy IT' ) || is_page( 'Cookie Policy IT' ) || is_page( 'Privacy Policy EN' ) || is_page( 'Cookie Policy EN' ) || is_page( 'Privacy Policy Form IT' ) || is_page( 'Privacy Policy Form EN' ) ) { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/css/privacy.css" />
<?php } ?>
	


<?php wp_head() ?>

</head>
<body <?php body_class('woocommerce id-pagina-' . get_the_ID()); ?>>
<input type="checkbox" id="hamburger"/>
<label class="menuicon" for="hamburger">
  <span></span>
</label>
	
	
<div id="bar" class="apparire-menu"> 
	<a href="<?php echo site_url(); ?>"> 
 		<div id="logo">
			<?php 
$logo = get_field('logo', 'option'); 
if ($logo): 
?>
  <div class="margin-bot-15px">
    <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
  </div>
<?php endif; ?>
		
		</div> <!--/fine logo-->   
	</a>
</div><!--/fine bar--> 


<nav id="menu-visibility">

	<div class="chef-mobile-menu-header">
		<button class="chef-mobile-menu-heading-back" type="button" aria-label="Torna al livello precedente"></button>
		<span class="chef-mobile-menu-heading-label">Menu</span>
	</div>
	
	<a href="<?php echo home_url(); ?>">
		<div class="logo-desktop visible-only-large">
			<?php 
$logo = get_field('logo', 'option'); 
if ($logo): 
?>
    <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
<?php endif; ?>
		</div>
	</a>
	
	
<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'menu-chef' ) ); ?>

	<?php
	$mobile_menu_cta_field = function_exists('get_field') ? get_field('menu_mobile_mostra_cta', 'option') : null;
	$mobile_menu_show_cta = ($mobile_menu_cta_field === null || $mobile_menu_cta_field === '') ? true : (bool) $mobile_menu_cta_field;

	$mobile_menu_title = function_exists('get_field') ? get_field('menu_mobile_titolo_cta', 'option') : '';
	$mobile_menu_title = $mobile_menu_title ? $mobile_menu_title : 'Serve aiuto?';

	$mobile_menu_text = function_exists('get_field') ? get_field('menu_mobile_testo_cta', 'option') : '';
	$mobile_menu_text = $mobile_menu_text ? $mobile_menu_text : 'Scrivici su WhatsApp o chiamaci: ti rispondiamo direttamente.';

	$mobile_menu_hours = function_exists('get_field') ? get_field('menu_mobile_orari', 'option') : '';
	$mobile_menu_whatsapp_label = function_exists('get_field') ? get_field('menu_mobile_label_whatsapp', 'option') : '';
	$mobile_menu_whatsapp_label = $mobile_menu_whatsapp_label ? $mobile_menu_whatsapp_label : 'WhatsApp';

	$mobile_menu_phone = function_exists('get_field') ? get_field('telefono', 'option') : '';
	$mobile_menu_phone_link = $mobile_menu_phone ? preg_replace('/[^0-9+]/', '', $mobile_menu_phone) : '';

	$mobile_menu_whatsapp_number = function_exists('get_field') ? get_field('menu_mobile_numero_whatsapp', 'option') : '';
	$mobile_menu_whatsapp_number = $mobile_menu_whatsapp_number ? $mobile_menu_whatsapp_number : $mobile_menu_phone;
	$mobile_menu_whatsapp_link = $mobile_menu_whatsapp_number ? preg_replace('/\D/', '', $mobile_menu_whatsapp_number) : '';
	?>

	<?php if ($mobile_menu_show_cta && ($mobile_menu_title || $mobile_menu_text || $mobile_menu_phone_link || $mobile_menu_whatsapp_link)): ?>
		<div class="chef-mobile-menu-contact">
			<?php if ($mobile_menu_whatsapp_link): ?>
				<div class="wrap-wa chef-mobile-menu-whatsapp">
					<a target="_blank" href="https://wa.me/<?php echo esc_attr($mobile_menu_whatsapp_link); ?>" rel="noopener noreferrer">
						<div class="chat-wa blob blob-wa">
							<img width="40" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/svg/whatsapp.svg'); ?>" alt="WhatsApp">
						</div>
					</a>
				</div>
			<?php endif; ?>

			<?php if ($mobile_menu_title): ?>
				<p class="chef-mobile-menu-contact-title"><?php echo esc_html($mobile_menu_title); ?></p>
			<?php endif; ?>

			<?php if ($mobile_menu_text): ?>
				<div class="chef-mobile-menu-contact-text">
					<?php echo wp_kses_post(wpautop($mobile_menu_text)); ?>
				</div>
			<?php endif; ?>

			<div class="chef-mobile-menu-contact-actions">
				<?php if ($mobile_menu_phone_link): ?>
					<a class="chef-mobile-menu-phone" href="tel:<?php echo esc_attr($mobile_menu_phone_link); ?>">
						<?php echo esc_html($mobile_menu_phone); ?>
					</a>
				<?php endif; ?>

				<?php if ($mobile_menu_whatsapp_link): ?>
					<a class="chef-mobile-menu-whatsapp-text" href="https://wa.me/<?php echo esc_attr($mobile_menu_whatsapp_link); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html($mobile_menu_whatsapp_label); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ($mobile_menu_hours): ?>
				<p class="chef-mobile-menu-hours"><?php echo esc_html($mobile_menu_hours); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
		<!--
	<div class="cerca-menu">
    	<?php get_search_form(); ?>
	</div>
-->
</nav>

<label class="chef-mobile-menu-backdrop" for="hamburger" aria-hidden="true"></label>
	
<div class="avviso-contenitore">
	<div class="avviso-principale">
		<?php if( get_field('banner_1', 'option') ): ?>
		<?php the_field('banner_1', 'option'); ?>
		<?php else : ?>
			<style>
				.avviso-principale {
					display: none;
				}
    		</style>
		<?php endif; ?>                 
	</div>		
</div>
	

<?php 
$telefono = get_field('telefono', 'option');
$telefono_link = $telefono ? preg_replace('/[^0-9+]/', '', $telefono) : '';
?>
<div class="wrap-wa">
  <?php if ($telefono_link): ?>
    <a target="_blank" href="https://wa.me/<?php echo esc_attr($telefono_link); ?>">
      <div class="chat-wa blob blob-wa">
        <img width="40" src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/whatsapp.svg" alt="WhatsApp">
      </div>
    </a>
  <?php else: ?>
    <div class="chat-wa blob blob-wa">
      <img width="40" src="<?php echo get_stylesheet_directory_uri(); ?>/img/svg/whatsapp.svg" alt="WhatsApp">
    </div>
  <?php endif; ?>
</div>

	
<div class="super-wrapper">
	<?php //
//if (is_woocommerce() || is_front_page() || is_search() || is_home() ) {
//    get_search_form();
//}
?>
