<?php
namespace GymBuilder\Inc\Controllers\TrainerLoginSystem;
use GymBuilder\Inc\Traits\Constants;

class GymTrainerMigration {

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

            $existing_trainers = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish', 'draft')",
                    self::$trainer_post_type
                )
            );

            if ($existing_trainers > 0) {
                update_option($this->installation_type_key, 'upgrade_with_data');
                update_option($this->migration_option_key, false);
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
        $installation_type = get_option($this->installation_type_key, 'fresh_install');
        return ($installation_type === 'fresh_install');
    }

    public function mark_migration_completed() {
        update_option($this->migration_option_key, true);
    }

    public function set_default_migration_status($post_id, $post) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        if ($post->post_type !== self::$trainer_post_type) {
            return;
        }
        if ($post->post_status === 'auto-draft' || empty($post->post_title) || $post->post_title === 'Auto Draft') {
            return;
        }

        $existing_status = get_post_meta($post_id, 'gym_builder_trainer_migration_status', true);
        $existing_user_id = get_post_meta($post_id, 'gym_builder_trainer_user_id', true);
        $trainer_email = get_post_meta($post_id, 'gym_builder_trainer_email', true);
        $should_create_user = (
                (empty($existing_status) || $existing_status === 'pre_migration') &&
                empty($existing_user_id) &&
                !empty($trainer_email)
        );
        if ($should_create_user) {
            $user_id = $this->create_trainer_user_account($post_id);
            if ($user_id) {
                update_post_meta($post_id, 'gym_builder_trainer_migration_status', 'new_registration');
                update_post_meta($post_id, 'gym_builder_trainer_user_created', 1);
                update_post_meta($post_id, 'gym_builder_trainer_user_id', $user_id);
            } else {
                update_post_meta($post_id, 'gym_builder_trainer_migration_status', 'creation_failed');
                update_post_meta($post_id, 'gym_builder_trainer_user_created', 0);
            }
        }
    }
    private function create_trainer_user_account($trainer_id) {
        $trainer_email = get_post_meta($trainer_id, 'gym_builder_trainer_email', true);
        $trainer_name = get_the_title($trainer_id);
        if (empty($trainer_email)) {
            return false;
        }

        $helpers = new GymTrainerHelpers();
        $username = $helpers->generate_unique_username($trainer_email, $trainer_name);
        $password = wp_generate_password(12, true);

        $user_id = wp_create_user($username, $password, $trainer_email);

        if (is_wp_error($user_id)) {
            return false;
        }

        $user = new \WP_User($user_id);
        $user->set_role('gym_builder_trainer');

        update_user_meta($user_id, 'gym_builder_trainer_user_id', $trainer_id);

        $email_class = new GymTrainerEmail();
        $user_data = [
                'name' => $trainer_name,
                'username' => $username,
                'password' => $password,
                'email' => $trainer_email
        ];
        $sent = $email_class->send_login_credentials_email($user_data);
        if ($sent) {
            update_user_meta($user_id, 'gym_builder_trainer_approval_status', true);
            update_post_meta( $trainer_id, 'gym_builder_approval_email_status', 'sent' );
        }
        return $user_id;
    }

    public function migration_page() {

        global $wpdb;

        $trainers_without_users = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p
                 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'gym_builder_trainer_user_id'
                 LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'gym_builder_trainer_migration_status'
                 WHERE p.post_type = %s 
                 AND p.post_status IN ('publish', 'draft')
                 AND (pm.meta_value = '' OR pm.meta_value IS NULL OR pm.meta_value = '0') 
                 AND (pm2.meta_value IS NULL OR pm2.meta_value = '' OR pm2.meta_value = 'pre_migration')",
                    self::$trainer_post_type
            )
        );


        $new_trainers = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'gym_builder_trainer_migration_status'
                 WHERE p.post_type = %s 
                 AND p.post_status IN ('publish', 'draft')
                 AND pm.meta_value IN ('new_registration', 'admin_created')",
                    self::$trainer_post_type
            )
        );

        $registration_draft_trainers = $wpdb->get_var(
            $wpdb->prepare(
                            "SELECT COUNT(*) 
                     FROM {$wpdb->posts} p
                     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'gym_builder_trainer_user_id'
                     INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'gym_builder_approval_email_status'
                     WHERE p.post_type = %s
                     AND p.post_status = 'draft'
                     AND pm.meta_value != '' 
                     AND pm.meta_value IS NOT NULL 
                     AND pm.meta_value != '0'
                     AND pm2.meta_value = 'still_pending'"
                            , self::$trainer_post_type
                        )
        );

        ?>
        <div class="gym-builder-trainer-migration-wrap">
            <h1><?php esc_html_e( 'Trainer Management', 'gym-builder' ); ?></h1>
            <p class="heading-info">
                <?php esc_html_e( 'Manage trainers, and track their current status from here.', 'gym-builder' ); ?>
            </p>
            <?php if ($trainers_without_users > 0){ ?>
                <div class="migration-intro">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database h-4 w-4 text-blue-600" aria-hidden="true" data-replit-metadata="client/src/pages/migration.tsx:131:12" data-component-name="Database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5V19A9 3 0 0 0 21 19V5"></path><path d="M3 12A9 3 0 0 0 21 12"></path></svg>
                    </div>
                    <div class="content">
                        <h2 class="intro-heading"><?php esc_html_e('System Upgrade Detected','gym-builder'); ?></h2>
                        <?php
                        $text = sprintf(
                                __( "We've detected %d trainers that need to be migrated to the new WordPress user account system.", 'gym-builder' ),
                                intval($trainers_without_users)
                        );

                        echo esc_html( $text );
                        ?>
                    </div>
                </div>
            <?php } ?>
            <h2 class="status-title"><?php esc_html_e('Migration Status','gym-builder'); ?></h2>
            <div class="migration-stats">
                <div class="status-item">
                    <div class="icon needing-migration">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert w-5 h-5 text-amber-600" aria-hidden="true" data-replit-metadata="client/src/pages/migration.tsx:147:18" data-component-name="AlertCircle"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="12"></line><line x1="12" x2="12.01" y1="16" y2="16"></line></svg>
                    </div>
                    <div class="count">
                        <?php echo intval($trainers_without_users); ?>
                    </div>
                    <div class="label">
                        <?php esc_html_e('Trainers needing migration','gym-builder'); ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="icon already-migrated">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check w-5 h-5 text-emerald-600" aria-hidden="true" data-replit-metadata="client/src/pages/migration.tsx:155:18" data-component-name="CheckCircle2"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                    </div>
                    <div class="count">
                        <?php echo intval($registration_draft_trainers); ?>
                    </div>
                    <div class="label">
                        <?php esc_html_e('Trainers are waiting for approval — change status from Draft to Publish.','gym-builder'); ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="icon new-trainers">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 text-blue-600" aria-hidden="true" data-replit-metadata="client/src/pages/migration.tsx:163:18" data-component-name="Users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><path d="M16 3.128a4 4 0 0 1 0 7.744"></path><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="count">
                        <?php echo intval($new_trainers); ?>
                    </div>
                    <div class="label">
                        <?php esc_html_e('New trainers created by admin with trainer given email(post-migration)','gym-builder'); ?>
                    </div>
                </div>
            </div>
            <div class="migration-intro upgrade-notice">
                <div class="icon">
                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#1C274C"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 17V11" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="#1C274C"></circle> <path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke="#1C274C" stroke-width="1.5"></path> </g></svg>
                </div>
                <div class="content">
                    <h2 class="intro-heading"><?php esc_html_e('Upgrade Notice','gym-builder'); ?></h2>
                    <p style="margin: 0;"><?php esc_html_e('If you add the email address for each trainer, a trainer user account will be created automatically. All trainers will receive an email containing their username and password. Using these credentials, trainers can log in to their dashboard.','gym-builder');?></p>
                </div>
            </div>
            <?php

            if ($registration_draft_trainers > 0 ){
                ?>
                <div class="migration-intro upgrade-notice">
                    <div class="icon">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#1C274C"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 17V11" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="#1C274C"></circle> <path d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z" stroke="#1C274C" stroke-width="1.5"></path> </g></svg>
                    </div>
                    <div class="content">
                        <h2 class="intro-heading"><?php esc_html_e('Trainer Registration Notice','gym-builder'); ?></h2>
                        <p style="margin: 0;"><?php echo intval($registration_draft_trainers);esc_html_e(' Registered trainers are awaiting approval. Please change their post status from Draft to Publish to activate their profiles. Once published, their trainer dashboard login credentials will be automatically sent to their email.','gym-builder');?></p>
                    </div>
                </div>
            <?php } ?>

        </div>

        <style>
            .gym-builder-trainer-migration-wrap {
                max-width: 1200px;
                margin: 20px auto;
                background-color:#f9fafb;
                padding:30px 50px;
                border-radius:10px;
            }
            .gym-builder-trainer-migration-wrap h1{
                text-align:center;
                margin-bottom:20px;
            }
            .heading-info {
                margin-bottom: 30px;
                text-align: center;
            }
            .migration-intro {
                display: flex;
                align-items: center;
                margin: 40px 0 30px 0;
                background-color:#eff6ff;
                border:1px solid oklch(93.2% 0.032 255.585);
                border-radius:10px;
                padding:15px;
            }
            .migration-intro p{
                font-size:14px;
            }
            .migration-intro .icon { margin-right: 10px; }
            .migration-intro .content { flex: 1; }
            .migration-intro .content h2 {
                margin: 0 0 7px 0;
                font-size: 18px;
            }
            .migration-stats{
                display:grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            .status-item{
                padding:30px;
                border:1px solid hsl(214 32% 91%);
                border-radius:15px;
                box-shadow: 0 4px 6px -1px #0000000d,0 2px 4px -1px #00000008;
            }
            .status-item .icon{
                width: 40px;
                height:40px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
            }
            .status-item .icon.already-migrated{
                background-color:#059669;
            }
            .status-item .icon.needing-migration{
                background-color:#d97706;
            }
            .status-item .icon.new-trainers{
                background-color:#256eff;
            }
            .status-item .count {
                font-size: 22px;
                font-weight: 600;
                color: #000;
                margin: 13px 0;
            }
            .status-item .icon svg{
                width:20px;
                height:20px;
                color:#fff;
            }
            .status-item .label {
                font-size: 16px;
                color: #666;
            }
            .upgrade-notice{
                padding: 30px 20px;
                gap: 10px;
            }
        </style>
        <?php
    }
}