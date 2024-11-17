<?php

// include 'database.php';

// $option = $_GET['option'] ?? '';

// $response = [];

// switch ($option) {
//     case 'user':
//         $stmt = $conn->query("SELECT id, username, name, password, status, user_type FROM users;");
//         $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         break;

//     case 'food':
//         $stmt = $conn->query("SELECT * FROM food");
//         $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         break;


//     default:
//         $response = ['error' => 'Invalid option'];
//         break;
// }

// echo json_encode($response);


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

    default:
        $response = ['error' => 'Invalid option'];
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
