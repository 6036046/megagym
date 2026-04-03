<?php
/**
 * Trainer Dashboard Template
 *
 * @package GymBuilder/Templates
 * @version 1.0.0
 * @var $wp_version
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\Models\GymBuilderClass;


defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();

$trainer_id = absint( get_user_meta( $current_user->ID, 'gym_builder_trainer_user_id', true ) );

if ( ! $trainer_id ) {
    echo '<p style="margin-top: 50px">' . esc_html__( 'Trainer profile not found. Please contact administrator.', 'gym-builder' ) . '</p>';
    return;
}

$trainer_post = get_post( $trainer_id );
if ( ! $trainer_post || $trainer_post->post_type !== Functions::$trainer_post_type ) {
    echo '<p style="margin-top: 50px">' . esc_html__( 'Trainer profile not found. Please contact administrator.', 'gym-builder' ) . '</p>';
    return;
}
if (  $trainer_post->post_status !== 'publish' ) {
    echo '<p style="margin-top: 50px">' . esc_html__( 'Your trainer profile is not approved yet.', 'gym-builder' ) . '</p>';
    return;
}

$trainer_designation = sanitize_text_field( get_post_meta( $trainer_id, 'gym_builder_trainer_designation', true ) );
$trainer_email = sanitize_email( get_post_meta( $trainer_id, 'gym_builder_trainer_email', true ) );
$trainer_socials = get_post_meta( $trainer_id, 'gym_builder_trainer_socials', true );
$trainer_skills = get_post_meta( $trainer_id, 'gym_builder_trainer_skill', true );
$trainer_status = sanitize_key( $trainer_post->post_status );

$trainer_classes = GymBuilderClass::get_trainer_classes( $trainer_id );
$today_classes = GymBuilderClass::get_today_trainer_classes( $trainer_id );
$weekly_summary = GymBuilderClass::get_trainer_weekly_summary( $trainer_id );
?>
<div class="gym-builder-trainer-dashboard">

        <?php
        $args =[
            'trainer_post' => $trainer_post,
            'trainer_status' => $trainer_status,
            'today_classes'   => $today_classes
        ];
        Functions::get_template('auth/trainer-dashboard-header',$args);
        ?>

        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card profile-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <?php esc_html_e( 'Your Profile', 'gym-builder' ); ?>
                    </h2>
                    <?php do_action('gym_builder_trainer_profile_editable_button',$trainer_id,$trainer_status); ?>
                </div>
                <div class="card-body">
                    <div class="profile-grid">
                        <div class="profile-item">
                            <span class="profile-label"><?php esc_html_e( 'Full Name', 'gym-builder' ); ?></span>
                            <span class="profile-value"><?php echo esc_html( $trainer_post->post_title ); ?></span>
                        </div>

                        <?php if ( ! empty( $trainer_designation ) ): ?>
                            <div class="profile-item">
                                <span class="profile-label"><?php esc_html_e( 'Designation', 'gym-builder' ); ?></span>
                                <span class="profile-value"><?php echo esc_html( $trainer_designation ); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="profile-item">
                            <span class="profile-label"><?php esc_html_e( 'Email', 'gym-builder' ); ?></span>
                            <span class="profile-value"><?php echo esc_html( $trainer_email ); ?></span>
                        </div>

                        <?php if ( ! empty( $trainer_post->post_content ) ): ?>
                            <div class="profile-item full-width">
                                <span class="profile-label"><?php esc_html_e( 'Bio', 'gym-builder' ); ?></span>
                                <div class="profile-bio"><?php echo wp_kses_post( $trainer_post->post_content ); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $trainer_skills ) && is_array( $trainer_skills ) ): ?>
                            <div class="profile-item full-width">
                                <span class="profile-label"><?php esc_html_e( 'Skills', 'gym-builder' ); ?></span>
                                <div class="skills-list">
                                    <?php foreach ( $trainer_skills as $skill ):
                                        if ( empty( $skill['skill_name'] ) ) continue;
                                        $skill_name = sanitize_text_field( $skill['skill_name'] );
                                        $skill_value = isset( $skill['skill_value'] ) ? absint( $skill['skill_value'] ) : 0;
                                        $skill_value = min( $skill_value, 100 ); // Cap at 100%
                                        ?>
                                        <div class="skill-item">
                                            <div class="skill-header">
                                                <span class="skill-name"><?php echo esc_html( $skill_name ); ?></span>
                                                <span class="skill-percentage"><?php echo esc_html( $skill_value ); ?>%</span>
                                            </div>
                                            <div class="skill-bar">
                                                <div class="skill-progress" style="width: <?php echo esc_attr( $skill_value ); ?>%;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $trainer_socials ) && is_array( $trainer_socials ) ):
                            $has_socials = false;
                            foreach ( $trainer_socials as $social ) {
                                if ( ! empty( $social ) ) {
                                    $has_socials = true;
                                    break;
                                }
                            }

                            if ( $has_socials ):
                                ?>
                                <div class="profile-item full-width">
                                    <span class="profile-label"><?php esc_html_e( 'Social Links', 'gym-builder' ); ?></span>
                                    <div class="social-links">
                                        <?php if ( ! empty( $trainer_socials['facebook'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link facebook" title="Facebook">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['twitter'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link twitter" title="Twitter">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['linkedin'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link linkedin" title="LinkedIn">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['instagram'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link instagram" title="Instagram">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['youtube'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['youtube'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link youtube" title="YouTube">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['tiktok'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['tiktok'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link tiktok" title="TikTok">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['pinterest'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['pinterest'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link pinterest" title="Pinterest">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $trainer_socials['skype'] ) ): ?>
                                            <a href="<?php echo esc_url( $trainer_socials['skype'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-link skype" title="Skype">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12.069 18.874c-4.023 0-5.82-1.979-5.82-3.464 0-.765.561-1.296 1.333-1.296 1.723 0 1.273 2.477 4.487 2.477 1.641 0 2.55-.895 2.55-1.811 0-.551-.269-1.16-1.354-1.429l-3.576-.895c-2.88-.724-3.403-2.286-3.403-3.751 0-3.047 2.861-4.191 5.549-4.191 2.471 0 5.393 1.373 5.393 3.199 0 .784-.688 1.24-1.453 1.24-1.469 0-1.198-2.037-4.164-2.037-1.469 0-2.292.664-2.292 1.617s1.153 1.258 2.157 1.487l2.637.587c2.891.649 3.624 2.346 3.624 3.944 0 2.476-1.902 4.324-5.722 4.324m11.084-4.882l-.029.135-.044-.24c.015.045.044.074.059.12.12-.675.181-1.363.181-2.052 0-1.529-.301-3.012-.898-4.42-.569-1.348-1.395-2.562-2.427-3.596-1.049-1.033-2.247-1.856-3.595-2.426-1.318-.631-2.801-.931-4.328-.931-.72 0-1.444.07-2.143.204l.119.06-.239-.033.119-.025C8.91.274 7.829 0 6.731 0c-1.789 0-3.47.698-4.736 1.967C.729 3.235.032 4.923.032 6.716c0 1.143.292 2.265.844 3.258l.02-.124.041.239-.06-.115c-.114.645-.172 1.299-.172 1.955 0 1.53.3 3.017.884 4.416.568 1.362 1.378 2.576 2.427 3.609 1.034 1.05 2.247 1.857 3.595 2.442 1.394.6 2.877.915 4.328.915.737 0 1.455-.074 2.143-.221l-.119-.062.239.046-.135.025c1.064.401 2.156.601 3.239.601 1.789 0 3.47-.697 4.736-1.982 1.267-1.267 1.964-2.955 1.964-4.749 0-1.097-.27-2.186-.78-3.149"/>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; endif; ?>
                    </div>
                </div>
            </div>

            <?php
            $args =[
                'trainer_classes' => $trainer_classes,
            ];
            Functions::get_template('auth/trainer-classes',$args);
            ?>

            <?php
            $args =[
                'trainer_classes' => $trainer_classes,
                'trainer_skills'=>$trainer_skills,
                'today_classes' => $today_classes,
                'weekly_summary' => $weekly_summary
            ];
            Functions::get_template('auth/trainer-stats',$args);

            ?>
        </div>
        <?php
        $args =[
            'weekly_summary' => $weekly_summary
        ];
        Functions::get_template('auth/trainer-weekly-summary',$args);
        ?>
    </div>