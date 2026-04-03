<?php
/**
 * @package MiltonPlugin
 */
namespace GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks;

use \GymBuilder\Inc\Base\BaseController;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerMigration;
use GymBuilder\Inc\Controllers\StudentLoginSystem\GymStudentMigration;

class AdminCallbacks extends BaseController{

    public function adminDashboard(){
        return require_once("$this->plugin_path/templates/admin/admin.php");
    }

    public function about_callback(){
        return require_once("$this->plugin_path/templates/admin/about.php");
    }
//	public function about_callback(){
//		return require_once("$this->plugin_path/templates/admin/about.php");
//	}
	public function add_member() {
		Functions::renderView('add-member');
	}

	public function gym_builder_get_help_page(  ) {
		Functions::renderView('get-help');
	}
	public function gym_builder_extensions_page(  ) {
		Functions::renderView('extensions');
	}
	public function gym_builder_members() {
		Functions::renderView('react-view');
	}

	public function gym_builder_settings_page(  ) {
		Functions::renderView('react-admin-settings');
	}
    public function gym_builder_dashboard_page(  ) {
        Functions::renderView('dashboard');
    }
    public function trainer_manage_page() {
        $migration = new GymTrainerMigration(
            'gym_builder_trainer_migration_status',
            'gym_builder_trainer_installation_type'
        );

        $migration->migration_page();
    }

    public function student_manage_page() {
        $migration = new GymStudentMigration(
            'gym_builder_student_migration_status',
            'gym_builder_student_installation_type'
        );

        $migration->migration_page();
    }

}