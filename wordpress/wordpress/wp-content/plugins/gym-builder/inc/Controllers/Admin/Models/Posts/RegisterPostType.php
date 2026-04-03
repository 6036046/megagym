<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Models\Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class RegisterPostType{

    	protected static $instance = null;

		private $post_types = array();

		private function __construct() {
			add_action( 'init', array( $this, 'initialize' ),1);
		}
		public static function getInstance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function initialize() {
			$this->register_gym_builder_post_types();
		}

		public function add_post_types( $post_types ) {
			foreach ( $post_types as $post_type => $args ) {

				$title = $args['title'];
				$plural_title = empty( $args['plural_title'] ) ? $title : $args['plural_title'];



				if ( ! empty( $args['rewrite'] ) ) {
					$args['rewrite'] = array( 'slug' => $args['rewrite'] );
				}

				$labels      = array(
					'name'                     => $plural_title,
					'singular_name'            => $title,
					'add_new'                  => esc_html__( 'Add New', 'gym-builder' ),
					'add_new_item'             => sprintf( esc_html__( 'Add New %s', 'gym-builder' ), $title ),
					'edit_item'                => sprintf( esc_html__( 'Edit %s', 'gym-builder' ), $title ),
					'new_item'                 => sprintf( esc_html__( 'New %s', 'gym-builder' ), $title ),
					'view_item'                => sprintf( esc_html__( 'View %s', 'gym-builder' ), $title ),
					'view_items'               => sprintf( esc_html__( 'View %s', 'gym-builder' ), $plural_title ),
					'search_items'             => sprintf( esc_html__( 'Search %s', 'gym-builder' ), $plural_title ),
					'not_found'                => sprintf( esc_html__( '%s not found', 'gym-builder' ), $plural_title ),
					'not_found_in_trash'       => sprintf( esc_html__( '%s found in Trash', 'gym-builder' ), $plural_title ),
					'parent_item_colon'        => '',
					'all_items'                => sprintf( esc_html__( 'All %s', 'gym-builder' ), $plural_title ),
					'archives'                 => sprintf( esc_html__( '%s Archives', 'gym-builder' ), $title ),
					'attributes'               => sprintf( esc_html__( '%s Attributes', 'gym-builder' ), $title ),
					'insert_into_item'         => sprintf( esc_html__( 'Insert into %s', 'gym-builder' ), $title ),
					'uploaded_to_this_item'    => sprintf( esc_html__( 'Uploaded to this %s', 'gym-builder' ), $title ),
					'filter_items_list'        => sprintf( esc_html__( 'Filter %s list', 'gym-builder' ), $plural_title ),
					'items_list_navigation'    => sprintf( esc_html__( '%s list navigation', 'gym-builder' ), $plural_title ),
					'items_list'               => sprintf( esc_html__( '%s list', 'gym-builder' ), $plural_title ),
					'item_published'           => sprintf( esc_html__( '%s published.', 'gym-builder' ), $title ),
					'item_published_privately' => sprintf( esc_html__( '%s published privately.', 'gym-builder' ), $title ),
					'item_reverted_to_draft'   => sprintf( esc_html__( '%s reverted to draft.', 'gym-builder' ), $title ),
					'item_scheduled'           => sprintf( esc_html__( '%s scheduled.', 'gym-builder' ), $title ),
					'item_updated'             => sprintf( esc_html__( '%s  updated.', 'gym-builder' ), $title ),
					'menu_name'                => $plural_title
				);

				if ( !empty( $args['labels_override'] ) ) {
					$labels = wp_parse_args( $args['labels_override'], $labels );
				}

				$defaults = array(
					'labels'             => $labels,
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_nav_menus'  => true,
					'query_var'          => true,
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => null,
					'menu_icon'          => null,
					'supports'           => array( 'title', 'thumbnail', 'editor' )
				);

				$args = wp_parse_args( $args, $defaults );
				$this->post_types[ $post_type ] = $args;
			}
		}


		private function register_gym_builder_post_types() {
			$post_types = apply_filters( 'gym_builder_post_types', $this->post_types );
			foreach ( $post_types as $post_type => $args ) {
				register_post_type( $post_type, $args );
			}
		}

		
		
}