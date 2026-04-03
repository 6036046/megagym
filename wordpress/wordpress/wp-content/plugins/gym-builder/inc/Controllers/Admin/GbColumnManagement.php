<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin;

use GymBuilder\Inc\Controllers\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) exit;

class GbColumnManagement{
	public static function init()
	{

		add_filter('manage_edit-gb_pricing_plan_columns' ,[__CLASS__,'gb_pricing_plan_columns']);
		add_action( 'manage_gb_pricing_plan_posts_custom_column', [ __CLASS__, 'gb_pricing_plan_posts_custom_column' ], 10, 2 );

	}
	public static function gb_pricing_plan_columns($columns) {
		$shortcode = [ 'gb_pricing_plan' => esc_html__( 'Package Price', 'gym-builder' ) ];

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}
	public static function gb_pricing_plan_posts_custom_column( $column,$post_id ) {

		switch ( $column ) {
			case 'gb_pricing_plan':
				$package_price = Functions::get_price_with_label($post_id);
				echo wp_kses_post($package_price);
				break;
			default:
				break;
		}

	}
}