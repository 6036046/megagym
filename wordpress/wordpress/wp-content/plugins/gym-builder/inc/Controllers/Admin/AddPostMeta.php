<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use \GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use \GymBuilder\Inc\Controllers\Admin\Models\Metabox\RegisterPostMeta;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\IconTraits;

class AddPostMeta{

	use Constants,IconTraits;
    public static $time_picker_format;

    public static function init(){

	    add_action('admin_init',[__CLASS__,'get_membership_package_categories'],2);
	    add_action('admin_init',[__CLASS__,'post_metabox_added'],9);

    }

	public static function post_metabox_added(  ) {
//		self::$time_picker_format = (SettingsApi::get_option( 'class_time_format','gym_builder_class_settings')==24 ? 'time_picker_24':'time_picker');
		self::$time_picker_format = 'time_picker';
		$postmeta = RegisterPostMeta::getInstance();
		self::class_post_meta($postmeta);
		self::trainer_post_meta($postmeta);
		self::membership_package_meta($postmeta);
	}
	public static function class_post_meta( $postmeta_instance ) {
		$postmeta_instance->add_meta_box( 'gym_builder_class_pricing_info', __( 'Course Pricing Info & Details ', 'gym-builder' ), array( 'gym_builder_class' ), '', '', 'high', array(
			'fields' => self::class_pricing_info_field()
		) );
		$postmeta_instance->add_meta_box( 'gym_builder_class_schedule', __( 'Schedule', 'gym-builder' ), array( 'gym_builder_class' ), '', '', 'high', array(
			'fields' => self::gym_builder_class_schedule_fields()
		) );
		$postmeta_instance->add_meta_box( 'gym_builder_class_media', __( 'Course Icon', 'gym-builder' ),array( "gym_builder_class" ),'',
			'side',
			'default', array(
				'fields' => array(
					"gym_builder_class_icon" => array(
						'label' => __( 'Choose Icon', 'gym-builder' ),
						'type'  => 'icon_select',
						'desc'  => __( "All class layouts are not display this icon", 'gym-builder' ),
						'options' => self::get_gym_builder_icons(),
					),
				)
			)
		);
	}

	public static function trainer_post_meta( $postmeta_instance ) {
		$postmeta_instance->add_meta_box( 'gym_builder_trainer_info', __( 'Trainer Info', 'gym-builder' ), array( 'gym_builder_trainer' ), '', '', 'high', array(
			'fields' => self::trainer_info_meta_field()
		) );

		$postmeta_instance->add_meta_box( 'trainer_skills', __( 'Trainer Skills', 'gym-builder' ), array( 'gym_builder_trainer' ), '', '', 'high', array(
			'fields' => array(
				'gym_builder_trainer_skill' => array(
					'type'  => 'repeater',
					'button' => __( 'Add New Skill', 'gym-builder' ),
					'value'  => array(
						'skill_name' => array(
							'label' => __( 'Skill Name', 'gym-builder' ),
							'type'  => 'text',
							'desc'  => __( 'eg. Yoga', 'gym-builder' ),
						),
						'skill_value' => array(
							'label' => __( 'Skill Percentage (%)', 'gym-builder' ),
							'type'  => 'text',
							'desc'  => __( 'eg. 75', 'gym-builder' ),
						),
					)
				),
			)
		) );
	}
	public static function membership_package_meta( $postmeta_instance ) {
		$postmeta_instance->add_meta_box( 'gym_builder_member_package_options', __( 'Membership Package Options', 'gym-builder' ), array( 'gb_pricing_plan' ), '', '', 'high', array(
			'fields' => array(
				'gym_builder_package_price' => array(
					'label' => __( 'Package Price', 'gym-builder' ),
					'type'  => 'text',
				),
				'gym_builder_package_price_duration' => array(
					'label' => __( 'Package Price Duration', 'gym-builder' ),
					'type'  => 'text',
					'desc'  => __( 'Example: per year/per month', 'gym-builder' ),
				),
				'gym_builder_package_features' => array(
					'type'  => 'repeater',
					'button' => __( 'Add New Package Feature', 'gym-builder' ),
					'value'  => array(
						'feature_icon' => array(
							'label' => __( 'Feature Item Icon', 'gym-builder' ),
							'type'  => 'select',
							'options' => array(
								'none' => __( 'Select a Icon Name', 'gym-builder' ),
								'check'  => __( 'Check', 'gym-builder' ),
								'uncheck'  => __( 'Uncheck', 'gym-builder' ),
							),
						),
						'feature_item' => array(
							'label' => __( 'Package Feature Item', 'gym-builder' ),
							'type'  => 'text',
						),
					)
				),
				'gym_builder_package_button_text' => array(
					'label' => __( 'Button Text', 'gym-builder' ),
					'type'  => 'text',
					'desc'  => __( 'Enter button text eg. Buy Now!', 'gym-builder' ),
				),
				'gym_builder_package_button_url' => array(
					'label' => __( 'Button URL', 'gym-builder' ),
					'type'  => 'text',
					'desc'  => __( 'Enter button url', 'gym-builder' ),
				),
			)
		) );
	}

	public static function get_membership_package_categories(  ) {
		$package_list = [];
		$terms           = get_terms( [
			'taxonomy'   => self::$membership_package_taxonomy,
			'hide_empty' => false
		] );
		$package_list[0] = esc_html__( 'Select a Course Package Name', 'gym-builder' );
		if ($terms){
			foreach ( $terms as $term ) {
				$package_list[$term->term_id] = $term->name;
			}
		}
		return $package_list;
	}

	public static function class_pricing_info_field(  ) {
		$pricing_info = [
			'gym_builder_course_duration_time' => array(
				'label' => __( 'Course Per Day Duration', 'gym-builder' ),
				'type'  => 'text',
				'desc'  => __( 'As a example: 60 min / Session', 'gym-builder' ),
			),
			'gym_builder_pricing_package_name' => array(
				'label' => __( 'Course Pricing Package Name', 'gym-builder' ),
				'type'  => 'select',
				'desc'  => __( 'This value comes from membership package types', 'gym-builder' ),
				'default' => '0',
				'options' => self::get_membership_package_categories()
			),
			'gym_builder_package_prices' => array(
				'label' => __( 'Course Pricing Package List & Price', 'gym-builder' ),
				'type'  => 'multi_select',
				'desc'  => __( 'Those values are  membership package name', 'gym-builder' ),
				'options' => Functions::get_membership_packages_with_price()
			),
		];
		return apply_filters('class_pricing_info_fields',$pricing_info);
	}

	public static function trainer_info_meta_field(  ) {
		$trainer_info = [
			'gym_builder_trainer_designation' => array(
				'label' => __( 'Trainer Designation', 'gym-builder' ),
				'type'  => 'text',
			),
            'gym_builder_trainer_email' => array(
                'label' => __( 'Trainer Email', 'gym-builder' ),
                'type'  => 'text',
                'desc'  => __( 'The trainer account will be created using this email address. If no email is provided for the trainer, the trainer account will not be created.', 'gym-builder' ),
            ),
			'gym_builder_trainer_header' => array(
				'label' => __( 'Trainer Socials Links', 'gym-builder' ),
				'type'  => 'header',
				'desc'  => __( 'Enter trainer social links here', 'gym-builder' ),
			),
			'gym_builder_trainer_socials' => array(
				'type'  => 'group',
				'value'  => Functions::trainer_socials()
			),
		];
		return apply_filters('gym_builder_trainer_info',$trainer_info);
	}

	public static function gym_builder_class_schedule_fields(  ) {
		$fields = [
			'gym_builder_class_color' => array(
				'label' => __( 'Color', 'gym-builder' ),
				'type'  => 'color_picker',
				'desc'  => __( 'Used in Routine Style', 'gym-builder' ),
			),
			'gym_builder_class_button_text' => array(
				'label' => __( 'Button Text', 'gym-builder' ),
				'type'  => 'text',
				'desc'  => __( 'Enter button text eg. Join Now!', 'gym-builder' ),
			),
			'gym_builder_class_button_url' => array(
				'label' => __( 'Button URL', 'gym-builder' ),
				'type'  => 'text',
				'desc'  => __( 'Enter button url', 'gym-builder' ),
			),
			'gym_builder_class_schedule' => array(
				'type'  => 'repeater',
				'button' => __( 'Add New Schedule', 'gym-builder' ),
				'value'  => self::gym_builder_class_schedule_repeater_fields()
			),
		];
		return apply_filters('gym_builder_class_schedule_fields',$fields);
	}

	public static function gym_builder_class_schedule_repeater_fields(  ) {
		$fields = [
			'trainer' => array(
				'label' => __( 'Trainer', 'gym-builder' ),
				'type'  => 'select',
				'options' => Functions::get_trainers(),
				'default'  => 'default',
			),
			'week' => array(
				'label' => __( 'Weekday', 'gym-builder' ),
				'type'  => 'select',
				'options' => array(
					'none' => __( 'Select a Weekday', 'gym-builder' ),
					'mon'  => __( 'Monday', 'gym-builder' ),
					'tue'  => __( 'Tuesday', 'gym-builder' ),
					'wed'  => __( 'Wednesday', 'gym-builder' ),
					'thu'  => __( 'Thursday', 'gym-builder' ),
					'fri'  => __( 'Friday', 'gym-builder' ),
					'sat'  => __( 'Saturday', 'gym-builder' ),
					'sun'  => __( 'Sunday', 'gym-builder' ),
				),
			),
			'start_time' => array(
				'label' => __( 'Start Time', 'gym-builder' ),
				'type'  => self::$time_picker_format,
			),
			'end_time' => array(
				'label' => __( 'End Time', 'gym-builder' ),
				'type'  => self::$time_picker_format,
			),
			'maximum_member_allow_booking' => array(
				'label' => __( 'Maximum Member Allowed Booking', 'gym-builder' ),
				'type'  => 'number',
				'desc'  => __( 'Maximum numbers of member can book in this session', 'gym-builder' ),
			),
		];
		return apply_filters('gym_builder_class_schedule_repeater_fields',$fields);

	}

}