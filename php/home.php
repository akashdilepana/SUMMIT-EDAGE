<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

echo json_encode([
    "user_id" => $_SESSION['user_id'],
    "username" => $_SESSION['username'],
    "usertype" => $_SESSION['usertype'],
    "name" => $_SESSION['name']
    

]); 

?>
