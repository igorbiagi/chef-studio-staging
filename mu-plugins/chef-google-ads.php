<?php
/**
 * Plugin Name: Chef Studio - Google Ads Tracking
 * Description: Tracking Google Ads limitato alle landing Paid e relative thank-you page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * =========================================================
 * GOOGLE ADS - TAG BASE
 * =========================================================
 *
 * Il Google tag viene caricato SOLO su:
 *
 * SEO
 * /landing-seo-01/
 * /thank-you-landing-seo/
 *
 * SITI WORDPRESS
 * /landing-siti-01/
 * /thank-you-landing-siti/
 *
 * E-COMMERCE
 * /landing-e-commerce-01/
 * /thank-you-landing-e-commerce/
 *
 */

function chefstudio_google_ads_base_tag() {

	$paid_pages = array(

		// SEO
		'landing-seo-01',
		'thank-you-landing-seo',

		// Siti WordPress
		'landing-siti-01',
		'thank-you-landing-siti',

		// E-commerce WooCommerce
		'landing-e-commerce-01',
		'thank-you-landing-e-commerce',
	);

	if ( ! is_page( $paid_pages ) ) {
		return;
	}
	?>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-1008528616"></script>

	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}

		gtag('js', new Date());
		gtag('config', 'AW-1008528616');
	</script>

	<?php
}

add_action( 'wp_head', 'chefstudio_google_ads_base_tag', 20 );


/**
 * =========================================================
 * CONVERSIONE LEAD - SEO
 * =========================================================
 *
 * Si attiva SOLO su:
 * /thank-you-landing-seo/
 */

function chefstudio_google_ads_conversion_seo() {

	if ( ! is_page( 'thank-you-landing-seo' ) ) {
		return;
	}
	?>

	<!-- Google Ads conversion: Lead SEO -->
	<script>
		gtag('event', 'conversion', {
			'send_to': 'AW-1008528616/wQJaCOjoqukcEOjZ8-AD',
			'value': 1.0,
			'currency': 'EUR'
		});
	</script>

	<?php
}

add_action( 'wp_head', 'chefstudio_google_ads_conversion_seo', 30 );


/**
 * =========================================================
 * CONVERSIONE LEAD - SITI WORDPRESS
 * =========================================================
 *
 * Si attiva SOLO su:
 * /thank-you-landing-siti/
 */

function chefstudio_google_ads_conversion_siti() {

	if ( ! is_page( 'thank-you-landing-siti' ) ) {
		return;
	}
	?>

	<!-- Google Ads conversion: Lead Siti WordPress -->
	<script>
		gtag('event', 'conversion', {
			'send_to': 'AW-1008528616/IzdqCL6UoukcEOjZ8-AD',
			'value': 1.0,
			'currency': 'EUR'
		});
	</script>

	<?php
}

add_action( 'wp_head', 'chefstudio_google_ads_conversion_siti', 30 );


/**
 * =========================================================
 * CONVERSIONE LEAD - E-COMMERCE WOOCOMMERCE
 * =========================================================
 *
 * Si attiva SOLO su:
 * /thank-you-landing-e-commerce/
 */

function chefstudio_google_ads_conversion_ecommerce() {

	if ( ! is_page( 'thank-you-landing-e-commerce' ) ) {
		return;
	}
	?>

	<!-- Google Ads conversion: Lead E-commerce -->
	<script>
		gtag('event', 'conversion', {
			'send_to': 'AW-1008528616/fUCLCIquoukcEOjZ8-AD',
			'value': 1.0,
			'currency': 'EUR'
		});
	</script>

	<?php
}

add_action( 'wp_head', 'chefstudio_google_ads_conversion_ecommerce', 30 );