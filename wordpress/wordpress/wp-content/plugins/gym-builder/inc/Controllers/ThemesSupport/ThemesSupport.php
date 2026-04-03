<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\ThemesSupport;

use GymBuilder\Inc\Traits\SingleTonTrait;
use GymBuilder\Inc\Controllers\ThemesSupport\Astra\ThemeSupport as AstraSupport;
use GymBuilder\Inc\Controllers\ThemesSupport\OceanWP\ThemeSupport as OceanWPSupport;
use GymBuilder\Inc\Traits\UtilityTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class ThemesSupport{

	use SingleTonTrait;
	use UtilityTrait;

	public function __construct() {

		$theme = $this->get_theme_name();

		if ('Astra' === $theme){
			$astraInstance = AstraSupport::init();
			new $astraInstance();
		} elseif ('OceanWP' === $theme){
			$oceanWPInstance = OceanWPSupport::init();
			new $oceanWPInstance;
		}

	}

	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}