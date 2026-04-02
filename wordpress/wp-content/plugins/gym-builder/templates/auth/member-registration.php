<?php
/**
 * @package GymBuilder/Templates
 * @version 1.0.0
 */

use GymBuilder\Inc\Controllers\Helpers\Functions;
use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;

defined( 'ABSPATH' ) || exit;

Functions::get_header( $wp_version );
$auth_mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'login';
$login_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'trainer';
GymTrainerHelpers::redirect_to_respective_dashboard();
?>
    <div class="gym-builder-trainer-registration-container">
        <header class="gym-builder-header">
            <?php if ( apply_filters( 'gym_builder_show_page_title', true ) ) : ?>
                <h2 class="gym-builder-registration-header-title page-title"><?php Functions::page_title( true, 'trainer_auth' ); ?></h2>
            <?php endif; ?>
        </header>
        <div class="gym-builder-trainer-registration-page">
            <div class="auth-toggle">
                <label>
                    <input type="radio" name="auth_mode" value="login" <?php checked($auth_mode, 'login'); ?>>
                    <?php _e('Sign In', 'gym-builder'); ?>
                </label>
                <label>
                    <input type="radio" name="auth_mode" value="register" <?php checked($auth_mode, 'register'); ?>>
                    <?php _e('Sign Up', 'gym-builder'); ?>
                </label>
            </div>

            <div class="auth-forms">
                <!-- LOGIN FORM -->
                <div class="login-form" style="<?php echo $auth_mode === 'login' ? 'display: block;' : 'display: none;'; ?>">
                    <!-- Login Type Selection -->
                    <div class="login-type-toggle">
                        <label class="login-type-option">
                            <input type="radio" name="login_type" value="trainer" <?php checked($login_type, 'trainer'); ?>>
                            <span class="login-type-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <?php _e('Trainer', 'gym-builder'); ?>
                        </span>
                        </label>
                        <label class="login-type-option">
                            <input type="radio" name="login_type" value="student" <?php checked($login_type, 'student'); ?>>
                            <span class="login-type-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <?php _e('Student', 'gym-builder'); ?>
                        </span>
                        </label>
                    </div>

                    <form id="trainer-login-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <div class="form-group">
                            <label for="trainer-email"><?php _e('Email', 'gym-builder'); ?></label>
                            <input type="email" id="trainer-email" name="trainer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="trainer-password"><?php _e('Password', 'gym-builder'); ?></label>
                            <input type="password" id="trainer-password" name="trainer_password" required>
                        </div>
                        <input type="hidden" name="user_type" id="login-user-type" value="trainer">
                        <button type="submit"><?php _e('Sign In', 'gym-builder'); ?></button>
                        <input type="hidden" name="action" value="gym_trainer_login">
                    </form>
                </div>

                <!-- REGISTER FORM (TRAINER ONLY) -->
                <div class="register-form" style="<?php echo $auth_mode === 'register' ? 'display: block;' : 'display: none;'; ?>">
                    <!-- Info Message -->
                    <div class="register-info-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span><?php _e('Trainer registration only. Students cannot register through this form.', 'gym-builder'); ?></span>
                    </div>

                    <form id="trainer-register-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <div class="form-group">
                            <label for="register-name"><?php _e('Full Name', 'gym-builder'); ?> <span class="required">*</span></label>
                            <input type="text" id="register-name" name="trainer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email"><?php _e('Email', 'gym-builder'); ?> <span class="required">*</span></label>
                            <input type="email" id="register-email" name="trainer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="register-password"><?php _e('Password', 'gym-builder'); ?> <span class="required">*</span></label>
                            <input type="password" id="register-password" name="trainer_password" required>
                        </div>

                        <!-- Additional Trainer Information (Optional) -->
                        <div class="form-group">
                            <label for="register-designation"><?php _e('Designation', 'gym-builder'); ?></label>
                            <input type="text" id="register-designation" name="trainer_designation" placeholder="<?php esc_attr_e('e.g. Personal Trainer, Yoga Instructor', 'gym-builder'); ?>">
                        </div>

                        <!-- Social Links Section -->
                        <div class="form-section-header">
                            <h4><?php _e('Social Links', 'gym-builder'); ?> <span class="optional-text">(<?php _e('Optional', 'gym-builder'); ?>)</span></h4>
                        </div>

                        <div class="social-links-grid">
                            <div class="form-group">
                                <label for="register-facebook"><?php _e('Facebook', 'gym-builder'); ?></label>
                                <input type="url" id="register-facebook" name="trainer_social_facebook" placeholder="https://facebook.com/username">
                            </div>

                            <div class="form-group">
                                <label for="register-twitter"><?php _e('Twitter', 'gym-builder'); ?></label>
                                <input type="url" id="register-twitter" name="trainer_social_twitter" placeholder="https://twitter.com/username">
                            </div>

                            <div class="form-group">
                                <label for="register-linkedin"><?php _e('LinkedIn', 'gym-builder'); ?></label>
                                <input type="url" id="register-linkedin" name="trainer_social_linkedin" placeholder="https://linkedin.com/in/username">
                            </div>

                            <div class="form-group">
                                <label for="register-instagram"><?php _e('Instagram', 'gym-builder'); ?></label>
                                <input type="url" id="register-instagram" name="trainer_social_instagram" placeholder="https://instagram.com/username">
                            </div>

                            <div class="form-group">
                                <label for="register-youtube"><?php _e('YouTube', 'gym-builder'); ?></label>
                                <input type="url" id="register-youtube" name="trainer_social_youtube" placeholder="https://youtube.com/@username">
                            </div>

                            <div class="form-group">
                                <label for="register-tiktok"><?php _e('TikTok', 'gym-builder'); ?></label>
                                <input type="url" id="register-tiktok" name="trainer_social_tiktok" placeholder="https://tiktok.com/@username">
                            </div>

                            <div class="form-group">
                                <label for="register-pinterest"><?php _e('Pinterest', 'gym-builder'); ?></label>
                                <input type="url" id="register-pinterest" name="trainer_social_pinterest" placeholder="https://pinterest.com/username">
                            </div>

                            <div class="form-group">
                                <label for="register-skype"><?php _e('Skype', 'gym-builder'); ?></label>
                                <input type="text" id="register-skype" name="trainer_social_skype" placeholder="your-skype-id">
                            </div>
                        </div>

                        <!-- Skills Section -->
                        <div class="form-section-header">
                            <h4><?php _e('Skills', 'gym-builder'); ?> <span class="optional-text">(<?php _e('Optional', 'gym-builder'); ?>)</span></h4>
                            <button type="button" class="btn-add-skill"><?php _e('+ Add Skill', 'gym-builder'); ?></button>
                        </div>

                        <div id="skills-container" class="skills-container"></div>

                        <button type="submit"><?php _e('Sign Up as Trainer', 'gym-builder'); ?></button>
                        <input type="hidden" name="action" value="gym_trainer_register">
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php

Functions::get_footer( $wp_version );