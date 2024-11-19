<?php
// Error handling setup
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'database.php'; // Ensure this includes the `$conn` variable

try {
    // Decode the incoming JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($data['name'], $data['address'], $data['phone'], $data['paymentMethod'], $data['totalAmount'], $data['cartItems'])) {
        throw new Exception('Invalid input data.');
    }

    // Insert order data into `orders` table
    $stmt = $conn->prepare("INSERT INTO orders (name, address, phone, payment_method, total_amount) 
                            VALUES (:name, :address, :phone, :paymentMethod, :totalAmount)");
    $stmt->execute([
        ':name' => $data['name'],
        ':address' => $data['address'],
        ':phone' => $data['phone'],
        ':paymentMethod' => $data['paymentMethod'],
        ':totalAmount' => $data['totalAmount']
    ]);

    // Get the inserted order ID
    $orderId = $conn->lastInsertId();

    // Insert cart items into `order_items` table
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

    // Send success response
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Send error response
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
