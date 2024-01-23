<?php
header('content-type: application/json');

// Database connection details
require('dbconnection.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $api_key = $_POST['api_key'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Check if the API key is valid
    $check_api_key = $conn->prepare("SELECT id FROM user WHERE api_key = ?");
    $check_api_key->bind_param("s", $api_key);
    $check_api_key->execute();
    $result = $check_api_key->get_result();

    if ($result->num_rows > 0) {
        // API key is valid, proceed with changing the password
        $user_id = $result->fetch_assoc()['id'];

        // Retrieve the hashed password from the database
        $get_password = $conn->prepare("SELECT password FROM user WHERE id = ?");
        $get_password->bind_param("i", $user_id);
        $get_password->execute();
        $result_password = $get_password->get_result();

        if ($result_password->num_rows > 0) {
            $hashed_password = $result_password->fetch_assoc()['password'];

            // Verify the old password
            if (password_verify($old_password, $hashed_password)) {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_password = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
                $update_password->bind_param("si", $new_hashed_password, $user_id);

                if ($update_password->execute()) {
                    $response = array();
                    $response['success'] = true;
                    $response['message'] = "Password changed successfully!";
                    echo json_encode($response);
                } else {
                    $response = array();
                    $response['success'] = false;
                    $response['message'] = "Error updating password: " . $conn->error;
                    echo json_encode($response);
                }
            } else {
                $response = array();
                $response['success'] = false;
                $response['message'] = "Incorrect old password";
                echo json_encode($response);
            }
        } else {
            $response = array();
            $response['success'] = false;
            $response['message'] = "Error retrieving old password";
            echo json_encode($response);
        }
    } else {
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
