<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
use GymBuilder\Inc\Controllers\Helpers\Functions;

class AddConfig{
    public function __construct()
    {
        add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
    }
    static function get_custom_page_list() {
		$pages = array(
			'classes'     => array(
				'title'   => esc_html__( 'Classes', 'gym-builder' ),
				'content' => ''
			),
            'trainers'     => array(
				'title'   => esc_html__( 'Trainers', 'gym-builder' ),
				'content' => ''
			),
		);

		return apply_filters( 'gym_builder_custom_pages_list', $pages );
	}
    public static function member_auth_page_list(){
        return [
            'member_auth'     => [
                'title'   => esc_html__( 'Member Login', 'gym-builder' ),
                'content' => ''
            ],
            'member_dashboard' =>[
                'title' => esc_html__('Member Dashboard','gym-builder'),
                'content' => ''
            ]
        ];
    }
    public function add_display_post_states( $post_states, $post ) {
        $page_settings = array_merge(
            Functions::get_page_ids(),
            Functions::get_member_auth_page_ids()
        );
        $pList         = array_merge(
            self::get_custom_page_list(),
            self::member_auth_page_list()
        );

        foreach ( $page_settings as $type => $id ) {
            if ( $post->ID == $id ) {
                $post_states[] = $pList[ $type ]['title'] . " " . esc_html__( "Page", "gym-builder" );
            }
        }

        return $post_states;
    }

}