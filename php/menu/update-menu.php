<?php
include '../database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['name'], $_POST['price'], $_POST['description'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $name = htmlspecialchars(trim($_POST['name']));
    $price = htmlspecialchars(trim($_POST['price']));
    $description = htmlspecialchars(trim($_POST['description']));
    $imagePath = '';

    if (!$id || empty($name) || empty($price) || empty($description)) {
        echo json_encode(['status' => 400, 'msg' => 'Invalid input. Please provide all required fields.']);
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imageExt = pathinfo($image['name'], PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($imageExt), $allowedExtensions)) {
            if ($image['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $newImageName = uniqid('', true) . '.' . $imageExt;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/menu_images/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $imagePath = '/uploads/menu_images/' . $newImageName;
                if (!move_uploaded_file($image['tmp_name'], $uploadDir . $newImageName)) {
                    echo json_encode(['status' => 500, 'msg' => 'Failed to upload image.']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 400, 'msg' => 'Image size exceeds the 5MB limit.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 400, 'msg' => 'Invalid image format. Only JPG, PNG, and GIF are allowed.']);
            exit();
        }
    }

    if (!empty($imagePath)) {
        $stmt = $conn->prepare("SELECT image FROM menu WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $oldImage = $stmt->fetchColumn();

        if ($oldImage && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImage)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $oldImage);
        }
    }

    $query = "UPDATE menu SET name = :name, price = :price, description = :description";
    if (!empty($imagePath)) {
        $query .= ", image = :image";
    }
    $query .= " WHERE id = :id";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        if (!empty($imagePath)) {
            $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 200, 'msg' => 'Menu updated successfully.']);
        } else {
            echo json_encode(['status' => 500, 'msg' => 'Failed to update Menu.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'msg' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 400, 'msg' => 'Invalid request. Missing required fields.']);
}
?>