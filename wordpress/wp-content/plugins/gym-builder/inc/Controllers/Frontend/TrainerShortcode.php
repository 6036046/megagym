<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Frontend;

use GymBuilder\Inc\Base\BaseController;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use GymBuilder\Inc\Controllers\Pagination;
use GymBuilder\Inc\Controllers\ShortcodeQuery;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;

class TrainerShortcode extends BaseController {

	use SingleTonTrait,Constants;
	private $scId;


	public function init(  ) {
		add_shortcode( 'gbtrainer', [ $this, 'gbtrainer_shortcode_render' ] );
	}

	public function gbtrainer_shortcode_render( $atts ) {
		$scID = isset( $atts['id'] ) ? absint( $atts['id'] ) : null;

		if ( ! $scID && is_null( get_post( $scID ) ) ) {
			return;
		}

		if ( 0 === $scID ) {
			return;
		}

		$this->scId          = $scID;
		$scTrainerSettingsMeta = get_post_meta( $scID );
		$rand                = absint( wp_rand() );
		$layoutID            = 'gbtrainer-shortcode-container-' . $rand;
		$metas = Helper::gbTrainerMetaScBuilder( $scTrainerSettingsMeta );
		$layout = $metas['layout'];
		$html                = null;
		$containerAttr       = null;
		$show_more_btn = $metas['more_btn'];
		$more_btn_text = $metas['more_btn_text'];
		$more_btn_url = $metas['more_btn_url'];
		$containerClass  = 'gbtrainer-shortcode-container gym-builder-grid gym-builder-trainer-items';
		$rowClass  = 'gbtrainer-shortcode-wrapper columns-' .esc_attr($metas['grid_columns']);
		$html = '<div class="' . esc_attr( $containerClass." ".esc_attr($metas['layout']) ) . '" id="' . esc_attr( $layoutID ) . '" ' . $containerAttr . '>';

		$query = ( new ShortcodeQuery() )->buildArgs( $scID, $metas, self::$trainer_post_type,self::$trainer_taxonomy )->get_gb_shortcode_posts();
		$temp           = Helper::wp_set_temp_query( $query );
		if ($query->have_posts()){
			$html .= '<div class="'.esc_attr($rowClass).'">';
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();

			while ( $query->have_posts() ) {
				$query->the_post();
				$metas['trainer_id'] = get_the_ID();
				$html .= Functions::render('shortcode/trainer/layouts/'. $layout,$metas,true);
			}
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();
			$html .= '</div>';

			if( 'on' === $show_more_btn){
				$html .= Pagination::get_more_btn_html($more_btn_url,$more_btn_text);
			}

		}else{
			$html .= '<p>' . esc_html__( 'No posts found.', 'gym-builder' ) . '</p>';
		}
		Helper::wp_reset_temp_query( $temp );
		$html .='</div>';
		return $html;
		


	}
}