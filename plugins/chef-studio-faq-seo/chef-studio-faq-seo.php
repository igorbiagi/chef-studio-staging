<?php
/**
 * Plugin Name: Chef Studio FAQ SEO
 * Plugin URI:  https://www.chefstudio.it
 * Description: Plugin WordPress per FAQ SEO con schema JSON-LD automatico e gestione avanzata delle FAQ su articoli, CPT e faq.php + cta_con_paragrafo_o_faq.php del layout flessibile. Titolo FAQ compatibile con WPML String Translation.
 * Version:     1.6.5
 * Author:      Igor Biagi - Chef Studio
 * Author URI:  https://www.chefstudio.it
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chef-studio-faq
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package ChefStudio\FAQ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CSFAQ_VERSION' ) ) {
	define( 'CSFAQ_VERSION', '1.6.5' );
}

if ( ! defined( 'CSFAQ_FILE' ) ) {
	define( 'CSFAQ_FILE', __FILE__ );
}

if ( ! defined( 'CSFAQ_PATH' ) ) {
	define( 'CSFAQ_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CSFAQ_URL' ) ) {
	define( 'CSFAQ_URL', plugin_dir_url( __FILE__ ) );
}

require_once CSFAQ_PATH . 'includes/helpers.php';
require_once CSFAQ_PATH . 'includes/class-admin-notices.php';
require_once CSFAQ_PATH . 'includes/class-acf-fields.php';
require_once CSFAQ_PATH . 'includes/class-import-handler.php';
require_once CSFAQ_PATH . 'includes/class-frontend-render.php';
require_once CSFAQ_PATH . 'includes/class-schema-output.php';
require_once CSFAQ_PATH . 'includes/class-settings.php';
require_once CSFAQ_PATH . 'includes/class-plugin.php';

register_activation_hook( CSFAQ_FILE, array( '\ChefStudio\FAQ\Plugin', 'activate' ) );
register_deactivation_hook( CSFAQ_FILE, array( '\ChefStudio\FAQ\Plugin', 'deactivate' ) );

\ChefStudio\FAQ\Plugin::instance()->boot();

if ( ! function_exists( 'csfaq_get_faq_markup' ) ) {
	/**
	 * Returns FAQ markup for manual theme output.
	 *
	 * @param int $post_id Optional post ID.
	 * @return string
	 */
	function csfaq_get_faq_markup( $post_id = 0 ) {
		return \ChefStudio\FAQ\get_faq_markup( $post_id );
	}
}

if ( ! function_exists( 'csfaq_the_faq_markup' ) ) {
	/**
	 * Echoes FAQ markup for manual theme output.
	 *
	 * @param int $post_id Optional post ID.
	 */
	function csfaq_the_faq_markup( $post_id = 0 ) {
		echo \ChefStudio\FAQ\get_faq_markup( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
