<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GymBuilder\Inc\Base\BaseController;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Admin\Models\Posts\RegisterPostType;

class AddPostTypes extends BaseController {

	protected static $instance = null;
	private $post_types = array();


	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function init() {

		$class_base = SettingsApi::get_option( 'class_base', 'gym_builder_permalinks_settings' );
		$class_base = $class_base ?: 'gym_builder_class';

		$trainer_base = SettingsApi::get_option( 'trainer_base', 'gym_builder_permalinks_settings' );
		$trainer_base = $trainer_base ?: 'gym_builder_trainer';

		$classes_page_id = Functions::get_page_id( 'classes' );

		$trainers_page_id = Functions::get_page_id( 'trainers' );

		if ( current_theme_supports( 'gym-builder' ) ) {
			$class_has_archive = $classes_page_id && get_post( $classes_page_id ) ? urldecode( get_page_uri( $classes_page_id ) ) : 'classes';
		} else {
			$class_has_archive = false;
		}

		if ( current_theme_supports( 'gym-builder' ) ) {
			$trainer_has_archive = $trainers_page_id && get_post( $trainers_page_id ) ? urldecode( get_page_uri( $trainers_page_id ) ) : 'trainers';
		} else {
			$trainer_has_archive = false;
		}

		$post_types        = array(
			'gym_builder_class'   => array(
				'title'         => __( 'Class', 'gym-builder' ),
				'plural_title'  => __( 'Classes', 'gym-builder' ),
				'menu_icon'     => $this->plugin_url . "assets/admin/images/class-icon-white.png",
				'rewrite'       => $class_base,
				'has_archive'   => $class_has_archive,
				'menu_position' => 60,
				'supports'      => array( 'title', 'thumbnail', 'editor', 'excerpt', 'page-attributes' ),
			),
			'gym_builder_trainer' => array(
				'title'           => __( 'Trainer Member', 'gym-builder' ),
				'plural_title'    => __( 'Trainers', 'gym-builder' ),
				'menu_icon'       => $this->plugin_url . "assets/admin/images/trainer-icon-white.png",
				'menu_position'   => 60,
				'labels_override' => array(
					'menu_name' => __( 'Trainer', 'gym-builder' ),
				),
				'rewrite'         => $trainer_base,
				'has_archive'     => $trainer_has_archive,
				'supports'        => array( 'title', 'thumbnail', 'editor', 'excerpt', 'page-attributes' )
			),
			'gb_class_shortcode'  => array(
				'title'               => __( 'Class Shortcode', 'gym-builder' ),
				'plural_title'        => __( 'Classes Shortcode', 'gym-builder' ),
				'supports'            => [ 'title' ],
				'public'              => false,
				'labels_override'     => array(
					'all_items'          => esc_html__( 'Shortcode Generator', 'gym-builder' ),
					'menu_name'          => esc_html__( 'Shortcode', 'gym-builder' ),
					'singular_name'      => esc_html__( 'Shortcode', 'gym-builder' ),
					'edit_item'          => esc_html__( 'Edit Shortcode', 'gym-builder' ),
					'new_item'           => esc_html__( 'New Shortcode', 'gym-builder' ),
					'view_item'          => esc_html__( 'View Shortcode', 'gym-builder' ),
					'search_items'       => esc_html__( 'Shortcode Locations', 'gym-builder' ),
					'not_found'          => esc_html__( 'No Shortcode found.', 'gym-builder' ),
					'not_found_in_trash' => esc_html__( 'No Shortcode found in trash.', 'gym-builder' ),
				),
				'rewrite'             => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => null,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			),
			'gb_trainer_shortcode'  => array(
				'title'               => __( 'Trainer Shortcode', 'gym-builder' ),
				'plural_title'        => __( 'Trainer Shortcode', 'gym-builder' ),
				'supports'            => [ 'title' ],
				'public'              => false,
				'labels_override'     => array(
					'all_items'          => esc_html__( 'Shortcode Generator', 'gym-builder' ),
					'menu_name'          => esc_html__( 'Shortcode', 'gym-builder' ),
					'singular_name'      => esc_html__( 'Shortcode', 'gym-builder' ),
					'edit_item'          => esc_html__( 'Edit Shortcode', 'gym-builder' ),
					'new_item'           => esc_html__( 'New Shortcode', 'gym-builder' ),
					'view_item'          => esc_html__( 'View Shortcode', 'gym-builder' ),
					'search_items'       => esc_html__( 'Shortcode Locations', 'gym-builder' ),
					'not_found'          => esc_html__( 'No Shortcode found.', 'gym-builder' ),
					'not_found_in_trash' => esc_html__( 'No Shortcode found in trash.', 'gym-builder' ),
				),
				'rewrite'             => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => null,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			),
			'gb_fitness_shortcode'  => array(
				'title'               => __( 'Fitness Calculator Shortcode', 'gym-builder' ),
				'plural_title'        => __( 'Fitness Calculator Shortcode', 'gym-builder' ),
				'supports'            => [ 'title' ],
				'public'              => false,
				'labels_override'     => array(
					'all_items'          => esc_html__( 'Fitness Calculator Generator', 'gym-builder' ),
					'menu_name'          => esc_html__( 'Fitness Calculator', 'gym-builder' ),
					'singular_name'      => esc_html__( 'Shortcode', 'gym-builder' ),
					'edit_item'          => esc_html__( 'Edit Shortcode', 'gym-builder' ),
					'new_item'           => esc_html__( 'New Shortcode', 'gym-builder' ),
					'view_item'          => esc_html__( 'View Shortcode', 'gym-builder' ),
					'search_items'       => esc_html__( 'Shortcode Locations', 'gym-builder' ),
					'not_found'          => esc_html__( 'No Shortcode found.', 'gym-builder' ),
					'not_found_in_trash' => esc_html__( 'No Shortcode found in trash.', 'gym-builder' ),
				),
				'rewrite'             => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => null,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			),
			'gb_pricing_plan'  => array(
				'title'               => __( 'Membership Package', 'gym-builder' ),
				'plural_title'        => __( 'Membership Package', 'gym-builder' ),
				'supports'            => [ 'title','page-attributes'],
				'public'              => false,
				'labels_override'     => array(
					'all_items'          => esc_html__( 'Membership Package', 'gym-builder' ),
					'menu_name'          => esc_html__( 'Membership Package', 'gym-builder' ),
					'singular_name'      => esc_html__( 'Membership Package', 'gym-builder' ),
					'edit_item'          => esc_html__( 'Edit Membership Package', 'gym-builder' ),
					'new_item'           => esc_html__( 'New Membership Package', 'gym-builder' ),
					'view_item'          => esc_html__( 'View Membership Package', 'gym-builder' ),
					'search_items'       => esc_html__( 'Membership Package Locations', 'gym-builder' ),
					'not_found'          => esc_html__( 'No Membership Package found.', 'gym-builder' ),
					'not_found_in_trash' => esc_html__( 'No Membership Package found in trash.', 'gym-builder' ),
				),
				'rewrite'             => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => null,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			),


		);
		$gym_builder_posts = RegisterPostType::getInstance();
		$gym_builder_posts->add_post_types( $post_types );
	}
}