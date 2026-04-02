<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Shortcode;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox\ClassShortcodeMeta;
use GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox\TrainerShortcodeMeta;
use GymBuilder\Inc\Controllers\Admin\Shortcode\Metabox\FitnessCalculatorShortcodeMeta;

class AdminInit{
	use Constants;
	public static function init()
	{
		//class shortcode
		add_action( 'edit_form_after_title', [ __CLASS__, 'after_title_text' ] );
		add_filter('manage_edit-gb_class_shortcode_columns' ,[__CLASS__,'gb_class_shortcode_columns']);
		add_action( 'manage_gb_class_shortcode_posts_custom_column', [ __CLASS__, 'gb_class_shortcode_posts_custom_column' ], 10, 2 );
		ClassShortcodeMeta::init();

		//trainer shortcode
		add_action( 'edit_form_after_title', [ __CLASS__, 'trainer_after_title_text' ] );
		add_filter('manage_edit-gb_trainer_shortcode_columns' ,[__CLASS__,'gb_trainer_shortcode_columns']);
		add_action( 'manage_gb_trainer_shortcode_posts_custom_column', [ __CLASS__, 'gb_trainer_shortcode_posts_custom_column' ], 10, 2 );
		TrainerShortcodeMeta::init();

		//fitness calculator shortcode
		add_action( 'edit_form_after_title', [ __CLASS__, 'fitness_calc_after_title_text' ] );
		add_filter('manage_edit-gb_fitness_shortcode_columns' ,[__CLASS__,'fitness_calc_shortcode_columns']);
		add_action( 'manage_gb_fitness_shortcode_posts_custom_column', [ __CLASS__, 'gb_fitness_calc_shortcode_posts_custom_column' ], 10, 2 );
		add_action('restrict_manage_posts',[__CLASS__,'gb_fitness_shortcode_filter_post']);
		add_action('pre_get_posts',[__CLASS__,'gb_fitness_shortcode_filter_post_data']);
		FitnessCalculatorShortcodeMeta::init();

	}
	public static function gb_class_shortcode_columns($columns) {
		$shortcode = [ 'gb_class_shortcode' => esc_html__( 'Shortcode', 'gym-builder' ) ];

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}

	public static function gb_trainer_shortcode_columns($columns) {
		$shortcode = [ 'gb_trainer_shortcode' => esc_html__( 'Shortcode', 'gym-builder' ) ];

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}
	public static function fitness_calc_shortcode_columns($columns) {
		$shortcode = [ 'gb_fitness_shortcode' => esc_html__( 'Shortcode', 'gym-builder' ) ];

		return array_slice( $columns, 0, 2, true ) + $shortcode + array_slice( $columns, 1, null, true );
	}

	public static function gb_class_shortcode_posts_custom_column( $column ) {

		switch ( $column ) {
			case 'gb_class_shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[gbclass id=&quot;' . esc_attr(get_the_ID()) . '&quot; title=&quot;' . esc_html( get_the_title() ) . '&quot;]" class="large-text code gym-builder-code-sc">';
				break;
			default:
				break;
		}

	}
	public static function gb_trainer_shortcode_posts_custom_column( $column ) {

		switch ( $column ) {
			case 'gb_trainer_shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[gbtrainer id=&quot;' . esc_attr(get_the_ID()) . '&quot; title=&quot;' . esc_html( get_the_title() ) . '&quot;]" class="large-text code gym-builder-code-sc">';
				break;
			default:
				break;
		}

	}
	public static function gb_fitness_calc_shortcode_posts_custom_column( $column ) {

		switch ( $column ) {
			case 'gb_fitness_shortcode':
				echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[gbfitness_calculator id=&quot;' . esc_attr(get_the_ID()) . '&quot; title=&quot;' . esc_html( get_the_title() ) . '&quot;]" class="large-text code gym-builder-code-sc">';
				break;
			default:
				break;
		}

	}
	public static function after_title_text( $post ) {

		if ( self::$class_shortcode_post_type !== $post->post_type ) {
			return;
		}

		$html  = null;
		$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[gbclass id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code gym-builder-code-sc">
		<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[gbclass id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code gym-builder-code-sc">
		</p>';
		$html .= '</div></div>';

		Functions::print_html($html,true);
	}
	public static function trainer_after_title_text( $post ) {

		if ( self::$trainer_shortcode_post_type !== $post->post_type ) {
			return;
		}

		$html  = null;
		$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[gbtrainer id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code gym-builder-code-sc">
		<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[gbtrainer id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code gym-builder-code-sc">
		</p>';
		$html .= '</div></div>';

		Functions::print_html($html,true);
	}
	public static function fitness_calc_after_title_text( $post ) {

		if ( self::$fitness_calc_shortcode_post_type !== $post->post_type ) {
			return;
		}

		$html  = null;
		$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
		$html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[gbfitness_calculator id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code gym-builder-code-sc">
		<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[gbfitness_calculator id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code gym-builder-code-sc">
		</p>';
		$html .= '</div></div>';

		Functions::print_html($html,true);
	}

	public static function gb_fitness_shortcode_filter_post(  ) {
		global $typenow;

		if ( self::$membership_package_post_type != $typenow ) {
			return;
		}
		$filter_value= isset($_GET['package_types']) ? $_GET['package_types']:'';

		$values= Functions::get_membership_package_categories();
		?>
		<select name=package_types>
            <option value="0" <?php selected( 'all', $filter_value ); ?>><?php esc_html_e( 'All Package Type ', 'gym-builder' ); ?></option>
			<?php foreach($values as $key=> $value){ ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $filter_value ); ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		</select>
		<?php
	}

	public static function gb_fitness_shortcode_filter_post_data( $wpquery ) {
		if(!is_admin() || ! self::$membership_package_post_type === $wpquery->get( 'post_type' )){
			return;
		}

		$filter_value = isset( $_GET['package_types'] ) ? sanitize_key($_GET['package_types']) : '';
		if ( !empty($filter_value) && $filter_value != 0){
			$tax_query = array(
				array(
					'taxonomy' => self::$membership_package_taxonomy,
					'field'    => 'term_id',
					'terms'    => [$filter_value],
					'operator' => 'IN',
				)
			);
			$wpquery->set( 'tax_query',$tax_query);
        }
    }
}

