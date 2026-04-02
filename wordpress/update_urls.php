<?php
$host = 'localhost';
$user = 'st1738846931';
$pass = 'WkLv7naIMOukQQt';
$db = 'st1738846931';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update site URL
$conn->query("UPDATE wp_options SET option_value='https://st1738846931.splsites.nl/' WHERE option_name='siteurl'");

// Update home URL
$conn->query("UPDATE wp_options SET option_value='https://st1738846931.splsites.nl/' WHERE option_name='home'");

echo "Site URLs updated successfully.";

$conn->close();
?>