<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Settings\Pages;

use \GymBuilder\Inc\Base\BaseController;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\ConfigureSettings;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\AdminCallbacks;

class Dashboard extends BaseController{

    public $settings;

    public $callbacks;

    public $pages = array();

    public function register() {

        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();


        $this->setPages();


        $this->settings->addPages( $this->pages )->withSubPage( 'Settings' )->register();
    }

    public function setPages() {
        $this->pages = array(
            array(
                'page_title' => 'Gym Builder',
                'menu_title' => 'Gym Builder',
                'capability' => 'manage_options',
                'menu_slug'  => 'gym_builder',
                'callback'   => array( $this->callbacks, 'gym_builder_settings_page' ),
                'icon_url'   => $this->plugin_url."assets/admin/images/gym-builder-icon-white.png",
                'position'   => '60',
            ),

        );
    }


}