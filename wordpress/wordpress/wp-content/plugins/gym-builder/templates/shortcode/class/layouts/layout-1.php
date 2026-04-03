<?php
/**
 * @package GymBuilder
 * @var array $args
 * @var int   $class_id
 * @var string $time_format
 * @var array $custom_image_size
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Models\GymBuilderClass;


defined( 'ABSPATH' ) || exit;

$weeknames = GymBuilderClass::get_the_weekname();
$schedules = GymBuilderClass::get_the_schedule( $class_id );
$content = GymBuilderClass::get_the_content( $class_id );
$content = wp_trim_words( $content, 20, '..' );

?>
<div class="gb-shortcode-class-item">
    <div class="class-thumb">
	    <?php do_action('gym_builder_class_type_badge',$class_id); ?>
		<?php if (has_post_thumbnail($class_id)){ ?>
            <a href="<?php echo esc_url(get_the_permalink($class_id)); ?>" class="gym-builder-media">
				<?php
                $img = Helper::getFeatureImage($class_id,'gym_builder_custom',$custom_image_size);
                Functions::print_html($img);
                ?>
            </a>
		<?php } else {
			echo '<a href="'. esc_url(get_the_permalink($class_id)) .'"><img class="gym-builder-media" src="' . Functions::get_img( 'noimage_570x400.jpg' ) . '" alt="'. the_title_attribute( array( 'echo'=> false ) ) .'"/></a>';
		} ?>
    </div>
    <div class="class-content">
	    <?php GymBuilderClass::get_category_html_format( $class_id ); ?>
        <h3 class="gym-builder-class-title"><a href="<?php echo esc_url(get_the_permalink($class_id)); ?>"><?php the_title(); ?></a></h3>
        <?php if (get_the_content()){
            ?>
            <p><?php echo wp_kses_post($content); ?></p>
        <?php }
        GymBuilderClass::get_schedule_meta( $schedules, $weeknames, true, true,true,$time_format );
        GymBuilderClass::get_button_html_format( $class_id );
        ?>
    </div>
</div>
