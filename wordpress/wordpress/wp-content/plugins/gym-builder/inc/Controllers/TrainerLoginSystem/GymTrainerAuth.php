<?php
namespace GymBuilder\Inc\Controllers\TrainerLoginSystem;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Traits\Constants;

class GymTrainerAuth {
    use Constants;
    public function create_trainer_role() {
        if (!get_role('gym_builder_trainer')) {
            add_role('gym_builder_trainer', __('Gym Trainer','gym-builder'), array(
                'read' => true,
                'gym_builder_trainer_dashboard' => true,
            ));
        }
    }
    public function handle_trainer_registration() {
        if (is_user_logged_in()) {
            wp_send_json_error(__('You are already logged in.', 'gym-builder'));
        }

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gym_builder_nonce')) {
            wp_send_json_error('Security check failed');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = 'gym_reg_limit_' . md5($ip);
        $attempts = (int) get_transient($transient_key);

        if ($attempts >= 3) {
            wp_send_json_error(__('Too many registration attempts. Please try again later.', 'gym-builder'));
        }

        set_transient($transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);

        $trainer_name = sanitize_text_field($_POST['trainer_name']);
        $trainer_email = sanitize_email($_POST['trainer_email']);
        $trainer_password = $_POST['trainer_password'];

        if (empty($trainer_name) || empty($trainer_email) || empty($trainer_password)) {
            wp_send_json_error(__('Please fill in all required fields.', 'gym-builder'));
            return;
        }

        if (strlen($trainer_password) < 8) {
            wp_send_json_error(__('Password must be at least 8 characters long.', 'gym-builder'));
            return;
        }

        if (!is_email($trainer_email)) {
            wp_send_json_error(__('Please provide a valid email address.', 'gym-builder'));
            return;
        }

        if (email_exists($trainer_email)) {
            wp_send_json_error(__('If this email is not already in use, a trainer account will be created. Please check your email.', 'gym-builder'));
            return;
        }

        $helpers = new GymTrainerHelpers();
        $username = $helpers->generate_unique_username($trainer_email, $trainer_name);
        $user_id = wp_create_user($username, $trainer_password, $trainer_email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(__('Failed to create user account. Please try again.', 'gym-builder'));
        }

        $user = new \WP_User($user_id);

        $user->set_role('gym_builder_trainer');


        $trainer_post_data = array(
            'post_title'   => $trainer_name,
            'post_type'    => Functions::$trainer_post_type,
            'post_status'  => 'draft',
            'post_author'  => $user_id,
        );

        $trainer_id = wp_insert_post($trainer_post_data);

        if (is_wp_error($trainer_id)) {
            wp_delete_user($user_id);
            wp_send_json_error(__('Failed to create trainer profile.', 'gym-builder'));
            return;
        }

        update_user_meta($user_id, 'gym_builder_trainer_user_id', $trainer_id);
        update_post_meta($trainer_id, 'gym_builder_trainer_user_id', $user_id);


        if (!empty($_POST['trainer_designation'])) {
            $designation = sanitize_text_field($_POST['trainer_designation']);
            update_post_meta($trainer_id, 'gym_builder_trainer_designation', $designation);
        }

        update_post_meta($trainer_id, 'gym_builder_trainer_email', $trainer_email);

        $social_links = array(
            'facebook'  => isset($_POST['trainer_social_facebook']) ? esc_url_raw($_POST['trainer_social_facebook']) : '',
            'twitter'   => isset($_POST['trainer_social_twitter']) ? esc_url_raw($_POST['trainer_social_twitter']) : '',
            'linkedin'  => isset($_POST['trainer_social_linkedin']) ? esc_url_raw($_POST['trainer_social_linkedin']) : '',
            'instagram' => isset($_POST['trainer_social_instagram']) ? esc_url_raw($_POST['trainer_social_instagram']) : '',
            'youtube'   => isset($_POST['trainer_social_youtube']) ? esc_url_raw($_POST['trainer_social_youtube']) : '',
            'tiktok'    => isset($_POST['trainer_social_tiktok']) ? esc_url_raw($_POST['trainer_social_tiktok']) : '',
            'pinterest' => isset($_POST['trainer_social_pinterest']) ? esc_url_raw($_POST['trainer_social_pinterest']) : '',
            'skype'     => isset($_POST['trainer_social_skype']) ? sanitize_text_field($_POST['trainer_social_skype']) : '',
        );

        update_post_meta($trainer_id, 'gym_builder_trainer_socials', $social_links);

        if (!empty($_POST['trainer_skills']) && is_array($_POST['trainer_skills'])) {
            $skills = array();
            $max_skills = 20;

            foreach (array_slice($_POST['trainer_skills'], 0, $max_skills) as $skill) {
                if (!empty($skill['skill_name'])) {
                    $skills[] = array(
                        'skill_name'  => sanitize_text_field($skill['skill_name']),
                        'skill_value' => isset($skill['skill_value']) ? absint($skill['skill_value']) : 0,
                    );
                }
            }

            if (!empty($skills)) {
                update_post_meta($trainer_id, 'gym_builder_trainer_skill', $skills);
            }
        }

        update_user_meta($user_id, 'gym_builder_trainer_approval_status', false);
        update_post_meta( $trainer_id, 'gym_builder_approval_email_status', 'still_pending' );
        wp_send_json_success(
            __('Registration successful! Your account is pending approval. You will receive an email once approved.', 'gym-builder')
        );
    }

    public function handle_member_login() {
        if ( is_user_logged_in() ) {
            if ( current_user_can( 'gym_builder_trainer_dashboard' ) ) {
                wp_send_json_error( __( 'You are already logged in as a trainer.', 'gym-builder' ) );
            } elseif ( current_user_can( 'gym_builder_student_dashboard' ) ) {
                wp_send_json_error( __( 'You are already logged in as a student.', 'gym-builder' ) );
            } else {
                wp_send_json_error( __( 'You are already logged in but you are not a trainer or a student.', 'gym-builder' ) );
            }
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gym_builder_nonce')) {
            wp_send_json_error('Security check failed');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = 'gym_login_limit_' . md5($ip);
        $attempts = (int) get_transient($transient_key);
        $max_attempts = 5;

        if ($attempts >= $max_attempts) {
            wp_send_json_error(__('Too many login attempts. Please try again after 15 minutes.', 'gym-builder'));
        }

        $email = sanitize_email($_POST['trainer_email']);
        $password    = $_POST['trainer_password'] ?? $_POST['password'] ?? '';
        $user_type = isset( $_POST['user_type'] ) ? sanitize_text_field( $_POST['user_type'] ) : 'trainer';

        if (empty($email) || empty($password)) {
            wp_send_json_error(__('Email/Username and Password are required.', 'gym-builder'));
        }


        $user_obj = get_user_by('email', $email);
        $user_login = $user_obj ? $user_obj->user_login : '';


        $user = wp_authenticate($user_login, $password);

        if (is_wp_error($user)) {
            $attempts = $attempts + 1;
            set_transient($transient_key, $attempts, 15 * MINUTE_IN_SECONDS);
            $remaining = $max_attempts - $attempts;

            if ($remaining > 0) {
                /* translators: %d: number of remaining login attempts */
                wp_send_json_error(sprintf(__('Invalid username or password. You have %d attempt(s) remaining.', 'gym-builder'), $remaining));
            } else {
                wp_send_json_error(__('Too many login attempts. Please try again after 15 minutes.', 'gym-builder'));
            }
        }


        if ( $user_type === 'trainer' ) {

            if ( ! in_array( 'gym_builder_trainer', (array) $user->roles, true ) && ! user_can( $user, 'gym_builder_trainer_dashboard' ) ) {
                wp_send_json_error( __( 'This account is not registered as a trainer. Please select "Student" login.', 'gym-builder' ) );
            }

        } elseif ( $user_type === 'student' ) {
            if ( ! in_array( 'gym_builder_student', (array) $user->roles, true ) && ! user_can( $user, 'gym_builder_student_dashboard' ) ) {
                wp_send_json_error( __( 'This account is not registered as a student. Please select "Trainer" login.', 'gym-builder' ) );
            }

        } else {
            wp_send_json_error( __( 'Invalid login type.', 'gym-builder' ) );
        }
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        wp_send_json_success(array(
            'redirect_url' => GymTrainerHelpers::get_member_dashboard_url(),
            'user_type' => $user_type,
        ));
    }
}