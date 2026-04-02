<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Frontend;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Query;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

class MembershipPackageShortcode {
	use Constants;
	use SingleTonTrait;

	public function init() {
		add_shortcode( 'membership_package_shortcode', [ $this, 'membership_package_shortcode_render' ] );
	}

	public function membership_package_shortcode_render( $atts, $content = '' ) {
		$atts = shortcode_atts(
			[
				'limit'             => - 1,
				'hide_package_type' => false,
				'include_package'   => null,
				'order_by'          => 'menu_order',
				'order'             => 'ASC',
			],
			$atts
		);

		if ( $atts['include_package'] ) {
			$include_package = explode( ",", $atts['include_package'] );
		}
		$html          = null;
		$count         = 0;
		$package_types = Helper::get_membership_package_types_array(true);
		$hide_package_type = $atts['hide_package_type'] == true ? 'hide-package-type':'show-package-type';
		$package_query = ( new Query() )->set_query(
			self::$membership_package_post_type,
			'',
			[
				'limit'   => $atts['limit'],
				'post_in' => $include_package ?? [],
				'order_by' => $atts['order_by'],
				'order'    => $atts['order']
			]
		)->get_posts_query();
		$temp          = Helper::wp_set_temp_query( $package_query );
		if ( $package_query->have_posts() ) {
			$html .= '<div class="gb-membership-package-wrapper gb-pricing-plan '.esc_attr($hide_package_type).'">';
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();
			if ( $package_types && ! $atts['hide_package_type'] ) {
				$html .= apply_filters( 'shortcode_package_type_html_start', '<ul class="package-type">' );
				foreach ( $package_types as $key => $value ) {
					$count                   = $count + 1;
					$type_args['type_name']  = $key;
					$type_args['type_value'] = $value;
					$type_args['type_count'] = $count;
					$html                    .= Functions::render( "shortcode/membership-package/pricing-types", $type_args, true );
				}
				$html .= apply_filters( 'shortcode_package_type_html_end', '</ul>' );
			}
			$html .= '<div class="package-content-wrapper">';
			while ( $package_query->have_posts() ) {
				$package_query->the_post();
				$id                    = get_the_ID();
//				$args['package_price'] = get_post_meta( $id, 'gym_builder_package_price', true );
				$args['package_price'] = Functions::get_price_with_label($id);
				$args['package_duration'] = get_post_meta( $id, 'gym_builder_package_price_duration', true );
				$args['button_text']   = get_post_meta( $id, 'gym_builder_package_button_text', true );
				$args['button_url']    = get_post_meta( $id, 'gym_builder_package_button_url', true );
				$args['package_features']    = get_post_meta( $id, 'gym_builder_package_features', true );
				$html                  .= Functions::render( "shortcode/membership-package/pricing-plan", $args, true );
			}
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$html = '<p>' . esc_html__( 'No posts found.', 'gym-builder' ) . '</p>';
		}
		Helper::wp_reset_temp_query( $temp );

		return $html;

	}
}