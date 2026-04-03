<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Hooks;

use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use \GymBuilder\Inc\Traits\Constants;
use \GymBuilder\Inc\Controllers\Pagination;
use \GymBuilder\Inc\Controllers\Helpers\Helper;
use \GymBuilder\Inc\Controllers\Helpers\Functions;
use \GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use \GymBuilder\Inc\Controllers\Models\GymBuilderTrainer;

class TemplateHooks {
    use Constants;
    public static function init() {
        
        add_filter( 'body_class', [__CLASS__, 'body_class'] );
        add_action( 'gym_builder_before_main_content_wrapper', [__CLASS__, 'output_main_wrapper_start'], 8 );
        add_action( 'gym_builder_before_main_content', [__CLASS__, 'output_content_wrapper'], 10 );
        add_action( 'gym_builder_after_main_content', [__CLASS__, 'output_content_wrapper_end'], 10 );
        add_action( 'gym_builder_after_main_content_wrapper', [__CLASS__, 'output_main_wrapper_end'], 12 );

        //class thumbnail hook

        add_action( 'gym_builder_class_loop_item_start', [__CLASS__, 'class_thumbnail'], 10 );

        //trainer thumbnail hook

        add_action( 'gym_builder_trainer_loop_item_start', [__CLASS__, 'trainer_thumbnail'], 10 );

        //class loop item content hook

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_wrapper_start'], 10 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_title'], 20 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_description'], 30 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_meta'], 40 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_footer_wrapper_start'], 50 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_category'], 60 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_footer_button'], 70 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_footer_wrapper_end'], 80 );

        add_action( 'gym_builder_class_loop_item', [__CLASS__, 'class_loop_item_wrapper_end'], 100 );

        add_action('gym_builder_loop_item_after_content',[__CLASS__,'gym_builder_pagination'],10);

        add_action('gym_builder_class_icon',[__CLASS__,'gym_builder_class_icon']);

        //trainer loop item content hook

	    $trainer_layout         = SettingsApi::get_option( 'trainer_archive_style', 'gym_builder_trainer_settings' ) ?: 'layout-1';

        add_action('gym_builder_trainer_loop_item',[__CLASS__,'trainer_loop_item_wrapper_start'],10);

        add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_title'], 20 );

        add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_designation'], 30 );

        if ('layout-2' !== $trainer_layout){
	        add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_social'], 40 );
        }
        add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_description'], 50 );
	    if ('layout-2' === $trainer_layout){
		    add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_social'], 60 );
	    }
        add_action( 'gym_builder_trainer_loop_item', [__CLASS__, 'trainer_loop_item_wrapper_end'], 100 );
    }

    public static function output_main_wrapper_start()
    {
        Functions::print_html( '<div class="gym-builder-wrapper">' );
    }

    public static function output_main_wrapper_end()
    {
        Functions::print_html( '</div>' );
    }

    public static function output_content_wrapper()
    {
        Functions::get_template( 'global/wrapper-start' );
    }

    public static function output_content_wrapper_end()
    {
        Functions::get_template( 'global/wrapper-end' );
    }

    public static function body_class( $classes )
    {

        $classes = (array) $classes;

        if ( Functions::is_class() || Functions::is_trainer()) {
            $classes[] = 'gym-builder';
            $classes[] = 'gym-builder-page';
        }
        if (Functions::is_single_class() ){
            $classes[]='gym-builder-single-class';
        }
        if (Functions::is_single_trainer()){
            $classes[]='gym-builder-single-trainer';
        }
        if ( Functions::is_classes() || Functions::is_trainers()) {
            $classes[] = 'gym-builder-archive';
        }
        if ( Functions::is_blog_theme() === true ) {
            $classes[] = 'block-theme';
        }

        return $classes;

    }

    public static function class_thumbnail( $class_id )
    {
        $class_args['class_id'] = $class_id;
        Functions::get_template( 'class/loop/thumbnail', $class_args );
    }

    public static function class_loop_item_wrapper_start()
    {
        echo apply_filters( 'gym_builder_loop_item_wrapper_start', '<div class="class-content">');
    }

    public static function class_loop_item_wrapper_end()
    {
        Functions::print_html( '</div>' );
    }

    public static function class_loop_item_title( $class_id )
    {
        ?>
		<h3 class="gym-builder-class-title"><a href="<?php echo esc_url(get_the_permalink($class_id)); ?>"><?php GymBuilderClass::the_title( $class_id );?></a></h3>
		<?php
    }

	public static function gym_builder_class_icon( $class_id ) {
        $icon = get_post_meta($class_id,'gym_builder_class_icon',true);
        if (empty($icon)){
            return;
        }
        ?>
        <div class="gym-builder-class-icon">
            <i class="<?php echo esc_attr($icon);?>"></i>
        </div>
        <?php
    }

    public static function class_loop_item_description( $class_id )
    {
        $content = GymBuilderClass::get_the_content( $class_id );
        $content = wp_trim_words( $content, 20, '..' );
        ?>
		<p class="gym-builder-class-des"><?php echo wp_kses_post( $content ); ?></p>
		<?php
    }

    public static function class_loop_item_meta( $class_id )
    {
        $class_args['class_id'] = $class_id;
        $class_args['trainer_post_type'] = self::$trainer_post_type;
        Functions::get_template( 'class/loop/meta', $class_args );
    }
    public static function class_loop_item_footer_wrapper_start()
    {
        echo apply_filters( 'gym_builder_loop_item_footer_wrapper_end', sprintf( '<div class="class-footer">' ) );
    }

    public static function class_loop_item_footer_wrapper_end()
    {
        Functions::print_html( '</div>' );
    }
    public static function class_loop_item_category($class_id)
    {
        $term_lists = Helper::get_the_terms($class_id,self::$class_taxonomy);
        $i = 1;
        if( $term_lists ){
        ?>
            <div class="class-category">
                <?php 
                    foreach( $term_lists as $term_list) {
                        $link = get_term_link( $term_list->term_id, self::$class_taxonomy ); ?>
                        <?php if ( $i > 1 ){ echo esc_html( ', ' ); } ?>
                        <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term_list->name ); ?></a>
                    <?php $i++; } ?>
            </div>
        <?php }
    }
    public static function class_loop_item_footer_button($class_id) {
        $class_args['class_id'] = $class_id;
        Functions::get_template( 'class/loop/button', $class_args );
    }
    public static function gym_builder_pagination()
    {
        Pagination::pagination();
    }

    public static function trainer_loop_item_wrapper_start() {
        echo apply_filters( 'gym_builder_trainer_loop_item_wrapper_start', '<div class="trainer-content">');
    }
    public static function trainer_thumbnail($trainer_id) {
        $trainer_args['trainer_id'] = $trainer_id;
        Functions::get_template( 'trainer/loop/thumbnail', $trainer_args );
    }
    public static function trainer_loop_item_title( $trainer_id ) {
        ?>
        <h3 class="gym-builder-trainer-title"><a href="<?php echo esc_url(get_the_permalink($trainer_id)); ?>"><?php GymBuilderTrainer::the_title( $trainer_id );?></a></h3>
        <?php
    }
    public static function trainer_loop_item_wrapper_end() {
        Functions::print_html( '</div>' );
    }
    public static function trainer_loop_item_designation($trainer_id) {
        ?>
        <div class="trainer-designation"><?php GymBuilderTrainer::the_trainer_designation( $trainer_id);?></div>
        <?php
    }
    public static function trainer_loop_item_description( $trainer_id ) {
        $content = GymBuilderClass::get_the_content( $trainer_id );
        $content = wp_trim_words( $content, 20, '..' );
        ?>
        <p class="trainer-description"><?php echo wp_kses_post( $content ); ?></p>
        <?php
    }

    public static function trainer_loop_item_social($trainer_id) {
        $trainer_args['trainer_id'] = $trainer_id;
        Functions::get_template( 'trainer/loop/socials', $trainer_args);
    }
}
