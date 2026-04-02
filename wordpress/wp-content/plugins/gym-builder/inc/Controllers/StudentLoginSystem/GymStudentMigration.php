<?php
namespace GymBuilder\Inc\Controllers\StudentLoginSystem;

use GymBuilder\Inc\Controllers\TrainerLoginSystem\GymTrainerHelpers;
use GymBuilder\Inc\Traits\Constants;

class GymStudentMigration {

    use Constants;

    private $migration_option_key;
    private $installation_type_key;

    public function __construct($migration_key, $type_key) {
        $this->migration_option_key = $migration_key;
        $this->installation_type_key = $type_key;
    }

    public function detect_installation_type() {
        $installation_type = get_option($this->installation_type_key, false);
        if (!$installation_type) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'gym_builder_members';

            $existing_members = $wpdb->get_var(
                "SELECT COUNT(*) FROM `$table_name`"
            );

            if ($existing_members > 0) {
                $members_without_wp_user = $wpdb->get_var(
                    "SELECT COUNT(*) FROM `$table_name` WHERE wp_user_id IS NULL"
                );

                if ($members_without_wp_user > 0) {
                    update_option($this->installation_type_key, 'upgrade_with_data');
                    update_option($this->migration_option_key, false);
                } else {
                    update_option($this->installation_type_key, 'upgrade_with_data');
                    update_option($this->migration_option_key, true);
                }
            } else {
                update_option($this->installation_type_key, 'fresh_install');
                update_option($this->migration_option_key, true);
            }
        }
    }

    public function is_migration_completed() {
        return get_option($this->migration_option_key, false);
    }

    public function is_fresh_installation() {
        $installation_type = get_option($this->installation_type_key);
        return ($installation_type === 'fresh_install');
    }

    public function mark_migration_completed() {
        update_option($this->migration_option_key, true);
    }

    public function get_migration_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gym_builder_members';

        $total_members = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table_name`");

        $needs_migration = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM `$table_name`
             WHERE wp_user_id IS NULL
             AND member_email IS NOT NULL
             AND member_email != ''"
        );

        $already_migrated = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM `$table_name` WHERE wp_user_id IS NOT NULL"
        );

        $no_email = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM `$table_name`
             WHERE wp_user_id IS NULL
             AND (member_email IS NULL OR member_email = '')"
        );

        return array(
            'total_members'    => $total_members,
            'needs_migration'  => $needs_migration,
            'already_migrated' => $already_migrated,
            'no_email'         => $no_email,
        );
    }

    public function migrate_all_members() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Unauthorized access.', 'gym-builder'));
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gym_builder_student_migration_nonce')) {
            wp_send_json_error(__('Security check failed.', 'gym-builder'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'gym_builder_members';

        $members = $wpdb->get_results(
            "SELECT * FROM `$table_name`
             WHERE wp_user_id IS NULL
             AND member_email IS NOT NULL
             AND member_email != ''"
        );

        $success_count = 0;
        $skipped_count = 0;
        $failed_count = 0;
        $skipped_emails = array();

        $helpers = new GymTrainerHelpers();
        $email_class = new GymStudentEmail();

        foreach ($members as $member) {
            $email = sanitize_email($member->member_email);

            if (!is_email($email)) {
                $failed_count++;
                continue;
            }
            $existing_user_id = email_exists($email);
            if ($existing_user_id) {
                $existing_user = get_userdata($existing_user_id);
                if (in_array('gym_builder_student', (array) $existing_user->roles, true)) {
                    $wpdb->update(
                        $table_name,
                        array('wp_user_id' => $existing_user_id),
                        array('id' => $member->id),
                        array('%d'),
                        array('%d')
                    );
                    update_user_meta($existing_user_id, 'gym_builder_student_member_id', $member->id);
                    $success_count++;
                } else {
                    $skipped_count++;
                    $skipped_emails[] = $email;
                }
                continue;
            }

            $username = $helpers->generate_unique_username($email, $member->member_name);
            $password = wp_generate_password(12, true);

            $user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) {
                $failed_count++;
                continue;
            }

            $user = new \WP_User($user_id);
            $user->set_role('gym_builder_student');

            $wpdb->update(
                $table_name,
                array('wp_user_id' => $user_id),
                array('id' => $member->id),
                array('%d'),
                array('%d')
            );

            update_user_meta($user_id, 'gym_builder_student_member_id', $member->id);

            $user_data = array(
                'name'     => $member->member_name,
                'username' => $username,
                'password' => $password,
                'email'    => $email,
            );
            $email_class->send_login_credentials_email($user_data);

            $success_count++;
        }

        $remaining = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM `$table_name`
             WHERE wp_user_id IS NULL
             AND member_email IS NOT NULL
             AND member_email != ''"
        );

        if ($remaining === 0) {
            $this->mark_migration_completed();
        }

        wp_send_json_success(array(
            'success_count'  => $success_count,
            'skipped_count'  => $skipped_count,
            'failed_count'   => $failed_count,
            'skipped_emails' => $skipped_emails,
            'migration_complete' => ($remaining === 0),
        ));
    }

    public function migration_page() {
        $stats = $this->get_migration_stats();
        ?>
        <div class="gym-builder-student-migration-wrap">
            <div class="migration-header">
                <div class="migration-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h1><?php esc_html_e('Student Management', 'gym-builder'); ?></h1>
                    <p class="heading-info">
                        <?php esc_html_e('Migrate existing members to the new WordPress user account system for student login access.', 'gym-builder'); ?>
                    </p>
                </div>
            </div>

            <?php if ($stats['needs_migration'] > 0) : ?>
                <div class="migration-alert migration-alert-info">
                    <div class="alert-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M3 5V19A9 3 0 0 0 21 19V5"></path>
                            <path d="M3 12A9 3 0 0 0 21 12"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <h3><?php esc_html_e('System Upgrade Detected', 'gym-builder'); ?></h3>
                        <p>
                            <?php
                            printf(
                                esc_html__('We\'ve detected %d members that can be migrated to WordPress user accounts. Once migrated, students can log in and view their dashboard.', 'gym-builder'),
                                intval($stats['needs_migration'])
                            );
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <h2 class="section-title"><?php esc_html_e('Migration Overview', 'gym-builder'); ?></h2>

            <div class="migration-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" x2="12" y1="8" y2="12"></line>
                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                        </svg>
                    </div>
                    <div class="stat-number" id="stat-needs-migration">
                        <?php echo intval($stats['needs_migration']); ?>
                    </div>
                    <div class="stat-label">
                        <?php esc_html_e('Students needing migration', 'gym-builder'); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="stat-number" id="stat-already-migrated">
                        <?php echo intval($stats['already_migrated']); ?>
                    </div>
                    <div class="stat-label">
                        <?php esc_html_e('Already migrated', 'gym-builder'); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-neutral">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                    </div>
                    <div class="stat-number" id="stat-no-email">
                        <?php echo intval($stats['no_email']); ?>
                    </div>
                    <div class="stat-label">
                        <?php esc_html_e('No email (cannot migrate)', 'gym-builder'); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-number">
                        <?php echo intval($stats['total_members']); ?>
                    </div>
                    <div class="stat-label">
                        <?php esc_html_e('Total members', 'gym-builder'); ?>
                    </div>
                </div>
            </div>

            <?php if ($stats['needs_migration'] > 0) : ?>
                <div class="migration-action-section">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h3><?php esc_html_e('Bulk Migration', 'gym-builder'); ?></h3>
                            <p><?php esc_html_e('Click the button below to create WordPress user accounts for all eligible members. Each student will receive an email with their login credentials.', 'gym-builder'); ?></p>
                            <button type="button" id="gym-builder-migrate-all-students" class="migration-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <line x1="19" y1="8" x2="19" y2="14"></line>
                                    <line x1="22" y1="11" x2="16" y2="11"></line>
                                </svg>
                                <?php
                                printf(
                                    esc_html__('Migrate All Students (%d)', 'gym-builder'),
                                    intval($stats['needs_migration'])
                                );
                                ?>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="migration-progress" style="display: none;">
                    <div class="migration-progress-card">
                        <div class="progress-spinner"></div>
                        <p class="progress-text"><?php esc_html_e('Migration in progress... Please do not close this page.', 'gym-builder'); ?></p>
                    </div>
                </div>

                <div id="migration-results" style="display: none;">
                    <div class="migration-results-card">
                        <div class="results-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3><?php esc_html_e('Migration Complete', 'gym-builder'); ?></h3>
                        <div class="results-details">
                            <p class="result-success"><span id="result-success-count">0</span> <?php esc_html_e('students migrated successfully', 'gym-builder'); ?></p>
                            <p class="result-skipped" style="display:none;"><span id="result-skipped-count">0</span> <?php esc_html_e('skipped (email already in use by another account)', 'gym-builder'); ?></p>
                            <p class="result-failed" style="display:none;"><span id="result-failed-count">0</span> <?php esc_html_e('failed', 'gym-builder'); ?></p>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        var migrateBtn = document.getElementById('gym-builder-migrate-all-students');
                        var progressEl = document.getElementById('migration-progress');
                        var resultsEl = document.getElementById('migration-results');

                        if (!migrateBtn) return;

                        migrateBtn.addEventListener('click', function() {
                            if (!confirm('<?php echo esc_js(__('Are you sure you want to migrate all students? This will create WordPress user accounts and send login credentials via email.', 'gym-builder')); ?>')) {
                                return;
                            }

                            migrateBtn.disabled = true;
                            migrateBtn.style.opacity = '0.6';
                            progressEl.style.display = 'block';
                            resultsEl.style.display = 'none';

                            var formData = new FormData();
                            formData.append('action', 'gym_builder_migrate_all_students');
                            formData.append('nonce', '<?php echo wp_create_nonce('gym_builder_student_migration_nonce'); ?>');

                            fetch(ajaxurl, {
                                method: 'POST',
                                body: formData,
                                credentials: 'same-origin'
                            })
                            .then(function(response) { return response.json(); })
                            .then(function(data) {
                                progressEl.style.display = 'none';

                                if (data.success) {
                                    resultsEl.style.display = 'block';
                                    document.getElementById('result-success-count').textContent = data.data.success_count;

                                    if (data.data.skipped_count > 0) {
                                        document.querySelector('.result-skipped').style.display = 'block';
                                        document.getElementById('result-skipped-count').textContent = data.data.skipped_count;
                                    }
                                    if (data.data.failed_count > 0) {
                                        document.querySelector('.result-failed').style.display = 'block';
                                        document.getElementById('result-failed-count').textContent = data.data.failed_count;
                                    }

                                    if (data.data.migration_complete) {
                                        migrateBtn.style.display = 'none';
                                    } else {
                                        migrateBtn.disabled = false;
                                        migrateBtn.style.opacity = '1';
                                    }
                                } else {
                                    alert(data.data || '<?php echo esc_js(__('Migration failed. Please try again.', 'gym-builder')); ?>');
                                    migrateBtn.disabled = false;
                                    migrateBtn.style.opacity = '1';
                                }
                            })
                            .catch(function(error) {
                                progressEl.style.display = 'none';
                                alert('<?php echo esc_js(__('An error occurred. Please try again.', 'gym-builder')); ?>');
                                migrateBtn.disabled = false;
                                migrateBtn.style.opacity = '1';
                            });
                        });
                    })();
                </script>
            <?php endif; ?>

            <?php if ($stats['no_email'] > 0) : ?>
                <div class="migration-alert migration-alert-notice">
                    <div class="alert-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 17V11" stroke-width="1.5" stroke-linecap="round"></path>
                            <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="currentColor"></circle>
                            <path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke-width="1.5"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <h3><?php esc_html_e('Members Without Email', 'gym-builder'); ?></h3>
                        <p>
                            <?php
                            printf(
                                esc_html__('%d members do not have an email address and cannot be migrated. To enable login for these members, please add their email address from the Members page.', 'gym-builder'),
                                intval($stats['no_email'])
                            );
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <style>
            .gym-builder-student-migration-wrap {
                max-width: 1200px;
                margin: 20px auto;
                background-color: #f9fafb;
                padding: 30px 50px 40px;
                border-radius: 12px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            }
            .migration-header {
                display: flex;
                align-items: center;
                gap: 20px;
                margin-bottom: 10px;
                text-align: left;
            }
            .migration-header-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .migration-header-icon svg {
                color: #fff;
            }
            .migration-header h1 {
                margin: 0 0 4px 0;
                font-size: 24px;
                font-weight: 700;
                color: #1a1a2e;
            }
            .migration-header .heading-info {
                margin: 0;
                color: #64748b;
                font-size: 15px;
            }
            .section-title {
                font-size: 18px;
                font-weight: 600;
                color: #1a1a2e;
                margin: 30px 0 20px;
            }
            .migration-alert {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                padding: 20px 24px;
                border-radius: 12px;
                margin: 24px 0;
            }
            .migration-alert-info {
                background-color: #eff6ff;
                border: 1px solid #bfdbfe;
            }
            .migration-alert-notice {
                background-color: #fffbeb;
                border: 1px solid #fde68a;
            }
            .migration-alert .alert-icon {
                width: 44px;
                height: 44px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .migration-alert-info .alert-icon {
                background-color: #dbeafe;
                color: #2563eb;
            }
            .migration-alert-notice .alert-icon {
                background-color: #fef3c7;
                color: #d97706;
            }
            .migration-alert .alert-content h3 {
                margin: 0 0 6px;
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
            }
            .migration-alert .alert-content p {
                margin: 0;
                font-size: 14px;
                color: #475569;
                line-height: 1.6;
            }
            .migration-stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
            @media (max-width: 1024px) {
                .migration-stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            .stat-card {
                background: #fff;
                padding: 24px;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
                transition: box-shadow 0.2s ease, transform 0.2s ease;
            }
            .stat-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                transform: translateY(-2px);
            }
            .stat-icon {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 16px;
            }
            .stat-icon svg {
                width: 22px;
                height: 22px;
                color: #fff;
            }
            .stat-icon-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
            .stat-icon-success { background: linear-gradient(135deg, #10b981, #059669); }
            .stat-icon-neutral { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
            .stat-icon-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
            .stat-number {
                font-size: 28px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 4px;
            }
            .stat-label {
                font-size: 14px;
                color: #64748b;
            }
            .migration-action-section {
                margin-top: 30px;
            }
            .action-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                overflow: hidden;
            }
            .action-card-body {
                padding: 28px;
            }
            .action-card-body h3 {
                margin: 0 0 8px;
                font-size: 18px;
                font-weight: 600;
                color: #1e293b;
            }
            .action-card-body p {
                margin: 0 0 20px;
                font-size: 14px;
                color: #64748b;
                line-height: 1.6;
            }
            .migration-btn {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 12px 28px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                border: none;
                border-radius: 10px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
            }
            .migration-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
            }
            .migration-btn:disabled {
                cursor: not-allowed;
                transform: none;
            }
            .migration-progress-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                padding: 40px;
                text-align: center;
                margin-top: 20px;
            }
            .progress-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #e2e8f0;
                border-top: 4px solid #667eea;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 16px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .progress-text {
                color: #64748b;
                font-size: 15px;
            }
            .migration-results-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #d1fae5;
                padding: 40px;
                text-align: center;
                margin-top: 20px;
            }
            .results-icon {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #d1fae5;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                color: #059669;
            }
            .migration-results-card h3 {
                margin: 0 0 16px;
                font-size: 20px;
                font-weight: 600;
                color: #059669;
            }
            .results-details p {
                margin: 6px 0;
                font-size: 15px;
                color: #475569;
            }
            .result-success span { font-weight: 700; color: #059669; }
            .result-skipped span { font-weight: 700; color: #d97706; }
            .result-failed span { font-weight: 700; color: #dc2626; }
        </style>
        <?php
    }
}
