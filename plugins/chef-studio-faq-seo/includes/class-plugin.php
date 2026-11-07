<?php
/**
 * Plugin bootstrap.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin bootstrapper.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance;

	/**
	 * Whether the plugin has already been booted.
	 *
	 * @var bool
	 */
	private $booted = false;

	/**
	 * Returns the singleton instance.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Prevent direct construction.
	 */
	private function __construct() {
	}

	/**
	 * Boots the plugin.
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;
		$this->migrate_legacy_options();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		$settings = new Settings();
		$settings->register();

		$admin_notices = new Admin_Notices();
		$admin_notices->register();

		$acf_fields = new Acf_Fields();
		$acf_fields->register();

		$import_handler = new Import_Handler();
		$import_handler->register();

		$frontend_render = new Frontend_Render();
		$frontend_render->register();

		$schema_output = new Schema_Output();
		$schema_output->register();
	}

	/**
	 * Loads plugin translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( plugin_textdomain(), false, dirname( plugin_basename( CSFAQ_FILE ) ) . '/languages' );
	}

	/**
	 * Enqueues light admin enhancements on supported post edit screens.
	 *
	 * @param string $hook_suffix Current admin hook.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		$load_assets = false;

		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) && is_acf_pro_active() ) {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			if ( ! $screen || is_supported_post_type( (string) $screen->post_type ) ) {
				$load_assets = true;
			}
		}

		if ( 'settings_page_' . Settings::PAGE_SLUG === $hook_suffix ) {
			$load_assets = true;
		}

		if ( ! $load_assets ) {
			return;
		}

		wp_enqueue_style(
			'csfaq-admin',
			CSFAQ_URL . 'assets/admin.css',
			array(),
			CSFAQ_VERSION
		);

		wp_enqueue_script(
			'csfaq-admin',
			CSFAQ_URL . 'assets/admin.js',
			array(),
			CSFAQ_VERSION,
			true
		);

		wp_localize_script(
			'csfaq-admin',
			'csfaqAdmin',
			array(
				'importFieldName'     => Acf_Fields::FIELD_IMPORT_RAW_NAME,
				'importModeFieldName' => Acf_Fields::FIELD_IMPORT_MODE_NAME,
				'appendHint'          => __( 'Aggiungi inserisce le nuove FAQ dopo quelle già presenti nel repeater.', plugin_textdomain() ),
				'replaceHint'         => __( 'Sostituisci elimina le FAQ attuali e mantiene solo quelle appena importate.', plugin_textdomain() ),
				'copySuccess'         => __( 'Codice copiato negli appunti.', plugin_textdomain() ),
				'copyFailure'         => __( 'Seleziona e copia manualmente il codice.', plugin_textdomain() ),
			)
		);
	}

	/**
	 * Runs on plugin activation.
	 */
	public static function activate() {
		self::instance()->migrate_legacy_options();
		update_option( 'csfaq_version', CSFAQ_VERSION );

		if ( false === get_option( 'csfaq_frontend_output_mode', false ) ) {
			add_option( 'csfaq_frontend_output_mode', 'content' );
		}

		if ( false === get_option( 'csfaq_enabled_post_types', false ) ) {
			add_option( 'csfaq_enabled_post_types', array( 'post' ) );
		}

		if ( false === get_option( 'csfaq_post_type_frontend_settings', false ) ) {
			add_option( 'csfaq_post_type_frontend_settings', array() );
		}
	}

	/**
	 * Runs on plugin deactivation.
	 */
	public static function deactivate() {
		// Intentionally left blank: user data must persist.
	}

	/**
	 * Migrates legacy option names to the current csfaq_* keys.
	 */
	private function migrate_legacy_options() {
		$legacy_prefix = implode( '', array( 'p', 'f', 'i', 's' ) );

		$option_map = array(
			$legacy_prefix . '_version'                     => 'csfaq_version',
			$legacy_prefix . '_frontend_output_mode'        => 'csfaq_frontend_output_mode',
			$legacy_prefix . '_enabled_post_types'          => 'csfaq_enabled_post_types',
			$legacy_prefix . '_post_type_frontend_settings' => 'csfaq_post_type_frontend_settings',
		);

		foreach ( $option_map as $old_option => $new_option ) {
			$old_value = get_option( $old_option, null );
			$new_value = get_option( $new_option, null );

			if ( null !== $old_value && null === $new_value ) {
				update_option( $new_option, $old_value );
			}

			if ( null !== $old_value ) {
				delete_option( $old_option );
			}
		}
	}
}
