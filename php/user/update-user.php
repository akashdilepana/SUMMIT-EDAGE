<?php
include '../database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['name'], $_POST['username'], $_POST['type'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $user_type = $_POST['type'];

    $stmt = $conn->prepare("UPDATE users SET name = :name, username = :username, user_type = :type WHERE id = :id");

    // Bind the parameters to the query
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':type', $user_type, PDO::PARAM_INT); 

    if ($stmt->execute()) {
        echo json_encode(['status' => 200, 'msg' => 'User updated successfully.']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to update user.']);
    }
} else {
    echo json_encode(['status' => 400, 'msg' => 'Invalid request. Missing required fields.']);
}
?>