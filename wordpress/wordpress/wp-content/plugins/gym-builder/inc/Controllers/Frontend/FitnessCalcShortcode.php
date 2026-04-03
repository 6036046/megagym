<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Frontend;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

class FitnessCalcShortcode {

	use SingleTonTrait, Constants;

	private $scId;

	public function init() {
		add_shortcode( 'gbfitness_calculator', [ $this, 'fitness_calc_shortcode_render' ] );
	}

	public function fitness_calc_shortcode_render( $atts, $content = '' ) {
		$scID = isset( $atts['id'] ) ? absint( $atts['id'] ) : null;
		if ( ! $scID && is_null( get_post( $scID ) ) ) {
			return;
		}
		if ( 0 === $scID ) {
			return;
		}
		$this->scId                = $scID;
		$scFitnessCalcSettingsMeta = get_post_meta( $scID );
		$rand                      = absint( wp_rand() );
		$layoutID                  = 'gbfitness-calc-shortcode-container-' . $rand;
		$metas                     = Helper::gbFitnessCalcMetaBuilder( $scFitnessCalcSettingsMeta );
		$calc_types                = $metas['calculator_shortcode_types'];
		$calc_types_layout         = Helper::fitness_calc_type_layout( $metas );
		$html                      = null;
		$containerClass            = 'gbfitness-calc-shortcode-container';
		$rowClass                  = 'gbfitness-shortcode-wrapper gym-builder-grid';
		$html                      = '<div class="' . esc_attr( $containerClass . " " . esc_attr( $calc_types ) ) . '" id="' . esc_attr( $layoutID ) . '">';
		$html                      .= '<div class="' . esc_attr( $rowClass ) . '">';
		ob_start();
		$html .= ob_get_contents();
		ob_end_clean();
		$html .= Functions::render( 'shortcode/fitness-calc/' . $calc_types_layout, $metas, true );
		ob_start();
		$html .= ob_get_contents();
		ob_end_clean();
		$html .= '</div>';
		$html .= '</div>';

		return $html;

	}
}