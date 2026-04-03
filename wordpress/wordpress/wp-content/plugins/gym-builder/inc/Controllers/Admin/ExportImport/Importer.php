<?php

namespace GymBuilder\Inc\Controllers\Admin\ExportImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class Importer {

	/**
	 * Import classes from JSON data.
	 *
	 * @param array $data           Decoded JSON export data.
	 * @param bool  $include_images Whether to download and set featured images.
	 *
	 * @return array Result with imported, skipped, errors counts.
	 */
	public static function import_classes( $data, $include_images = false ) {
		$items    = $data['items'] ?? [];
		$imported = 0;
		$skipped  = 0;
		$errors   = [];

		foreach ( $items as $item ) {
			$title = sanitize_text_field( $item['title'] ?? '' );

			if ( empty( $title ) ) {
				$errors[] = __( 'Skipped class with empty title.', 'gym-builder' );
				continue;
			}

			// Duplicate check.
			if ( self::post_exists( $title, 'gym_builder_class' ) ) {
				$skipped++;
				continue;
			}

			$post_id = wp_insert_post( [
				'post_title'   => $title,
				'post_content' => wp_kses_post( $item['content'] ?? '' ),
				'post_excerpt' => sanitize_textarea_field( $item['excerpt'] ?? '' ),
				'post_status'  => sanitize_text_field( $item['status'] ?? 'publish' ),
				'post_type'    => 'gym_builder_class',
				'menu_order'   => absint( $item['menu_order'] ?? 0 ),
			], true );

			if ( is_wp_error( $post_id ) ) {
				$errors[] = sprintf(
					/* translators: 1: class title, 2: error message */
					__( 'Failed to import class "%1$s": %2$s', 'gym-builder' ),
					$title,
					$post_id->get_error_message()
				);
				continue;
			}

			// Categories.
			if ( ! empty( $item['categories'] ) && is_array( $item['categories'] ) ) {
				$cat_names = array_map( 'sanitize_text_field', $item['categories'] );
				wp_set_object_terms( $post_id, $cat_names, 'gym_builder_class_category' );
			}

			// Meta.
			$meta = $item['meta'] ?? [];

			// Schedule — resolve trainer titles to IDs.
			$schedule = $meta['gym_builder_class_schedule'] ?? [];
			if ( is_array( $schedule ) ) {
				foreach ( $schedule as &$entry ) {
					if ( ! empty( $entry['trainer'] ) && ! is_numeric( $entry['trainer'] ) ) {
						$entry['trainer'] = self::find_post_id_by_title( $entry['trainer'], 'gym_builder_trainer' );
					}
					$entry['week']       = sanitize_text_field( $entry['week'] ?? '' );
					$entry['start_time'] = sanitize_text_field( $entry['start_time'] ?? '' );
					$entry['end_time']   = sanitize_text_field( $entry['end_time'] ?? '' );
					$entry['maximum_member_allow_booking'] = absint( $entry['maximum_member_allow_booking'] ?? 0 );
				}
				unset( $entry );
				update_post_meta( $post_id, 'gym_builder_class_schedule', $schedule );
			}

			// Simple text/color meta.
			$simple_meta = [
				'gym_builder_class_color'         => 'sanitize_hex_color',
				'gym_builder_class_button_text'   => 'sanitize_text_field',
				'gym_builder_class_button_url'    => 'esc_url_raw',
				'gym_builder_class_icon'          => 'sanitize_text_field',
				'gym_builder_course_duration_time' => 'sanitize_text_field',
			];

			foreach ( $simple_meta as $key => $sanitize_fn ) {
				if ( isset( $meta[ $key ] ) && '' !== $meta[ $key ] ) {
					update_post_meta( $post_id, $key, $sanitize_fn( $meta[ $key ] ) );
				}
			}

			// Package category — resolve name to term ID.
			if ( ! empty( $meta['gym_builder_pricing_package_name'] ) ) {
				$term = get_term_by( 'name', sanitize_text_field( $meta['gym_builder_pricing_package_name'] ), 'gb_pricing_plan_category' );
				if ( $term ) {
					update_post_meta( $post_id, 'gym_builder_pricing_package_name', $term->term_id );
				}
			}

			// Package prices — resolve titles to IDs.
			if ( ! empty( $meta['gym_builder_package_prices'] ) && is_array( $meta['gym_builder_package_prices'] ) ) {
				$price_ids = [];
				foreach ( $meta['gym_builder_package_prices'] as $pkg_title ) {
					$pkg_id = self::find_post_id_by_title( sanitize_text_field( $pkg_title ), 'gb_pricing_plan' );
					if ( $pkg_id ) {
						$price_ids[] = $pkg_id;
					}
				}
				if ( ! empty( $price_ids ) ) {
					update_post_meta( $post_id, 'gym_builder_package_prices', $price_ids );
				}
			}

			// Featured image.
			if ( $include_images && ! empty( $item['featured_image'] ) ) {
				self::sideload_featured_image( $item['featured_image'], $post_id );
			}

			$imported++;
		}

		return self::result( $imported, $skipped, $errors, __( 'classes', 'gym-builder' ) );
	}

	/**
	 * Import trainers from JSON data.
	 *
	 * @param array $data           Decoded JSON export data.
	 * @param bool  $include_images Whether to download and set featured images.
	 *
	 * @return array Result with imported, skipped, errors counts.
	 */
	public static function import_trainers( $data, $include_images = false ) {
		$items    = $data['items'] ?? [];
		$imported = 0;
		$skipped  = 0;
		$errors   = [];

		foreach ( $items as $item ) {
			$title = sanitize_text_field( $item['title'] ?? '' );

			if ( empty( $title ) ) {
				$errors[] = __( 'Skipped trainer with empty title.', 'gym-builder' );
				continue;
			}

			if ( self::post_exists( $title, 'gym_builder_trainer' ) ) {
				$skipped++;
				continue;
			}

			$post_id = wp_insert_post( [
				'post_title'   => $title,
				'post_content' => wp_kses_post( $item['content'] ?? '' ),
				'post_excerpt' => sanitize_textarea_field( $item['excerpt'] ?? '' ),
				'post_status'  => sanitize_text_field( $item['status'] ?? 'publish' ),
				'post_type'    => 'gym_builder_trainer',
				'menu_order'   => absint( $item['menu_order'] ?? 0 ),
			], true );

			if ( is_wp_error( $post_id ) ) {
				$errors[] = sprintf(
					__( 'Failed to import trainer "%1$s": %2$s', 'gym-builder' ),
					$title,
					$post_id->get_error_message()
				);
				continue;
			}

			// Categories.
			if ( ! empty( $item['categories'] ) && is_array( $item['categories'] ) ) {
				$cat_names = array_map( 'sanitize_text_field', $item['categories'] );
				wp_set_object_terms( $post_id, $cat_names, 'gym_builder_trainer_category' );
			}

			$meta = $item['meta'] ?? [];

			if ( isset( $meta['gym_builder_trainer_designation'] ) ) {
				update_post_meta( $post_id, 'gym_builder_trainer_designation', sanitize_text_field( $meta['gym_builder_trainer_designation'] ) );
			}

			if ( isset( $meta['gym_builder_trainer_email'] ) ) {
				update_post_meta( $post_id, 'gym_builder_trainer_email', sanitize_email( $meta['gym_builder_trainer_email'] ) );
			}

			// Skills repeater.
			$skills = $meta['gym_builder_trainer_skill'] ?? [];
			if ( is_array( $skills ) ) {
				$sanitized_skills = [];
				foreach ( $skills as $skill ) {
					$sanitized_skills[] = [
						'skill_name'  => sanitize_text_field( $skill['skill_name'] ?? '' ),
						'skill_value' => sanitize_text_field( $skill['skill_value'] ?? '0' ),
					];
				}
				update_post_meta( $post_id, 'gym_builder_trainer_skill', $sanitized_skills );
			}

			// Socials group.
			$socials = $meta['gym_builder_trainer_socials'] ?? [];
			if ( is_array( $socials ) ) {
				$sanitized_socials = [];
				$allowed_keys      = [ 'facebook', 'twitter', 'linkedin', 'skype', 'youtube', 'pinterest', 'instagram' ];
				foreach ( $allowed_keys as $key ) {
					$sanitized_socials[ $key ] = isset( $socials[ $key ] ) ? esc_url_raw( $socials[ $key ] ) : '';
				}
				update_post_meta( $post_id, 'gym_builder_trainer_socials', $sanitized_socials );
			}

			// Featured image.
			if ( $include_images && ! empty( $item['featured_image'] ) ) {
				self::sideload_featured_image( $item['featured_image'], $post_id );
			}

			$imported++;
		}

		return self::result( $imported, $skipped, $errors, __( 'trainers', 'gym-builder' ) );
	}

	/**
	 * Import membership packages from JSON data.
	 *
	 * @param array $data           Decoded JSON export data.
	 * @param bool  $include_images Whether to download and set featured images.
	 *
	 * @return array Result with imported, skipped, errors counts.
	 */
	public static function import_packages( $data, $include_images = false ) {
		$items    = $data['items'] ?? [];
		$imported = 0;
		$skipped  = 0;
		$errors   = [];

		foreach ( $items as $item ) {
			$title = sanitize_text_field( $item['title'] ?? '' );

			if ( empty( $title ) ) {
				$errors[] = __( 'Skipped package with empty title.', 'gym-builder' );
				continue;
			}

			$item_categories = ! empty( $item['categories'] ) && is_array( $item['categories'] )
				? array_map( 'sanitize_text_field', $item['categories'] )
				: [];

			if ( self::post_exists_with_terms( $title, 'gb_pricing_plan', 'gb_pricing_plan_category', $item_categories ) ) {
				$skipped++;
				continue;
			}

			$post_id = wp_insert_post( [
				'post_title'   => $title,
				'post_content' => wp_kses_post( $item['content'] ?? '' ),
				'post_status'  => sanitize_text_field( $item['status'] ?? 'publish' ),
				'post_type'    => 'gb_pricing_plan',
				'menu_order'   => absint( $item['menu_order'] ?? 0 ),
			], true );

			if ( is_wp_error( $post_id ) ) {
				$errors[] = sprintf(
					__( 'Failed to import package "%1$s": %2$s', 'gym-builder' ),
					$title,
					$post_id->get_error_message()
				);
				continue;
			}

			// Categories.
			if ( ! empty( $item['categories'] ) && is_array( $item['categories'] ) ) {
				$cat_names = array_map( 'sanitize_text_field', $item['categories'] );
				wp_set_object_terms( $post_id, $cat_names, 'gb_pricing_plan_category' );
			}

			$meta = $item['meta'] ?? [];

			if ( isset( $meta['gym_builder_package_price'] ) ) {
				update_post_meta( $post_id, 'gym_builder_package_price', sanitize_text_field( $meta['gym_builder_package_price'] ) );
			}

			if ( isset( $meta['gym_builder_package_price_duration'] ) ) {
				update_post_meta( $post_id, 'gym_builder_package_price_duration', sanitize_text_field( $meta['gym_builder_package_price_duration'] ) );
			}

			if ( isset( $meta['gym_builder_package_button_text'] ) ) {
				update_post_meta( $post_id, 'gym_builder_package_button_text', sanitize_text_field( $meta['gym_builder_package_button_text'] ) );
			}

			if ( isset( $meta['gym_builder_package_button_url'] ) ) {
				update_post_meta( $post_id, 'gym_builder_package_button_url', esc_url_raw( $meta['gym_builder_package_button_url'] ) );
			}

			// Features repeater.
			$features = $meta['gym_builder_package_features'] ?? [];
			if ( is_array( $features ) ) {
				$sanitized_features = [];
				foreach ( $features as $feature ) {
					$sanitized_features[] = [
						'feature_icon' => sanitize_text_field( $feature['feature_icon'] ?? 'none' ),
						'feature_item' => sanitize_text_field( $feature['feature_item'] ?? '' ),
					];
				}
				update_post_meta( $post_id, 'gym_builder_package_features', $sanitized_features );
			}

			// Featured image.
			if ( $include_images && ! empty( $item['featured_image'] ) ) {
				self::sideload_featured_image( $item['featured_image'], $post_id );
			}

			$imported++;
		}

		return self::result( $imported, $skipped, $errors, __( 'packages', 'gym-builder' ) );
	}

	/* ─── CSV Parsing ─── */

	/**
	 * Parse a CSV string into the standard import data format.
	 *
	 * @param string $csv_string Raw CSV content.
	 *
	 * @return array|\WP_Error Parsed data with type and items, or WP_Error.
	 */
	public static function parse_csv( $csv_string ) {
		if ( ! is_string( $csv_string ) || '' === $csv_string ) {
			return new \WP_Error( 'invalid_csv', __( 'Invalid or empty CSV data.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$rows = self::csv_to_array( $csv_string );

		if ( count( $rows ) < 2 ) {
			return new \WP_Error( 'invalid_csv', __( 'CSV file is empty or has no data rows.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$headers      = array_map( 'trim', $rows[0] );
		$header_count = count( $headers );
		$type         = self::detect_csv_type( $headers );

		if ( ! $type ) {
			return new \WP_Error(
				'unknown_csv_type',
				__( 'Could not determine the data type from CSV headers. Ensure the file was exported from Gym Builder.', 'gym-builder' ),
				[ 'status' => 400 ]
			);
		}

		$items = [];

		for ( $i = 1, $len = count( $rows ); $i < $len; $i++ ) {
			// Skip empty rows.
			if ( empty( array_filter( $rows[ $i ], 'strlen' ) ) ) {
				continue;
			}

			$row = $rows[ $i ];

			// If the row has fewer columns than headers, pad with empty strings.
			if ( count( $row ) < $header_count ) {
				$row = array_pad( $row, $header_count, '' );
			}

			// If the row has more columns than headers, trim the extras.
			if ( count( $row ) > $header_count ) {
				$row = array_slice( $row, 0, $header_count );
			}

			$mapped = array_combine( $headers, $row );

			if ( false === $mapped ) {
				continue;
			}

			switch ( $type ) {
				case 'gym_builder_classes':
					$items[] = self::map_csv_class( $mapped );
					break;
				case 'gym_builder_trainers':
					$items[] = self::map_csv_trainer( $mapped );
					break;
				case 'gym_builder_packages':
					$items[] = self::map_csv_package( $mapped );
					break;
			}
		}

		if ( empty( $items ) ) {
			return new \WP_Error( 'empty_csv', __( 'No valid data rows found in the CSV file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		return [
			'type'  => $type,
			'count' => count( $items ),
			'items' => $items,
		];
	}

	/**
	 * Parse a CSV string into an array of rows using fgetcsv.
	 *
	 * @param string $csv_string Raw CSV content.
	 *
	 * @return array Array of row arrays.
	 */
	private static function csv_to_array( $csv_string ) {
		$rows   = [];
		$handle = fopen( 'php://temp', 'r+' );

		fwrite( $handle, $csv_string );
		rewind( $handle );

		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			$rows[] = $row;
		}

		fclose( $handle );

		return $rows;
	}

	/**
	 * Detect the data type from CSV column headers.
	 *
	 * @param array $headers Header row values.
	 *
	 * @return string|null Post type identifier or null if unknown.
	 */
	private static function detect_csv_type( $headers ) {
		$lower = array_map( 'strtolower', $headers );

		// Check packages FIRST — "price" and "price duration" are unique to packages.
		if ( in_array( 'price', $lower, true ) || in_array( 'price duration', $lower, true ) || in_array( 'features', $lower, true ) ) {
			return 'gym_builder_packages';
		}

		// Check trainers — "designation" and "skills" are unique to trainers.
		if ( in_array( 'designation', $lower, true ) || in_array( 'skills', $lower, true ) ) {
			return 'gym_builder_trainers';
		}

		// Check classes LAST — "schedule" and "duration" are unique to classes.
		if ( in_array( 'schedule', $lower, true ) || in_array( 'duration', $lower, true ) ) {
			return 'gym_builder_classes';
		}

		return null;
	}

	/**
	 * Map a CSV row to the class item format.
	 *
	 * @param array $row Associative array (header => value).
	 *
	 * @return array Class item in standard import format.
	 */
	private static function map_csv_class( $row ) {
		$schedule       = json_decode( $row['Schedule'] ?? '', true );
		$categories     = self::split_csv_list( $row['Categories'] ?? '' );
		$package_prices = self::split_csv_list( $row['Package Prices'] ?? '' );

		return [
			'title'      => $row['Title'] ?? '',
			'content'    => $row['Content'] ?? '',
			'excerpt'    => $row['Excerpt'] ?? '',
			'status'     => $row['Status'] ?? 'publish',
			'menu_order' => (int) ( $row['Menu Order'] ?? 0 ),
			'categories' => $categories,
			'meta'       => [
				'gym_builder_class_schedule'        => is_array( $schedule ) ? $schedule : [],
				'gym_builder_class_color'           => $row['Color'] ?? '',
				'gym_builder_class_button_text'     => $row['Button Text'] ?? '',
				'gym_builder_class_button_url'      => $row['Button URL'] ?? '',
				'gym_builder_class_icon'            => $row['Icon'] ?? '',
				'gym_builder_course_duration_time'  => $row['Duration'] ?? '',
				'gym_builder_pricing_package_name'  => $row['Package Category'] ?? '',
				'gym_builder_package_prices'        => $package_prices,
			],
			'featured_image' => $row['Featured Image'] ?? '',
		];
	}

	/**
	 * Map a CSV row to the trainer item format.
	 *
	 * @param array $row Associative array (header => value).
	 *
	 * @return array Trainer item in standard import format.
	 */
	private static function map_csv_trainer( $row ) {
		$skills     = json_decode( $row['Skills'] ?? '', true );
		$socials    = json_decode( $row['Socials'] ?? '', true );
		$categories = self::split_csv_list( $row['Categories'] ?? '' );

		return [
			'title'      => $row['Title'] ?? '',
			'content'    => $row['Content'] ?? '',
			'excerpt'    => $row['Excerpt'] ?? '',
			'status'     => $row['Status'] ?? 'publish',
			'menu_order' => (int) ( $row['Menu Order'] ?? 0 ),
			'categories' => $categories,
			'meta'       => [
				'gym_builder_trainer_designation' => $row['Designation'] ?? '',
				'gym_builder_trainer_email'       => $row['Email'] ?? '',
				'gym_builder_trainer_skill'       => is_array( $skills ) ? $skills : [],
				'gym_builder_trainer_socials'     => is_array( $socials ) ? $socials : [],
			],
			'featured_image' => $row['Featured Image'] ?? '',
		];
	}

	/**
	 * Map a CSV row to the package item format.
	 *
	 * @param array $row Associative array (header => value).
	 *
	 * @return array Package item in standard import format.
	 */
	private static function map_csv_package( $row ) {
		$features   = json_decode( $row['Features'] ?? '', true );
		$categories = self::split_csv_list( $row['Categories'] ?? '' );

		return [
			'title'      => $row['Title'] ?? '',
			'content'    => $row['Content'] ?? '',
			'status'     => $row['Status'] ?? 'publish',
			'menu_order' => (int) ( $row['Menu Order'] ?? 0 ),
			'categories' => $categories,
			'meta'       => [
				'gym_builder_package_price'          => $row['Price'] ?? '',
				'gym_builder_package_price_duration'  => $row['Price Duration'] ?? '',
				'gym_builder_package_features'       => is_array( $features ) ? $features : [],
				'gym_builder_package_button_text'    => $row['Button Text'] ?? '',
				'gym_builder_package_button_url'     => $row['Button URL'] ?? '',
			],
			'featured_image' => $row['Featured Image'] ?? '',
		];
	}

	/**
	 * Split a comma-separated string into a trimmed array, filtering empties.
	 *
	 * @param string $value Comma-separated string.
	 *
	 * @return array
	 */
	private static function split_csv_list( $value ) {
		if ( '' === $value || null === $value ) {
			return [];
		}

		return array_values( array_filter( array_map( 'trim', explode( ',', $value ) ), 'strlen' ) );
	}

	/**
	 * Check if a post with the given title and type already exists.
	 *
	 * @param string $title     Post title.
	 * @param string $post_type Post type slug.
	 *
	 * @return bool
	 */
	private static function post_exists( $title, $post_type ) {
		global $wpdb;

		return (bool) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
				$title,
				$post_type
			)
		);
	}

	/**
	 * Check if a post with the given title, type, and term names already exists.
	 *
	 * Falls back to title-only check when no terms are provided.
	 *
	 * @param string $title     Post title.
	 * @param string $post_type Post type slug.
	 * @param string $taxonomy  Taxonomy slug to check against.
	 * @param array  $terms     Term names to match.
	 *
	 * @return bool
	 */
	private static function post_exists_with_terms( $title, $post_type, $taxonomy, $terms = [] ) {
		if ( empty( $terms ) ) {
			return self::post_exists( $title, $post_type );
		}

		global $wpdb;

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
				$title,
				$post_type
			)
		);

		if ( ! $post_id ) {
			return false;
		}

		// Check all posts with this title for matching terms.
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status != 'trash'",
				$title,
				$post_type
			)
		);

		foreach ( $post_ids as $pid ) {
			$existing_terms = wp_get_object_terms( (int) $pid, $taxonomy, [ 'fields' => 'names' ] );
			if ( is_wp_error( $existing_terms ) ) {
				continue;
			}
			if ( array_intersect( $terms, $existing_terms ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Find a post ID by its title and post type.
	 *
	 * @param string $title     Post title.
	 * @param string $post_type Post type slug.
	 *
	 * @return int Post ID or 0.
	 */
	private static function find_post_id_by_title( $title, $post_type ) {
		global $wpdb;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
				$title,
				$post_type
			)
		);

		return $id ? (int) $id : 0;
	}

	/**
	 * Download an image from a URL and set it as the post's featured image.
	 *
	 * @param string $url     Image URL.
	 * @param int    $post_id Post ID to attach the image to.
	 */
	private static function sideload_featured_image( $url, $post_id ) {
		$url = esc_url_raw( $url );
		if ( empty( $url ) ) {
			return;
		}

		// Only allow HTTP(S) URLs.
		if ( ! preg_match( '/^https?:\/\//i', $url ) ) {
			return;
		}

		// Block requests to private/reserved IPs (SSRF protection).
		if ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $url ) ) {
			return;
		}

		// Ensure required functions are available.
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$attachment_id = media_sideload_image( $url, $post_id, null, 'id' );

		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	/**
	 * Build a standard result array.
	 *
	 * @param int    $imported Number of items imported.
	 * @param int    $skipped  Number of duplicates skipped.
	 * @param array  $errors   Error messages.
	 * @param string $label    Item type label (e.g. "classes").
	 *
	 * @return array
	 */
	private static function result( $imported, $skipped, $errors, $label ) {
		$parts = [];

		if ( $imported > 0 ) {
			/* translators: 1: count, 2: item type */
			$parts[] = sprintf( __( 'Imported %1$d %2$s.', 'gym-builder' ), $imported, $label );
		}

		if ( $skipped > 0 ) {
			/* translators: 1: count */
			$parts[] = sprintf( __( 'Skipped %1$d duplicates.', 'gym-builder' ), $skipped );
		}

		if ( ! empty( $errors ) ) {
			$parts[] = sprintf( __( '%d errors occurred.', 'gym-builder' ), count( $errors ) );
		}

		if ( empty( $parts ) ) {
			$parts[] = __( 'No items found to import.', 'gym-builder' );
		}

		return [
			'success'  => empty( $errors ),
			'imported' => $imported,
			'skipped'  => $skipped,
			'errors'   => $errors,
			'message'  => implode( ' ', $parts ),
		];
	}
}
