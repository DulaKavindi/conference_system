<?php
session_start();
require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['email'])) {
    $session_id = $_POST['session_id'];
    $email = $_SESSION['email'];

    // Insert check-in record into the attendance table
    try {
        $sql = "INSERT INTO attendance (session_id, participant_email, check_in_time) VALUES (:session_id, :email, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Redirect to the dashboard with a check-in success message
        header("Location: index.php?checkin=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
