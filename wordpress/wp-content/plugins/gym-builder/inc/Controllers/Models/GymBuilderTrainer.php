<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Models;

use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Traits\Constants;

class GymBuilderTrainer{

    use Constants;
    public static function the_title($trainer_id)
    {
        echo esc_html(get_the_title($trainer_id));
    }

    public static function get_the_title($trainer_id)
    {
        return get_the_title($trainer_id);
    }
    public static function get_the_content( $trainer_id ) {
		return get_the_content( $trainer_id );
	}
    public static function the_trainer_designation($trainer_id)
    {
        echo esc_html(get_post_meta($trainer_id,'gym_builder_trainer_designation',true));
    }

    public static function get_the_trainer_designation($trainer_id)
    {
        return get_post_meta($trainer_id,'gym_builder_trainer_designation',true);
    }

    public static function get_category_html_format( $trainer_id ) {
		$term_lists = get_the_terms( $trainer_id, self::$trainer_taxonomy );
		$i          = 1;
		if ( $term_lists ) {
			?>
            <div class="trainer-category">
				<?php
				foreach ( $term_lists as $term_list ) {
					$link = get_term_link( $term_list->term_id, self::$trainer_taxonomy ); ?>
					<?php if ( $i > 1 ) {
						echo esc_html( ', ' );
					} ?>
                    <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term_list->name ); ?></a>
					<?php $i ++;
				} ?>
            </div>
		<?php }
	}

    public static function the_trainer_socials($trainer_id)
    {
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
    }

    public static function get_trainer_fields($trainer_id){
        $socials       	= get_post_meta( $trainer_id, 'gym_builder_trainer_socials', true );
        return Functions::trainer_socials();
    }

	public static function get_trainers()
	{
		return get_posts(
			[
				'post_type'     => self::$trainer_post_type,
				'post_status'   =>'publish',
                'posts_per_page' => -1
			]
		);
	}
	public static function get_categories_array(  ) {
		$categories_list = [];
		$terms = get_terms([
			'taxonomy' => self::$trainer_taxonomy,
			'hide_empty' => false
		]);
        if (!empty($terms)){
	        foreach ($terms as $term){
		        $categories_list[$term->term_id] = $term->name;
	        }
        }

		return apply_filters("gym_builder_array_trainer_category_list",$categories_list);
	}
}