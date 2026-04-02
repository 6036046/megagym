<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Widgets;


use GymBuilder\Inc\Traits\FileLocations;
use GymBuilder\Inc\Traits\SingleTonTrait;
use GymBuilder\Inc\Controllers\Helpers\Functions;
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class CustomWidgetsInit{
	use SingleTonTrait;
	use FileLocations;
	public array $widgets;

	public function __construct() {

		$this->widgets =  array(
			 'ClassWidget',
			 'TrainerWidget',
		);

	}
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function custom_widgets() {

		foreach ( $this->widgets as $widget ) {

			$template_name = $this->get_file_locations('plugin_path').'inc/Controllers/Widgets/' . $widget . '.php';

			require_once $template_name;

			$class = __NAMESPACE__ . '\\' . $widget;
			register_widget( $class );
		}
	}
}