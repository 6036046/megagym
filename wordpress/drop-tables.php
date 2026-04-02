<?php
// Database instellingen
$db_host = 'localhost';
$db_user = 'st1738846931';
$db_pass = 'WkLv7naIMOukQQt';
$db_name = 'st1738846931';
$sql_file = 'wordpress-6_9-nl_nl.sql'; // Eventueel voor latere referentie
 
// Drop existing tables and allow fresh install
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
 
echo "Dropping existing tables...<br>";
 
$tables = ['wp_comments', 'wp_links', 'wp_options', 'wp_commentmeta'];
 
foreach ($tables as $table) {
    if ($mysqli->query("DROP TABLE IF EXISTS $table")) {
        echo "✓ Dropped $table<br>";
    }
}
 
echo "<br><strong>Done! Now delete wp-config.php via FTP and reinstall WordPress.</strong><br>";
echo "<strong>DELETE THIS FILE TOO!</strong>";
 
$mysqli->close();
?>