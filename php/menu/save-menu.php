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
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $imagePath = '';

    // Validate input fields
    if (empty($name)) {
        echo json_encode(['status' => 400, 'msg' => 'Name cannot be empty.']);
        exit();
    }
    if (empty($price)) {
        echo json_encode(['status' => 400, 'msg' => 'Price cannot be empty.']);
        exit();
    }
    if (empty($description)) {
        echo json_encode(['status' => 400, 'msg' => 'Description is required.']);
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/menu_images/';
        $relativePath = '/uploads/menu_images/';

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $imageName = basename($_FILES['image']['name']);
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

        // Validate file type and size
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($imageExt), $allowedTypes)) {
            echo json_encode(['status' => 400, 'msg' => 'Invalid image type. Allowed types: JPEG, PNG, GIF.']);
            exit();
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) { // 2MB size limit
            echo json_encode(['status' => 400, 'msg' => 'Image size exceeds 2MB limit.']);
            exit();
        }

        // Generate unique filename and save
        $uniqueImageName = uniqid('', true) . '.' . $imageExt;
        $imagePath = $relativePath . $uniqueImageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $uniqueImageName)) {
            echo json_encode(['status' => 500, 'msg' => 'Failed to save the uploaded image.']);
            exit();
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO menu (name, price, description, image, status, create_by, create_date) 
                                VALUES (:name, :price, :description, :image, 'active', :create_by, NOW())");

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':price', $price, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':image', $imagePath, PDO::PARAM_STR);
        $stmt->bindValue(':create_by', $_SESSION['user_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 200, 'msg' => 'Menu has been saved successfully.']);
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
