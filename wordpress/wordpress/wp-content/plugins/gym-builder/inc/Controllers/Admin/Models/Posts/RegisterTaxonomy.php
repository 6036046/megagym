<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Models\Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class RegisterTaxonomy{
    
    protected static $instance = null;

    private $taxonomies = array();

    private function __construct() {
        add_action( 'init', array( $this, 'initialize' ),9);
    }
    public static function getInstance() {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    public function initialize() {
        $this->register_taxonomies();
    }
    public function add_taxonomies( $taxonomies ) {

        foreach ($taxonomies as $taxonomy => $args ) {

            $title = $args['title'];
            $plural_title = empty( $args['plural_title'] ) ? $title : $args['plural_title'];
            if ( ! empty( $args['rewrite'] ) ) {
                $args['rewrite'] = array( 'slug' => $args['rewrite'] );
            }
            $labels     = array(
                'name'                       => $title,
                'singular_name'              => $title,
                'search_items'               => sprintf( esc_html__( 'Search %s', 'gym-builder' ), $plural_title ),
                'popular_items'              => sprintf( esc_html__( 'Popular %s', 'gym-builder' ), $plural_title ),
                'all_items'                  => sprintf( esc_html__( 'All %s', 'gym-builder' ), $plural_title ),
                'parent_item'                => sprintf( esc_html__( 'Parent %s', 'gym-builder' ), $title ),
                'parent_item_colon'          => sprintf( esc_html__( 'Parent %s:', 'gym-builder' ), $title ),
                'edit_item'                  => sprintf( esc_html__( 'Edit %s', 'gym-builder' ), $title ),
                'view_item'                  => sprintf( esc_html__( 'View %s', 'gym-builder' ), $title ),
                'update_item'                => sprintf( esc_html__( 'Update %s', 'gym-builder' ), $title ),
                'add_new_item'               => sprintf( esc_html__( 'Add New %s', 'gym-builder' ), $title ),
                'new_item_name'              => sprintf( esc_html__( 'New %s Name', 'gym-builder' ), $title ),
                'separate_items_with_commas' => sprintf( esc_html__( 'Separate %s with commas', 'gym-builder' ), $plural_title ),
                'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s', 'gym-builder' ), $plural_title ),
                'choose_from_most_used'      => sprintf( esc_html__( 'Choose from the most used %s', 'gym-builder' ), $plural_title ),
                'not_found'                  => sprintf( esc_html__( 'No %s found.', 'gym-builder' ), $plural_title ),
                'no_terms'                   => sprintf( esc_html__( 'No %s', 'gym-builder' ), $plural_title ),
                'items_list_navigation'      => sprintf( esc_html__( '%s list navigation', 'gym-builder' ), $plural_title ),
                'items_list'                 => sprintf( esc_html__( '%s list', 'gym-builder' ), $plural_title ),
                'back_to_items'              => sprintf( esc_html__( '&larr; Back to %s', 'gym-builder' ), $plural_title ),
                'menu_name'                  => $plural_title,
            );

            if ( !empty( $args['labels_override'] ) ) {
                $labels = wp_parse_args( $args['labels_override'], $labels );
            }

            $defaults = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_in_nav_menus' => true,
                'show_ui'           => null,
                'show_admin_column' => true,
                'query_var'         => true,
            );

            $args = wp_parse_args( $args, $defaults );
            $this->taxonomies[ $taxonomy ] = $args;
        }
    }
    private function register_taxonomies() {
        $taxonomies = apply_filters( 'gym_builder_taxonomies', $this->taxonomies );
        foreach ( $taxonomies as $taxonomy => $args ) {
            register_taxonomy( $taxonomy, $args['post_types'], $args );
        }
    }
}