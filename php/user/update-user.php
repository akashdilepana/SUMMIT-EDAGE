<?php
include '../database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['name'], $_POST['username'], $_POST['type'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $user_type = $_POST['type'];
    $imagePath = ''; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imageExt = pathinfo($image['name'], PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($imageExt), $allowedExtensions)) {
            if ($image['size'] <= 5 * 1024 * 1024) { 
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
        $stmt = $conn->prepare("SELECT image FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $oldImage = $stmt->fetchColumn();

        if ($oldImage && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImage)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $oldImage);
        }
    }

    $query = "UPDATE users SET name = :name, username = :username, user_type = :type";

    if (!empty($imagePath)) {
        $query .= ", image = :image";
    }

    $query .= " WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':type', $user_type, PDO::PARAM_INT);

    if (!empty($imagePath)) {
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 200, 'msg' => 'User updated successfully.']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to update user.']);
    }
} else {
    echo json_encode(['status' => 400, 'msg' => 'Invalid request. Missing required fields.']);
}
?>
