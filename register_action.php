<?php
session_start();
require_once('../config.php'); // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture participant details
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $organization = htmlspecialchars($_POST['organization']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Create a unique string for the QR code
    $participant_info = "Name: $name\nEmail: $email\nOrganization: $organization";

    // Define the filename for the QR code
    $file_name = md5($email) . '.png'; // Unique filename using email hash
    $file_path = '../qrcodes/' . $file_name; // Path to save the QR code

    // Generate and save the QR code
    if (!file_exists('../qrcodes')) {
        mkdir('../qrcodes', 0777, true); // Create folder if not exists
    }

    // Use the PHP QR Code library to generate the QR code
    require_once('../libs/phpqrcode/qrlib.php');
    QRcode::png($participant_info, $file_path, QR_ECLEVEL_L, 4, 4);

    // Save the participant information to the database
    try {
        $sql = "INSERT INTO participants (name, email, organization, password, QR_code) VALUES (:name, :email, :organization, :password, :qr_code)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':organization', $organization);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':qr_code', $file_path);
        $stmt->execute();

        // Save the participant info in session for later use
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['organization'] = $organization;
        $_SESSION['qr_code'] = $file_path;

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
