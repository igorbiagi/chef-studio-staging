<?php
/**
 * Admin notices.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin admin notices.
 */
final class Admin_Notices {

	/**
	 * Registers the notice hooks.
	 */
	public function register() {
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
	}

	/**
	 * Persists an import notice across the post-save redirect.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $message Notice message.
	 * @param string $type    success|warning|error|info.
	 */
	public static function store_notice( $post_id, $message, $type = 'info' ) {
		$user_id = get_current_user_id();
		$post_id = absint( $post_id );

		if ( ! $user_id || ! $post_id || '' === trim( (string) $message ) ) {
			return;
		}

		$payload = array(
			'type'    => self::sanitize_notice_type( $type ),
			'message' => wp_kses_post( (string) $message ),
		);

		set_transient( self::get_transient_key( $user_id, $post_id ), $payload, 10 * MINUTE_IN_SECONDS );
	}

	/**
	 * Renders all notices for the current admin request.
	 */
	public function render_notices() {
		$this->render_acf_notice();
		$this->render_import_notice();
	}

	/**
	 * Renders the ACF Pro dependency notice.
	 */
	private function render_acf_notice() {
		if ( is_acf_pro_active() || ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$message = __(
			'Chef Studio FAQ SEO richiede ACF Pro per registrare i campi FAQ e gestire l’import. L’output delle FAQ già salvate continuerà comunque a funzionare sui post che hanno già i meta esistenti.',
			plugin_textdomain()
		);

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html( $message )
		);
	}

	/**
	 * Renders an import notice for the current post edit screen.
	 */
	private function render_import_notice() {
		$post_id = $this->get_request_post_id();
		$user_id = get_current_user_id();

		if ( ! $post_id || ! $user_id ) {
			return;
		}

		$payload = get_transient( self::get_transient_key( $user_id, $post_id ) );

		if ( ! is_array( $payload ) || empty( $payload['message'] ) ) {
			return;
		}

		delete_transient( self::get_transient_key( $user_id, $post_id ) );

		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $payload['type'] ),
			wp_kses_post( $payload['message'] )
		);
	}

	/**
	 * Returns the current post ID from the request.
	 */
	private function get_request_post_id() {
		if ( isset( $_GET['post'] ) ) {
			return absint( wp_unslash( $_GET['post'] ) );
		}

		if ( isset( $_POST['post_ID'] ) ) {
			return absint( wp_unslash( $_POST['post_ID'] ) );
		}

		return 0;
	}

	/**
	 * Builds a unique transient key per user and post.
	 *
	 * @param int $user_id User ID.
	 * @param int $post_id Post ID.
	 */
	private static function get_transient_key( $user_id, $post_id ) {
		return 'csfaq_notice_' . absint( $user_id ) . '_' . absint( $post_id );
	}

	/**
	 * Sanitizes notice types.
	 *
	 * @param string $type Notice type.
	 */
	private static function sanitize_notice_type( $type ) {
		$allowed = array( 'success', 'warning', 'error', 'info' );

		return in_array( $type, $allowed, true ) ? $type : 'info';
	}
}
