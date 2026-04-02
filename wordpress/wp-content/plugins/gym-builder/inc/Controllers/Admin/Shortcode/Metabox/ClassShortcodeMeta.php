<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Controllers\Admin\Models\Metabox\RegisterPostMeta;
use GymBuilder\Inc\Traits\IconTraits;


class ClassShortcodeMeta {
	use Constants,IconTraits;

	public static function init() {
		add_action('admin_init',[__CLASS__,'get_categories_array'],2);
		add_action('admin_init',[__CLASS__,'shortcode_metabox_added'],9);
	}

	public static function shortcode_metabox_added(  ) {
		$Postmeta = RegisterPostMeta::getInstance();

		$Postmeta->add_meta_box( 'gb_class_shortcode_settings', __( 'Class Settings', 'gym-builder' ), array( self::$class_shortcode_post_type ), '', '', 'high', array(
			'fields' => self::gb_class_shortcode_meta_setting()
		) );

		$Postmeta->add_meta_box( 'gb_class_shortcode_image_settings', __( 'Image Settings', 'gym-builder' ), array( self::$class_shortcode_post_type ), '', '', 'high', array(
			'fields' => self::gb_class_shortcode_image_meta_settings()
		) );
		$Postmeta->add_meta_box( 'gb_class_shortcode_filtering_settings', __( 'Class Filtering', 'gym-builder' ), array( self::$class_shortcode_post_type ), '', '', 'default', array(
			'fields' => self::gb_class_shortcode_filtering()
		) );

	}

	public static function gb_classs_shortcode_layout() {
		$layout = [
			'layout-1' => [
				'title'      => 'Grid Layout 1',
				'img_source' => 'class-layout-1'
			],
			'layout-3' => [
				'title'      => 'Grid Layout 2',
				'img_source' => 'class-layout-3'
			],
			'layout-2' => [
				'title'      => 'Schedule Layout',
				'img_source' => 'class-layout-2'
			],

		];

		return apply_filters( 'gym_builder_class_shortcode_layout', $layout );
	}

	public static function gb_class_shortcode_meta_setting() {
		$fields = [
			'gb_class_shortcode_time_format'    => array(
				'label'   => __( 'Class Time Format', 'gym-builder' ),
				'type'    => 'radio',
				'desc'    => __( 'you can select class time format from here', 'gym-builder' ),
				'default' => '12',
				'options' => array(
					'24' => '24-hour',
					'12' => '12-hour',
				),
			),
			'gb_class_shortcode_layout'         => array(
				'label'   => __( 'Class Layout', 'gym-builder' ),
				'type'    => 'image_radio',
				'desc'    => __( 'you can select different class layout from here', 'gym-builder' ),
				'default' => 'layout-1',
				'options' => self::gb_classs_shortcode_layout()
			),
            'gb_class_routine_nav' => array(
                'label'   => __( 'Routine Navigation', 'gym-builder' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Show or Hide Class Routine Filtering Navigation List', 'gym-builder' ),
                'required' => [
                    'gb_class_shortcode_layout'=> ['layout-2']
                ],
            ),
			'gb_class_shortcode_posts_per_page' => array(
				'label'   => __( 'Post\'s Limit', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can select class post item limit and in class schedule layout post\'s limit will not be working.Default empty is set for showing all posts', 'gym-builder' ),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
				'default' => '',
			),
			'gb_class_shortcode_more_btn' => array(
				'label'   => __( 'More Button', 'gym-builder' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Show or Hide More Button', 'gym-builder' ),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
			),
			'gb_class_shortcode_more_btn_text' => array(
				'label'   => __( 'Button Text', 'gym-builder' ),
				'type'    => 'text',
				'default' => __('More Classes','gym-builder'),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
				
			),
			'gb_class_shortcode_more_btn_url' => array(
				'label'   => __( 'Button URL', 'gym-builder' ),
				'type'    => 'text',
				'desc'    => __( 'Show or Hide More Button', 'gym-builder' ),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
			),
			'gb_class_shortcode_grid_columns'   => array(
				'label'   => __( 'Grid Layout', 'gym-builder' ),
				'type'    => 'select',
				'desc'    => __( 'you can select class grid columns and in class schedule layout post\'s grid columns will not be working', 'gym-builder' ),
				'default' => '3',
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
				'options' => [
					'1' => '1 Columns',
					'2' => '2 Columns',
					'3' => '3 Columns',
					'4' => '4 Columns',
					'5' => '5 Columns',
					'6' => '6 Columns',
				]
			),
		];

		return apply_filters( 'gb_class_shortcode_basic_meta', $fields );
	}

	public static function gb_class_shortcode_image_meta_settings() {
		$fields = [
			'gb_class_shortcode_thumb_width'     => array(
				'label'   => __( 'Class Thumbnail Width', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can set class post thumbnail image width', 'gym-builder' ),
				'default' => '570',
			),
			'gb_class_shortcode_thumb_height'    => array(
				'label'   => __( 'Class Thumbnail Height', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can set class post thumbnail image height', 'gym-builder' ),
				'default' => '400',
			),
			'gb_class_shortcode_thumb_crop'   => array(
				'label'   => __( 'Crop', 'gym-builder' ),
				'type'    => 'select',
				'desc'    => __( 'you can select class image thumbnail crop ( Hard crop means images are forced to crop but soft copy crops images its ratio', 'gym-builder' ),
				'default' => 'hard',
				'options' => [
					'hard' => 'Hard Crop',
					'soft' => 'Soft Crop',
				]
			),

		];

		return apply_filters( 'gb_class_shortcode_image_meta', $fields );
	}

	public static function gb_class_shortcode_filtering() {
		$fields = [
			'gb_class_include_shortcode'    => array(
				'label'   => __( 'Include Class', 'gym-builder' ),
				'desc'    => __( 'Select the classes that you want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => Functions::classesOptionsValue()
			),
			'gb_class_exclude_shortcode'    => array(
				'label'   => __( 'Exclude Class', 'gym-builder' ),
				'desc'    => __( 'Select the classes that you don\'t t want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => Functions::classesOptionsValue()
			),
			'gb_class_categories_shortcode' => array(
				'label'   => __( 'Class Categories', 'gym-builder' ),
				'desc'    => __( 'Select the class categories that you want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => self::get_categories_array()
			),
			'gb_class_order_by_shortcode'   => array(
				'label'   => __( 'Class OrderBy', 'gym-builder' ),
				'type'    => 'select',
				'default' => 'none',
				'options' => Helper::orderbyQueryOptions(),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
			),
			'gb_class_order_shortcode'   => array(
				'label'   => __( 'Class Order', 'gym-builder' ),
				'type'    => 'radio',
				'default' => 'ASC',
				'options' => array(
					'ASC'  => 'Ascending',
					'DESC' => 'Descending',
				),
				'required' => [
					'gb_class_shortcode_layout'=> ['layout-1','layout-3']
				],
			)
		];

		return apply_filters( 'gb_class_shortcode_filtering_meta', $fields );
	}
	public static function get_categories_array() {
		$categories_list = [];
		$terms           = get_terms( [
			'taxonomy' => self::$class_taxonomy,
			'hide_empty' => false
		] );
		if (!empty($terms)){
			foreach ( $terms as $term ) {
				$categories_list[ $term->term_id ] = $term->name;
			}
		}

		return apply_filters( "gym_builder_array_classes_category_list", $categories_list );
	}
}
