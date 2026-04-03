<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $trainer_post object trainer post
 * @var $trainer_status string trainer status
 * @var $today_classes array trainer today classes
 */

use GymBuilder\Inc\Controllers\Models\GymBuilderClass;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;

?>
<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="dashboard-title"><?php esc_html_e( 'Trainer Dashboard', 'gym-builder' ); ?></h1>
            <p class="welcome-text">
                <?php
                printf(
                /* translators: %s: trainer name */
                    esc_html__( 'Welcome back, %s', 'gym-builder' ),
                    '<strong>' . esc_html( $trainer_post->post_title ) . '</strong>'
                );
                ?>
            </p>
        </div>
        <div class="header-right">
            <div class="status-badge status-<?php echo esc_attr( $trainer_status ); ?>">
                <span class="status-dot"></span>
                <?php echo esc_html( ucfirst( $trainer_status ) ); ?>
            </div>
            <a href="<?php echo esc_url( wp_logout_url( GymTrainerHelpers::get_member_login_page_url() ) ); ?>" class="btn-logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <?php esc_html_e( 'Logout', 'gym-builder' ); ?>
            </a>
        </div>
    </div>
</div>

<?php if ( $trainer_status === 'draft' ): ?>
    <div class="pending-notice">
        <div class="notice-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="notice-content">
            <h3><?php esc_html_e( 'Account Pending Approval', 'gym-builder' ); ?></h3>
            <p><?php esc_html_e( 'Your trainer account is currently pending admin approval. Once approved, you will be able to access all trainer features and be assigned to classes.', 'gym-builder' ); ?></p>
        </div>
    </div>
<?php endif; ?>
<?php if ( ! empty( $today_classes ) ): ?>
    <div class="today-schedule-section">
        <div class="today-schedule-header">
            <h2 class="today-schedule-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <?php
                printf(
                /* translators: %s: current day name */
                    esc_html__( "Today's Schedule - %s", 'gym-builder' ),
                    esc_html( date_i18n( 'l, F j, Y' ) )
                );
                ?>
            </h2>
        </div>
        <div class="today-schedule-list">
            <?php foreach ( $today_classes as $class ): ?>
                <div class="today-class-item">
                    <div class="today-class-time">
                        <span class="time-start"><?php echo esc_html( GymBuilderClass::format_time( $class->schedule['start_time'] ) ); ?></span>
                        <span class="time-separator">-</span>
                        <span class="time-end"><?php echo esc_html( GymBuilderClass::format_time( $class->schedule['end_time'] ) ); ?></span>
                    </div>
                    <div class="today-class-info">
                        <h3 class="today-class-name"><?php echo esc_html( $class->post_title ); ?></h3>
                        <?php if ( $class->schedule['max_members'] > 0 ): ?>
                            <span class="today-class-capacity">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <?php
                                    printf(
                                    /* translators: %d: number of people */
                                        esc_html( _n( '%d person', '%d people', $class->schedule['max_members'], 'gym-builder' ) ),
                                        absint( $class->schedule['max_members'] )
                                    );
                                    ?>
                                </span>
                        <?php endif; ?>
                    </div>
                    <div class="today-class-status">
                        <span class="status-dot"></span>
                        <?php esc_html_e( 'Upcoming', 'gym-builder' ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
