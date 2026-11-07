<?php
/**
 * Uninstall logic.
 *
 * @package ChefStudio\FAQ
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'csfaq_version' );

if ( ! defined( 'CSFAQ_DELETE_DATA_ON_UNINSTALL' ) || ! CSFAQ_DELETE_DATA_ON_UNINSTALL ) {
	return;
}

global $wpdb;

$exact_meta_keys = array(
	'faq_items',
	'_faq_items',
	'faq_import_raw',
	'_faq_import_raw',
	'faq_import_mode',
	'_faq_import_mode',
);

foreach ( $exact_meta_keys as $meta_key ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
			$meta_key
		)
	);
}

$like_patterns = array(
	$wpdb->esc_like( 'faq_items_' ) . '%' . $wpdb->esc_like( '_question' ),
	$wpdb->esc_like( '_faq_items_' ) . '%' . $wpdb->esc_like( '_question' ),
	$wpdb->esc_like( 'faq_items_' ) . '%' . $wpdb->esc_like( '_answer' ),
	$wpdb->esc_like( '_faq_items_' ) . '%' . $wpdb->esc_like( '_answer' ),
);

foreach ( $like_patterns as $pattern ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
			$pattern
		)
	);
}
