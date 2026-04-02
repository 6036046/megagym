<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\ThemesSupport\Astra;

use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class ThemeSupport{

	use SingleTonTrait;
	use Constants;

	public function __construct(  ) {
		add_filter('astra_dynamic_post_structure_posttypes',[__CLASS__,'astra_post_types'],15);
		add_filter('astra_blog_post_per_page_exclusions',[__CLASS__,'gym_builder_post_types_exclude']);
	}

	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public static function astra_post_types( $post_types )
	{
		foreach ($post_types as $post_type){
			if (self::$trainer_post_type === $post_type || self::$class_post_type === $post_type){
				$position = array_search($post_type,$post_types);
				unset($post_types[$position]);
			}
		}

		return $post_types;
	}
	public static function gym_builder_post_types_exclude($exclusions){
		$exclusions[] = self::$class_post_type;
		$exclusions[] = self::$trainer_post_type;
		return $exclusions;
	}
}