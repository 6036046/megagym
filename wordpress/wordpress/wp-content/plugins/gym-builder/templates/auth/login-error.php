<?php
/**
 * Trainer Dashboard Template
 *
 * @package GymBuilder/Templates
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;

defined( 'ABSPATH' ) || exit;
?>
<div class="trainer-dashboard-login-prompt">
    <div class="login-prompt-container">
        <div class="login-prompt-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
        </div>
        <h2 class="login-prompt-title"><?php esc_html_e( 'Access Required', 'gym-builder' ); ?></h2>
        <p class="login-prompt-text">
            <?php esc_html_e( 'Please log in to access your trainer or student dashboard.', 'gym-builder' ); ?>
        </p>
        <a href="<?php echo esc_url( GymTrainerHelpers::get_member_login_page_url() ); ?>" class="login-prompt-button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <?php esc_html_e( 'Go to Login', 'gym-builder' ); ?>
        </a>
    </div>
</div>
