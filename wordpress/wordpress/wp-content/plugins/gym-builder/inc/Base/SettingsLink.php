<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
use \GymBuilder\Inc\Base\BaseController;
class SettingsLink extends BaseController {

	public function register(){
		add_filter("plugin_action_links_".$this->plugin,array($this,'admin_settings_link'));
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
	}
	public function admin_settings_link($links){
		$links[]='<a target="_blank" href="'.esc_url('https://www.youtube.com/playlist?list=PLhe8H70KgCii7HCo7ZdLVYUniF_18gGni').'">'.__('Documentation','gym-builder').'</a>';
		$links[]='<a href="'.esc_url('admin.php?page=gym_builder').'">'.__('Settings','gym-builder').'</a>';
		return $links;
	}
	public function plugin_row_meta( $links, $file ) {

		if ( $this->plugin === $file ) {
			$report_url         = 'https://wpdreamers.com/contact-us';
			$row_meta['issues'] = sprintf(
				'%2$s <a target="_blank" href="%1$s"><span style="color: red">%3$s</span></a>',
				esc_url( $report_url ),
				esc_html__( 'Facing issue?', 'gym-builder' ),
				esc_html__( 'Please open a support ticket.', 'gym-builder' )
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}