<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox;

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Controllers\Admin\Models\Metabox\RegisterPostMeta;


class FitnessCalculatorShortcodeMeta {
	use Constants;

	public static function init() {
		add_action( 'admin_init', [ __CLASS__, 'shortcode_metabox_added' ], 9 );
	}

	public static function shortcode_metabox_added() {
		$Postmeta = RegisterPostMeta::getInstance();

		$Postmeta->add_meta_box( 'gb_fitness_calculator_shortcode_settings', __( 'Calculator Settings', 'gym-builder' ), array( self::$fitness_calc_shortcode_post_type ), '', '', 'high', array(
			'fields' => self::gb_fitness_calculator_shortcode_meta_setting()
		) );

	}

	public static function gb_fitness_calculator_types() {
		$types = [
			'bmi'            => 'BMI',
			'body_fat'       => 'Body Fat',
			'protien_intake' => 'Protien Intake',
			'water_intake'   => 'Water Intake',
		];

		return apply_filters( 'gym_builder_fitness_calculator_types', $types );
	}

	public static function gb_fitness_bmi_calc_layout() {
		$layout = [
			'layout-1' => 'Layout 1'
		];

		return apply_filters( 'gb_fitness_bmi_calc_layout', $layout );
	}

	public static function gb_fitness_calculator_shortcode_meta_setting() {
		$fields = [
			'gb_fitness_calculator_shortcode_types' => array(
				'label'   => __( 'Calculator Types', 'gym-builder' ),
				'type'    => 'select',
				'desc'    => __( 'you can select fitness calculator types from here', 'gym-builder' ),
				'default' => 'bmi',
				'options' => self::gb_fitness_calculator_types()
			),
			'gb_bmi_calc_layout'                    => array(
				'label'    => __( 'BMI Layout', 'gym-builder' ),
				'type'     => 'select',
				'desc'     => __( 'you can select bmi calculator layout from here', 'gym-builder' ),
				'default'  => 'layout-1',
				'options'  => self::gb_fitness_bmi_calc_layout(),
				'required' => [
					'gb_fitness_calculator_shortcode_types' => [ 'bmi' ]
				],
			),
			'gb_fitness_calc_heading'               => array(
				'label'   => __( 'Calculator Heading', 'gym-builder' ),
				'type'    => 'text',
				'default' => __( 'Fitness Calculator', 'gym-builder' ),
			),
			'gb_fitness_calc_des'                   => array(
				'label' => __( 'Calculator Description', 'gym-builder' ),
				'type'  => 'textarea',
			),
			'gb_fintess_calc_unit'                  => array(
				'label'   => __( 'Default Calculation Unit', 'gym-builder' ),
				'type'    => 'radio',
				'default' => 'metric',
				'options' => array(
					'metric'   => 'Metric',
					'imperial' => 'Imperial',
				),
			),
			'gb_fitness_calc_btn_text'              => array(
				'label'   => __( 'Button Text', 'gym-builder' ),
				'type'    => 'text',
				'default' => __( 'Calculator', 'gym-builder' ),
			),

		];

		return apply_filters( 'gb_fintess_calculator_shortcode_basic_meta', $fields );
	}

}
