<?php
/**
 * Plugin settings page.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and renders plugin settings.
 */
final class Settings {

	/**
	 * Settings page slug.
	 */
	const PAGE_SLUG = 'csfaq-settings';

	/**
	 * Registers hooks.
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'register_wpml_strings' ), 20 );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CSFAQ_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Registers the settings schema.
	 */
	public function register_settings() {
		register_setting(
			'csfaq_settings',
			'csfaq_enabled_post_types',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_enabled_post_types' ),
				'default'           => array( 'post' ),
			)
		);

		register_setting(
			'csfaq_settings',
			'csfaq_post_type_frontend_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_post_type_frontend_settings' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'csfaq_post_types_section',
			__( 'Contenuti abilitati', plugin_textdomain() ),
			array( $this, 'render_post_types_intro' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'csfaq_enabled_post_types',
			__( 'Post type con FAQ attive', plugin_textdomain() ),
			array( $this, 'render_post_types_field' ),
			self::PAGE_SLUG,
			'csfaq_post_types_section'
		);

		register_setting(
			'csfaq_settings',
			'csfaq_frontend_output_mode',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_output_mode' ),
				'default'           => 'content',
			)
		);

		add_settings_section(
			'csfaq_frontend_output_section',
			__( 'Output FAQ nel frontend', plugin_textdomain() ),
			array( $this, 'render_section_intro' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'csfaq_frontend_output_mode',
			__( 'Configurazione per post type', plugin_textdomain() ),
			array( $this, 'render_output_mode_field' ),
			self::PAGE_SLUG,
			'csfaq_frontend_output_section'
		);

		add_settings_field(
			'csfaq_frontend_manual_code',
			__( 'Codice da copiare', plugin_textdomain() ),
			array( $this, 'render_manual_code_field' ),
			self::PAGE_SLUG,
			'csfaq_frontend_output_section'
		);
	}

	/**
	 * Registers the settings page under Settings.
	 */
	public function register_menu() {
		add_options_page(
			__( 'Chef Studio FAQ SEO', plugin_textdomain() ),
			__( 'FAQ SEO', plugin_textdomain() ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Adds a shortcut in the plugins list.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_settings_link( $links ) {
		$settings_url = admin_url( 'options-general.php?page=' . self::PAGE_SLUG );

		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( $settings_url ),
				esc_html__( 'Impostazioni', plugin_textdomain() )
			)
		);

		return $links;
	}

	/**
	 * Sanitizes the output mode option.
	 *
	 * @param string $value Raw option value.
	 */
	public function sanitize_output_mode( $value ) {
		$allowed = array( 'content', 'shortcode', 'php_template' );
		$value   = sanitize_key( (string) $value );

		return in_array( $value, $allowed, true ) ? $value : 'content';
	}

	/**
	 * Sanitizes the enabled post types option.
	 *
	 * @param mixed $value Raw option value.
	 * @return string[]
	 */
	public function sanitize_enabled_post_types( $value ) {
		$value = is_array( $value ) ? $value : array();
		$value = array_unique( array_map( 'sanitize_key', $value ) );

		$allowed = array_keys( get_available_post_type_objects() );

		$value = array_values(
			array_filter(
				$value,
				static function ( $post_type ) use ( $allowed ) {
					return in_array( $post_type, $allowed, true );
				}
			)
		);

		return ! empty( $value ) ? $value : array( 'post' );
	}

	/**
	 * Sanitizes the per-post-type frontend settings.
	 *
	 * @param mixed $value Raw option value.
	 * @return array<string, array<string, string>>
	 */
	public function sanitize_post_type_frontend_settings( $value ) {
		$value          = is_array( $value ) ? $value : array();
		$available      = get_available_post_type_objects();
		$legacy_mode    = get_default_frontend_output_mode();
		$default_title  = get_default_frontend_section_title();
		$sanitized_data = array();

		foreach ( $available as $post_type => $post_type_object ) {
			$item = isset( $value[ $post_type ] ) && is_array( $value[ $post_type ] ) ? $value[ $post_type ] : array();
			$default_mode = 'post' === $post_type ? $legacy_mode : 'content';

			$title = isset( $item['title'] ) ? sanitize_text_field( (string) $item['title'] ) : '';
			$mode  = isset( $item['output_mode'] ) ? $this->sanitize_output_mode( $item['output_mode'] ) : $default_mode;

			$sanitized_data[ $post_type ] = array(
				'title'       => '' !== $title ? $title : $default_title,
				'output_mode' => $mode,
			);
		}

		register_wpml_section_title_strings( $sanitized_data );

		return $sanitized_data;
	}

	/**
	 * Registers CPT FAQ titles in WPML String Translation.
	 */
	public function register_wpml_strings() {
		register_wpml_section_title_strings();
	}

	/**
	 * Renders the settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap csfaq-settings-page">
			<h1><?php echo esc_html__( 'Chef Studio FAQ SEO', plugin_textdomain() ); ?></h1>
			<p><?php echo esc_html__( 'Scegli come visualizzare le FAQ nel frontend e copia il codice pronto se preferisci inserirle manualmente nel template del tema.', plugin_textdomain() ); ?></p>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'csfaq_settings' );
				do_settings_sections( self::PAGE_SLUG );
				submit_button( __( 'Salva impostazioni', plugin_textdomain() ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Renders the section intro text.
	 */
	public function render_section_intro() {
		echo '<p>' . esc_html__(
			'Scegli per ogni post type come visualizzare le FAQ nel frontend. Puoi pubblicarle automaticamente nel contenuto, inserirle in un altro contenuto tramite shortcode oppure stamparle direttamente nel template PHP del tema.',
			plugin_textdomain()
		) . '</p>';
	}

	/**
	 * Renders the post types section intro text.
	 */
	public function render_post_types_intro() {
		echo '<p>' . esc_html__(
			'Seleziona i post type in cui vuoi usare il repeater FAQ, il render frontend e il JSON-LD FAQPage. Questa impostazione vale per tutto il plugin.',
			plugin_textdomain()
		) . '</p>';
	}

	/**
	 * Renders the enabled post types field.
	 */
	public function render_post_types_field() {
		$current_post_types = get_enabled_post_types();
		$available_types    = get_available_post_type_objects();
		?>
		<fieldset class="csfaq-settings-post-types">
			<?php foreach ( $available_types as $post_type ) : ?>
				<label>
					<input type="checkbox" name="csfaq_enabled_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $current_post_types, true ) ); ?> />
					<strong><?php echo esc_html( $post_type->labels->singular_name ); ?></strong>
					<code><?php echo esc_html( $post_type->name ); ?></code>
				</label>
			<?php endforeach; ?>
			<p class="description">
				<?php echo esc_html__( 'Se non selezioni nulla, il plugin userà comunque almeno il post type "post".', plugin_textdomain() ); ?>
			</p>
		</fieldset>
		<?php
	}

	/**
	 * Renders the output mode field.
	 */
	public function render_output_mode_field() {
		$available_types = get_available_post_type_objects();
		?>
		<div class="csfaq-settings-output-table-wrap">
			<table class="widefat striped csfaq-settings-output-table">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Post type', plugin_textdomain() ); ?></th>
						<th><?php echo esc_html__( 'Titolo sezione FAQ', plugin_textdomain() ); ?></th>
						<th><?php echo esc_html__( 'Modalità output', plugin_textdomain() ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $available_types as $post_type => $post_type_object ) : ?>
						<?php $settings = get_post_type_frontend_settings( $post_type ); ?>
						<tr>
							<td>
								<strong><?php echo esc_html( $post_type_object->labels->singular_name ); ?></strong>
								<div><code><?php echo esc_html( $post_type ); ?></code></div>
							</td>
							<td>
								<input type="text" class="regular-text" name="csfaq_post_type_frontend_settings[<?php echo esc_attr( $post_type ); ?>][title]" value="<?php echo esc_attr( $settings['title'] ); ?>" />
							</td>
							<td>
								<select name="csfaq_post_type_frontend_settings[<?php echo esc_attr( $post_type ); ?>][output_mode]">
									<option value="content" <?php selected( $settings['output_mode'], 'content' ); ?>><?php echo esc_html__( 'Nel contenuto', plugin_textdomain() ); ?></option>
									<option value="shortcode" <?php selected( $settings['output_mode'], 'shortcode' ); ?>><?php echo esc_html__( 'In un altro contenuto via shortcode', plugin_textdomain() ); ?></option>
									<option value="php_template" <?php selected( $settings['output_mode'], 'php_template' ); ?>><?php echo esc_html__( 'Nel template PHP', plugin_textdomain() ); ?></option>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="description">
				<?php echo esc_html__( 'Se scegli "In un altro contenuto via shortcode", inserisci lo shortcode in un campo o contenuto del tema che supporti do_shortcode(). Se scegli "Nel template PHP", usa lo snippet qui sotto.', plugin_textdomain() ); ?>
			</p>
			<?php if ( has_filter( 'wpml_translate_single_string' ) || has_action( 'wpml_register_single_string' ) ) : ?>
				<p class="description">
					<?php echo esc_html__( 'WPML rilevato: i titoli sezione FAQ vengono registrati in String Translation nel contesto "Chef Studio FAQ SEO".', plugin_textdomain() ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renders the copyable template snippet field.
	 */
	public function render_manual_code_field() {
		$php_snippet       = get_manual_render_snippet();
		$shortcode_snippet = get_shortcode_render_snippet();
		?>
		<div class="csfaq-manual-code">
			<p class="description">
				<?php echo esc_html__( 'Qui trovi sia lo shortcode da inserire in un contenuto diverso, sia lo snippet PHP da usare nel tema. In modalità manuale il plugin presuppone che uno dei due venga davvero inserito nel punto desiderato.', plugin_textdomain() ); ?>
			</p>
			<p><strong><?php echo esc_html__( 'Shortcode', plugin_textdomain() ); ?></strong></p>
			<textarea id="csfaq-shortcode-render-code" class="large-text code" rows="2" readonly><?php echo esc_textarea( $shortcode_snippet ); ?></textarea>
			<p>
				<button type="button" class="button button-secondary" data-csfaq-copy-target="csfaq-shortcode-render-code"><?php echo esc_html__( 'Copia shortcode', plugin_textdomain() ); ?></button>
				<span class="description csfaq-copy-feedback" aria-live="polite"></span>
			</p>
			<p><strong><?php echo esc_html__( 'Snippet PHP', plugin_textdomain() ); ?></strong></p>
			<textarea id="csfaq-manual-render-code" class="large-text code" rows="5" readonly><?php echo esc_textarea( $php_snippet ); ?></textarea>
			<p>
				<button type="button" class="button button-secondary" data-csfaq-copy-target="csfaq-manual-render-code"><?php echo esc_html__( 'Copia codice PHP', plugin_textdomain() ); ?></button>
				<span class="description csfaq-copy-feedback" aria-live="polite"></span>
			</p>
			<hr />
			<h3><?php echo esc_html__( 'Traduzioni WPML', plugin_textdomain() ); ?></h3>
			<p class="description">
				<?php echo esc_html__( 'Per tradurre i titoli delle FAQ per ciascun post type, usa WPML String Translation e cerca le stringhe nel contesto "Chef Studio FAQ SEO".', plugin_textdomain() ); ?>
			</p>
			<p class="description">
				<?php echo esc_html__( 'Per tradurre le FAQ dei singoli contenuti, apri la traduzione del relativo articolo o CPT nella lingua desiderata e inserisci o modifica lì le FAQ di quella versione.', plugin_textdomain() ); ?>
			</p>
		</div>
		<?php
	}
}
