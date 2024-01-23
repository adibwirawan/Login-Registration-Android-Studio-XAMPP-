<?php

header('content-type: application/json');

// Database connection details
require('dbconnection.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $apiKey = $_POST['api_key'];

    // Check if the API key is valid
    $checkQuery = "SELECT * FROM user WHERE api_key = '$apiKey'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // API key is valid, fetch user details
        $row = $checkResult->fetch_assoc();
        
        $response = array();
        $response['success'] = true;
        $response['name'] = $row['name'];
        $response['email'] = $row['email'];
        $response['username'] = $row['username'];
        $response['phone'] = $row['phone'];

        echo json_encode($response);
    } else {
        // API key is invalid
        $response = array();
        $response['success'] = false;
        $response['message'] = "Invalid API key";
        echo json_encode($response);
    }

    // Close the database connection
    $conn->close();
} else {
    $response = array();
    $response['success'] = false;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}

?>
