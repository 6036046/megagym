<?php
$host = 'localhost';
$user = 'st1738846931';
$pass = 'WkLv7naIMOukQQt';
$db = 'st1738846931';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "Tables in database:<br>";
    while ($row = $result->fetch_array()) {
        echo $row[0] . "<br>";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>