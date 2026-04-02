<?php
/**
 * @package GymBuilder
 */

use GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use \GymBuilder\Inc\Controllers\Query;
use \GymBuilder\Inc\Controllers\Helpers\Helper;
use \GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;

defined( 'ABSPATH' ) || exit;


Functions::get_header( $wp_version );

$grid_columns         = SettingsApi::get_option( 'class_grid_columns', 'gym_builder_class_settings' ) ?: '3';
$page_layout          = SettingsApi::get_option( 'class_page_layout', 'gym_builder_class_settings' ) ?: 'full-width';
$archive_page_sidebar = Functions::is_class() ? 'class-sidebar' : '';
$sidebar              = Functions::$class_archive_sidebar;
$class_layout         = SettingsApi::get_option( 'class_archive_style', 'gym_builder_class_settings' ) ?: 'layout-1';
$is_slider            = Helper::is_slider_layout( 'class_archive_style', $class_layout ) ?? false;
$class_type_badge     = 'has-class-type-badge';
/**
 * Hook: gym_builder_before_main_content_wrapper.
 *
 * @hooked output_main_wrapper_start - 10 (outputs opening divs for the wrapper content)
 */
do_action( 'gym_builder_before_main_content_wrapper' );

/**
 * Hook: gym_builder_before_main_content.
 *
 * @hooked output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action( 'gym_builder_before_main_content' );

?>
    <header class="gym-builder-header">
		<?php if ( apply_filters( 'gym_builder_show_page_title', true ) ) : ?>
            <h2 class="gym-builder-class-header-title page-title"><?php Functions::page_title( true, 'class' ); ?></h2>
		<?php endif; ?>
    </header>
    <div class="gym-builder-class-wrapper">
        <div class="class-items-wrapper <?php echo esc_attr( $page_layout." ".$class_type_badge ); ?>">
			<?php if ( 'left-sidebar' == $page_layout && Functions::is_active_sidebar( $sidebar ) === true ) { ?>
                <aside <?php Functions::sidebar_class( $archive_page_sidebar ); ?>>
					<?php
					dynamic_sidebar( $sidebar );
					?>
                </aside>
			<?php } ?>
            <div class="gym-builder-class-items <?php echo esc_attr($class_layout); ?>">
                <?php
                   if ($is_slider){
                       printf('<div class="%s swiper" data-slider="%s">',esc_attr('gym-builder-global-slider'),esc_attr(json_encode(GymBuilderClass::get_slider_settings())));
                   }
                ?>

                <?php
                    $grid_columns_class = ! $is_slider ? 'columns-'.esc_attr( $grid_columns ):esc_attr('swiper-wrapper');
                    printf('<div class="class-items-inner %s">',$grid_columns_class);

                        $args['layout'] = $class_layout;
                        $args['is_slider_layout'] = $is_slider;
                        if ( have_posts() ) {
                            while ( have_posts() ) {
                                the_post();
                                $args['id'] = get_the_id();
                                Functions::get_template( 'content-class', $args );
                            }
                        }
                    Functions::print_html('</div>');
                ?>
                <?php
                    if ($is_slider){
	                    printf('<div class="%s"></div>',esc_attr("swiper-pagination"));
                        do_action('class_slider_navigation');
                        Functions::print_html('</div>');
                    }
                ?>
				<?php
				/**
				 * Hook: gym_builder_loop_item_after_content.
				 *
				 * @hooked gym_builder_class_pagination-10
				 */

				do_action( 'gym_builder_loop_item_after_content' );
				?>
            </div>
			<?php if ( 'right-sidebar' == $page_layout && Functions::is_active_sidebar( $sidebar ) === true ) { ?>
                <aside <?php Functions::sidebar_class( $archive_page_sidebar ); ?>>
					<?php
					dynamic_sidebar( $sidebar );
					?>
                </aside>
			<?php } ?>
        </div>

    </div>
<?php

/**
 * Hook: gym_builder_after_main_content.
 *
 * @hooked output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'gym_builder_after_main_content' );

/**
 * Hook: gym_builder_after_main_content_wrapper.
 *
 * @hooked output_main_wrapper_end - 10 (outputs closing divs for the wrapper content)
 */
do_action( 'gym_builder_after_main_content_wrapper' );


Functions::get_footer( $wp_version );
