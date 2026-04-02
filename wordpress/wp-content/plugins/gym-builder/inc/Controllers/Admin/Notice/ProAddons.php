<?php

namespace GymBuilder\Inc\Controllers\Admin\Notice;

use GymBuilder\Inc\Abstracts\Discount;
use GymBuilder\Inc\Traits\SingleTonTrait;


if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class ProAddons extends Discount {
	use SingleTonTrait;
	public function the_options(): array {
		return [
			'option_name'    => 'pro-addons-notice',
			'global_check'   => isset( $GLOBALS['pro_addons_notice'] ),
			'plugin_name'    => 'Gym Builder',
			'notice_for'     => 'Themes & Pro Addons are now available!!!',
			'download_link'  => 'https://wpdreamers.com/',
			'start_date'     => '10 December 2023',
			'end_date'       => '30 January 2029',
			'notice_message' => 'Acquire our  <b>Yoga WordPress Theme</b>,<b>Member Subscription with Stripe</b>, <b>Member Attendance System Addon</b>,<b> Member Registration Page Popup Addon</b>, <b> Zoom Integration Addon</b> and <b>Class Booking with Woo Payment Gateway & Payment System Addon</b>',
		];
	}
}