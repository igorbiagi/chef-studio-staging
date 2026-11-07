<?php
/**
 * Frontend shortcodes.
 *
 * @package ChefStudio\GREW
 */

namespace ChefStudio\GREW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders all frontend shortcodes.
 */
final class Shortcodes {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Render count for unique section IDs.
	 *
	 * @var int
	 */
	private int $render_count = 0;

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
		add_shortcode( 'chef_google_reviews', array( $this, 'render_reviews_shortcode' ) );
		add_shortcode( 'chef_google_reviews_summary', array( $this, 'render_summary_shortcode' ) );
		add_shortcode( 'chef_google_reviews_schema', array( $this, 'render_schema_shortcode' ) );
	}

	/**
	 * Render the main reviews shortcode.
	 *
	 * @param array<string,mixed>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_reviews_shortcode( array|string $atts = array() ): string {
		csgrew_enqueue_assets();

		$settings = csgrew_get_settings();
		$data     = csgrew_get_public_data();
		$args     = $this->normalize_reviews_args( is_array( $atts ) ? $atts : array(), $settings );
		$args['from_shortcode'] = true;
		$reviews  = $this->get_filtered_reviews( $data, $args );

		if ( empty( $data['reviews'] ) ) {
			return $this->render_empty_message();
		}

		if ( empty( $reviews ) ) {
			return current_user_can( 'manage_options' )
				? '<p class="chef-google-reviews-empty">' . esc_html__( 'Nessuna recensione Google corrisponde ai filtri configurati.', 'chef-studio-google-reviews' ) . '</p>'
				: '';
		}

		$this->render_count++;
		$section_id = 1 === $this->render_count ? 'google-reviews' : 'google-reviews-' . $this->render_count;
		$layout     = $args['layout'];

		ob_start();
		?>
		<section class="chef-google-reviews chef-google-reviews-layout-<?php echo esc_attr( $layout ); ?>" id="<?php echo esc_attr( $section_id ); ?>">
			<div class="chef-google-reviews-inner">
				<?php if ( $args['summary'] ) : ?>
					<aside class="chef-google-reviews-summary">
						<?php echo $this->render_business_summary( $data, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</aside>
				<?php endif; ?>

				<div class="chef-google-reviews-carousel-wrap">
					<?php if ( 'slider' === $layout ) : ?>
						<button class="chef-google-reviews-nav chef-google-reviews-prev" type="button" aria-label="<?php esc_attr_e( 'Recensione precedente', 'chef-studio-google-reviews' ); ?>"></button>
					<?php endif; ?>

					<div class="chef-google-reviews-track" tabindex="0">
						<?php foreach ( $reviews as $review ) : ?>
							<?php echo $this->render_review_card( $review, $settings, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					</div>

					<?php if ( 'slider' === $layout ) : ?>
						<button class="chef-google-reviews-nav chef-google-reviews-next" type="button" aria-label="<?php esc_attr_e( 'Recensione successiva', 'chef-studio-google-reviews' ); ?>"></button>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $args['show_source_note'] ) : ?>
				<?php echo $this->render_source_note( $settings, $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</section>
		<?php
		$html = (string) ob_get_clean();

		if ( $args['schema'] && ! empty( $settings['enable_schema'] ) ) {
			$html .= Schema::instance()->render_json_ld( $reviews, $args );
		}

		return $html;
	}

	/**
	 * Render summary-only shortcode.
	 *
	 * @param array<string,mixed>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_summary_shortcode( array|string $atts = array() ): string {
		csgrew_enqueue_assets();

		$settings = csgrew_get_settings();
		$data     = csgrew_get_public_data();

		if ( empty( $data['reviews'] ) && empty( $data['totalReviewCount'] ) ) {
			return $this->render_empty_message();
		}

		return '<section class="chef-google-reviews chef-google-reviews-summary-only">' . $this->render_business_summary( $data, $settings ) . '</section>';
	}

	/**
	 * Render schema-only shortcode.
	 *
	 * @param array<string,mixed>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_schema_shortcode( array|string $atts = array() ): string {
		$settings = csgrew_get_settings();

		if ( empty( $settings['enable_schema'] ) ) {
			return '';
		}

		$data    = csgrew_get_public_data();
		$args    = $this->normalize_reviews_args( is_array( $atts ) ? $atts : array(), $settings );
		$args['from_shortcode'] = true;
		$reviews = $this->get_filtered_reviews( $data, $args );

		if ( empty( $reviews ) ) {
			return '';
		}

		return Schema::instance()->render_json_ld( $reviews, $args );
	}

	/**
	 * Normalize main shortcode attributes.
	 *
	 * @param array<string,mixed> $atts     Attributes.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	private function normalize_reviews_args( array $atts, array $settings ): array {
		$atts = shortcode_atts(
			array(
				'limit'            => absint( $settings['shortcode_limit'] ),
				'layout'           => 'slider',
				'schema'           => 'true',
				'summary'          => 'true',
				'min_rating'       => absint( $settings['min_rating'] ),
				'show_source_note' => 'true',
				'show_read_more'   => ! empty( $settings['show_read_more'] ) ? 'true' : 'false',
				'max_chars'        => min( 135, max( 40, absint( $settings['read_more_chars'] ) ) ),
				'only_with_text'   => ! empty( $settings['only_with_text'] ) ? 'true' : 'false',
			),
			$atts,
			'chef_google_reviews'
		);

		$layout = sanitize_key( (string) $atts['layout'] );
		if ( ! in_array( $layout, array( 'slider', 'grid', 'list' ), true ) ) {
			$layout = 'slider';
		}

		return array(
			'limit'            => max( 1, absint( $atts['limit'] ) ),
			'layout'           => $layout,
			'schema'           => csgrew_bool( $atts['schema'], true ),
			'summary'          => csgrew_bool( $atts['summary'], true ),
			'min_rating'       => max( 1, min( 5, absint( $atts['min_rating'] ) ) ),
			'show_source_note' => csgrew_bool( $atts['show_source_note'], true ),
			'show_read_more'   => csgrew_bool( $atts['show_read_more'], true ),
			'max_chars'        => max( 40, absint( $atts['max_chars'] ) ),
			'only_with_text'   => csgrew_bool( $atts['only_with_text'], ! empty( $settings['only_with_text'] ) ),
		);
	}

	/**
	 * Return filtered reviews matching display args.
	 *
	 * @param array<string,mixed> $data Cache data.
	 * @param array<string,mixed> $args Display args.
	 * @return array<int,array<string,mixed>>
	 */
	private function get_filtered_reviews( array $data, array $args ): array {
		$settings   = csgrew_get_settings();
		$hidden_ids = isset( $settings['hidden_review_ids'] ) && is_array( $settings['hidden_review_ids'] ) ? array_map( 'strval', $settings['hidden_review_ids'] ) : array();
		$reviews    = array();

		foreach ( $data['reviews'] as $review ) {
			if ( ! is_array( $review ) ) {
				continue;
			}

			$review_id = isset( $review['reviewId'] ) ? (string) $review['reviewId'] : '';
			$rating  = isset( $review['starRating'] ) ? absint( $review['starRating'] ) : 0;
			$comment = isset( $review['comment'] ) ? trim( (string) $review['comment'] ) : '';

			if ( '' !== $review_id && in_array( $review_id, $hidden_ids, true ) ) {
				continue;
			}

			if ( $rating < $args['min_rating'] ) {
				continue;
			}

			if ( $args['only_with_text'] && '' === $comment ) {
				continue;
			}

			$review['comment'] = $this->clean_display_comment( $comment );
			$reviews[] = $review;

			if ( count( $reviews ) >= $args['limit'] ) {
				break;
			}
		}

		return $reviews;
	}

	/**
	 * Render business summary block.
	 *
	 * @param array<string,mixed> $data     Cache data.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function render_business_summary( array $data, array $settings ): string {
		$business_name = (string) $settings['business_name'];
		$business_url  = ! empty( $settings['business_url'] ) ? (string) $settings['business_url'] : (string) $data['business_url'];
		$logo_url      = csgrew_resolve_logo_url( $settings['logo'] );

		if ( '' === $logo_url ) {
			$logo_url = $this->get_fallback_logo_url();
		}
		$average       = (float) $data['averageRating'];
		$total         = absint( $data['totalReviewCount'] );

		ob_start();
		?>
		<div class="chef-google-reviews-business">
			<?php if ( '' !== $logo_url ) : ?>
				<img class="chef-google-reviews-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $business_name ); ?>">
			<?php endif; ?>

			<strong class="chef-google-reviews-business-name"><?php echo esc_html( $business_name ); ?></strong>

			<?php echo $this->render_stars( $average, 'chef-google-reviews-stars', sprintf( __( 'Valutazione media %s su 5', 'chef-studio-google-reviews' ), number_format_i18n( $average, 1 ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( '' !== $business_url ) : ?>
				<a class="chef-google-reviews-count" href="<?php echo esc_url( $business_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php
					printf(
						/* translators: %d is review count. */
						esc_html( _n( '%d recensione su Google', '%d recensioni su Google', $total, 'chef-studio-google-reviews' ) ),
						$total
					);
					?>
				</a>
			<?php else : ?>
				<span class="chef-google-reviews-count">
					<?php
					printf(
						/* translators: %d is review count. */
						esc_html( _n( '%d recensione su Google', '%d recensioni su Google', $total, 'chef-studio-google-reviews' ) ),
						$total
					);
					?>
				</span>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render one review card.
	 *
	 * @param array<string,mixed> $review   Review data.
	 * @param array<string,mixed> $settings Settings.
	 * @param array<string,mixed> $args     Display args.
	 * @return string
	 */
	private function render_review_card( array $review, array $settings, array $args ): string {
		$author     = ! empty( $review['reviewerName'] ) ? (string) $review['reviewerName'] : __( 'Utente Google', 'chef-studio-google-reviews' );
		$avatar     = ! empty( $review['reviewerPhotoUrl'] ) ? (string) $review['reviewerPhotoUrl'] : '';
		$rating     = isset( $review['starRating'] ) ? absint( $review['starRating'] ) : 0;
		$date       = ! empty( $review['relativeTimeDescription'] ) ? (string) $review['relativeTimeDescription'] : '';
		$comment    = ! empty( $review['comment'] ) ? (string) $review['comment'] : '';
		$show_more  = $args['show_read_more'] && $this->text_length( $comment ) > $args['max_chars'];
		$short_text = $show_more ? $this->trim_text( $comment, $args['max_chars'] ) : $comment;

		if ( '' === $date ) {
			$date_basis = ! empty( $review['createTime'] ) ? (string) $review['createTime'] : (string) ( $review['updateTime'] ?? '' );
			$date       = '' !== $date_basis ? csgrew_relative_time( $date_basis ) : '';
		}

		ob_start();
		?>
		<article class="chef-google-review-card">
			<header class="chef-google-review-header">
				<?php if ( ! empty( $settings['show_avatar'] ) ) : ?>
					<?php if ( '' !== $avatar ) : ?>
						<img class="chef-google-review-avatar" src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Foto profilo di %s', 'chef-studio-google-reviews' ), $author ) ); ?>">
					<?php else : ?>
						<span class="chef-google-review-avatar chef-google-review-avatar-placeholder" aria-hidden="true"><?php echo esc_html( $this->get_initial( $author ) ); ?></span>
					<?php endif; ?>
				<?php endif; ?>

				<div class="chef-google-review-meta">
					<strong class="chef-google-review-author"><?php echo esc_html( $author ); ?></strong>
					<?php if ( '' !== $date ) : ?>
						<div class="chef-google-review-date"><?php echo esc_html( $date ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $settings['show_google_logo'] ) ) : ?>
					<span class="chef-google-review-source" role="img" aria-label="<?php esc_attr_e( 'Recensione pubblicata su Google', 'chef-studio-google-reviews' ); ?>"><?php echo $this->render_google_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
			</header>

			<div class="chef-google-review-rating-row">
				<?php echo $this->render_stars( $rating, 'chef-google-review-stars', sprintf( __( 'Valutazione %d su 5', 'chef-studio-google-reviews' ), $rating ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $this->render_verified_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( '' !== $comment ) : ?>
				<div class="chef-google-review-text<?php echo $show_more ? ' is-collapsed' : ''; ?>" data-full-text="<?php echo esc_attr( $comment ); ?>" data-short-text="<?php echo esc_attr( $short_text ); ?>" data-max-chars="<?php echo esc_attr( (string) $args['max_chars'] ); ?>"><?php echo esc_html( $short_text ); ?></div>
			<?php endif; ?>

			<?php if ( $show_more ) : ?>
				<button class="chef-google-review-more" type="button" hidden data-more-label="<?php esc_attr_e( 'Leggi di più', 'chef-studio-google-reviews' ); ?>" data-less-label="<?php esc_attr_e( 'Mostra meno', 'chef-studio-google-reviews' ); ?>">
					<?php esc_html_e( 'Leggi di più', 'chef-studio-google-reviews' ); ?>
				</button>
			<?php endif; ?>
		</article>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render source note.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param array<string,mixed> $data     Cache data.
	 * @return string
	 */
	private function render_source_note( array $settings, array $data ): string {
		$business_url = ! empty( $settings['business_url'] ) ? (string) $settings['business_url'] : (string) $data['business_url'];
		$note         = (string) $settings['source_note'];

		ob_start();
		?>
		<p class="chef-google-reviews-source-note">
			<?php echo esc_html( $note ); ?>
			<?php if ( '' !== $business_url ) : ?>
				<a href="<?php echo esc_url( $business_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Leggi tutte le recensioni su Google', 'chef-studio-google-reviews' ); ?>
				</a>
			<?php endif; ?>
		</p>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render accessible stars.
	 *
	 * @param float|int $rating     Rating.
	 * @param string    $class_name CSS class.
	 * @param string    $aria_label Aria label.
	 * @return string
	 */
	private function render_stars( float|int $rating, string $class_name, string $aria_label ): string {
		$rating = max( 0, min( 5, (float) $rating ) );
		$html   = '<div class="' . esc_attr( $class_name ) . '" role="img" aria-label="' . esc_attr( $aria_label ) . '">';
		$star   = CSGREW_URL . 'star.svg';

		for ( $i = 1; $i <= 5; $i++ ) {
			$class = $i <= round( $rating ) ? 'chef-google-reviews-star chef-google-reviews-star-filled' : 'chef-google-reviews-star';
			$html .= '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $star ) . '" alt="' . esc_attr__( 'Stella valutazione', 'chef-studio-google-reviews' ) . '" aria-hidden="true" loading="lazy" decoding="async">';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render the verified badge icon.
	 *
	 * @return string
	 */
	private function render_verified_icon(): string {
		$icon = CSGREW_URL . 'verified.svg';

		return '<span class="chef-google-review-verified" aria-hidden="true"><img class="chef-google-review-verified-icon" src="' . esc_url( $icon ) . '" alt="' . esc_attr__( 'icona verificato', 'chef-studio-google-reviews' ) . '" loading="lazy" decoding="async"></span>';
	}

	/**
	 * Get a safe fallback logo from the active theme when plugin logo is empty.
	 *
	 * @return string
	 */
	private function get_fallback_logo_url(): string {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$custom_logo_url = wp_get_attachment_image_url( absint( $custom_logo_id ), 'thumbnail' );

			if ( $custom_logo_url ) {
				return $custom_logo_url;
			}
		}

		$site_icon_url = get_site_icon_url( 128 );

		return $site_icon_url ? $site_icon_url : '';
	}

	/**
	 * Render a compact Google G icon.
	 *
	 * @return string
	 */
	private function render_google_icon(): string {
		return '<svg class="chef-google-review-google-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#fbbc05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/><path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38z"/></svg>';
	}

	/**
	 * Remove Google auto-translation suffix from displayed text.
	 *
	 * @param string $comment Raw comment.
	 * @return string
	 */
	private function clean_display_comment( string $comment ): string {
		$comment = preg_replace( '/\s*\(Translated by Google\).*$/su', '', $comment );
		$comment = preg_replace( '/\s*\(Original\).*$/su', '', (string) $comment );

		return trim( (string) $comment );
	}

	/**
	 * Render empty frontend/admin fallback.
	 *
	 * @return string
	 */
	private function render_empty_message(): string {
		if ( current_user_can( 'manage_options' ) ) {
			return '<p class="chef-google-reviews-empty">' . esc_html__( 'Recensioni Google non ancora disponibili. Configura il plugin e aggiorna le recensioni.', 'chef-studio-google-reviews' ) . '</p>';
		}

		return '';
	}

	/**
	 * Get a safe author initial.
	 *
	 * @param string $name Name.
	 * @return string
	 */
	private function get_initial( string $name ): string {
		$name = trim( $name );

		if ( '' === $name ) {
			return 'G';
		}

		return function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : strtoupper( substr( $name, 0, 1 ) );
	}

	/**
	 * Multibyte-safe string length.
	 *
	 * @param string $text Text.
	 * @return int
	 */
	private function text_length( string $text ): int {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text );
	}

	/**
	 * Multibyte-safe text trim.
	 *
	 * @param string $text  Text.
	 * @param int    $limit Character limit.
	 * @return string
	 */
	private function trim_text( string $text, int $limit ): string {
		if ( $this->text_length( $text ) <= $limit ) {
			return $text;
		}

		$trimmed = function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $limit ) : substr( $text, 0, $limit );

		return rtrim( (string) $trimmed ) . '...';
	}
}
