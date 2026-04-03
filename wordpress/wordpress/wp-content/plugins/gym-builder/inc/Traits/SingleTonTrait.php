<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
trait SingleTonTrait{

	protected static $instance = null;

	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}