<?php
// Database Import Script - DELETE AFTER USE!
$db_host = 'localhost';
$db_user = 'st1738846931';
$db_pass = 'WkLv7naIMOukQQt';
$db_name = 'st1738846931';
$sql_file = 'wordpress-6_9-nl_nl.sql';
 
if (!file_exists($sql_file)) {
    die("SQL file not found: $sql_file");
}
 
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
 
echo "Connected to database...<br>";
echo "Importing SQL file...<br>";
 
$sql = file_get_contents($sql_file);
$queries = explode(';', $sql);
 
$success = 0;
$errors = 0;
 
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($mysqli->query($query)) {
            $success++;
        } else {
            $errors++;
        }
    }
}
 
echo "Import complete!<br>";
echo "Successful queries: $success<br>";
echo "Errors: $errors<br>";
echo "<br><strong>DELETE THIS FILE NOW!</strong>";
 
$mysqli->close();
?>