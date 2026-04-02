<?php
/**
 * @package GymBuilder
 * @var array $args
 * @var int   $trainer_id
 * @var array $custom_image_size
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Models\GymBuilderTrainer;


defined( 'ABSPATH' ) || exit;


$content = GymBuilderTrainer::get_the_content( $trainer_id );
$content = wp_trim_words( $content, 20, '..' );

?>
<div class="gb-shortcode-trainer-item">
    <div class="trainer-thumb">
		<?php if (has_post_thumbnail()){ ?>
            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="gym-builder-media">
				<?php
                $img = Helper::getFeatureImage($trainer_id,'gym_builder_custom',$custom_image_size);
                Functions::print_html($img);
                ?>
            </a>
		<?php } else {
			echo '<a href="'. esc_url(get_the_permalink()) .'"><img class="gym-builder-media" src="' . Functions::get_img( 'noimage_570x400.jpg' ) . '" alt="'. the_title_attribute( array( 'echo'=> false ) ) .'"/></a>';
		} ?>
    </div>
    <div class="trainer-content">
        <?php GymBuilderTrainer::get_category_html_format($trainer_id); ?>
        <h3 class="gym-builder-trainer-title"><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a></h3>
        <?php do_action( 'gym_builder_shortcode_trainer_after_title', $trainer_id ); ?>
        <?php if(GymBuilderTrainer::get_the_trainer_designation($trainer_id)){?>
            <div class="designation"><?php GymBuilderTrainer::the_trainer_designation($trainer_id); ?></div>
        <?php } ?>
        <?php if (get_the_content()){
            ?>
            <p><?php echo wp_kses_post($content); ?></p>
        <?php }
        GymBuilderTrainer::the_trainer_socials($trainer_id);
        ?>
       
    </div>
</div>
