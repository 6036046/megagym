<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Abstracts;

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\FileLocations;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

abstract class Discount {
    use FileLocations;
	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * Class Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'show_notice' ] );
	}

	/**
	 * @return array
	 */
	abstract public function the_options();
	/**
	 * @return void
	 */
	public function show_notice() {
		$defaults      = [
			'download_link'  => 'https://wpdreamers.com/',
			'global_check'   => false,
			'plugin_name'    => 'Gym Builder',
			'image_url'      => self::get_file_locations('plugin_url').'assets/admin/images/100x100-logo.png',
			'option_name'    => '',
			'start_date'     => '',
			'end_date'       => '',
			'notice_for'     => 'Cyber Monday Deal!!',
			'notice_message' => '',
		];
		$options       = apply_filters( 'gym_builder_offer_notice', $this->the_options() );
		$this->options = wp_parse_args( $options, $defaults );
		$current       = time();
		$start         = strtotime( $this->options['start_date'] );
		$end           = strtotime( $this->options['end_date'] );

		if ( $this->options['global_check'] ) {
			return;
		}

		// Black Friday Notice.
		if ( ! Helper::gym_builder_has_pro_addons() && $start <= $current && $current <= $end ) {
			if ( get_option( $this->options['option_name'] ) != '1' ) {
				if ( ! isset( $GLOBALS['gym_builder__notice'] ) ) {
					$GLOBALS['gym_builder__notice'] = 'gym_builder__notice';
					$this->offer_notice();
				}
			}
		}
	}
	/**
	 * Black Friday Notice.
	 *
	 * @return void
	 */
	private function offer_notice() {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_script( 'jquery' );
			}
		);

		add_action(
			'admin_notices',
			function () {
				?>
				<style>
                    .notice.gym_builder-offer-notice {
                        --e-button-context-color: #0a4b78;
                        --e-button-context-color-dark: #005dd0;
                        --e-button-context-tint: rgb(75 47 157/4%);
                        --e-focus-color: rgb(75 47 157/40%);
                        display:grid;
                        grid-template-columns: 100px auto;
                        padding-top: 15px;
                        padding-bottom: 12px;
                        column-gap: 15px;
                    }

                    .gym_builder-offer-notice img {
                        grid-row: 1 / 4;
                        align-self: center;
                        justify-self: center;
                    }

                    .gym_builder-offer-notice h3,
                    .gym_builder-offer-notice p {
                        margin: 0 !important;
                    }

                    .gym_builder-offer-notice .notice-text {
                        margin: 0 0 2px;
                        padding: 5px 0;
                        max-width: 100%;
                        font-size: 14px;
                    }

                    .gym_builder-offer-notice .button-primary,
                    .gym_builder-offer-notice .button-dismiss {
                        display: inline-block;
                        border: 0;
                        border-radius: 3px;
                        background: var(--e-button-context-color-dark);
                        color: #fff;
                        vertical-align: middle;
                        text-align: center;
                        text-decoration: none;
                        white-space: nowrap;
                        margin-right: 5px;
                        transition: all 0.3s;
                    }

                    .gym_builder-offer-notice .button-primary:hover,
                    .gym_builder-offer-notice .button-dismiss:hover {
                        background: var(--e-button-context-color);
                        color: #fff;
                    }

                    .gym_builder-offer-notice .button-primary:focus,
                    .gym_builder-offer-notice .button-dismiss:focus {
                        box-shadow: 0 0 0 1px #fff, 0 0 0 3px var(--e-button-context-color);
                        background: var(--e-button-context-color);
                        color: #fff;
                    }

                    .gym_builder-offer-notice .button-dismiss {
                        border: 1px solid;
                        background: 0 0;
                        color: var(--e-button-context-color);
                        background: #fff;
                    }
				</style>

				<div class="gym_builder-offer-notice notice notice-info is-dismissible"
				     data-gym_builderdismissable="gym_builder_offer">
					<img alt="<?php echo esc_attr( $this->options['plugin_name'] ); ?>"
					     src="<?php echo esc_url( $this->options['image_url'] ); ?>"
					     width="100px"
					     height="100px"/>
					<h3><?php echo sprintf( '%s – %s', esc_html( $this->options['plugin_name'] ), esc_html( $this->options['notice_for'] ) ); ?></h3>

					<p class="notice-text">
						<?php echo wp_kses_post( $this->options['notice_message'] ); ?>
					</p>
					<p>
						<a class="button button-primary"
						   href="<?php echo esc_url( $this->options['download_link'] ); ?>" target="_blank">Buy Now</a>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>

				<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
                    (function ($) {
                        $(function () {
                            setTimeout(function () {
                                $('div[data-gym_builderdismissable] .notice-dismiss, div[data-gym_builderdismissable] .button-dismiss')
                                    .on('click', function (e) {
                                        e.preventDefault();
                                        $.post(ajaxurl, {
                                            'action': 'gym_builder_dismiss_offer_admin_notice',
                                            'nonce': <?php echo wp_json_encode( wp_create_nonce( 'gym_builder-offer-dismissible-notice' ) ); ?>
                                        });
                                        $(e.target).closest('.is-dismissible').remove();
                                    });
                            }, 1000);
                        });
                    })(jQuery);
				</script>
				<?php
			}
		);

		add_action(
			'wp_ajax_gym_builder_dismiss_offer_admin_notice',
			function () {
				check_ajax_referer( 'gym_builder-offer-dismissible-notice', 'nonce' );
				if ( ! empty( $this->options['option_name'] ) ) {
					update_option( $this->options['option_name'], '1' );
				}
				wp_die();
			}
		);
	}
}