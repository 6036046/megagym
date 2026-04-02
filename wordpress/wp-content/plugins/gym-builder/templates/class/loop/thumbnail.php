<?php
/**
 * @package Gym Builder/templates/class/loop
 * @var array $args
 * @var int   $class_id
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;

?>
<div class="class-thumb-wrapper">
    <div class="class-thumb">
        <?php do_action('gym_builder_class_type_badge',$class_id); ?>
        <?php if (has_post_thumbnail($class_id)){ ?>
            <a href="<?php echo esc_url(get_the_permalink($class_id)); ?>" class="gym-builder-media">
                <?php echo wp_kses_post(get_the_post_thumbnail($class_id, 'class_thumb_size')); ?>
            </a>
        <?php } else {
            echo '<a href="'. esc_url(get_the_permalink($class_id)) .'"><img class="gym-builder-media" src="' . Functions::get_img( 'noimage_570x400.jpg' ) . '" alt="'. the_title_attribute( array( 'echo'=> false ) ) .'"/></a>';
         } ?>
    </div>
</div>