<?php
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'database.php'; 

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['address'], $data['phone'], $data['paymentMethod'], $data['totalAmount'], $data['cartItems'])) {
        throw new Exception('Invalid input data.');
    }

    $stmt = $conn->prepare("INSERT INTO orders (name, address, phone, payment_method, total_amount) 
                            VALUES (:name, :address, :phone, :paymentMethod, :totalAmount)");
    $stmt->execute([
        ':name' => $data['name'],
        ':address' => $data['address'],
        ':phone' => $data['phone'],
        ':paymentMethod' => $data['paymentMethod'],
        ':totalAmount' => $data['totalAmount']
    ]);

    $orderId = $conn->lastInsertId();

    $cartItems = $data['cartItems'];
    foreach ($cartItems as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) 
                                VALUES (:order_id, :item_id, :quantity, :price)");
        $stmt->execute([
            ':order_id' => $orderId,
            ':item_id' => $item['id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
