<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    // Validate the token (check against a database or your unique token generation process)
    $tokenIsValid = true; // You should replace this with your validation logic

    if ($tokenIsValid) {
        // Database connection details
        require('dbconnection.php');
        // Update the user's password in the Users table based on the token in the passwordreset table
        // Replace 'your_update_query' with your actual database update query
        // Make sure to hash the new password before storing it in the database
        $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $updateUserQuery = "UPDATE Users 
            SET Password = '$newPasswordHashed' 
            WHERE Email = (SELECT Email FROM passwordreset WHERE Token = '$token')";

        echo 'Update Query: ' . $updateUserQuery . '<br>';

        if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            // Handle the case where the token is missing in the URL
            echo 'Token missing.';
            exit;
        }
        
        $conn->begin_transaction();
        if ($conn->query($updateUserQuery) === TRUE) {
            $conn->commit();
            echo 'Password reset successful';
        } else {
            $conn->rollback(); // Roll back the changes if there is an error
            echo 'Password reset failed: ' . $conn->error;
        }

        $conn->close();
        } else {
            echo 'Invalid token';
        }


}
?>
