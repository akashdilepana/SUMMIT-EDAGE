<?php
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $date = $_POST['date']; 
    $time = $_POST['time'];
    $person = $_POST['person']; 
    $message = $_POST['message']; 

    try {
        $stmt = $conn->prepare("INSERT INTO `reservation` (`name`, `phone`, `date`, `time`,`person`, `message`) 
            VALUES (:name, :phone, :date, :time, :person, :message)");

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':person', $person);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo "ok";  
        } else {
            echo "error";  
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
