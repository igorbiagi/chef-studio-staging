<?php
/**
 * Schema.org JSON-LD and Yoast integration.
 *
 * @package ChefStudio\GREW
 */

namespace ChefStudio\GREW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates review JSON-LD and safely enriches Yoast Organization.
 */
final class Schema {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

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
		add_filter( 'wpseo_schema_organization', array( $this, 'add_aggregate_rating_to_yoast_organization' ) );
	}

	/**
	 * Render ItemList JSON-LD once per page.
	 *
	 * @param array<int,array<string,mixed>> $reviews Displayed reviews.
	 * @param array<string,mixed>            $args    Shortcode args.
	 * @return string
	 */
	public function render_json_ld( array $reviews, array $args = array() ): string {
		$settings = csgrew_get_settings();

		if ( empty( $settings['enable_schema'] ) || empty( $reviews ) ) {
			return '';
		}

		if ( ! empty( $GLOBALS['csgrew_schema_printed'] ) ) {
			return '';
		}

		if ( ! empty( $settings['schema_only_shortcode_pages'] ) && empty( $args['from_shortcode'] ) && ! csgrew_current_page_has_shortcode() ) {
			return '';
		}

		$schema = $this->build_reviews_item_list( $reviews, $settings );
		$json   = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if ( ! $json ) {
			return '';
		}

		$GLOBALS['csgrew_schema_printed'] = true;

		return "\n" . '<script type="application/ld+json">' . $json . '</script>' . "\n";
	}

	/**
	 * Add aggregateRating to Yoast Organization without changing other fields.
	 *
	 * @param array<string,mixed> $data Yoast organization graph node.
	 * @return array<string,mixed>
	 */
	public function add_aggregate_rating_to_yoast_organization( array $data ): array {
		$settings = csgrew_get_settings();

		if ( empty( $settings['enable_yoast_aggregate'] ) || ! $this->is_aggregate_allowed_on_current_page( $settings ) ) {
			return $data;
		}

		$cache  = csgrew_get_public_data();
		$total  = absint( $cache['totalReviewCount'] );
		$rating = (float) $cache['averageRating'];

		if ( $total <= 0 || $rating <= 0 ) {
			return $data;
		}

		$data['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => (string) $cache['averageRating'],
			'bestRating'  => '5',
			'worstRating' => '1',
			'ratingCount' => $total,
			'reviewCount' => $total,
		);

		return $data;
	}

	/**
	 * Build the ItemList schema for displayed reviews.
	 *
	 * @param array<int,array<string,mixed>> $reviews  Displayed reviews.
	 * @param array<string,mixed>            $settings Settings.
	 * @return array<string,mixed>
	 */
	private function build_reviews_item_list( array $reviews, array $settings ): array {
		$business_name = wp_strip_all_tags( (string) $settings['business_name'] );
		$business_url  = esc_url_raw( (string) $settings['business_url'] );
		$site_url      = home_url( '/' );
		$item_list     = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'ItemList',
			'@id'              => home_url( '/#google-reviews' ),
			'name'             => sprintf(
				/* translators: %s is business name. */
				__( 'Recensioni Google di %s', 'chef-studio-google-reviews' ),
				$business_name
			),
			'description'      => sprintf(
				/* translators: %s is business name. */
				__( 'Recensioni pubblicate sul profilo Google Business di %s e visibili in questa pagina.', 'chef-studio-google-reviews' ),
				$business_name
			),
			'url'              => home_url( '/#google-reviews' ),
			'numberOfItems'    => count( $reviews ),
			'mainEntityOfPage' => array(
				'@id' => $site_url,
			),
			'itemListElement'  => array(),
		);

		$position = 1;

		foreach ( $reviews as $review ) {
			$item_list['itemListElement'][] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => $this->build_review_schema( $review, $position, $business_url ),
			);
			$position++;
		}

		return $item_list;
	}

	/**
	 * Build one Review schema object.
	 *
	 * @param array<string,mixed> $review       Review data.
	 * @param int                 $position     Review position.
	 * @param string              $business_url Google Business Profile URL.
	 * @return array<string,mixed>
	 */
	private function build_review_schema( array $review, int $position, string $business_url ): array {
		$review_id = ! empty( $review['reviewId'] ) ? sanitize_title( (string) $review['reviewId'] ) : (string) $position;
		$rating    = isset( $review['starRating'] ) ? max( 1, min( 5, absint( $review['starRating'] ) ) ) : 1;
		$date      = $this->date_to_ymd( (string) ( $review['createTime'] ?? '' ) );

		if ( '' === $date ) {
			$date = $this->date_to_ymd( (string) ( $review['updateTime'] ?? '' ) );
		}

		$schema = array(
			'@type'        => 'Review',
			'@id'          => home_url( '/#google-review-' . $review_id ),
			'url'          => ! empty( $review['reviewUrl'] ) ? esc_url_raw( (string) $review['reviewUrl'] ) : $business_url,
			'itemReviewed' => array(
				'@id' => home_url( '/#organization' ),
			),
			'author'       => array(
				'@type' => 'Person',
				'name'  => ! empty( $review['reviewerName'] ) ? wp_strip_all_tags( (string) $review['reviewerName'] ) : __( 'Utente Google', 'chef-studio-google-reviews' ),
			),
			'reviewRating' => array(
				'@type'       => 'Rating',
				'ratingValue' => (string) $rating,
				'bestRating'  => '5',
				'worstRating' => '1',
			),
			'publisher'    => array(
				'@type' => 'Organization',
				'name'  => 'Google',
				'url'   => 'https://www.google.com/',
			),
			'isBasedOn'    => array(
				'@type' => 'WebPage',
				'url'   => $business_url,
			),
		);

		if ( ! empty( $review['comment'] ) ) {
			$schema['reviewBody'] = wp_strip_all_tags( (string) $review['comment'] );
		}

		if ( '' !== $date ) {
			$schema['datePublished'] = $date;
		}

		return $schema;
	}

	/**
	 * Check whether aggregateRating can be added on the current page.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function is_aggregate_allowed_on_current_page( array $settings ): bool {
		$has_shortcode = $this->current_page_has_display_shortcode();

		if ( $has_shortcode ) {
			return true;
		}

		if ( is_front_page() && ! empty( $settings['front_page_aggregate'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Detect display shortcodes relevant to Yoast aggregateRating.
	 *
	 * @return bool
	 */
	private function current_page_has_display_shortcode(): bool {
		global $post;

		if ( ! $post instanceof \WP_Post || empty( $post->post_content ) ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'chef_google_reviews' )
			|| has_shortcode( $post->post_content, 'chef_google_reviews_summary' );
	}

	/**
	 * Convert an ISO date to YYYY-MM-DD.
	 *
	 * @param string $datetime Raw datetime.
	 * @return string
	 */
	private function date_to_ymd( string $datetime ): string {
		$timestamp = strtotime( $datetime );

		if ( false === $timestamp ) {
			return '';
		}

		return gmdate( 'Y-m-d', $timestamp );
	}
}
