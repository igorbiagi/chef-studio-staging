<?php
/**
 * Frontend FAQ renderer.
 *
 * @package ChefStudio\FAQ
 */

namespace ChefStudio\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Appends FAQ markup to eligible singular content.
 */
final class Frontend_Render {

	/**
	 * Tracks posts already rendered in the current request.
	 *
	 * @var array<int, bool>
	 */
	private $rendered_posts = array();

	/**
	 * Registers hooks.
	 */
	public function register() {
		$priority = (int) apply_filters( 'csfaq_frontend_filter_priority', 20 );
		add_filter( 'the_content', array( $this, 'inject_into_content' ), $priority );
		add_shortcode( 'csfaq', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Appends or prepends the FAQ block to the post content.
	 *
	 * @param string $content Original post content.
	 */
	public function inject_into_content( $content ) {
		$post = get_post();
		$is_trusted_loop_render = in_the_loop() && is_main_query();

		if ( ! $post || ! $this->should_render( $post->ID ) ) {
			return $content;
		}

		/*
		 * Only suppress duplicate output for the canonical main-loop render.
		 * Some themes/plugins pre-process the_content() off-loop for TOC,
		 * previews or analysis. Those calls must not consume the one visible
		 * FAQ render that users expect later in the template.
		 */
		if ( $is_trusted_loop_render && isset( $this->rendered_posts[ $post->ID ] ) ) {
			return $content;
		}

		$markup = $this->build_markup( $post->ID );

		if ( '' === $markup ) {
			return $content;
		}

		if ( $is_trusted_loop_render ) {
			$this->rendered_posts[ $post->ID ] = true;
		}

		$position = (string) apply_filters( 'csfaq_frontend_position', 'after_content', $post->ID );

		if ( 'before_content' === $position ) {
			return $markup . $content;
		}

		return $content . $markup;
	}

	/**
	 * Checks whether automatic frontend rendering should happen.
	 *
	 * @param int $post_id Post ID.
	 */
	private function should_render( $post_id ) {
		if ( is_admin() || is_feed() || is_embed() ) {
			return false;
		}

		if ( ! is_singular( get_enabled_post_types() ) ) {
			return false;
		}

		if ( ! is_supported_post_type( get_post_type( $post_id ) ) ) {
			return false;
		}

		if ( ! is_automatic_frontend_render_enabled( $post_id ) ) {
			return false;
		}

		$queried_post_id = get_current_singular_post_id();

		/*
		 * Some themes call the_content() for the queried singular object
		 * outside the canonical main loop. In that case the post content is
		 * still the right one to augment, while the old in_the_loop() /
		 * is_main_query() checks would suppress the FAQ block incorrectly.
		 */
		if ( $queried_post_id && absint( $post_id ) === $queried_post_id ) {
			return true;
		}

		return in_the_loop() && is_main_query();
	}

	/**
	 * Builds the FAQ HTML block.
	 *
	 * @param int $post_id Post ID.
	 */
	private function build_markup( $post_id ) {
		return get_faq_markup( $post_id );
	}

	/**
	 * Renders the FAQ block through shortcode for manual placement.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_id' => 0,
			),
			(array) $atts,
			'csfaq'
		);

		$post_id = absint( $atts['post_id'] );

		if ( ! $post_id ) {
			$post_id = get_the_ID() ? absint( get_the_ID() ) : get_current_singular_post_id();
		}

		return get_faq_markup( $post_id );
	}
}
