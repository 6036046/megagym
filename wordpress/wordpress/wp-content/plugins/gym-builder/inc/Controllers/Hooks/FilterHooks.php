<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Hooks;

class FilterHooks {
	public static function init() {
		add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'custom_post_tags' ], 10, 2 );
	}

	public static function custom_post_tags( $tags, $context ) {

		$tags['input']  = [
			'type'        => true,
			'class'       => true,
			'name'        => true,
			'step'        => true,
			'min'         => true,
			'title'       => true,
			'size'        => true,
			'pattern'     => true,
			'inputmode'   => true,
			'checked'     => true,
			'value'       => true,
			'id'          => true,
			'placeholder' => true,
		];
		$tags['select'] = array(
			'name'     => true,
			'label'    => true,
			'class'    => true,
			'id'       => true,
			'multiple' => true,
			'desc'     => true,
			'type'     => true,
			'default'  => true,
		);
		$tags['option'] = [
			'value'    => true,
			'selected' => true,
		];

		return $tags;
	}
}