<?php
namespace GymBuilder\Inc\Controllers\TrainerLoginSystem;
use GymBuilder\Inc\Controllers\Helpers\Functions;

class GymTrainerEmail {

    public function send_login_credentials_email($user_data) {

        $subject = __('Your Gym Trainer Account Login Credentials', 'gym-builder');

        $message = sprintf(__('Hello %s,', 'gym-builder'), $user_data['name']) . "\n\n";
        $message .= __('Your gym trainer account has been set up successfully!', 'gym-builder') . "\n\n";
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

    /**
     * Send approval email when trainer post status changes from draft/pending to publish
     *
     * @param string $new_status New post status
     * @param string $old_status Old post status
     * @param WP_Post $post Post object
     */
    public function send_approval_email( $new_status, $old_status, $post ) {

        if ( $post->post_type !== Functions::$trainer_post_type ) {
            return;
        }

        if ( $new_status !== 'publish' ) {
            return;
        }

        if ( $old_status === 'publish' ) {
            return;
        }

        $email_sent = get_post_meta( $post->ID, 'gym_builder_approval_email_status', true );
        if ( 'sent' === $email_sent ) {
            return;
        }

        $trainer_id = $post->ID;
        $trainer_name = $post->post_title;
        $trainer_email = get_post_meta( $trainer_id, 'gym_builder_trainer_email', true );

        if ( empty( $trainer_email ) ) {
            return;
        }

        $user_id = GymTrainerHelpers::get_trainer_user_id( $trainer_id );

        if ( ! $user_id ) {
            return;
        }

        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }
        $password = wp_generate_password( 12, true, false );

        wp_set_password( $password, $user_id );

        $user_data = [
            'name' => $trainer_name,
            'username' => $trainer_email,
            'password' => $password,
            'email' => $trainer_email
        ];

        $email_sent = $this->send_login_credentials_email( $user_data );

        if ( $email_sent ) {
            update_post_meta( $trainer_id, 'gym_builder_trainer_approval_status', true );
            update_post_meta( $trainer_id, 'gym_builder_approval_email_status', 'sent' );
        }
    }
}