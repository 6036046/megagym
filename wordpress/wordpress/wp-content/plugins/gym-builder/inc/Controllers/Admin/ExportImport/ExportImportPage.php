<?php

namespace GymBuilder\Inc\Controllers\Admin\ExportImport;

use GymBuilder\Inc\Traits\SingleTonTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

class ExportImportPage {
	use SingleTonTrait;

	public function register() {
		add_action( 'admin_menu', [ $this, 'add_submenu' ], 23 );
	}

	public function add_submenu() {
		add_submenu_page(
			'gym_builder',
			esc_html__( 'Import / Export', 'gym-builder' ),
			esc_html__( 'Import / Export', 'gym-builder' ),
			'manage_options',
			'gym-builder-import-export',
			[ $this, 'render_page' ],
			80
		);
	}

	public function render_page() {
		$this->render_permalink_notice();
		echo '<div class="wrap"><div id="gym-builder-export-import-root"></div></div>';
	}

	private function render_permalink_notice() {
		if ( ! get_option( 'gym_builder_show_permalink_notice' ) ) {
			return;
		}

		if ( isset( $_GET['gb_flush_permalinks'] ) ) {
			check_admin_referer( 'gb_flush_permalinks' );
			flush_rewrite_rules();
			delete_option( 'gym_builder_show_permalink_notice' );
			$this->render_success_notice();
			return;
		}

		$permalink_structure = get_option( 'permalink_structure' );
		$is_postname         = '/%postname%/' === $permalink_structure;

		if ( $is_postname ) {
			$this->render_flush_notice();
		} else {
			$this->render_warning_notice();
		}
	}

	private function render_success_notice() {
		?>
		<div class="gb-permalink-notice gb-permalink-notice--success">
			<div class="gb-permalink-notice__icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
			</div>
			<div class="gb-permalink-notice__content">
				<h3 class="gb-permalink-notice__title"><?php esc_html_e( 'You\'re All Set!', 'gym-builder' ); ?></h3>
				<p class="gb-permalink-notice__text"><?php esc_html_e( 'Permalinks have been flushed successfully. Gym Builder is ready to use.', 'gym-builder' ); ?></p>
			</div>
		</div>
		<?php
		$this->render_permalink_styles();
	}

	private function render_flush_notice() {
		$flush_url = wp_nonce_url(
			admin_url( 'admin.php?page=gym-builder-import-export&gb_flush_permalinks=1' ),
			'gb_flush_permalinks'
		);
		?>
		<div class="gb-permalink-notice gb-permalink-notice--info">
			<div class="gb-permalink-notice__icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
			</div>
			<div class="gb-permalink-notice__content">
				<h3 class="gb-permalink-notice__title"><?php esc_html_e( 'Flush Permalinks', 'gym-builder' ); ?></h3>
				<p class="gb-permalink-notice__text"><?php esc_html_e( 'Your permalink structure is set to "Post name". Please flush the permalinks to ensure all Gym Builder pages (trainers, classes, packages) load correctly.', 'gym-builder' ); ?></p>
				<a href="<?php echo esc_url( $flush_url ); ?>" class="gb-permalink-notice__btn gb-permalink-notice__btn--info">
					<?php esc_html_e( 'Flush Permalinks', 'gym-builder' ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.2M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
				</a>
			</div>
		</div>
		<?php
		$this->render_permalink_styles();
	}

	private function render_warning_notice() {
		$permalink_url = esc_url( admin_url( 'options-permalink.php' ) );
		?>
		<div class="gb-permalink-notice gb-permalink-notice--warning">
			<div class="gb-permalink-notice__icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="12"></line><line x1="12" x2="12.01" y1="16" y2="16"></line></svg>
			</div>
			<div class="gb-permalink-notice__content">
				<h3 class="gb-permalink-notice__title"><?php esc_html_e( 'Permalink Setup Required', 'gym-builder' ); ?></h3>
				<p class="gb-permalink-notice__text"><?php esc_html_e( 'Please set your permalink structure to "Post name" otherwise trainer, class, and package pages will not be accessible.', 'gym-builder' ); ?></p>
				<a href="<?php echo $permalink_url; ?>" class="gb-permalink-notice__btn">
					<?php esc_html_e( 'Go to Permalink Settings', 'gym-builder' ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
				</a>
			</div>
		</div>
		<?php
		$this->render_permalink_styles();
	}

	private function render_permalink_styles() {
		static $rendered = false;
		if ( $rendered ) {
			return;
		}
		$rendered = true;
		?>
		<style>
			.gb-permalink-notice {
				display: flex;
				align-items: flex-start;
				gap: 16px;
				border-radius: 12px;
				padding: 20px 24px;
				margin: 20px 0;
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
			}
			.gb-permalink-notice--warning {
				background: linear-gradient(135deg, #fff7ed 0%, #fffbeb 100%);
				border: 1px solid #fed7aa;
			}
			.gb-permalink-notice--success {
				background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
				border: 1px solid #bbf7d0;
			}
			.gb-permalink-notice--info {
				background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
				border: 1px solid #bfdbfe;
			}
			.gb-permalink-notice__icon {
				flex-shrink: 0;
				width: 40px;
				height: 40px;
				border-radius: 10px;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
			}
			.gb-permalink-notice--warning .gb-permalink-notice__icon { background: #f97316; }
			.gb-permalink-notice--success .gb-permalink-notice__icon { background: #16a34a; }
			.gb-permalink-notice--info .gb-permalink-notice__icon { background: #2563eb; }
			.gb-permalink-notice__icon svg { width: 20px; height: 20px; }
			.gb-permalink-notice__content { flex: 1; min-width: 0; }
			.gb-permalink-notice__title {
				margin: 0 0 6px 0;
				font-size: 15px;
				font-weight: 600;
			}
			.gb-permalink-notice--warning .gb-permalink-notice__title { color: #9a3412; }
			.gb-permalink-notice--success .gb-permalink-notice__title { color: #166534; }
			.gb-permalink-notice--info .gb-permalink-notice__title { color: #1e40af; }
			.gb-permalink-notice__text {
				margin: 0 0 14px 0;
				font-size: 13px;
				line-height: 1.5;
			}
			.gb-permalink-notice--warning .gb-permalink-notice__text { color: #c2410c; }
			.gb-permalink-notice--success .gb-permalink-notice__text { color: #15803d; margin-bottom: 0; }
			.gb-permalink-notice--info .gb-permalink-notice__text { color: #1d4ed8; }
			.gb-permalink-notice__btn {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				padding: 8px 16px;
				background: #f97316;
				color: #fff;
				font-size: 13px;
				font-weight: 500;
				border-radius: 8px;
				text-decoration: none;
				transition: background 0.2s;
			}
			.gb-permalink-notice__btn:hover { background: #ea580c; color: #fff; }
			.gb-permalink-notice__btn:focus { color: #fff; }
			.gb-permalink-notice__btn--info { background: #2563eb; }
			.gb-permalink-notice__btn--info:hover { background: #1d4ed8; }
			.gb-permalink-notice__btn svg { width: 14px; height: 14px; }
		</style>
		<?php
	}
}
