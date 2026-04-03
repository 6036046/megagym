<?php
/**
* @package GymBuilder
*/
namespace GymBuilder\Inc\Controllers\ThemesSupport\OceanWP;

use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class ThemeSupport{

	use SingleTonTrait;


	public function __construct(  ) {
		add_action('admin_enqueue_scripts',array($this,'enqueue'));
	}

	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function enqueue($screen){

		$_screen=get_current_screen();

		if('toplevel_page_gym_builder'==$screen){
			add_filter('oceanwp:admin:display-ocean-extra-plugin-notice',function($arg){
				return false;
			});
		}

	}
}