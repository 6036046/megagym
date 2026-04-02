<?php

namespace GymBuilder\Inc\Controllers\Admin\ExportImport;

use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class ExportImportApi {
	use SingleTonTrait;

	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		$namespace = 'gym-builder/v1';

		// Export endpoints.
		register_rest_route( $namespace, '/export/classes', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'export_classes' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		register_rest_route( $namespace, '/export/trainers', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'export_trainers' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		register_rest_route( $namespace, '/export/packages', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'export_packages' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		// Import endpoints.
		register_rest_route( $namespace, '/import/classes', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'import_classes' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		register_rest_route( $namespace, '/import/trainers', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'import_trainers' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		register_rest_route( $namespace, '/import/packages', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'import_packages' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		// Universal import endpoint — auto-detects type from CSV headers.
		register_rest_route( $namespace, '/import', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'import_auto' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		// Demo data endpoints.
		register_rest_route( $namespace, '/demo-data/list', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'list_demo_data' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		register_rest_route( $namespace, '/demo-data/import', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'import_demo_data' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );

		// Counts endpoint for export cards.
		register_rest_route( $namespace, '/export-import/counts', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_counts' ],
			'permission_callback' => [ $this, 'admin_permission' ],
		] );
	}

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	/* ─── Export handlers ─── */

	public function export_classes( $request ) {
		$format         = sanitize_text_field( $request->get_param( 'format' ) ?: 'json' );
		$include_images = rest_sanitize_boolean( $request->get_param( 'include_images' ) );
		$data           = Exporter::export_classes( $include_images );

		if ( 'csv' === $format ) {
			return rest_ensure_response( [
				'csv'      => Exporter::to_csv( $data ),
				'filename' => 'classes-' . gmdate( 'Y-m-d' ) . '.csv',
			] );
		}

		return rest_ensure_response( $data );
	}

	public function export_trainers( $request ) {
		$format         = sanitize_text_field( $request->get_param( 'format' ) ?: 'json' );
		$include_images = rest_sanitize_boolean( $request->get_param( 'include_images' ) );
		$data           = Exporter::export_trainers( $include_images );

		if ( 'csv' === $format ) {
			return rest_ensure_response( [
				'csv'      => Exporter::to_csv( $data ),
				'filename' => 'trainers-' . gmdate( 'Y-m-d' ) . '.csv',
			] );
		}

		return rest_ensure_response( $data );
	}

	public function export_packages( $request ) {
		$format         = sanitize_text_field( $request->get_param( 'format' ) ?: 'json' );
		$include_images = rest_sanitize_boolean( $request->get_param( 'include_images' ) );
		$data           = Exporter::export_packages( $include_images );

		if ( 'csv' === $format ) {
			return rest_ensure_response( [
				'csv'      => Exporter::to_csv( $data ),
				'filename' => 'packages-' . gmdate( 'Y-m-d' ) . '.csv',
			] );
		}

		return rest_ensure_response( $data );
	}

	/* ─── Import handlers ─── */

	public function import_classes( $request ) {
		$data = $this->extract_import_data( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$include_images = ! empty( $data['include_images'] );

		// If CSV auto-detected a different type, route to the correct importer.
		if ( isset( $data['type'] ) && 'gym_builder_classes' !== $data['type'] ) {
			return $this->route_by_type( $data, $include_images );
		}

		if ( empty( $data['items'] ) ) {
			return new \WP_Error( 'invalid_data', __( 'No items found in the uploaded file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$result = Importer::import_classes( $data, $include_images );

		return rest_ensure_response( $result );
	}

	public function import_trainers( $request ) {
		$data = $this->extract_import_data( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$include_images = ! empty( $data['include_images'] );

		// If CSV auto-detected a different type, route to the correct importer.
		if ( isset( $data['type'] ) && 'gym_builder_trainers' !== $data['type'] ) {
			return $this->route_by_type( $data, $include_images );
		}

		if ( empty( $data['items'] ) ) {
			return new \WP_Error( 'invalid_data', __( 'No items found in the uploaded file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$result = Importer::import_trainers( $data, $include_images );

		return rest_ensure_response( $result );
	}

	public function import_packages( $request ) {
		$data = $this->extract_import_data( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$include_images = ! empty( $data['include_images'] );

		// If CSV auto-detected a different type, route to the correct importer.
		if ( isset( $data['type'] ) && 'gym_builder_packages' !== $data['type'] ) {
			return $this->route_by_type( $data, $include_images );
		}

		if ( empty( $data['items'] ) ) {
			return new \WP_Error( 'invalid_data', __( 'No items found in the uploaded file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$result = Importer::import_packages( $data, $include_images );

		return rest_ensure_response( $result );
	}

	/**
	 * Universal import endpoint — auto-detects type from CSV/JSON and routes accordingly.
	 */
	public function import_auto( $request ) {
		$data = $this->extract_import_data( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$include_images = ! empty( $data['include_images'] );

		if ( empty( $data['items'] ) ) {
			return new \WP_Error( 'invalid_data', __( 'No items found in the uploaded file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		return $this->route_by_type( $data, $include_images );
	}

	/**
	 * Route import data to the correct importer based on the detected type.
	 *
	 * @param array $data           Parsed import data with 'type' and 'items'.
	 * @param bool  $include_images Whether to download and set featured images.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	private function route_by_type( $data, $include_images ) {
		if ( empty( $data['items'] ) ) {
			return new \WP_Error( 'invalid_data', __( 'No items found in the uploaded file.', 'gym-builder' ), [ 'status' => 400 ] );
		}

		$type = $data['type'] ?? '';

		switch ( $type ) {
			case 'gym_builder_classes':
				$result = Importer::import_classes( $data, $include_images );
				break;
			case 'gym_builder_trainers':
				$result = Importer::import_trainers( $data, $include_images );
				break;
			case 'gym_builder_packages':
				$result = Importer::import_packages( $data, $include_images );
				break;
			default:
				return new \WP_Error(
					'unknown_type',
					__( 'Could not determine the data type. Ensure the file was exported from Gym Builder.', 'gym-builder' ),
					[ 'status' => 400 ]
				);
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Extract import data from request, handling both JSON and CSV formats.
	 *
	 * @param \WP_REST_Request $request The request object.
	 *
	 * @return array|\WP_Error Import data array or error.
	 */
	private function extract_import_data( $request ) {
		$data           = $request->get_json_params();
		$include_images = ! empty( $data['include_images'] );

		// Handle CSV format.
		if ( ! empty( $data['csv'] ) ) {
			if ( ! is_string( $data['csv'] ) || strlen( $data['csv'] ) > 10 * 1024 * 1024 ) {
				return new \WP_Error( 'invalid_csv', __( 'Invalid or oversized CSV data.', 'gym-builder' ), [ 'status' => 400 ] );
			}

			$parsed = Importer::parse_csv( $data['csv'] );
			if ( is_wp_error( $parsed ) ) {
				return $parsed;
			}

			$parsed['include_images'] = $include_images;

			return $parsed;
		}

		return $data;
	}

	/* ─── Demo data handlers ─── */

	public function list_demo_data() {
		$demo_path = $this->demo_data_path();
		$files     = [
			'classes'  => $demo_path . 'classes.json',
			'trainers' => $demo_path . 'trainers.json',
			'packages' => $demo_path . 'packages.json',
		];

		$result = [];
		foreach ( $files as $key => $file ) {
			if ( file_exists( $file ) ) {
				$content        = file_get_contents( $file );
				$data           = json_decode( $content, true );
				$result[ $key ] = [
					'available' => true,
					'count'     => isset( $data['items'] ) ? count( $data['items'] ) : 0,
				];
			} else {
				$result[ $key ] = [
					'available' => false,
					'count'     => 0,
				];
			}
		}

		return rest_ensure_response( $result );
	}

	public function import_demo_data( $request ) {
		$type           = sanitize_text_field( $request->get_param( 'type' ) ?: 'all' );
		$include_images = rest_sanitize_boolean( $request->get_param( 'include_images' ) );
		$demo_path      = $this->demo_data_path();
		$results        = [];

		$type_map = [
			'packages' => [ 'file' => 'packages.json', 'importer' => 'import_packages' ],
			'trainers' => [ 'file' => 'trainers.json', 'importer' => 'import_trainers' ],
			'classes'  => [ 'file' => 'classes.json', 'importer' => 'import_classes' ],
		];

		$types_to_import = 'all' === $type ? array_keys( $type_map ) : [ $type ];

		foreach ( $types_to_import as $t ) {
			if ( ! isset( $type_map[ $t ] ) ) {
				continue;
			}

			$file = $demo_path . $type_map[ $t ]['file'];

			if ( ! file_exists( $file ) ) {
				$results[ $t ] = [
					'success'  => false,
					'imported' => 0,
					'skipped'  => 0,
					'errors'   => [ sprintf( __( 'Demo data file not found: %s', 'gym-builder' ), $type_map[ $t ]['file'] ) ],
					'message'  => __( 'Demo data file not found.', 'gym-builder' ),
				];
				continue;
			}

			$content = file_get_contents( $file );
			$data    = json_decode( $content, true );

			if ( ! $data || empty( $data['items'] ) ) {
				$results[ $t ] = [
					'success'  => false,
					'imported' => 0,
					'skipped'  => 0,
					'errors'   => [ __( 'Invalid or empty demo data file.', 'gym-builder' ) ],
					'message'  => __( 'Invalid demo data file.', 'gym-builder' ),
				];
				continue;
			}

			$method        = $type_map[ $t ]['importer'];
			$results[ $t ] = Importer::$method( $data, $include_images );
		}

		return rest_ensure_response( $results );
	}

	/* ─── Counts ─── */

	public function get_counts() {
		$types = [
			'classes'  => 'gym_builder_class',
			'trainers' => 'gym_builder_trainer',
			'packages' => 'gb_pricing_plan',
		];

		$counts = [];
		foreach ( $types as $key => $post_type ) {
			$count          = wp_count_posts( $post_type );
			$counts[ $key ] = isset( $count->publish ) ? (int) $count->publish : 0;
		}

		return rest_ensure_response( $counts );
	}

	/**
	 * Get the demo data directory path.
	 *
	 * @return string
	 */
	private function demo_data_path() {
		return plugin_dir_path( dirname( __FILE__, 4 ) ) . 'demo-data/';
	}
}