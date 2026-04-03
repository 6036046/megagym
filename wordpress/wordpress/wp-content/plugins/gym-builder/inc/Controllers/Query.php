<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers;

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Traits\Constants;
use \WP_Query;
use \GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class Query {
	use Constants;
	/**
	 * Query Args.
	 *
	 * @var array
	 */
	private $args = [];

	/**
	 * Post Type values.
	 *
	 * @var string
	 */

	private $post_type;

	/**
	 * Taxonomies values.
	 *
	 * @var string
	 */

	private $taxonomy;


	public function set_query( string $post_type, string $taxonomy= null ,array $args=[]) {
		$this->post_type = $post_type;
		$this->taxonomy  = $taxonomy;
		$this->args      = $args;
		return $this;
	}

	public function get_posts_query() {
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		} else {
			$paged = 1;
		}
		$args = [
			'post_type'           => $this->post_type,
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish',
			'posts_per_page'      => $this->args['limit'] ?? -1,
			'paged'               => $paged

		];

		if ( ! empty( $this->args['post_in'] ) ) {
			$args['post__in'] = $this->args['post_in'];
		}

		if ( ! empty( $this->args['post_not_in'] ) ) {
			$args['post__not_in'] = $this->args['post_not_in'];
		}

		if ( ! empty( $this->args['categories'] ) && $this->taxonomy) {
			$args['tax_query'] = [
				[
					'taxonomy' => $this->taxonomy,
					'field'    => 'term_id',
					'terms'    => $this->args['categories'],
				]
			];
		}
		if (  !empty($this->args['order_by']) && $this->args['order_by'] !== 'none'  ) {
			$args['orderby'] = $this->args['order_by'];
		}
		if (  !empty($this->args['order'])  ) {
			$args['order'] = $this->args['order'];
		}


		$query_args = apply_filters( 'gym_builder_' . $this->post_type . '_query', $args );
		return new WP_Query( $query_args );
	}

}
