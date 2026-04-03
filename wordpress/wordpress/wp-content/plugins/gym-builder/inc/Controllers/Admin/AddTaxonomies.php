<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use \GymBuilder\Inc\Controllers\Admin\Models\Posts\RegisterTaxonomy;
class AddTaxonomies{

    private $taxonomies=array();

    public static function init(){

        $class_category_base=SettingsApi::get_option( 'class_category_base','gym_builder_permalinks_settings');
        
        $class_category_base= $class_category_base ?:'gym_builder_class_category';
        $trainer_category_base=SettingsApi::get_option( 'trainer_category_base','gym_builder_permalinks_settings'); 
        $trainer_category_base= $trainer_category_base ?:'gym_builder_trainer_category';

        $taxonomies = array(
            'gym_builder_trainer_category' => array(
                'title'        => __( 'Trainer Category', 'gym-builder' ),
                'plural_title' => __( 'Trainers Categories', 'gym-builder' ),
                'post_types'   => 'gym_builder_trainer',
                'rewrite'      => $trainer_category_base,
            ),
            'gym_builder_class_category' => array(
                'title'        => __( 'Class Category', 'gym-builder' ),
                'plural_title' => __( 'Classes Categories', 'gym-builder' ),
                'post_types'   => 'gym_builder_class',
                'rewrite'      => $class_category_base,
            ),
            'gb_pricing_plan_category' => array(
                'title'        => __( 'Package Type', 'gym-builder' ),
                'plural_title' => __( 'Package Types', 'gym-builder' ),
                'post_types'   => 'gb_pricing_plan',
                'rewrite'      => __('membership_package_types','gym-builder'),
            ),
            
        );
        $gym_builder_tax = RegisterTaxonomy::getInstance();
        $gym_builder_tax->add_taxonomies($taxonomies);
    }
    
}