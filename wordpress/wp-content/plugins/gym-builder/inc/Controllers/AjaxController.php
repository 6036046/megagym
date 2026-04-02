<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers;

use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Models\GymBuilderMail;
use GymBuilder\Inc\Controllers\StudentLoginSystem\GymStudentEmail;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

class AjaxController {
	use Constants, SingleTonTrait;

	public function init() {
		add_action( 'wp_ajax_gym_builder_insert_members', [ $this, 'gym_builder_insert_members' ] );
		add_action( 'wp_ajax_gym_builder_delete_member', [ $this, 'delete_single_member_data' ] );
		add_action( 'wp_ajax_gym_builder_edit_members', [ $this, 'edit_member_data' ] );
		add_action( 'wp_ajax_gym_builder_send_member_email', [ $this, 'send_member_mail' ] );
		add_action( 'wp_ajax_gym_builder_save_settings', [ $this, 'save_options_settings' ] );
		add_action( 'wp_ajax_gym_builder_fetch_membership_package_posts', [
			$this,
			'get_membership_package_posts_by_taxonomy'
		] );
		add_action('wp_ajax_gym_builder_update_booking_availability',[$this,'update_booking_availability']);
		add_action('wp_ajax_gym_builder_update_booking_total_slot',[$this,'update_booking_total_slot']);
	}

	public function gym_builder_insert_members() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		global $wpdb;
		$membership_package_type_name = null;
		$membership_package_type_id   = intval( $_POST['packageType'] );
		$class_id                     = intval( $_POST['classId'] );
		$class_weekday                = sanitize_text_field( $_POST['scheduleWeekday'] );
		$class_time                   = sanitize_text_field( $_POST['scheduleTime'] );
		if ( $membership_package_type_id != null ) {
			$membership_package_type_obj  = get_term_by( 'id', intval( $_POST['packageType'] ), self::$membership_package_taxonomy );
			$membership_package_type_name = $membership_package_type_obj->name;
		}

		$member_user_id = Helper::generate_member_user_id();
		$data           = [
			'member_name'               => sanitize_text_field( $_POST['memberName'] ),
			'member_address'            => sanitize_textarea_field( $_POST['memberAddress'] ),
			'member_email'              => sanitize_email( $_POST['memberEmail'] ),
			'member_phone'              => sanitize_text_field( $_POST['memberPhone'] ),
			'member_age'                => intval( $_POST['memberAge'] ),
			'membership_status'         => $_POST['membershipStatus'] ? 1 : 0,
			'member_joining_date'       => ! empty( $_POST['memberJoiningDate'] ) ? sanitize_text_field( date( 'Y-m-d', strtotime( $_POST['memberJoiningDate'] ) ) ) : '',
			'membership_duration_start' => ! empty( $_POST['membershipDuration'] ) ? sanitize_text_field( date( 'Y-m-d', strtotime( $_POST['membershipDuration'][0] ) ) ) : '',
			'membership_duration_end'   => ! empty( $_POST['membershipDuration'] ) ? sanitize_text_field( date( 'Y-m-d', strtotime( $_POST['membershipDuration'][1] ) ) ) : '',
			'member_gender'             => sanitize_text_field( $_POST['memberGender'] ),
			'membership_package_type'   => sanitize_text_field( $membership_package_type_name ),
			'membership_package_name'   => sanitize_text_field( $_POST['packageName'] ),
			'package_name_id'           => intval( $_POST['packageNameId'] ),
			'membership_classes'        => sanitize_text_field( $_POST['classesName'] ),
			'file_url'                  => esc_url_raw( $_POST['fileUrl'] ),
			'member_user_id'            => sanitize_text_field( $member_user_id ),
			'schedule_weekday'          => $class_weekday,
			'schedule_time'             => $class_time,
			'class_id'                  => $class_id,
		];
		$inserted       = $wpdb->insert(
			"{$wpdb->prefix}gym_builder_members",
			$data,
			[
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d'
			]
		);

		if ( $inserted ) {
			$member_db_id = $wpdb->insert_id;

			if ( $class_id != null ) {
				$class_schedule_v2 = get_post_meta( $class_id, 'gym_builder_v2_class_schedule', true );
				$class_day = Helper::day_name_map()[ $class_weekday ];
				if (isset($class_schedule_v2[$class_id][$class_day][$class_time])){
					$class_schedule_v2[$class_id][$class_day][$class_time]['total_booked'] = $class_schedule_v2[$class_id][$class_day][$class_time]['total_booked'] +1;
					update_post_meta($class_id,'gym_builder_v2_class_schedule',$class_schedule_v2);
				}

			}

			$member_email = sanitize_email( $_POST['memberEmail'] );
			if ( ! empty( $member_email ) && is_email( $member_email ) && ! email_exists( $member_email ) ) {
				$member_name = sanitize_text_field( $_POST['memberName'] );
				$helpers     = new GymTrainerHelpers();
				$username    = $helpers->generate_unique_username( $member_email, $member_name );
				$password    = wp_generate_password( 12, true );
				$wp_user_id  = wp_create_user( $username, $password, $member_email );

				if ( ! is_wp_error( $wp_user_id ) ) {
					$user = new \WP_User( $wp_user_id );
					$user->set_role( 'gym_builder_student' );

					$wpdb->update(
						"{$wpdb->prefix}gym_builder_members",
						array( 'wp_user_id' => $wp_user_id ),
						array( 'id' => $member_db_id ),
						array( '%d' ),
						array( '%d' )
					);

					update_user_meta( $wp_user_id, 'gym_builder_student_member_id', $member_db_id );

					$email_class = new GymStudentEmail();
					$email_class->send_login_credentials_email( array(
						'name'     => $member_name,
						'username' => $username,
						'password' => $password,
						'email'    => $member_email,
					) );
				}
			}

			wp_send_json_success( __( 'Member added successfully.', 'gym-builder' ) );
		} else {
			wp_send_json_error( __( 'Failed to add member.', 'gym-builder' ) );
		}

	}

	public function delete_single_member_data() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		$member_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( $member_id <= 0 ) {
			wp_send_json_error( 'Invalid member ID.' );
		}
		global $wpdb;
		$table_name = $wpdb->prefix . 'gym_builder_members';

		$member = $wpdb->get_row( $wpdb->prepare( "SELECT wp_user_id FROM $table_name WHERE id = %d", $member_id ) );
		$wp_user_id = ! empty( $member->wp_user_id ) ? intval( $member->wp_user_id ) : 0;

		$delete_sql    = $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $member_id );
		$delete_result = $wpdb->query( $delete_sql );
		if ( $delete_result !== false ) {
			if ( $wp_user_id ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
				wp_delete_user( $wp_user_id );
			}
			wp_send_json_success( __( 'Member deleted successfully.', 'gym-builder' ) );
		} else {
			wp_send_json_error( __( 'Failed to delete member.', 'gym-builder' ) );
		}

	}

	public function edit_member_data() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		$member_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( $member_id <= 0 ) {
			wp_send_json_error( 'Invalid member ID.' );
		}
		global $wpdb;
		$membership_package_type_name = null;
		$membership_package_type_id   = intval( $_POST['packageType'] );
		$class_id                     = intval( $_POST['classId'] );
		$class_weekday                = !empty($_POST['scheduleWeekday']) ? sanitize_text_field( $_POST['scheduleWeekday'] ):'';
		$class_time                   = !empty($_POST['scheduleTime']) ? sanitize_text_field( $_POST['scheduleTime'] ):'';
		if ( $membership_package_type_id != null ) {
			$membership_package_type_obj  = get_term_by( 'id', intval( $_POST['packageType'] ), self::$membership_package_taxonomy );
			$membership_package_type_name = $membership_package_type_obj->name;
		}

		$data    = [
			'member_address'            => sanitize_textarea_field( $_POST['memberAddress'] ),
			'member_email'              => sanitize_email( $_POST['memberEmail'] ),
			'member_phone'              => sanitize_text_field( $_POST['memberPhone'] ),
			'member_age'                => intval( $_POST['memberAge'] ),
			'membership_status'         => $_POST['membershipStatus'] ? 1 : 0,
			'membership_duration_start' => ! empty( $_POST['membershipDuration'] ) ? sanitize_text_field( date( 'Y-m-d', strtotime( $_POST['membershipDuration'][0] ) ) ) : '',
			'membership_duration_end'   => ! empty( $_POST['membershipDuration'] ) ? sanitize_text_field( date( 'Y-m-d', strtotime( $_POST['membershipDuration'][1] ) ) ) : '',
			'member_gender'             => sanitize_text_field( $_POST['memberGender'] ),
			'membership_package_type'   => sanitize_text_field( $membership_package_type_name ),
			'membership_package_name'   => sanitize_text_field( $_POST['packageName'] ),
			'package_name_id'           => intval( $_POST['packageNameId'] ),
			'membership_classes'        => sanitize_text_field( $_POST['classesName'] ),
			'file_url'                  => esc_url_raw( $_POST['fileUrl'] ),
			'schedule_weekday'          => $class_weekday,
			'schedule_time'             => $class_time,
			'class_id'                  => $class_id,
		];
		$table_name    = $wpdb->prefix . 'gym_builder_members';
		$sql = $wpdb->prepare("SELECT schedule_weekday,schedule_time,class_id,wp_user_id,member_email FROM $table_name WHERE id = %d", $member_id);
		$result = $wpdb->get_row($sql, ARRAY_A);

		$previous_class_weekday = $result['schedule_weekday'] ?? '';
		$previous_class_time = $result['schedule_time'] ?? '';
        $previous_class_id       = intval( $result['class_id'] ?? 0 );

		$updated = $wpdb->update(
			"{$wpdb->prefix}gym_builder_members",
			$data,
			[ 'id' => $member_id ],
			[
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d'
			]

		);

		if ( $updated > 0 ) {
			$new_email      = sanitize_email( $_POST['memberEmail'] );
			$old_wp_user_id = ! empty( $result['wp_user_id'] ) ? intval( $result['wp_user_id'] ) : 0;
			$old_email      = $result['member_email'] ?? '';

			if ( ! empty( $new_email ) && is_email( $new_email ) && empty( $old_wp_user_id ) ) {
				if ( ! email_exists( $new_email ) ) {
					$member_name = sanitize_text_field( $_POST['memberName'] ?? '' );
					if ( empty( $member_name ) ) {
						$member_row = $wpdb->get_row( $wpdb->prepare( "SELECT member_name FROM $table_name WHERE id = %d", $member_id ) );
						$member_name = $member_row ? $member_row->member_name : 'student';
					}
					$helpers    = new GymTrainerHelpers();
					$username   = $helpers->generate_unique_username( $new_email, $member_name );
					$password   = wp_generate_password( 12, true );
					$wp_user_id = wp_create_user( $username, $password, $new_email );

					if ( ! is_wp_error( $wp_user_id ) ) {
						$user = new \WP_User( $wp_user_id );
						$user->set_role( 'gym_builder_student' );

						$wpdb->update(
							$table_name,
							array( 'wp_user_id' => $wp_user_id ),
							array( 'id' => $member_id ),
							array( '%d' ),
							array( '%d' )
						);

						update_user_meta( $wp_user_id, 'gym_builder_student_member_id', $member_id );

						$email_class = new GymStudentEmail();
						$email_class->send_login_credentials_email( array(
							'name'     => $member_name,
							'username' => $username,
							'password' => $password,
							'email'    => $new_email,
						) );
					}
				}
			} elseif ( ! empty( $new_email ) && $new_email !== $old_email && $old_wp_user_id ) {
				wp_update_user( array(
					'ID'         => $old_wp_user_id,
					'user_email' => $new_email,
				) );
			}

            if ( $class_id ) {
                $new_class_day = Helper::day_name_map()[ $class_weekday ];
                $old_class_day = ! empty( $previous_class_weekday ) && isset( Helper::day_name_map()[ $previous_class_weekday ] )
                    ? Helper::day_name_map()[ $previous_class_weekday ]
                    : '';


                if ( $class_id !== $previous_class_id ) {

                    $new_schedule = get_post_meta( $class_id, 'gym_builder_v2_class_schedule', true );

                    if ( isset( $new_schedule[ $class_id ][ $new_class_day ][ $class_time ] ) ) {
                        $new_schedule[ $class_id ][ $new_class_day ][ $class_time ]['total_booked'] += 1;
                    }
                    update_post_meta( $class_id, 'gym_builder_v2_class_schedule', $new_schedule );
                    if ( $previous_class_id && $old_class_day && $previous_class_time ) {
                        $old_schedule = get_post_meta( $previous_class_id, 'gym_builder_v2_class_schedule', true );

                        if ( isset( $old_schedule[ $previous_class_id ][ $old_class_day ][ $previous_class_time ] ) ) {
                            $old_schedule[ $previous_class_id ][ $old_class_day ][ $previous_class_time ]['total_booked'] -= 1;
                        }
                        update_post_meta( $previous_class_id, 'gym_builder_v2_class_schedule', $old_schedule );
                    }


                } elseif ( $previous_class_weekday !== $class_weekday || $previous_class_time !== $class_time ) {

                    $schedule = get_post_meta( $class_id, 'gym_builder_v2_class_schedule', true );

                    if ( isset( $schedule[ $class_id ][ $new_class_day ][ $class_time ] ) ) {
                        $schedule[ $class_id ][ $new_class_day ][ $class_time ]['total_booked'] += 1;
                    }

                    if ( $old_class_day && $previous_class_time && isset( $schedule[ $class_id ][ $old_class_day ][ $previous_class_time ] ) ) {
                        $schedule[ $class_id ][ $old_class_day ][ $previous_class_time ]['total_booked'] -= 1;
                    }

                    update_post_meta( $class_id, 'gym_builder_v2_class_schedule', $schedule );
                }
            }
			wp_send_json_success( __( 'Member Info Updated successfully.', 'gym-builder' ) );
		} elseif($updated === 0){
			wp_send_json_success( __( 'No changes made.', 'gym-builder' ) );
		}else {

			wp_send_json_error( __( 'Failed updated member info.', 'gym-builder' ) );
		}

	}

	public function send_member_mail() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}

		$name           = sanitize_text_field( $_POST['name'] );
		$email_to       = sanitize_email( $_POST['email'] );
		$message        = sanitize_textarea_field( $_POST['message'] );
		$mail_subject   = esc_html__( 'Hello ' . $name, 'gym-builder' );
		$mail_from_name = SettingsApi::get_option( 'member_id_generate_title', 'gym_builder_global_settings' ) ?: get_bloginfo( 'name' );
		$mail_from      = SettingsApi::get_option( 'member_sender_mail', 'gym_builder_global_settings' ) ?: wp_get_current_user()->data->user_email;
		$email_args     = [
			'to'        => $email_to,
			'subject'   => $mail_subject,
			'mail_body' => $message,
			'from'      => $mail_from,
			'from_name' => $mail_from_name,
		];

		if ( isset( $_FILES['mail_file'] ) ) {
			$email_args['file'] = $_FILES;
			$mail_sent          = GymBuilderMail::send_mail( true, $email_args );
		} else {
			$mail_sent = GymBuilderMail::send_mail( false, $email_args );
		}

		if ( $mail_sent ) {
			wp_send_json_success( __( 'Mail Sent Successfully.', 'gym-builder' ) );
		} else {
			wp_send_json_error( __( 'Failed to mail sent.', 'gym-builder' ) );
		}

	}

	public function save_options_settings() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		$this->page_options_settings_save( 'gym_builder_page_settings', $_POST['gym_builder_page_settings'] );
		$this->permalink_options_settings_save( 'gym_builder_permalinks_settings', $_POST['gym_builder_permalinks_settings'] );
		$this->class_options_settings_save( 'gym_builder_class_settings', $_POST['gym_builder_class_settings'] );
		$this->trainer_options_settings_save( 'gym_builder_trainer_settings', $_POST['gym_builder_trainer_settings'] );
		$this->style_options_settings_save( 'gym_builder_style_settings', $_POST['gym_builder_style_settings'] );
		$this->global_options_settings_save( 'gym_builder_global_settings', $_POST['gym_builder_global_settings'] );

		if ( Helper::has_zoom_integration_addon() ) {
			$this->zoom_integration_settings_save( 'gym_builder_zoom_integration_settings', $_POST['gym_builder_zoom_integration_settings'] );
		}

		wp_send_json_success( __( 'Settings Save Successfully.', 'gym-builder' ) );
	}

	public function class_options_settings_save( $key, $data ) {

		$class_settings                              = get_option( $key, [] );
		$class_settings['class_time_format']         = sanitize_text_field( $data['class_time_format'] ) ?? '12';
		$class_settings['class_archive_style']       = sanitize_text_field( $data['class_archive_style'] ) ?? 'layout-1';
		$class_settings['class_posts_per_page']      = sanitize_text_field( $data['class_posts_per_page'] ) ?? '9';
		$class_settings['class_grid_columns']        = sanitize_text_field( $data['class_grid_columns'] ) ?? '3';
		$class_settings['class_page_layout']         = sanitize_text_field( $data['class_page_layout'] ) ?? 'full-width';
		$class_settings['class_single_page_layout']  = sanitize_text_field( $data['class_single_page_layout'] ) ?? 'full-width';
		$class_settings['include_class']             = ! empty( $data['include_class'] ) ? array_map( 'sanitize_text_field', $data['include_class'] ) : [];
		$class_settings['exclude_class']             = ! empty( $data['exclude_class'] ) ? array_map( 'sanitize_text_field', $data['exclude_class'] ) : [];
		$class_settings['class_categories']          = ! empty( $data['class_categories'] ) ? array_map( 'sanitize_text_field', $data['class_categories'] ) : [];
		$class_settings['class_orderBy']             = sanitize_text_field( $data['class_orderBy'] ) ?? 'none';
		$class_settings['class_order']               = sanitize_text_field( $data['class_order'] ) ?? 'ASC';
		$class_settings['class_thumbnail_width']     = sanitize_text_field( $data['class_thumbnail_width'] ) ?? '570';
		$class_settings['class_thumbnail_height']    = sanitize_text_field( $data['class_thumbnail_height'] ) ?? '400';
		$class_settings['class_thumbnail_hard_crop'] = sanitize_text_field( $data['class_thumbnail_hard_crop'] ) ?? 'on';
		$class_settings['slider_autoplay']           = sanitize_text_field( $data['slider_autoplay'] ) ?? 'on';
		$class_settings['slider_loop']               = sanitize_text_field( $data['slider_loop'] ) ?? 'on';
		$class_settings['centered_slider']           = sanitize_text_field( $data['centered_slider'] ) ?? 'off';
		$class_settings['slides_per_view']           = sanitize_text_field( $data['slides_per_view'] ) ?? '3';

		update_option( $key, $class_settings );
	}

	public function trainer_options_settings_save( $key, $data ) {
		$trainer_settings                                = get_option( $key, [] );
		$trainer_settings['trainer_archive_style']       = sanitize_text_field( $data['trainer_archive_style'] ) ?? 'layout-1';
		$trainer_settings['trainer_posts_per_page']      = sanitize_text_field( $data['trainer_posts_per_page'] ) ?? '9';
		$trainer_settings['trainer_grid_columns']        = sanitize_text_field( $data['trainer_grid_columns'] ) ?? '3';
		$trainer_settings['trainer_page_layout']         = sanitize_text_field( $data['trainer_page_layout'] ) ?? 'full-width';
		$trainer_settings['trainer_single_page_layout']  = sanitize_text_field( $data['trainer_single_page_layout'] ) ?? 'full-width';
		$trainer_settings['include_trainer']             = ! empty( $data['include_trainer'] ) ? array_map( 'sanitize_text_field', $data['include_trainer'] ) : [];
		$trainer_settings['exclude_trainer']             = ! empty( $data['exclude_trainer'] ) ? array_map( 'sanitize_text_field', $data['exclude_trainer'] ) : [];
		$trainer_settings['trainer_categories']          = ! empty( $data['trainer_categories'] ) ? array_map( 'sanitize_text_field', $data['trainer_categories'] ) : [];
		$trainer_settings['trainer_orderBy']             = sanitize_text_field( $data['trainer_orderBy'] ) ?? 'none';
		$trainer_settings['trainer_order']               = sanitize_text_field( $data['trainer_order'] ) ?? 'ASC';
		$trainer_settings['trainer_thumbnail_width']     = sanitize_text_field( $data['trainer_thumbnail_width'] ) ?? '570';
		$trainer_settings['trainer_thumbnail_height']    = sanitize_text_field( $data['trainer_thumbnail_height'] ) ?? '400';
		$trainer_settings['trainer_thumbnail_hard_crop'] = sanitize_text_field( $data['trainer_thumbnail_hard_crop'] ) ?? 'on';

		update_option( $key, $trainer_settings );
	}

	public function style_options_settings_save( $key, $data ) {
		$style_settings                                          = get_option( $key, [] );
		$style_settings['gym_builder_primary_color']             = sanitize_text_field( $data['gym_builder_primary_color'] ) ?? '#005dd0';
		$style_settings['gym_builder_secondary_color']           = sanitize_text_field( $data['gym_builder_secondary_color'] ) ?? '#0a4b78';
		$style_settings['gym_builder_class_title_color']         = sanitize_text_field( $data['gym_builder_class_title_color'] ) ?? '';
		$style_settings['gym_builder_class_content_color']       = sanitize_text_field( $data['gym_builder_class_content_color'] ) ?? '';
		$style_settings['gym_builder_class_schedule_color']      = sanitize_text_field( $data['gym_builder_class_schedule_color'] ) ?? '';
		$style_settings['gym_builder_class_trainer_color']       = sanitize_text_field( $data['gym_builder_class_trainer_color'] ) ?? '';
		$style_settings['gym_builder_class_table_title_color']   = sanitize_text_field( $data['gym_builder_class_table_title_color'] ) ?? '';
		$style_settings['gym_builder_class_table_border_color']  = sanitize_text_field( $data['gym_builder_class_table_border_color'] ) ?? '';
		$style_settings['gym_builder_class_table_heading_color'] = sanitize_text_field( $data['gym_builder_class_table_heading_color'] ) ?? '';
		$style_settings['gym_builder_trainer_title_color']       = sanitize_text_field( $data['gym_builder_trainer_title_color'] ) ?? '';
		$style_settings['gym_builder_trainer_designation_color'] = sanitize_text_field( $data['gym_builder_trainer_designation_color'] ) ?? '';
		$style_settings['gym_builder_trainer_content_color']     = sanitize_text_field( $data['gym_builder_trainer_content_color'] ) ?? '';
		$style_settings['gym_builder_trainer_bg_color']          = sanitize_text_field( $data['gym_builder_trainer_bg_color'] ) ?? '';

		update_option( $key, $style_settings );
	}

	public function global_options_settings_save( $key, $data ) {
		$global_settings                             = get_option( $key, [] );
		$global_settings['member_id_generate_title'] = sanitize_text_field( $data['member_id_generate_title'] ) ?? '';
		$global_settings['shop_address']             = sanitize_text_field( $data['shop_address'] ) ?? '';
		$global_settings['footer_note']              = sanitize_text_field( $data['footer_note'] ) ?? '';
		$global_settings['contact_number']           = sanitize_text_field( $data['contact_number'] ) ?? '';
		$global_settings['member_sender_mail']       = sanitize_text_field( $data['member_sender_mail'] ) ?? '';
		$global_settings['currency']                 = sanitize_text_field( $data['currency'] ) ?? 'USD';
		$global_settings['currency_position']        = sanitize_text_field( $data['currency_position'] ) ?? 'left';
		update_option( $key, $global_settings );
	}

	public static function zoom_integration_settings_save( $key, $data ) {
		$zoom_integration_settings                             = get_option( $key, [] );
		$zoom_integration_settings['class_type_badge_display'] = sanitize_text_field( $data['class_type_badge_display'] ) ?? 'on';
		$zoom_integration_settings['offline_class_badge_text'] = sanitize_text_field( $data['offline_class_badge_text'] ) ?? '';
		$zoom_integration_settings['online_class_badge_text']  = sanitize_text_field( $data['online_class_badge_text'] ) ?? '';
		$zoom_integration_settings['get_zoom_btn_text']        = sanitize_text_field( $data['get_zoom_btn_text'] ) ?? '';
		$zoom_integration_settings['online_badge_color']       = sanitize_text_field( $data['online_badge_color'] ) ?? '';
		$zoom_integration_settings['offline_badge_color']      = sanitize_text_field( $data['offline_badge_color'] ) ?? '';
		update_option( $key, $zoom_integration_settings );
	}

	public function permalink_options_settings_save( $key, $data ) {
		$permalinks_settings                          = get_option( $key, [] );
		$permalinks_settings['class_base']            = sanitize_text_field( $data['class_base'] ) ?? '';
		$permalinks_settings['class_category_base']   = sanitize_text_field( $data['class_category_base'] ) ?? '';
		$permalinks_settings['trainer_base']          = sanitize_text_field( $data['trainer_base'] ) ?? '';
		$permalinks_settings['trainer_category_base'] = sanitize_text_field( $data['trainer_category_base'] ) ?? '';

		update_option( $key, $permalinks_settings );
	}

	public function page_options_settings_save( $key, $data ) {
		$page_settings             = get_option( $key, [] );
		$page_settings['classes']  = sanitize_text_field( $data['classes'] ) ?? '';
		$page_settings['trainers'] = sanitize_text_field( $data['trainers'] ) ?? '';
		$page_settings['member_auth'] = sanitize_text_field( $data['member_auth'] ) ?? '';
		$page_settings['member_dashboard'] = sanitize_text_field( $data['member_dashboard'] ) ?? '';
		update_option( $key, $page_settings );
	}

	public function get_membership_package_posts_by_taxonomy() {
		$taxonomy_id = intval( $_POST['taxonomy_id'] );

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_post_meta_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}

		$args = array( 'post_type' => self::$membership_package_post_type );

		if ( $taxonomy_id ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => self::$membership_package_taxonomy,
					'field'    => 'term_id',
					'terms'    => $taxonomy_id
				)
			);
		}

		$posts = get_posts( $args );

		$response = array();
		foreach ( $posts as $post ) {
			$price      = Functions::get_price_with_label( $post->ID ) ?? '0';
			$response[] = array(
				'id'    => $post->ID,
				'price' => $price,
				'title' => $post->post_title
			);
		}

		wp_send_json( $response );
	}

	public function update_booking_availability(  ) {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		$class_id = isset( $_POST['class_id'] ) ? intval( $_POST['class_id'] ) : 0;
		if ( $class_id <= 0 ) {
			wp_send_json_error( 'Invalid Class ID.' );
		}
		$class_day = isset( $_POST['day'] ) ? sanitize_text_field( $_POST['day'] ) : '';
		$class_time = isset( $_POST['time'] ) ? sanitize_text_field( $_POST['time'] ) : '';
		$class_array_time = explode(" - ",$class_time);
		$class_start_time = $class_array_time[0];
		$class_end_time = $class_array_time[1];
		$class_schedule = get_post_meta($class_id,'gym_builder_class_schedule',true);
		$class_schedule_v2 = get_post_meta($class_id,'gym_builder_v2_class_schedule',true);
		if (isset($class_schedule_v2[$class_id][$class_day][$class_time])){
			$class_schedule_v2[$class_id][$class_day][$class_time]['total_slot'] = 0;
			$class_schedule_v2[$class_id][$class_day][$class_time]['total_booked'] = 0;
			if (update_post_meta($class_id,'gym_builder_v2_class_schedule',$class_schedule_v2)){
				if ($class_schedule){
					foreach ($class_schedule as &$schedule){
						if ( empty( $schedule['week'] ) || $schedule['week'] == 'none' || empty( $schedule['start_time'] ) ) {
							continue;
						}
						if ($schedule['week'] == $class_day && $class_start_time == $schedule['start_time'] && $class_end_time == $schedule['end_time'] ){
							$schedule['maximum_member_allow_booking'] = 0;
							break;
						}
					}
				}
				update_post_meta($class_id,'gym_builder_class_schedule',$class_schedule);
				wp_send_json_success( __( 'Booking total slot & total booking count updated successfully.', 'gym-builder' ) );
			}else{
				wp_send_json_error( __( 'Already total slot and total booking are set 0.', 'gym-builder' ) );
			}
		}else{
			wp_send_json_error( __( 'Failed to reset booking slot & booking count.', 'gym-builder' ) );
		}
	}

	public function update_booking_total_slot(  ) {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gym_builder_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}
		$class_id = isset( $_POST['class_id'] ) ? intval( $_POST['class_id'] ) : 0;
		if ( $class_id <= 0 ) {
			wp_send_json_error( 'Invalid Class ID.' );
		}
		$class_day = isset( $_POST['day'] ) ? sanitize_text_field( $_POST['day'] ) : '';
		$class_time = isset( $_POST['time'] ) ? sanitize_text_field( $_POST['time'] ) : '';
		$class_array_time = explode(" - ",$class_time);
		$class_start_time = $class_array_time[0];
		$class_end_time = $class_array_time[1];
		$new_slot = isset( $_POST['new_slot'] ) ? intval( $_POST['new_slot'] ) : '';


		$class_schedule_v2 = get_post_meta($class_id,'gym_builder_v2_class_schedule',true);
		$class_schedule = get_post_meta($class_id,'gym_builder_class_schedule',true);

		if (isset($class_schedule_v2[$class_id][$class_day][$class_time])){
			$class_schedule_v2[$class_id][$class_day][$class_time]['total_slot'] = $new_slot;
			if (update_post_meta($class_id,'gym_builder_v2_class_schedule',$class_schedule_v2)){
				if ($class_schedule){
					foreach ($class_schedule as &$schedule){
						if ( empty( $schedule['week'] ) || $schedule['week'] == 'none' || empty( $schedule['start_time'] ) ) {
							continue;
						}
						if ($schedule['week'] == $class_day && $class_start_time == $schedule['start_time'] && $class_end_time == $schedule['end_time'] ){
							$schedule['maximum_member_allow_booking'] = $new_slot;
							break;
						}
					}
				}
				update_post_meta($class_id,'gym_builder_class_schedule',$class_schedule);
				wp_send_json_success( __( 'Booking slot updated successfully.', 'gym-builder' ) );
			}else{
				wp_send_json_error( __( 'Failed updated booking slot.', 'gym-builder' ) );
			}
		}else{
			wp_send_json_error( __( 'Failed updated booking slot.', 'gym-builder' ) );
		}

	}
}
