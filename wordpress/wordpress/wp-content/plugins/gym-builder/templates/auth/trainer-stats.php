<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $trainer_classes int  trainer total class
 * @var $trainer_skills array trainer status
 * @var $today_classes array total today classes
 * @var $weekly_summary array weekly summary
 */
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label"><?php esc_html_e( 'Total Classes', 'gym-builder' ); ?></p>
            <p class="stat-value"><?php echo absint( count( $trainer_classes ) ); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label"><?php esc_html_e( 'Today\'s Classes', 'gym-builder' ); ?></p>
            <p class="stat-value"><?php echo absint( count( $today_classes ) ); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label"><?php esc_html_e( 'Total Skills', 'gym-builder' ); ?></p>
            <p class="stat-value"><?php echo absint( is_array( $trainer_skills ) ? count( $trainer_skills ) : 0 ); ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label"><?php esc_html_e( 'Weekly Sessions', 'gym-builder' ); ?></p>
            <p class="stat-value"><?php echo absint( array_sum( $weekly_summary ) ); ?></p>
        </div>
    </div>
</div>