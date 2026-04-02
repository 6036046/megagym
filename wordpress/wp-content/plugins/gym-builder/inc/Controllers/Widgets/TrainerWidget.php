<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Widgets;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Models\GymBuilderTrainer;
use GymBuilder\Inc\Traits\Constants;
use \WP_Widget;
use GymBuilder\Inc\Controllers\Admin\Models\Widgets\WidgetFields;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class TrainerWidget extends WP_Widget {
	use Constants;

	public function __construct() {
		$id = 'gym_builder_trainer_widget';
		parent::__construct(
			$id,
			esc_html__( 'Gym Builder: Trainer', 'gym-builder' ),
			[
				'description'                 => esc_html__( 'Gym Builder trainer widget.It should be used only trainer sidebar', 'gym-builder' ),
				'customize_selective_refresh' => true,
			] );
	}

	public function widget( $args, $instance ) {

		if ( is_singular( self::$trainer_post_type ) ) {
			$post_id      = get_the_id();
			$current_post = array( $post_id );
		} else {
			$current_post = array();
			$post_id      = '';
		}
		$title     = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Classes', 'gym-builder' );

		$posts_per_page = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 6;
		$order_by       = ( ! empty( $instance['order_by'] ) ) ? wp_kses_post( $instance['order_by'] ) : '';
		$order          = ( ! empty( $instance['order'] ) ) ? wp_kses_post( $instance['order'] ) : 'ASC';

		$query_args = [
			'post_type'           => self::$trainer_post_type,
			'posts_per_page'      => $posts_per_page,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		];
		if ( ! empty( $current_post ) ) {
			$query_args['post__not_in'] = $current_post;
		}
		if ( ! empty( $order_by ) ) {
			$query_args['orderby'] = $order_by;
		}
		if ( ! empty( $order ) ) {
			$query_args['order'] = $order;
		}
		$query = new \WP_Query( $query_args );
		$temp  = Helper::wp_set_temp_query( $query );
		if ( $query->have_posts() ) {
			echo wp_kses_post( $args['before_widget'] );
			if ( $title ) {
				echo wp_kses_post( $args['before_title'] ) . $title . wp_kses_post( $args['after_title'] );
			}
			echo '<div class="gym-builder-trainer-wrapper">';
			while ( $query->have_posts() ) {
				$query->the_post();
				$trainer_id  = get_the_ID();

				?>
                <div class="trainer-item">
                    <div class="thumb">
						<?php
						if ( has_post_thumbnail() ) {
							?>
                            <a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
                            </a>
						<?php } else {
							echo '<a href="' . get_the_permalink() . '"><img class="gym-builder-media" src="' . Functions::get_img( 'noimage_150x150.jpg' ) . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '"/></a>';
						}
						?>
                    </div>
                    <div class="trainer-content">
                        <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <div class="trainer-design"><?php GymBuilderTrainer::the_trainer_designation( $trainer_id);?></div>
                    </div>
                </div>
				<?php
			}
			echo '</div>';
			echo wp_kses_post( $args['after_widget'] );
		}
		Helper::wp_reset_temp_query( $temp );

	}

	public function update( $new_instance, $old_instance ) {
		$instance                   = [];
		$instance['title']          = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['number']         = ( ! empty( $new_instance['number'] ) ) ? wp_kses_post( $new_instance['number'] ) : 6;
		$instance['order_by']       = ( ! empty( $new_instance['order_by'] ) ) ? wp_kses_post( $new_instance['order_by'] ) : 'none';
		$instance['order']          = ( ! empty( $new_instance['order'] ) ) ? wp_kses_post( $new_instance['order'] ) : 'ASC';

		return $instance;
	}

	public function form( $instance ) {
		$defaults = [
			'title'          => '',
			'number'         => 6,
			'order_by'       => 'none',
			'order'          => 'ASC',
			'schedule_limit' => 1,
		];
		$instance = wp_parse_args( (array) $instance, $defaults );

		$fields = [
			'title'          => [
				'label' => esc_html__( 'Title', 'gym-builder' ),
				'type'  => 'text',
			],
			'number'         => [
				'label' => esc_html__( 'Number of posts to show', 'gym-builder' ),
				'type'  => 'number',
			],
			'order_by'       => [
				'label'   => esc_html__( 'Order By', 'gym-builder' ),
				'type'    => 'select',
				'options' => Helper::orderbyQueryOptions()
			],
			'order'          => [
				'label'   => esc_html__( 'Order', 'gym-builder' ),
				'type'    => 'select',
				'options' => array(
					'ASC'  => 'Ascending',
					'DESC' => 'Descending',
				),
			],
		];

		WidgetFields::display( $fields, $instance, $this );
	}
}