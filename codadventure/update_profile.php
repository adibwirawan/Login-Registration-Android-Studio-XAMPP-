<?php
header('content-type: application/json');

require('dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $apiKey = $_POST['api_key'];
    $newName = $_POST['new_name'];
    $newUsername = $_POST['new_username'];
    $newPhone = $_POST['new_phone'];
    $newEmail = $_POST['new_email'];

    // Check if the API key is valid
    $checkApiKeyQuery = "SELECT * FROM user WHERE api_key = '$apiKey'";
    $checkApiKeyResult = $conn->query($checkApiKeyQuery);

    if ($checkApiKeyResult->num_rows > 0) {
        // Check for the uniqueness of the new username
        if (!empty($newUsername)) {
            $checkUsernameQuery = "SELECT * FROM user WHERE username = '$newUsername' AND api_key != '$apiKey'";
            $checkUsernameResult = $conn->query($checkUsernameQuery);

            if ($checkUsernameResult->num_rows > 0) {
                $response = array();
                $response['success'] = false;
                $response['message'] = "Username already exists";
                echo json_encode($response);
                exit;
            }
        }

        // Check for the uniqueness of the new email
        if (!empty($newEmail)) {
            $checkEmailQuery = "SELECT * FROM user WHERE email = '$newEmail' AND api_key != '$apiKey'";
            $checkEmailResult = $conn->query($checkEmailQuery);

            if ($checkEmailResult->num_rows > 0) {
                $response = array();
                $response['success'] = false;
                $response['message'] = "Email already exists";
                echo json_encode($response);
                exit;
            }
        }

        // Check for the uniqueness of the new phone
        if (!empty($newPhone)) {
            $checkPhoneQuery = "SELECT * FROM user WHERE phone = '$newPhone' AND api_key != '$apiKey'";
            $checkPhoneResult = $conn->query($checkPhoneQuery);

            if ($checkPhoneResult->num_rows > 0) {
                $response = array();
                $response['success'] = false;
                $response['message'] = "Phone already exists";
                echo json_encode($response);
                exit;
            }
        }

        // Update the profile if checks pass
        $updateQuery = "UPDATE user SET ";
        $updates = array();

        if (!empty($newName)) {
            $updates[] = "name = '$newName'";
        }

        if (!empty($newUsername)) {
            $updates[] = "username = '$newUsername'";
        }

        if (!empty($newPhone)) {
            $updates[] = "phone = '$newPhone'";
        }

        if (!empty($newEmail)) {
            $updates[] = "email = '$newEmail'";
        }

        $updateQuery .= implode(', ', $updates);
        $updateQuery .= " WHERE api_key = '$apiKey'";

        if ($conn->query($updateQuery)) {
            $response = array();
            $response['success'] = true;
            $response['message'] = "Profile changes saved successfully";
            echo json_encode($response);
        } else {
            $response = array();
            $response['success'] = false;
            $response['message'] = "Error updating profile: " . $conn->error;
            echo json_encode($response);
        }
    } else {
        $response = array();
        $response['success'] = false;
        $response['message'] = "Invalid API key";
        echo json_encode($response);
    }

    $conn->close();
} else {
    $response = array();
    $response['success'] = false;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}
?>
