<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
trait UtilityTrait {

    use FileLocations;

	public static string $current_theme;


	public static function get_theme_name(  ) {

		$theme = wp_get_theme();

		self::$current_theme = $theme->name;

		return self::$current_theme;
	}
	public static  function sidebar_class($archive_page=null)
    {
        $classes=[
            'gym-builder-sidebar',
             $archive_page
        ];
        $classes = apply_filters('gym_builder_sidebar',$classes);
        if(!empty($classes)){
            echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        }
    }
    public static function get_img( $img ) {
	    return self::get_file_locations('plugin_url') . '/assets/public/img/' . $img;
    }
}