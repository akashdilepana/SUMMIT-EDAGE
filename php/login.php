<?php

include 'database.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :name AND password = :password AND status = 'active'");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $password);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['usertype'] = $user['user_type'];
        $_SESSION['image'] = $user['image'];

        echo "ok";
    } else {
        echo "inactive_or_no_user";
    }
} else {
    echo "no";
}
?>