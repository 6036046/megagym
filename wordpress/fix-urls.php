<?php

// URL Fix Script - DELETE AFTER USE!

$db_host = 'localhost';

$db_user = 'st1738846931';

$db_pass = 'WkLv7naIMOukQQt';

$db_name = 'st1738846931';
 
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
 
if ($mysqli->connect_error) {

    die("Connection failed: " . $mysqli->connect_error);

}
 
echo "Updating URLs...<br>";
 
$queries = [

    "UPDATE wp_options SET option_value = 'https://st1738846931.splsites.nl' WHERE option_name = 'home' OR option_name = 'siteurl'",

    "UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://localhost:8080', 'https://st1738846931.splsites.nl')",

    "UPDATE wp_posts SET guid = REPLACE(guid, 'http://localhost:8080', 'https://st1738846931.splsites.nl')",

    "UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http://localhost:8080', 'https://st1738846931.splsites.nl')"

];
 
foreach ($queries as $query) {

    if ($mysqli->query($query)) {

        echo "✓ Query executed<br>";

    } else {

        echo "✗ Error: " . $mysqli->error . "<br>";

    }

}
 
echo "<br><strong>URLs updated! DELETE THIS FILE NOW!</strong>";

$mysqli->close();

?>
 