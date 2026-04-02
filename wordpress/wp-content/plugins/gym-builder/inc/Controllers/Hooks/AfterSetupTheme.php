<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Hooks;

use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use  GymBuilder\Inc\Controllers\Helpers\Functions;

class AfterSetupTheme{
    public static function template_functions()
    {
        self::addPluginSupport();
        add_action('template_redirect', [__CLASS__, 'template_redirect']);
    }
    public static function template_redirect()
    {

        global $wp;

        if (!empty($_GET['page_id']) && '' === get_option('permalink_structure') && Functions::get_page_id('classes') === absint($_GET['page_id']) && get_post_type_archive_link('gym_builder_class')) { // WPCS: input var ok, CSRF ok.
            wp_safe_redirect(get_post_type_archive_link('gym_builder_class'));
            
            exit;
        }
        
    }
    public static function addPluginSupport()
    {
        global $_wp_theme_features;

	    $class_thumb_width   = SettingsApi::get_option( 'class_thumbnail_width','gym_builder_class_settings') ?:'570';
	    $class_thumb_height   = SettingsApi::get_option( 'class_thumbnail_height','gym_builder_class_settings') ?:'400';
	    $class_thumb_crop   = SettingsApi::get_option( 'class_thumbnail_hard_crop','gym_builder_class_settings') ?:'on';
		$class_thumb_crop = $class_thumb_crop === 'on';

	    $trainer_thumb_width   = SettingsApi::get_option( 'trainer_thumbnail_width','gym_builder_trainer_settings') ?:'570';
	    $trainer_thumb_height   = SettingsApi::get_option( 'trainer_thumbnail_height','gym_builder_trainer_settings') ?:'400';
	    $trainer_thumb_crop   = SettingsApi::get_option( 'trainer_thumbnail_hard_crop','gym_builder_trainer_settings') ?:'on';
	    $trainer_thumb_crop = $trainer_thumb_crop === 'on';
        
        if( !isset($_wp_theme_features['add_theme_support']) ){
            add_theme_support('gym-builder');
        }

        add_image_size( 'gym_builder_size1', 1240, 720, true );
		add_image_size('class_thumb_size',$class_thumb_width,$class_thumb_height,$class_thumb_crop);
		add_image_size('trainer_thumb_size',$trainer_thumb_width,$trainer_thumb_height,$trainer_thumb_crop);

    }
}