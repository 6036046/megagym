<?php
// Database Test Script
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<h2>Database Connection Test</h2>";
 
// Bestandsnaam voor eventuele latere import
$sql_file = 'wordpress-6_9-nl_nl.sql';
$configs = [
    ['host' => 'localhost', 'user' => 'st1738846931', 'pass' => 'WkLv7naIMOukQQt', 'db' => 'st1738846931'],
    ['host' => '127.0.0.1', 'user' => 'st1738846931', 'pass' => 'WkLv7naIMOukQQt', 'db' => 'st1738846931'],
    ['host' => 'localhost:3306', 'user' => 'st1738846931', 'pass' => 'WkLv7naIMOukQQt', 'db' => 'st1738846931'],
];
foreach ($configs as $i => $config) {
    echo "<br><strong>Test " . ($i+1) . ": {$config['host']}</strong><br>";
    $mysqli = @new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
    if ($mysqli->connect_error) {
        echo "❌ Failed: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ SUCCESS! Use this DB_HOST: {$config['host']}<br>";
        echo "Database: {$config['db']}<br>";
        // Check tables
        $result = $mysqli->query("SHOW TABLES");
        if ($result) {
            echo "Tables found: " . $result->num_rows . "<br>";
        }
        $mysqli->close();
        break;
    }
}
echo "<br><strong>DELETE THIS FILE AFTER TESTING!</strong>";
?>