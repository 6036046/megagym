<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
trait TemplateTrait{
    static function page_title($echo = true,$page=null) {
        $page_title='';
        if (is_search()) {
            /* translators: %s: search query */
            $page_title = sprintf(__('Search results: &ldquo;%s&rdquo;', 'gym-builder'), get_search_query());

            if (get_query_var('paged')) {
                /* translators: %s: page number */
                $page_title .= sprintf(__('&nbsp;&ndash; Page %s', 'gym-builder'), get_query_var('paged'));
            }
        } elseif (is_tax()) {

            $page_title = single_term_title('', false);

        } elseif('class'===$page) {
            $class_page_id = self::get_page_id('classes');
            $page_title = get_the_title($class_page_id);
        }elseif ('trainer'===$page){
            $trainer_page_id = self::get_page_id('trainers');
            $page_title = get_the_title($trainer_page_id);
        } elseif ('trainer_auth'===$page){
            $trainer_auth_page_id = self::get_page_id('trainer_auth');
            $page_title = get_the_title($trainer_auth_page_id);
        } elseif ('trainer_dashboard'===$page){
            $trainer_dashboard_page_id = self::get_page_id('trainer_dashboard');
            $page_title = get_the_title($trainer_dashboard_page_id);
        }

        $page_title = apply_filters('gym_builder_page_title', $page_title);

        if ($echo) {
            echo esc_html($page_title);
        } else {
            return $page_title;
        }
    }
}