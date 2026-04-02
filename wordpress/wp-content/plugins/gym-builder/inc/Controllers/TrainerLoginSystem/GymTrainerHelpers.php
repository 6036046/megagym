<?php
namespace GymBuilder\Inc\Controllers\TrainerLoginSystem;
use GymBuilder\Inc\Controllers\Helpers\Functions;

class GymTrainerHelpers {

    public function generate_unique_username($email, $name) {
        $email_parts = explode('@', $email);
        $base_username = sanitize_user($email_parts[0]);

        if (empty($base_username) || strlen($base_username) < 3) {
            $base_username = sanitize_user(strtolower(str_replace(' ', '_', $name)));
        }

        $site_name = get_bloginfo('name');
        $username = $base_username . '_' .  sanitize_title($site_name);

        $original_username = $username;
        $counter = 1;

        while (username_exists($username)) {
            $username = $original_username . '_' . $counter;
            $counter++;

            if ($counter > 100) {
                $username = $base_username . '_' . wp_generate_password(4, false);
                break;
            }
        }

        return $username;
    }
    public static function get_member_dashboard_url() {

        $dashboard_page_id =  Functions::get_page_id( 'member_dashboard' );
        if ($dashboard_page_id && get_permalink($dashboard_page_id)) {
            return get_permalink($dashboard_page_id);
        }

        return home_url('/');
    }
    public static function get_member_login_page_url() {

        $login_page_id =  Functions::get_page_id( 'member_auth' );
        if ($login_page_id && get_permalink($login_page_id)) {
            return get_permalink($login_page_id);
        }

        return home_url('/');
    }
    public static function redirect_to_respective_dashboard() {
        if ( ! is_user_logged_in() ) {
            return;
        }
        if ( current_user_can( 'gym_builder_trainer_dashboard' ) ) {
            wp_safe_redirect( self::get_member_dashboard_url() );
            exit();
        }

        if ( current_user_can( 'gym_builder_student_dashboard' ) ) {
            wp_safe_redirect( self::get_member_dashboard_url() );
            exit();
        }

    }
    /**
     * Get user ID associated with trainer post
     *
     * @param int $trainer_id Trainer post ID
     * @return int|false User ID or false
     */
    public static function get_trainer_user_id( $trainer_id ) {
        $users = get_users(array(
            'meta_key'   => 'gym_builder_trainer_user_id',
            'meta_value' => $trainer_id,
            'number'     => 1,
            'fields'     => 'ID',
        ));

        return !empty($users) ? absint($users[0]) : false;
    }
}