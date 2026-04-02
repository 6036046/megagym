<?php
/**
 * @package GymBuilder
 */

use GymBuilder\Inc\Controllers\Helpers\Helper;
use GymBuilder\Inc\Controllers\Admin\Settings\Api\SettingsApi;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$primary_color   = Helper::get_primary_color(); // #005dd0
$secondary_color = Helper::get_secondary_color(); // #0a4b78

$class_title_color         = SettingsApi::get_option( 'gym_builder_class_title_color', 'gym_builder_style_settings' ) ?: '';
$class_content_color       = SettingsApi::get_option( 'gym_builder_class_content_color', 'gym_builder_style_settings' ) ?: '';
$class_schedule_color      = SettingsApi::get_option( 'gym_builder_class_schedule_color', 'gym_builder_style_settings' ) ?: '';
$class_trainer_name_color  = SettingsApi::get_option( 'gym_builder_class_trainer_color', 'gym_builder_style_settings' ) ?: '';
$class_table_title_color   = SettingsApi::get_option( 'gym_builder_class_table_title_color', 'gym_builder_style_settings' ) ?: '';
$class_table_border_color  = SettingsApi::get_option( 'gym_builder_class_table_border_color', 'gym_builder_style_settings' ) ?: '';
$class_table_heading_color = SettingsApi::get_option( 'gym_builder_class_table_heading_color', 'gym_builder_style_settings' ) ?: '';

$trainer_title_color       = SettingsApi::get_option( 'gym_builder_trainer_title_color', 'gym_builder_style_settings' ) ?: '';
$trainer_designation_color = SettingsApi::get_option( 'gym_builder_trainer_designation_color', 'gym_builder_style_settings' ) ?: '';
$trainer_content_color     = SettingsApi::get_option( 'gym_builder_trainer_content_color', 'gym_builder_style_settings' ) ?: '';
$trainer_bg_color          = SettingsApi::get_option( 'gym_builder_trainer_bg_color', 'gym_builder_style_settings' ) ?: '';
$online_badge_color        = SettingsApi::get_option( 'online_badge_color', 'gym_builder_zoom_integration_settings' ) ?: '';
$offline_badge_color       = SettingsApi::get_option( 'offline_badge_color', 'gym_builder_zoom_integration_settings' ) ?: '';
?>
:root {
--gym-builder-primary-color: <?php echo esc_html( $primary_color ? $primary_color : '#005dd0' ); ?>;
--gym-builder-secondary-color: <?php echo esc_html( $secondary_color ? $secondary_color : '#0a4b78' ); ?>;
}

<?php
/*-------------------------------------
#. Class Style
---------------------------------------*/
?>
<?php if ( $class_title_color ) {
	?>
    .gym-builder-class-items .gym-builder-class-title a,
    .single-class-inner .entry-content .entry-title{
    color:<?php echo esc_html( $class_title_color ); ?>;
    }
<?php } ?>
<?php if ( $class_content_color ) {
	?>
    .gym-builder-class-items .gym-builder-class-des,
    .single-class-inner .entry-content p{
    color:<?php echo esc_html( $class_content_color ); ?>;
    }
<?php } ?>
<?php if ( $class_schedule_color ) {
	?>
    .gym-builder-class-items .class-meta .schedule .day,
    .gym-builder-class-items .class-meta .schedule .time,
    .single-class-inner .schedule-table td
    {
    color:<?php echo esc_html( $class_schedule_color ); ?>;
    }
<?php } ?>
<?php if ( $class_trainer_name_color ) {
	?>
    .gym-builder-class-items .class-meta .trainer .trainer-name,
    .single-class-inner .schedule-table td a
    {
    color:<?php echo esc_html( $class_trainer_name_color ); ?>;
    }
<?php } ?>
<?php if ( $class_table_title_color ) {
	?>
    .single-class-inner .class-schedule .table-title
    {
    color:<?php echo esc_html( $class_table_title_color ); ?>;
    }
<?php } ?>
<?php if ( $class_table_border_color ) {
	?>
    .single-class-inner .schedule-table,
    .single-class-inner .schedule-table td,
    .single-class-inner .schedule-table th
    {
    border-color:<?php echo esc_html( $class_table_border_color ); ?>;
    }
<?php } ?>

<?php if ( $class_table_heading_color ) {
	?>
    .single-class-inner .schedule-table tr th
    {
    color:<?php echo esc_html( $class_table_heading_color ); ?>;
    }
<?php } ?>
<?php
/*-------------------------------------
#. Trainer Style
---------------------------------------*/
?>
<?php if ( $trainer_title_color ) {
	?>
    .gym-builder-trainer-items .gym-builder-trainer-title a,
    .gym-builder-single-trainer-wrapper .trainer-content .entry-title{
    color:<?php echo esc_html( $trainer_title_color ); ?>;
    }
<?php } ?>
<?php if ( $trainer_designation_color ) {
	?>
    .gym-builder-trainer-items .trainer-designation,
    .gym-builder-single-trainer-wrapper .trainer-content .trainer-designation{
    color:<?php echo esc_html( $trainer_designation_color ); ?>;
    }
<?php } ?>
<?php if ( $trainer_content_color ) {
	?>
    .gym-builder-trainer-items .trainer-description,
    .gym-builder-single-trainer-wrapper .entry-content p{
    color:<?php echo esc_html( $trainer_content_color ); ?>;
    }
<?php } ?>
<?php if ( $trainer_bg_color ) {
	?>
    .gym-builder-trainer-items .trainer-item
    {
    background-color:<?php echo esc_html( $trainer_bg_color ); ?>;
    }
<?php } ?>
<?php if ( $online_badge_color ) {
	?>
    .gb-class-type-badge li.online-badge
    {
        background-color:<?php echo esc_html( $online_badge_color ); ?>;
    }
    .gym-builder-single-class-wrapper .schedule-table .class-type.online{
        color:<?php echo esc_html( $online_badge_color ); ?>;
    }
<?php } ?>
<?php if ( $offline_badge_color ) {
	?>
    .gb-class-type-badge li.offline-badge
    {
    background-color:<?php echo esc_html( $offline_badge_color ); ?>;
    }
    .gym-builder-single-class-wrapper .schedule-table .class-type.offline{
        color:<?php echo esc_html( $offline_badge_color ); ?>;
    }
<?php } ?>
