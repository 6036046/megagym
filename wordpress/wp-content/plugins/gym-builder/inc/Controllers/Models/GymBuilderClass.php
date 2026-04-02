<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Models;

use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\ShortcodeQuery;
use GymBuilder\Inc\Traits\Constants;
use WpDreamers\GBCBAP\Helper\Fns;

class GymBuilderClass {

	use Constants;


	public static function the_title( $class_id ) {
		echo esc_html( get_the_title( $class_id ) );
	}

	public static function get_the_title( $class_id ) {
		return get_the_title( $class_id );
	}

	public static function the_content( $class_id ) {
		echo esc_html( get_the_content( $class_id ) );
	}

	public static function get_the_content( $class_id ) {
		return get_the_content( $class_id );
	}

	public static function get_classes() {
		return get_posts(
			[
				'post_type'   => self::$class_post_type,
				'post_status' => 'publish',
                'posts_per_page' => -1
			]
		);
	}

	public static function get_the_schedule( $class_id, $schedule_limit = '' ) {
		$schedule = get_post_meta( $class_id, 'gym_builder_class_schedule', true );
		$schedule = ( $schedule != '' ) ? $schedule : array();

		if ( $schedule && ! empty( $schedule_limit ) ) {
			$schedule = array_slice( $schedule, 0, $schedule_limit );
		}

		return $schedule;
	}

	public static function get_the_weekname() {

		$weeknames = array(
			'mon' => esc_html__( 'Mon', 'gym-builder' ),
			'tue' => esc_html__( 'Tue', 'gym-builder' ),
			'wed' => esc_html__( 'Wed', 'gym-builder' ),
			'thu' => esc_html__( 'Thur', 'gym-builder' ),
			'fri' => esc_html__( 'Fri', 'gym-builder' ),
			'sat' => esc_html__( 'Sat', 'gym-builder' ),
			'sun' => esc_html__( 'Sun', 'gym-builder' ),
		);

		return apply_filters( 'gym_builder_weeknames_short', $weeknames );
	}

	public static function get_the_routine_weekname() {

		$weeknames = array(
			'mon' => esc_html__( 'Mon', 'gym-builder' ),
			'tue' => esc_html__( 'Tue', 'gym-builder' ),
			'wed' => esc_html__( 'Wed', 'gym-builder' ),
			'thu' => esc_html__( 'Thur', 'gym-builder' ),
			'fri' => esc_html__( 'Fri', 'gym-builder' ),
			'sat' => esc_html__( 'Sat', 'gym-builder' ),
			'sun' => esc_html__( 'Sun', 'gym-builder' ),
		);

		return apply_filters( 'gym_builder_routine_weeknames_short', $weeknames );
	}

	public static function sort_by_time_as_key( $a, $b ) {
		 return strtotime( $a ) - strtotime( $b );
	}

	public static function sort_by_end_time( $a, $b ) {
		return strtotime( $a['end_time'] ) <=> strtotime( $b['end_time'] );
	}

	public static function get_schedule_routine( int $shortcode_id, array $metas, string $layout_id ) {
		$html             = null;
		$containerAttr    = null;
		$weeknames        = self::get_the_routine_weekname();
		$schedule         = array();
		$routines_info    = array();
		$class_query_info = array();
		$available_weeks  = array();
		$html             .= '<div class="' . esc_attr( $layout_id . ' ' . $metas['layout'] ) . ' gym-builder-routine">';
		$query            = ( new ShortcodeQuery() )->buildArgs( $shortcode_id, $metas, self::$class_post_type, self::$class_taxonomy )->get_gb_shortcode_posts();
		$temp             = Helper::wp_set_temp_query( $query );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$class_id                         = get_the_ID();
                $routine_color                    = get_post_meta($class_id,'gym_builder_class_color',true);
				$class_info                       = get_post_meta( $class_id, 'gym_builder_class_schedule', true );
				$class_info                       = ( $class_info != '' ) ? $class_info : array();
				$class_query_info[ get_the_ID() ] = get_the_title();

				foreach ( $class_info as $meta ) {
					if ( empty( $meta['week'] ) || $meta['week'] == 'none' || empty( $meta['start_time'] ) ) {
						continue;
					}
					$start_time = strtotime( $meta['start_time'] );
					$end_time   = ! empty( $meta['end_time'] ) ? strtotime( $meta['end_time'] ) : false;

					if ( $metas['time_format'] == '24' ) {
						$start_time = date( "H:i", $start_time );
						$end_time   = $end_time ? date( "H:i", $end_time ) : '';
					} else {
						$start_time = date( "g:ia", $start_time );
						$end_time   = $end_time ? date( "g:ia", $end_time ) : '';
					}

					if ( ! in_array( $meta['week'], $available_weeks ) ) {
						$available_weeks[] = $meta['week'];
					}
					$schedule[ $start_time ][ $meta['week'] ][] = array(
						'id'         => $class_id,
                        'color'      => $routine_color,
						'class'      => get_the_title(),
						'start_time' => $start_time,
						'end_time'   => $end_time,
                        'trainer_name'    => !empty( $meta['trainer'] ) ? get_the_title( $meta['trainer'] ) : '',
					);
				}

			}
			foreach ( $weeknames as $key => $value ) {
				if ( ! in_array( $key, $available_weeks ) ) {
					unset( $weeknames[ $key ] );
				}
			}
			uksort( $schedule, array( GymBuilderClass::class, 'sort_by_time_as_key' ) );
			$routines_info['schedule']          = $schedule;
			$routines_info['weeknames']         = $weeknames;
			$routines_info['class_query_info'] = $class_query_info;
            $routines_info['show_routine_nav'] = $metas['routine_nav'] ?? false;
            $routines_info['shortcode_time_format'] = $metas['time_format'] ?? '12';
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();
			$html .= Functions::render('shortcode/class/layouts/'.$metas['layout'],$routines_info,true);
			ob_start();
			$html .= ob_get_contents();
			ob_end_clean();

		} else {
			$html .= '<p>' . esc_html__( 'No posts found.', 'gym-builder' ) . '</p>';
		}
		Helper::wp_reset_temp_query( $temp );
		$html .= '</div>';

		return $html;
	}

	public static function exclude_global_query_layout() {
		return [
			'layout-2',
		];
	}

	public static function print_routine( $routine,$weekname='',$time_format ='12' ) {
		usort( $routine, array( GymBuilderClass::class, 'sort_by_end_time' ) );

		?>
		<?php foreach ( $routine as $each_routine ): ?>

			<?php
			$class     = 'gym-builder-routine show fade gym-builder-routine-id-' . $each_routine['id'];
            $style = $each_routine['color'] ? ' style="background-color:' . esc_attr( $each_routine['color'] ) . '; color: #fff;"' : '';
            $permalink = get_the_permalink( $each_routine['id'] );
			$start_tag = '<div class="' . $class . '" '.$style.'>';
			$end_tag   = '</div>';
            $start_time = $each_routine['start_time'] ?? '';
            $end_time = $each_routine['end_time'] ?? '';
            if ($time_format == '24'){
                $time_range = date( "g:ia", strtotime($start_time) ) . " - ".date("g:ia", strtotime($end_time));
            }else{
	            $time_range = $start_time . ' - ' . $end_time;
            }

            $schedule_day = $weekname ? Helper::reverse_day_name_map()[$weekname]:'';
            $gym_builder_schedule_v2 = get_post_meta($each_routine['id'], 'gym_builder_v2_class_schedule',true);
            $available_slot = '';
            if ($gym_builder_schedule_v2 && isset($gym_builder_schedule_v2[$each_routine['id']][$weekname][$time_range])){
                $total_slot = $gym_builder_schedule_v2[$each_routine['id']][$weekname][$time_range]['total_slot'] ?? 0;
                $total_booked = $gym_builder_schedule_v2[$each_routine['id']][$weekname][$time_range]['total_booked'] ?? 0;
                $available_slot = $total_slot > 0 ? (intval($total_slot)- intval($total_booked)) : 0;
            }
            $schedule_booking_info = [
                'class_id'   => $each_routine['id'],
                'class_name' =>$each_routine['class'],
                'time_range' => $time_range,
                'class_day'  => $schedule_day,
                'available_slot' => $available_slot,
            ];

			?>
			<?php echo wp_kses_post($start_tag); ?>
            <div class="gym-builder-routine-time">
                <span><?php echo esc_html( $each_routine['start_time'] ); ?></span>
				<?php if ( ! empty( $each_routine['end_time'] ) ): ?>
                    <span>- <?php echo esc_html( $each_routine['end_time'] ); ?></span>
				<?php endif; ?>
            </div>
            <h4 class="gym-builder-routine-title"><a
                        href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $each_routine['class'] ); ?></a>
            </h4>
            <?php
            if ($each_routine['trainer_name']){
            ?>
                <div class="trainer-title">
                    <?php echo esc_html($each_routine['trainer_name']); ?>
                </div>
            <?php } ?>
            <?php do_action('gym_builder_schedule_booking',$schedule_booking_info);?>
			<?php echo wp_kses_post($end_tag); ?>
		<?php endforeach; ?>
		<?php
	}

	public static function time_picker_format() {
		return SettingsApi::get_option( 'class_time_format', 'gym_builder_class_settings' ) == 24 ? '24' : '12';
	}

	public static function get_schedule_time( $start_time, $end_time ) {

		$start_time = ! empty( $start_time ) ? strtotime( $start_time ) : false;
		$end_time   = ! empty( $end_time ) ? strtotime( $end_time ) : false;

		if ( self::time_picker_format() == '24' ) {
			$start_time = $start_time ? date_i18n( "H:i", $start_time ) : '';
			$end_time   = $end_time ? date_i18n( "H:i", $end_time ) : '';
		} else {
			$start_time = $start_time ? date_i18n( "g:ia", $start_time ) : '';
			$end_time   = $end_time ? date_i18n( "g:ia", $end_time ) : '';
		}

		return [
			'start_time' => $start_time,
			'end_time'   => $end_time
		];

	}

	public static function get_shortcode_schedule_time( $start_time, $end_time, $time_format ) {

		$start_time = ! empty( $start_time ) ? strtotime( $start_time ) : false;
		$end_time   = ! empty( $end_time ) ? strtotime( $end_time ) : false;

		if ( $time_format == '24' ) {
			$start_time = $start_time ? date_i18n( "H:i", $start_time ) : '';
			$end_time   = $end_time ? date_i18n( "H:i", $end_time ) : '';
		} else {
			$start_time = $start_time ? date_i18n( "g:ia", $start_time ) : '';
			$end_time   = $end_time ? date_i18n( "g:ia", $end_time ) : '';
		}

		return [
			'start_time' => $start_time,
			'end_time'   => $end_time
		];

	}

	public static function get_schedule_meta( array $schedules, array $weeknames, bool $show_trainer = true, bool $show_schedule_title = true, bool $shortcode_time_format = false, string $time_format = '12' ) {
		?>
        <ul class="class-meta">
			<?php
			foreach ( $schedules as $schedule_info ) {

				if ( ! empty( $schedule_info['week'] ) && ! empty( $schedule_info['start_time'] ) ) {

					$start_time = $schedule_info['start_time'];

					$end_time = ! empty( $schedule_info['end_time'] ) ? $schedule_info['end_time'] : false;

					$type = ! empty( $schedule_info['trainer'] ) ? get_post_type( $schedule_info['trainer'] ) : '';

					if ( $type == self::$trainer_post_type ) {
						$trainer_name = get_the_title( $schedule_info['trainer'] );
					}

					if ( $shortcode_time_format ) {
						$class_schedule_time = self::get_shortcode_schedule_time( $start_time, $end_time, $time_format );
					} else {
						$class_schedule_time = self::get_schedule_time( $start_time, $end_time );
					}


					$full_time = $class_schedule_time['start_time'] . "-" . $class_schedule_time['end_time']

					?>
                    <li>
						<?php if ( ! empty( $trainer_name ) && $show_trainer === true ): ?>
                            <span class="trainer">
		                            <span class="trainer-title"><?php esc_html_e( 'Trainer : ', 'gym-builder' ); ?></span>
		                            <span class="trainer-name"><?php echo esc_html( $trainer_name ); ?></span>
		                        </span>
						<?php endif; ?>
                        <span class="schedule">
                            <?php if ( $show_schedule_title === true ) {
	                            ?>
                                <span class="schedule-title"><?php esc_html_e( 'Schedule : ', 'gym-builder' ); ?></span>
                            <?php } ?>
		                        <span class="day"><?php echo esc_html( $weeknames[ $schedule_info['week'] ] ); ?>:</span>
		                        <span class="time"><?php echo esc_html( $full_time ); ?></span>
		                    </span>
                    </li>
				<?php }

			}
			?>
        </ul>
		<?php
	}

	public static function get_category_html_format( $class_id ) {
		$term_lists = get_the_terms( $class_id, self::$class_taxonomy );
		$i          = 1;
		if ( $term_lists ) {
			?>
            <div class="class-category">
				<?php
				foreach ( $term_lists as $term_list ) {
					$link = get_term_link( $term_list->term_id, self::$class_taxonomy ); ?>
					<?php if ( $i > 1 ) {
						echo esc_html( ', ' );
					} ?>
                    <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term_list->name ); ?></a>
					<?php $i ++;
				} ?>
            </div>
		<?php }
	}

	public static function get_button_html_format( $class_id ) {
		$button_text = get_post_meta( $class_id, 'gym_builder_class_button_text', true );
		$button_link = get_post_meta( $class_id, 'gym_builder_class_button_url', true ) ?: '#';
		if (function_exists('gbcbap') &&  Fns::is_wc_product( $class_id )){
			do_action('gym_builder_class_page_buy_button',$class_id);
		} elseif ( $button_text ) {
			?>
            <div class="class-button">
                <a class="gym-builder-btn"
                   href="<?php echo esc_url( $button_link ); ?>"><?php echo esc_html( $button_text ); ?>
                </a>
            </div>
		<?php }
	}

	public static function get_categories_array() {
		$categories_list = [];
		$terms           = get_terms( [
			'taxonomy'   => self::$class_taxonomy,
			'hide_empty' => false
		] );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories_list[ $term->term_id ] = $term->name;
			}
		}

		return apply_filters( "gym_builder_array_classes_category_list", $categories_list );
	}

	public static function get_slider_settings() {
		$slides_per_view = SettingsApi::get_option( 'slides_per_view', 'gym_builder_class_settings' ) ?: '3';
		$slider_autoplay = SettingsApi::get_option( 'slider_autoplay', 'gym_builder_class_settings' ) === 'on';
		$slider_loop     = SettingsApi::get_option( 'slider_loop', 'gym_builder_class_settings' ) === 'on';
		$centered_slider = SettingsApi::get_option( 'centered_slider', 'gym_builder_class_settings' ) === 'on';
		$slider_settings = array(
			'slidesPerView'       => $slides_per_view,
			'loop'                => $slider_loop,
			'spaceBetween'        => 20,
			'slidesPerGroup'      => 1,
			'centeredSlides'      => $centered_slider,
			'slideToClickedSlide' => true,
			'autoplay'            => array(
				'delay' => 2000,
			),
			'speed'               => 2000,
			'breakpoints'         => array(
				'0'    => array( 'slidesPerView' => 1 ),
				'576'  => array( 'slidesPerView' => 1 ),
				'768'  => array( 'slidesPerView' => 1 ),
				'992'  => array( 'slidesPerView' => 2 ),
				'1200' => array( 'slidesPerView' => 2 ),
				'1600' => array( 'slidesPerView' => $slides_per_view )
			),
			'auto'                => $slider_autoplay
		);

		return apply_filters( 'gym_builder_class_slider_settings', $slider_settings );
	}
    /**
     * Get all classes assigned to a specific trainer
     *
     * @param int $trainer_id The trainer post ID
     * @return array Array of class objects with schedule information
     */
    public static function get_trainer_classes( $trainer_id ) {
        global $wpdb;

        if ( empty( $trainer_id ) ) {
            return array();
        }

        // Get all published gym_class posts
        $classes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID, p.post_title, p.post_content
                 FROM {$wpdb->posts} p
                 WHERE p.post_type = %s
                 AND p.post_status = %s
                 ORDER BY p.post_title ASC",
                Functions::$class_post_type,
                'publish'
            )
        );

        if ( empty( $classes ) ) {
            return array();
        }

        $trainer_classes = array();

        // Check each class for the trainer in schedule
        foreach ( $classes as $class ) {
            $schedules = get_post_meta( $class->ID, 'gym_builder_class_schedule', true );

            if ( empty( $schedules ) || ! is_array( $schedules ) ) {
                continue;
            }

            // Check if trainer is assigned to any schedule
            $has_trainer = false;
            $class_schedules = array();

            foreach ( $schedules as $schedule ) {
                if ( isset( $schedule['trainer'] ) && absint( $schedule['trainer'] ) === absint( $trainer_id ) ) {
                    $has_trainer = true;
                    $class_schedules[] = array(
                        'week'          => isset( $schedule['week'] ) ? sanitize_text_field( $schedule['week'] ) : '',
                        'start_time'    => isset( $schedule['start_time'] ) ? sanitize_text_field( $schedule['start_time'] ) : '',
                        'end_time'      => isset( $schedule['end_time'] ) ? sanitize_text_field( $schedule['end_time'] ) : '',
                    );
                }
            }

            if ( $has_trainer ) {
                $class->schedules = $class_schedules;
                $class->total_schedules = count( $class_schedules );
                $trainer_classes[] = $class;
            }
        }

        return $trainer_classes;
    }

    /**
     * Get today's class schedule for a specific trainer
     *
     * @param int $trainer_id The trainer post ID
     * @return array Array of today's classes with schedule details
     */
    public static function get_today_trainer_classes( $trainer_id ) {
        global $wpdb;

        if ( empty( $trainer_id ) ) {
            return array();
        }

        // Get current day abbreviation (mon, tue, wed, etc.)
        $current_day = strtolower( current_time( 'D' ) ); // Returns: mon, tue, wed, thu, fri, sat, sun

        // Get all published gym_class posts
        $classes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID, p.post_title, p.post_content
                 FROM {$wpdb->posts} p
                 WHERE p.post_type = %s
                 AND p.post_status = %s
                 ORDER BY p.post_title ASC",
                Functions::$class_post_type,
                'publish'
            )
        );

        if ( empty( $classes ) ) {
            return array();
        }

        $today_classes = array();

        // Check each class for today's schedule
        foreach ( $classes as $class ) {
            $schedules = get_post_meta( $class->ID, 'gym_builder_class_schedule', true );

            if ( empty( $schedules ) || ! is_array( $schedules ) ) {
                continue;
            }

            // Check if trainer has class today
            foreach ( $schedules as $schedule ) {
                if (
                    isset( $schedule['trainer'] ) &&
                    absint( $schedule['trainer'] ) === absint( $trainer_id ) &&
                    isset( $schedule['week'] ) &&
                    strtolower( $schedule['week'] ) === $current_day
                ) {
                    $class_data = clone $class;
                    $class_data->schedule = array(
                        'day'           => sanitize_text_field( $schedule['week'] ),
                        'start_time'    => isset( $schedule['start_time'] ) ? sanitize_text_field( $schedule['start_time'] ) : '',
                        'end_time'      => isset( $schedule['end_time'] ) ? sanitize_text_field( $schedule['end_time'] ) : '',
                        'max_members'   => isset( $schedule['maximum_member_allow_booking'] ) ? absint( $schedule['maximum_member_allow_booking'] ) : 0,
                    );

                    // Calculate time for sorting
                    $class_data->sort_time = self::convert_time_to_minutes( $class_data->schedule['start_time'] );

                    $today_classes[] = $class_data;
                }
            }
        }

        // Sort by start time
        usort( $today_classes, function( $a, $b ) {
            return $a->sort_time - $b->sort_time;
        });

        return $today_classes;
    }

    /**
     * Convert time string to minutes for sorting
     *
     * @param string $time Time string (e.g., "8:00am", "12:30pm", "14:30")
     * @return int Total minutes from midnight
     */
    private static function convert_time_to_minutes( $time ) {
        if ( empty( $time ) ) {
            return 0;
        }

        // Remove spaces and convert to lowercase
        $time = strtolower( str_replace( ' ', '', $time ) );

        // Check for 12-hour format (with am/pm)
        if ( preg_match( '/(\d+):(\d+)(am|pm)/', $time, $matches ) ) {
            $hours = absint( $matches[1] );
            $minutes = absint( $matches[2] );
            $period = $matches[3];

            // Convert to 24-hour format
            if ( $period === 'pm' && $hours !== 12 ) {
                $hours += 12;
            } elseif ( $period === 'am' && $hours === 12 ) {
                $hours = 0;
            }

            return ( $hours * 60 ) + $minutes;
        }

        // Check for 24-hour format
        if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $matches ) ) {
            $hours = absint( $matches[1] );
            $minutes = absint( $matches[2] );

            return ( $hours * 60 ) + $minutes;
        }

        return 0;
    }

    /**
     * Get weekly schedule summary for trainer
     *
     * @param int $trainer_id The trainer post ID
     * @return array Array with day-wise class count
     */
    public static function get_trainer_weekly_summary( $trainer_id ) {
        if ( empty( $trainer_id ) ) {
            return array();
        }

        $days = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
        $weekly_summary = array_fill_keys( $days, 0 );

        $all_classes = self::get_trainer_classes( $trainer_id );

        foreach ( $all_classes as $class ) {
            if ( ! empty( $class->schedules ) ) {
                foreach ( $class->schedules as $schedule ) {
                    $day = strtolower( $schedule['week'] );
                    if ( isset( $weekly_summary[ $day ] ) ) {
                        $weekly_summary[ $day ]++;
                    }
                }
            }
        }

        return $weekly_summary;
    }

    /**
     * Format time for display based on settings
     *
     * @param string $time Time string
     * @return string Formatted time
     */
    public static function format_time( $time ) {
        if ( empty( $time ) ) {
            return '';
        }

        // Clean up the time string
        $time = trim( str_replace( ' ', '', $time ) );

        // Get time format from settings (12 or 24 hour)
        $time_format = self::get_time_format();

        // If already in correct format, return as is
        if ( $time_format === '12' ) {
            // 12-hour format - ensure am/pm is present
            if ( preg_match( '/\d+:\d+(am|pm)/i', $time ) ) {
                return strtolower( $time );
            }
            // If 24-hour format provided, convert to 12-hour
            if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $matches ) ) {
                return self::convert_24_to_12( $time );
            }
        } else {
            // 24-hour format
            if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $time ) ) {
                return $time;
            }
            // If 12-hour format provided, convert to 24-hour
            if ( preg_match( '/\d+:\d+(am|pm)/i', $time ) ) {
                return self::convert_12_to_24( $time );
            }
        }

        return $time;
    }

    /**
     * Get time format from settings
     *
     * @return string '12' or '24'
     */
    private static function get_time_format() {
        // Check if SettingsApi class exists
        if ( class_exists( 'GymBuilder\Inc\Api\SettingsApi' ) ) {
            $format = \GymBuilder\Inc\Api\SettingsApi::get_option( 'class_time_format', 'gym_builder_class_settings' );
            return $format == 24 ? '24' : '12';
        }

        // Default to 12-hour format
        return '12';
    }

    /**
     * Convert 24-hour time to 12-hour format
     *
     * @param string $time Time in 24-hour format (e.g., "14:30")
     * @return string Time in 12-hour format (e.g., "2:30pm")
     */
    private static function convert_24_to_12( $time ) {
        if ( empty( $time ) ) {
            return '';
        }

        // Parse the time
        if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $matches ) ) {
            return $time;
        }

        $hours = absint( $matches[1] );
        $minutes = $matches[2];

        // Determine AM or PM
        $period = $hours >= 12 ? 'pm' : 'am';

        // Convert hours
        if ( $hours === 0 ) {
            $hours = 12;
        } elseif ( $hours > 12 ) {
            $hours -= 12;
        }

        return sprintf( '%d:%s%s', $hours, $minutes, $period );
    }

    /**
     * Convert 12-hour time to 24-hour format
     *
     * @param string $time Time in 12-hour format (e.g., "2:30pm")
     * @return string Time in 24-hour format (e.g., "14:30")
     */
    private static function convert_12_to_24( $time ) {
        if ( empty( $time ) ) {
            return '';
        }

        // Parse the time
        $time = strtolower( str_replace( ' ', '', $time ) );

        if ( ! preg_match( '/^(\d{1,2}):(\d{2})(am|pm)$/', $time, $matches ) ) {
            return $time;
        }

        $hours = absint( $matches[1] );
        $minutes = $matches[2];
        $period = $matches[3];

        // Convert to 24-hour
        if ( $period === 'am' ) {
            if ( $hours === 12 ) {
                $hours = 0;
            }
        } else { // pm
            if ( $hours !== 12 ) {
                $hours += 12;
            }
        }

        return sprintf( '%02d:%s', $hours, $minutes );
    }

    /**
     * Get day name from abbreviation
     *
     * @param string $day_abbr Day abbreviation (mon, tue, etc.)
     * @return string Full day name
     */
    public static function get_day_name( $day_abbr ) {
        $days = array(
            'mon' => __( 'Monday', 'gym-builder' ),
            'tue' => __( 'Tuesday', 'gym-builder' ),
            'wed' => __( 'Wednesday', 'gym-builder' ),
            'thu' => __( 'Thursday', 'gym-builder' ),
            'fri' => __( 'Friday', 'gym-builder' ),
            'sat' => __( 'Saturday', 'gym-builder' ),
            'sun' => __( 'Sunday', 'gym-builder' ),
        );

        $day_abbr = strtolower( $day_abbr );

        return isset( $days[ $day_abbr ] ) ? $days[ $day_abbr ] : ucfirst( $day_abbr );
    }

}