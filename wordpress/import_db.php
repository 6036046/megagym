<?php
$host = 'localhost'; // Database host (usually localhost on the server)
$user = 'st1738846931'; // Database username
$pass = 'WkLv7naIMOukQQt'; // Database password
$db = 'st1738846931'; // Database name

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop existing wp_ tables to avoid conflicts
$result = $conn->query("SHOW TABLES LIKE 'wp_%'");
if ($result) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        $conn->query("DROP TABLE `$table`");
    }
}

// Read the SQL file (assuming it's in the same directory)
$sql = file_get_contents('wordpress.sql');

if ($sql === false) {
    die("Error reading SQL file");
}

// Execute the SQL
if ($conn->multi_query($sql)) {
    do {
        // Check for errors in each result
        if ($result = $conn->store_result()) {
            $result->free();
        }
        if ($conn->errno) {
            echo "Error: " . $conn->error . "<br>";
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Database import completed successfully.";
} else {
    echo "Error importing database: " . $conn->error;
}

$conn->close();
?>