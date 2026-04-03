<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$gym_builder_post_types = [
	'gym_builder_trainer',
	'gym_builder_class',
	'gb_class_shortcode',
	'gb_trainer_shortcode',
	'gb_pricing_plan',
	'gb_fitness_shortcode'
];

foreach ( $gym_builder_post_types as $post_type ) {

	$items = get_posts(
		[
			'post_type'   => $post_type,
			'post_status' => 'any',
			'numberposts' => - 1,
			'fields'      => 'ids'
		]
	);

	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item, true );
		}
	}

}
$gym_builder_taxonomies = [
	'gym_builder_class_category',
	'gym_builder_trainer_category',
	'gb_pricing_plan_category'
];

global $wpdb;


$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}gym_builder_members" );

foreach ( $gym_builder_taxonomies as $taxonomy ) {

	$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

	if ( $terms ) {
		foreach ( $terms as $term ) {
			$wpdb->delete( $wpdb->term_taxonomy, [ 'term_taxonomy_id' => $term->term_taxonomy_id ] );
			$wpdb->delete( $wpdb->terms, [ 'term_id' => $term->term_id ] );
		}
	}

	$wpdb->delete( $wpdb->term_taxonomy, [ 'taxonomy' => $taxonomy ], [ '%s' ] );

}

$class_page   = get_page_by_path( 'classes' );
$trainer_page = get_page_by_path( 'trainers' );

if ($class_page !== null || $trainer_page !== null){
	wp_delete_post( $class_page->ID, true );
	wp_delete_post( $trainer_page->ID, true );
}

$gym_builder_settings = [
	'gym_builder_page_settings',
	'gym_builder_permalinks_settings',
	'gym_builder_class_settings',
	'gym_builder_trainer_settings',
	'gym_builder_style_settings',
	'gym_builder_global_settings',
	'gym_builder_zoom_integration_settings'
];

foreach ( $gym_builder_settings as $settings ) {
	delete_option( $settings );
}

delete_option( 'gym_builder_version' );
delete_option( 'gb_members_db_table_version' );