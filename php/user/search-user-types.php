<?php
include '../database.php'; 
$search = $_POST['search'] ?? '';

$sql = "SELECT id, user_type FROM user_type WHERE user_type LIKE :search LIMIT 10"; 

$stmt = $conn->prepare($sql);

$searchTerm = '%' . $search . '%';
$stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);

$stmt->execute();

$userTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];

foreach ($userTypes as $userType) {
    $response[] = [
        'text' => $userType['user_type'], // The text to display in the select dropdown
        'id'   => $userType['id']         // The value to be stored when selected
    ];
}

// Return the response as JSON
echo json_encode($response);
?>
