<?php
/*
Plugin Name: Workout Planner
Description: Genereert workout schema’s voor gym gebruikers
Version: 1.0
Author: Gabriel
*/

// beveiliging
if (!defined('ABSPATH')) {
    exit;
}

/* ======================
   DATABASE AANMAKEN
====================== */
function wp_create_workout_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'workouts';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name text NOT NULL,
        level text NOT NULL,
        plan text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'wp_create_workout_table');


/* ======================
   SHORTCODE FORMULIER
====================== */
function wp_workout_form() {
    ob_start();
    ?>

    <form method="post">
        <input type="text" name="name" placeholder="Naam" required>

        <select name="level">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>

        <button type="submit" name="generate">Genereer Workout</button>
    </form>

    <?php
    return ob_get_clean();
}

add_shortcode('workout_planner', 'wp_workout_form');


/* ======================
   WORKOUT GENERATOR
====================== */
function wp_generate_plan($level) {

    if ($level == 'beginner') {
        return "Maandag: Full Body\nWoensdag: Cardio\nVrijdag: Core";
    }

    if ($level == 'intermediate') {
        return "Maandag: Chest\nDinsdag: Back\nDonderdag: Legs\nVrijdag: Arms";
    }

    if ($level == 'advanced') {
        return "Maandag: Chest\nDinsdag: Back\nWoensdag: Legs\nDonderdag: Shoulders\nVrijdag: Arms";
    }
}


/* ======================
   FORM HANDLER
====================== */
function wp_handle_workout() {
    if (isset($_POST['generate'])) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'workouts';

        $name = sanitize_text_field($_POST['name']);
        $level = sanitize_text_field($_POST['level']);

        $plan = wp_generate_plan($level);

        $wpdb->insert($table_name, [
            'name' => $name,
            'level' => $level,
            'plan' => $plan
        ]);

        echo "<h3>Jouw schema:</h3>";
        echo nl2br($plan);
    }
}

add_action('init', 'wp_handle_workout');


/* ======================
   ADMIN MENU
====================== */
function wp_workout_menu() {
    add_menu_page(
        'Workout Planner',
        'Workouts',
        'manage_options',
        'workouts',
        'wp_workout_admin_page'
    );
}

add_action('admin_menu', 'wp_workout_menu');


/* ======================
   ADMIN PAGINA
====================== */
function wp_workout_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'workouts';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo "<h1>Workout Plannen</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Naam</th><th>Niveau</th><th>Schema</th></tr>";

    foreach ($results as $row) {
        echo "<tr>
            <td>{$row->name}</td>
            <td>{$row->level}</td>
            <td>{$row->plan}</td>
        </tr>";
    }

    echo "</table>";
}