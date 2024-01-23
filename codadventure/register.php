<?php

header('content-type: application/json');

// Database connection details
require('dbconnection.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Check if any of the fields are empty
    if (empty($name) || empty($phone) || empty($email) || empty($password) || empty($username)) {
        $response = array();
        $response['success'] = false;
        $response['message'] = "All fields are required!";
        echo json_encode($response);
        exit;
    }

    // Hash the password (for security, you should use a more secure hashing algorithm)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a 5-digit random ID
    $randomId = generateRandomId($conn);

    // Check if the username, email, and phone are unique
    $checkQuery = "SELECT * FROM user WHERE username = '$username' OR email = '$email' OR phone = '$phone'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $response = array();
        $response['success'] = false;
        $response['message'] = "Username, email, or phone number already exists!";
        echo json_encode($response);
        exit;
    }

    // Perform the registration logic (e.g., store the data in a database)
    $sql = "INSERT INTO user (id, name, phone, email, password, username) VALUES ('$randomId', '$name', '$phone', '$email', '$hashedPassword', '$username')";

    if ($conn->query($sql) === TRUE) {
        $response = array();
        $response['success'] = true;
        $response['message'] = "Registration successful!";
        echo json_encode($response);
    } else {
        $response = array();
        $response['success'] = false;
        $response['message'] = "Error: " . $sql . "<br>" . $conn->error;
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

function generateRandomId($conn) {
    // Generate a 5-digit random ID
    $randomId = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    // Check if the ID is unique
    $checkQuery = "SELECT * FROM user WHERE id = '$randomId'";
    $checkResult = $conn->query($checkQuery);

    while ($checkResult->num_rows > 0) {
        // Regenerate the ID if not unique
        $randomId = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $checkResult = $conn->query($checkQuery);
    }

    return $randomId;
}

?>
