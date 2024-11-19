<?php
include 'database.php'; 

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("
        SELECT id, name, price, description, image 
        FROM menu 
        WHERE status = 'active'
    ");
    $stmt->execute();

    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $base_url = ''; 
    foreach ($menus as &$menu) {
        if (!empty($menu['image'])) {
            $menu['image'] = $base_url . $menu['image'];
        }
    }

    echo json_encode($menus);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
