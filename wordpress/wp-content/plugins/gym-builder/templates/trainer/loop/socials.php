<?php
/**
 * @package Gym Builder/templates/trainer/loop
 * @var array $trainer_args
 * @var int   $trainer_id
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;

$socials       	= get_post_meta( $trainer_id, 'gym_builder_trainer_socials', true );
$social_fields 	= Functions::trainer_socials();

 if ( !empty( $socials )) { ?>
    <ul class="social-icon">
        <?php foreach ( $socials as $key => $social ): ?>
            <?php if ( !empty( $social ) ): ?>
                <li><a target="_blank" class="<?php echo esc_attr($key); ?>" href="<?php echo esc_url( $social );?>"><i class="gym-builder-icon-<?php echo esc_attr( $social_fields[$key]['icon'] );?>" aria-hidden="true"></i></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
 <?php }