<?php
/**
 * @package GymBuilder
 */

namespace GymBuilder\Inc\Controllers\Models;

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Traits\Constants;

class GymBuilderDatabase {
	use Constants;

	public static function create_member_db_table() {
		global $wpdb;
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		$charset_collate = $wpdb->get_charset_collate();
		$sql_query       = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}gym_builder_members` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `member_user_id` VARCHAR(100) NOT NULL,
        `member_name` VARCHAR(255) NOT NULL,
        `member_address` TEXT NOT NULL,
        `member_email` VARCHAR(100) DEFAULT NULL,
        `member_phone` VARCHAR(30) NOT NULL,
        `member_age` INT DEFAULT NULL,
        `membership_status` TINYINT(1) DEFAULT 0,
        `member_joining_date` DATE DEFAULT NULL,
        `membership_duration_start` DATE DEFAULT NULL,
        `membership_duration_end` DATE DEFAULT NULL,
        `member_gender` VARCHAR(10) DEFAULT NULL,
        `attendance_count` int(100) DEFAULT NULL,
        `package_name_id` int(100) DEFAULT NULL,
        `membership_package_type` VARCHAR(30) DEFAULT NULL,
        `membership_package_name` VARCHAR(30) DEFAULT NULL,
        `membership_classes` TEXT DEFAULT NULL,
        `file_url` VARCHAR(255) DEFAULT NULL,
        `schedule_weekday` TEXT DEFAULT NULL,
        `schedule_time` VARCHAR(30) DEFAULT NULL,
        `class_id` int(100) DEFAULT NULL,
        `wp_user_id` INT DEFAULT NULL
    	) $charset_collate;";
		dbDelta( $sql_query );

	}

	public static function check_and_update_db_table() {
		$installed_version       = get_option( 'gym_builder_version' );
		$member_db_table_version = get_option( 'gb_members_db_table_version' );
		if ( $installed_version !== self::$plugin_version) {
			self::database_migration();
            update_option( 'gym_builder_version', self::$plugin_version );
            update_option( 'gb_members_db_table_version', self::$members_db_table_version );
		}
	}

	public static function database_migration(  ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'gym_builder_members';
		$package_name_id_column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'package_name_id'");
		$weekday_name_column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'schedule_weekday'");

		if (empty($package_name_id_column_exists)) {
			$wpdb->query("ALTER TABLE `$table_name` ADD `package_name_id` INT DEFAULT NULL");
		}
		if (empty($weekday_name_column_exists)){
			$wpdb->query("ALTER TABLE `$table_name` 
	        ADD `schedule_weekday` TEXT DEFAULT NULL, 
	        ADD `schedule_time` VARCHAR(30) DEFAULT NULL,
	        ADD `class_id` int (100) DEFAULT NULL");
		}
		$wp_user_id_column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'wp_user_id'");
		if (empty($wp_user_id_column_exists)) {
			$wpdb->query("ALTER TABLE `$table_name` ADD `wp_user_id` INT DEFAULT NULL");
		}

		$member_user_id_column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'member_user_id'");
		if (empty($member_user_id_column_exists)) {
			$wpdb->query("ALTER TABLE `$table_name` ADD `member_user_id` VARCHAR(100) NOT NULL");
			$members = $wpdb->get_results("SELECT id FROM `$table_name` WHERE member_user_id IS NULL OR member_user_id = ''");
			if (!empty($members)) {
				foreach ($members as $member) {
					$random_user_id = Helper::generate_member_user_id();
					$wpdb->update(
						$table_name,
						array('member_user_id' => $random_user_id),
						array('id' => $member->id),
						array('%s'),
						array('%d')
					);
				}
			}
		}
	}
}