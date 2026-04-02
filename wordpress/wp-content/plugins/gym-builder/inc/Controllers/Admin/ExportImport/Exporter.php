<?php

namespace GymBuilder\Inc\Controllers\Admin\ExportImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class Exporter {

	/**
	 * Export classes as structured array.
	 *
	 * @param bool $include_images Whether to include featured image URLs.
	 *
	 * @return array
	 */
	public static function export_classes( $include_images = false ) {
		$posts = get_posts( [
			'post_type'      => 'gym_builder_class',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		$items = [];
		foreach ( $posts as $post ) {
			$schedule = get_post_meta( $post->ID, 'gym_builder_class_schedule', true );

			// Replace trainer IDs with trainer titles for portability.
			if ( is_array( $schedule ) ) {
				foreach ( $schedule as &$entry ) {
					if ( ! empty( $entry['trainer'] ) ) {
						$trainer_title    = get_the_title( (int) $entry['trainer'] );
						$entry['trainer'] = $trainer_title ?: '';
					}
				}
				unset( $entry );
			}

			// Replace package IDs with titles.
			$package_ids    = get_post_meta( $post->ID, 'gym_builder_package_prices', true );
			$package_titles = [];
			if ( is_array( $package_ids ) ) {
				foreach ( $package_ids as $pid ) {
					$title = get_the_title( (int) $pid );
					if ( $title ) {
						$package_titles[] = $title;
					}
				}
			}

			// Package category name instead of term ID.
			$package_name_id = get_post_meta( $post->ID, 'gym_builder_pricing_package_name', true );
			$package_cat     = '';
			if ( $package_name_id ) {
				$term = get_term( (int) $package_name_id, 'gb_pricing_plan_category' );
				if ( $term && ! is_wp_error( $term ) ) {
					$package_cat = $term->name;
				}
			}

			$categories = wp_get_post_terms( $post->ID, 'gym_builder_class_category', [ 'fields' => 'names' ] );
			if ( is_wp_error( $categories ) ) {
				$categories = [];
			}

			$featured_image = '';
			if ( $include_images ) {
				$featured_image = get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '';
			}

			$items[] = [
				'title'      => $post->post_title,
				'content'    => $post->post_content,
				'excerpt'    => $post->post_excerpt,
				'status'     => $post->post_status,
				'menu_order' => $post->menu_order,
				'categories' => $categories,
				'meta'       => [
					'gym_builder_class_schedule'        => $schedule ?: [],
					'gym_builder_class_color'           => get_post_meta( $post->ID, 'gym_builder_class_color', true ) ?: '',
					'gym_builder_class_button_text'     => get_post_meta( $post->ID, 'gym_builder_class_button_text', true ) ?: '',
					'gym_builder_class_button_url'      => get_post_meta( $post->ID, 'gym_builder_class_button_url', true ) ?: '',
					'gym_builder_class_icon'            => get_post_meta( $post->ID, 'gym_builder_class_icon', true ) ?: '',
					'gym_builder_course_duration_time'   => get_post_meta( $post->ID, 'gym_builder_course_duration_time', true ) ?: '',
					'gym_builder_pricing_package_name'  => $package_cat,
					'gym_builder_package_prices'        => $package_titles,
				],
				'featured_image' => $featured_image,
			];
		}

		return [
			'type'        => 'gym_builder_classes',
			'version'     => self::plugin_version(),
			'exported_at' => current_time( 'c' ),
			'count'       => count( $items ),
			'items'       => $items,
		];
	}

	/**
	 * Export trainers as structured array.
	 *
	 * @param bool $include_images Whether to include featured image URLs.
	 *
	 * @return array
	 */
	public static function export_trainers( $include_images = false ) {
		$posts = get_posts( [
			'post_type'      => 'gym_builder_trainer',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		$items = [];
		foreach ( $posts as $post ) {
			$categories = wp_get_post_terms( $post->ID, 'gym_builder_trainer_category', [ 'fields' => 'names' ] );
			if ( is_wp_error( $categories ) ) {
				$categories = [];
			}

			$featured_image = '';
			if ( $include_images ) {
				$featured_image = get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '';
			}

			$items[] = [
				'title'      => $post->post_title,
				'content'    => $post->post_content,
				'excerpt'    => $post->post_excerpt,
				'status'     => $post->post_status,
				'menu_order' => $post->menu_order,
				'categories' => $categories,
				'meta'       => [
					'gym_builder_trainer_designation' => get_post_meta( $post->ID, 'gym_builder_trainer_designation', true ) ?: '',
					'gym_builder_trainer_email'       => get_post_meta( $post->ID, 'gym_builder_trainer_email', true ) ?: '',
					'gym_builder_trainer_skill'       => get_post_meta( $post->ID, 'gym_builder_trainer_skill', true ) ?: [],
					'gym_builder_trainer_socials'     => get_post_meta( $post->ID, 'gym_builder_trainer_socials', true ) ?: [],
				],
				'featured_image' => $featured_image,
			];
		}

		return [
			'type'        => 'gym_builder_trainers',
			'version'     => self::plugin_version(),
			'exported_at' => current_time( 'c' ),
			'count'       => count( $items ),
			'items'       => $items,
		];
	}

	/**
	 * Export membership packages as structured array.
	 *
	 * @param bool $include_images Whether to include featured image URLs.
	 *
	 * @return array
	 */
	public static function export_packages( $include_images = false ) {
		$posts = get_posts( [
			'post_type'      => 'gb_pricing_plan',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		$items = [];
		foreach ( $posts as $post ) {
			$categories = wp_get_post_terms( $post->ID, 'gb_pricing_plan_category', [ 'fields' => 'names' ] );
			if ( is_wp_error( $categories ) ) {
				$categories = [];
			}

			$featured_image = '';
			if ( $include_images ) {
				$featured_image = get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '';
			}

			$items[] = [
				'title'      => $post->post_title,
				'content'    => $post->post_content,
				'status'     => $post->post_status,
				'menu_order' => $post->menu_order,
				'categories' => $categories,
				'meta'       => [
					'gym_builder_package_price'          => get_post_meta( $post->ID, 'gym_builder_package_price', true ) ?: '',
					'gym_builder_package_price_duration'  => get_post_meta( $post->ID, 'gym_builder_package_price_duration', true ) ?: '',
					'gym_builder_package_features'       => get_post_meta( $post->ID, 'gym_builder_package_features', true ) ?: [],
					'gym_builder_package_button_text'    => get_post_meta( $post->ID, 'gym_builder_package_button_text', true ) ?: '',
					'gym_builder_package_button_url'     => get_post_meta( $post->ID, 'gym_builder_package_button_url', true ) ?: '',
				],
				'featured_image' => $featured_image,
			];
		}

		return [
			'type'        => 'gym_builder_packages',
			'version'     => self::plugin_version(),
			'exported_at' => current_time( 'c' ),
			'count'       => count( $items ),
			'items'       => $items,
		];
	}

	/**
	 * Convert structured export data to CSV string.
	 *
	 * @param array $data Export data array.
	 *
	 * @return string CSV content.
	 */
	public static function to_csv( $data ) {
		if ( empty( $data['items'] ) ) {
			return '';
		}

		$output = fopen( 'php://temp', 'r+' );

		$type = $data['type'];

		if ( 'gym_builder_classes' === $type ) {
			$headers = [
				'Title', 'Content', 'Excerpt', 'Status', 'Menu Order', 'Categories',
				'Schedule', 'Color', 'Button Text', 'Button URL', 'Icon',
				'Duration', 'Package Category', 'Package Prices', 'Featured Image',
			];
			fputcsv( $output, $headers );

			foreach ( $data['items'] as $item ) {
				fputcsv( $output, [
					$item['title'],
					$item['content'],
					$item['excerpt'],
					$item['status'],
					$item['menu_order'],
					implode( ', ', $item['categories'] ),
					wp_json_encode( $item['meta']['gym_builder_class_schedule'] ),
					$item['meta']['gym_builder_class_color'],
					$item['meta']['gym_builder_class_button_text'],
					$item['meta']['gym_builder_class_button_url'],
					$item['meta']['gym_builder_class_icon'],
					$item['meta']['gym_builder_course_duration_time'],
					$item['meta']['gym_builder_pricing_package_name'],
					implode( ', ', $item['meta']['gym_builder_package_prices'] ),
					$item['featured_image'],
				] );
			}
		} elseif ( 'gym_builder_trainers' === $type ) {
			$headers = [
				'Title', 'Content', 'Excerpt', 'Status', 'Menu Order', 'Categories',
				'Designation', 'Email', 'Skills', 'Socials', 'Featured Image',
			];
			fputcsv( $output, $headers );

			foreach ( $data['items'] as $item ) {
				fputcsv( $output, [
					$item['title'],
					$item['content'],
					$item['excerpt'],
					$item['status'],
					$item['menu_order'],
					implode( ', ', $item['categories'] ),
					$item['meta']['gym_builder_trainer_designation'],
					$item['meta']['gym_builder_trainer_email'],
					wp_json_encode( $item['meta']['gym_builder_trainer_skill'] ),
					wp_json_encode( $item['meta']['gym_builder_trainer_socials'] ),
					$item['featured_image'],
				] );
			}
		} elseif ( 'gym_builder_packages' === $type ) {
			$headers = [
				'Title', 'Content', 'Status', 'Menu Order', 'Categories',
				'Price', 'Price Duration', 'Features', 'Button Text', 'Button URL', 'Featured Image',
			];
			fputcsv( $output, $headers );

			foreach ( $data['items'] as $item ) {
				fputcsv( $output, [
					$item['title'],
					$item['content'],
					$item['status'],
					$item['menu_order'],
					implode( ', ', $item['categories'] ),
					$item['meta']['gym_builder_package_price'],
					$item['meta']['gym_builder_package_price_duration'],
					wp_json_encode( $item['meta']['gym_builder_package_features'] ),
					$item['meta']['gym_builder_package_button_text'],
					$item['meta']['gym_builder_package_button_url'],
					$item['featured_image'],
				] );
			}
		}

		rewind( $output );
		$csv = stream_get_contents( $output );
		fclose( $output );

		return $csv;
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	private static function plugin_version() {
		if ( defined( 'GYM_BUILDER_VERSION' ) ) {
			return GYM_BUILDER_VERSION;
		}

		return '2.3.0';
	}
}
