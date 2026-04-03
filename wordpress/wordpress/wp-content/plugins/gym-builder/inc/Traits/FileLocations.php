<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
trait FileLocations{

    private static $plugin_path;
    private static $plugin_url;
    private static $plugin;

    public static function get_file_locations($location_type){
        
        if('plugin_path'==$location_type){
            return self::$plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        }else if('plugin_url'==$location_type){
            return self::$plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
        }elseif('plugin'==$location_type){
            return self::$plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/gym-builder.php';
        }else{
	        return self::$plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        }

        
    }
    public static function get_template_path() {
        return apply_filters( 'gym_builder_template_path', 'gym-builder/' );
    }
	public static function get_plugin_template_path() {
		return apply_filters( 'gym_builder_plugin_template_path', self::get_file_locations('plugin_path').'templates/' );
	}
}