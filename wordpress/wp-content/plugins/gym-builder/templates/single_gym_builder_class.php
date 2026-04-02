<?php 
/**
 * @package GymBuilder
 */

use \GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use WpDreamers\GBCBAP\Helper\Fns;

defined('ABSPATH') || exit;
global $post;

Functions::get_header($wp_version);

$page_layout            = SettingsApi::get_option('class_single_page_layout','gym_builder_class_settings') ?:'full-width';
$single_page_sidebar    = Functions::is_class() ? 'class-sidebar':'';
$sidebar                = Functions::$class_single_sidebar;

$class_id        =  get_the_ID();
$thumb_size      ='gym_builder_size1';
$class_schedules =  GymBuilderClass::get_the_schedule($class_id,'');
$weeknames       = GymBuilderClass::get_the_weekname();
/**
 * Hook: gym_builder_before_main_content_wrapper.
 *
 * @hooked output_main_wrapper_start - 10 (outputs opening divs for the content)
 */
do_action('gym_builder_before_main_content_wrapper');

/**
 * Hook: gym_builder_before_main_content.
 *
 * @hooked output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action('gym_builder_before_main_content');

?>
<div class="gym-builder-single-class-wrapper <?php echo esc_attr($page_layout); ?>">
    <?php if('left-sidebar'==$page_layout && Functions::is_active_sidebar($sidebar)===true){ ?>
        <aside <?php Functions::sidebar_class($single_page_sidebar); ?>>
            <?php
            dynamic_sidebar($sidebar);
            ?>
        </aside>
    <?php } ?>
    <div class="post-wrapper">
        <?php
        if ( class_exists( 'WooCommerce' ) ) {
            wc_print_notices();
        }
        ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class( 'class-single' ); ?>>
            <div class="single-class-inner">
                <div class="class-thumb">
                    <?php
                    if ( has_post_thumbnail() ){
                        the_post_thumbnail($thumb_size);
                    } else {
                        echo '<img class="wp-post-image" src="' . Functions::get_img( 'noimage_1210X584.jpg' ) . '" alt="'. the_title_attribute( array( 'echo'=> false ) ) .'">';
                    }
                    ?>
                </div>
                <div class="entry-content">
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                    <?php
                    if (function_exists('gbcbap') &&  Fns::is_wc_product( $class_id )){
                        do_action('gym_builder_class_booking_button',get_the_ID());
                    }else{
                        Functions::get_template( 'class/class-price-info' );
                    }
                    ?>

                    <?php if ( !empty( $class_schedules ) ): ?>
	                    <?php

	                    $weekday_order = array(
		                    'mon' => 1,
		                    'tue' => 2,
		                    'wed' => 3,
		                    'thu' => 4,
		                    'fri' => 5,
		                    'sat' => 6,
		                    'sun' => 7,
	                    );

	                    usort( $class_schedules, function( $a, $b ) use ( $weekday_order ) {
		                    $a_day = isset($weekday_order[$a['week']]) ? $weekday_order[$a['week']] : 99;
		                    $b_day = isset($weekday_order[$b['week']]) ? $weekday_order[$b['week']] : 99;
		                    return $a_day - $b_day;
	                    });
	                    ?>
                        <div class="class-schedule">
                            <h3 class="table-title single-heading"><?php esc_html_e( 'Class Time Table', 'gym-builder' );?></h3>
                            <div class="table-layout table-responsive">
                                <table class="schedule-table">
                                    <tbody>
                                    <tr>
                                        <th><?php esc_html_e( 'Day', 'gym-builder' ); ?></th>
                                        <th><?php esc_html_e( 'Time', 'gym-builder' ); ?></th>
                                        <th><?php esc_html_e( 'Trainer', 'gym-builder' ); ?></th>
                                    </tr>
                                    <?php foreach ( $class_schedules as $class_schedule ): ?>
                                        <?php
                                        if ( !isset( $weeknames[$class_schedule['week']] ) || !$class_schedule['start_time'] ) continue;

                                        $gym_builder_trainer = false;

                                        if ( !empty( $class_schedule['trainer'] ) && get_post_status( $class_schedule['trainer'] ) ) {
                                            $gym_builder_trainer = true;
                                            $gym_builder_trainer = get_the_title( $class_schedule['trainer'] );
                                            $gym_builder_trainer_link = get_permalink( $class_schedule['trainer'] );
                                        }
                                        $class_schedule_time = GymBuilderClass::get_schedule_time($class_schedule['start_time'],$class_schedule['end_time']);
                                        ?>
                                        <tr>
                                            <td>
                                                <?php if($weeknames[$class_schedule['week']]){ ?>
                                                    <?php echo esc_html( $weeknames[$class_schedule['week']] ); ?>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php  if($class_schedule_time['start_time'] || $class_schedule_time['end_time']){
                                                    $start_time = $class_schedule_time['start_time'] ?:'';
                                                    $end_time   = $class_schedule_time['end_time'] ? " - {$class_schedule_time['end_time']}":'';
                                                    ?>
                                                    <?php echo esc_html( $start_time ); ?> <?php echo esc_html($end_time); ?>
                                                <?php } ?>
                                                <?php do_action('gym_builder_class_type',$class_schedule); ?>
                                            </td>
                                            <td>
                                                <?php  if(!empty($gym_builder_trainer)){ ?>
                                                    <a href="<?php echo esc_url($gym_builder_trainer_link); ?>"><?php echo esc_html($gym_builder_trainer); ?></a>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    <?php  endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    /**
                     * Hook: gym_builder_after_class_single_content.
                     *
                     * @param int $class_id Current class post ID.
                     */
                    do_action( 'gym_builder_after_class_single_content', $class_id );
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php if('right-sidebar'==$page_layout && Functions::is_active_sidebar($sidebar)===true){ ?>
        <aside <?php Functions::sidebar_class($single_page_sidebar); ?>>
            <?php
            dynamic_sidebar($sidebar);
            ?>
        </aside>
    <?php } ?>
</div>

<?php
/**
 * Hook: gym_builder_after_main_content.
 *
 * @hooked output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('gym_builder_after_main_content');
 
/**
 * Hook: gym_builder_after_main_content_wrapper.
 *
 * @hooked output_main_wrapper_end - 10 (outputs closing divs for the wrapper content)
 */
do_action('gym_builder_after_main_content_wrapper');

Functions::get_footer($wp_version);

