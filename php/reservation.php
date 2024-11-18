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
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO `reservation` (`name`, `phone`, `date`, `time`,`person`, `message`) 
            VALUES (:name, :phone, :date, :time, :person, :message)");

        // Bind the parameters to the SQL query
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':person', $person);
        $stmt->bindParam(':message', $message);

        // Execute the query
        if ($stmt->execute()) {
            echo "ok";  // Success response
        } else {
            echo "error";  // Error response
        }
    } catch (PDOException $e) {
        // Catch and display any errors during the execution
        echo "Error: " . $e->getMessage();
    }
}
?>
