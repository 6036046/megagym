<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $trainer_classes array  trainer  classes
 */

use GymBuilder\Inc\Controllers\Models\GymBuilderClass;

?>
<div class="dashboard-card classes-card">
    <div class="card-header">
        <h2 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <?php esc_html_e( 'Your Classes', 'gym-builder' ); ?>
            <span class="class-count"><?php echo absint( count( $trainer_classes ) ); ?></span>
        </h2>
    </div>
    <div class="card-body">
        <?php if ( empty( $trainer_classes ) ): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h3><?php esc_html_e( 'No Classes Yet', 'gym-builder' ); ?></h3>
                <p><?php esc_html_e( 'You are not assigned to any classes yet.', 'gym-builder' ); ?></p>
            </div>
        <?php else: ?>
            <div class="classes-list">
                <?php foreach ( $trainer_classes as $class ): ?>
                    <div class="class-item">
                        <div class="class-header">
                            <h3 class="class-name"><?php echo esc_html( $class->post_title ); ?></h3>
                            <span class="class-status"><?php esc_html_e( 'Active', 'gym-builder' ); ?></span>
                        </div>

                        <?php if ( ! empty( $class->schedules ) ): ?>
                            <div class="class-schedules">
                                <?php foreach ( $class->schedules as $schedule ): ?>
                                    <div class="schedule-item">
                                        <div class="meta-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <span><?php echo esc_html( GymBuilderClass::get_day_name( $schedule['week'] ) ); ?></span>
                                        </div>

                                        <div class="meta-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <span>
                                                        <?php
                                                        printf(
                                                            '%s - %s',
                                                            esc_html( GymBuilderClass::format_time( $schedule['start_time'] ) ),
                                                            esc_html( GymBuilderClass::format_time( $schedule['end_time'] ) )
                                                        );
                                                        ?>
                                                    </span>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $class->post_content ) ): ?>
                            <div class="class-description">
                                <?php echo wp_kses_post( wp_trim_words( $class->post_content, 20 ) ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>