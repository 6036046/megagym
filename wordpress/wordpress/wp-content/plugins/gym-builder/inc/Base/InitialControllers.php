<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Base;


use GymBuilder\Inc\Base\Install;
use GymBuilder\Inc\Base\BaseController;
use GymBuilder\Inc\Controllers\Admin\AddConfig;
use GymBuilder\Inc\Controllers\Admin\Api\RestApi;
use GymBuilder\Inc\Controllers\Admin\Notice\PersonalSessionNotice;
use GymBuilder\Inc\Controllers\Admin\Notice\ProAddons;
use GymBuilder\Inc\Controllers\Admin\Notice\Review;
use GymBuilder\Inc\Controllers\Admin\PluginSurvey;
use GymBuilder\Inc\Controllers\AjaxController;
use GymBuilder\Inc\Controllers\Models\GymBuilderDatabase;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\TrainerLoginSystem;
use GymBuilder\Inc\Controllers\StudentLoginSystem\StudentLoginSystem;
use \GymBuilder\Inc\Controllers\Widgets\Widgets;
use GymBuilder\Inc\Controllers\Hooks\FilterHooks;
use \GymBuilder\Inc\Controllers\Admin\AddPostMeta;
use \GymBuilder\Inc\Controllers\Admin\AddPostTypes;
use GymBuilder\Inc\Controllers\Models\QueryBuilder;
use \GymBuilder\Inc\Controllers\Hooks\TemplateHooks;
use \GymBuilder\Inc\Controllers\Admin\AddTaxonomies;
use \GymBuilder\Inc\Controllers\Hooks\TemplateLoader;
use \GymBuilder\Inc\Controllers\Hooks\AfterSetupTheme;
use GymBuilder\Inc\Controllers\Frontend\ClassShortcode;
use \GymBuilder\Inc\Controllers\Admin\GbColumnManagement;
use GymBuilder\Inc\Controllers\Frontend\TrainerShortcode;
use GymBuilder\Inc\Controllers\ThemesSupport\ThemesSupport;
use GymBuilder\Inc\Controllers\Frontend\FitnessCalcShortcode;
use GymBuilder\Inc\Controllers\Frontend\MembershipPackageShortcode;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\ConfigureSettings;
use GymBuilder\Inc\Controllers\Admin\Shortcode\AdminInit as ShortcodeAdmin;
use GymBuilder\Inc\Controllers\Admin\ExportImport\ExportImportPage;
use GymBuilder\Inc\Controllers\Admin\ExportImport\ExportImportApi;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class InitialControllers extends BaseController {
	public function register() {
		Widgets::init();
		new QueryBuilder();
		$this->load_hooks();
		$this->load_classes();
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_hook();
		}
		if ( $this->is_request( 'admin' ) ) {
			$this->remove_all_notices();
			Review::instance();
            PersonalSessionNotice::instance();
			ProAddons::instance();
            PluginSurvey::instance();
		}
        TrainerLoginSystem::instance();
        StudentLoginSystem::instance();
	}

	public function frontend_hook() {
		TemplateHooks::init();
		add_action( 'init', [ TemplateLoader::class, 'init' ] );
		$this->register_shortcode_classes();

	}

	private function load_hooks() {
		register_activation_hook( $this->plugin, [ Install::class, 'activate' ] );
		register_deactivation_hook( $this->plugin, [ Install::class, 'deactivate' ] );
		add_action( 'plugins_loaded', [ GymBuilderDatabase::class, 'check_and_update_db_table' ] );
		add_action( 'plugins_loaded', [ Install::class, 'plugin_activation_time' ] );
		add_action( 'after_setup_theme', [ AfterSetupTheme::class, 'template_functions' ], 11 );
		add_action( 'init', [ $this, 'init_hooks' ], 0 );
        add_action( 'init', [ $this, 'gym_builder_loaded_text_domain' ] );
		add_action( 'init', [ $this, 'admin_settings_page' ], 99 );
		add_action( 'admin_init', [ $this, 'activation_redirect' ] );
	}

	public function init_hooks() {
		AddTaxonomies::init();
		$postTypeObj = AddPostTypes::getInstance();
		$postTypeObj->init();
		AddPostMeta::init();
		ShortcodeAdmin::init();
		GbColumnManagement::init();
		new AddConfig();
		$themesSupportIns = ThemesSupport::init();
		new $themesSupportIns();
		FilterHooks::init();

	}

	public function admin_settings_page() {
		$instanceObj = ConfigureSettings::getInstance();
		$instanceObj->register();
	}

	public function gym_builder_loaded_text_domain() {
		load_plugin_textdomain( 'gym-builder', false, $this->plugin_path . "languages" );
	}

	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}


	public function register_shortcode_classes() {
		$shortcode_classes = [
			ClassShortcode::class,
			TrainerShortcode::class,
			MembershipPackageShortcode::class,
			FitnessCalcShortcode::class
		];
		foreach ( $shortcode_classes as $class_name ) {
			if ( class_exists( $class_name ) ) {
				if ( method_exists( $class_name, 'init' ) ) {
					$instance = $class_name::instance();
					$instance->init();
				}
			}
		}

	}

	public function activation_redirect() {
		if ( ! get_transient( 'gym_builder_activation_redirect' ) ) {
			return;
		}

		delete_transient( 'gym_builder_activation_redirect' );

		if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=gym-builder-import-export##demo' ) );
		exit;
	}

	public function load_classes() {
		ExportImportPage::instance()->register();

		$classes = [
			RestApi::class,
			AjaxController::class,
			ExportImportApi::class,
		];
		foreach ( $classes as $class_name ) {
			if ( class_exists( $class_name ) ) {
				if ( method_exists( $class_name, 'init' ) ) {
					$instance = $class_name::instance();
					$instance->init();
				}
			}
		}

	}

	public function remove_all_notices() {
		add_action(
			'in_admin_header',
			function() {
				$screen = get_current_screen();
				if ( in_array( $screen->base, [
					'toplevel_page_gym_builder',
					'gym-builder_page_gym-builder-members',
					'gym-builder_page_gym-builder-get-help',
					'gym-builder_page_gym-builder-dashboard',
					'gym-builder_page_gym-builder-extensions',
					'gym-builder_page_gym-builder-import-export',
				] ) ) {
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );
				}
			},
			1000
		);
	}

}