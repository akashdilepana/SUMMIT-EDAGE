<?php
session_start(); 

$host = 'localhost';
$dbname = 'summit_edge';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());   
}

// session_start(); 

// $host = 'localhost';
// $dbname = 'summit_edge';
// $user = 'root';
// $pass = '';

// try {
//     // Initialize the database connection
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Error connecting to database: " . $e->getMessage());
// }

