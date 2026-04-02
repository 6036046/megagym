<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers\Admin\Api;

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;
use GymBuilder\Inc\Traits\SingleTonTrait;
use GymBuilder\Inc\Controllers\Helpers\Functions;
use WP_REST_Request;
use WP_Error;
use WP_Query;
use WP_REST_Response;

class RestApi{
	use Constants,SingleTonTrait;
	public function init(){
		add_action('rest_api_init', [$this,'gym_builder_rest_api_posts_endpoint']);
	}

	public function gym_builder_rest_api_posts_endpoint(  ) {
		register_rest_route(self::$classes_endpoint_namespace, '/posts', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_classes'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$classes_endpoint_namespace2, '/posts', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_classes_json'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$classes_endpoint_namespace, '/categories', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_class_categories'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$trainers_endpoint_namespace, '/posts', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_trainers'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$trainers_endpoint_namespace, '/categories', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_trainer_categories'],
			'permission_callback' => '__return_true',
		));
		register_rest_route('get_gym_builder/v1', '/pages', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_pages'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$gym_builder_currency_endpoint_namespace, '/data', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_currency'],
			'permission_callback' => '__return_true',
		));
		register_rest_route(self::$membership_package_endpoint_namespace, '/posts', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_membership_package'],
			'permission_callback' => array($this, 'check_admin_permission'),
		));
		register_rest_route('get_gym_builder_settings/v1', '/options', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_settings_options'],
			'permission_callback' => array($this, 'check_admin_permission'),
		));
		register_rest_route(self::$membership_package_endpoint_namespace, '/types', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_membership_package_types'],
			'permission_callback' => array($this, 'check_admin_permission'),
		));
		register_rest_route(self::$membership_package_endpoint_namespace, '/category/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_rest_membership_package_by_cat_id'],
			'permission_callback' => '__return_true',
			'args' => array(
				'id' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
				),
			),
		));
		register_rest_route(self::$gym_builder_members_endpoint_namespace, '/data', array(
			'methods' => 'GET',
			'callback' => [$this,'get_all_members_data'],
			'permission_callback' => array($this, 'check_admin_permission'),
		));
        register_rest_route(self::$gym_builder_members_endpoint_namespace, '/total-members', array(
            'methods' => 'GET',
            'callback' => [$this,'get_total_members_count'],
            'permission_callback' => array($this, 'check_admin_permission'),
        ));
		register_rest_route(self::$gym_builder_members_endpoint_namespace, '/data/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => [$this,'get_single_member_data'],
			'permission_callback' => array($this, 'check_admin_permission'),
			'args' => array(
				'id' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
				),
			),
		));
		register_rest_route(self::$classes_endpoint_namespace, '/schedule_data/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => [$this,'get_single_v2_class_schedule_data'],
			'permission_callback' => array($this, 'check_admin_permission'),
			'args' => array(
				'id' => array(
					'validate_callback' => function($param, $request, $key) {
						return is_numeric( $param );
					}
				),
			),
		));
		register_rest_route( 'gym_builder/v1', '/all-posts', [
			'methods'  => 'GET',
			'callback' => [ $this, 'get_all_posts_type' ],
			'permission_callback' => function () {
				return true;
			}
		] );
        register_rest_route( 'gym_builder/v1', '/class-schedule', [
            'methods'  => 'GET',
            'callback' => [ $this, 'get_all_class_schedule' ],
            'permission_callback' => array($this, 'check_admin_permission'),
        ] );


	}

	public function get_all_pages( $data ) {
		$page_list = [];
		$pages     = get_pages(
			[
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'hierarchical' => 0,
			]
		);
		if ($pages){
			foreach ( $pages as $page ) {
				$page_list[]=[
					'value' => $page->ID,
					'label' => ! empty( $page->post_title ) ? $page->post_title : '#' . $page->ID
				];
			}
		}
		return wp_json_encode($page_list);
	}

	public function get_all_rest_classes( $data ) {
		$args = array(
			'post_type' => self::$class_post_type,
			'post_status'  => 'publish',
			'posts_per_page' => -1,
		);

		$query = new WP_Query($args);

		if ( $query->have_posts() ) {
			return $query->posts;
		}else{
			return [];
		}


	}
	public function get_all_rest_classes_json(){
		$class_list=[];
		$args = array(
			'post_type'  => self::$class_post_type,
			'post_status'  => 'publish',
			'numberposts' => -1,
		);
		$classes = get_posts($args);
		if($classes){
			foreach ( $classes as $class ) {
				$class_list[]=[
					'value' => $class->ID,
					'label' => ! empty( $class->post_title ) ? $class->post_title : '#' . $class->ID
				];
			}
		}
		return wp_json_encode($class_list);
	}

	public function get_all_rest_trainers(  ) {
		$trainer_list=[];
		$args = array(
			'post_type'  => self::$trainer_post_type,
			'post_status'  => 'publish',
			'numberposts' => -1,
		);
		$trainers = get_posts($args);
		if($trainers){
			foreach ( $trainers as $trainer ) {
				$trainer_list[]=[
					'value' => $trainer->ID,
					'label' => ! empty( $trainer->post_title ) ? $trainer->post_title : '#' . $trainer->ID
				];
			}
		}
		return wp_json_encode($trainer_list);
		
	}

	public function get_all_rest_class_categories(  ) {
		$terms_list = [];
		$terms           = get_terms( [
			'taxonomy'   => self::$class_taxonomy,
			'hide_empty' => false
		] );
		if ( ! is_wp_error( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				$terms_list[] = [
					'value' => $term->term_id,
					'label' => $term->name,
				];
			}
		}
		return wp_json_encode( $terms_list );
		
	}

	public function get_all_rest_trainer_categories(  ) {
		$terms_list = [];
		$terms           = get_terms( [
			'taxonomy'   => self::$trainer_taxonomy,
			'hide_empty' => false
		] );
		if ( ! is_wp_error( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				$terms_list[] = [
					'value' => $term->term_id,
					'label' => $term->name,
				];
			}
		}
		return wp_json_encode( $terms_list );
	}
	public function get_all_rest_membership_package(  ) {
		 $args = array(
		 	'post_type' => self::$membership_package_post_type,
		 	'posts_per_page' => -1,
			'post_status'  => 'publish',
		 );

		 $query = new WP_Query($args);
		 if ( $query->have_posts()) {
			 return $query->posts;
		 }else{
			return [];
		 }
		
	}

	public function get_all_rest_membership_package_by_cat_id($data) {
		$args = array(
			'post_type' => self::$membership_package_post_type,
			'posts_per_page' => -1,
			'post_status'  => 'publish',
			'tax_query' =>[
				[
					'taxonomy' => self::$membership_package_taxonomy,
					'field'    => 'ID',
					'terms'    => $data['id'],
				]
			]
		);

		$query = new WP_Query($args);

		if ( $query->have_posts()) {
			return $query->posts;
		}else{
			return [];
		}
	}

	public function get_all_rest_membership_package_types($data){
		$terms           = get_terms( [
			'taxonomy'   => self::$membership_package_taxonomy,
			'hide_empty' => false
		] );
		if ( $terms) {
			return $terms;
		}else{
			return[];
		}
	}

	public function get_all_members_data( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'gym_builder_members';
		$page = $request->get_param('page') ? (int) $request->get_param('page') : 1;
		$per_page = $request->get_param('per_page') ? (int) $request->get_param('per_page') : 10;
		$offset = ($page - 1) * $per_page;
		$search = $request->get_param('search') ? $request->get_param('search') : '';
		$order = $request->get_param('order') ? $request->get_param('order') : 'DESC';
		$sql = "SELECT id,member_name,member_gender,member_address,member_phone,member_email,membership_status,file_url,member_user_id 
            FROM $table_name";
		if (!empty($search)) {
			$sql .= $wpdb->prepare(" WHERE member_name LIKE %s OR member_email LIKE %s", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
		}

		$sql .= " ORDER BY id $order";

		$sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);

		$results = $wpdb->get_results(apply_filters('get_members_data_sql',$sql), ARRAY_A);
		$total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name" . (!empty($search) ? $wpdb->prepare(" WHERE member_name LIKE %s OR member_email LIKE %s", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%') : ''));

		if ($results) {
			$response = new WP_REST_Response( $results );
			$response->set_status( 200 );
			$response->header( 'X-WP-Total', $total );
			$response->header( 'X-WP-TotalPages', ceil( $total / $per_page ) );
		}else{
			$response = [];
		}
		return $response;
	}
	public function get_single_member_data( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'gym_builder_members';
		$member_id = absint($request['id']);
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $member_id);
		$result = $wpdb->get_row($sql, ARRAY_A);
		if ($result) {
			if ($result['membership_package_type']){
				$package_type_obj =  get_term_by('name', $result['membership_package_type'], self::$membership_package_taxonomy);
				$package_type_id= $package_type_obj->term_id;
				$result['package_type_id'] = $package_type_id;
			}
			$response = new WP_REST_Response( $result );
			$response->set_status( 200 );
		}else{
			$response = [];
		}
		return $response;
	}

	public function get_single_v2_class_schedule_data( $request ) {
		$class_id = absint($request['id']);
		$class_schedule_v2 = get_post_meta($class_id,'gym_builder_v2_class_schedule',true);
		if (!empty($class_schedule_v2 && is_array($class_schedule_v2))){
			$response = new WP_REST_Response( $class_schedule_v2 );
			$response->set_status( 200 );
		}else{
			$response = [];
		}
		return $response;
	}

	public function check_admin_permission( WP_REST_Request $request ) {
		$nonce      = $request->get_header( 'X-WP-Nonce' );
		
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) || ! current_user_can( 'manage_options' )) {
			return new WP_Error('rest_forbidden', __('You are not allowed to access this endpoint.','gym-builder'), array('status' => 403));
		}

		return true;
	}

	public function get_all_settings_options(  ) {
		return Helper::gym_builder_get_options();
	}

	public function get_all_currency(  ) {
		$currency_list=[];
		foreach (Helper::currency_list() as $key=>$currency){
			$currency_list[]= [
				'value' => $key,
				'label' => $currency['name'] . ' (' . $currency['symbol'] . ')'
			];
		}

		return wp_json_encode($currency_list);
	}
	function get_all_posts_type( $request ) {

		$posts_data = [];
		$paged      = $request->get_param( 'page' );
		$search     = $request->get_param( 'search' );
		$paged      = $paged ?? 1;
		$post_type  = Helper::get_post_types();
		$posts = get_posts( [
				'paged'          => $paged,
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'post__not_in'   => get_option( 'sticky_posts' ),
				'posts_per_page' => 30,
				"s"              => $search,
				'post_type'      => array_keys( $post_type )
			]
		);

		foreach ( $posts as $post ) {
			$id = $post->ID;

			$posts_data[] = (object) [
				'id'   => $id,
				'slug' => $post->post_name,
				'type' => $post->post_type,
				'text' => $post->post_title,
			];
		}

		return $posts_data;
	}

    public function get_total_members_count($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gym_builder_members';

        // Fetch all members
        $members = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );
        $total_members = is_array($members) ? count($members) : 0;

        // Count active members
        $total_active = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} WHERE membership_status = 1"
        );

        // Count inactive members
        $total_inactive = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} WHERE membership_status = 0"
        );

        // Current month and year
        $current_year  = date('Y');
        $current_month = date('n');

        // Previous month (only same year)
        $previous_month = $current_month - 1;


        // Registered this month
        $registered_this_month = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name}
             WHERE YEAR(member_joining_date) = %d AND MONTH(member_joining_date) = %d",
                $current_year,
                $current_month
            )
        );

        // Registered previous month
        $registered_previous_month = 0;
        if ( $previous_month > 0 ) {
            $registered_previous_month = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_name}
                 WHERE YEAR(member_joining_date) = %d AND MONTH(member_joining_date) = %d",
                    $current_year,
                    $previous_month
                )
            );
        }
        $class_posts_count = wp_count_posts( 'gym_builder_class' );
        $trainer_posts_count = wp_count_posts( 'gym_builder_trainer' );

        $class_published = isset( $class_posts_count->publish ) ? (int) $class_posts_count->publish : 0;
        $trainer_published = isset( $trainer_posts_count->publish ) ? (int) $trainer_posts_count->publish : 0;
        return wp_json_encode(
            [
                'total_members'              => (int) $total_members,
                'total_active'               => (int) $total_active,
                'total_inactive'             => (int) $total_inactive,
                'registered_this_month'      => (int) $registered_this_month,
                'registered_previous_month' => (int) $registered_previous_month,
                'total_classes'             => $class_published,
                'total_trainers'            => $trainer_published
            ]
        );
    }

    public function get_all_class_schedule($request) {
        $args = [
            'post_type'      => 'gym_builder_class',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        $query = new WP_Query($args);
        $result = [];

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $schedule = get_post_meta($post_id, 'gym_builder_v2_class_schedule', true);

            if (!empty($schedule) && is_array($schedule)) {
                $result[] = [
                    'id'       => $post_id,
                    'title'    => get_the_title(),
                    'schedule' => $schedule[$post_id] ?? [],
                ];
            }
        }
        wp_reset_postdata();
        return wp_json_encode($result);
    }

}