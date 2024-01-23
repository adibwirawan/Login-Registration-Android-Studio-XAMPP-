<?php
header('content-type: application/json');

// Include the PHPMailer library
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';


// Database connection details
require('dbconnection.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        if (empty($email)) {
            echo 'Please enter your email.';
        } else {
            // Generate a unique token (simplified for this example)
            $token = bin2hex(random_bytes(5)); // Generate a random token

            // Store the token, email, and expiration time in the database
            $expiration_time = time() + 3600; // Expiration time: 1 hour
            $insertQuery = "INSERT INTO PasswordReset (Email, Token, ExpirationTime) VALUES ('$email', '$token', $expiration_time)";

            if ($conn->query($insertQuery) === TRUE) {

                    // Send a password reset email with a link that includes the token
                    $reset_link = "192.168.0.113/codadventure/reset_password.html?token=$token"; // Modify the URL
                    $subject = "Password Reset";
                    $message = "To reset your password, click the following link: $reset_link \n Your token is Token = $token";

                    // Send the email using PHPMailer
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->IsSMTP();
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    $mail->Username = 'codadventuregame@gmail.com'; // Replace with your Gmail email address
                    $mail->Password = 'smsloncdswviikij'; // Replace with your Gmail password or App Password

                    $mail->SetFrom('codadventuregame@gmail.com'); // Replace with your Gmail email address
                    $mail->Subject = $subject;
                    $mail->Body = $message;
                    $mail->AddAddress($email);


                if ($mail->Send()) {
                    echo 'Password reset link sent to your email.';
                } else {
                    echo 'Failed to send the password reset email: ' . $mail->ErrorInfo;
                }
            } else {
                echo 'Password reset request failed.';
            }
        }
    } else {
        echo 'Email parameter is missing in the POST data.';
    }
}

$conn->close();
?>

