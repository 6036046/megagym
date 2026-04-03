<?php

namespace GymBuilder\Inc\Controllers\Admin\Notice;
use GymBuilder\Inc\Traits\SingleTonTrait;
class Review {
	use SingleTonTrait;
	private function __construct() {
		add_action( 'admin_init', [ $this, 'check_installation_time' ], 10 );
		add_action( 'admin_init', [ $this, 'notice_actions' ], 5 );

		$this->remove_all_notices();
	}
	public function check_installation_time() {
		$nobug = get_option( 'gb_admin_review_spare_me' );
		$rated = get_option( 'gb_admin_review_rated' );

		if ( '1' == $nobug || 'yes' == $rated ) {
			return;
		}

		$now = strtotime( 'now' );

		$install_date = get_option( 'gb_plugin_activation_time', false );
		$showing_date = strtotime( '+15 days', $install_date );
		$remind_time  = get_option( 'gb_admin_review_remind_me' );

		if ( ! $remind_time ) {
			$remind_time = $install_date;
		}

		$remind_due = strtotime( '+20 days', $remind_time );

		if ( ! $now > $showing_date || $now < $remind_due ) {
			return;
		}

		add_action( 'admin_notices', [ $this, 'display_admin_notice' ] );
	}
	public function notice_actions() {
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'gb_notice_nonce' ) ) {
			return;
		}

		if ( ! empty( $_GET['gb_admin_review_spare_me'] ) ) {
			$spare_me = absint( $_GET['gb_admin_review_spare_me'] );

			if ( 1 == $spare_me ) {
				update_option( 'gb_admin_review_spare_me', '1' );
			}
		}

		if ( ! empty( $_GET['gb_admin_review_remind_me'] ) ) {
			$remind_me = absint( $_GET['gb_admin_review_remind_me'] );

			if ( 1 == $remind_me ) {
				$get_activation_time = strtotime( 'now' );

				update_option( 'gb_admin_review_remind_me', $get_activation_time );
			}
		}

		if ( ! empty( $_GET['gb_admin_review_rated'] ) ) {
			$gb_admin_review_rated = absint( $_GET['gb_admin_review_rated'] );

			if ( 1 == $gb_admin_review_rated ) {
				update_option( 'gb_admin_review_rated', 'yes' );
			}
		}
	}
	protected function gb_current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

		if ( ! $uri ) {
			return '';
		}

		return remove_query_arg(
			[
				'_wpnonce',
				'_wc_notice_nonce',
				'wc_db_update',
				'wc_db_update_nonce',
				'wc-hide-notice',
				'gb_admin_review_spare_me',
				'gb_admin_review_remind_me',
				'gb_admin_review_rated',
			],
			admin_url( $uri )
		);
	}
	public function display_admin_notice() {
		global $pagenow;

		$exclude = [
			'themes.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'options-writing.php',
			'options-reading.php',
			'options-discussion.php',
			'options-media.php',
			'options-permalink.php',
			'options-privacy.php',
			'admin.php',
			'import.php',
			'export.php',
			'site-health.php',
			'export-personal-data.php',
			'erase-personal-data.php',
		];

		if ( ! in_array( $pagenow, $exclude, true ) ) {
			$args         = [ '_wpnonce' => wp_create_nonce( 'gb_notice_nonce' ) ];
			$dont_disturb = add_query_arg( $args + [ 'gb_admin_review_spare_me' => '1' ], $this->gb_current_admin_url() );
			$remind_me    = add_query_arg( $args + [ 'gb_admin_review_remind_me' => '1' ], $this->gb_current_admin_url() );
			$rated        = add_query_arg( $args + [ 'gb_admin_review_rated' => '1' ], $this->gb_current_admin_url() );
			$reviewurl    = 'https://wordpress.org/support/plugin/gym-builder/reviews/?filter=5#new-post';
			$plugin_name  = 'Gym Builder – Fitness, Gym, Class Schedule Maker Plugin';
			?>
			<div class="notice gb-review-notice gb-review-notice--extended">
				<div class="gb-review-notice_content">
					<h3>Enjoying <?php echo esc_html( $plugin_name ); ?>? </h3>
					<p>Thank you for choosing <strong>Gym Builder</strong>. If you have found our plugin useful and makes you smile, please consider giving us a 5-star rating on WordPress.org. It will help us to grow.</p>
					<div class="gb-review-notice_actions">
						<a href="<?php echo esc_url( $reviewurl ); ?>"
						   class="gb-review-button gb-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
						<a href="<?php echo esc_url( $rated ); ?>"
						   class="gb-review-button gb-review-button--cta gb-review-button--outline"><span>😀 Already Rated!</span></a>
						<a href="<?php echo esc_url( $remind_me ); ?>"
						   class="gb-review-button gb-review-button--cta gb-review-button--outline"><span>🔔 Remind Me Later</span></a>
						<a href="<?php echo esc_url( $dont_disturb ); ?>"
						   class="gb-review-button gb-review-button--cta gb-review-button--error gb-review-button--outline"><span>😐 No Thanks </span></a>
					</div>
				</div>
			</div>
			<style>
				.gb-review-button--cta {
					--e-button-context-color: #4360ef;
					--e-button-context-color-dark: #1f3edc;
					--e-button-context-tint: rgb(75 47 157/4%);
					--e-focus-color: rgb(75 47 157/40%);
				}

				.gb-review-notice {
					position: relative;
					margin: 5px 20px 5px 2px;
					border: 1px solid #ccd0d4;
					background: #fff;
					box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
					font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
					border-inline-start-width: 4px;
				}
				.gb-review-notice.notice {
					padding: 0;
				}
				.gb-review-notice:before {
					position: absolute;
					top: -1px;
					bottom: -1px;
					left: -4px;
					display: block;
					width: 4px;
					background: -webkit-linear-gradient(0deg, #5d3dfd 0%, #6939c6 100%);
					background: linear-gradient(0deg, #5d3dfd 0%, #6939c6 100%);
					content: "";
				}

				.gb-review-notice_content {
					padding: 20px;
				}

				.gb-review-notice_actions > * + * {
					margin-inline-start: 8px;
					-webkit-margin-start: 8px;
					-moz-margin-start: 8px;
				}

				.gb-review-notice p {
					margin: 0;
					padding: 0;
					line-height: 1.5;
				}

				p + .gb-review-notice_actions {
					margin-top: 1rem;
				}

				.gb-review-notice h3 {
					margin: 0;
					font-size: 1.0625rem;
					line-height: 1.2;
				}

				.gb-review-notice h3 + p {
					margin-top: 8px;
				}

				.gb-review-button {
					display: inline-block;
					padding: 0.4375rem 0.75rem;
					border: 0;
					border-radius: 3px;;
					background: var(--e-button-context-color);
					color: #fff;
					vertical-align: middle;
					text-align: center;
					text-decoration: none;
					white-space: nowrap;
				}

				.gb-review-button:active {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.gb-review-button:focus {
					outline: 0;
					background: var(--e-button-context-color-dark);
					box-shadow: 0 0 0 2px var(--e-focus-color);
					color: #fff;
					text-decoration: none;
				}

				.gb-review-button:hover {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.gb-review-button.focus {
					outline: 0;
					box-shadow: 0 0 0 2px var(--e-focus-color);
				}

				.gb-review-button--error {
					--e-button-context-color: #d72b3f;
					--e-button-context-color-dark: #ae2131;
					--e-button-context-tint: rgba(215, 43, 63, 0.04);
					--e-focus-color: rgba(215, 43, 63, 0.4);
				}

				.gb-review-button.gb-review-button--outline {
					border: 1px solid;
					background: 0 0;
					color: var(--e-button-context-color);
				}

				.gb-review-button.gb-review-button--outline:focus {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}

				.gb-review-button.gb-review-button--outline:hover {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}
			</style>
			<?php
		}
	}
	public function remove_all_notices() {
		add_action(
			'in_admin_header',
			function () {
				$screen = get_current_screen();
				if ( in_array( $screen->base, [ 'gym-builder_page_gym-builder-get-help' ], true ) ) {
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );
				}
			},
			1000
		);
	}
}