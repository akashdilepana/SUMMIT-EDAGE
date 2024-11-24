<?php
include_once '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);

        try {
            $stmt = $conn->prepare("UPDATE users SET status = 'deactive' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 200, 'msg' => 'User has been deactivated.']);
            } else {
                echo json_encode(['status' => 404, 'msg' => 'User not found or already deactivated.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 500, 'msg' => 'Query execution failed: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 400, 'msg' => 'Invalid user ID.']);
    }
} else {
    echo json_encode(['status' => 405, 'msg' => 'Invalid request method.']);
}
?>

