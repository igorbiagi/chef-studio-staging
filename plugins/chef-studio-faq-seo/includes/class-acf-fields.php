<?php
/**
 * ACF field registration.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the plugin field group through ACF local PHP.
 */
final class Acf_Fields {

	/**
	 * Field group key.
	 */
	const GROUP_KEY = 'group_csfaq_editorial_faqs';

	/**
	 * Repeater field key.
	 */
	const FIELD_FAQ_ITEMS_KEY = 'field_csfaq_faq_items';

	/**
	 * Question field key.
	 */
	const FIELD_QUESTION_KEY = 'field_csfaq_faq_question';

	/**
	 * Answer field key.
	 */
	const FIELD_ANSWER_KEY = 'field_csfaq_faq_answer';

	/**
	 * Import textarea field key.
	 */
	const FIELD_IMPORT_RAW_KEY = 'field_csfaq_faq_import_raw';

	/**
	 * Import mode field key.
	 */
	const FIELD_IMPORT_MODE_KEY = 'field_csfaq_faq_import_mode';

	/**
	 * Repeater field name.
	 */
	const FIELD_FAQ_ITEMS_NAME = 'faq_items';

	/**
	 * Question field name.
	 */
	const FIELD_QUESTION_NAME = 'question';

	/**
	 * Answer field name.
	 */
	const FIELD_ANSWER_NAME = 'answer';

	/**
	 * Import textarea field name.
	 */
	const FIELD_IMPORT_RAW_NAME = 'faq_import_raw';

	/**
	 * Import mode field name.
	 */
	const FIELD_IMPORT_MODE_NAME = 'faq_import_mode';

	/**
	 * Flexible content field name used by the theme builder.
	 */
	const FLEX_FIELD_NAME = 'layout_flessibile_1';

	/**
	 * FAQ layout name inside the flexible content field.
	 */
	const FLEX_LAYOUT_FAQ_NAME = 'faq';

	/**
	 * CTA/FAQ hybrid layout name inside the flexible content field.
	 */
	const FLEX_LAYOUT_CTA_FAQ_NAME = 'cta_con_paragrafo_o_faq';

	/**
	 * Reusable block layout name inside the flexible content field.
	 */
	const FLEX_LAYOUT_REUSABLE_NAME = 'blocco_riutilizzabile';

	/**
	 * Reusable block target field name.
	 */
	const FLEX_LAYOUT_REUSABLE_TARGET_NAME = 'seleziona_blocco_riutilizzabile';

	/**
	 * Flexible layout title field name.
	 */
	const FLEX_LAYOUT_TITLE_NAME = 'titolo_faq';

	/**
	 * Flexible layout FAQ repeater name.
	 */
	const FLEX_LAYOUT_REPEATER_NAME = 'faq';

	/**
	 * Flexible layout FAQ question field name.
	 */
	const FLEX_LAYOUT_QUESTION_NAME = 'titolo_faq';

	/**
	 * Flexible layout FAQ answer field name.
	 */
	const FLEX_LAYOUT_ANSWER_NAME = 'contenuto_faq';

	/**
	 * CTA/FAQ hybrid layout content type field name.
	 */
	const FLEX_LAYOUT_CONTENT_TYPE_NAME = 'tipo_contenuto';

	/**
	 * CTA/FAQ hybrid layout value that enables the FAQ repeater.
	 */
	const FLEX_LAYOUT_CONTENT_TYPE_FAQ_VALUE = 'FAQ';

	/**
	 * Flexible layout import textarea field key.
	 */
	const FLEX_IMPORT_RAW_KEY = 'field_csfaq_flex_faq_import_raw';

	/**
	 * Flexible layout import mode field key.
	 */
	const FLEX_IMPORT_MODE_KEY = 'field_csfaq_flex_faq_import_mode';

	/**
	 * CTA/FAQ hybrid layout import textarea field key.
	 */
	const FLEX_CTA_IMPORT_RAW_KEY = 'field_csfaq_flex_cta_faq_import_raw';

	/**
	 * CTA/FAQ hybrid layout import mode field key.
	 */
	const FLEX_CTA_IMPORT_MODE_KEY = 'field_csfaq_flex_cta_faq_import_mode';

	/**
	 * Flexible layout import textarea field name.
	 */
	const FLEX_IMPORT_RAW_NAME = 'csfaq_faq_import_raw';

	/**
	 * Flexible layout import mode field name.
	 */
	const FLEX_IMPORT_MODE_NAME = 'csfaq_faq_import_mode';

	/**
	 * Registers hooks.
	 */
	public function register() {
		if ( ! is_acf_pro_active() ) {
			return;
		}

		add_action( 'acf/init', array( $this, 'register_field_group' ) );
		add_filter( 'acf/load_field/name=' . self::FLEX_FIELD_NAME, array( $this, 'inject_fields_into_flexible_layout' ) );
	}

	/**
	 * Registers the field group through acf_add_local_field_group().
	 */
	public function register_field_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$field_group = array(
			'key'                   => self::GROUP_KEY,
			'title'                 => __( 'FAQ editoriali', plugin_textdomain() ),
			'fields'                => $this->get_fields(),
			'location'              => $this->get_location_rules(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'description'           => __( 'Gestisci le FAQ dei post, l’import rapido da GPT, l’output frontend e lo schema FAQPage partendo da un’unica fonte dati.', plugin_textdomain() ),
		);

		$field_group = apply_filters( 'csfaq_acf_field_group_args', $field_group );

		acf_add_local_field_group( $field_group );
	}

	/**
	 * Returns field definitions.
	 *
	 * The field keys are plugin-owned and stable so update_field() can create
	 * repeater rows reliably even when the post has no previous FAQ values.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_fields() {
		return array(
			array(
				'key'           => self::FIELD_FAQ_ITEMS_KEY,
				'label'         => __( 'FAQ ufficiali', plugin_textdomain() ),
				'name'          => self::FIELD_FAQ_ITEMS_NAME,
				'type'          => 'repeater',
				'instructions'  => __( 'Questo repeater è la fonte dati ufficiale usata sia nel frontend sia nello schema JSON-LD. Dopo ogni import, modifica qui le singole FAQ.', plugin_textdomain() ),
				'required'      => 0,
				'collapsed'     => self::FIELD_QUESTION_KEY,
				'layout'        => 'block',
				'button_label'  => __( 'Aggiungi FAQ', plugin_textdomain() ),
				'min'           => 0,
				'sub_fields'    => array(
					array(
						'key'          => self::FIELD_QUESTION_KEY,
						'label'        => __( 'Domanda', plugin_textdomain() ),
						'name'         => self::FIELD_QUESTION_NAME,
						'type'         => 'text',
						'instructions' => __( 'Domanda in testo semplice usata sia nel blocco FAQ frontend sia nello schema FAQPage.', plugin_textdomain() ),
						'required'     => 1,
						'wrapper'      => array(
							'width' => 100,
						),
					),
					array(
						'key'          => self::FIELD_ANSWER_KEY,
						'label'        => __( 'Risposta', plugin_textdomain() ),
						'name'         => self::FIELD_ANSWER_NAME,
						'type'         => 'textarea',
						'instructions' => __( 'Risposta editoriale in testo semplice. I ritorni a capo vengono mantenuti per i paragrafi nel frontend e puliti in modo sicuro per lo schema.', plugin_textdomain() ),
						'required'     => 1,
						'rows'         => 5,
						'wrapper'      => array(
							'width' => 100,
						),
					),
				),
			),
			array(
				'key'           => self::FIELD_IMPORT_RAW_KEY,
				'label'         => __( 'Import rapido FAQ', plugin_textdomain() ),
				'name'          => self::FIELD_IMPORT_RAW_NAME,
				'type'          => 'textarea',
				'instructions'  => __( 'Incolla qui FAQ in JSON semplice, in un blocco FAQPage JSON-LD oppure nel formato Q:/A:. Al salvataggio i contenuti verranno importati nel repeater e questo campo sarà svuotato automaticamente. Dopo l’import, modifica le singole FAQ nel repeater qui sopra.', plugin_textdomain() ),
				'required'      => 0,
				'rows'          => 10,
				'wrapper'       => array(
					'width' => 100,
				),
				'placeholder'   => "[\n  {\n    \"question\": \"Come funziona...?\",\n    \"answer\": \"Funziona così...\"\n  }\n]",
			),
			array(
				'key'           => self::FIELD_IMPORT_MODE_KEY,
				'label'         => __( 'Modalità di import', plugin_textdomain() ),
				'name'          => self::FIELD_IMPORT_MODE_NAME,
				'type'          => 'radio',
				'instructions'  => __( 'Aggiungi accoda le nuove FAQ a quelle esistenti. Sostituisci cancella le FAQ attuali e importa solo quelle nuove.', plugin_textdomain() ),
				'required'      => 0,
				'choices'       => array(
					'append'  => __( 'Aggiungi', plugin_textdomain() ),
					'replace' => __( 'Sostituisci', plugin_textdomain() ),
				),
				'default_value' => 'append',
				'layout'        => 'horizontal',
				'return_format' => 'value',
				'wrapper'       => array(
					'width' => 100,
				),
			),
		);
	}

	/**
	 * Builds ACF location rules for all enabled post types.
	 *
	 * @return array<int, array<int, array<string, string>>>
	 */
	private function get_location_rules() {
		$rules = array();

		foreach ( get_enabled_post_types() as $post_type ) {
			$rules[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				),
			);
		}

		return ! empty( $rules ) ? $rules : array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				),
			),
		);
	}

	/**
	 * Injects FAQ import helper fields into the existing flexible FAQ layout.
	 *
	 * @param array<string, mixed> $field Flexible content field definition.
	 * @return array<string, mixed>
	 */
	public function inject_fields_into_flexible_layout( $field ) {
		$current_post_type = get_current_admin_post_type();

		if ( '' !== $current_post_type && ! is_supported_post_type( $current_post_type ) ) {
			return $field;
		}

		if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
			return $field;
		}

		$layout_configs = $this->get_flexible_layout_configs();

		foreach ( $field['layouts'] as &$layout ) {
			$layout_name = isset( $layout['name'] ) && is_string( $layout['name'] ) ? $layout['name'] : '';

			if ( '' === $layout_name || ! isset( $layout_configs[ $layout_name ] ) ) {
				continue;
			}

			if ( empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
				$layout['sub_fields'] = array();
			}

			$config         = $layout_configs[ $layout_name ];
			$existing_names = array();
			$type_field_key = '';

			foreach ( $layout['sub_fields'] as $sub_field ) {
				if ( isset( $sub_field['name'] ) && is_string( $sub_field['name'] ) ) {
					$existing_names[] = $sub_field['name'];

					if ( self::FLEX_LAYOUT_CONTENT_TYPE_NAME === $sub_field['name'] && ! empty( $sub_field['key'] ) && is_string( $sub_field['key'] ) ) {
						$type_field_key = $sub_field['key'];
					}
				}
			}

			if ( in_array( self::FLEX_IMPORT_RAW_NAME, $existing_names, true ) || in_array( self::FLEX_IMPORT_MODE_NAME, $existing_names, true ) ) {
				continue;
			}

			$import_fields = array(
				array(
					'key'          => $config['import_raw_key'],
					'label'        => __( 'Import rapido FAQ', plugin_textdomain() ),
					'name'         => self::FLEX_IMPORT_RAW_NAME,
					'type'         => 'textarea',
					'instructions' => __( 'Incolla qui FAQ in JSON semplice, FAQPage JSON-LD o formato Q:/A:. Al salvataggio verranno importate nel repeater FAQ di questo layout. Dopo l’import, modifica le singole FAQ nel repeater qui sotto.', plugin_textdomain() ),
					'required'     => 0,
					'rows'         => 8,
					'wrapper'      => array(
						'width' => 100,
					),
				),
				array(
					'key'           => $config['import_mode_key'],
					'label'         => __( 'Modalità di import', plugin_textdomain() ),
					'name'          => self::FLEX_IMPORT_MODE_NAME,
					'type'          => 'radio',
					'instructions'  => __( 'Aggiungi accoda le nuove FAQ a quelle già presenti nel layout. Sostituisci cancella le FAQ attuali del layout e importa solo quelle nuove.', plugin_textdomain() ),
					'required'      => 0,
					'choices'       => array(
						'append'  => __( 'Aggiungi', plugin_textdomain() ),
						'replace' => __( 'Sostituisci', plugin_textdomain() ),
					),
					'default_value' => 'append',
					'layout'        => 'horizontal',
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => 100,
					),
				),
			);

			if ( ! empty( $config['needs_faq_type'] ) && $type_field_key ) {
				$conditional_logic = array(
					array(
						array(
							'field'    => $type_field_key,
							'operator' => '==',
							'value'    => self::FLEX_LAYOUT_CONTENT_TYPE_FAQ_VALUE,
						),
					),
				);

				foreach ( $import_fields as &$import_field ) {
					$import_field['conditional_logic'] = $conditional_logic;
				}

				unset( $import_field );
			}

			foreach ( $import_fields as &$import_field ) {
				$import_field = $this->prepare_injected_flexible_sub_field( $import_field );
			}

			unset( $import_field );

			$insert_index = count( $layout['sub_fields'] );

			foreach ( $layout['sub_fields'] as $index => $sub_field ) {
				if ( isset( $sub_field['name'] ) && self::FLEX_LAYOUT_REPEATER_NAME === $sub_field['name'] ) {
					$insert_index = $index;
					break;
				}
			}

			array_splice( $layout['sub_fields'], $insert_index, 0, $import_fields );
		}

		return $field;
	}

	/**
	 * Validates fields injected after ACF has loaded the parent flexible field.
	 *
	 * ACF expects sub-fields to contain internal keys such as _name when values
	 * are formatted on the frontend.
	 *
	 * @param array<string,mixed> $field Field definition.
	 * @return array<string,mixed>
	 */
	private function prepare_injected_flexible_sub_field( array $field ) {
		if ( function_exists( 'acf_get_valid_field' ) ) {
			return acf_get_valid_field( $field );
		}

		if ( empty( $field['_name'] ) ) {
			$field['_name'] = isset( $field['name'] ) && is_string( $field['name'] ) ? $field['name'] : '';
		}

		if ( isset( $field['type'] ) && 'radio' === $field['type'] && ! isset( $field['return_format'] ) ) {
			$field['return_format'] = 'value';
		}

		return $field;
	}

	/**
	 * Returns the flexible layouts that should receive the FAQ import helper fields.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_flexible_layout_configs() {
		return array(
			self::FLEX_LAYOUT_FAQ_NAME => array(
				'import_raw_key'  => self::FLEX_IMPORT_RAW_KEY,
				'import_mode_key' => self::FLEX_IMPORT_MODE_KEY,
				'needs_faq_type'  => false,
			),
			self::FLEX_LAYOUT_CTA_FAQ_NAME => array(
				'import_raw_key'  => self::FLEX_CTA_IMPORT_RAW_KEY,
				'import_mode_key' => self::FLEX_CTA_IMPORT_MODE_KEY,
				'needs_faq_type'  => true,
			),
		);
	}
}
