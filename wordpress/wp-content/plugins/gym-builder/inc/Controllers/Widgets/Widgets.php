<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Widgets;

use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\UtilityTrait;
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class Widgets {

    use Constants;
    public static function init()
    {
        add_action( 'widgets_init', [__CLASS__, 'register_sidebar'] );
        add_action( 'widgets_init', [__CLASS__, 'register_widget'] );
        add_action( 'init', [__CLASS__, 'widget_support'] );
    }

    static function widget_support()
    {
        add_filter( 'elementor/widgets/wordpress/widget_args', [__CLASS__, 'elementor_wordpress_widget_support'] );
    }

    public static function register_sidebar()
    {

        if ( !is_registered_sidebar( self::$class_archive_sidebar ) ) {
            register_sidebar( [
                'name'          => apply_filters( 'gym_builder_archive_sidebar_title', esc_html__( 'Gym Builder - Class Archive Sidebar', 'gym-builder' ) ),
                'id'            => self::$class_archive_sidebar,
                'description'   => esc_html__( 'Add widgets on class archive page', 'gym-builder' ),
                'before_widget' => '<div class="widget gym-builder-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<div class="gym-builder-widget-heading"><h2>',
                'after_title'   => '</h2></div>',
            ] );
        }
        if ( !is_registered_sidebar( self::$trainer_archive_sidebar ) ) {
            register_sidebar( [
                'name'          => apply_filters( 'gym_builder_trainer_archive_sidebar_title', esc_html__( 'Gym Builder - Trainer Archive Sidebar', 'gym-builder' ) ),
                'id'            => self::$trainer_archive_sidebar,
                'description'   => esc_html__( 'Add widgets on trainer archive page', 'gym-builder' ),
                'before_widget' => '<div class="widget gym-builder-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<div class="gym-builder-widget-heading"><h2>',
                'after_title'   => '</h2></div>',
            ] );
        }
        if ( !is_registered_sidebar( self::$class_single_sidebar ) ) {
            register_sidebar( [
                'name'          => apply_filters( 'gym_builder_single_sidebar_title', esc_html__( 'Gym Builder - Class Single Sidebar', 'gym-builder' ) ),
                'id'            => self::$class_single_sidebar,
                'description'   => esc_html__( 'Add widgets on class single page', 'gym-builder' ),
                'before_widget' => '<div class="widget gym-builder-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<div class="gym-builder-widget-heading"><h2>',
                'after_title'   => '</h2></div>',
            ] );
        }
        if ( !is_registered_sidebar( self::$trainer_single_sidebar ) ) {
            register_sidebar( [
                'name'          => apply_filters( 'gym_builder_single_sidebar_title', esc_html__( 'Gym Builder - Trainer Single Sidebar', 'gym-builder' ) ),
                'id'            => self::$trainer_single_sidebar,
                'description'   => esc_html__( 'Add widgets on trainer single page', 'gym-builder' ),
                'before_widget' => '<div class="widget gym-builder-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<div class="gym-builder-widget-heading"><h2>',
                'after_title'   => '</h2></div>',
            ] );
        }

    }

	public static function register_widget(  ) {
		$instanceObj = new CustomWidgetsInit();
		$instanceObj->custom_widgets();
	}

    public static function elementor_wordpress_widget_support()
    {

        $args['before_widget'] = '<div class="gym-builder-widget">';
        $args['after_widget'] = '</div>';
        $args['before_title'] = '<h2>';
        $args['after_title'] = '</h2>';

        return $args;
    }

}
