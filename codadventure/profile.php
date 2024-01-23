<?php
if (!empty($_POST['email']) && !empty($_POST['apiKey'])){
    $email = $_POST['email'];
    $apiKey = $_POST['apiKey'];
    $result = array();
    require('dbconnection.php'); // Include the database connection file
    //$con = mysqli_connect("localhost","root","","ipoultry");
    if($con){
        $sql = "select * from user where email = '".$email."' and apiKey = '".$apiKey."'";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res) != 0) {
            $row = mysqli_fetch_assoc($res);
            $result = array("status" => "success", "message" => "Data fetched successfully",
                "name" => $row['name'], "email" => $row['email'], "phone" => $row['phone'], "apiKey" => $row['apiKey']);
        } else $result = array("status" => "failed", "message" => "Unauthorised access");
    } else $result = array("status" => "failed", "message" => "Database connection failed");
} else $result = array("status" => "failed", "message" => "All fields are required");

echo json_encode($result, JSON_PRETTY_PRINT);
