<?php
namespace GymBuilder\Inc\Controllers\StudentLoginSystem;

use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;

class GymStudentEmail {

    public function send_login_credentials_email($user_data) {

        $subject = __('Your Gym Student Account Login Credentials', 'gym-builder');

        $message = sprintf(__('Hello %s,', 'gym-builder'), $user_data['name']) . "\n\n";
        $message .= __('Your gym student account has been set up successfully!', 'gym-builder') . "\n\n";
        $message .= __('Login Details:', 'gym-builder') . "\n";
        $message .= sprintf(__('Username: %s', 'gym-builder'), $user_data['username']) . "\n";
        $message .= sprintf(__('Email: %s', 'gym-builder'), $user_data['email']) . "\n";
        $message .= sprintf(__('Password: %s', 'gym-builder'), $user_data['password']) . "\n";
        $message .= sprintf(__('Login URL: %s', 'gym-builder'), esc_url(GymTrainerHelpers::get_member_login_page_url())) . "\n\n";
        $message .= __('Please login and change your password for security.', 'gym-builder') . "\n\n";
        $message .= __('Best regards,', 'gym-builder') . "\n";
        $message .= get_bloginfo('name');

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );

        return wp_mail($user_data['email'], $subject, $message, $headers);
    }
}
