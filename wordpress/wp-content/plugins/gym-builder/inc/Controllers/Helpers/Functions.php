<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Helpers;


use GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use GymBuilder\Inc\Controllers\Models\GymBuilderTrainer;
use \GymBuilder\Inc\Traits\Constants;
use \GymBuilder\Inc\Traits\TemplateTrait;
use \GymBuilder\Inc\Traits\FileLocations;
use \GymBuilder\Inc\Controllers\Admin\AddConfig;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
use GymBuilder\Inc\Traits\UtilityTrait;
use ParagonIE\Sodium\File;

class Functions{

	use FileLocations;
	use TemplateTrait;
	use Constants;
	use UtilityTrait;

    public static function get_page_id( $page ) {

		$page_id          = 0;
		$settings_page_id =SettingsApi::get_option( $page, 'gym_builder_page_settings'); 

		if ( $settings_page_id && get_post( $settings_page_id ) ) {
			$page_id = $settings_page_id;
		}

		return $page_id; 

	}
    public static function insert_custom_pages(){

		// Vars
		$page_settings    = self::get_page_ids();
		$page_definitions = AddConfig::get_custom_page_list();

		$pages = [];
		foreach ( $page_definitions as $slug => $page ) {
			$id = 0;
			if ( array_key_exists( $slug, $page_settings ) ) {
				$id = (int) $page_settings[ $slug ];
			}
			if ( ! $id ) { 
				$id = wp_insert_post(
					[
						'post_title'     => $page['title'],
						'post_content'   => $page['content'],
						'post_status'    => 'publish',
						'post_author'    => 1,
						'post_type'      => 'page',
						'comment_status' => 'closed'
					]
				);
			}
			$pages[ $slug ] = $id;
		}

		return array_merge($pages,self::insert_member_auth_page());
	}
    public static function insert_member_auth_page(){
        // Vars
        $page_settings    = self::get_member_auth_page_ids();
        $page_definitions = AddConfig::member_auth_page_list();

        $pages = [];
        foreach ( $page_definitions as $slug => $page ) {
            $id = 0;
            if ( array_key_exists( $slug, $page_settings ) ) {
                $id = (int) $page_settings[ $slug ];
            }
            if ( ! $id ) {
                $id = wp_insert_post(
                        [
                                'post_title'     => $page['title'],
                                'post_content'   => $page['content'],
                                'post_status'    => 'publish',
                                'post_author'    => 1,
                                'post_type'      => 'page',
                                'comment_status' => 'closed'
                        ]
                );
            }
            $pages[ $slug ] = $id;
        }

        return $pages;
    }
    public static function get_page_ids(){
		$pages    = AddConfig::get_custom_page_list();
		$page_ids = [];
		foreach ( $pages as $page_key => $page_title ) {
			if ( $id = self::get_page_id( $page_key ) ) {
				$page_ids[ $page_key ] = $id;
			}
		}

		return $page_ids;
	}
    public static function get_member_auth_page_ids(){
        $pages    = AddConfig::member_auth_page_list();
        $page_ids = [];
        foreach ( $pages as $page_key => $page_title ) {
            if ( $id = self::get_page_id( $page_key ) ) {
                $page_ids[ $page_key ] = $id;
            }
        }

        return $page_ids;
    }
    
	public static function get_pages(){
		$page_list = [];
		$pages     = get_pages(
			[
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'hierarchical' => 0,
			]
		);
		foreach ( $pages as $page ) {
			$page_list[ $page->ID ] = ! empty( $page->post_title ) ? $page->post_title : '#' . $page->ID;
		}

		return $page_list;
	}

	public static function get_trainers(){
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => 'gym_builder_trainer',
		);
		$posts = get_posts( $args );
		$trainers[0] = esc_html__( 'Select a Trainer', 'gym-builder' );
		foreach ( $posts as $post ) {
			$trainers[$post->ID] = $post->post_title;
		}
		return $trainers;
	}
	public static function get_membership_package_categories(  ) {
		$package_list = [];
		$terms           = get_terms( [
			'taxonomy'   => self::$membership_package_taxonomy,
			'hide_empty' => false
		] );
		if ($terms){
			foreach ( $terms as $term ) {
				$package_list[$term->term_id] = $term->name;
			}
		}
		return $package_list;
	}
	public static function get_membership_packages_with_price(  ) {
		$package_list=[];
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => self::$membership_package_post_type,
		);
		$posts = get_posts( $args );
		if ($posts){
			foreach ( $posts as $post ) {
                $package_price = get_post_meta($post->ID,'gym_builder_package_price',true) ?? '0';
				$package_list[$post->ID] = $post->post_title." - ".$package_price;
			}
        }
		return $package_list;
	}

	public static function classesOptionsValue() {
		$options = [];

		$classes = GymBuilderClass::get_classes();

		foreach ( $classes as $class ) {
			$options[ $class->ID ] = $class->post_title;
		}

		return $options;
	}

	public static function class_page_layout() {
		$layout = [
			'layout-1' => [
				'title'      => 'Layout 1',
				'img_source' => 'layout-1'
			],
			'layout-2' => [
				'title'      => 'Slider Layout',
				'img_source' => 'layout-2'
			]
		];

		return apply_filters( 'gym_builder_class_page_layout', $layout );
	}
	public static function trainersOptionsValue()
	{
		$options=[];

		$trainers=GymBuilderTrainer::get_trainers();

		foreach ($trainers as $trainer){
			$options[$trainer->ID] = $trainer->post_title;
		}

		return $options;
	}

	public static function trainer_socials(){
		$trainer_socials = array(
			'facebook' => array(
				'label' => esc_html__( 'Facebook', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'facebook',
			),
			'twitter' => array(
				'label' => esc_html__( 'Twitter', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'twitter',
			),
			'linkedin' => array(
				'label' => esc_html__( 'Linkedin', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'linkedin',
			),
			'skype' => array(
				'label' => esc_html__( 'Skype', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'skype',
			),
			'youtube' => array(
				'label' => esc_html__( 'Youtube', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'youtube',
			),
			'pinterest' => array(
				'label' => esc_html__( 'Pinterest', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'pinterest',
			),
			'instagram' => array(
				'label' => esc_html__( 'Instagram', 'gym-builder' ),
				'type'  => 'text',
				'icon'  => 'instagram',
			),
            'tiktok' => array(
                'label' => esc_html__( 'Tiktok', 'gym-builder' ),
                'type'  => 'text',
                'icon'  => 'tik-tok',
            ),
		);
		return apply_filters( 'gym_builder_trainer_socials', $trainer_socials );
	}

	public static function get_currency() {
		return SettingsApi::get_option('currency','gym_builder_global_settings') ?:'USD';
	}
	public static function get_currency_symbol() {
		$currency = self::get_currency();
		$cList    = Helper::currency_list();
		return $cList[ $currency ]['symbol'];
	}
	public static function get_currency_position() {
		return SettingsApi::get_option('currency_position','gym_builder_global_settings') ?:'left';
	}
	public static function get_price( $id = null ) {
		if ( $id ) {
			$id = absint( $id );
		} else {
			global $post;
			$id = $post->ID;
		}

		return get_post_meta( $id, 'gym_builder_package_price', true );
	}
	public static function get_price_with_label($id) {
		$price = self::get_price($id);
		if ( $price ) {
			$symbol    = self::get_currency_symbol();
			$currencyP = self::get_currency_position();

			switch ( $currencyP ) {
				case 'left':
					$price = $symbol . $price;
					break;

				case 'right':
					$price = $price . $symbol;
					break;

				case 'left_space':
					$price = $symbol . ' ' . $price;
					break;

				case 'right_space':
					$price = $price . ' ' . $symbol;
					break;

				default:
					break;
			}
		}

		return apply_filters( 'gym_builder_membership_package_price_modifier', $price,$id );
	}

	public static function get_header($wp_version) {
		if ( version_compare( $wp_version, '5.9', '>=' ) && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) { ?>
			<!doctype html>
				<html <?php language_attributes(); ?>>
				<head>
					<meta charset="<?php bloginfo( 'charset' ); ?>">
					<?php wp_head(); ?>
				</head>
				<body <?php body_class(); ?>>
				<?php wp_body_open(); ?>
					<div class="wp-site-blocks">
					<?php
						$theme      = wp_get_theme();
						$theme_slug = $theme->get( 'TextDomain' );
						echo do_blocks( '<!-- wp:template-part {"slug":"header","theme":"' . esc_attr( $theme_slug ) . '","tagName":"header","className":"site-header"} /-->' );
		} else {
			get_header();
		}
	}

	public static function get_footer($wp_version) {
		if ( version_compare( $wp_version, '5.9', '>=' ) && function_exists( 'wp_is_block_theme' ) && true === wp_is_block_theme() ) {
			$theme      = wp_get_theme();
			$theme_slug = $theme->get( 'TextDomain' );
			echo do_blocks('<!-- wp:template-part {"slug":"footer","theme":"' . esc_attr( $theme_slug ) . '","tagName":"footer","className":"site-footer"} /-->');
			echo '</div>';
			wp_footer();
			echo '</body>';
			echo '</html>';
		} else {
			get_footer();
		}
	}

	public static function print_html( $html, $allHtml = false ) {
		if ( $allHtml ) {
			echo stripslashes_deep( $html );
		} else {
			echo wp_kses_post( stripslashes_deep( $html ) );
		}
	}

	public static function get_template( $template_name, $args = null, $template_path = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = self::locate_template( $template_name, $template_path, $default_path );


		if ( ! file_exists( $located ) ) {

			self::doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'gym-builder' ), '<code>' . $located . '</code>' ), '1.0' );

			return;
		}

		$located = apply_filters( 'gym_builder_get_template', $located, $template_name, $args );

		do_action( 'gym_builder_before_template_part', $template_name, $located, $args );

		include $located;

		do_action( 'gym_builder_after_template_part', $template_name, $located, $args );
	}
	/**
	 * @param        $template_name
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return mixed|void
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {

		$template_name = $template_name . ".php";

		if ( ! $template_path ) {
			$template_path = self::get_template_path();
		}

		if ( ! $default_path ) {
			$default_path = self::get_file_locations('plugin_path'). '/templates/';
		}

		$template_files = [];

		
		
		$template_files[] = trailingslashit( $template_path ) . $template_name;

		$template = locate_template( apply_filters( 'gym_builder_locate_template_files', $template_files, $template_name, $template_path, $default_path ) );

		//Get default template/.
		
		if ( ! $template) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		return apply_filters( 'gym_builder_locate_template', $template, $template_name );
	}

	public static function get_theme_slug_for_templates() {
		return apply_filters( 'gym_builder_theme_slug_for_templates', get_option( 'template' ) );
	}

	public static function doing_it_wrong( $function, $message, $version ) {
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
		_doing_it_wrong( $function, $message, $version );
	}
	/**
	 * Render.
	 *
	 * @param string  $template_name View name.
	 * @param array   $args View args.
	 * @param boolean $return View return.
	 * @return string|void
	 */
	public static function render( $template_name, $args = [], $return = false ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$template = [
			$template_name . '.php',
			"gym-builder/{$template_name}.php",
		];

		if ( locate_template( $template ) ) {
			$template_file = locate_template( $template );
		}  else {
			$template_file = self::get_plugin_template_path() . $template_name . '.php';
		}

		if ( ! file_exists( $template_file ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $template_file ) ), '1.7.0' );

			return;
		}

		if ( $return ) {
			ob_start();
			include $template_file;

			return ob_get_clean();
		} else {
			include $template_file;
		}
	}
	public static function renderView( $viewName, $args = [], $return = false ) {
		$viewName = str_replace( '.', '/', $viewName );

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}
		$view_file = self::get_file_locations('plugin_path') . 'resources/' . $viewName . '.php';

		if ( ! file_exists( $view_file ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $view_file ) ), '1.7.0' );

			return;
		}

		if ( $return ) {
			ob_start();
			include $view_file;

			return ob_get_clean();
		} else {
			include $view_file;
		}
	}
	/**
	 * Check is Class 
	 *
	 * @return bool
	 */
	public static function is_class(){
		return apply_filters( 'gym_builder_is_class', self::is_classes() || self::is_class_taxonomy() || self::is_single_class() );
	}
	/**
	 * Check is Class Archive Page
	 *
	 * @return bool
	 */
	public static function is_classes() {
		return apply_filters( 'gym_builder_is_class_page', is_post_type_archive( self::$class_post_type ) || is_page( self::get_page_id( 'classes' ) ) );
	}
    /**
     * Check is Trainer
     *
     * @return bool
     */
    public static function is_trainer() {
        return apply_filters( 'gym_builder_is_trainer', self::is_trainers() || self::is_trainer_taxonomy() || self::is_single_trainer() );
    }
    /**
     * Check is Trainer Archive Page
     *
     * @return bool
     */
    public static function is_trainers() {
        return apply_filters( 'gym_builder_is_trainer_page', is_post_type_archive( self::$trainer_post_type ) || is_page( self::get_page_id( 'trainers' ) ) );
    }
	/**
	 * Check is Class single  page
	 *
	 * @return bool
	 */
	public static function is_single_class() {
		return is_singular( [ 'gym_builder_class' ] );
	}
    /**
     * Check is Trainer single  page
     *
     * @return bool
     */
    public static function is_single_trainer() {
        return is_singular( [ self::$trainer_post_type ] );
    }
	/**
	 * Check is Class taxonomy archive page
	 *
	 * @return bool
	 */
	public static function is_class_taxonomy() {
		return is_tax( get_object_taxonomies( 'gym_builder_class' ) );
	}
    /**
     * Check is Trainer taxonomy archive page
     *
     * @return bool
     */
    public static function is_trainer_taxonomy() {
        return is_tax( get_object_taxonomies( self::$trainer_post_type ) );
    }

	public static function get_template_part( $slug, $name = '' ) {

		$cache_key = sanitize_key( implode( '-', [ 'template-part', $slug, $name, self::$plugin_version ] ) );
		$template  = (string) wp_cache_get( $cache_key, 'gym_builder' );

		if ( ! $template ) {
			if ( $name ) {
				$template = self::$plugin_template_load ? '' : locate_template(
					[
						"{$slug}-{$name}.php",
						self::get_file_locations('plugin_path') . "{$slug}-{$name}.php"
					]
				);

				if ( ! $template ) {
					$fallback = self::get_file_locations('plugin_path') . "/templates/{$slug}-{$name}.php";
					$template = file_exists( $fallback ) ? $fallback : '';
				}
			}


			if ( ! $template ) {

				// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/gym-builder/slug.php.

				$template = self::$plugin_template_load  ? '' : locate_template(
					[
						"{$slug}.php",
						self::get_template_path() . "{$slug}.php",
					]
				);
			}
			wp_cache_set( $cache_key, $template, 'gym_builder' );
		}

		//Allow 3rd party plugins to filter template file from their plugin.

		$template = apply_filters( 'gym_builder_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
	/**
	 * Check is Blog Theme
	 *
	 * @return bool
	 */

	 public static function is_blog_theme() {

		global $wp_version;
		
		if ( version_compare( $wp_version, '5.9', '>=' ) && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) { 
			return true;
		}
		return false;
	 }

    /**
     * Check is Active Sidebar
     *
     * @param $sidebar
     * @return bool
     */

	public static function is_active_sidebar($sidebar) {
		if(is_active_sidebar($sidebar)){
			return true;
		}
		return false;
		
	}



}