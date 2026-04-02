<?php
/**
 * Plugin Name: Mega Workout Planner
 * Description: Een vette plugin om je workouts te plannen. Voeg oefeningen toe per dag in Mega Gym stijl!
 * Version: 1.0
 * Author: Jij
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1. Database tabel aanmaken bij activatie
 */
function mwp_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'gym_workouts';
    $charset_collate = $wpdb->get_charset_collate();

    // We slaan de dag, oefening, sets en reps op
    $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        workout_day varchar(20) NOT NULL,
        exercise varchar(100) NOT NULL,
        sets varchar(20) NOT NULL,
        reps varchar(20) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'mwp_install' );

/**
 * 2. CSS inladen via PHP (Geen losse style.css nodig!)
 */
function mwp_inline_styles() {
    // We echoën de CSS rechtstreeks in de <head> van de website
    echo '
    <style>
        /* Styling voor het formulier */
        .workout-form-box { background-color: #111; padding: 25px; border-radius: 8px; border: 2px solid #D4AF37; max-width: 500px; margin-bottom: 30px; }
        .workout-form-box h3 { color: #D4AF37; margin-top: 0; text-transform: uppercase; }
        .workout-form-box label { color: #fff; font-weight: bold; display: block; margin-top: 10px; }
        .workout-form-box input, .workout-form-box select { width: 100%; padding: 10px; margin-top: 5px; background: #222; border: 1px solid #555; color: #fff; border-radius: 4px; }
        .workout-form-box button { margin-top: 15px; background-color: #D4AF37; color: #000; border: none; padding: 12px 20px; font-weight: bold; text-transform: uppercase; cursor: pointer; width: 100%; border-radius: 4px; transition: 0.3s; }
        .workout-form-box button:hover { background-color: #fff; }
        
        /* Styling voor de Workout Tabel */
        .workout-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #000; }
        .workout-table th { background-color: #D4AF37; color: #000; padding: 12px; text-align: left; text-transform: uppercase; }
        .workout-table td { border-bottom: 1px solid #333; padding: 12px; color: #fff; }
        .workout-table tr:hover td { background-color: #111; }
    </style>
    ';
}
// Hook de CSS aan de wp_head zodat het op de voorkant laadt
add_action('wp_head', 'mwp_inline_styles');

/**
 * 3. Shortcode & Formulier afhandeling
 */
function mwp_planner_shortcode() {
    global $wpdb;
    $table = $wpdb->prefix . 'gym_workouts';

    // Check of het formulier is verzonden
    if ( isset($_POST['mwp_submit']) ) {
        $wpdb->insert(
            $table,
            array(
                'workout_day' => sanitize_text_field($_POST['mwp_day']),
                'exercise'    => sanitize_text_field($_POST['mwp_exercise']),
                'sets'        => sanitize_text_field($_POST['mwp_sets']),
                'reps'        => sanitize_text_field($_POST['mwp_reps']),
            )
        );
        echo "<div style='color:#D4AF37; font-weight:bold; margin-bottom:15px;'>✅ Oefening succesvol toegevoegd aan je schema!</div>";
    }

    ob_start(); // Start output buffering
    ?>
    
    <div class="workout-form-box">
        <h3>Plan je Oefening</h3>
        <form method="post">
            <label>Dag van de week</label>
            <select name="mwp_day" required>
                <option value="Maandag">Maandag</option>
                <option value="Dinsdag">Dinsdag</option>
                <option value="Woensdag">Woensdag</option>
                <option value="Donderdag">Donderdag</option>
                <option value="Vrijdag">Vrijdag</option>
                <option value="Zaterdag">Zaterdag</option>
                <option value="Zondag">Zondag</option>
            </select>

            <label>Oefening</label>
            <input type="text" name="mwp_exercise" placeholder="Bijv. Bench Press of Squat" required>

            <label>Aantal Sets</label>
            <input type="text" name="mwp_sets" placeholder="Bijv. 4" required>

            <label>Aantal Reps</label>
            <input type="text" name="mwp_reps" placeholder="Bijv. 10-12" required>

            <button type="submit" name="mwp_submit">Voeg toe aan schema</button>
        </form>
    </div>

    <?php
    // Haal alle opgeslagen workouts op uit de database
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    // Print de tabel als er workouts zijn gevonden
    if ( $results ) {
        echo '<h3>Jouw Mega Gym Schema</h3>';
        echo '<table class="workout-table">';
        echo '<thead><tr><th>Dag</th><th>Oefening</th><th>Sets</th><th>Reps</th></tr></thead>';
        echo '<tbody>';
        foreach ( $results as $row ) {
            echo "<tr>
                    <td><strong>{$row->workout_day}</strong></td>
                    <td>{$row->exercise}</td>
                    <td>{$row->sets}</td>
                    <td>{$row->reps}</td>
                  </tr>";
        }
        echo '</tbody></table>';
    } else {
        echo '<p style="color:#fff;">Je schema is nog leeg. Voeg hierboven je eerste oefening toe!</p>';
    }

    return ob_get_clean(); // Stuur alles naar de website
}
// Registreer de shortcode [workout_planner]
add_shortcode('workout_planner', 'mwp_planner_shortcode');

/**
 * 4. Admin Menu (Voor het overzicht in de achterkant)
 */
function mwp_admin_menu() {
    add_menu_page(
        'Alle Workouts', 
        'Workout Schema', 
        'manage_options', 
        'mega-workouts', 
        'mwp_admin_page', 
        'dashicons-clipboard' // Een handig klembord icoontje voor fitness schema's
    );
}
add_action('admin_menu', 'mwp_admin_menu');

function mwp_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'gym_workouts';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    echo "<div class='wrap'><h1>Alle Ingevulde Workouts</h1>";
    echo "<table class='widefat'><thead><tr>
            <th>Dag</th><th>Oefening</th><th>Sets</th><th>Reps</th>
          </tr></thead><tbody>";

    if ($results) {
        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row->workout_day}</td>
                    <td>{$row->exercise}</td>
                    <td>{$row->sets}</td>
                    <td>{$row->reps}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Nog geen workouts gevonden.</td></tr>";
    }
    
    echo "</tbody></table></div>";
}
?>