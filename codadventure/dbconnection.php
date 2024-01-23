<?php
header('content-type: application/json');

$servername = "localhost";
$username = "pma";
$password = "ipoultry@msu1122";
$database = "codadventure";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
