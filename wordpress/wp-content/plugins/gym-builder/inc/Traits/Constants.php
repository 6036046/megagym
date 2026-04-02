<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

trait Constants {
	public static bool $plugin_template_load = false;
	public static string $plugin_version = "2.3.2";
	public static string $members_db_table_version = "2.3.0";
	public static string $trainer_post_type = 'gym_builder_trainer';
	public static string $class_post_type = 'gym_builder_class';
	public static string $class_shortcode_post_type = 'gb_class_shortcode';
	public static string $trainer_shortcode_post_type = 'gb_trainer_shortcode';
	public static string $fitness_calc_shortcode_post_type = 'gb_fitness_shortcode';
	public static string $membership_package_post_type = 'gb_pricing_plan';
	public static string $class_taxonomy = 'gym_builder_class_category';
	public static string $trainer_taxonomy = 'gym_builder_trainer_category';
	public static string $membership_package_taxonomy = 'gb_pricing_plan_category';
	public static string $class_archive_sidebar = 'gym-builder-class-archive-sidebar';
	public static string $trainer_archive_sidebar = 'gym-builder-trainer-archive-sidebar';
	public static string $class_single_sidebar = 'gym-builder-class-single-sidebar';
	public static string $trainer_single_sidebar = 'gym-builder-trainer-single-sidebar';
	public static string $classes_endpoint_namespace = 'get_gym_builder_classes/v1';
	public static string $classes_endpoint_namespace2 = 'get_gym_builder_classes_json/v1';
	public static string $trainers_endpoint_namespace = 'get_gym_builder_trainers/v1';
	public static string $membership_package_endpoint_namespace = 'get_gym_builder_membership_package/v1';
	public static string $gym_builder_members_endpoint_namespace = 'get_gym_builder_members/v1';
	public static string $gym_builder_currency_endpoint_namespace = 'get_gym_builder_currency_list/v1';


}