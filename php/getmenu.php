<?php
include 'database.php'; // Include your database connection file

header('Content-Type: application/json');

try {
    // Query to fetch menu details including the image field
    $stmt = $conn->prepare("
        SELECT id, name, price, description, image 
        FROM menu 
        WHERE status = 'active'
    ");
    $stmt->execute();

    // Fetch all menu items as an associative array
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add base URL for images (if needed)
    $base_url = ''; // Replace with your actual base URL
    foreach ($menus as &$menu) {
        if (!empty($menu['image'])) {
            $menu['image'] = $base_url . $menu['image'];
        }
    }

    // Return JSON response
    echo json_encode($menus);
} catch (Exception $e) {
    // Return error message in case of failure
    echo json_encode(['error' => $e->getMessage()]);
}
?>
