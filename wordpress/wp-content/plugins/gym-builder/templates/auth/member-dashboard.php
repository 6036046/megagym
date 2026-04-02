<?php
/**
 * Unified Dashboard Template - Routes to Trainer or Student Dashboard
 *
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $wp_version
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;

defined( 'ABSPATH' ) || exit;

Functions::get_header( $wp_version );

if ( ! is_user_logged_in() ) {
    Functions::get_template('auth/login-error');
    Functions::get_footer( $wp_version );
    return;
}


if ( current_user_can('gym_builder_trainer_dashboard') ) {
    Functions::get_template( 'auth/trainer-dashboard', ['wp_version' => $wp_version ] );
} elseif ( current_user_can('gym_builder_student_dashboard') ) {
    Functions::get_template( 'auth/student-dashboard', ['wp_version' => $wp_version] );
} else {
    Functions::get_template('auth/login-error');
}

Functions::get_footer( $wp_version );