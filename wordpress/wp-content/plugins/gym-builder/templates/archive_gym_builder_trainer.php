<?php
/**
 * @package GymBuilder
 */

use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use \GymBuilder\Inc\Controllers\Query;
use \GymBuilder\Inc\Controllers\Helpers\Helper;
use \GymBuilder\Inc\Controllers\Helpers\Functions;

defined('ABSPATH') || exit;

Functions::get_header($wp_version);

$grid_columns           = SettingsApi::get_option( 'trainer_grid_columns','gym_builder_trainer_settings') ?:'3';
$page_layout            = SettingsApi::get_option('trainer_page_layout','gym_builder_trainer_settings') ?:'full-width';
$archive_page_sidebar   = Functions::is_trainer() ? 'trainer-sidebar':'';
$trainer_layout         = SettingsApi::get_option( 'trainer_archive_style', 'gym_builder_trainer_settings' ) ?: 'layout-1';
$sidebar                = Functions::$trainer_archive_sidebar;

/**
 * Hook: gym_builder_before_main_content_wrapper.
 *
 * @hooked output_main_wrapper_start - 10 (outputs opening divs for the wrapper content)
 */
do_action('gym_builder_before_main_content_wrapper');

/**
 * Hook: gym_builder_before_main_content.
 *
 * @hooked output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action('gym_builder_before_main_content');

?>

<header class="gym-builder-header">
        <?php if (apply_filters('gym_builder_show_page_title', true)) : ?>
            <h2 class="gym-builder-trainer-header-title page-title"><?php Functions::page_title(true,'trainer'); ?></h2>
        <?php endif; ?>
</header>
    <div class="gym-builder-trainer-wrapper">
        <div class="trainer-items-wrapper <?php echo esc_attr($page_layout); ?>">
            <?php if('left-sidebar'==$page_layout && Functions::is_active_sidebar($sidebar)===true){ ?>
                <aside <?php Functions::sidebar_class($archive_page_sidebar); ?>>
                    <?php
                    dynamic_sidebar($sidebar);
                    ?>
                </aside>
            <?php } ?>
            <div class="gym-builder-trainer-items <?php echo esc_attr($trainer_layout); ?>">
                <div class="trainer-items-inner columns-<?php echo esc_attr($grid_columns); ?>">
                    <?php
                    $args['layout'] = $trainer_layout;
                    if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post();
                            $args['id'] = get_the_id();
                            Functions::get_template('content-trainer',$args);
                        }
                    }
                    ?>
                </div>
                <?php
                /**
                 * Hook: gym_builder_loop_item_after_content.
                 *
                 * @hooked gym_builder_class_pagination-10
                 */

                do_action('gym_builder_loop_item_after_content');
                ?>
            </div>
            <?php if('right-sidebar'== $page_layout && Functions::is_active_sidebar($sidebar ) === true){ ?>
                <aside <?php Functions::sidebar_class($archive_page_sidebar); ?>>
                    <?php
                    dynamic_sidebar($sidebar);
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
do_action('gym_builder_after_main_content');

/**
 * Hook: gym_builder_after_main_content_wrapper.
 *
 * @hooked output_main_wrapper_end - 10 (outputs closing divs for the wrapper content)
 */
do_action('gym_builder_after_main_content_wrapper');


Functions::get_footer($wp_version);