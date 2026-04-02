<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Models;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Traits\SingleTonTrait;

class QueryBuilder{
	public function __construct() {
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ],11 );
	}

	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}
		if ('gym_builder_class' === $query->get( 'post_type' )){

			$posts_per_page = SettingsApi::get_option( 'class_posts_per_page', 'gym_builder_class_settings' ) ?: '9';
			$post_in = SettingsApi::get_option( "include_class", "gym_builder_class_settings" ) ?: [];
			$post_not_in = SettingsApi::get_option( "exclude_class", "gym_builder_class_settings" ) ?: [];
			$post_categories = SettingsApi::get_option( "class_categories", "gym_builder_class_settings" ) ?: [];
			$post_orderby = SettingsApi::get_option( "class_orderBy", "gym_builder_class_settings" ) ?: [];
			$post_order = SettingsApi::get_option( "class_order", "gym_builder_class_settings" ) ?: [];
	 
			if ( ! empty( $post_in ) ) {
				$query->set( 'post__in', $post_in );
			}
			if ( ! empty( $post_not_in ) ) {
				$query->set( 'post__not_in',$post_not_in );
			}
			if ( ! empty( $post_categories ) ) {
				$tax_query = array(
						array(
							'taxonomy' => 'gym_builder_class_category', 
							'field'    => 'term_id', 
							'terms'    => $post_categories,
							'operator' => 'IN',
						)
					);
				$query->set( 'tax_query',$tax_query);
			}
			if ( ! empty( $post_orderby ) ) {
				$query->set( 'orderby',$post_orderby );
			}
			if ( ! empty( $post_order ) ) {
				$query->set( 'order',$post_order );
			}
			$query->set('posts_per_page',$posts_per_page);
			return;
		}
		if ('gym_builder_trainer' === $query->get( 'post_type' )){

			$trainer_posts_per_page = SettingsApi::get_option( 'trainer_posts_per_page', 'gym_builder_trainer_settings' ) ?: '9';
			$trainer_post_in = SettingsApi::get_option( "include_trainer", "gym_builder_trainer_settings" ) ?: [];
			$trainer_post_not_in = SettingsApi::get_option( "exclude_trainer", "gym_builder_trainer_settings" ) ?: [];
			$trainer_post_categories = SettingsApi::get_option( "trainer_categories", "gym_builder_trainer_settings" ) ?: [];
			$trainer_post_orderby = SettingsApi::get_option( "trainer_orderBy", "gym_builder_trainer_settings" ) ?: [];
			$trainer_post_order = SettingsApi::get_option( "trainer_order", "gym_builder_trainer_settings" ) ?: [];

			if ( ! empty( $trainer_post_in ) ) {
				$query->set( 'post__in', $trainer_post_in );
			}
			if ( ! empty( $trainer_post_not_in ) ) {
				$query->set( 'post__not_in',$trainer_post_not_in );
			}
			if ( ! empty( $trainer_post_categories ) ) {
				$trainer_tax_query = array(
						array(
							'taxonomy' => 'gym_builder_trainer_category', 
							'field'    => 'term_id', 
							'terms'    => $trainer_post_categories,
							'operator' => 'IN',
						)
					);
				$query->set( 'tax_query',$trainer_tax_query);
			}
			if ( ! empty( $trainer_post_orderby ) ) {
				$query->set( 'orderby',$trainer_post_orderby );
			}
			if ( ! empty( $trainer_post_order ) ) {
				$query->set( 'order',$trainer_post_order );
			}
			$query->set('posts_per_page',$trainer_posts_per_page);
			return;
		}
	}
}
