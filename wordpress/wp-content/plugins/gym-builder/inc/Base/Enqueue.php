<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Base;

use GymBuilder\Inc\Base\BaseController;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;
use \GymBuilder\Inc\Traits\Constants;


class Enqueue extends BaseController {
	use Constants;

	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
	}

	public function enqueue( $screen ) {

		$_screen = get_current_screen();
		wp_register_style( 'gym-builder-icons', $this->plugin_url . 'assets/icons/css/gym-builder-icons.css', array(), self::$plugin_version );
		wp_register_style( 'gym-builder-admin-style', $this->plugin_url . 'assets/admin/css/gym-builder-admin.css', array(), self::$plugin_version );

		wp_register_style( 'jquery-ui-style', $this->plugin_url . 'assets/vendor/jquery-ui.css', array(), self::$plugin_version );

		wp_register_style( 'select2-style', $this->plugin_url . 'assets/vendor/select2.min.css', array(), self::$plugin_version );

		wp_register_style( 'jquery-timepicker-style', $this->plugin_url . 'assets/vendor/jquery.timepicker.css', array(), self::$plugin_version );

		wp_register_style( 'gym-builder-meta-fields-style', $this->plugin_url . 'assets/admin/css/meta-fields.css', array(), self::$plugin_version );

		wp_register_script( 'select2', $this->plugin_url . 'assets/vendor/select2.min.js', array(
			'jquery',
			'wp-color-picker'
		), self::$plugin_version, true );

		wp_register_script( 'jquery-timepicker-script', $this->plugin_url . 'assets/vendor/jquery.timepicker.min.js', array( 'jquery' ), self::$plugin_version, true );

		wp_register_script( 'gym-builder-meta-fields-script', $this->plugin_url . 'assets/admin/js/meta-fields.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-datepicker',
			'wp-color-picker'
		), self::$plugin_version, true );

		//react admin settings page script
		wp_register_script( 'gym-builder-admin-settings-script', $this->plugin_url . 'assets/admin/js/admin-settings-page.js', array( 'jquery', ), self::$plugin_version, true );
		//member crud operation js
		wp_register_script( 'gym-builder-admin-page-script', $this->plugin_url . 'assets/admin/js/admin-page.js', array( 'jquery', ), self::$plugin_version, true );

        // dashboard
        wp_register_script( 'gym-builder-admin-dashboard-script', $this->plugin_url . 'assets/admin/js/admin-dashboard.js', array( 'jquery', ), self::$plugin_version, true );

		// export / import
		wp_register_script( 'gym-builder-export-import-script', $this->plugin_url . 'assets/admin/js/admin-export-import.js', array( 'jquery' ), self::$plugin_version, true );
		wp_register_style( 'gym-builder-export-import-style', $this->plugin_url . 'assets/admin/css/export-import.css', array(), self::$plugin_version );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'select2-style' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'select2' );

		if ( ( 'edit.php' == $screen || 'post.php' == $screen || 'post-new.php' == $screen ) && ( $this->assets_enqueue_posts_type( $_screen ) ) ) {

			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'jquery-timepicker-style' );
			wp_enqueue_style( 'gym-builder-meta-fields-style' );
			wp_enqueue_style( 'gym-builder-icons' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-timepicker-script' );
			wp_enqueue_script( 'gym-builder-meta-fields-script' );
			$admin_meta_localize_data = array(
				'memberPackageTaxUrl' => esc_url( admin_url( 'edit-tags.php?taxonomy=gb_pricing_plan_category&post_type=gb_pricing_plan' ) ),
				'ajaxurl'             => esc_url( admin_url( 'admin-ajax.php' ) ),
				'post_meta_nonce'     => wp_create_nonce( 'gym_builder_post_meta_nonce' ),
				'rest_url'            => esc_url_raw( rest_url() )
			);
			wp_localize_script( 'gym-builder-meta-fields-script', 'adminMetaData', $admin_meta_localize_data );
		}
		//settings api page script
		if ( 'toplevel_page_gym_builder' == $screen ) {
			//react settings page script
			wp_enqueue_script( 'gym-builder-admin-settings-script' );
			wp_localize_script(
				'gym-builder-admin-settings-script',
				'gymbuilderParams',
				$this->gym_builder_localize_params()
			);
		}
		if ( 'gym-builder_page_gym-builder-members' == $screen ) {

			wp_enqueue_media();
			wp_enqueue_script( 'gym-builder-admin-page-script' );
			wp_localize_script(
				'gym-builder-admin-page-script',
				'gymbuilderParams',
				$this->gym_builder_localize_params()
			);

		}
        if ( 'gym-builder_page_gym-builder-dashboard' == $screen ) {

            wp_enqueue_script( 'gym-builder-admin-dashboard-script' );
            wp_localize_script(
                'gym-builder-admin-dashboard-script',
                'gymbuilderParams',
                $this->gym_builder_localize_params()
            );

        }

		if ( 'gym-builder_page_gym-builder-import-export' == $screen ) {
			wp_enqueue_style( 'gym-builder-export-import-style' );
			wp_enqueue_script( 'gym-builder-export-import-script' );
			wp_localize_script(
				'gym-builder-export-import-script',
				'gymbuilderParams',
				$this->gym_builder_localize_params()
			);
		}

		wp_enqueue_style( 'gym-builder-admin-style' );

	}

	public function frontend_enqueue_scripts() {
		$this->register_style();
		wp_enqueue_style( 'select2-style' );
		wp_enqueue_style( 'gym-builder-swiper' );
		wp_enqueue_style( 'gym-builder-icons' );
		wp_enqueue_style( 'gym-builder-style' );
		$this->dynamic_styles();
		$this->register_script();
		$this->load_swiper();
		$frontend_localize_data = Helper::fitness_calculator_translatable_text()
		                          + [
			                          'has_zoom_integration_addon' => Helper::has_zoom_integration_addon() ? 'true' : 'false',
			                          'has_class_booking_and_payment_addon' => Helper::has_class_booking_and_payment_addon() ? 'true' : 'false',
			                          'empty_user_id_text'         => __( 'Please enter your member user id', 'gym-builder' ),
			                          'empty_package_price_text'         => __( 'Please select at least one pricing plan.', 'gym-builder' ),
			                          'ajaxUrl'                    => esc_url( admin_url( 'admin-ajax.php' ) ),
                                       'dashboard_url' =>GymTrainerHelpers::get_member_dashboard_url(),
                                      'success_message' => __('Registration successful! Check your email for login details.', 'gym-builder'),
                                      'nonce' => wp_create_nonce('gym_builder_nonce'),
                                        'skill_name_placeholder' => __('Skill name (e.g. Yoga)', 'gym-builder'),
                                        'skill_percentage_placeholder' => __('Percentage', 'gym-builder'),
                                        'remove_skill_text' => __('Remove skill', 'gym-builder'),
                                        'success_message' => __('Registration successful! Your account is pending approval.', 'gym-builder'),
                                        'valid_name_error' => __('Please enter a valid name.','gym-builder'),
                                        'email_error' => __('Please enter a valid email address.','gym-builder'),
                                        'password_error' => __('Password must be at least 8 characters long.','gym-builder'),

		                          ];
		wp_localize_script( 'gym-builder-script', 'gymBuilderData', $frontend_localize_data );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'gym-builder-script' );
	}

	private function dynamic_styles() {
		ob_start();
		require_once $this->plugin_path . 'inc/DynamicStyles/Frontend.php';
		$dynamic_css = ob_get_clean();
		$dynamic_css = $this->optimized_css( $dynamic_css );
		wp_register_style( 'gym-builder-dynamic', false );
		wp_enqueue_style( 'gym-builder-dynamic' );
		wp_add_inline_style( 'gym-builder-dynamic', $dynamic_css );
	}

	private function optimized_css( $css ) {
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( [ "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ], ' ', $css );

		return $css;
	}

	public function register_style() {
		wp_register_style( 'select2-style', $this->plugin_url . 'assets/vendor/select2.min.css', array(), self::$plugin_version );
		wp_register_style( 'gym-builder-icons', $this->plugin_url . 'assets/icons/css/gym-builder-icons.css', array(), self::$plugin_version );
		wp_register_style( 'gym-builder-swiper', $this->plugin_url . 'assets/vendor/swiper.min.css', array(), self::$plugin_version );
		wp_register_style( 'gym-builder-style', $this->plugin_url . 'assets/public/css/gym-builder.css', array(), self::$plugin_version );
	}

	public function register_script() {
		wp_register_script( 'select2', $this->plugin_url . 'assets/vendor/select2.min.js', array( 'jquery', ), self::$plugin_version, true );
		wp_register_script( 'gym-builder-script', $this->plugin_url . 'assets/public/js/app.js', array( 'jquery' ), self::$plugin_version, true );
	}

	public function load_swiper() {
		$default_swiper_handle = 'swiper';
		$default_swiper_path   = $this->plugin_url . 'assets/vendor/swiper.min.js';
		if ( defined( 'ELEMENTOR_ASSETS_PATH' ) ) {
			$is_swiper8_enable = get_option( 'elementor_experiment-e_swiper_latest' );

			if ( $is_swiper8_enable == 'active' ) {
				$el_swiper_path = 'lib/swiper/v8/swiper.min.js';
			} else {
				$el_swiper_path = 'lib/swiper/swiper.min.js';
			}

			$elementor_swiper_path = ELEMENTOR_ASSETS_PATH . $el_swiper_path;

			if ( file_exists( $elementor_swiper_path ) ) {
				$default_swiper_path = ELEMENTOR_ASSETS_URL . $el_swiper_path;
			}
		}
		wp_register_script( $default_swiper_handle, $default_swiper_path, array( 'jquery' ), self::$plugin_version, true );
		wp_enqueue_script( $default_swiper_handle );
	}

	public function assets_enqueue_posts_type( $screen ) {
		if ( in_array($screen->post_type,$this->scripts_post_type_support(),true)  ) {
			return true;
		} else {
			return false;
		}
	}

	public function gym_builder_localize_params() {
		$member_id_card_title = SettingsApi::get_option( 'member_id_generate_title', 'gym_builder_global_settings' ) ?: '';
		$shop_address         = SettingsApi::get_option( 'shop_address', 'gym_builder_global_settings' ) ?: '';
		$footer_note          = SettingsApi::get_option( 'footer_note', 'gym_builder_global_settings' ) ?: '';
		$contact_number       = SettingsApi::get_option( 'contact_number', 'gym_builder_global_settings' ) ?: '';

		return [
			'member_id_card_title'       => $member_id_card_title,
			'shop_address'               => $shop_address,
			'footer_note'                => $footer_note,
			'contact_number'             => $contact_number,
			'ajaxurl'                    => esc_url( admin_url( 'admin-ajax.php' ) ),
			'homeurl'                    => home_url(),
			'restApiUrl'                 => esc_url_raw( rest_url() ),
			'rest_nonce'                 => wp_create_nonce( 'wp_rest' ),
			'gb_admin_nonce'             => wp_create_nonce( 'gym_builder_nonce' ),
			'plugin_file_url'            => $this->plugin_url,
			'class_layout'               => Helper::class_page_layout(),
			'trainer_layout'               => Helper::trainer_page_layout(),
			'has_zoom_integration_addon' => Helper::has_zoom_integration_addon() ? 'true' : 'false',
		];
	}
	public function scripts_post_type_support(  ) {
		return apply_filters(
			'gym_builder_scripts_supported_post_types',
			[
				'gym_builder_class',
				'gym_builder_trainer',
				'gb_class_shortcode',
				'gb_trainer_shortcode',
				'gb_pricing_plan',
				'gb_fitness_shortcode'
			]
		);
	}
}