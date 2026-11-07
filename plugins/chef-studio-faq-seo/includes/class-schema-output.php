<?php
/**
 * FAQPage schema output.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Outputs FAQPage JSON-LD for eligible singular posts.
 */
final class Schema_Output {

	/**
	 * Registers the output hook.
	 */
	public function register() {
		$hook = apply_filters( 'csfaq_schema_hook', 'wp_head' );

		if ( ! is_string( $hook ) || '' === $hook ) {
			$hook = 'wp_head';
		}

		$priority = (int) apply_filters( 'csfaq_schema_priority', 20 );

		add_action( $hook, array( $this, 'output_schema' ), $priority );
	}

	/**
	 * Prints the JSON-LD script tag when the page qualifies.
	 */
	public function output_schema() {
		$post_id = get_current_singular_post_id();

		if ( ! $post_id || ! $this->should_output( $post_id ) ) {
			return;
		}

		$faq_items = get_schema_faq_items( $post_id );

		if ( empty( $faq_items ) ) {
			return;
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array(),
		);

		foreach ( $faq_items as $faq_item ) {
			$schema['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => prepare_question_for_schema( $faq_item['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => prepare_answer_for_schema( $faq_item['answer'] ),
				),
			);
		}

		if ( empty( $schema['mainEntity'] ) ) {
			return;
		}

		$schema = apply_filters( 'csfaq_schema_data', $schema, $post_id, $faq_items );
		$schema = decode_html_entities_deep( $schema );

		/*
		 * Keep Unicode escaped in the final JSON-LD output. This is still valid
		 * JSON and avoids third-party HTML optimizers/buffers turning UTF-8
		 * characters into HTML entities like &#224; inside the script tag.
		 */
		$flags = (int) apply_filters( 'csfaq_schema_json_flags', JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT, $post_id );
		$json   = wp_json_encode( $schema, $flags );

		if ( ! $json ) {
			return;
		}

		printf(
			"\n<script type=\"application/ld+json\" class=\"csfaq-faq-schema\">%s</script>\n",
			$json
		);
	}

	/**
	 * Checks whether schema output is enabled for a post.
	 *
	 * @param int $post_id Post ID.
	 */
	private function should_output( $post_id ) {
		if ( is_admin() || is_feed() || is_embed() ) {
			return false;
		}

		if ( ! is_supported_post_type( get_post_type( $post_id ) ) ) {
			return false;
		}

		if ( ! apply_filters( 'csfaq_enable_schema_output', true, $post_id ) ) {
			return false;
		}

		return are_faqs_visible_on_page( $post_id );
	}
}
