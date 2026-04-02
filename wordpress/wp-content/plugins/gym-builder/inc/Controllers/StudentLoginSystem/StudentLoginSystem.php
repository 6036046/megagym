<?php
namespace GymBuilder\Inc\Controllers\StudentLoginSystem;

use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'This script cannot be accessed directly.' );
}

class StudentLoginSystem {
    use Constants, SingleTonTrait;

    private $migration_option_key = 'gym_builder_student_migration_status';
    private $installation_type_key = 'gym_builder_student_installation_type';

    private $migration;
    private $email;


    public function __construct() {
        $this->init_components();
        $this->init_hooks();
    }

    private function init_components() {
        $this->migration = new GymStudentMigration($this->migration_option_key, $this->installation_type_key);
        $this->email = new GymStudentEmail();
    }

    private function init_hooks() {
        add_action('init', array($this, 'init_student_login_system'));
        add_action('wp_ajax_gym_builder_migrate_all_students', array($this->migration, 'migrate_all_members'));
    }

    public function init_student_login_system() {
        $this->migration->detect_installation_type();
        $this->create_student_role();

    }
    public function create_student_role() {
        if (!get_role('gym_builder_student')) {
            add_role('gym_builder_student', __('Gym Student','gym-builder'), array(
                'read' => true,
                'gym_builder_student_dashboard' => true,
            ));
        }
    }
}
