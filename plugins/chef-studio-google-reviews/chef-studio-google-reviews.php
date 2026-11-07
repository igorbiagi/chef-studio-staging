<?php
/**
 * Plugin Name: Chef Studio Google Reviews con Schema
 * Plugin URI: https://www.chefstudio.it
 * Description: Plugin WordPress per recensioni Google My Business con schema JSON-LD automatico via API.
 * Version: 1.0.1
 * Author: Igor Biagi - Chef Studio
 * Author URI: https://www.chefstudio.it
 * Text Domain: chef-studio-google-reviews
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * Per usare il plugin servono:
 * 1. Progetto Google Cloud.
 * 2. Accesso alla Google Business Profile API.
 * 3. OAuth Client ID.
 * 4. OAuth Client Secret.
 * 5. Refresh Token con scope https://www.googleapis.com/auth/business.manage.
 * 6. Account ID.
 * 7. Location ID verificata.
 * 8. URL profilo Google Business.
 *
 * Il plugin non usa scraping, non dipende da Trustindex e non chiama Google
 * a ogni caricamento pagina. Le recensioni vengono aggiornate tramite API
 * ufficiale e salvate in cache locale. L'aggiornamento automatico predefinito
 * è settimanale; dal backend è disponibile il bottone "Aggiorna recensioni ora"
 * per forzare un refresh manuale.
 *
 * @package ChefStudio\GREW
 */

namespace ChefStudio\GREW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CSGREW_VERSION', '1.0.1' );
define( 'CSGREW_FILE', __FILE__ );
define( 'CSGREW_PATH', plugin_dir_path( __FILE__ ) );
define( 'CSGREW_URL', plugin_dir_url( __FILE__ ) );
define( 'CSGREW_SETTINGS_OPTION', 'csgrew_settings' );
define( 'CSGREW_DATA_OPTION', 'csgrew_data' );
define( 'CSGREW_CRON_HOOK', 'csgrew_update_event' );
define( 'CSGREW_WEEKLY_SCHEDULE', 'csgrew_weekly' );

require_once CSGREW_PATH . 'includes/class-chef-google-reviews-api.php';
require_once CSGREW_PATH . 'includes/class-chef-google-reviews-admin.php';
require_once CSGREW_PATH . 'includes/class-chef-google-reviews-shortcodes.php';
require_once CSGREW_PATH . 'includes/class-chef-google-reviews-schema.php';

/**
 * Return default plugin settings.
 *
 * @return array<string,mixed>
 */
function csgrew_default_settings(): array {
	return array(
		'client_id'                   => '',
		'client_secret'               => '',
		'refresh_token'               => '',
		'account_id'                  => '',
		'location_id'                 => '',
		'business_name'               => 'Chef Studio / Design Siti Web, Consulenza SEO, Grafica e Foto',
		'business_url'                => 'https://maps.app.goo.gl/b3eS7jqkT7RrQ7Ey8',
		'logo'                        => '',
		'source_note'                 => 'Recensioni tratte dal profilo Google Business di Chef Studio.',
		'api_review_limit'            => 50,
		'shortcode_limit'             => 8,
		'min_rating'                  => 1,
		'only_with_text'              => 1,
		'show_avatar'                 => 1,
		'show_google_logo'            => 1,
		'show_read_more'              => 1,
		'read_more_chars'             => 135,
		'enable_schema'               => 1,
		'enable_yoast_aggregate'      => 1,
		'schema_only_shortcode_pages' => 1,
		'front_page_aggregate'        => 1,
		'update_frequency'            => 'weekly',
		'global_assets'               => 0,
		'hidden_review_ids'           => array(),
	);
}

/**
 * Return default public data cache.
 *
 * @return array<string,mixed>
 */
function csgrew_default_data(): array {
	return array(
		'updated_at'        => 0,
		'averageRating'     => '0.0',
		'totalReviewCount'  => 0,
		'reviews'           => array(),
		'last_error'        => '',
		'raw_debug'         => array(),
		'business_url'      => '',
	);
}

/**
 * Get merged plugin settings.
 *
 * @return array<string,mixed>
 */
function csgrew_get_settings(): array {
	$settings = get_option( CSGREW_SETTINGS_OPTION, array() );

	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	return wp_parse_args( $settings, csgrew_default_settings() );
}

/**
 * Get merged public cache data.
 *
 * @return array<string,mixed>
 */
function csgrew_get_public_data(): array {
	$data = get_option( CSGREW_DATA_OPTION, array() );

	if ( ! is_array( $data ) ) {
		$data = array();
	}

	$data = wp_parse_args( $data, csgrew_default_data() );

	if ( ! is_array( $data['reviews'] ) ) {
		$data['reviews'] = array();
	}

	if ( empty( $data['business_url'] ) ) {
		$settings             = csgrew_get_settings();
		$data['business_url'] = $settings['business_url'];
	}

	return $data;
}

/**
 * Cast common shortcode/admin values to bool.
 *
 * @param mixed $value   Raw value.
 * @param bool  $default Default when value is not explicit.
 * @return bool
 */
function csgrew_bool( mixed $value, bool $default = false ): bool {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( null === $value || '' === $value ) {
		return $default;
	}

	$value = strtolower( trim( (string) $value ) );

	if ( in_array( $value, array( '1', 'true', 'yes', 'on', 'si', 'sì' ), true ) ) {
		return true;
	}

	if ( in_array( $value, array( '0', 'false', 'no', 'off', 'manuale' ), true ) ) {
		return false;
	}

	return $default;
}

/**
 * Resolve a logo setting that can be either a URL or an attachment ID.
 *
 * @param mixed $logo Logo setting.
 * @return string
 */
function csgrew_resolve_logo_url( mixed $logo ): string {
	$logo = is_string( $logo ) || is_numeric( $logo ) ? trim( (string) $logo ) : '';

	if ( '' === $logo ) {
		return '';
	}

	if ( ctype_digit( $logo ) ) {
		$url = wp_get_attachment_image_url( absint( $logo ), 'thumbnail' );
		return $url ? $url : '';
	}

	return esc_url_raw( $logo );
}

/**
 * Italian relative time labels for review dates.
 *
 * @param string $datetime ISO datetime.
 * @return string
 */
function csgrew_relative_time( string $datetime ): string {
	$timestamp = strtotime( $datetime );

	if ( false === $timestamp ) {
		return '';
	}

	$now  = current_time( 'timestamp' );
	$diff = max( 0, $now - $timestamp );

	if ( $diff < DAY_IN_SECONDS ) {
		return 'oggi';
	}

	if ( $diff < WEEK_IN_SECONDS ) {
		$days = max( 1, (int) floor( $diff / DAY_IN_SECONDS ) );
		return 1 === $days ? '1 giorno fa' : $days . ' giorni fa';
	}

	if ( $diff < MONTH_IN_SECONDS ) {
		$weeks = max( 1, (int) floor( $diff / WEEK_IN_SECONDS ) );
		return 1 === $weeks ? '1 settimana fa' : $weeks . ' settimane fa';
	}

	if ( $diff < YEAR_IN_SECONDS ) {
		$months = max( 1, (int) floor( $diff / MONTH_IN_SECONDS ) );
		return 1 === $months ? '1 mese fa' : $months . ' mesi fa';
	}

	$years = max( 1, (int) floor( $diff / YEAR_IN_SECONDS ) );
	return 1 === $years ? '1 anno fa' : $years . ' anni fa';
}

/**
 * Register custom cron schedules.
 *
 * @param array<string,array<string,mixed>> $schedules Existing schedules.
 * @return array<string,array<string,mixed>>
 */
function csgrew_cron_schedules( array $schedules ): array {
	if ( ! isset( $schedules[ CSGREW_WEEKLY_SCHEDULE ] ) ) {
		$schedules[ CSGREW_WEEKLY_SCHEDULE ] = array(
			'interval' => 7 * DAY_IN_SECONDS,
			'display'  => __( 'Weekly', 'chef-studio-google-reviews' ),
		);
	}

	return $schedules;
}
add_filter( 'cron_schedules', __NAMESPACE__ . '\\csgrew_cron_schedules' );

/**
 * Clear and schedule the update event according to settings.
 *
 * @param string|null $frequency Optional frequency override.
 * @return void
 */
function csgrew_reschedule( ?string $frequency = null ): void {
	wp_clear_scheduled_hook( CSGREW_CRON_HOOK );

	$settings  = csgrew_get_settings();
	$frequency = $frequency ?: (string) $settings['update_frequency'];

	if ( 'manual' === $frequency ) {
		return;
	}

	if ( 'daily' === $frequency ) {
		wp_schedule_event( time(), 'daily', CSGREW_CRON_HOOK );
		return;
	}

	wp_schedule_event( time(), CSGREW_WEEKLY_SCHEDULE, CSGREW_CRON_HOOK );
}

/**
 * Detect review shortcodes in the main post content.
 *
 * @return bool
 */
function csgrew_current_page_has_shortcode(): bool {
	global $post;

	if ( ! $post instanceof \WP_Post || empty( $post->post_content ) ) {
		return false;
	}

	return has_shortcode( $post->post_content, 'chef_google_reviews' )
		|| has_shortcode( $post->post_content, 'chef_google_reviews_summary' );
}

/**
 * Register frontend assets and enqueue them when detectable.
 *
 * @return void
 */
function csgrew_register_assets(): void {
	$css_path = CSGREW_PATH . 'assets/css/chef-google-reviews.css';
	$js_path  = CSGREW_PATH . 'assets/js/chef-google-reviews.js';
	$version  = CSGREW_VERSION;

	if ( file_exists( $css_path ) ) {
		$version = (string) filemtime( $css_path );
	}

	wp_register_style(
		'csgrew-google-reviews',
		CSGREW_URL . 'assets/css/chef-google-reviews.css',
		array(),
		$version
	);

	wp_register_script(
		'csgrew-google-reviews',
		CSGREW_URL . 'assets/js/chef-google-reviews.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : CSGREW_VERSION,
		true
	);

	$settings = csgrew_get_settings();

	if ( ! is_admin() && ( ! empty( $settings['global_assets'] ) || csgrew_current_page_has_shortcode() ) ) {
		csgrew_enqueue_assets();
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\csgrew_register_assets' );

/**
 * Enqueue frontend assets.
 *
 * @return void
 */
function csgrew_enqueue_assets(): void {
	wp_enqueue_style( 'csgrew-google-reviews' );
	wp_enqueue_script( 'csgrew-google-reviews' );
}

/**
 * Activation callback.
 *
 * @return void
 */
function csgrew_activate(): void {
	if ( false === get_option( CSGREW_SETTINGS_OPTION, false ) ) {
		add_option( CSGREW_SETTINGS_OPTION, csgrew_default_settings(), '', false );
	}

	if ( false === get_option( CSGREW_DATA_OPTION, false ) ) {
		add_option( CSGREW_DATA_OPTION, csgrew_default_data(), '', false );
	}

	csgrew_reschedule();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\csgrew_activate' );

/**
 * Deactivation callback.
 *
 * @return void
 */
function csgrew_deactivate(): void {
	wp_clear_scheduled_hook( CSGREW_CRON_HOOK );
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\csgrew_deactivate' );

/**
 * Bootstrap plugin classes.
 *
 * @return void
 */
function csgrew_bootstrap(): void {
	load_plugin_textdomain( 'chef-studio-google-reviews', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	API::instance();
	Schema::instance();
	Shortcodes::instance();

	if ( is_admin() ) {
		Admin::instance();
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\csgrew_bootstrap' );
