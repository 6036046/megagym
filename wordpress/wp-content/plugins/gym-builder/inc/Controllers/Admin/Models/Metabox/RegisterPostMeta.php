<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Models\Metabox;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
use GymBuilder\Inc\Controllers\Admin\Models\Metabox\Fields\MetaFields;

class RegisterPostMeta{
	protected static $instance = null;

	private $fields_obj       = null;
	public $metaboxes        = array();
	public $metabox_fields   = array();

	private $nonce_action     = 'gym-builder_metabox_nonce';
	private $nonce_field      = 'gym-builder_metabox_nonce_secret';

	public function __construct() {

		$this->fields_obj = new MetaFields;
		add_action( 'init', array( $this, 'initialize' ), 12 );
	}

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function initialize() {
		if ( !is_admin() ) return;
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ));
		add_action( 'save_post', array( $this, 'save_metaboxes' ) );
		add_action( 'save_post_gym_builder_class', array( $this, 'class_schedule_save_metaboxes' ) );
	}



	public function add_meta_box( $id, $title, $post_types, $callback = '', $context = '', $priority = '', $fields = '' ) {

		$fields = apply_filters( 'gym-builder_postmeta_field_' . $id, $fields );
		$metaboxes = array(
			'title'         => $title,
			'callback'      => $callback,
			'post_type'     => $post_types,
			'context'       => empty( $context ) ? 'normal' : $context,
			'priority'      => $priority,
			'callback_args' => $fields,
		);
		$this->metaboxes[$id] = apply_filters( 'gym-builder_metabox_' . $id, $metaboxes );

		$this->metabox_fields[$id] = $fields['fields'];
	}

	public  function register_meta_boxes() {

		if(is_array($this->metaboxes) && !empty($this->metaboxes)){
			foreach ( $this->metaboxes as $metabox_id => $args ) {
				add_meta_box(
					$metabox_id,
					$args['title'],
					empty( $args['callback'] ) ? array( $this, 'display_metaboxes' ) : $args['callback'],
					$args['post_type'],
					$args['context'],
					$args['priority'],
					$args['callback_args']
				);
			}
		}

	}

	public function display_metaboxes( $post, $metabox ) {
		$fields = $metabox['args']['fields'];
		wp_nonce_field( $this->nonce_action, $this->nonce_field );
		$this->fields_obj->display_fields( $fields, $post->ID );
	}

	public function class_schedule_save_metaboxes( $post_id ) {
		if ( empty( $_POST[$this->nonce_field] ) || !check_admin_referer( $this->nonce_action, $this->nonce_field ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		if ( get_post_type( $post_id ) !== 'gym_builder_class' ){
			return $post_id;
		}
		if ( ! isset( $_POST['gym_builder_class_schedule'] ) ){
			return $post_id;
		}
		if ( isset( $schedule_data['hidden'] ) ) {
			unset( $schedule_data['hidden'] );
		}
		$schedule_data = $_POST['gym_builder_class_schedule'];
		$final_schedule = [];

		foreach ( $schedule_data as $item ) {
			$old = get_post_meta( $post_id, 'gym_builder_v2_class_schedule', true );

			$week = isset($item['week']) ? sanitize_text_field($item['week']) : '';
			$start_time = isset($item['start_time']) ? sanitize_text_field($item['start_time']) : '';
			$end_time = isset($item['end_time']) ? sanitize_text_field($item['end_time']) : '';
			$maximum_members = isset($item['maximum_member_allow_booking']) && $item['maximum_member_allow_booking'] !== ''
				? intval($item['maximum_member_allow_booking'])
				: 0;

			if ( empty($week) || empty($start_time) || empty($end_time) ) {
				continue;
			}

			$time_range = $start_time . ' - ' . $end_time;

			if ( ! isset( $final_schedule[$post_id] ) ) {
				$final_schedule[$post_id] = [];
			}

			if ( ! isset( $final_schedule[$post_id][$week] ) ) {
				$final_schedule[$post_id][$week] = [];
			}
			$total_booked = $old[$post_id][$week][$time_range]['total_booked'] ?? 0;

			$final_schedule[$post_id][$week][ $time_range] = [
				'total_slot' => $maximum_members,
				'total_booked' => $total_booked
			];
		}

		update_post_meta( $post_id, 'gym_builder_v2_class_schedule', $final_schedule );
	}
	public function save_metaboxes( $post_id ) {

		if ( empty( $_POST[$this->nonce_field] ) || !check_admin_referer( $this->nonce_action, $this->nonce_field ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->metabox_fields as $fields ) {
			foreach ( $fields as $field => $data ) {
				$this->save_single_meta( $field, $data, $post_id );
			}
		}
	}

	public function save_single_meta( $field, $data, $post_id ){
		if( isset( $_POST[ $field ] ) ){
			$old = get_post_meta( $post_id, $field, true );
			if ( $data['type'] == 'group' ) {
				$new = $this->sanitize_group_field( $_POST[ $field ], $data['value'] );
			}
			elseif ( $data['type'] == 'repeater' ) {
				$new = $this->sanitize_repeater_field( $_POST[ $field ], $data['value'] );
			}
			else{
				$new = $this->sanitize_field( $_POST[ $field ], $data['type'] );
			}

			// Update
			if ( $new != $old ) {
				if ( $new == array() ) { // assuming repeater field is empty array
					delete_post_meta( $post_id, $field);
				}
				else {
					update_post_meta( $post_id, $field, $new );
				}
			}
		}
		else{
			if ( $data['type'] == 'checkbox' || $data['type'] == 'multi_checkbox' || $data['type'] == 'multi_select' || $data['type'] == 'multi_select2' || $data['type'] == 'ajax_select') {
				delete_post_meta( $post_id, $field);
			}
		}
	}

	public function sanitize_group_field( $data, $type ){
		foreach ( $type as $key => $value ) {
			$data[$key] = $this->sanitize_field( $data[$key], $value['type'] );
		}
		return $data;
	}

	public function filter_empty( $data ){
		return array_filter( $data );
	}

	public function sanitize_repeater_field( $data, $type ){
		unset( $data['hidden'] ); // unset hidden
		foreach ( $data as $key => $value ) {
			foreach ( $value as $key2 => $value2 ) {
				$fieldtype = $type[$key2]["type"];
				$data[$key][$key2] = $this->sanitize_field( $data[$key][$key2], $fieldtype );
			}
		}
		$data = array_values( $data ); //rearrange
		return $data;
	}

	public function sanitize_field( $data, $type ){
		switch ( $type ) {
			case 'multi_select':
			case 'multi_checkbox':
			case 'multi_select2':
			case 'ajax_select':
				$data = array_filter( $data, 'sanitize_text_field' );
				break;
			case 'textarea':
				$data = trim( $data );
				$data = wp_kses_post( $data );
				break;
			case 'textarea_html':
				$data = trim( $data );
				break;
			case 'color_picker':
				$data = sanitize_hex_color( $data );
				break;
			default:
				$data = sanitize_text_field( $data );
				break;
		}
		return $data;
	}

}
