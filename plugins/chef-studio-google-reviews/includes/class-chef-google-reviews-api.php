<?php
/**
 * Google Business Profile API client and cache manager.
 *
 * @package ChefStudio\GREW
 */

namespace ChefStudio\GREW;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles OAuth token exchange, Google reviews fetch and public cache writes.
 */
final class API {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Transient key for short-lived access tokens.
	 */
	private const ACCESS_TOKEN_TRANSIENT = 'csgrew_access_token';

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
		add_action( CSGREW_CRON_HOOK, array( $this, 'refresh_reviews' ) );
	}

	/**
	 * Refresh cached Google reviews.
	 *
	 * @return bool|\WP_Error
	 */
	public function refresh_reviews(): bool|\WP_Error {
		$settings = csgrew_get_settings();

		$missing = $this->get_missing_credentials( $settings );
		if ( ! empty( $missing ) ) {
			$error = new \WP_Error(
				'csgrew_missing_credentials',
				sprintf(
					/* translators: %s is a comma-separated list of missing fields. */
					__( 'Credenziali mancanti: %s.', 'chef-studio-google-reviews' ),
					implode( ', ', $missing )
				)
			);
			$this->record_error( $error->get_error_message() );
			return $error;
		}

		$access_token = $this->get_access_token( $settings );

		if ( is_wp_error( $access_token ) ) {
			$this->record_error( $access_token->get_error_message() );
			return $access_token;
		}

		$cache = $this->fetch_reviews( $settings, $access_token );

		if ( is_wp_error( $cache ) ) {
			$this->record_error( $cache->get_error_message() );
			return $cache;
		}

		update_option( CSGREW_DATA_OPTION, $cache, false );

		return true;
	}

	/**
	 * Clear the short-lived access token transient.
	 *
	 * @return void
	 */
	public function clear_access_token(): void {
		delete_transient( self::ACCESS_TOKEN_TRANSIENT );
	}

	/**
	 * Store a public-safe error in the data option without clearing old reviews.
	 *
	 * @param string $message Error message.
	 * @return void
	 */
	public function record_error( string $message ): void {
		$data               = csgrew_get_public_data();
		$data['last_error'] = sanitize_text_field( wp_strip_all_tags( $message ) );

		update_option( CSGREW_DATA_OPTION, $data, false );
	}

	/**
	 * Get an OAuth access token using the refresh token.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string|\WP_Error
	 */
	private function get_access_token( array $settings ): string|\WP_Error {
		$stored_token = get_transient( self::ACCESS_TOKEN_TRANSIENT );

		if ( is_string( $stored_token ) && '' !== $stored_token ) {
			return $stored_token;
		}

		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 20,
				'body'    => array(
					'client_id'     => (string) $settings['client_id'],
					'client_secret' => (string) $settings['client_secret'],
					'refresh_token' => (string) $settings['refresh_token'],
					'grant_type'    => 'refresh_token',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'csgrew_token_request_failed',
				sprintf(
					/* translators: %s is a WordPress HTTP error. */
					__( 'Richiesta token OAuth non riuscita: %s', 'chef-studio-google-reviews' ),
					$response->get_error_message()
				)
			);
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( $body, true );

		if ( $code < 200 || $code >= 300 || ! is_array( $json ) || empty( $json['access_token'] ) ) {
			$message = $this->extract_google_error_message( is_array( $json ) ? $json : array(), __( 'impossibile ottenere un access token.', 'chef-studio-google-reviews' ) );

			return new \WP_Error(
				'csgrew_token_error',
				sprintf(
					/* translators: %s is a Google API error message. */
					__( 'Errore OAuth Google: %s', 'chef-studio-google-reviews' ),
					$message
				)
			);
		}

		$expires_in = isset( $json['expires_in'] ) ? absint( $json['expires_in'] ) : HOUR_IN_SECONDS;
		$ttl        = $expires_in > MINUTE_IN_SECONDS ? min( $expires_in - MINUTE_IN_SECONDS, HOUR_IN_SECONDS ) : max( 1, $expires_in );
		$token      = (string) $json['access_token'];

		set_transient( self::ACCESS_TOKEN_TRANSIENT, $token, $ttl );

		return $token;
	}

	/**
	 * Fetch reviews with pagination up to the configured limit.
	 *
	 * @param array<string,mixed> $settings     Settings.
	 * @param string             $access_token OAuth access token.
	 * @return array<string,mixed>|\WP_Error
	 */
	private function fetch_reviews( array $settings, string $access_token ): array|\WP_Error {
		$limit       = max( 1, min( 500, absint( $settings['api_review_limit'] ) ) );
		$account_id  = $this->normalize_resource_id( (string) $settings['account_id'], 'accounts' );
		$location_id = $this->normalize_resource_id( (string) $settings['location_id'], 'locations' );

		if ( '' === $account_id || '' === $location_id ) {
			return new \WP_Error( 'csgrew_invalid_location', __( 'Account ID o Location ID non validi.', 'chef-studio-google-reviews' ) );
		}

		$endpoint = sprintf(
			'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/reviews',
			rawurlencode( $account_id ),
			rawurlencode( $location_id )
		);

		$reviews       = array();
		$raw_debug     = array();
		$page_token    = '';
		$average       = '0.0';
		$total_reviews = 0;
		$page_count    = 0;
		$max_pages     = (int) ceil( $limit / 50 ) + 1;

		do {
			$page_count++;
			$remaining = $limit - count( $reviews );
			$page_size = min( 50, max( 1, $remaining ) );
			$query     = array(
				'pageSize' => $page_size,
				'orderBy'  => 'updateTime desc',
			);

			if ( '' !== $page_token ) {
				$query['pageToken'] = $page_token;
			}

			$response = wp_remote_get(
				add_query_arg( $query, $endpoint ),
				array(
					'timeout' => 20,
					'headers' => array(
						'Authorization' => 'Bearer ' . $access_token,
						'Accept'        => 'application/json',
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				return new \WP_Error(
					'csgrew_api_request_failed',
					sprintf(
						/* translators: %s is a WordPress HTTP error. */
						__( 'Richiesta recensioni Google non riuscita: %s', 'chef-studio-google-reviews' ),
						$response->get_error_message()
					)
				);
			}

			$code = wp_remote_retrieve_response_code( $response );
			$body = wp_remote_retrieve_body( $response );
			$json = json_decode( $body, true );

			if ( $code < 200 || $code >= 300 || ! is_array( $json ) ) {
				$message = $this->extract_google_error_message( is_array( $json ) ? $json : array(), __( 'risposta API recensioni non valida.', 'chef-studio-google-reviews' ) );

				return new \WP_Error(
					'csgrew_api_error',
					sprintf(
						/* translators: %s is a Google API error message. */
						__( 'Errore Google Business Profile API: %s', 'chef-studio-google-reviews' ),
						$message
					)
				);
			}

			$raw_debug[] = $this->sanitize_debug_payload( $json );

			if ( isset( $json['averageRating'] ) ) {
				$average = number_format( (float) $json['averageRating'], 1, '.', '' );
			}

			if ( isset( $json['totalReviewCount'] ) ) {
				$total_reviews = absint( $json['totalReviewCount'] );
			}

			if ( ! empty( $json['reviews'] ) && is_array( $json['reviews'] ) ) {
				foreach ( $json['reviews'] as $review ) {
					if ( count( $reviews ) >= $limit ) {
						break;
					}

					if ( is_array( $review ) ) {
						$reviews[] = $this->normalize_review( $review, (string) $settings['business_url'] );
					}
				}
			}

			$page_token = isset( $json['nextPageToken'] ) ? sanitize_text_field( (string) $json['nextPageToken'] ) : '';
		} while ( '' !== $page_token && count( $reviews ) < $limit && $page_count < $max_pages );

		return array(
			'updated_at'        => time(),
			'averageRating'     => $average,
			'totalReviewCount'  => $total_reviews,
			'reviews'           => $reviews,
			'last_error'        => '',
			'raw_debug'         => $raw_debug,
			'business_url'      => esc_url_raw( (string) $settings['business_url'] ),
		);
	}

	/**
	 * Normalize one Google review into the public cache shape.
	 *
	 * @param array<string,mixed> $review       Raw review.
	 * @param string             $business_url Google Business Profile URL.
	 * @return array<string,mixed>
	 */
	private function normalize_review( array $review, string $business_url ): array {
		$reviewer = isset( $review['reviewer'] ) && is_array( $review['reviewer'] ) ? $review['reviewer'] : array();
		$comment  = isset( $review['comment'] ) ? (string) $review['comment'] : '';
		$comment  = html_entity_decode( wp_strip_all_tags( $comment ), ENT_QUOTES, get_bloginfo( 'charset' ) );
		$comment  = trim( preg_replace( '/\s+/u', ' ', $comment ) ?: '' );

		$create_time = isset( $review['createTime'] ) ? sanitize_text_field( (string) $review['createTime'] ) : '';
		$update_time = isset( $review['updateTime'] ) ? sanitize_text_field( (string) $review['updateTime'] ) : '';
		$date_basis  = '' !== $create_time ? $create_time : $update_time;

		$review_id = isset( $review['reviewId'] ) ? sanitize_text_field( (string) $review['reviewId'] ) : '';

		if ( '' === $review_id && ! empty( $review['name'] ) ) {
			$name_parts = explode( '/', (string) $review['name'] );
			$review_id  = sanitize_text_field( (string) end( $name_parts ) );
		}

		if ( '' === $review_id ) {
			$review_id = md5( wp_json_encode( array( $comment, $create_time, $reviewer['displayName'] ?? '' ) ) ?: uniqid( 'review', true ) );
		}

		return array(
			'reviewId'                => $review_id,
			'reviewerName'            => ! empty( $reviewer['displayName'] ) ? sanitize_text_field( (string) $reviewer['displayName'] ) : __( 'Utente Google', 'chef-studio-google-reviews' ),
			'reviewerPhotoUrl'        => ! empty( $reviewer['profilePhotoUrl'] ) ? esc_url_raw( (string) $reviewer['profilePhotoUrl'] ) : '',
			'starRating'              => $this->map_star_rating( $review['starRating'] ?? '' ),
			'comment'                 => $comment,
			'createTime'              => $create_time,
			'updateTime'              => $update_time,
			'relativeTimeDescription' => '' !== $date_basis ? csgrew_relative_time( $date_basis ) : '',
			'source'                  => 'Google',
			'reviewUrl'               => esc_url_raw( $business_url ),
		);
	}

	/**
	 * Map Google starRating enum to an integer.
	 *
	 * @param mixed $rating Raw rating.
	 * @return int
	 */
	private function map_star_rating( mixed $rating ): int {
		if ( is_numeric( $rating ) ) {
			return max( 0, min( 5, absint( $rating ) ) );
		}

		$map = array(
			'ONE'   => 1,
			'TWO'   => 2,
			'THREE' => 3,
			'FOUR'  => 4,
			'FIVE'  => 5,
		);

		$rating = strtoupper( trim( (string) $rating ) );

		return $map[ $rating ] ?? 0;
	}

	/**
	 * Extract non-sensitive API error text.
	 *
	 * @param array<string,mixed> $payload  JSON payload.
	 * @param string             $fallback Fallback message.
	 * @return string
	 */
	private function extract_google_error_message( array $payload, string $fallback ): string {
		$error             = '';
		$error_description = '';

		if ( ! empty( $payload['error'] ) && is_string( $payload['error'] ) ) {
			$error = sanitize_text_field( $payload['error'] );
		}

		if ( ! empty( $payload['error_description'] ) ) {
			$error_description = sanitize_text_field( (string) $payload['error_description'] );
		}

		if ( '' !== $error && '' !== $error_description ) {
			return $error . ' - ' . $error_description;
		}

		if ( '' !== $error_description ) {
			return $error_description;
		}

		if ( '' !== $error ) {
			return $error;
		}

		if ( ! empty( $payload['error'] ) && is_array( $payload['error'] ) && ! empty( $payload['error']['message'] ) ) {
			return sanitize_text_field( (string) $payload['error']['message'] );
		}

		return sanitize_text_field( $fallback );
	}

	/**
	 * Remove fields that are not useful for admin debug.
	 *
	 * @param array<string,mixed> $payload Raw API payload.
	 * @return array<string,mixed>
	 */
	private function sanitize_debug_payload( array $payload ): array {
		unset( $payload['nextPageToken'], $payload['pageToken'], $payload['access_token'], $payload['refresh_token'], $payload['client_secret'] );

		return $payload;
	}

	/**
	 * Normalize account/location IDs when users paste full resource names.
	 *
	 * @param string $value  Raw value.
	 * @param string $prefix accounts or locations.
	 * @return string
	 */
	private function normalize_resource_id( string $value, string $prefix ): string {
		$value = trim( $value );

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '~(?:^|/)' . preg_quote( $prefix, '~' ) . '/([^/]+)~', $value, $matches ) ) {
			return sanitize_text_field( $matches[1] );
		}

		$value = preg_replace( '~^' . preg_quote( $prefix, '~' ) . '/~', '', $value );

		return sanitize_text_field( (string) $value );
	}

	/**
	 * Find missing required credentials.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,string>
	 */
	private function get_missing_credentials( array $settings ): array {
		$required = array(
			'client_id'     => 'Client ID',
			'client_secret' => 'Client Secret',
			'refresh_token' => 'Refresh Token',
			'account_id'    => 'Account ID',
			'location_id'   => 'Location ID',
		);

		$missing = array();

		foreach ( $required as $key => $label ) {
			if ( empty( $settings[ $key ] ) ) {
				$missing[] = $label;
			}
		}

		return $missing;
	}
}
