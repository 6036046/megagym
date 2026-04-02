<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Helpers;

use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Models\ThumbnailSizeGenerator;

class Helper {
	use Constants;

	public static function wp_set_temp_query( $query ) {
		global $wp_query;
		global $post;
		$temp     = $wp_query;
		$wp_query = $query;

		return $temp;
	}

	public static function wp_reset_temp_query( $temp ) {
		global $wp_query;
		$wp_query = $temp;
		wp_reset_postdata();
	}

	public static function get_the_terms( $post_id, $taxonomy ) {
		return get_the_terms( $post_id, $taxonomy );
	}

	public static function orderbyQueryOptions() {
		$options = [
			'none'       => 'No order',
			'title'      => 'Order by title',
			'ID'         => 'Order by post id',
			'name'       => 'Order by post name ',
			'date'       => 'Order by date',
			'rand'       => 'Random order',
			'menu_order' => 'Order by Page Order',
		];

		return apply_filters( 'gym_builder_orderby_query_options', $options );
	}

	public static function day_name_map() {
		$days = [
			__( 'Monday', 'gym-builder' )    => 'mon',
			__( 'Tuesday', 'gym-builder' )   => 'tue',
			__( 'Wednesday', 'gym-builder' ) => 'wed',
			__( 'Thursday', 'gym-builder' )  => 'thu',
			__( 'Friday', 'gym-builder' )    => 'fri',
			__( 'Saturday', 'gym-builder' )  => 'sat',
			__( 'Sunday', 'gym-builder' )    => 'sun',
		];

		return apply_filters( 'gym_builder_day_name_map', $days );
	}

	public static function reverse_day_name_map() {
		$days = [
			'mon' => __( 'Monday', 'gym-builder' ),
			'tue' => __( 'Tuesday', 'gym-builder' ),
			'wed' => __( 'Wednesday', 'gym-builder' ),
			'thu' => __( 'Thursday', 'gym-builder' ),
			'fri' => __( 'Friday', 'gym-builder' ),
			'sat' => __( 'Saturday', 'gym-builder' ),
			'sun' => __( 'Sunday', 'gym-builder' )
		];

		return apply_filters( 'gym_builder_reverse_day_name_map', $days );
	}

	public static function get_primary_color() {
		return apply_filters( 'gym_builder_primary_color', SettingsApi::get_option( 'gym_builder_primary_color', 'gym_builder_style_settings' ) ?: '#005dd0' );
	}

	public static function get_secondary_color() {
		return apply_filters( 'gym_builder_secondary_color', SettingsApi::get_option( 'gym_builder_secondary_color', 'gym_builder_style_settings' ) ?: '#0a4b78' );
	}

	public static function slider_layouts() {
		return [
			'class_archive_style'   => [ 'layout-2' ],
			'trainer_archive_style' => [ 'layout-3' ]
		];
	}

	public static function slider_layout_search( $needleKey, $needleValue, $haystack ) {
		foreach ( $haystack as $key => $value ) {
			if ( $key == $needleKey ) {
				$result = in_array( $needleValue, $value );
				if ( $result !== false ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function is_slider_layout( $settings, $layout ) {
		$layouts = self::slider_layouts();

		return self::slider_layout_search( $settings, $layout, $layouts );
	}

	public static function gbClassMetaScBuilder( $meta ) {
		$custom_thumb_size = [
			'width'  => ! empty( $meta['gb_class_shortcode_thumb_width'][0] ) ? absint( $meta['gb_class_shortcode_thumb_width'][0] ) : '570',
			'height' => ! empty( $meta['gb_class_shortcode_thumb_height'][0] ) ? absint( $meta['gb_class_shortcode_thumb_height'][0] ) : '400',
			'crop'   => ! empty( $meta['gb_class_shortcode_thumb_crop'][0] ) ? esc_attr( $meta['gb_class_shortcode_thumb_crop'][0] ) : 'hard',
		];
		$metas             = [
			'time_format'       => ! empty( $meta['gb_class_shortcode_time_format'][0] ) ? absint( $meta['gb_class_shortcode_time_format'][0] ) : '12',
			'layout'            => ! empty( $meta['gb_class_shortcode_layout'][0] ) ? esc_attr( $meta['gb_class_shortcode_layout'][0] ) : 'layout-1',
			'posts_per_page'    => ! empty( $meta['gb_class_shortcode_posts_per_page'][0] ) ? absint( $meta['gb_class_shortcode_posts_per_page'][0] ) : '-1',
			'grid_columns'      => ! empty( $meta['gb_class_shortcode_grid_columns'][0] ) ? esc_attr( $meta['gb_class_shortcode_grid_columns'][0] ) : '3',
			'custom_image_size' => $custom_thumb_size,
			'post_in'           => ! empty( $meta['gb_class_include_shortcode'][0] ) ? $meta['gb_class_include_shortcode'][0] : [],
			'post_not_in'       => ! empty( $meta['gb_class_exclude_shortcode'][0] ) ? $meta['gb_class_exclude_shortcode'][0] : [],
			'categories'        => ! empty( $meta['gb_class_categories_shortcode'][0] ) ? $meta['gb_class_categories_shortcode'][0] : [],
			'order_by'          => ! empty( $meta['gb_class_order_by_shortcode'][0] ) ? $meta['gb_class_order_by_shortcode'][0] : null,
			'order'             => ! empty( $meta['gb_class_order_shortcode'][0] ) ? $meta['gb_class_order_shortcode'][0] : null,
			'more_btn'          => ! empty( $meta['gb_class_shortcode_more_btn'][0] ) ? esc_attr( $meta['gb_class_shortcode_more_btn'][0] ) : '',
			'routine_nav'       => ! empty( $meta['gb_class_routine_nav'][0] ) ? esc_attr( $meta['gb_class_routine_nav'][0] ) : '',
			'more_btn_text'     => ! empty( $meta['gb_class_shortcode_more_btn_text'][0] ) ? esc_attr( $meta['gb_class_shortcode_more_btn_text'][0] ) : __( 'More Classes', 'gym-builder' ),
			'more_btn_url'      => ! empty( $meta['gb_class_shortcode_more_btn_url'][0] ) ? esc_url( $meta['gb_class_shortcode_more_btn_url'][0] ) : '#'

		];

		return apply_filters( 'gb_class_meta_sc_builder', $metas, $meta );
	}

	public static function gbFitnessCalcMetaBuilder( $meta ) {

		$metas = [
			'calculator_shortcode_types' => ! empty( $meta['gb_fitness_calculator_shortcode_types'][0] ) ? esc_attr( $meta['gb_fitness_calculator_shortcode_types'][0] ) : 'bmi',
			'calculator_heading_text'    => ! empty( $meta['gb_fitness_calc_heading'][0] ) ? esc_html( $meta['gb_fitness_calc_heading'][0] ) : __( 'Fitness Calculators', 'gym-builder' ),
			'calculator_des'             => ! empty( $meta['gb_fitness_calc_des'][0] ) ? esc_html( $meta['gb_fitness_calc_des'][0] ) : '',
			'calculator_unit'            => ! empty( $meta['gb_fintess_calc_unit'][0] ) ? esc_attr( $meta['gb_fintess_calc_unit'][0] ) : 'metric',
			'calculator_btn_text'        => ! empty( $meta['gb_fitness_calc_btn_text'][0] ) ? esc_html( $meta['gb_fitness_calc_btn_text'][0] ) : __( 'Calculator', 'gym-builder' ),
			'bmi_layout'                 => ! empty( $meta['gb_bmi_calc_layout'][0] ) ? esc_html( $meta['gb_bmi_calc_layout'][0] ) : 'layout-1',
			'body_fat_layout'            => ! empty( $meta['gb_body_fat_layout'][0] ) ? esc_html( $meta['gb_body_fat_layout'][0] ) : 'layout-1',
			'protien_intake_layout'      => ! empty( $meta['protien_intake_layout'][0] ) ? esc_html( $meta['protien_intake_layout'][0] ) : 'layout-1',
			'water_intake_layout'        => ! empty( $meta['water_intake_layout'][0] ) ? esc_html( $meta['water_intake_layout'][0] ) : 'layout-1',
		];

		return apply_filters( 'gb_fitness_calc_meta_sc_builder', $metas, $meta );
	}

	public static function fitness_calc_type_layout( $metas ) {
		$calc_types = $metas['calculator_shortcode_types'];
		if ( 'bmi' === $calc_types ) {
			$calc_types_layout = 'bmi/' . $metas['bmi_layout'];
		} elseif ( 'body_fat' === $calc_types ) {
			$calc_types_layout = 'body-fat/' . $metas['body_fat_layout'];
		} elseif ( 'protien_intake' === $calc_types ) {
			$calc_types_layout = 'protien-intake/' . $metas['protien_intake_layout'];
		} elseif ( 'water_intake' === $calc_types ) {
			$calc_types_layout = 'water-intake/' . $metas['water_intake_layout'];
		}

		return $calc_types_layout;
	}

	public static function gbTrainerMetaScBuilder( $meta ) {
		$custom_thumb_size = [
			'width'  => ! empty( $meta['gb_trainer_shortcode_thumb_width'][0] ) ? absint( $meta['gb_trainer_shortcode_thumb_width'][0] ) : '570',
			'height' => ! empty( $meta['gb_trainer_shortcode_thumb_height'][0] ) ? absint( $meta['gb_trainer_shortcode_thumb_height'][0] ) : '400',
			'crop'   => ! empty( $meta['gb_trainer_shortcode_thumb_crop'][0] ) ? esc_attr( $meta['gb_trainer_shortcode_thumb_crop'][0] ) : 'hard',
		];
		$metas             = [
			'layout'            => ! empty( $meta['gb_trainer_shortcode_layout'][0] ) ? esc_attr( $meta['gb_trainer_shortcode_layout'][0] ) : 'layout-1',
			'posts_per_page'    => ! empty( $meta['gb_trainer_shortcode_posts_per_page'][0] ) ? absint( $meta['gb_trainer_shortcode_posts_per_page'][0] ) : '',
			'grid_columns'      => ! empty( $meta['gb_trainer_shortcode_grid_columns'][0] ) ? esc_attr( $meta['gb_trainer_shortcode_grid_columns'][0] ) : '3',
			'custom_image_size' => $custom_thumb_size,
			'post_in'           => ! empty( $meta['gb_trainer_include_shortcode'][0] ) ? $meta['gb_trainer_include_shortcode'][0] : [],
			'post_not_in'       => ! empty( $meta['gb_trainer_exclude_shortcode'][0] ) ? $meta['gb_trainer_exclude_shortcode'][0] : [],
			'categories'        => ! empty( $meta['gb_trainer_categories_shortcode'][0] ) ? $meta['gb_trainer_categories_shortcode'][0] : [],
			'order_by'          => ! empty( $meta['gb_trainer_order_by_shortcode'][0] ) ? $meta['gb_trainer_order_by_shortcode'][0] : null,
			'order'             => ! empty( $meta['gb_trainer_order_shortcode'][0] ) ? $meta['gb_trainer_order_shortcode'][0] : null,
			'more_btn'          => ! empty( $meta['gb_trainer_shortcode_more_btn'][0] ) ? esc_attr( $meta['gb_trainer_shortcode_more_btn'][0] ) : '',
			'more_btn_text'     => ! empty( $meta['gb_trainer_shortcode_more_btn_text'][0] ) ? esc_attr( $meta['gb_trainer_shortcode_more_btn_text'][0] ) : __( 'More Trainer', 'gym-builder' ),
			'more_btn_url'      => ! empty( $meta['gb_trainer_shortcode_more_btn_url'][0] ) ? esc_url( $meta['gb_trainer_shortcode_more_btn_url'][0] ) : '#'

		];

		return apply_filters( 'gb_trainer_meta_sc_builder', $metas, $meta );
	}

	public static function CustomImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		$thumbResize = new ThumbnailSizeGenerator();

		return $thumbResize->process( $url, $width, $height, $crop, $single, $upscale );
	}

	public static function getFeatureImage( $post_id = null, $gbImgSize = 'medium', $customImgSize = [] ) {
		$imgHtml = $imgSrc = $attachment_id = null;
		$cSize   = false;

		if ( $gbImgSize == 'gym_builder_custom' ) {
			$gbImgSize = 'full';
			$cSize     = true;
		}

		$aID        = get_post_thumbnail_id( $post_id );
		$post_title = get_the_title( $post_id );
		$img_alt    = trim( wp_strip_all_tags( get_post_meta( $aID, '_wp_attachment_image_alt', true ) ) );
		$alt_tag    = ! empty( $img_alt ) ? $img_alt : trim( wp_strip_all_tags( $post_title ) );

		$attr = [
			'class' => 'gym-builder-feature-img ',
			'alt'   => $alt_tag,
		];

		$actual_dimension = wp_get_attachment_metadata( $aID, true );


		$actual_w = ! empty( $actual_dimension['width'] ) ? $actual_dimension['width'] : '';
		$actual_h = ! empty( $actual_dimension['height'] ) ? $actual_dimension['height'] : '';

		if ( $aID ) {
			$imgHtml       = wp_get_attachment_image( $aID, $gbImgSize, false, $attr );
			$attachment_id = $aID;
		}


		if ( $imgHtml && $cSize ) {
			preg_match( '@src="([^"]+)"@', $imgHtml, $match );

			$imgSrc = array_pop( $match );
			$w      = ! empty( $customImgSize['width'] ) ? absint( $customImgSize['width'] ) : null;
			$h      = ! empty( $customImgSize['height'] ) ? absint( $customImgSize['height'] ) : null;
			$c      = ! empty( $customImgSize['crop'] ) && $customImgSize['crop'] == 'soft' ? false : true;

			if ( $w && $h ) {
				if ( $w >= $actual_w || $h >= $actual_h ) {
					$w = 150;
					$h = 150;
					$c = true;
				}

				$image = self::CustomImageReSize( $imgSrc, $w, $h, $c, false );

				if ( ! empty( $image ) ) {

					list( $src, $width, $height ) = $image;

					$hwstring    = image_hwstring( $width, $height );
					$attachment  = get_post( $attachment_id );
					$attr        = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $gbImgSize );
					$attr['src'] = $src;
					$attr        = array_map( 'esc_attr', $attr );
					$imgHtml     = rtrim( "<img $hwstring" );

					foreach ( $attr as $name => $value ) {
						$imgHtml .= " $name=" . '"' . $value . '"';
					}

					$imgHtml .= ' />';

				}
			}
		}

		return $imgHtml;
	}

	public static function get_membership_package_types_array( $slug = false ) {
		$type_list = [];
		$terms     = get_terms( [
			'taxonomy'   => self::$membership_package_taxonomy,
			'hide_empty' => false
		] );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( $slug ) {
					$type_list[ $term->slug ] = $term->name;
				} else {
					$type_list[ $term->term_id ] = $term->name;
				}
			}
		}

		return apply_filters( "gym_builder_array_membership_packages_type_list", $type_list );
	}

	public static function get_membership_package_type_slug() {
		$cat_slug = '';
		$terms    = get_the_terms( get_the_ID(), self::$membership_package_taxonomy );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$slug_list = array();
			foreach ( $terms as $term ) {
				$slug_list[] = $term->slug;
			}
			$cat_slug = join( " ", $slug_list );
		}

		return $cat_slug;
	}

	public static function get_membership_package_type_html_format( $package_id, $linkable = true ) {
		$term_lists = get_the_terms( $package_id, self::$membership_package_taxonomy );
		$i          = 1;
		if ( $term_lists ) {
			?>
            <span class="inner-package-type">
				<?php
				foreach ( $term_lists as $term_list ) {
					$link = get_term_link( $term_list->term_id, self::$membership_package_taxonomy ); ?>
					<?php if ( $i > 1 ) {
						echo esc_html( ', ' );
					}
					if ( $linkable ) {
						?>
                        <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term_list->name ); ?></a>
					<?php } else {
						?>
                        <span class="seperator"> <?php echo esc_html( "/" ); ?></span>
                        <span><?php echo esc_html( $term_list->name ); ?></span>
					<?php }
					$i ++;
				} ?>
			</span>
		<?php }
	}

	public static function fitness_calculator_translatable_text() {

		$text = [
			'heightCentimeter'       => __( 'Centimeter', 'gym-builder' ),
			'weightKilogram'         => __( 'Kilogram', 'gym-builder' ),
			'heightFeet'             => __( 'Feet', 'gym-builder' ),
			'weightPound'            => __( 'Pound', 'gym-builder' ),
			'unitLTR'                => __( 'Ltr', 'gym-builder' ),
			'unitOz'                 => __( 'Oz', 'gym-builder' ),
			'unitLBS'                => __( 'lbs', 'gym-builder' ),
			'unitGram'               => __( 'gram', 'gym-builder' ),
			'bmiUnderweight'         => __( 'Underweight', 'gym-builder' ),
			'bmiNormalweight'        => __( 'Normal Weight', 'gym-builder' ),
			'bmiOverweight'          => __( 'Overweight', 'gym-builder' ),
			'bmiClass1'              => __( '(Class I Obese)', 'gym-builder' ),
			'bmiClass2'              => __( '(Class II Obese)', 'gym-builder' ),
			'bmiClass3'              => __( '(Class III Obese)', 'gym-builder' ),
			'requireField'           => __( 'Required Fields', 'gym-builder' ),
			'numberOnly'             => __( 'Numbers Only', 'gym-builder' ),
			'positiveNumberOnly'     => __( 'Positive Numbers Only', 'gym-builder' ),
			'nonNegativeNumberOnly'  => __( 'Non Negative Numbers Only', 'gym-builder' ),
			'integerOnly'            => __( 'Integers Only', 'gym-builder' ),
			'positiveIntegerOnly'    => __( 'Positive Integers Only', 'gym-builder' ),
			'nonNegativeIntegerOnly' => __( 'Non Negative Integres Only', 'gym-builder' ),
		];

		return apply_filters( "fitness_calculator_translatable_text", $text );

	}

	public static function gym_builder_get_options() {
		$options_keys = [
			'gym_builder_page_settings',
			'gym_builder_permalinks_settings',
			'gym_builder_class_settings',
			'gym_builder_trainer_settings',
			'gym_builder_style_settings',
			'gym_builder_global_settings'
		];

		if ( Helper::has_zoom_integration_addon() ) {
			$options_keys[] = 'gym_builder_zoom_integration_settings';
		}

		$settings = [ 'options' => [] ];

		foreach ( $options_keys as $key ) {
			$settings['options'][ $key ] = get_option( $key ) ?? [];
		}
		return $settings;

	}

	public static function class_page_layout() {
		$layout = [
			'layout-1' => [
				'title'      => 'Layout 1',
				'img_source' => 'layout-1'
			],
			'layout-2' => [
				'title'      => 'Slider Layout',
				'img_source' => 'layout-2'
			],
			'layout-3' => [
				'title'      => 'Layout 2',
				'img_source' => 'layout-3'
			],
		];

		return apply_filters( 'gym_builder_class_page_layout', $layout );
	}

	public static function trainer_page_layout() {
		$layout = [
			'layout-1' => [
				'title'      => 'Layout 1',
				'img_source' => 'layout-1'
			],
			'layout-2' => [
				'title'      => 'Layout 2',
				'img_source' => 'layout-2'
			],
		];

		return apply_filters( 'gym_builder_trainer_page_layout', $layout );
	}

	public static function currency_position_list() {
		return [
			'left'        => esc_html__( 'Left ($99.99)', 'gym-builder' ),
			'right'       => esc_html__( 'Right (99.99$)', 'gym-builder' ),
			'left_space'  => esc_html__( 'Left with space ($ 99.99)', 'gym-builder' ),
			'right_space' => esc_html__( 'Right with space (99.99 $)', 'gym-builder' ),
		];
	}

	public static function currency_list() {
		return apply_filters(
			'gym_builder_currency_list',
			[
				'AED' => [
					'code'           => 'AED',
					'symbol'         => 'د.إ',
					'name'           => 'United Arab Emirates Dirham',
					'numeric_code'   => '784',
					'code_placement' => 'before',
					'minor_unit'     => 'Fils',
					'major_unit'     => 'Dirham',
				],
				'AFN' => [
					'code'         => 'AFN',
					'symbol'       => 'Af',
					'name'         => 'Afghan Afghani',
					'decimals'     => 0,
					'numeric_code' => '971',
					'minor_unit'   => 'Pul',
					'major_unit'   => 'Afghani',
				],
				'ANG' => [
					'code'         => 'ANG',
					'symbol'       => 'NAf.',
					'name'         => 'Netherlands Antillean Guilder',
					'numeric_code' => '532',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Guilder',
				],
				'AOA' => [
					'code'         => 'AOA',
					'symbol'       => 'Kz',
					'name'         => 'Angolan Kwanza',
					'numeric_code' => '973',
					'minor_unit'   => 'Cêntimo',
					'major_unit'   => 'Kwanza',
				],
				'ARM' => [
					'code'       => 'ARM',
					'symbol'     => 'm$n',
					'name'       => 'Argentine Peso Moneda Nacional',
					'minor_unit' => 'Centavos',
					'major_unit' => 'Peso',
				],
				'ARS' => [
					'code'         => 'ARS',
					'symbol'       => 'AR$',
					'name'         => 'Argentine Peso',
					'numeric_code' => '032',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Peso',
				],
				'AUD' => [
					'code'             => 'AUD',
					'symbol'           => '$',
					'name'             => 'Australian Dollar',
					'numeric_code'     => '036',
					'symbol_placement' => 'before',
					'minor_unit'       => 'Cent',
					'major_unit'       => 'Dollar',
				],
				'AWG' => [
					'code'         => 'AWG',
					'symbol'       => 'Afl.',
					'name'         => 'Aruban Florin',
					'numeric_code' => '533',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Guilder',
				],
				'AZN' => [
					'code'       => 'AZN',
					'symbol'     => 'man.',
					'name'       => 'Azerbaijanian Manat',
					'minor_unit' => 'Qəpik',
					'major_unit' => 'New Manat',
				],
				'BAM' => [
					'code'         => 'BAM',
					'symbol'       => 'KM',
					'name'         => 'Bosnia-Herzegovina Convertible Mark',
					'numeric_code' => '977',
					'minor_unit'   => 'Fening',
					'major_unit'   => 'Convertible Marka',
				],
				'BBD' => [
					'code'         => 'BBD',
					'symbol'       => 'Bds$',
					'name'         => 'Barbadian Dollar',
					'numeric_code' => '052',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'BDT' => [
					'code'         => 'BDT',
					'symbol'       => 'Tk',
					'name'         => 'Bangladeshi Taka',
					'numeric_code' => '050',
					'minor_unit'   => 'Paisa',
					'major_unit'   => 'Taka',
				],
				'BGN' => [
					'code'                => 'BGN',
					'symbol'              => 'лв',
					'name'                => 'Bulgarian lev',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'numeric_code'        => '975',
					'minor_unit'          => 'Stotinka',
					'major_unit'          => 'Lev',
				],
				'BHD' => [
					'code'         => 'BHD',
					'symbol'       => 'BD',
					'name'         => 'Bahraini Dinar',
					'decimals'     => 3,
					'numeric_code' => '048',
					'minor_unit'   => 'Fils',
					'major_unit'   => 'Dinar',
				],
				'BIF' => [
					'code'         => 'BIF',
					'symbol'       => 'FBu',
					'name'         => 'Burundian Franc',
					'decimals'     => 0,
					'numeric_code' => '108',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'BMD' => [
					'code'         => 'BMD',
					'symbol'       => 'BD$',
					'name'         => 'Bermudan Dollar',
					'numeric_code' => '060',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'BND' => [
					'code'         => 'BND',
					'symbol'       => 'BN$',
					'name'         => 'Brunei Dollar',
					'numeric_code' => '096',
					'minor_unit'   => 'Sen',
					'major_unit'   => 'Dollar',
				],
				'BOB' => [
					'code'         => 'BOB',
					'symbol'       => 'Bs',
					'name'         => 'Bolivian Boliviano',
					'numeric_code' => '068',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Bolivianos',
				],
				'BRL' => [
					'code'                => 'BRL',
					'symbol'              => 'R$',
					'name'                => 'Brazilian Real',
					'numeric_code'        => '986',
					'symbol_placement'    => 'before',
					'code_placement'      => 'hidden',
					'thousands_separator' => '.',
					'decimal_separator'   => ',',
					'minor_unit'          => 'Centavo',
					'major_unit'          => 'Reais',
				],
				'BSD' => [
					'code'         => 'BSD',
					'symbol'       => 'BS$',
					'name'         => 'Bahamian Dollar',
					'numeric_code' => '044',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'BTN' => [
					'code'         => 'BTN',
					'symbol'       => 'Nu.',
					'name'         => 'Bhutanese Ngultrum',
					'numeric_code' => '064',
					'minor_unit'   => 'Chetrum',
					'major_unit'   => 'Ngultrum',
				],
				'BWP' => [
					'code'         => 'BWP',
					'symbol'       => 'BWP',
					'name'         => 'Botswanan Pula',
					'numeric_code' => '072',
					'minor_unit'   => 'Thebe',
					'major_unit'   => 'Pulas',
				],
				'BYR' => [
					'code'                => 'BYR',
					'symbol'              => 'руб.',
					'name'                => 'Belarusian ruble',
					'numeric_code'        => '974',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'decimals'            => 0,
					'thousands_separator' => ' ',
					'major_unit'          => 'Ruble',
				],
				'BZD' => [
					'code'         => 'BZD',
					'symbol'       => 'BZ$',
					'name'         => 'Belize Dollar',
					'numeric_code' => '084',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'CAD' => [
					'code'         => 'CAD',
					'symbol'       => 'CA$',
					'name'         => 'Canadian Dollar',
					'numeric_code' => '124',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'CDF' => [
					'code'         => 'CDF',
					'symbol'       => 'CDF',
					'name'         => 'Congolese Franc',
					'numeric_code' => '976',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'CHF' => [
					'code'          => 'CHF',
					'symbol'        => 'Fr.',
					'name'          => 'Swiss Franc',
					'rounding_step' => '0.05',
					'numeric_code'  => '756',
					'minor_unit'    => 'Rappen',
					'major_unit'    => 'Franc',
				],
				'CLP' => [
					'code'         => 'CLP',
					'symbol'       => 'CL$',
					'name'         => 'Chilean Peso',
					'decimals'     => 0,
					'numeric_code' => '152',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Peso',
				],
				'CNY' => [
					'code'                => 'CNY',
					'symbol'              => '¥',
					'name'                => 'Chinese Yuan Renminbi',
					'numeric_code'        => '156',
					'symbol_placement'    => 'before',
					'code_placement'      => 'hidden',
					'thousands_separator' => '',
					'minor_unit'          => 'Fen',
					'major_unit'          => 'Yuan',
				],
				'COP' => [
					'code'                => 'COP',
					'symbol'              => '$',
					'name'                => 'Colombian Peso',
					'decimals'            => 0,
					'numeric_code'        => '170',
					'symbol_placement'    => 'before',
					'code_placement'      => 'hidden',
					'thousands_separator' => '.',
					'decimal_separator'   => ',',
					'minor_unit'          => 'Centavo',
					'major_unit'          => 'Peso',
				],
				'CRC' => [
					'code'         => 'CRC',
					'symbol'       => '¢',
					'name'         => 'Costa Rican Colón',
					'decimals'     => 0,
					'numeric_code' => '188',
					'minor_unit'   => 'Céntimo',
					'major_unit'   => 'Colón',
				],
				'CUC' => [
					'code'       => 'CUC',
					'symbol'     => 'CUC$',
					'name'       => 'Cuban Convertible Peso',
					'minor_unit' => 'Centavo',
					'major_unit' => 'Peso',
				],
				'CUP' => [
					'code'         => 'CUP',
					'symbol'       => 'CU$',
					'name'         => 'Cuban Peso',
					'numeric_code' => '192',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Peso',
				],
				'CVE' => [
					'code'         => 'CVE',
					'symbol'       => 'CV$',
					'name'         => 'Cape Verdean Escudo',
					'numeric_code' => '132',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Escudo',
				],
				'CZK' => [
					'code'                => 'CZK',
					'symbol'              => 'Kč',
					'name'                => 'Czech Republic Koruna',
					'numeric_code'        => '203',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Haléř',
					'major_unit'          => 'Koruna',
				],
				'DJF' => [
					'code'         => 'DJF',
					'symbol'       => 'Fdj',
					'name'         => 'Djiboutian Franc',
					'numeric_code' => '262',
					'decimals'     => 0,
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'DKK' => [
					'code'                => 'DKK',
					'symbol'              => 'kr.',
					'name'                => 'Danish Krone',
					'numeric_code'        => '208',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Øre',
					'major_unit'          => 'Kroner',
				],
				'DOP' => [
					'code'         => 'DOP',
					'symbol'       => 'RD$',
					'name'         => 'Dominican Peso',
					'numeric_code' => '214',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Peso',
				],
				'DZD' => [
					'code'         => 'DZD',
					'symbol'       => 'DA',
					'name'         => 'Algerian Dinar',
					'numeric_code' => '012',
					'minor_unit'   => 'Santeem',
					'major_unit'   => 'Dinar',
				],
				'EEK' => [
					'code'                => 'EEK',
					'symbol'              => 'Ekr',
					'name'                => 'Estonian Kroon',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'numeric_code'        => '233',
					'minor_unit'          => 'Sent',
					'major_unit'          => 'Krooni',
				],
				'EGP' => [
					'code'         => 'EGP',
					'symbol'       => 'EG£',
					'name'         => 'Egyptian Pound',
					'numeric_code' => '818',
					'minor_unit'   => 'Piastr',
					'major_unit'   => 'Pound',
				],
				'ERN' => [
					'code'         => 'ERN',
					'symbol'       => 'Nfk',
					'name'         => 'Eritrean Nakfa',
					'numeric_code' => '232',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Nakfa',
				],
				'ETB' => [
					'code'         => 'ETB',
					'symbol'       => 'Br',
					'name'         => 'Ethiopian Birr',
					'numeric_code' => '230',
					'minor_unit'   => 'Santim',
					'major_unit'   => 'Birr',
				],
				'EUR' => [
					'code'                => 'EUR',
					'symbol'              => '€',
					'name'                => 'Euro',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'numeric_code'        => '978',
					'minor_unit'          => 'Cent',
					'major_unit'          => 'Euro',
				],
				'FJD' => [
					'code'         => 'FJD',
					'symbol'       => 'FJ$',
					'name'         => 'Fijian Dollar',
					'numeric_code' => '242',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'FKP' => [
					'code'         => 'FKP',
					'symbol'       => 'FK£',
					'name'         => 'Falkland Islands Pound',
					'numeric_code' => '238',
					'minor_unit'   => 'Penny',
					'major_unit'   => 'Pound',
				],
				'GBP' => [
					'code'             => 'GBP',
					'symbol'           => '£',
					'name'             => 'British Pound Sterling',
					'numeric_code'     => '826',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Penny',
					'major_unit'       => 'Pound',
				],
				'GHS' => [
					'code'       => 'GHS',
					'symbol'     => 'GH₵',
					'name'       => 'Ghanaian Cedi',
					'minor_unit' => 'Pesewa',
					'major_unit' => 'Cedi',
				],
				'GIP' => [
					'code'         => 'GIP',
					'symbol'       => 'GI£',
					'name'         => 'Gibraltar Pound',
					'numeric_code' => '292',
					'minor_unit'   => 'Penny',
					'major_unit'   => 'Pound',
				],
				'GMD' => [
					'code'         => 'GMD',
					'symbol'       => 'GMD',
					'name'         => 'Gambian Dalasi',
					'numeric_code' => '270',
					'minor_unit'   => 'Butut',
					'major_unit'   => 'Dalasis',
				],
				'GNF' => [
					'code'         => 'GNF',
					'symbol'       => 'FG',
					'name'         => 'Guinean Franc',
					'decimals'     => 0,
					'numeric_code' => '324',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'GTQ' => [
					'code'         => 'GTQ',
					'symbol'       => 'GTQ',
					'name'         => 'Guatemalan Quetzal',
					'numeric_code' => '320',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Quetzales',
				],
				'GYD' => [
					'code'         => 'GYD',
					'symbol'       => 'GY$',
					'name'         => 'Guyanaese Dollar',
					'decimals'     => 0,
					'numeric_code' => '328',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'HKD' => [
					'code'             => 'HKD',
					'symbol'           => 'HK$',
					'name'             => 'Hong Kong Dollar',
					'numeric_code'     => '344',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Cent',
					'major_unit'       => 'Dollar',
				],
				'HNL' => [
					'code'         => 'HNL',
					'symbol'       => 'HNL',
					'name'         => 'Honduran Lempira',
					'numeric_code' => '340',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Lempiras',
				],
				'HRK' => [
					'code'         => 'HRK',
					'symbol'       => 'kn',
					'name'         => 'Croatian Kuna',
					'numeric_code' => '191',
					'minor_unit'   => 'Lipa',
					'major_unit'   => 'Kuna',
				],
				'HTG' => [
					'code'         => 'HTG',
					'symbol'       => 'HTG',
					'name'         => 'Haitian Gourde',
					'numeric_code' => '332',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Gourde',
				],
				'HUF' => [
					'code'                => 'HUF',
					'symbol'              => 'Ft',
					'name'                => 'Hungarian Forint',
					'numeric_code'        => '348',
					'decimal_separator'   => ',',
					'thousands_separator' => ' ',
					'decimals'            => 0,
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'major_unit'          => 'Forint',
				],
				'IDR' => [
					'code'         => 'IDR',
					'symbol'       => 'Rp',
					'name'         => 'Indonesian Rupiah',
					'decimals'     => 0,
					'numeric_code' => '360',
					'minor_unit'   => 'Sen',
					'major_unit'   => 'Rupiahs',
				],
				'ILS' => [
					'code'             => 'ILS',
					'symbol'           => '₪',
					'name'             => 'Israeli New Shekel',
					'numeric_code'     => '376',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Agora',
					'major_unit'       => 'New Shekels',
				],
				'INR' => [
					'code'         => 'INR',
					'symbol'       => 'Rs',
					'name'         => 'Indian Rupee',
					'numeric_code' => '356',
					'minor_unit'   => 'Paisa',
					'major_unit'   => 'Rupee',
				],
				'IRR' => [
					'code'             => 'IRR',
					'symbol'           => '﷼',
					'name'             => 'Iranian Rial',
					'numeric_code'     => '364',
					'symbol_placement' => 'after',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Rial',
					'major_unit'       => 'Toman',
				],
				'ISK' => [
					'code'                => 'ISK',
					'symbol'              => 'Ikr',
					'name'                => 'Icelandic Króna',
					'decimals'            => 0,
					'thousands_separator' => ' ',
					'numeric_code'        => '352',
					'minor_unit'          => 'Eyrir',
					'major_unit'          => 'Kronur',
				],
				'JMD' => [
					'code'             => 'JMD',
					'symbol'           => 'J$',
					'name'             => 'Jamaican Dollar',
					'numeric_code'     => '388',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Cent',
					'major_unit'       => 'Dollar',
				],
				'JOD' => [
					'code'         => 'JOD',
					'symbol'       => 'JD',
					'name'         => 'Jordanian Dinar',
					'decimals'     => 3,
					'numeric_code' => '400',
					'minor_unit'   => 'Piastr',
					'major_unit'   => 'Dinar',
				],
				'JPY' => [
					'code'             => 'JPY',
					'symbol'           => '¥',
					'name'             => 'Japanese Yen',
					'decimals'         => 0,
					'numeric_code'     => '392',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Sen',
					'major_unit'       => 'Yen',
				],
				'KES' => [
					'code'         => 'KES',
					'symbol'       => 'Ksh',
					'name'         => 'Kenyan Shilling',
					'numeric_code' => '404',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Shilling',
				],
				'KGS' => [
					'code'                => 'KGS',
					'code_placement'      => 'hidden',
					'symbol'              => 'сом',
					'symbol_placement'    => 'after',
					'name'                => 'Kyrgyzstani Som',
					'numeric_code'        => '417',
					'thousands_separator' => '',
					'major_unit'          => 'Som',
					'minor_unit'          => 'Tyiyn',
				],
				'KMF' => [
					'code'         => 'KMF',
					'symbol'       => 'CF',
					'name'         => 'Comorian Franc',
					'decimals'     => 0,
					'numeric_code' => '174',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'KRW' => [
					'code'         => 'KRW',
					'symbol'       => '₩',
					'name'         => 'South Korean Won',
					'decimals'     => 0,
					'numeric_code' => '410',
					'minor_unit'   => 'Jeon',
					'major_unit'   => 'Won',
				],
				'KWD' => [
					'code'         => 'KWD',
					'symbol'       => 'KD',
					'name'         => 'Kuwaiti Dinar',
					'decimals'     => 3,
					'numeric_code' => '414',
					'minor_unit'   => 'Fils',
					'major_unit'   => 'Dinar',
				],
				'KYD' => [
					'code'         => 'KYD',
					'symbol'       => 'KY$',
					'name'         => 'Cayman Islands Dollar',
					'numeric_code' => '136',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'KZT' => [
					'code'                => 'KZT',
					'symbol'              => 'тг.',
					'name'                => 'Kazakhstani tenge',
					'numeric_code'        => '398',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Tiyn',
					'major_unit'          => 'Tenge',
				],
				'LAK' => [
					'code'         => 'LAK',
					'symbol'       => '₭N',
					'name'         => 'Laotian Kip',
					'decimals'     => 0,
					'numeric_code' => '418',
					'minor_unit'   => 'Att',
					'major_unit'   => 'Kips',
				],
				'LBP' => [
					'code'         => 'LBP',
					'symbol'       => 'LB£',
					'name'         => 'Lebanese Pound',
					'decimals'     => 0,
					'numeric_code' => '422',
					'minor_unit'   => 'Piastre',
					'major_unit'   => 'Pound',
				],
				'LKR' => [
					'code'         => 'LKR',
					'symbol'       => 'SLRs',
					'name'         => 'Sri Lanka Rupee',
					'numeric_code' => '144',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Rupee',
				],
				'LRD' => [
					'code'         => 'LRD',
					'symbol'       => 'L$',
					'name'         => 'Liberian Dollar',
					'numeric_code' => '430',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'LSL' => [
					'code'         => 'LSL',
					'symbol'       => 'LSL',
					'name'         => 'Lesotho Loti',
					'numeric_code' => '426',
					'minor_unit'   => 'Sente',
					'major_unit'   => 'Loti',
				],
				'LTL' => [
					'code'         => 'LTL',
					'symbol'       => 'Lt',
					'name'         => 'Lithuanian Litas',
					'numeric_code' => '440',
					'minor_unit'   => 'Centas',
					'major_unit'   => 'Litai',
				],
				'LVL' => [
					'code'         => 'LVL',
					'symbol'       => 'Ls',
					'name'         => 'Latvian Lats',
					'numeric_code' => '428',
					'minor_unit'   => 'Santims',
					'major_unit'   => 'Lati',
				],
				'LYD' => [
					'code'         => 'LYD',
					'symbol'       => 'LD',
					'name'         => 'Libyan Dinar',
					'decimals'     => 3,
					'numeric_code' => '434',
					'minor_unit'   => 'Dirham',
					'major_unit'   => 'Dinar',
				],
				'MAD' => [
					'code'             => 'MAD',
					'symbol'           => ' Dhs',
					'name'             => 'Moroccan Dirham',
					'numeric_code'     => '504',
					'symbol_placement' => 'after',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Santimat',
					'major_unit'       => 'Dirhams',
				],
				'MDL' => [
					'code'             => 'MDL',
					'symbol'           => 'MDL',
					'name'             => 'Moldovan leu',
					'symbol_placement' => 'after',
					'numeric_code'     => '498',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'bani',
					'major_unit'       => 'Lei',
				],
				'MMK' => [
					'code'         => 'MMK',
					'symbol'       => 'MMK',
					'name'         => 'Myanma Kyat',
					'decimals'     => 0,
					'numeric_code' => '104',
					'minor_unit'   => 'Pya',
					'major_unit'   => 'Kyat',
				],
				'MNT' => [
					'code'         => 'MNT',
					'symbol'       => '₮',
					'name'         => 'Mongolian Tugrik',
					'decimals'     => 0,
					'numeric_code' => '496',
					'minor_unit'   => 'Möngö',
					'major_unit'   => 'Tugriks',
				],
				'MOP' => [
					'code'         => 'MOP',
					'symbol'       => 'MOP$',
					'name'         => 'Macanese Pataca',
					'numeric_code' => '446',
					'minor_unit'   => 'Avo',
					'major_unit'   => 'Pataca',
				],
				'MRO' => [
					'code'         => 'MRO',
					'symbol'       => 'UM',
					'name'         => 'Mauritanian Ouguiya',
					'decimals'     => 0,
					'numeric_code' => '478',
					'minor_unit'   => 'Khoums',
					'major_unit'   => 'Ouguiya',
				],
				'MTP' => [
					'code'       => 'MTP',
					'symbol'     => 'MT£',
					'name'       => 'Maltese Pound',
					'minor_unit' => 'Shilling',
					'major_unit' => 'Pound',
				],
				'MUR' => [
					'code'         => 'MUR',
					'symbol'       => 'MURs',
					'name'         => 'Mauritian Rupee',
					'decimals'     => 0,
					'numeric_code' => '480',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Rupee',
				],
				'MXN' => [
					'code'             => 'MXN',
					'symbol'           => '$',
					'name'             => 'Mexican Peso',
					'numeric_code'     => '484',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Centavo',
					'major_unit'       => 'Peso',
				],
				'MYR' => [
					'code'             => 'MYR',
					'symbol'           => 'RM',
					'name'             => 'Malaysian Ringgit',
					'numeric_code'     => '458',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Sen',
					'major_unit'       => 'Ringgits',
				],
				'MZN' => [
					'code'       => 'MZN',
					'symbol'     => 'MTn',
					'name'       => 'Mozambican Metical',
					'minor_unit' => 'Centavo',
					'major_unit' => 'Metical',
				],
				'NAD' => [
					'code'         => 'NAD',
					'symbol'       => 'N$',
					'name'         => 'Namibian Dollar',
					'numeric_code' => '516',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'NGN' => [
					'code'         => 'NGN',
					'symbol'       => '₦',
					'name'         => 'Nigerian Naira',
					'numeric_code' => '566',
					'minor_unit'   => 'Kobo',
					'major_unit'   => 'Naira',
				],
				'NIO' => [
					'code'         => 'NIO',
					'symbol'       => 'C$',
					'name'         => 'Nicaraguan Cordoba Oro',
					'numeric_code' => '558',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Cordoba',
				],
				'NOK' => [
					'code'                => 'NOK',
					'symbol'              => 'Nkr',
					'name'                => 'Norwegian Krone',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'numeric_code'        => '578',
					'minor_unit'          => 'Øre',
					'major_unit'          => 'Krone',
				],
				'NPR' => [
					'code'         => 'NPR',
					'symbol'       => 'NPRs',
					'name'         => 'Nepalese Rupee',
					'numeric_code' => '524',
					'minor_unit'   => 'Paisa',
					'major_unit'   => 'Rupee',
				],
				'NZD' => [
					'code'         => 'NZD',
					'symbol'       => 'NZ$',
					'name'         => 'New Zealand Dollar',
					'numeric_code' => '554',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'OMR' => [
					'code'         => 'OMR',
					'symbol'       => 'OMR',
					'name'         => 'Omani Rial',
					'numeric_code' => '512',
					'minor_unit'   => 'Baisa',
					'major_unit'   => 'Rials',
				],
				'PAB' => [
					'code'         => 'PAB',
					'symbol'       => 'B/.',
					'name'         => 'Panamanian Balboa',
					'numeric_code' => '590',
					'minor_unit'   => 'Centésimo',
					'major_unit'   => 'Balboa',
				],
				'PEN' => [
					'code'             => 'PEN',
					'symbol'           => 'S/.',
					'name'             => 'Peruvian Nuevo Sol',
					'numeric_code'     => '604',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Céntimo',
					'major_unit'       => 'Nuevos Sole',
				],
				'PGK' => [
					'code'         => 'PGK',
					'symbol'       => 'PGK',
					'name'         => 'Papua New Guinean Kina',
					'numeric_code' => '598',
					'minor_unit'   => 'Toea',
					'major_unit'   => 'Kina ',
				],
				'PHP' => [
					'code'         => 'PHP',
					'symbol'       => '₱',
					'name'         => 'Philippine Peso',
					'numeric_code' => '608',
					'minor_unit'   => 'Centavo',
					'major_unit'   => 'Peso',
				],
				'PKR' => [
					'code'         => 'PKR',
					'symbol'       => 'PKRs',
					'name'         => 'Pakistani Rupee',
					'decimals'     => 0,
					'numeric_code' => '586',
					'minor_unit'   => 'Paisa',
					'major_unit'   => 'Rupee',
				],
				'PLN' => [
					'code'                => 'PLN',
					'symbol'              => 'zł',
					'name'                => 'Polish Złoty',
					'decimal_separator'   => ',',
					'thousands_separator' => ' ',
					'numeric_code'        => '985',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Grosz',
					'major_unit'          => 'Złotych',
				],
				'PYG' => [
					'code'         => 'PYG',
					'symbol'       => '₲',
					'name'         => 'Paraguayan Guarani',
					'decimals'     => 0,
					'numeric_code' => '600',
					'minor_unit'   => 'Céntimo',
					'major_unit'   => 'Guarani',
				],
				'QAR' => [
					'code'         => 'QAR',
					'symbol'       => 'QR',
					'name'         => 'Qatari Rial',
					'numeric_code' => '634',
					'minor_unit'   => 'Dirham',
					'major_unit'   => 'Rial',
				],
				'RHD' => [
					'code'       => 'RHD',
					'symbol'     => 'RH$',
					'name'       => 'Rhodesian Dollar',
					'minor_unit' => 'Cent',
					'major_unit' => 'Dollar',
				],
				'RON' => [
					'code'       => 'RON',
					'symbol'     => 'RON',
					'name'       => 'Romanian Leu',
					'minor_unit' => 'Ban',
					'major_unit' => 'Leu',
				],
				'RSD' => [
					'code'       => 'RSD',
					'symbol'     => 'din.',
					'name'       => 'Serbian Dinar',
					'decimals'   => 0,
					'minor_unit' => 'Para',
					'major_unit' => 'Dinars',
				],
				'RUB' => [
					'code'                => 'RUB',
					'symbol'              => 'руб.',
					'name'                => 'Russian Ruble',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'numeric_code'        => '643',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Kopek',
					'major_unit'          => 'Ruble',
				],
				'SAR' => [
					'code'         => 'SAR',
					'symbol'       => 'SR',
					'name'         => 'Saudi Riyal',
					'numeric_code' => '682',
					'minor_unit'   => 'Hallallah',
					'major_unit'   => 'Riyals',
				],
				'SBD' => [
					'code'         => 'SBD',
					'symbol'       => 'SI$',
					'name'         => 'Solomon Islands Dollar',
					'numeric_code' => '090',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'SCR' => [
					'code'         => 'SCR',
					'symbol'       => 'SRe',
					'name'         => 'Seychellois Rupee',
					'numeric_code' => '690',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Rupee',
				],
				'SDD' => [
					'code'         => 'SDD',
					'symbol'       => 'LSd',
					'name'         => 'Old Sudanese Dinar',
					'numeric_code' => '736',
					'minor_unit'   => 'None',
					'major_unit'   => 'Dinar',
				],
				'SEK' => [
					'code'                => 'SEK',
					'symbol'              => 'kr',
					'name'                => 'Swedish Krona',
					'numeric_code'        => '752',
					'thousands_separator' => ' ',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Öre',
					'major_unit'          => 'Kronor',
				],
				'SGD' => [
					'code'         => 'SGD',
					'symbol'       => 'S$',
					'name'         => 'Singapore Dollar',
					'numeric_code' => '702',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'SHP' => [
					'code'         => 'SHP',
					'symbol'       => 'SH£',
					'name'         => 'Saint Helena Pound',
					'numeric_code' => '654',
					'minor_unit'   => 'Penny',
					'major_unit'   => 'Pound',
				],
				'SLL' => [
					'code'         => 'SLL',
					'symbol'       => 'Le',
					'name'         => 'Sierra Leonean Leone',
					'decimals'     => 0,
					'numeric_code' => '694',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Leone',
				],
				'SOS' => [
					'code'         => 'SOS',
					'symbol'       => 'Ssh',
					'name'         => 'Somali Shilling',
					'decimals'     => 0,
					'numeric_code' => '706',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Shilling',
				],
				'SRD' => [
					'code'       => 'SRD',
					'symbol'     => 'SR$',
					'name'       => 'Surinamese Dollar',
					'minor_unit' => 'Cent',
					'major_unit' => 'Dollar',
				],
				'SRG' => [
					'code'         => 'SRG',
					'symbol'       => 'Sf',
					'name'         => 'Suriname Guilder',
					'numeric_code' => '740',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Guilder',
				],
				'STD' => [
					'code'         => 'STD',
					'symbol'       => 'Db',
					'name'         => 'São Tomé and Príncipe Dobra',
					'decimals'     => 0,
					'numeric_code' => '678',
					'minor_unit'   => 'Cêntimo',
					'major_unit'   => 'Dobra',
				],
				'SYP' => [
					'code'         => 'SYP',
					'symbol'       => 'SY£',
					'name'         => 'Syrian Pound',
					'decimals'     => 0,
					'numeric_code' => '760',
					'minor_unit'   => 'Piastre',
					'major_unit'   => 'Pound',
				],
				'SZL' => [
					'code'         => 'SZL',
					'symbol'       => 'SZL',
					'name'         => 'Swazi Lilangeni',
					'numeric_code' => '748',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Lilangeni',
				],
				'THB' => [
					'code'         => 'THB',
					'symbol'       => '฿',
					'name'         => 'Thai Baht',
					'numeric_code' => '764',
					'minor_unit'   => 'Satang',
					'major_unit'   => 'Baht',
				],
				'TND' => [
					'code'         => 'TND',
					'symbol'       => 'DT',
					'name'         => 'Tunisian Dinar',
					'decimals'     => 3,
					'numeric_code' => '788',
					'minor_unit'   => 'Millime',
					'major_unit'   => 'Dinar',
				],
				'TOP' => [
					'code'         => 'TOP',
					'symbol'       => 'T$',
					'name'         => 'Tongan Paʻanga',
					'numeric_code' => '776',
					'minor_unit'   => 'Senit',
					'major_unit'   => 'Paʻanga',
				],
				'TRY' => [
					'code'                => 'TRY',
					'symbol'              => 'TL',
					'name'                => 'Turkish Lira',
					'numeric_code'        => '949',
					'thousands_separator' => '.',
					'decimal_separator'   => ',',
					'symbol_placement'    => 'after',
					'code_placement'      => '',
					'minor_unit'          => 'Kurus',
					'major_unit'          => 'Lira',
				],
				'TTD' => [
					'code'         => 'TTD',
					'symbol'       => 'TT$',
					'name'         => 'Trinidad and Tobago Dollar',
					'numeric_code' => '780',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'TWD' => [
					'code'         => 'TWD',
					'symbol'       => 'NT$',
					'name'         => 'New Taiwan Dollar',
					'numeric_code' => '901',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'New Dollar',
				],
				'TZS' => [
					'code'         => 'TZS',
					'symbol'       => 'TSh',
					'name'         => 'Tanzanian Shilling',
					'decimals'     => 0,
					'numeric_code' => '834',
					'minor_unit'   => 'Senti',
					'major_unit'   => 'Shilling',
				],
				'UAH' => [
					'code'                => 'UAH',
					'symbol'              => 'грн.',
					'name'                => 'Ukrainian Hryvnia',
					'numeric_code'        => '980',
					'thousands_separator' => '',
					'decimal_separator'   => '.',
					'symbol_placement'    => 'after',
					'code_placement'      => 'hidden',
					'minor_unit'          => 'Kopiyka',
					'major_unit'          => 'Hryvnia',
				],
				'UGX' => [
					'code'         => 'UGX',
					'symbol'       => 'USh',
					'name'         => 'Ugandan Shilling',
					'decimals'     => 0,
					'numeric_code' => '800',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Shilling',
				],
				'USD' => [
					'code'             => 'USD',
					'symbol'           => '$',
					'name'             => 'United States Dollar',
					'numeric_code'     => '840',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Cent',
					'major_unit'       => 'Dollar',
				],
				'UYU' => [
					'code'         => 'UYU',
					'symbol'       => '$U',
					'name'         => 'Uruguayan Peso',
					'numeric_code' => '858',
					'minor_unit'   => 'Centésimo',
					'major_unit'   => 'Peso',
				],
				'VEF' => [
					'code'       => 'VEF',
					'symbol'     => 'Bs.F.',
					'name'       => 'Venezuelan Bolívar Fuerte',
					'minor_unit' => 'Céntimo',
					'major_unit' => 'Bolivares Fuerte',
				],
				'VND' => [
					'code'                => 'VND',
					'symbol'              => 'đ',
					'name'                => 'Vietnamese Dong',
					'decimals'            => 0,
					'thousands_separator' => '.',
					'symbol_placement'    => 'after',
					'symbol_spacer'       => '',
					'code_placement'      => 'hidden',
					'numeric_code'        => '704',
					'minor_unit'          => 'Hà',
					'major_unit'          => 'Dong',
				],
				'VUV' => [
					'code'         => 'VUV',
					'symbol'       => 'VT',
					'name'         => 'Vanuatu Vatu',
					'decimals'     => 0,
					'numeric_code' => '548',
					'major_unit'   => 'Vatu',
				],
				'WST' => [
					'code'         => 'WST',
					'symbol'       => 'WS$',
					'name'         => 'Samoan Tala',
					'numeric_code' => '882',
					'minor_unit'   => 'Sene',
					'major_unit'   => 'Tala',
				],
				'XAF' => [
					'code'         => 'XAF',
					'symbol'       => 'FCFA',
					'name'         => 'CFA Franc BEAC',
					'decimals'     => 0,
					'numeric_code' => '950',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'XCD' => [
					'code'         => 'XCD',
					'symbol'       => 'EC$',
					'name'         => 'East Caribbean Dollar',
					'numeric_code' => '951',
					'minor_unit'   => 'Cent',
					'major_unit'   => 'Dollar',
				],
				'XOF' => [
					'code'         => 'XOF',
					'symbol'       => 'CFA',
					'name'         => 'CFA Franc BCEAO',
					'decimals'     => 0,
					'numeric_code' => '952',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'XPF' => [
					'code'         => 'XPF',
					'symbol'       => 'CFPF',
					'name'         => 'CFP Franc',
					'decimals'     => 0,
					'numeric_code' => '953',
					'minor_unit'   => 'Centime',
					'major_unit'   => 'Franc',
				],
				'YER' => [
					'code'         => 'YER',
					'symbol'       => 'YR',
					'name'         => 'Yemeni Rial',
					'decimals'     => 0,
					'numeric_code' => '886',
					'minor_unit'   => 'Fils',
					'major_unit'   => 'Rial',
				],
				'ZAR' => [
					'code'             => 'ZAR',
					'symbol'           => 'R',
					'name'             => 'South African Rand',
					'numeric_code'     => '710',
					'symbol_placement' => 'before',
					'code_placement'   => 'hidden',
					'minor_unit'       => 'Cent',
					'major_unit'       => 'Rand',
				],
				'ZMK' => [
					'code'         => 'ZMK',
					'symbol'       => 'ZK',
					'name'         => 'Zambian Kwacha',
					'decimals'     => 0,
					'numeric_code' => '894',
					'minor_unit'   => 'Ngwee',
					'major_unit'   => 'Kwacha',
				],
				'MKD' => [
					'code'       => 'MKD',
					'symbol'     => 'ден',
					'name'       => 'Macedonian Denar',
					'decimals'   => 0,
					'minor_unit' => 'Deni',
				],
			]
		);
	}

	public static function get_sent_localize_global_settings( $key ) {
		return SettingsApi::get_option( $key, 'gym_builder_global_settings' ) ?: '';
	}

	public static function generate_member_user_id() {
		$four_digit_random = wp_rand( 1000, 9999 );

		return 'GB' . $four_digit_random;
	}

	public static function has_zoom_integration_addon() {
		return function_exists( 'gbzi' );
	}

	public static function has_class_booking_and_payment_addon() {
		return function_exists( 'gbcbap' );
	}

	public static function gym_builder_has_pro_addons() {
		return false;
	}

	/**
	 * Get all Post Type
	 * @return array
	 */
	public static function get_post_types( $exc = '' ) {
		$post_types = get_post_types(
			[
				'public' => true,
			],
			'objects'
		);
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		$exclude = apply_filters(
			'gym_builder_ajax_post_exclude',
			[ 'attachment', 'revision', 'nav_menu_item', 'elementor_library', 'tpg_builder', 'e-landing-page' ]
		);
		if ( $exc ) {
			$exclude = array_merge( $exclude, $exc );
		}

		foreach ( $exclude as $ex ) {
			unset( $post_types[ $ex ] );
		}

		return $post_types;
	}

}