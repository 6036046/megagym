<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Controllers\Admin\Models\Metabox\RegisterPostMeta;


class TrainerShortcodeMeta {
	use Constants;

	public static function init() {
		add_action('admin_init',[__CLASS__,'get_categories_array'],2);
		add_action('admin_init',[__CLASS__,'shortcode_metabox_added'],9);
	}

	public static function shortcode_metabox_added(  ) {
		$Postmeta = RegisterPostMeta::getInstance();

		$Postmeta->add_meta_box( 'gb_trainer_shortcode_settings', __( 'Trainer Settings', 'gym-builder' ), array( self::$trainer_shortcode_post_type ), '', '', 'high', array(
			'fields' => self::gb_trainer_shortcode_meta_setting()
		) );

		$Postmeta->add_meta_box( 'gb_trainer_shortcode_image_settings', __( 'Trainer Settings', 'gym-builder' ), array( self::$trainer_shortcode_post_type ), '', '', 'high', array(
			'fields' => self::gb_trainer_shortcode_image_meta_settings()
		) );
		$Postmeta->add_meta_box( 'gb_trainer_shortcode_filtering_settings', __( 'Trainer Filtering', 'gym-builder' ), array( self::$trainer_shortcode_post_type ), '', '', 'default', array(
			'fields' => self::gb_trainer_shortcode_filtering()
		) );
	}

	public static function gb_trainer_shortcode_layout() {
		$layout = [
			'layout-1' => [
				'title'      => 'Layout 1',
				'img_source' => 'class-layout-1'
			],
			'layout-2' => [
				'title'      => 'Layout 2',
				'img_source' => 'trainer-layout-2'
			],

		];

		return apply_filters( 'gym_builder_trainer_shortcode_layout', $layout );
	}

	public static function gb_trainer_shortcode_meta_setting() {
		$fields = [
			
			'gb_trainer_shortcode_layout'         => array(
				'label'   => __( 'Trainer Layout', 'gym-builder' ),
				'type'    => 'image_radio',
				'desc'    => __( 'you can select different trainer layout from here', 'gym-builder' ),
				'default' => 'layout-1',
				'options' => self::gb_trainer_shortcode_layout()
			),
			'gb_trainer_shortcode_posts_per_page' => array(
				'label'   => __( 'Post\'s Limit', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can select trainer post item limit.Default empty is set for showing all posts', 'gym-builder' ),
				'default' => '',
			),
			'gb_trainer_shortcode_more_btn' => array(
				'label'   => __( 'More Button', 'gym-builder' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Show or Hide More Button', 'gym-builder' ),
			),
			'gb_trainer_shortcode_more_btn_text' => array(
				'label'   => __( 'Button Text', 'gym-builder' ),
				'type'    => 'text',
				'default' => __('More Trainers','gym-builder'),
				
			),
			'gb_trainer_shortcode_more_btn_url' => array(
				'label'   => __( 'Button URL', 'gym-builder' ),
				'type'    => 'text',
				'desc'    => __( 'Show or Hide More Button', 'gym-builder' ),
			),
			'gb_trainer_shortcode_grid_columns'   => array(
				'label'   => __( 'Grid Layout', 'gym-builder' ),
				'type'    => 'select',
				'desc'    => __( 'you can select trainer grid columns.', 'gym-builder' ),
				'default' => '3',
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

		return apply_filters( 'gb_trainer_shortcode_basic_meta', $fields );
	}

	public static function gb_trainer_shortcode_image_meta_settings() {
		$fields = [
			'gb_trainer_shortcode_thumb_width'     => array(
				'label'   => __( 'Trainer Thumbnail Width', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can set trainer post thumbnail image width', 'gym-builder' ),
				'default' => '570',
			),
			'gb_trainer_shortcode_thumb_height'    => array(
				'label'   => __( 'Trainer Thumbnail Height', 'gym-builder' ),
				'type'    => 'number',
				'desc'    => __( 'you can set trainer post thumbnail image height', 'gym-builder' ),
				'default' => '400',
			),
			'gb_trainer_shortcode_thumb_crop'   => array(
				'label'   => __( 'Crop', 'gym-builder' ),
				'type'    => 'select',
				'desc'    => __( 'you can select trainer image thumbnail crop ( Hard crop means images are forced to crop but soft copy crops images its ratio', 'gym-builder' ),
				'default' => 'hard',
				'options' => [
					'hard' => 'Hard Crop',
					'soft' => 'Soft Crop',
				]
			),

		];

		return apply_filters( 'gb_trainer_shortcode_image_meta', $fields );
	}

	public static function gb_trainer_shortcode_filtering() {
		$fields = [
			'gb_trainer_include_shortcode'    => array(
				'label'   => __( 'Include Trainer', 'gym-builder' ),
				'desc'    => __( 'Select the trainers that you want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => Functions::trainersOptionsValue()
			),
			'gb_trainer_exclude_shortcode'    => array(
				'label'   => __( 'Exclude Trainer', 'gym-builder' ),
				'desc'    => __( 'Select the trainers that you don\'t t want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => Functions::trainersOptionsValue()
			),
			'gb_trainer_categories_shortcode' => array(
				'label'   => __( 'Trainer Categories', 'gym-builder' ),
				'desc'    => __( 'Select the trainer categories that you want to display on class shortcode', 'gym-builder' ),
				'type'    => 'multi_select',
				'options' => self::get_categories_array()
			),
			'gb_trainer_order_by_shortcode'   => array(
				'label'   => __( 'Trainer OrderBy', 'gym-builder' ),
				'type'    => 'select',
				'default' => 'none',
				'options' => Helper::orderbyQueryOptions(),
				
			),
			'gb_trainer_order_shortcode'   => array(
				'label'   => __( 'Trainer Order', 'gym-builder' ),
				'type'    => 'radio',
				'default' => 'ASC',
				'options' => array(
					'ASC'  => 'Ascending',
					'DESC' => 'Descending',
				),
				
			)
		];

		return apply_filters( 'gb_trainer_shortcode_filtering_meta', $fields );
	}
	public static function get_categories_array() {
		$categories_list = [];
		$terms           = get_terms( [
			'taxonomy' => self::$trainer_taxonomy,
			'hide_empty' => false
		] );
		if (!empty($terms)){
			foreach ( $terms as $term ) {
				$categories_list[ $term->term_id ] = $term->name;
			}
		}

		return apply_filters( "gym_builder_array_trainer_category_list", $categories_list );
	}
}
