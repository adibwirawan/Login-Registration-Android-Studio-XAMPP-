<?php
if (!empty($_POST['email']) && !empty($_POST['apiKey'])) {
    $email = $_POST['email'];
    $apiKey = $_POST['apiKey'];
    require('dbconnection.php'); // Include the database connection file
    //$con = mysqli_connect("localhost","root","","ipoultry");
    if ($con) {
        $sql = "select * from user where email = '" . $email . "' and apiKey = '" . $apiKey . "'";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $sqlUpdate = "update user set apiKey = '' where email = '" . $email . "'";
            if (mysqli_query($con, $sqlUpdate)) {
                echo "success";
            } else echo "Logout failed";
        } else echo "Unauthorised to access";
    } else echo "Database connection failed";
} else echo "All fields are required";