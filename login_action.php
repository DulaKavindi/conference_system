<?php
session_start();
include '../config.php'; // Adjust the path to your database connection file.
require_once('../libs/phpqrcode/qrlib.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the database to validate credentials.
    $query = "SELECT * FROM participants WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $participant = $result->fetch_assoc();

        // Verify password (hashed in the database).
        if (password_verify($password, $participant['password'])) {
            $_SESSION['role'] = 'participant';
            $_SESSION['name'] = $participant['name'];
            $_SESSION['email'] = $participant['email'];

            header("Location: dashboard.php"); // Redirect to participant dashboard.
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Participant not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
