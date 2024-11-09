<?php
// Database credentials
$host = 'localhost';
$dbname = 'summit_edge';
$user = 'root';
$pass = '';

// Connect to database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}

// Check if POST data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :name AND password = :password");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Check if user exists
    if ($stmt->rowCount() > 0) {
        echo "ok";
    } else {
        echo "no";
    }
}
?>