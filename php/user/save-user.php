<?php
include_once '../database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $userType = isset($_POST['type']) ? $_POST['type'] : '';

    if (empty($name)) {
        echo json_encode(['status' => 400, 'msg' => 'Name cannot be empty.']);
        exit();
    }
    if (empty($username)) {
        echo json_encode(['status' => 400, 'msg' => 'Username cannot be empty.']);
        exit();
    }
    if (empty($userType)) {
        echo json_encode(['status' => 400, 'msg' => 'User Type is required.']);
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, username, user_type, status, create_by, create_date) 
                                VALUES (:name, :username, :user_type, 'active', :create_by, NOW())");

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':user_type', $userType, PDO::PARAM_INT);
        $stmt->bindValue(':create_by', $_SESSION['user_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 200, 'msg' => 'User has been saved successfully.']);
        } else {
            echo json_encode(['status' => 500, 'msg' => 'An error occurred while processing your request.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 500, 'msg' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 405, 'msg' => 'Method not allowed.']);
}
?>
