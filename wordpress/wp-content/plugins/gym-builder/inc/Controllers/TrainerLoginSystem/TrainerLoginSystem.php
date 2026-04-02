<?php
namespace GymBuilder\Inc\Controllers\TrainerLoginSystem;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'This script cannot be accessed directly.' );
}

class TrainerLoginSystem{
    use Constants,SingleTonTrait;
    private $migration_option_key = 'gym_builder_trainer_migration_status';
    private $installation_type_key = 'gym_builder_trainer_installation_type';

    private $migration;
    private $auth;
    private $dashboard;
    private $email;
    private $helpers;

    public function __construct() {
        $this->init_components();
        $this->init_hooks();
    }

    private function init_components() {
        $this->migration = new GymTrainerMigration($this->migration_option_key, $this->installation_type_key);
        $this->auth = new GymTrainerAuth();
        $this->email = new GymTrainerEmail();
        $this->helpers = new GymTrainerHelpers();
    }

    private function init_hooks() {
        add_action('init', array($this, 'init_trainer_login_system'));

        add_action('save_post', array($this->migration, 'set_default_migration_status'), 999, 2);

        add_action('wp_ajax_nopriv_gym_builder_member_login', array($this->auth, 'handle_member_login'));
        add_action('wp_ajax_gym_builder_member_login', array($this->auth, 'handle_member_login'));
        add_action('wp_ajax_gym_builder_trainer_registration', array($this->auth, 'handle_trainer_registration'));
        add_action('wp_ajax_nopriv_gym_builder_trainer_registration', array($this->auth, 'handle_trainer_registration'));

        add_action( 'transition_post_status', array( $this->email, 'send_approval_email' ), 10, 3 );

    }

    public function init_trainer_login_system() {
        $this->auth->create_trainer_role();
        $this->migration->detect_installation_type();
        $member_auth_page       = get_option( 'gym_builder_has_member_auth_page',false );
        if ( !$member_auth_page) {
            Functions::insert_member_auth_page();
            update_option( 'gym_builder_has_member_auth_page', true  );
        }
    }

}