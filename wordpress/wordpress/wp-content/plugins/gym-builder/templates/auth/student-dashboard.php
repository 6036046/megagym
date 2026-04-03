<?php
/**
 * Student Dashboard Template
 *
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $wp_version
 */

use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$member_id    = absint( get_user_meta( $current_user->ID, 'gym_builder_student_member_id', true ) );

if ( ! $member_id ) {
    echo '<p style="margin-top: 50px; text-align:center;">' . esc_html__( 'Student profile not found. Please contact administrator.', 'gym-builder' ) . '</p>';
    return;
}

global $wpdb;
$table_name = $wpdb->prefix . 'gym_builder_members';
$member     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$table_name` WHERE id = %d", $member_id ) );

if ( ! $member ) {
    echo '<p style="margin-top: 50px; text-align:center;">' . esc_html__( 'Student profile not found. Please contact administrator.', 'gym-builder' ) . '</p>';
    return;
}

$member_name     = esc_html( $member->member_name );
$member_email    = esc_html( $member->member_email );
$member_phone    = esc_html( $member->member_phone );
$member_address  = esc_html( $member->member_address );
$member_age      = $member->member_age ? intval( $member->member_age ) : '';
$member_gender   = $member->member_gender ? esc_html( ucfirst( $member->member_gender ) ) : '';
$member_user_id  = esc_html( $member->member_user_id );
$file_url        = $member->file_url ? esc_url( $member->file_url ) : '';

$membership_status = intval( $member->membership_status );
$package_type      = esc_html( $member->membership_package_type );
$package_name      = esc_html( $member->membership_package_name );
$joining_date      = $member->member_joining_date;
$duration_start    = $member->membership_duration_start;
$duration_end      = $member->membership_duration_end;

$days_remaining = 0;
if ( $duration_end ) {
    $end_date       = new DateTime( $duration_end );
    $today          = new DateTime( 'today' );
    $diff           = $today->diff( $end_date );
    $days_remaining = $diff->invert ? 0 : $diff->days;
}

$class_id        = intval( $member->class_id );
$class_name      = '';
$schedule_weekday = $member->schedule_weekday ? esc_html( $member->schedule_weekday ) : '';
$schedule_time    = $member->schedule_time ? esc_html( $member->schedule_time ) : '';

if ( $class_id ) {
    $class_post = get_post( $class_id );
    if ( $class_post ) {
        $class_name = esc_html( $class_post->post_title );
    }
}
?>

<div class="gym-builder-student-dashboard">
    <!-- Dashboard Header -->
    <div class="sd-header">
        <div class="sd-header-content">
            <div class="sd-header-left">
                <h1 class="sd-title"><?php esc_html_e( 'Student Dashboard', 'gym-builder' ); ?></h1>
                <p class="sd-welcome">
                    <?php
                    printf(
                        esc_html__( 'Welcome back, %s', 'gym-builder' ),
                        '<strong>' . $member_name . '</strong>'
                    );
                    ?>
                </p>
            </div>
            <div class="sd-header-right">
                <div class="sd-status-badge sd-status-<?php echo $membership_status ? 'active' : 'inactive'; ?>">
                    <span class="sd-status-dot"></span>
                    <?php echo $membership_status ? esc_html__( 'Active', 'gym-builder' ) : esc_html__( 'Inactive', 'gym-builder' ); ?>
                </div>
                <?php do_action( 'gym_builder_student_dashboard_header_actions', $member, $current_user ); ?>
                <a href="<?php echo esc_url( wp_logout_url( GymTrainerHelpers::get_member_login_page_url() ) ); ?>" class="sd-btn-logout">
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

    <?php do_action( 'gym_builder_student_dashboard_before_grid', $member, $current_user ); ?>

    <div class="sd-grid">
        <!-- Profile Card -->
        <div class="sd-card sd-profile-card">
            <div class="sd-card-header">
                <h2 class="sd-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <?php esc_html_e( 'Profile Information', 'gym-builder' ); ?>
                </h2>
                <?php if ( $member_user_id ) : ?>
                    <span class="sd-member-id"><?php echo $member_user_id; ?></span>
                <?php endif; ?>
            </div>
            <div class="sd-card-body">
                <?php if ( $file_url ) : ?>
                    <div class="sd-profile-avatar">
                        <img src="<?php echo $file_url; ?>" alt="<?php echo $member_name; ?>">
                    </div>
                <?php endif; ?>

                <div class="sd-profile-grid">
                    <div class="sd-profile-item">
                        <span class="sd-profile-label"><?php esc_html_e( 'Full Name', 'gym-builder' ); ?></span>
                        <span class="sd-profile-value"><?php echo $member_name; ?></span>
                    </div>

                    <?php if ( $member_email ) : ?>
                        <div class="sd-profile-item">
                            <span class="sd-profile-label"><?php esc_html_e( 'Email', 'gym-builder' ); ?></span>
                            <span class="sd-profile-value"><?php echo $member_email; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $member_phone ) : ?>
                        <div class="sd-profile-item">
                            <span class="sd-profile-label"><?php esc_html_e( 'Phone', 'gym-builder' ); ?></span>
                            <span class="sd-profile-value"><?php echo $member_phone; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $member_age ) : ?>
                        <div class="sd-profile-item">
                            <span class="sd-profile-label"><?php esc_html_e( 'Age', 'gym-builder' ); ?></span>
                            <span class="sd-profile-value"><?php echo $member_age; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $member_gender ) : ?>
                        <div class="sd-profile-item">
                            <span class="sd-profile-label"><?php esc_html_e( 'Gender', 'gym-builder' ); ?></span>
                            <span class="sd-profile-value"><?php echo $member_gender; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $member_address ) : ?>
                        <div class="sd-profile-item sd-profile-full">
                            <span class="sd-profile-label"><?php esc_html_e( 'Address', 'gym-builder' ); ?></span>
                            <span class="sd-profile-value"><?php echo $member_address; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Membership Status Card -->
        <div class="sd-card sd-membership-card">
            <div class="sd-card-header">
                <h2 class="sd-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?php esc_html_e( 'Membership Status', 'gym-builder' ); ?>
                </h2>
            </div>
            <div class="sd-card-body">
                <div class="sd-membership-status-banner sd-status-<?php echo $membership_status ? 'active' : 'inactive'; ?>">
                    <div class="sd-status-icon">
                        <?php if ( $membership_status ) : ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        <?php else : ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="sd-status-info">
                        <span class="sd-status-text"><?php echo $membership_status ? esc_html__( 'Active Membership', 'gym-builder' ) : esc_html__( 'Inactive Membership', 'gym-builder' ); ?></span>
                        <?php if ( $days_remaining > 0 ) : ?>
                            <span class="sd-days-remaining">
                                <?php printf( esc_html__( '%d days remaining', 'gym-builder' ), $days_remaining ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sd-membership-details">
                    <?php if ( $package_type ) : ?>
                        <div class="sd-detail-item">
                            <span class="sd-detail-label"><?php esc_html_e( 'Package Type', 'gym-builder' ); ?></span>
                            <span class="sd-detail-value"><?php echo $package_type; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $package_name ) : ?>
                        <div class="sd-detail-item">
                            <span class="sd-detail-label"><?php esc_html_e( 'Package Name', 'gym-builder' ); ?></span>
                            <span class="sd-detail-value"><?php echo $package_name; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $joining_date ) : ?>
                        <div class="sd-detail-item">
                            <span class="sd-detail-label"><?php esc_html_e( 'Joining Date', 'gym-builder' ); ?></span>
                            <span class="sd-detail-value"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $joining_date ) ) ); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $duration_start && $duration_end ) : ?>
                        <div class="sd-detail-item">
                            <span class="sd-detail-label"><?php esc_html_e( 'Duration', 'gym-builder' ); ?></span>
                            <span class="sd-detail-value">
                                <?php
                                echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $duration_start ) ) );
                                echo ' &mdash; ';
                                echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $duration_end ) ) );
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $duration_end && $membership_status ) : ?>
                    <div class="sd-progress-section">
                        <?php
                        $total_days = 0;
                        $elapsed_days = 0;
                        if ( $duration_start && $duration_end ) {
                            $start = new DateTime( $duration_start );
                            $end   = new DateTime( $duration_end );
                            $now   = new DateTime( 'today' );
                            $total_days   = max( 1, $start->diff( $end )->days );
                            $elapsed_days = max( 0, $start->diff( $now )->days );
                            $elapsed_days = min( $elapsed_days, $total_days );
                        }
                        $progress = $total_days > 0 ? round( ( $elapsed_days / $total_days ) * 100 ) : 0;
                        $progress_display = $elapsed_days > 0 ? $progress : 2;
                        ?>
                        <div class="sd-progress-bar">
                            <div class="sd-progress-fill" style="width: <?php echo esc_attr( $progress_display ); ?>%;"></div>
                        </div>
                        <div class="sd-progress-labels">
                            <span><?php printf( esc_html__( '%d%% elapsed', 'gym-builder' ), $progress ); ?></span>
                            <span><?php printf( esc_html__( '%d days left', 'gym-builder' ), $days_remaining ); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Classes Card -->
        <?php if ( $class_id && $class_name ) : ?>
            <div class="sd-card sd-classes-card">
                <div class="sd-card-header">
                    <h2 class="sd-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <?php esc_html_e( 'My Class', 'gym-builder' ); ?>
                    </h2>
                </div>
                <div class="sd-card-body">
                    <div class="sd-class-item">
                        <div class="sd-class-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        <div class="sd-class-info">
                            <h3 class="sd-class-name"><?php echo $class_name; ?></h3>
                            <div class="sd-class-meta">
                                <?php if ( $schedule_weekday ) : ?>
                                    <span class="sd-class-day">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <?php echo $schedule_weekday; ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( $schedule_time ) : ?>
                                    <span class="sd-class-time">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo $schedule_time; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $day_map = array(
                        'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
                        'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7,
                    );
                    $current_day_num = intval( date( 'N' ) );
                    $class_day_num   = isset( $day_map[ $schedule_weekday ] ) ? $day_map[ $schedule_weekday ] : 0;

                    if ( $class_day_num > 0 ) :
                        $days_until = $class_day_num - $current_day_num;
                        if ( $days_until < 0 ) {
                            $days_until += 7;
                        }
                        if ( $days_until === 0 ) {
                            $next_class_text = __( 'Today', 'gym-builder' );
                        } elseif ( $days_until === 1 ) {
                            $next_class_text = __( 'Tomorrow', 'gym-builder' );
                        } else {
                            $next_date = new DateTime();
                            $next_date->modify( "+{$days_until} days" );
                            $next_class_text = sprintf(
                                __( 'In %d days (%s)', 'gym-builder' ),
                                $days_until,
                                date_i18n( 'M j', $next_date->getTimestamp() )
                            );
                        }
                        ?>
                        <div class="sd-next-class">
                            <span class="sd-next-label"><?php esc_html_e( 'Next class:', 'gym-builder' ); ?></span>
                            <span class="sd-next-value <?php echo $days_until === 0 ? 'sd-today' : ''; ?>">
                                <?php echo esc_html( $next_class_text ); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php do_action( 'gym_builder_student_dashboard_after_cards', $member, $current_user ); ?>
    </div>

    <?php do_action( 'gym_builder_student_dashboard_after_grid', $member, $current_user ); ?>
</div>

