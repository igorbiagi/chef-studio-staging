<?php
/**
 * Shared helpers.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the plugin text domain.
 */
function plugin_textdomain() {
	return 'chef-studio-faq';
}

/**
 * Returns the post type objects that can use FAQs.
 *
 * @return array<string, \WP_Post_Type>
 */
function get_available_post_type_objects() {
	$post_types = get_post_types(
		array(
			'show_ui' => true,
		),
		'objects'
	);

	if ( ! is_array( $post_types ) ) {
		return array();
	}

	$post_types = array_filter(
		$post_types,
		static function ( $post_type ) {
			if ( ! $post_type instanceof \WP_Post_Type ) {
				return false;
			}

			if ( 'attachment' === $post_type->name ) {
				return false;
			}

			return ! empty( $post_type->show_ui );
		}
	);

	uasort(
		$post_types,
		static function ( $first, $second ) {
			return strcasecmp( (string) $first->labels->singular_name, (string) $second->labels->singular_name );
		}
	);

	return $post_types;
}

/**
 * Returns the default frontend output mode used as fallback.
 */
function get_default_frontend_output_mode() {
	$mode    = get_option( 'csfaq_frontend_output_mode', 'content' );
	$allowed = array( 'content', 'shortcode', 'php_template' );

	if ( ! in_array( $mode, $allowed, true ) ) {
		$mode = 'content';
	}

	return (string) $mode;
}

/**
 * Returns the default FAQ section title.
 */
function get_default_frontend_section_title() {
	return (string) apply_filters( 'csfaq_default_frontend_section_title', __( 'Domande frequenti', plugin_textdomain() ) );
}

/**
 * Returns the WPML String Translation context used by the plugin.
 */
function get_wpml_string_context() {
	return 'Chef Studio FAQ SEO';
}

/**
 * Returns the WPML String Translation key for one CPT FAQ title.
 *
 * @param string $post_type Post type name.
 */
function get_wpml_section_title_string_name( $post_type ) {
	$post_type = sanitize_key( (string) $post_type );

	return 'faq-section-title-' . ( $post_type ? $post_type : 'default' );
}

/**
 * Registers one FAQ section title in WPML String Translation.
 *
 * @param string $post_type Post type name.
 * @param string $title     Source title.
 */
function register_wpml_section_title_string( $post_type, $title ) {
	$title = sanitize_text_field( (string) $title );

	if ( '' === $title ) {
		return;
	}

	do_action(
		'wpml_register_single_string',
		get_wpml_string_context(),
		get_wpml_section_title_string_name( $post_type ),
		$title
	);
}

/**
 * Registers all configured CPT FAQ titles in WPML String Translation.
 *
 * @param array<string, array<string, string>>|null $settings Optional settings map.
 */
function register_wpml_section_title_strings( $settings = null ) {
	$settings        = is_array( $settings ) ? $settings : get_option( 'csfaq_post_type_frontend_settings', array() );
	$available_types = get_available_post_type_objects();
	$default_title   = get_default_frontend_section_title();

	foreach ( $available_types as $post_type => $post_type_object ) {
		$title = isset( $settings[ $post_type ]['title'] ) ? sanitize_text_field( (string) $settings[ $post_type ]['title'] ) : $default_title;

		if ( '' === $title ) {
			$title = $default_title;
		}

		register_wpml_section_title_string( $post_type, $title );
	}
}

/**
 * Returns a FAQ section title translated via WPML String Translation when available.
 *
 * @param string $post_type Post type name.
 * @param string $title     Source title.
 */
function translate_wpml_section_title( $post_type, $title ) {
	$title = sanitize_text_field( (string) $title );

	if ( '' === $title ) {
		return '';
	}

	return (string) apply_filters(
		'wpml_translate_single_string',
		$title,
		get_wpml_string_context(),
		get_wpml_section_title_string_name( $post_type )
	);
}

/**
 * Returns the configured frontend settings for a post type.
 *
 * @param string $post_type Optional post type.
 * @return array{title:string,output_mode:string}
 */
function get_post_type_frontend_settings( $post_type = '' ) {
	$post_type = $post_type ? sanitize_key( $post_type ) : get_post_type( get_current_singular_post_id() );
	$all       = get_option( 'csfaq_post_type_frontend_settings', array() );
	$settings  = isset( $all[ $post_type ] ) && is_array( $all[ $post_type ] ) ? $all[ $post_type ] : array();
	$default_output_mode = 'post' === $post_type ? get_default_frontend_output_mode() : 'content';

	$title       = isset( $settings['title'] ) ? sanitize_text_field( (string) $settings['title'] ) : '';
	$output_mode = isset( $settings['output_mode'] ) ? sanitize_key( $settings['output_mode'] ) : $default_output_mode;
	$allowed     = array( 'content', 'shortcode', 'php_template' );

	if ( ! in_array( $output_mode, $allowed, true ) ) {
		$output_mode = $default_output_mode;
	}

	if ( '' === $title ) {
		$title = get_default_frontend_section_title();
	}

	register_wpml_section_title_string( $post_type, $title );
	$title = translate_wpml_section_title( $post_type, $title );

	$settings = array(
		'title'       => $title,
		'output_mode' => $output_mode,
	);

	return (array) apply_filters( 'csfaq_post_type_frontend_settings', $settings, $post_type );
}

/**
 * Returns the frontend output mode for the requested post type.
 *
 * Supported values:
 * - content: automatic append/prepend via the_content
 * - shortcode: manual output via shortcode in another content field
 * - php_template: manual output in the theme template
 *
 * @param string $post_type Optional post type.
 */
function get_frontend_output_mode( $post_type = '' ) {
	$settings = get_post_type_frontend_settings( $post_type );

	return (string) apply_filters( 'csfaq_frontend_output_mode', $settings['output_mode'], $post_type, $settings );
}

/**
 * Returns the frontend FAQ section title for a post type.
 *
 * @param string $post_type Optional post type.
 * @param int    $post_id   Optional post ID.
 * @param array  $faq_items Optional FAQ rows.
 */
function get_frontend_section_title( $post_type = '', $post_id = 0, $faq_items = array() ) {
	$settings = get_post_type_frontend_settings( $post_type );

	return (string) apply_filters( 'csfaq_frontend_section_title', $settings['title'], $post_id, $faq_items, $post_type, $settings );
}

/**
 * Returns true when automatic the_content output is enabled.
 *
 * @param int $post_id Optional post ID.
 */
function is_automatic_frontend_render_enabled( $post_id = 0 ) {
	$post_id = absint( $post_id );
	$post_type = $post_id ? get_post_type( $post_id ) : '';

	if ( 'content' !== get_frontend_output_mode( $post_type ) ) {
		return false;
	}

	if ( ! apply_filters( 'csfaq_enable_frontend_render', true, $post_id ) ) {
		return false;
	}

	return 'disabled' !== apply_filters( 'csfaq_frontend_position', 'after_content', $post_id );
}

/**
 * Returns the copy/paste-ready template snippet for manual frontend output.
 */
function get_manual_render_snippet() {
	return <<<'PHP'
<?php if ( function_exists( 'csfaq_the_faq_markup' ) ) : ?>
	<?php csfaq_the_faq_markup( get_the_ID() ); ?>
<?php endif; ?>
PHP;
}

/**
 * Returns the copy/paste-ready shortcode snippet for manual output.
 */
function get_shortcode_render_snippet() {
	return '[csfaq]';
}

/**
 * Returns flexible builder rows for a post when available.
 *
 * @param int $post_id Post ID.
 * @return array<int, array<string, mixed>>
 */
function get_flexible_layout_rows( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}

	return get_flexible_layout_rows_from_post_meta( $post_id );
}

/**
 * Returns only the flexible rows relevant to FAQ schema from raw post meta.
 *
 * Reading the formatted flexible field would force ACF to format every sub-field
 * in every layout, including unrelated or dynamically injected fields.
 *
 * @param int $post_id Post ID.
 * @return array<int, array<string, mixed>>
 */
function get_flexible_layout_rows_from_post_meta( $post_id ) {
	$layout_order = get_flexible_layout_order_from_post_meta( $post_id );

	if ( empty( $layout_order ) ) {
		return array();
	}

	$rows             = array();
	$disabled_indexes = get_disabled_flexible_layout_row_indexes( $post_id );

	foreach ( $layout_order as $index => $layout_name ) {
		$index       = absint( $index );
		$layout_name = sanitize_key( (string) $layout_name );

		if ( '' === $layout_name || in_array( $index, $disabled_indexes, true ) ) {
			continue;
		}

		if ( ! is_faq_flexible_layout_name( $layout_name ) && Acf_Fields::FLEX_LAYOUT_REUSABLE_NAME !== $layout_name ) {
			continue;
		}

		$meta_prefix = Acf_Fields::FLEX_FIELD_NAME . '_' . $index;
		$row         = array(
			'acf_fc_layout' => $layout_name,
		);

		if ( is_faq_flexible_layout_name( $layout_name ) ) {
			$row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] = get_flexible_layout_repeater_rows_from_post_meta( $post_id, $meta_prefix );
			$row[ Acf_Fields::FLEX_IMPORT_RAW_NAME ]      = get_post_meta( $post_id, $meta_prefix . '_' . Acf_Fields::FLEX_IMPORT_RAW_NAME, true );
			$row[ Acf_Fields::FLEX_IMPORT_MODE_NAME ]     = get_post_meta( $post_id, $meta_prefix . '_' . Acf_Fields::FLEX_IMPORT_MODE_NAME, true );
		}

		if ( Acf_Fields::FLEX_LAYOUT_CTA_FAQ_NAME === $layout_name ) {
			$row[ Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME ] = get_post_meta( $post_id, $meta_prefix . '_' . Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME, true );
		}

		if ( Acf_Fields::FLEX_LAYOUT_REUSABLE_NAME === $layout_name ) {
			$row[ Acf_Fields::FLEX_LAYOUT_REUSABLE_TARGET_NAME ] = get_post_meta( $post_id, $meta_prefix . '_' . Acf_Fields::FLEX_LAYOUT_REUSABLE_TARGET_NAME, true );
		}

		$rows[ $index ] = $row;
	}

	return $rows;
}

/**
 * Returns the saved flexible layout order from post meta.
 *
 * @param int $post_id Post ID.
 * @return array<int, string>
 */
function get_flexible_layout_order_from_post_meta( $post_id ) {
	$value = get_post_meta( $post_id, Acf_Fields::FLEX_FIELD_NAME, true );

	if ( is_array( $value ) ) {
		$layout_order = array();

		foreach ( $value as $index => $layout_name ) {
			if ( ! is_scalar( $layout_name ) ) {
				continue;
			}

			if ( is_numeric( $index ) ) {
				$layout_order[ absint( $index ) ] = (string) $layout_name;
			} else {
				$layout_order[] = (string) $layout_name;
			}
		}

		return $layout_order;
	}

	$row_count = absint( $value );

	if ( ! $row_count ) {
		return array();
	}

	$layout_order = array();

	for ( $index = 0; $index < $row_count; ++$index ) {
		$layout_name = get_post_meta( $post_id, Acf_Fields::FLEX_FIELD_NAME . '_' . $index . '_acf_fc_layout', true );

		if ( is_scalar( $layout_name ) && '' !== (string) $layout_name ) {
			$layout_order[ $index ] = (string) $layout_name;
		}
	}

	return $layout_order;
}

/**
 * Returns disabled flexible row indexes outside the admin editor.
 *
 * @param int $post_id Post ID.
 * @return int[]
 */
function get_disabled_flexible_layout_row_indexes( $post_id ) {
	if ( function_exists( 'is_admin' ) && is_admin() ) {
		return array();
	}

	$layout_meta = get_post_meta( $post_id, '_' . Acf_Fields::FLEX_FIELD_NAME . '_layout_meta', true );

	if ( ! is_array( $layout_meta ) || empty( $layout_meta['disabled'] ) || ! is_array( $layout_meta['disabled'] ) ) {
		return array();
	}

	return array_map( 'absint', $layout_meta['disabled'] );
}

/**
 * Returns FAQ repeater rows from one flexible layout row prefix.
 *
 * @param int    $post_id     Post ID.
 * @param string $meta_prefix Flexible row meta prefix.
 * @return array<int, array<string, mixed>>
 */
function get_flexible_layout_repeater_rows_from_post_meta( $post_id, $meta_prefix ) {
	$row_count = absint( get_post_meta( $post_id, $meta_prefix . '_' . Acf_Fields::FLEX_LAYOUT_REPEATER_NAME, true ) );

	if ( ! $row_count ) {
		return array();
	}

	$rows = array();

	for ( $index = 0; $index < $row_count; ++$index ) {
		$row_prefix = $meta_prefix . '_' . Acf_Fields::FLEX_LAYOUT_REPEATER_NAME . '_' . $index;

		$rows[] = array(
			Acf_Fields::FLEX_LAYOUT_QUESTION_NAME => get_post_meta( $post_id, $row_prefix . '_' . Acf_Fields::FLEX_LAYOUT_QUESTION_NAME, true ),
			Acf_Fields::FLEX_LAYOUT_ANSWER_NAME   => get_post_meta( $post_id, $row_prefix . '_' . Acf_Fields::FLEX_LAYOUT_ANSWER_NAME, true ),
		);
	}

	return $rows;
}

/**
 * Checks whether a flexible content layout can host FAQ data.
 *
 * @param string $layout_name Flexible layout name.
 */
function is_faq_flexible_layout_name( $layout_name ) {
	$layout_name = sanitize_key( (string) $layout_name );

	return in_array(
		$layout_name,
		array(
			Acf_Fields::FLEX_LAYOUT_FAQ_NAME,
			Acf_Fields::FLEX_LAYOUT_CTA_FAQ_NAME,
		),
		true
	);
}

/**
 * Checks whether a flexible layout row currently represents visible FAQs.
 *
 * @param array<string,mixed> $row Flexible content row.
 */
function is_visible_faq_flexible_layout_row( array $row ) {
	if ( empty( $row['acf_fc_layout'] ) || ! is_string( $row['acf_fc_layout'] ) ) {
		return false;
	}

	if ( Acf_Fields::FLEX_LAYOUT_FAQ_NAME === $row['acf_fc_layout'] ) {
		return true;
	}

	if ( Acf_Fields::FLEX_LAYOUT_CTA_FAQ_NAME !== $row['acf_fc_layout'] ) {
		return false;
	}

	$content_type = isset( $row[ Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME ] ) ? sanitize_text_field( (string) $row[ Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME ] ) : '';

	return Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_FAQ_VALUE === $content_type;
}

/**
 * Normalizes a post object / relationship value to a post ID.
 *
 * @param mixed $value Raw relationship value.
 */
function normalize_related_post_id( $value ) {
	if ( is_numeric( $value ) ) {
		return absint( $value );
	}

	if ( is_object( $value ) && isset( $value->ID ) ) {
		return absint( $value->ID );
	}

	if ( is_array( $value ) ) {
		if ( isset( $value['ID'] ) ) {
			return absint( $value['ID'] );
		}

		if ( 1 === count( $value ) ) {
			$first = reset( $value );

			return normalize_related_post_id( $first );
		}
	}

	return 0;
}

/**
 * Checks whether any ACF build is active.
 */
function is_acf_active() {
	return function_exists( 'acf_add_local_field_group' ) && function_exists( 'update_field' );
}

/**
 * Checks whether ACF Pro is available.
 */
function is_acf_pro_active() {
	if ( ! is_acf_active() ) {
		return false;
	}

	if ( function_exists( 'acf_get_setting' ) ) {
		return (bool) acf_get_setting( 'pro' );
	}

	return false;
}

/**
 * Returns the enabled post types for the FAQ feature.
 *
 * @return string[]
 */
function get_enabled_post_types() {
	$post_types = get_option( 'csfaq_enabled_post_types', array( 'post' ) );

	if ( ! is_array( $post_types ) ) {
		$post_types = array( 'post' );
	}

	/*
	 * Do not validate enabled post types against the currently registered
	 * objects at runtime. Some themes/plugins register CPTs later than ACF's
	 * local field bootstrap, so filtering here would hide legitimate saved
	 * post types (for example on acf/init) and the global FAQ field group
	 * would disappear from their edit screens.
	 */
	$post_types = array_filter(
		array_unique(
			array_map( 'sanitize_key', $post_types )
		)
	);

	if ( empty( $post_types ) ) {
		$post_types = array( 'post' );
	}

	$post_types = apply_filters( 'csfaq_enabled_post_types', array_values( $post_types ) );

	if ( ! is_array( $post_types ) ) {
		$post_types = array( 'post' );
	}

	$post_types = array_filter(
		array_unique(
			array_map( 'sanitize_key', $post_types )
		)
	);

	return ! empty( $post_types ) ? array_values( $post_types ) : array( 'post' );
}

/**
 * Returns the current admin post type when editing a post.
 */
function get_current_admin_post_type() {
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();

		if ( $screen instanceof \WP_Screen && ! empty( $screen->post_type ) ) {
			return sanitize_key( (string) $screen->post_type );
		}
	}

	if ( isset( $_POST['post_type'] ) ) {
		return sanitize_key( wp_unslash( $_POST['post_type'] ) );
	}

	if ( isset( $_GET['post_type'] ) ) {
		return sanitize_key( wp_unslash( $_GET['post_type'] ) );
	}

	if ( isset( $_POST['post_ID'] ) ) {
		$post = get_post( absint( $_POST['post_ID'] ) );

		if ( $post instanceof \WP_Post ) {
			return sanitize_key( $post->post_type );
		}
	}

	if ( isset( $_GET['post'] ) ) {
		$post = get_post( absint( $_GET['post'] ) );

		if ( $post instanceof \WP_Post ) {
			return sanitize_key( $post->post_type );
		}
	}

	return '';
}

/**
 * Checks whether a post type is supported by the plugin.
 *
 * @param string $post_type Post type name.
 */
function is_supported_post_type( $post_type ) {
	return is_string( $post_type ) && in_array( $post_type, get_enabled_post_types(), true );
}

/**
 * Polyfill for array_is_list().
 *
 * @param array $value Array to test.
 */
function is_list_array( array $value ) {
	if ( function_exists( 'array_is_list' ) ) {
		return array_is_list( $value );
	}

	$index = 0;

	foreach ( array_keys( $value ) as $key ) {
		if ( $index !== $key ) {
			return false;
		}

		++$index;
	}

	return true;
}

/**
 * Normalizes line endings.
 *
 * @param string $value Raw text.
 */
function normalize_line_endings( $value ) {
	return str_replace( array( "\r\n", "\r" ), "\n", (string) $value );
}

/**
 * Decodes HTML entities to their plain-text representation.
 *
 * @param string $value Raw text.
 */
function decode_html_entities_to_text( $value ) {
	$value = (string) $value;

	/*
	 * Some editors/import chains store entities more than once, for example
	 * "&amp;#224;" instead of "à". Decode repeatedly until the string stops
	 * changing or we reach a small safety limit.
	 */
	for ( $index = 0; $index < 5; ++$index ) {
		$decoded = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		if ( ! is_string( $decoded ) || $decoded === $value ) {
			break;
		}

		$value = $decoded;
	}

	$value = preg_replace( '/\x{00A0}/u', ' ', $value );

	return is_string( $value ) ? $value : '';
}

/**
 * Recursively decodes HTML entities from strings contained in arrays/scalars.
 *
 * @param mixed $value Raw value.
 * @return mixed
 */
function decode_html_entities_deep( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = decode_html_entities_deep( $item );
		}

		return $value;
	}

	if ( is_string( $value ) ) {
		return decode_html_entities_to_text( $value );
	}

	return $value;
}

/**
 * Converts HTML/WYSIWYG content to clean plain text.
 *
 * @param string $value Raw content.
 */
function sanitize_rich_text_to_plain_text( $value ) {
	$value = decode_html_entities_to_text( normalize_line_endings( (string) $value ) );
	$value = preg_replace( '/<\s*br\s*\/?\s*>/iu', "\n", $value );
	$value = preg_replace( '/<\/\s*(p|div|li|ul|ol|h[1-6]|blockquote)\s*>/iu', "\n", $value );
	$value = wp_strip_all_tags( $value );
	$value = sanitize_textarea_field( $value );
	$value = preg_replace( "/[ \t]+\n/u", "\n", $value );
	$value = preg_replace( "/\n{3,}/u", "\n\n", $value );

	return trim( is_string( $value ) ? $value : '' );
}

/**
 * Normalizes whitespace for text comparisons.
 *
 * @param string $value Raw text.
 */
function normalize_space( $value ) {
	$value = trim( normalize_line_endings( $value ) );
	$value = preg_replace( '/\s+/u', ' ', $value );

	return is_string( $value ) ? $value : '';
}

/**
 * Normalizes a question for duplicate checks.
 *
 * @param string $question Question text.
 */
function normalize_question( $question ) {
	$normalized = normalize_space( $question );

	if ( function_exists( 'mb_strtolower' ) ) {
		return mb_strtolower( $normalized, 'UTF-8' );
	}

	return strtolower( $normalized );
}

/**
 * Sanitizes a FAQ question.
 *
 * @param string $question Raw question text.
 */
function sanitize_question( $question ) {
	$question = decode_html_entities_to_text( (string) $question );

	return trim( sanitize_text_field( $question ) );
}

/**
 * Sanitizes a FAQ answer while preserving meaningful line breaks.
 *
 * @param string $answer Raw answer text.
 */
function sanitize_answer( $answer ) {
	$answer = decode_html_entities_to_text( normalize_line_endings( $answer ) );
	$answer = sanitize_textarea_field( $answer );
	$answer = preg_replace( "/[ \t]+\n/u", "\n", $answer );
	$answer = preg_replace( "/\n{3,}/u", "\n\n", $answer );

	return trim( is_string( $answer ) ? $answer : '' );
}

/**
 * Sanitizes one FAQ item.
 *
 * @param array $item FAQ item data.
 * @return array{question:string,answer:string}
 */
function sanitize_faq_item( array $item ) {
	$question = sanitize_question( get_faq_item_value( $item, Acf_Fields::FIELD_QUESTION_NAME, Acf_Fields::FIELD_QUESTION_KEY ) );
	$answer   = sanitize_answer( get_faq_item_value( $item, Acf_Fields::FIELD_ANSWER_NAME, Acf_Fields::FIELD_ANSWER_KEY ) );

	return array(
		'question' => $question,
		'answer'   => $answer,
	);
}

/**
 * Returns a FAQ row value by field name or ACF field key.
 *
 * @param array  $item      FAQ row data.
 * @param string $name      Expected field name.
 * @param string $field_key Expected ACF field key.
 */
function get_faq_item_value( array $item, $name, $field_key ) {
	if ( array_key_exists( $name, $item ) ) {
		return is_scalar( $item[ $name ] ) ? (string) $item[ $name ] : '';
	}

	if ( array_key_exists( $field_key, $item ) ) {
		return is_scalar( $item[ $field_key ] ) ? (string) $item[ $field_key ] : '';
	}

	return '';
}

/**
 * Checks whether a FAQ item is usable.
 *
 * @param array $item FAQ item data.
 */
function is_valid_faq_item( array $item ) {
	return isset( $item['question'], $item['answer'] ) && '' !== $item['question'] && '' !== $item['answer'];
}

/**
 * Sanitizes and filters a list of FAQ items.
 *
 * @param array $items Raw FAQ items.
 * @return array<int, array{question:string,answer:string}>
 */
function prepare_faq_items( array $items ) {
	$sanitized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$faq = sanitize_faq_item( $item );

		if ( is_valid_faq_item( $faq ) ) {
			$sanitized[] = $faq;
		}
	}

	return $sanitized;
}

/**
 * Returns FAQ items for a post.
 *
 * Uses ACF when available and falls back to raw post meta when ACF Pro is unavailable.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{question:string,answer:string}>
 */
function get_faq_items( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}

	$rows = array();

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( Acf_Fields::FIELD_FAQ_ITEMS_NAME, $post_id );

		if ( is_array( $value ) ) {
			$rows = $value;
		}
	}

	if ( empty( $rows ) ) {
		$rows = get_faq_items_from_post_meta( $post_id );
	}

	return prepare_faq_items( $rows );
}

/**
 * Returns FAQ items gathered from the existing flexible FAQ layouts.
 *
 * Supports reusable blocks rendered through the blocco_riutilizzabile layout.
 *
 * @param int   $post_id Post ID.
 * @param array $visited Already visited post IDs.
 * @return array<int, array{question:string,answer:string}>
 */
function get_flexible_layout_faq_items( $post_id, array $visited = array() ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || isset( $visited[ $post_id ] ) ) {
		return array();
	}

	$visited[ $post_id ] = true;
	$rows                = get_flexible_layout_rows( $post_id );
	$items               = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['acf_fc_layout'] ) ) {
			continue;
		}

		if ( is_visible_faq_flexible_layout_row( $row ) && ! empty( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) && is_array( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) ) {
			foreach ( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] as $faq_row ) {
				if ( ! is_array( $faq_row ) ) {
					continue;
				}

				$faq = array(
					'question' => sanitize_question( sanitize_rich_text_to_plain_text( get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_QUESTION_NAME, '' ) ) ),
					'answer'   => sanitize_answer( sanitize_rich_text_to_plain_text( get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_ANSWER_NAME, '' ) ) ),
				);

				if ( is_valid_faq_item( $faq ) ) {
					$items[] = $faq;
				}
			}
		}

		if ( Acf_Fields::FLEX_LAYOUT_REUSABLE_NAME === $row['acf_fc_layout'] ) {
			$reusable_id = normalize_related_post_id( $row[ Acf_Fields::FLEX_LAYOUT_REUSABLE_TARGET_NAME ] ?? 0 );

			if ( $reusable_id ) {
				$items = array_merge( $items, get_flexible_layout_faq_items( $reusable_id, $visited ) );
			}
		}
	}

	return $items;
}

/**
 * Returns the complete FAQ list for schema output, deduplicated by question.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{question:string,answer:string}>
 */
function get_schema_faq_items( $post_id ) {
	$items          = array_merge( get_faq_items( $post_id ), get_flexible_layout_faq_items( $post_id ) );
	$unique_items   = array();
	$seen_questions = array();

	foreach ( $items as $item ) {
		$key = normalize_question( $item['question'] );

		if ( '' === $key || isset( $seen_questions[ $key ] ) ) {
			continue;
		}

		$seen_questions[ $key ] = true;
		$unique_items[]         = $item;
	}

	return $unique_items;
}

/**
 * Reconstructs repeater rows from raw post meta.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{question:string,answer:string}>
 */
function get_faq_items_from_post_meta( $post_id ) {
	$count = absint( get_post_meta( $post_id, Acf_Fields::FIELD_FAQ_ITEMS_NAME, true ) );

	if ( $count < 1 ) {
		return array();
	}

	$rows = array();

	for ( $index = 0; $index < $count; ++$index ) {
		$rows[] = array(
			'question' => get_post_meta( $post_id, Acf_Fields::FIELD_FAQ_ITEMS_NAME . '_' . $index . '_' . Acf_Fields::FIELD_QUESTION_NAME, true ),
			'answer'   => get_post_meta( $post_id, Acf_Fields::FIELD_FAQ_ITEMS_NAME . '_' . $index . '_' . Acf_Fields::FIELD_ANSWER_NAME, true ),
		);
	}

	return $rows;
}

/**
 * Returns the currently queried singular post ID when applicable.
 */
function get_current_singular_post_id() {
	return is_singular( get_enabled_post_types() ) ? absint( get_queried_object_id() ) : 0;
}

/**
 * Determines whether the FAQ block is visible on the page.
 *
 * Sites can override this when they disable automatic rendering and output FAQs manually.
 *
 * @param int $post_id Post ID.
 */
function are_faqs_visible_on_page( $post_id ) {
	$post_id = absint( $post_id );
	$mode    = get_frontend_output_mode( get_post_type( $post_id ) );

	if ( 'php_template' === $mode ) {
		$is_visible = true;
	} else {
		$is_visible = is_automatic_frontend_render_enabled( $post_id );
	}

	return (bool) apply_filters( 'csfaq_faqs_visible_on_page', $is_visible, $post_id, $mode );
}

/**
 * Formats an answer for safe frontend output.
 *
 * @param string $answer Answer text.
 */
function prepare_answer_for_frontend( $answer ) {
	return wpautop( esc_html( sanitize_answer( $answer ) ) );
}

/**
 * Formats an answer for JSON-LD output.
 *
 * @param string $answer Answer text.
 */
function prepare_answer_for_schema( $answer ) {
	$answer = sanitize_answer( sanitize_rich_text_to_plain_text( $answer ) );

	return trim( is_string( $answer ) ? $answer : '' );
}

/**
 * Formats a question for JSON-LD output.
 *
 * @param string $question Question text.
 */
function prepare_question_for_schema( $question ) {
	return sanitize_question( sanitize_rich_text_to_plain_text( $question ) );
}

/**
 * Builds FAQ markup for manual theme output.
 *
 * @param int $post_id Optional post ID.
 */
function get_faq_markup( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_the_ID() );

	if ( ! $post_id ) {
		return '';
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof \WP_Post ) {
		return '';
	}

	if ( ! is_supported_post_type( $post->post_type ) ) {
		return '';
	}

	if ( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post_id ) ) {
		return '';
	}

	$post_type = $post->post_type;
	$faq_items = get_faq_items( $post_id );

	if ( empty( $faq_items ) ) {
		return '';
	}

	$section_title = get_frontend_section_title( $post_type, $post_id, $faq_items );

	$section_id  = 'csfaq-faqs-title-' . $post_id;
	$has_title   = '' !== trim( $section_title );
	$labelled_by = $has_title ? ' aria-labelledby="' . esc_attr( $section_id ) . '"' : '';

	ob_start();
	?>
	<section class="csfaq-faqs"<?php echo $labelled_by; ?>>
		<?php if ( $has_title ) : ?>
			<h2 id="<?php echo esc_attr( $section_id ); ?>" class="csfaq-faqs-title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<?php foreach ( $faq_items as $faq_item ) : ?>
			<div class="csfaq-faq-item">
				<h3 class="csfaq-faq-question"><?php echo esc_html( $faq_item['question'] ); ?></h3>
				<div class="csfaq-faq-answer">
					<?php echo wp_kses_post( prepare_answer_for_frontend( $faq_item['answer'] ) ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}
