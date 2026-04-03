<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class BaseController {

    public $plugin_path;

    public $plugin_url;

    public $plugin;

    protected $gym_builder_template_path;

    public function __construct() {
        $this->gym_builder_template_path="gym-builder";
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
        $this->plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/gym-builder.php';

    }

    
}