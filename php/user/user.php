<?php
// Include the database connection
include '../database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve the user ID from the URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        try {
            // Prepare and execute the query to fetch user data
            $stmt = $conn->prepare("SELECT id, name, username, user_type FROM users WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Return the user data as JSON
                echo json_encode($user);
            } else {
                // Return an error if the user is not found
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } catch (Exception $e) {
            // Handle database errors
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Return an error for invalid or missing ID
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user ID']);
    }
} else {
    // Handle unsupported HTTP methods
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
