<?php
/**
 * FAQ import handler.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Parses the quick-import field and syncs the official repeater.
 */
final class Import_Handler {

	/**
	 * Prevents recursive saves.
	 *
	 * @var bool
	 */
	private $processing = false;

	/**
	 * Registers hooks.
	 */
	public function register() {
		if ( ! is_acf_pro_active() ) {
			return;
		}

		add_action( 'acf/save_post', array( $this, 'maybe_import_faqs' ), 20 );
	}

	/**
	 * Imports FAQ content from the raw import field when needed.
	 *
	 * @param int|string $post_id ACF post ID.
	 */
	public function maybe_import_faqs( $post_id ) {
		$post_id = absint( $post_id );

		if ( $this->processing || ! $this->should_process_request( $post_id ) ) {
			return;
		}

		$results = array();
		$results = array_filter(
			array(
				$this->process_standard_post_import( $post_id ),
				$this->process_flexible_layout_imports( $post_id ),
			)
		);

		if ( empty( $results ) ) {
			return;
		}

		$messages = array();
		$types    = array();

		foreach ( $results as $result ) {
			if ( ! empty( $result['message'] ) ) {
				$messages[] = $result['message'];
			}

			if ( ! empty( $result['type'] ) ) {
				$types[] = $result['type'];
			}
		}

		if ( empty( $messages ) ) {
			return;
		}

		Admin_Notices::store_notice( $post_id, implode( ' ', $messages ), $this->combine_notice_types( $types ) );
	}

	/**
	 * Processes the standard top-level FAQ import field.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,string>|null
	 */
	private function process_standard_post_import( $post_id ) {
		$raw_value = $this->normalize_import_input( $this->get_import_raw_value( $post_id ) );

		if ( '' === $raw_value ) {
			return null;
		}

		$mode   = $this->sanitize_mode( $this->get_import_mode_value( $post_id ) );
		$parsed = $this->parse_import_payload( $raw_value );

		if ( empty( $parsed['success'] ) ) {
			return array(
				'message' => $this->get_parse_error_message( $parsed ),
				'type'    => 'error',
			);
		}

		$existing_items = 'replace' === $mode ? array() : $this->get_existing_faq_items( $post_id );
		$merged_items   = $this->merge_faqs( $existing_items, $parsed['items'] );
		$notice_type    = $this->get_completion_notice_type( $merged_items['imported_count'], $merged_items['duplicate_count'], $parsed['invalid_count'] );
		$message        = $this->get_completion_message(
			$mode,
			$merged_items['imported_count'],
			$merged_items['duplicate_count'],
			$parsed['invalid_count']
		);

		if ( $merged_items['imported_count'] > 0 ) {
			$this->persist_import( $post_id, $merged_items['items'], true );
		} else {
			$this->persist_import( $post_id, array(), false );
		}

		return array(
			'message' => $message,
			'type'    => $notice_type,
		);
	}

	/**
	 * Processes FAQ imports inside the existing flexible FAQ layouts.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,string>|null
	 */
	private function process_flexible_layout_imports( $post_id ) {
		$field_object = $this->get_flexible_field_object( $post_id );
		$rows         = get_flexible_layout_rows( $post_id );
		$posted_rows  = $this->get_posted_flexible_rows( $field_object );

		if ( empty( $field_object ) || empty( $rows ) ) {
			return null;
		}

		$messages = array();
		$types    = array();

		foreach ( $rows as $index => $row ) {
			if ( ! is_array( $row ) || empty( $row['acf_fc_layout'] ) || ! is_faq_flexible_layout_name( $row['acf_fc_layout'] ) ) {
				continue;
			}

			$layout_map = $this->get_flexible_layout_map( $field_object, $row['acf_fc_layout'] );

			if ( empty( $layout_map ) ) {
				continue;
			}

			$posted_row = isset( $posted_rows[ $index ] ) && is_array( $posted_rows[ $index ] ) ? $posted_rows[ $index ] : array();
			$raw_value  = $this->normalize_import_input( $this->get_flexible_row_field_value( $posted_row, Acf_Fields::FLEX_IMPORT_RAW_NAME, $layout_map['import_raw_key'] ) );

			if ( '' === $raw_value ) {
				$raw_value = $this->normalize_import_input( $this->get_flexible_row_field_value( $row, Acf_Fields::FLEX_IMPORT_RAW_NAME, $layout_map['import_raw_key'] ) );
			}

			if ( '' === $raw_value ) {
				continue;
			}

			$mode_value = $this->get_flexible_row_field_value( $posted_row, Acf_Fields::FLEX_IMPORT_MODE_NAME, $layout_map['import_mode_key'] );

			if ( '' === $mode_value ) {
				$mode_value = $this->get_flexible_row_field_value( $row, Acf_Fields::FLEX_IMPORT_MODE_NAME, $layout_map['import_mode_key'] );
			}

			$mode   = $this->sanitize_mode( $mode_value );
			$parsed = $this->parse_import_payload( $raw_value );

			if ( empty( $parsed['success'] ) ) {
				$messages[] = sprintf(
					/* translators: 1: layout index, 2: error message */
					__( 'Layout FAQ #%1$d: %2$s', plugin_textdomain() ),
					$index + 1,
					$this->get_parse_error_message( $parsed )
				);
				$types[] = 'error';
				continue;
			}

			$existing_source   = ! empty( $posted_row ) ? $posted_row : $row;
			$existing_items    = 'replace' === $mode ? array() : $this->get_flexible_layout_existing_items( $existing_source, $layout_map );
			$existing_raw_rows = 'replace' === $mode ? array() : $this->get_flexible_layout_existing_raw_rows( $existing_source, $layout_map );
			$merged_items      = $this->merge_faqs( $existing_items, $parsed['items'] );
			$rows_to_save      = 'replace' === $mode
				? $this->format_flexible_layout_repeater_rows( $merged_items['items'], $layout_map )
				: array_merge(
					$existing_raw_rows,
					$this->format_flexible_layout_repeater_rows( $merged_items['added_items'], $layout_map )
				);

			$this->persist_flexible_layout_row(
				$post_id,
				$field_object,
				$layout_map,
				$index + 1,
				$rows_to_save,
				Acf_Fields::FLEX_LAYOUT_CTA_FAQ_NAME === $row['acf_fc_layout']
			);

			$messages[] = sprintf(
				/* translators: 1: layout index, 2: import message */
				__( 'Layout FAQ #%1$d: %2$s', plugin_textdomain() ),
				$index + 1,
				$this->get_completion_message(
					$mode,
					$merged_items['imported_count'],
					$merged_items['duplicate_count'],
					$parsed['invalid_count']
				)
			);

			$types[]  = $this->get_completion_notice_type( $merged_items['imported_count'], $merged_items['duplicate_count'], $parsed['invalid_count'] );
		}

		if ( empty( $messages ) ) {
			return null;
		}

		return array(
			'message' => implode( ' ', $messages ),
			'type'    => $this->combine_notice_types( $types ),
		);
	}

	/**
	 * Verifies that the current save request is safe to process.
	 *
	 * @param int $post_id Post ID.
	 */
	private function should_process_request( $post_id ) {
		if ( ! is_admin() || ! $post_id ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( ! $post || ! is_supported_post_type( $post->post_type ) ) {
			return false;
		}

		if ( ! isset( $_POST['acf'] ) || ! is_array( $_POST['acf'] ) ) {
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return $this->verify_post_nonce( $post_id );
	}

	/**
	 * Verifies the classic post save nonce.
	 *
	 * @param int $post_id Post ID.
	 */
	private function verify_post_nonce( $post_id ) {
		if ( empty( $_POST['_wpnonce'] ) ) {
			return false;
		}

		$nonce     = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
		$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : get_post_type( $post_id );

		if ( wp_verify_nonce( $nonce, 'update-post_' . $post_id ) ) {
			return true;
		}

		if ( $post_type && wp_verify_nonce( $nonce, 'add-' . $post_type ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the raw import value.
	 *
	 * @param int $post_id Post ID.
	 */
	private function get_import_raw_value( $post_id ) {
		$value = function_exists( 'get_field' ) ? get_field( Acf_Fields::FIELD_IMPORT_RAW_NAME, $post_id, false ) : '';

		if ( is_string( $value ) ) {
			return $value;
		}

		return (string) get_post_meta( $post_id, Acf_Fields::FIELD_IMPORT_RAW_NAME, true );
	}

	/**
	 * Returns the saved import mode.
	 *
	 * @param int $post_id Post ID.
	 */
	private function get_import_mode_value( $post_id ) {
		$value = function_exists( 'get_field' ) ? get_field( Acf_Fields::FIELD_IMPORT_MODE_NAME, $post_id, false ) : '';

		if ( is_string( $value ) ) {
			return $value;
		}

		return (string) get_post_meta( $post_id, Acf_Fields::FIELD_IMPORT_MODE_NAME, true );
	}

	/**
	 * Returns the current repeater rows that should be used as append source.
	 *
	 * @param int $post_id Post ID.
	 * @return array<int, array{question:string,answer:string}>
	 */
	private function get_existing_faq_items( $post_id ) {
		if ( isset( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_KEY ] ) && is_array( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_KEY ] ) ) {
			return prepare_faq_items( wp_unslash( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_KEY ] ) );
		}

		if ( isset( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_NAME ] ) && is_array( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_NAME ] ) ) {
			return prepare_faq_items( wp_unslash( $_POST['acf'][ Acf_Fields::FIELD_FAQ_ITEMS_NAME ] ) );
		}

		return get_faq_items( $post_id );
	}

	/**
	 * Returns current FAQ rows from one flexible FAQ layout row.
	 *
	 * @param array<string,mixed> $row        Flexible layout row.
	 * @param array<string,string> $layout_map Flexible layout field-key map.
	 * @return array<int, array{question:string,answer:string}>
	 */
	private function get_flexible_layout_existing_items( array $row, array $layout_map = array() ) {
		$items        = array();
		$repeater_key = ! empty( $layout_map['repeater_key'] ) ? $layout_map['repeater_key'] : '';
		$faq_rows     = array();

		if ( ! empty( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) && is_array( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) ) {
			$faq_rows = $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ];
		} elseif ( $repeater_key && ! empty( $row[ $repeater_key ] ) && is_array( $row[ $repeater_key ] ) ) {
			$faq_rows = $row[ $repeater_key ];
		}

		if ( empty( $faq_rows ) ) {
			return $items;
		}

		foreach ( $faq_rows as $faq_row ) {
			if ( ! is_array( $faq_row ) ) {
				continue;
			}

			$faq = array(
				'question' => sanitize_question( sanitize_rich_text_to_plain_text( get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_QUESTION_NAME, $layout_map['question_key'] ?? '' ) ) ),
				'answer'   => sanitize_answer( sanitize_rich_text_to_plain_text( get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_ANSWER_NAME, $layout_map['answer_key'] ?? '' ) ) ),
			);

			if ( is_valid_faq_item( $faq ) ) {
				$items[] = $faq;
			}
		}

		return $items;
	}

	/**
	 * Returns current raw repeater rows from one flexible FAQ layout row.
	 *
	 * This preserves the original WYSIWYG markup for existing FAQs when appending.
	 *
	 * @param array<string,mixed>  $row        Flexible layout row.
	 * @param array<string,string> $layout_map Flexible layout field-key map.
	 * @return array<int, array<string,mixed>>
	 */
	private function get_flexible_layout_existing_raw_rows( array $row, array $layout_map = array() ) {
		$repeater_key = ! empty( $layout_map['repeater_key'] ) ? $layout_map['repeater_key'] : '';
		$question_key = ! empty( $layout_map['question_key'] ) ? $layout_map['question_key'] : Acf_Fields::FLEX_LAYOUT_QUESTION_NAME;
		$answer_key   = ! empty( $layout_map['answer_key'] ) ? $layout_map['answer_key'] : Acf_Fields::FLEX_LAYOUT_ANSWER_NAME;
		$faq_rows     = array();

		if ( ! empty( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) && is_array( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] ) ) {
			$faq_rows = array_values( $row[ Acf_Fields::FLEX_LAYOUT_REPEATER_NAME ] );
		}

		if ( empty( $faq_rows ) && $repeater_key && ! empty( $row[ $repeater_key ] ) && is_array( $row[ $repeater_key ] ) ) {
			$faq_rows = array_values( $row[ $repeater_key ] );
		}

		if ( empty( $faq_rows ) ) {
			return array();
		}

		$normalized_rows = array();

		foreach ( $faq_rows as $faq_row ) {
			if ( ! is_array( $faq_row ) ) {
				continue;
			}

			$normalized_rows[] = array(
				$question_key => get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_QUESTION_NAME, $layout_map['question_key'] ?? '' ),
				$answer_key   => get_faq_item_value( $faq_row, Acf_Fields::FLEX_LAYOUT_ANSWER_NAME, $layout_map['answer_key'] ?? '' ),
			);
		}

		return $normalized_rows;
	}

	/**
	 * Returns the submitted flexible rows from the current ACF request.
	 *
	 * @param array<string,mixed> $field_object Flexible field object.
	 * @return array<int, array<string,mixed>>
	 */
	private function get_posted_flexible_rows( array $field_object ) {
		$field_key = isset( $field_object['key'] ) && is_string( $field_object['key'] ) ? $field_object['key'] : '';

		if ( '' === $field_key || empty( $_POST['acf'][ $field_key ] ) || ! is_array( $_POST['acf'][ $field_key ] ) ) {
			return array();
		}

		$rows = wp_unslash( $_POST['acf'][ $field_key ] );

		return is_array( $rows ) ? array_values( $rows ) : array();
	}

	/**
	 * Returns the flexible content field object.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	private function get_flexible_field_object( $post_id ) {
		$field_object = function_exists( 'get_field_object' ) ? get_field_object( Acf_Fields::FLEX_FIELD_NAME, $post_id, false, false ) : false;

		return is_array( $field_object ) ? $field_object : array();
	}

	/**
	 * Returns the field-key map for one flexible FAQ layout.
	 *
	 * @param array<string,mixed> $field_object Flexible field object.
	 * @param string              $layout_name  Layout name.
	 * @return array<string,string>
	 */
	private function get_flexible_layout_map( array $field_object, $layout_name ) {
		if ( empty( $field_object['layouts'] ) || ! is_array( $field_object['layouts'] ) ) {
			return array();
		}

		foreach ( $field_object['layouts'] as $layout ) {
			if ( empty( $layout['name'] ) || $layout_name !== $layout['name'] ) {
				continue;
			}

			$map = array(
				'repeater_key'     => '',
				'question_key'     => '',
				'answer_key'       => '',
				'import_raw_key'   => '',
				'import_mode_key'  => '',
				'content_type_key' => '',
			);

			if ( empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
				return $map;
			}

			foreach ( $layout['sub_fields'] as $sub_field ) {
				$name = isset( $sub_field['name'] ) && is_string( $sub_field['name'] ) ? $sub_field['name'] : '';
				$key  = isset( $sub_field['key'] ) && is_string( $sub_field['key'] ) ? $sub_field['key'] : '';

				if ( Acf_Fields::FLEX_LAYOUT_REPEATER_NAME === $name ) {
					$map['repeater_key'] = $key;

					if ( ! empty( $sub_field['sub_fields'] ) && is_array( $sub_field['sub_fields'] ) ) {
						foreach ( $sub_field['sub_fields'] as $repeater_sub_field ) {
							$repeater_name = isset( $repeater_sub_field['name'] ) && is_string( $repeater_sub_field['name'] ) ? $repeater_sub_field['name'] : '';
							$repeater_key  = isset( $repeater_sub_field['key'] ) && is_string( $repeater_sub_field['key'] ) ? $repeater_sub_field['key'] : '';

							if ( Acf_Fields::FLEX_LAYOUT_QUESTION_NAME === $repeater_name ) {
								$map['question_key'] = $repeater_key;
							}

							if ( Acf_Fields::FLEX_LAYOUT_ANSWER_NAME === $repeater_name ) {
								$map['answer_key'] = $repeater_key;
							}
						}
					}
				}

				if ( Acf_Fields::FLEX_IMPORT_RAW_NAME === $name ) {
					$map['import_raw_key'] = $key;
				}

				if ( Acf_Fields::FLEX_IMPORT_MODE_NAME === $name ) {
					$map['import_mode_key'] = $key;
				}

				if ( Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME === $name ) {
					$map['content_type_key'] = $key;
				}
			}

			return $map;
		}

		return array();
	}

	/**
	 * Returns one flexible row value by field name or field key.
	 *
	 * @param array<string,mixed> $row       Flexible row data.
	 * @param string              $name      Expected field name.
	 * @param string              $field_key Expected field key.
	 */
	private function get_flexible_row_field_value( array $row, $name, $field_key = '' ) {
		if ( isset( $row[ $name ] ) && is_scalar( $row[ $name ] ) ) {
			return (string) $row[ $name ];
		}

		if ( $field_key && isset( $row[ $field_key ] ) && is_scalar( $row[ $field_key ] ) ) {
			return (string) $row[ $field_key ];
		}

		return '';
	}

	/**
	 * Normalizes the import mode.
	 *
	 * @param string $mode Raw mode value.
	 */
	private function sanitize_mode( $mode ) {
		return 'replace' === $mode ? 'replace' : 'append';
	}

	/**
	 * Removes surrounding code fences and normalizes input.
	 *
	 * @param string $value Raw textarea value.
	 */
	private function normalize_import_input( $value ) {
		$value = trim( normalize_line_endings( (string) $value ) );

		if ( preg_match( '/^```[a-z0-9_-]*\s*(.*?)\s*```$/is', $value, $matches ) ) {
			$value = trim( $matches[1] );
		}

		if ( preg_match( '/^\s*<script\b[^>]*type\s*=\s*(?:"application\/ld\+json"|\'application\/ld\+json\'|application\/ld\+json)[^>]*>(.*?)<\/script>\s*$/is', $value, $matches ) ) {
			$value = trim( $matches[1] );
		}

		return $value;
	}

	/**
	 * Parses supported import formats.
	 *
	 * @param string $raw_value Raw textarea value.
	 * @return array<string, mixed>
	 */
	private function parse_import_payload( $raw_value ) {
		$json_result = $this->parse_json_payload( $raw_value );

		if ( ! empty( $json_result['success'] ) ) {
			return $json_result;
		}

		$json_ld_result = $this->parse_json_ld_payload( $raw_value );

		if ( ! empty( $json_ld_result['recognized'] ) ) {
			return $json_ld_result;
		}

		if ( ! empty( $json_result['recognized'] ) ) {
			return $json_result;
		}

		$qa_result = $this->parse_qa_payload( $raw_value );

		if ( ! empty( $qa_result['success'] ) ) {
			return $qa_result;
		}

		return array(
			'success'      => false,
			'recognized'   => false,
			'items'        => array(),
			'invalid_count' => 0,
			'error'        => 'invalid_format',
		);
	}

	/**
	 * Parses a simple JSON array of FAQ objects.
	 *
	 * @param string $raw_value Raw textarea value.
	 * @return array<string, mixed>
	 */
	private function parse_json_payload( $raw_value ) {
		$decoded = json_decode( $raw_value, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return array(
				'success'      => false,
				'recognized'   => false,
				'items'        => array(),
				'invalid_count' => 0,
				'error'        => 'invalid_json',
			);
		}

		if ( ! is_array( $decoded ) || ! is_list_array( $decoded ) ) {
			return array(
				'success'      => false,
				'recognized'   => true,
				'items'        => array(),
				'invalid_count' => 0,
				'error'        => 'unsupported_json_structure',
			);
		}

		$items         = array();
		$invalid_count = 0;

		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) || ! array_key_exists( 'question', $item ) || ! array_key_exists( 'answer', $item ) ) {
				++$invalid_count;
				continue;
			}

			$faq = sanitize_faq_item( $item );

			if ( ! is_valid_faq_item( $faq ) ) {
				++$invalid_count;
				continue;
			}

			$items[] = $faq;
		}

		if ( empty( $items ) ) {
			return array(
				'success'      => false,
				'recognized'   => true,
				'items'        => array(),
				'invalid_count' => $invalid_count,
				'error'        => 'no_valid_faqs',
			);
		}

		return array(
			'success'      => true,
			'recognized'   => true,
			'items'        => $items,
			'invalid_count' => $invalid_count,
			'error'        => '',
		);
	}

	/**
	 * Parses FAQPage JSON-LD objects produced by GPT or SEO tools.
	 *
	 * @param string $raw_value Raw textarea value.
	 * @return array<string, mixed>
	 */
	private function parse_json_ld_payload( $raw_value ) {
		$decoded = json_decode( $raw_value, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) || is_list_array( $decoded ) ) {
			return array(
				'success'       => false,
				'recognized'    => false,
				'items'         => array(),
				'invalid_count' => 0,
				'error'         => 'invalid_jsonld',
			);
		}

		$type        = isset( $decoded['@type'] ) ? sanitize_text_field( (string) $decoded['@type'] ) : '';
		$main_entity = isset( $decoded['mainEntity'] ) && is_array( $decoded['mainEntity'] ) ? $decoded['mainEntity'] : array();

		if ( 'FAQPage' !== $type || empty( $main_entity ) ) {
			return array(
				'success'       => false,
				'recognized'    => false,
				'items'         => array(),
				'invalid_count' => 0,
				'error'         => 'invalid_jsonld',
			);
		}

		$items         = array();
		$invalid_count = 0;

		foreach ( $main_entity as $entity ) {
			if ( ! is_array( $entity ) ) {
				++$invalid_count;
				continue;
			}

			$question = isset( $entity['name'] ) ? $entity['name'] : '';
			$answer   = '';

			if ( isset( $entity['acceptedAnswer'] ) && is_array( $entity['acceptedAnswer'] ) && array_key_exists( 'text', $entity['acceptedAnswer'] ) ) {
				$answer = $entity['acceptedAnswer']['text'];
			}

			$faq = sanitize_faq_item(
				array(
					'question' => $question,
					'answer'   => $answer,
				)
			);

			if ( ! is_valid_faq_item( $faq ) ) {
				++$invalid_count;
				continue;
			}

			$items[] = $faq;
		}

		if ( empty( $items ) ) {
			return array(
				'success'       => false,
				'recognized'    => true,
				'items'         => array(),
				'invalid_count' => $invalid_count,
				'error'         => 'no_valid_jsonld_faqs',
			);
		}

		return array(
			'success'       => true,
			'recognized'    => true,
			'items'         => $items,
			'invalid_count' => $invalid_count,
			'error'         => '',
		);
	}

	/**
	 * Parses Q:/A: text blocks with multiline answers.
	 *
	 * @param string $raw_value Raw textarea value.
	 * @return array<string, mixed>
	 */
	private function parse_qa_payload( $raw_value ) {
		$lines         = preg_split( '/\n/u', normalize_line_endings( $raw_value ) );
		$items         = array();
		$invalid_count = 0;
		$question      = '';
		$answer_lines  = array();
		$has_markers   = false;

		$flush_item = static function () use ( &$items, &$invalid_count, &$question, &$answer_lines ) {
			if ( '' === $question && empty( $answer_lines ) ) {
				return;
			}

			$faq = sanitize_faq_item(
				array(
					'question' => $question,
					'answer'   => implode( "\n", $answer_lines ),
				)
			);

			if ( is_valid_faq_item( $faq ) ) {
				$items[] = $faq;
			} else {
				++$invalid_count;
			}

			$question     = '';
			$answer_lines = array();
		};

		foreach ( (array) $lines as $line ) {
			$trimmed = trim( $line );

			if ( preg_match( '/^Q\s*:\s*(.+)$/iu', $trimmed, $matches ) ) {
				$has_markers = true;
				$flush_item();
				$question = $matches[1];
				continue;
			}

			if ( preg_match( '/^A\s*:\s*(.*)$/iu', $trimmed, $matches ) ) {
				$has_markers = true;

				if ( '' === $question ) {
					++$invalid_count;
					continue;
				}

				$answer_lines[] = $matches[1];
				continue;
			}

			if ( '' === $trimmed ) {
				if ( ! empty( $answer_lines ) ) {
					$answer_lines[] = '';
				}
				continue;
			}

			if ( '' !== $question && empty( $answer_lines ) ) {
				$question .= ' ' . $trimmed;
				continue;
			}

			if ( ! empty( $answer_lines ) ) {
				$answer_lines[] = trim( $line );
			}
		}

		$flush_item();

		if ( ! $has_markers || empty( $items ) ) {
			return array(
				'success'      => false,
				'recognized'   => false,
				'items'        => array(),
				'invalid_count' => $invalid_count,
				'error'        => 'invalid_format',
			);
		}

		return array(
			'success'      => true,
			'recognized'   => true,
			'items'        => $items,
			'invalid_count' => $invalid_count,
			'error'        => '',
		);
	}

	/**
	 * Merges imported FAQ items with the existing official list.
	 *
	 * @param array<int, array{question:string,answer:string}> $existing_items Existing FAQs.
	 * @param array<int, array{question:string,answer:string}> $imported_items Imported FAQs.
	 * @return array<string, mixed>
	 */
	private function merge_faqs( array $existing_items, array $imported_items ) {
		$items           = $existing_items;
		$added_items     = array();
		$seen_questions  = array();
		$imported_count  = 0;
		$duplicate_count = 0;

		foreach ( $existing_items as $item ) {
			$key = normalize_question( $item['question'] );

			if ( '' !== $key ) {
				$seen_questions[ $key ] = true;
			}
		}

		foreach ( $imported_items as $item ) {
			$key = normalize_question( $item['question'] );

			if ( '' === $key || isset( $seen_questions[ $key ] ) ) {
				++$duplicate_count;
				continue;
			}

			$seen_questions[ $key ] = true;
			$items[]                = $item;
			$added_items[]          = $item;
			++$imported_count;
		}

		return array(
			'items'           => $items,
			'added_items'     => $added_items,
			'imported_count'  => $imported_count,
			'duplicate_count' => $duplicate_count,
		);
	}

	/**
	 * Persists imported FAQ data and resets the import helper fields.
	 *
	 * @param int   $post_id          Post ID.
	 * @param array $faq_items        Repeater rows to save.
	 * @param bool  $update_repeater  Whether the repeater should be updated.
	 */
	private function persist_import( $post_id, array $faq_items, $update_repeater ) {
		$this->processing = true;

		try {
			if ( $update_repeater ) {
				update_field( Acf_Fields::FIELD_FAQ_ITEMS_KEY, $this->format_acf_repeater_rows( $faq_items ), $post_id );
			}

			update_field( Acf_Fields::FIELD_IMPORT_RAW_KEY, '', $post_id );
			update_field( Acf_Fields::FIELD_IMPORT_MODE_KEY, 'append', $post_id );
		} finally {
			$this->processing = false;
		}
	}

	/**
	 * Persists one modified flexible FAQ layout row.
	 *
	 * @param int   $post_id        Post ID.
	 * @param array $field_object   Flexible field object.
	 * @param array $layout_map     Flexible layout field-key map.
	 * @param int   $row_number     1-based flexible row number.
	 * @param array $repeater_rows  Flexible repeater rows ready for ACF.
	 * @param bool  $force_faq_type Whether tipo_contenuto must be forced to FAQ.
	 */
	private function persist_flexible_layout_row( $post_id, array $field_object, array $layout_map, $row_number, array $repeater_rows, $force_faq_type ) {
		$this->processing = true;

		try {
			$flex_selector        = ! empty( $field_object['key'] ) ? $field_object['key'] : Acf_Fields::FLEX_FIELD_NAME;
			$repeater_selector    = ! empty( $layout_map['repeater_key'] ) ? $layout_map['repeater_key'] : Acf_Fields::FLEX_LAYOUT_REPEATER_NAME;
			$import_raw_selector  = ! empty( $layout_map['import_raw_key'] ) ? $layout_map['import_raw_key'] : Acf_Fields::FLEX_IMPORT_RAW_NAME;
			$import_mode_selector = ! empty( $layout_map['import_mode_key'] ) ? $layout_map['import_mode_key'] : Acf_Fields::FLEX_IMPORT_MODE_NAME;

			update_sub_field(
				array( $flex_selector, $row_number, $repeater_selector ),
				$repeater_rows,
				$post_id
			);

			update_sub_field(
				array( $flex_selector, $row_number, $import_raw_selector ),
				'',
				$post_id
			);

			update_sub_field(
				array( $flex_selector, $row_number, $import_mode_selector ),
				'append',
				$post_id
			);

			if ( $force_faq_type ) {
				$content_type_selector = ! empty( $layout_map['content_type_key'] ) ? $layout_map['content_type_key'] : Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_NAME;

				update_sub_field(
					array( $flex_selector, $row_number, $content_type_selector ),
					Acf_Fields::FLEX_LAYOUT_CONTENT_TYPE_FAQ_VALUE,
					$post_id
				);
			}
		} finally {
			$this->processing = false;
		}
	}

	/**
	 * Converts sanitized FAQ rows into the structure expected by ACF.
	 *
	 * @param array<int, array{question:string,answer:string}> $faq_items FAQ rows.
	 * @return array<int, array<string, string>>
	 */
	private function format_acf_repeater_rows( array $faq_items ) {
		$rows = array();

		foreach ( $faq_items as $item ) {
			$rows[] = array(
				Acf_Fields::FIELD_QUESTION_KEY => $item['question'],
				Acf_Fields::FIELD_ANSWER_KEY   => $item['answer'],
			);
		}

		return $rows;
	}

	/**
	 * Converts sanitized FAQ rows into the existing flexible layout repeater shape.
	 *
	 * @param array<int, array{question:string,answer:string}> $faq_items FAQ rows.
	 * @return array<int, array<string, string>>
	 */
	private function format_flexible_layout_repeater_rows( array $faq_items, array $layout_map = array() ) {
		$rows = array();
		$question_selector = ! empty( $layout_map['question_key'] ) ? $layout_map['question_key'] : Acf_Fields::FLEX_LAYOUT_QUESTION_NAME;
		$answer_selector   = ! empty( $layout_map['answer_key'] ) ? $layout_map['answer_key'] : Acf_Fields::FLEX_LAYOUT_ANSWER_NAME;

		foreach ( $faq_items as $item ) {
			$rows[] = array(
				$question_selector => sprintf( '<h3>%s</h3>', esc_html( $item['question'] ) ),
				$answer_selector   => wpautop( esc_html( $item['answer'] ) ),
			);
		}

		return $rows;
	}

	/**
	 * Builds a parse error message.
	 *
	 * @param array<string, mixed> $parsed_result Parse result.
	 */
	private function get_parse_error_message( array $parsed_result ) {
		switch ( $parsed_result['error'] ) {
			case 'unsupported_json_structure':
				return __( 'Il JSON deve essere un array semplice di oggetti con chiavi "question" e "answer".', plugin_textdomain() );

			case 'no_valid_jsonld_faqs':
				return __( 'Il JSON-LD FAQPage incollato non contiene coppie domanda/risposta valide. Il repeater non è stato modificato.', plugin_textdomain() );

			case 'no_valid_faqs':
				return __( 'Nel campo di import non sono state trovate FAQ valide. Il repeater non è stato modificato.', plugin_textdomain() );

			case 'invalid_json':
				return __( 'Il JSON incollato non è valido. Usa un array JSON semplice, un FAQPage JSON-LD o il formato Q:/A:.', plugin_textdomain() );

			case 'invalid_format':
			default:
				return __( 'Nessuna FAQ valida trovata. Usa un array JSON semplice, un FAQPage JSON-LD o il formato Q:/A:.', plugin_textdomain() );
		}
	}

	/**
	 * Builds a completion message for the editor.
	 *
	 * @param string $mode            append|replace.
	 * @param int    $imported_count  Imported items.
	 * @param int    $duplicate_count Duplicate items ignored.
	 * @param int    $invalid_count   Invalid items skipped.
	 */
	private function get_completion_message( $mode, $imported_count, $duplicate_count, $invalid_count ) {
		$parts = array();

		if ( $imported_count > 0 ) {
			$parts[] = sprintf(
				_n( '%d FAQ importata.', '%d FAQ importate.', $imported_count, plugin_textdomain() ),
				$imported_count
			);

			if ( 'replace' === $mode ) {
				$parts[] = __( 'Le FAQ esistenti sono state sostituite.', plugin_textdomain() );
			}
		} else {
			$parts[] = __( 'Nessuna nuova FAQ è stata importata.', plugin_textdomain() );
		}

		if ( $duplicate_count > 0 ) {
			$parts[] = sprintf(
				_n( '%d duplicato ignorato.', '%d duplicati ignorati.', $duplicate_count, plugin_textdomain() ),
				$duplicate_count
			);
		}

		if ( $invalid_count > 0 ) {
			$parts[] = sprintf(
				_n( '%d elemento non valido ignorato.', '%d elementi non validi ignorati.', $invalid_count, plugin_textdomain() ),
				$invalid_count
			);
		}

		return implode( ' ', $parts );
	}

	/**
	 * Chooses the best notice type for an import result.
	 *
	 * @param int $imported_count Imported items.
	 * @param int $duplicate_count Duplicate items ignored.
	 * @param int $invalid_count Invalid items skipped.
	 */
	private function get_completion_notice_type( $imported_count, $duplicate_count, $invalid_count ) {
		if ( $imported_count > 0 && 0 === $duplicate_count && 0 === $invalid_count ) {
			return 'success';
		}

		if ( $imported_count > 0 || $duplicate_count > 0 || $invalid_count > 0 ) {
			return 'warning';
		}

		return 'info';
	}

	/**
	 * Chooses the strongest notice type from multiple results.
	 *
	 * @param array<int,string> $types Notice types.
	 */
	private function combine_notice_types( array $types ) {
		$priority = array(
			'error'   => 4,
			'warning' => 3,
			'success' => 2,
			'info'    => 1,
		);

		$winner      = 'info';
		$winner_rank = 0;

		foreach ( $types as $type ) {
			$rank = $priority[ $type ] ?? 0;

			if ( $rank > $winner_rank ) {
				$winner      = $type;
				$winner_rank = $rank;
			}
		}

		return $winner;
	}
}
