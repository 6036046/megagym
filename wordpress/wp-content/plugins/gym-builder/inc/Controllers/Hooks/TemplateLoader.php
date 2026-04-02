<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Hooks;

use \GymBuilder\Inc\Base\BaseController;
use \GymBuilder\Inc\Traits\FileLocations;
use \GymBuilder\Inc\Controllers\Helpers\Functions;
class TemplateLoader{

    use FileLocations;

    private static $theme_support = false;

    public static function init(){
        
        self::$theme_support = current_theme_supports('gym-builder');
  
        if (self::$theme_support) {
            add_filter('template_include', [__CLASS__, 'template_loader']);
        }
    }
    public static function template_loader($template) {

        if (is_embed()) {
            return $template;
        }
        
        $default_file = self::get_template_loader_default_file();


        if ($default_file) {

            $search_files = self::get_template_loader_files($default_file);

            $template = locate_template($search_files);

            if (!$template) {
                $fallback = self::get_file_locations('plugin_path'). "/templates/" . $default_file;
                $template = file_exists($fallback) ? $fallback : '';
                $template = apply_filters('gym_builder_template_loader_fallback_file', $template, $default_file);
            }

        }

        return $template;
    }
    private static function get_template_loader_default_file() {
        $default_file = '';

        if (is_singular('gym_builder_class')) {
            $default_file = 'single_gym_builder_class.php';
        }
        elseif(is_singular('gym_builder_trainer')){
            $default_file = 'single_gym_builder_trainer.php';
        } 
        //elseif (is_tax( get_object_taxonomies( 'gym_builder_class' ) ) || is_tax( get_object_taxonomies( 'gym_builder_trainer' ) )) {
        //     $object = get_queried_object();
        //     if (is_tax( get_object_taxonomies('gym_builder_class')) || is_tax( get_object_taxonomies('gym_builder_trainer'))) {
        //         $default_file = 'taxonomy-' . $object->taxonomy . '.php';
        //     } else {
        //         $default_file = 'archive-' . 'post_type' . '.php';
        //     }
        elseif (is_post_type_archive('gym_builder_class') || (($classes_page_id = Functions::get_page_id('classes')) && is_page($classes_page_id))) {
            $default_file = 'archive_gym_builder_class.php';
        } 
        elseif (is_post_type_archive('gym_builder_trainer') || (($trainer_page_id = Functions::get_page_id('trainers')) && is_page($trainer_page_id))) {
            $default_file = 'archive_gym_builder_trainer.php';
        }elseif (($member_auth_page_id = Functions::get_page_id('member_auth')) && is_page($member_auth_page_id)){
            $default_file='auth/member-registration.php';
        }elseif (($member_dashboard_page_id = Functions::get_page_id('member_dashboard')) && is_page($member_dashboard_page_id)){
            $default_file = 'auth/member-dashboard.php';
        }
        $default_file = apply_filters('gym_builder_template_loader_default_file', $default_file);

        return $default_file;
    }
    private static function get_template_loader_files($default_file) {

        if (is_page_template()) {
            $templates[] = get_page_template_slug();
        }

        if (is_singular('gym_builder_class')) {
            $object = get_queried_object();
            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "single_gym_builder_class-{$name_decoded}.php";
            }
            $templates[] = "single_gym_builder_class-{$object->post_name}.php";
        }

        elseif (is_singular('gym_builder_trainer')) {
            $object = get_queried_object();
            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "single_gym_builder_trainer-{$name_decoded}.php";
            }
            $templates[] = "single_gym_builder_trainer-{$object->post_name}.php";
        }

        $templates = [
            $default_file,
            self::get_template_path() . $default_file,
        ];

        $templates = apply_filters('gym_builder_template_loader_files', $templates, $default_file);

        return array_unique($templates);
    }

}