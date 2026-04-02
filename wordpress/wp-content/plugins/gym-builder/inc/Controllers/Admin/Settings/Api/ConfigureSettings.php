<?php
/**
 * @package MiltonPlugin
 */

namespace GymBuilder\Inc\Controllers\Admin\Settings\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\AdminCallbacks;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerMigration;
use GymBuilder\Inc\Controllers\StudentLoginSystem\GymStudentMigration;


class ConfigureSettings {

	private static $settings_api;

	public $callbacks;

	protected static $instance = null;

	private array $subpages = array();

	public static function getInstance(): ?ConfigureSettings {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function register() {

		self::$settings_api = new SettingsApi();

		$this->callbacks = new AdminCallbacks();


		$this->setSubPages();

		self::$settings_api->addSubPages( $this->subpages )->register();

	}

	public function setSubPages() {
		$menu_link_part = admin_url( 'admin.php?page=gym-builder-members' );
		$this->subpages = array(
            array(
                'parent_slug' => 'gym_builder',
                'page_title'  => 'Dashboard',
                'menu_title'  => 'Dashboard',
                'capability'  => 'manage_options',
                'menu_slug'   => 'gym-builder-dashboard',
                'callback'    => array($this->callbacks,'gym_builder_dashboard_page'),
            ),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Class Shortcode',
				'menu_title'  => 'Class Shortcode',
				'capability'  => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=gb_class_shortcode',
				'callback'    => '',
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Trainer Shortcode',
				'menu_title'  => 'Trainer Shortcode',
				'capability'  => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=gb_trainer_shortcode',
				'callback'    => '',
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Fitness Calc Shortcode',
				'menu_title'  => 'Fitness Calc Shortcode',
				'capability'  => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=gb_fitness_shortcode',
				'callback'    => '',
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Membership Package',
				'menu_title'  => 'Membership Package',
				'capability'  => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=gb_pricing_plan',
				'callback'    => '',
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'All Members',
				'menu_title'  => 'All Members',
				'capability'  => 'manage_options',
				'menu_slug'   => 'gym-builder-members',
				'callback'    => array( $this->callbacks, 'gym_builder_members' ),
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Add Member',
				'menu_title'  => 'Add Member',
				'capability'  => 'manage_options',
				'menu_slug'   => $menu_link_part . '#/add-member',
				'callback' => ''
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Get Help',
				'menu_title'  => 'Get Help',
				'capability'  => 'manage_options',
				'menu_slug'   => 'gym-builder-get-help',
				'callback'    => array( $this->callbacks, 'gym_builder_get_help_page' ),
			),
			array(
				'parent_slug' => 'gym_builder',
				'page_title'  => 'Extensions',
				'menu_title'  => 'Extensions',
				'capability'  => 'manage_options',
				'menu_slug'   => 'gym-builder-extensions',
				'callback'    => array( $this->callbacks, 'gym_builder_extensions_page' ),
			),
            $this->get_migration_subpage(),
            $this->get_student_migration_subpage(),
		);
        $this->subpages = array_filter($this->subpages, function($subpage) {
            return !empty($subpage) && is_array($subpage);
        });
	}
    private function get_student_migration_subpage() {
        $migration = new GymStudentMigration(
            'gym_builder_student_migration_status',
            'gym_builder_student_installation_type'
        );

        if ($migration->is_fresh_installation() || $migration->is_migration_completed()) {
            return null;
        }

        return array(
            'parent_slug' => 'gym_builder',
            'page_title'  => __('Student Migration', 'gym-builder'),
            'menu_title'  => __('Student Migration', 'gym-builder'),
            'capability'  => 'manage_options',
            'menu_slug'   => 'gym_builder_student_manage',
            'callback'    => array( $this->callbacks, 'student_manage_page' ),
        );
    }

    private function get_migration_subpage() {

//        $migration = new GymTrainerMigration(
//            'gym_builder_trainer_migration_status',
//            'gym_builder_trainer_installation_type'
//        );
//
//        if ($migration->is_fresh_installation()) {
//            return null;
//        }

        return array(
            'parent_slug' => 'gym_builder',
            'page_title'  => __('Trainer Manage', 'gym-builder'),
            'menu_title'  => __('Trainer Manage', 'gym-builder'),
            'capability'  => 'manage_options',
            'menu_slug'   => 'gym_builder_trainer_manage',
            'callback'    => array( $this->callbacks, 'trainer_manage_page' ),
        );
    }

}

