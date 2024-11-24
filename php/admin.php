<?php

include 'database.php';

$option = $_GET['option'] ?? '';

$response = [];

switch ($option) {
    case 'user':
        $stmt = $conn->query("SELECT * FROM users;");
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'menu':
        $stmt = $conn->query("SELECT * FROM menu");
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'reservation':
        $stmt = $conn->query("SELECT * FROM reservation");
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'oders':
        $stmt = $conn->query("SELECT o.id AS order_id, o.name AS customer_name, o.address AS delivery_address, o.phone AS customer_phone,
         o.payment_method, o.total_amount, GROUP_CONCAT(i.name ORDER BY oi.item_id) AS item_names, GROUP_CONCAT(oi.quantity ORDER BY oi.item_id)
          AS quantities, GROUP_CONCAT(oi.price ORDER BY oi.item_id) AS prices, GROUP_CONCAT((oi.quantity * oi.price) ORDER BY oi.item_id)
           AS item_totals FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN menu i ON oi.item_id = i.id GROUP BY o.id ORDER BY o.id DESC;");
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
