<?php
/**
 * Admin settings page.
 *
 * @package ChefStudio\GREW
 */

namespace ChefStudio\GREW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin admin UI and secure settings updates.
 */
final class Admin {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Admin page slug.
	 */
	private const PAGE_SLUG = 'csgrew-google-reviews';

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_post_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Load admin-only assets for the plugin settings page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'csgrew-admin',
			CSGREW_URL . 'assets/js/chef-google-reviews-admin.js',
			array(),
			CSGREW_VERSION,
			true
		);
		wp_localize_script(
			'csgrew-admin',
			'csgrewAdmin',
			array(
				'mediaTitle'  => __( 'Seleziona logo attività', 'chef-studio-google-reviews' ),
				'mediaButton' => __( 'Usa questo logo', 'chef-studio-google-reviews' ),
			)
		);
	}

	/**
	 * Register settings page under Settings.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_options_page(
			__( 'CS Google Reviews', 'chef-studio-google-reviews' ),
			__( 'CS Google Reviews', 'chef-studio-google-reviews' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Process secure admin POST actions.
	 *
	 * @return void
	 */
	public function handle_post_actions(): void {
		if ( empty( $_POST['csgrew_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permessi insufficienti.', 'chef-studio-google-reviews' ) );
		}

		$raw_action = wp_unslash( $_POST['csgrew_action'] );

		if ( is_array( $raw_action ) ) {
			return;
		}

		$action = sanitize_key( $raw_action );

		if ( 'save_settings' === $action ) {
			check_admin_referer( 'csgrew_save_settings', 'csgrew_nonce' );
			$this->save_settings();
			$this->redirect_with_notice( 'settings_saved' );
		}

		if ( 'refresh_reviews' === $action ) {
			check_admin_referer( 'csgrew_refresh_reviews', 'csgrew_nonce' );
			$result = API::instance()->refresh_reviews();
			$this->redirect_with_notice( is_wp_error( $result ) ? 'refresh_error' : 'refresh_ok' );
		}

		if ( 'clear_cache' === $action ) {
			check_admin_referer( 'csgrew_clear_cache', 'csgrew_nonce' );
			update_option( CSGREW_DATA_OPTION, csgrew_default_data(), false );
			$this->redirect_with_notice( 'cache_cleared' );
		}

		if ( 'save_review_visibility' === $action ) {
			check_admin_referer( 'csgrew_save_review_visibility', 'csgrew_nonce' );
			$this->save_review_visibility();
			$this->redirect_with_notice( 'review_visibility_saved' );
		}
	}

	/**
	 * Render the admin settings page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = csgrew_get_settings();
		$data     = csgrew_get_public_data();
		$next     = wp_next_scheduled( CSGREW_CRON_HOOK );
		$tab      = $this->get_current_tab();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'CS Google Reviews', 'chef-studio-google-reviews' ); ?></h1>

			<?php $this->render_notice(); ?>
			<?php $this->render_tabs( $tab, count( $data['reviews'] ) ); ?>

			<?php if ( 'reviews' === $tab ) : ?>
				<?php $this->render_cache_actions( $tab ); ?>
				<?php $this->render_reviews_manager( $data, $settings ); ?>
			<?php elseif ( 'guide' === $tab ) : ?>
				<?php $this->render_setup_guide(); ?>
			<?php else : ?>
				<?php $this->render_status_table( $data, $next ); ?>
				<?php $this->render_cache_actions( $tab ); ?>

				<form method="post" action="">
					<?php wp_nonce_field( 'csgrew_save_settings', 'csgrew_nonce' ); ?>
					<input type="hidden" name="csgrew_action" value="save_settings">
					<input type="hidden" name="csgrew_redirect_tab" value="settings">

					<h2><?php esc_html_e( 'Google OAuth / API', 'chef-studio-google-reviews' ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
							<?php $this->render_text_row( 'client_id', __( 'Client ID', 'chef-studio-google-reviews' ), (string) $settings['client_id'], 'text' ); ?>
							<?php $this->render_sensitive_row( 'client_secret', __( 'Client Secret', 'chef-studio-google-reviews' ), (string) $settings['client_secret'] ); ?>
							<?php $this->render_sensitive_row( 'refresh_token', __( 'Refresh Token', 'chef-studio-google-reviews' ), (string) $settings['refresh_token'] ); ?>
							<?php $this->render_text_row( 'account_id', __( 'Account ID', 'chef-studio-google-reviews' ), (string) $settings['account_id'], 'text' ); ?>
							<?php $this->render_text_row( 'location_id', __( 'Location ID', 'chef-studio-google-reviews' ), (string) $settings['location_id'], 'text' ); ?>
						</tbody>
					</table>

					<h2><?php esc_html_e( 'Profilo attività', 'chef-studio-google-reviews' ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
							<?php $this->render_text_row( 'business_name', __( 'Nome attività mostrato', 'chef-studio-google-reviews' ), (string) $settings['business_name'], 'text' ); ?>
							<?php $this->render_text_row( 'business_url', __( 'Google Business Profile URL', 'chef-studio-google-reviews' ), (string) $settings['business_url'], 'url' ); ?>
							<?php $this->render_logo_row( (string) $settings['logo'] ); ?>
							<?php $this->render_textarea_row( 'source_note', __( 'Testo sorgente recensioni', 'chef-studio-google-reviews' ), (string) $settings['source_note'] ); ?>
						</tbody>
					</table>

					<h2><?php esc_html_e( 'Visualizzazione', 'chef-studio-google-reviews' ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
							<?php $this->render_number_row( 'api_review_limit', __( "Numero recensioni da recuperare dall'API", 'chef-studio-google-reviews' ), absint( $settings['api_review_limit'] ), 1, 500 ); ?>
							<?php $this->render_number_row( 'shortcode_limit', __( 'Numero recensioni da mostrare nello shortcode default', 'chef-studio-google-reviews' ), absint( $settings['shortcode_limit'] ), 1, 100 ); ?>
							<?php $this->render_number_row( 'min_rating', __( 'Rating minimo da mostrare', 'chef-studio-google-reviews' ), absint( $settings['min_rating'] ), 1, 5 ); ?>
							<?php $this->render_checkbox_row( 'only_with_text', __( 'Mostra solo recensioni con testo', 'chef-studio-google-reviews' ), ! empty( $settings['only_with_text'] ) ); ?>
							<?php $this->render_checkbox_row( 'show_avatar', __( 'Mostra avatar autore', 'chef-studio-google-reviews' ), ! empty( $settings['show_avatar'] ) ); ?>
							<?php $this->render_checkbox_row( 'show_google_logo', __( 'Mostra logo Google', 'chef-studio-google-reviews' ), ! empty( $settings['show_google_logo'] ) ); ?>
							<?php $this->render_checkbox_row( 'show_read_more', __( 'Mostra link "Leggi di più"', 'chef-studio-google-reviews' ), ! empty( $settings['show_read_more'] ) ); ?>
							<?php $this->render_number_row( 'read_more_chars', __( 'Numero caratteri prima del "Leggi di più"', 'chef-studio-google-reviews' ), absint( $settings['read_more_chars'] ), 40, 2000 ); ?>
							<?php $this->render_checkbox_row( 'global_assets', __( 'Carica asset globalmente', 'chef-studio-google-reviews' ), ! empty( $settings['global_assets'] ) ); ?>
						</tbody>
					</table>

					<h2><?php esc_html_e( 'Schema', 'chef-studio-google-reviews' ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
							<?php $this->render_checkbox_row( 'enable_schema', __( 'Abilita Schema JSON-LD', 'chef-studio-google-reviews' ), ! empty( $settings['enable_schema'] ) ); ?>
							<?php $this->render_checkbox_row( 'enable_yoast_aggregate', __( 'Abilita AggregateRating su Organization Yoast', 'chef-studio-google-reviews' ), ! empty( $settings['enable_yoast_aggregate'] ) ); ?>
							<?php $this->render_checkbox_row( 'schema_only_shortcode_pages', __( 'Inserisci Schema solo nelle pagine dove compare lo shortcode', 'chef-studio-google-reviews' ), ! empty( $settings['schema_only_shortcode_pages'] ) ); ?>
							<?php $this->render_checkbox_row( 'front_page_aggregate', __( 'Se front page, consenti aggregateRating anche se lo shortcode è in home', 'chef-studio-google-reviews' ), ! empty( $settings['front_page_aggregate'] ) ); ?>
						</tbody>
					</table>

					<h2><?php esc_html_e( 'Aggiornamento', 'chef-studio-google-reviews' ); ?></h2>
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row"><label for="update_frequency"><?php esc_html_e( 'Frequenza aggiornamento', 'chef-studio-google-reviews' ); ?></label></th>
								<td>
									<select name="update_frequency" id="update_frequency">
										<option value="manual" <?php selected( $settings['update_frequency'], 'manual' ); ?>><?php esc_html_e( 'manuale', 'chef-studio-google-reviews' ); ?></option>
										<option value="daily" <?php selected( $settings['update_frequency'], 'daily' ); ?>><?php esc_html_e( 'giornaliera', 'chef-studio-google-reviews' ); ?></option>
										<option value="weekly" <?php selected( $settings['update_frequency'], 'weekly' ); ?>><?php esc_html_e( 'settimanale', 'chef-studio-google-reviews' ); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>

					<?php submit_button( __( 'Salva impostazioni', 'chef-studio-google-reviews' ) ); ?>
				</form>

				<?php $this->render_debug_box( $data ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Save sanitized settings.
	 *
	 * @return void
	 */
	private function save_settings(): void {
		$current = csgrew_get_settings();

		$settings = array(
			'client_id'                   => $this->posted_text( 'client_id' ),
			'client_secret'               => $this->posted_sensitive( 'client_secret', (string) $current['client_secret'] ),
			'refresh_token'               => $this->posted_sensitive( 'refresh_token', (string) $current['refresh_token'] ),
			'account_id'                  => $this->posted_text( 'account_id' ),
			'location_id'                 => $this->posted_text( 'location_id' ),
			'business_name'               => $this->posted_text( 'business_name', (string) csgrew_default_settings()['business_name'] ),
			'business_url'                => esc_url_raw( $this->posted_text( 'business_url', (string) csgrew_default_settings()['business_url'] ) ),
			'logo'                        => $this->posted_text( 'logo' ),
			'source_note'                 => $this->posted_textarea( 'source_note', (string) csgrew_default_settings()['source_note'] ),
			'api_review_limit'            => max( 1, min( 500, $this->posted_absint( 'api_review_limit', 50 ) ) ),
			'shortcode_limit'             => max( 1, min( 100, $this->posted_absint( 'shortcode_limit', 8 ) ) ),
			'min_rating'                  => max( 1, min( 5, $this->posted_absint( 'min_rating', 1 ) ) ),
			'only_with_text'              => $this->posted_checkbox( 'only_with_text' ),
			'show_avatar'                 => $this->posted_checkbox( 'show_avatar' ),
			'show_google_logo'            => $this->posted_checkbox( 'show_google_logo' ),
			'show_read_more'              => $this->posted_checkbox( 'show_read_more' ),
			'read_more_chars'             => max( 40, min( 2000, $this->posted_absint( 'read_more_chars', 135 ) ) ),
			'enable_schema'               => $this->posted_checkbox( 'enable_schema' ),
			'enable_yoast_aggregate'      => $this->posted_checkbox( 'enable_yoast_aggregate' ),
			'schema_only_shortcode_pages' => $this->posted_checkbox( 'schema_only_shortcode_pages' ),
			'front_page_aggregate'        => $this->posted_checkbox( 'front_page_aggregate' ),
			'update_frequency'            => $this->posted_frequency(),
			'global_assets'               => $this->posted_checkbox( 'global_assets' ),
			'hidden_review_ids'           => $this->sanitize_review_ids( $current['hidden_review_ids'] ?? array() ),
		);

		update_option( CSGREW_SETTINGS_OPTION, wp_parse_args( $settings, csgrew_default_settings() ), false );

		API::instance()->clear_access_token();
		csgrew_reschedule( $settings['update_frequency'] );
	}

	/**
	 * Return the active settings tab.
	 *
	 * @return string
	 */
	private function get_current_tab(): string {
		$raw_tab = isset( $_GET['tab'] ) ? wp_unslash( $_GET['tab'] ) : 'settings';
		$tab     = is_array( $raw_tab ) ? 'settings' : sanitize_key( $raw_tab );

		return $this->sanitize_tab( $tab );
	}

	/**
	 * Normalize an admin tab key.
	 *
	 * @param string $tab Raw tab key.
	 * @return string
	 */
	private function sanitize_tab( string $tab ): string {
		return in_array( $tab, array( 'settings', 'reviews', 'guide' ), true ) ? $tab : 'settings';
	}

	/**
	 * Render admin navigation tabs.
	 *
	 * @param string $active_tab  Current tab.
	 * @param int    $review_count Cached review count.
	 * @return void
	 */
	private function render_tabs( string $active_tab, int $review_count ): void {
		$tabs = array(
			'settings' => __( 'Impostazioni', 'chef-studio-google-reviews' ),
			'reviews'  => sprintf(
				/* translators: %d is cached review count. */
				__( 'Recensioni salvate (%d)', 'chef-studio-google-reviews' ),
				$review_count
			),
			'guide'    => __( 'Guida API', 'chef-studio-google-reviews' ),
		);
		?>
		<nav class="nav-tab-wrapper" style="margin-top: 16px;">
			<?php foreach ( $tabs as $tab => $label ) : ?>
				<?php
				$url = add_query_arg(
					array(
						'page' => self::PAGE_SLUG,
						'tab'  => $tab,
					),
					admin_url( 'options-general.php' )
				);
				?>
				<a class="nav-tab <?php echo $active_tab === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	/**
	 * Render cache status table.
	 *
	 * @param array<string,mixed> $data Cache data.
	 * @param int|false           $next Next cron timestamp.
	 * @return void
	 */
	private function render_status_table( array $data, int|false $next ): void {
		?>
		<h2><?php esc_html_e( 'Stato cache', 'chef-studio-google-reviews' ); ?></h2>
		<table class="widefat striped" style="max-width: 980px;">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Ultimo aggiornamento dati', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo esc_html( ! empty( $data['updated_at'] ) ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), absint( $data['updated_at'] ) ) : __( 'Mai', 'chef-studio-google-reviews' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Prossimo aggiornamento programmato', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo esc_html( $next ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next ) : __( 'Non programmato', 'chef-studio-google-reviews' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Numero recensioni salvate', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo esc_html( (string) count( $data['reviews'] ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Rating medio salvato', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo esc_html( (string) $data['averageRating'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Numero totale recensioni salvato', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo esc_html( (string) absint( $data['totalReviewCount'] ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Ultimo errore API', 'chef-studio-google-reviews' ); ?></th>
					<td><?php echo ! empty( $data['last_error'] ) ? esc_html( (string) $data['last_error'] ) : esc_html__( 'Nessun errore salvato.', 'chef-studio-google-reviews' ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Render cache action buttons.
	 *
	 * @param string $tab Current tab.
	 * @return void
	 */
	private function render_cache_actions( string $tab ): void {
		$tab = $this->sanitize_tab( $tab );
		?>
		<div style="margin: 16px 0;">
			<form method="post" style="display:inline-block; margin-right: 8px;">
				<?php wp_nonce_field( 'csgrew_refresh_reviews', 'csgrew_nonce' ); ?>
				<input type="hidden" name="csgrew_action" value="refresh_reviews">
				<input type="hidden" name="csgrew_redirect_tab" value="<?php echo esc_attr( $tab ); ?>">
				<?php submit_button( __( 'Aggiorna recensioni ora', 'chef-studio-google-reviews' ), 'primary', 'submit', false ); ?>
			</form>

			<form method="post" style="display:inline-block;">
				<?php wp_nonce_field( 'csgrew_clear_cache', 'csgrew_nonce' ); ?>
				<input type="hidden" name="csgrew_action" value="clear_cache">
				<input type="hidden" name="csgrew_redirect_tab" value="<?php echo esc_attr( $tab ); ?>">
				<?php submit_button( __( 'Svuota cache recensioni', 'chef-studio-google-reviews' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render admin notices.
	 *
	 * @return void
	 */
	private function render_notice(): void {
		$raw_notice = isset( $_GET['csgrew_notice'] ) ? wp_unslash( $_GET['csgrew_notice'] ) : '';
		$notice     = is_array( $raw_notice ) ? '' : sanitize_key( $raw_notice );

		if ( '' === $notice ) {
			return;
		}

		$messages = array(
			'settings_saved' => array( 'success', __( 'Impostazioni salvate.', 'chef-studio-google-reviews' ) ),
			'refresh_ok'     => array( 'success', __( 'Recensioni aggiornate correttamente.', 'chef-studio-google-reviews' ) ),
			'refresh_error'  => array( 'error', __( 'Aggiornamento non riuscito. Controlla "Ultimo errore API" nello stato cache.', 'chef-studio-google-reviews' ) ),
			'cache_cleared'  => array( 'success', __( 'Cache recensioni svuotata.', 'chef-studio-google-reviews' ) ),
			'review_visibility_saved' => array( 'success', __( 'Visibilità recensioni salvata.', 'chef-studio-google-reviews' ) ),
		);

		if ( empty( $messages[ $notice ] ) ) {
			return;
		}

		$class   = $messages[ $notice ][0];
		$message = $messages[ $notice ][1];
		?>
		<div class="notice notice-<?php echo esc_attr( $class ); ?> is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}

	/**
	 * Save which cached reviews should be hidden on frontend.
	 *
	 * @return void
	 */
	private function save_review_visibility(): void {
		$settings    = csgrew_get_settings();
		$data        = csgrew_get_public_data();
		$visible_ids = isset( $_POST['visible_review_ids'] ) ? wp_unslash( $_POST['visible_review_ids'] ) : array();
		$visible_ids = $this->sanitize_review_ids( $visible_ids );
		$hidden_ids  = array();

		foreach ( $data['reviews'] as $review ) {
			if ( ! is_array( $review ) || empty( $review['reviewId'] ) ) {
				continue;
			}

			$review_id = sanitize_text_field( (string) $review['reviewId'] );

			if ( ! in_array( $review_id, $visible_ids, true ) ) {
				$hidden_ids[] = $review_id;
			}
		}

		$settings['hidden_review_ids'] = array_values( array_unique( $hidden_ids ) );

		update_option( CSGREW_SETTINGS_OPTION, wp_parse_args( $settings, csgrew_default_settings() ), false );
	}

	/**
	 * Render cached reviews table with visibility controls.
	 *
	 * @param array<string,mixed> $data     Public cache data.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_reviews_manager( array $data, array $settings ): void {
		$reviews    = isset( $data['reviews'] ) && is_array( $data['reviews'] ) ? $data['reviews'] : array();
		$hidden_ids = $this->sanitize_review_ids( $settings['hidden_review_ids'] ?? array() );

		?>
		<h2><?php esc_html_e( 'Recensioni salvate', 'chef-studio-google-reviews' ); ?></h2>

		<?php if ( empty( $reviews ) ) : ?>
			<p><?php esc_html_e( 'Nessuna recensione in cache. Configura le credenziali e clicca "Aggiorna recensioni ora".', 'chef-studio-google-reviews' ); ?></p>
			<?php return; ?>
		<?php endif; ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'csgrew_save_review_visibility', 'csgrew_nonce' ); ?>
			<input type="hidden" name="csgrew_action" value="save_review_visibility">
			<input type="hidden" name="csgrew_redirect_tab" value="reviews">

			<table class="widefat striped" style="max-width: 1280px;">
				<thead>
					<tr>
						<th scope="col" style="width: 90px;"><?php esc_html_e( 'Mostra', 'chef-studio-google-reviews' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Autore', 'chef-studio-google-reviews' ); ?></th>
						<th scope="col" style="width: 90px;"><?php esc_html_e( 'Rating', 'chef-studio-google-reviews' ); ?></th>
						<th scope="col" style="width: 150px;"><?php esc_html_e( 'Data', 'chef-studio-google-reviews' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Testo recensione', 'chef-studio-google-reviews' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $reviews as $review ) : ?>
						<?php
						if ( ! is_array( $review ) || empty( $review['reviewId'] ) ) {
							continue;
						}

						$review_id = sanitize_text_field( (string) $review['reviewId'] );
						$is_hidden = in_array( $review_id, $hidden_ids, true );
						$author    = ! empty( $review['reviewerName'] ) ? (string) $review['reviewerName'] : __( 'Utente Google', 'chef-studio-google-reviews' );
						$rating    = isset( $review['starRating'] ) ? absint( $review['starRating'] ) : 0;
						$comment   = ! empty( $review['comment'] ) ? (string) $review['comment'] : '';
						$date_raw  = ! empty( $review['createTime'] ) ? (string) $review['createTime'] : (string) ( $review['updateTime'] ?? '' );
						$date      = '' !== $date_raw && false !== strtotime( $date_raw ) ? date_i18n( get_option( 'date_format' ), strtotime( $date_raw ) ) : '';
						?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="visible_review_ids[]" value="<?php echo esc_attr( $review_id ); ?>" <?php checked( ! $is_hidden ); ?>>
									<?php esc_html_e( 'Sì', 'chef-studio-google-reviews' ); ?>
								</label>
							</td>
							<td>
								<strong><?php echo esc_html( $author ); ?></strong><br>
								<code style="font-size: 11px;"><?php echo esc_html( $review_id ); ?></code>
							</td>
							<td><?php echo esc_html( str_repeat( '★', max( 0, min( 5, $rating ) ) ) ); ?></td>
							<td><?php echo esc_html( $date ); ?></td>
							<td><?php echo esc_html( wp_html_excerpt( $comment, 220, '...' ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p class="description">
				<?php esc_html_e( 'Le recensioni non selezionate non vengono mostrate nello shortcode e non entrano nel JSON-LD delle recensioni mostrate.', 'chef-studio-google-reviews' ); ?>
			</p>

			<?php submit_button( __( 'Salva visibilità recensioni', 'chef-studio-google-reviews' ), 'secondary' ); ?>
		</form>
		<?php
	}

	/**
	 * Render setup guide for Google credentials and Business Profile IDs.
	 *
	 * @return void
	 */
	private function render_setup_guide(): void {
		?>
		<div class="card" style="max-width: 980px; margin-top: 16px;">
			<h2><?php esc_html_e( 'Guida configurazione Google', 'chef-studio-google-reviews' ); ?></h2>
			<p>
				<?php esc_html_e( 'Per collegare il plugin servono un progetto Google Cloud, un client OAuth 2.0, un refresh token con scope Business Profile e gli ID account/location del profilo attività.', 'chef-studio-google-reviews' ); ?>
			</p>

			<details open>
				<summary><strong><?php esc_html_e( '1. Prepara Google Cloud e OAuth', 'chef-studio-google-reviews' ); ?></strong></summary>
				<ol>
					<li><?php esc_html_e( 'Apri Google Cloud Console con lo stesso account che gestisce il Google Business Profile, oppure con un account che ha accesso manager/proprietario.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Crea o seleziona un progetto Google Cloud.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Abilita le API Business Profile necessarie: Account Management, Business Information e la API usata per leggere le recensioni.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Configura la schermata consenso OAuth. Per uso stabile evita lo stato External/Testing: i refresh token di app esterne in testing possono scadere dopo 7 giorni.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Crea un OAuth Client ID di tipo Web application.', 'chef-studio-google-reviews' ); ?></li>
					<li>
						<?php esc_html_e( 'Se usi OAuth 2.0 Playground per generare il token, aggiungi questo redirect URI autorizzato nel client OAuth:', 'chef-studio-google-reviews' ); ?>
						<code>https://developers.google.com/oauthplayground</code>
					</li>
					<li><?php esc_html_e( 'Copia Client ID e Client Secret nei campi del plugin.', 'chef-studio-google-reviews' ); ?></li>
				</ol>
			</details>

			<details>
				<summary><strong><?php esc_html_e( '2. Genera il Refresh Token', 'chef-studio-google-reviews' ); ?></strong></summary>
				<ol>
					<li>
						<?php esc_html_e( 'Apri OAuth 2.0 Playground:', 'chef-studio-google-reviews' ); ?>
						<a href="<?php echo esc_url( 'https://developers.google.com/oauthplayground' ); ?>" target="_blank" rel="noopener noreferrer">developers.google.com/oauthplayground</a>
					</li>
					<li><?php esc_html_e( 'Apri le impostazioni del Playground e abilita "Use your own OAuth credentials". Inserisci Client ID e Client Secret del tuo progetto.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Nel campo scope inserisci esattamente:', 'chef-studio-google-reviews' ); ?> <code>https://www.googleapis.com/auth/business.manage</code></li>
					<li><?php esc_html_e( 'Clicca "Authorize APIs" e autorizza con l account che gestisce il profilo attività.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Clicca "Exchange authorization code for tokens". Copia il Refresh Token nel plugin.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Non condividere il refresh token: permette al plugin di ottenere access token temporanei senza nuovo login.', 'chef-studio-google-reviews' ); ?></li>
				</ol>
			</details>

			<details>
				<summary><strong><?php esc_html_e( '3. Trova Account ID e Location ID', 'chef-studio-google-reviews' ); ?></strong></summary>
				<p><?php esc_html_e( 'Nel Playground puoi usare l access token appena generato per fare queste richieste di prova.', 'chef-studio-google-reviews' ); ?></p>

				<p><strong><?php esc_html_e( 'Account ID', 'chef-studio-google-reviews' ); ?></strong></p>
				<pre style="white-space: pre-wrap;"><code>GET https://mybusinessaccountmanagement.googleapis.com/v1/accounts</code></pre>
				<p><?php esc_html_e( 'Nella risposta cerca il campo "name", ad esempio "accounts/1234567890". Nel plugin puoi incollare tutto il valore oppure solo "1234567890".', 'chef-studio-google-reviews' ); ?></p>

				<p><strong><?php esc_html_e( 'Location ID', 'chef-studio-google-reviews' ); ?></strong></p>
				<pre style="white-space: pre-wrap;"><code>GET https://mybusinessbusinessinformation.googleapis.com/v1/accounts/ACCOUNT_ID/locations?readMask=name,title</code></pre>
				<p><?php esc_html_e( 'Sostituisci ACCOUNT_ID con il valore trovato prima. Nella risposta cerca la location corretta: il campo "name" contiene "locations/LOCATION_ID". Nel plugin puoi incollare tutto il valore oppure solo LOCATION_ID.', 'chef-studio-google-reviews' ); ?></p>
			</details>

			<details>
				<summary><strong><?php esc_html_e( '4. Configura e aggiorna il plugin', 'chef-studio-google-reviews' ); ?></strong></summary>
				<ol>
					<li><?php esc_html_e( 'Compila Client ID, Client Secret, Refresh Token, Account ID e Location ID.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Inserisci nome attività, URL profilo Google Business e, se vuoi, logo e testo sorgente recensioni.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Salva le impostazioni.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Clicca "Aggiorna recensioni ora". Se Google risponde correttamente, lo stato cache mostrerà data aggiornamento, numero recensioni e rating medio.', 'chef-studio-google-reviews' ); ?></li>
					<li><?php esc_html_e( 'Inserisci lo shortcode nella pagina desiderata:', 'chef-studio-google-reviews' ); ?> <code>[chef_google_reviews limit="8" layout="slider" schema="true" summary="true"]</code></li>
				</ol>
			</details>

			<p>
				<strong><?php esc_html_e( 'Documentazione ufficiale:', 'chef-studio-google-reviews' ); ?></strong>
				<a href="<?php echo esc_url( 'https://developers.google.com/identity/protocols/oauth2' ); ?>" target="_blank" rel="noopener noreferrer">OAuth 2.0 Google</a>,
				<a href="<?php echo esc_url( 'https://developers.google.com/my-business/reference/accountmanagement/rest/v1/accounts/list' ); ?>" target="_blank" rel="noopener noreferrer">accounts.list</a>,
				<a href="<?php echo esc_url( 'https://developers.google.com/my-business/reference/businessinformation/rest/v1/accounts.locations/list' ); ?>" target="_blank" rel="noopener noreferrer">locations.list</a>,
				<a href="<?php echo esc_url( 'https://developers.google.com/my-business/reference/rest/v4/accounts.locations.reviews/list' ); ?>" target="_blank" rel="noopener noreferrer">reviews.list</a>.
			</p>
		</div>
		<?php
	}

	/**
	 * Render a text field row.
	 *
	 * @param string $key         Setting key.
	 * @param string $label       Label.
	 * @param string $value       Value.
	 * @param string $type        Input type.
	 * @param string $description Description.
	 * @return void
	 */
	private function render_text_row( string $key, string $label, string $value, string $type = 'text', string $description = '' ): void {
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>" class="regular-text" value="<?php echo esc_attr( $value ); ?>">
				<?php if ( '' !== $description ) : ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render logo setting with WordPress media picker support.
	 *
	 * @param string $value Stored logo value.
	 * @return void
	 */
	private function render_logo_row( string $value ): void {
		$logo_url = csgrew_resolve_logo_url( $value );
		?>
		<tr>
			<th scope="row"><label for="logo"><?php esc_html_e( 'Logo attività', 'chef-studio-google-reviews' ); ?></label></th>
			<td>
				<input name="logo" id="logo" type="text" class="regular-text csgrew-logo-field" value="<?php echo esc_attr( $value ); ?>">
				<button type="button" class="button csgrew-select-logo"><?php esc_html_e( 'Scegli dalla libreria media', 'chef-studio-google-reviews' ); ?></button>
				<button type="button" class="button csgrew-clear-logo"><?php esc_html_e( 'Rimuovi', 'chef-studio-google-reviews' ); ?></button>
				<p class="description"><?php esc_html_e( 'Puoi salvare un media ID WordPress oppure un URL immagine. Il selettore media salva automaticamente l’ID.', 'chef-studio-google-reviews' ); ?></p>
				<div class="csgrew-logo-preview" style="margin-top:10px;">
					<?php if ( '' !== $logo_url ) : ?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="" style="height:64px;width:64px;object-fit:cover;border:1px solid #dcdcde;border-radius:50%;background:#fff;">
					<?php endif; ?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render a sensitive field without printing the stored value.
	 *
	 * @param string $key   Setting key.
	 * @param string $label Label.
	 * @param string $value Stored value.
	 * @return void
	 */
	private function render_sensitive_row( string $key, string $label, string $value ): void {
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" type="password" class="regular-text" value="" autocomplete="off" placeholder="<?php esc_attr_e( 'Lascia vuoto per mantenere il valore salvato', 'chef-studio-google-reviews' ); ?>">
				<p class="description">
					<?php esc_html_e( 'Valore salvato:', 'chef-studio-google-reviews' ); ?>
					<code><?php echo esc_html( $this->mask_sensitive( $value ) ); ?></code>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render a textarea field row.
	 *
	 * @param string $key   Setting key.
	 * @param string $label Label.
	 * @param string $value Value.
	 * @return void
	 */
	private function render_textarea_row( string $key, string $label, string $value ): void {
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<textarea name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" rows="3" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render a number field row.
	 *
	 * @param string $key   Setting key.
	 * @param string $label Label.
	 * @param int    $value Value.
	 * @param int    $min   Min.
	 * @param int    $max   Max.
	 * @return void
	 */
	private function render_number_row( string $key, string $label, int $value, int $min, int $max ): void {
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<input name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" type="number" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="small-text">
			</td>
		</tr>
		<?php
	}

	/**
	 * Render a checkbox row.
	 *
	 * @param string $key     Setting key.
	 * @param string $label   Label.
	 * @param bool   $checked Checked.
	 * @return void
	 */
	private function render_checkbox_row( string $key, string $label, bool $checked ): void {
		?>
		<tr>
			<th scope="row"><?php echo esc_html( $label ); ?></th>
			<td>
				<label>
					<input name="<?php echo esc_attr( $key ); ?>" type="checkbox" value="1" <?php checked( $checked ); ?>>
					<?php esc_html_e( 'Attivo', 'chef-studio-google-reviews' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render WP_DEBUG payload box.
	 *
	 * @param array<string,mixed> $data Cache data.
	 * @return void
	 */
	private function render_debug_box( array $data ): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$debug = $this->redact_sensitive_debug_data( $data['raw_debug'] ?? array() );
		?>
		<h2><?php esc_html_e( 'Debug', 'chef-studio-google-reviews' ); ?></h2>
		<details style="max-width: 980px;">
			<summary><?php esc_html_e( 'Mostra ultimo payload API salvato', 'chef-studio-google-reviews' ); ?></summary>
			<pre style="white-space: pre-wrap; background: #fff; border: 1px solid #ccd0d4; padding: 16px; overflow: auto;"><?php echo esc_html( wp_json_encode( $debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ?: '' ); ?></pre>
		</details>
		<?php
	}

	/**
	 * Redirect to settings page with a status code.
	 *
	 * @param string $notice Notice code.
	 * @return void
	 */
	private function redirect_with_notice( string $notice ): void {
		$raw_tab = isset( $_POST['csgrew_redirect_tab'] ) ? wp_unslash( $_POST['csgrew_redirect_tab'] ) : 'settings';
		$tab     = is_array( $raw_tab ) ? 'settings' : $this->sanitize_tab( sanitize_key( $raw_tab ) );
		$args    = array(
			'page'          => self::PAGE_SLUG,
			'csgrew_notice' => $notice,
		);

		if ( 'settings' !== $tab ) {
			$args['tab'] = $tab;
		}

		wp_safe_redirect(
			add_query_arg(
				$args,
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Get sanitized text from POST.
	 *
	 * @param string $key     Key.
	 * @param string $default Default.
	 * @return string
	 */
	private function posted_text( string $key, string $default = '' ): string {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $default;
		}

		$value = wp_unslash( $_POST[ $key ] );

		return is_array( $value ) ? $default : sanitize_text_field( $value );
	}

	/**
	 * Get sanitized textarea from POST.
	 *
	 * @param string $key     Key.
	 * @param string $default Default.
	 * @return string
	 */
	private function posted_textarea( string $key, string $default = '' ): string {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $default;
		}

		$value = wp_unslash( $_POST[ $key ] );

		return is_array( $value ) ? $default : sanitize_textarea_field( $value );
	}

	/**
	 * Get sensitive POST field, preserving old value when blank.
	 *
	 * @param string $key     Key.
	 * @param string $current Current value.
	 * @return string
	 */
	private function posted_sensitive( string $key, string $current ): string {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $current;
		}

		$value = wp_unslash( $_POST[ $key ] );

		if ( is_array( $value ) ) {
			return $current;
		}

		$value = sanitize_text_field( $value );

		return '' === $value ? $current : $value;
	}

	/**
	 * Get absint from POST.
	 *
	 * @param string $key     Key.
	 * @param int    $default Default.
	 * @return int
	 */
	private function posted_absint( string $key, int $default ): int {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $default;
		}

		$value = wp_unslash( $_POST[ $key ] );

		return is_array( $value ) ? $default : absint( $value );
	}

	/**
	 * Get checkbox as integer bool.
	 *
	 * @param string $key Key.
	 * @return int
	 */
	private function posted_checkbox( string $key ): int {
		return isset( $_POST[ $key ] ) ? 1 : 0;
	}

	/**
	 * Sanitize a list of review IDs.
	 *
	 * @param mixed $ids Raw IDs.
	 * @return array<int,string>
	 */
	private function sanitize_review_ids( mixed $ids ): array {
		if ( ! is_array( $ids ) ) {
			return array();
		}

		$clean = array();

		foreach ( $ids as $id ) {
			if ( is_array( $id ) ) {
				continue;
			}

			$id = sanitize_text_field( (string) $id );

			if ( '' !== $id ) {
				$clean[] = $id;
			}
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * Get allowed update frequency.
	 *
	 * @return string
	 */
	private function posted_frequency(): string {
		$frequency = $this->posted_text( 'update_frequency', 'weekly' );

		return in_array( $frequency, array( 'manual', 'daily', 'weekly' ), true ) ? $frequency : 'weekly';
	}

	/**
	 * Mask a stored sensitive value.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private function mask_sensitive( string $value ): string {
		if ( '' === $value ) {
			return __( 'Non configurato', 'chef-studio-google-reviews' );
		}

		$visible = substr( $value, -4 );

		if ( strlen( $value ) <= 4 ) {
			return '********';
		}

		return str_repeat( '*', min( 16, max( 8, strlen( $value ) - 4 ) ) ) . $visible;
	}

	/**
	 * Recursively remove sensitive-looking keys before debug output.
	 *
	 * @param mixed $value Raw value.
	 * @return mixed
	 */
	private function redact_sensitive_debug_data( mixed $value ): mixed {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$redacted = array();

		foreach ( $value as $key => $item ) {
			$key_string = is_string( $key ) ? strtolower( $key ) : '';

			if ( in_array( $key_string, array( 'client_secret', 'refresh_token', 'access_token', 'authorization' ), true ) ) {
				$redacted[ $key ] = '[redacted]';
				continue;
			}

			$redacted[ $key ] = $this->redact_sensitive_debug_data( $item );
		}

		return $redacted;
	}
}
