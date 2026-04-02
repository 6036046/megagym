<?php
/**
 * @package Gym Builder/templates/trainer/loop
 * @var array $args
 * @var int   $trainer_id
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;

?>

<div class="trainer-thumb">
    <?php if (has_post_thumbnail($trainer_id)){ ?>
    <a href="<?php echo esc_url(get_the_permalink($trainer_id)); ?>" class="gym-builder-media">
        <?php echo wp_kses_post(get_the_post_thumbnail($trainer_id, 'trainer_thumb_size')); ?>
    </a>
    <?php } else{
        echo '<a href="'. esc_url(get_the_permalink($trainer_id)) .'"><img class="gym-builder-media" src="' . Functions::get_img( 'noimage_570x400.jpg' ) . '" alt="'. the_title_attribute( array( 'echo'=> false ) ) .'"/></a>';
    } ?>
</div>
