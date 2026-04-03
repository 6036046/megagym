<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Settings\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
use GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackCheckbox;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackColor;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackImageRadio;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackMultiSelect;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackText;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackSelect;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackMultiCheck;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackRadio;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\Callbacks\CallbackHeading;
use GymBuilder\Inc\Controllers\Helpers\Functions;

class SettingsApi {

    public array $admin_pages = array();

    public array $admin_subpages = array();


    public function register()
    {

        if ( !empty( $this->admin_pages ) || !empty($this->admin_subpages) ) {
            add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
        }

    }

    public function addPages( array $pages )
    {
        $this->admin_pages = $pages;
        return $this;
    }

    public function addSubPages( array $pages )
    {
        $this->admin_subpages = array_merge( $this->admin_subpages, $pages );
        return $this;
    }

    public function withSubPage( string $title = null ) {

        if ( empty( $this->admin_pages ) ) {
            return;
        }
        $admin_page = $this->admin_pages[0];
        $subpages = array(
            array(
                'parent_slug' => $admin_page['menu_slug'],
                'page_title'  => $admin_page['page_title'],
                'menu_title'  => ( $title ) ? $title : $admin_page['menu_title'],
                'capability'  => $admin_page['capability'],
                'menu_slug'   => $admin_page['menu_slug'],
                'callback'    => $admin_page['callback'],
            ),
        );
        $this->admin_subpages = $subpages;

        return $this;
    }


    public function addAdminMenu()
    {

        foreach ( $this->admin_pages as $page ) {
            add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
        }

        foreach ( $this->admin_subpages as $page ) {
            add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
        }

    }


    public static function get_option( $option, $section, $default = '' ) {

        $options = get_option( $section );

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }



}
