<?php
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Correctly use $_POST to capture form data
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $date = $_POST['date']; // Corrected the key for 'date'
    $time = $_POST['time'];  // Corrected the key for 'time'
    $message = $_POST['message'];  // Corrected the key for 'message'

    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO `reservation` (`name`, `phone`, `date`, `time`, `message`) 
            VALUES (:name, :phone, :date, :time, :message)");

        // Bind the parameters to the SQL query
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
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
