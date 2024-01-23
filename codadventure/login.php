<?php

header('content-type: application/json');

// Database connection details
require('dbconnection.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emailUsername = $_POST['email_username'];
    $password = $_POST['password'];

    // Check if any of the fields are empty
    if (empty($emailUsername) || empty($password)) {
        $response = array();
        $response['success'] = false;
        $response['message'] = "Both email/username and password are required!";
        echo json_encode($response);
        exit;
    }

    // Check if the user exists
    $checkQuery = "SELECT * FROM user WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('ss', $emailUsername, $emailUsername);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        // User exists, verify the password
        $row = $checkResult->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, generate and update the API key
            $apiKey = generateUniqueApiKey($conn);

            $updateQuery = "UPDATE user SET api_key = ? WHERE email = ? OR username = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('sss', $apiKey, $emailUsername, $emailUsername);
            $stmt->execute();

            // Log user login
            logUserLogin($conn, $row['id']);

            // Fetch user details
            $userDetails = array();
            $userDetails['name'] = $row['name'];
            $userDetails['email'] = $row['email'];
            $userDetails['username'] = $row['username'];
            $userDetails['phone'] = $row['phone'];

            $response = array();
            $response['success'] = true;
            $response['message'] = "Login successful!";
            $response['api_key'] = $apiKey;
            $response['user_details'] = $userDetails;
            echo json_encode($response);
        } else {
            // Incorrect password
            $response = array();
            $response['success'] = false;
            $response['message'] = "Incorrect password";
            echo json_encode($response);
        }
    } else {
        // User does not exist
        $response = array();
        $response['success'] = false;
        $response['message'] = "User not found";
        echo json_encode($response);
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    $response = array();
    $response['success'] = false;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}

function generateUniqueApiKey($conn) {
    $apiKey = bin2hex(random_bytes(16)); // Generate a random API key (32 characters)

    // Check if the API key is unique
    $checkQuery = "SELECT * FROM user WHERE api_key = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('s', $apiKey);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    while ($checkResult->num_rows > 0) {
        // Regenerate the API key if not unique
        $apiKey = bin2hex(random_bytes(16));
        $stmt->execute();
        $checkResult = $stmt->get_result();
    }

    return $apiKey;
}

function logUserLogin($conn, $userId) {
    $loginTime = date("Y-m-d H:i:s");
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    $logQuery = "INSERT INTO user_login_log (id, login_time, ip_address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($logQuery);
    $stmt->bind_param('iss', $userId, $loginTime, $ipAddress);
    $stmt->execute();
    $stmt->close();
}
?>
