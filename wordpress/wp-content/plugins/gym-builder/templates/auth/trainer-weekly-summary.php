<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $weekly_summary array weekly summary
 */

use GymBuilder\Inc\Controllers\Models\GymBuilderClass;

?>
<!-- Weekly Schedule Summary -->
<?php if ( ! empty( $weekly_summary ) && array_sum( $weekly_summary ) > 0 ): ?>
    <div class="weekly-summary-card">
        <div class="card-header">
            <h2 class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <?php esc_html_e( 'Weekly Schedule Overview', 'gym-builder' ); ?>
            </h2>
        </div>
        <div class="card-body">
            <div class="weekly-summary-grid">
                <?php
                $days_order = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
                $current_day = strtolower( current_time( 'D' ) );

                foreach ( $days_order as $day ):
                    $count = isset( $weekly_summary[ $day ] ) ? absint( $weekly_summary[ $day ] ) : 0;
                    $is_today = ( $day === $current_day );
                    ?>
                    <div class="weekly-day-item <?php echo $is_today ? 'is-today' : ''; ?>">
                        <div class="day-name"><?php echo esc_html( GymBuilderClass::get_day_name( $day ) ); ?></div>
                        <div class="day-count">
                            <?php if ( $count > 0 ): ?>
                                <span class="count-number"><?php echo esc_html( $count ); ?></span>
                                <span class="count-label">
                                            <?php echo esc_html( _n( 'class', 'classes', $count, 'gym-builder' ) ); ?>
                                        </span>
                            <?php else: ?>
                                <span class="count-empty">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>