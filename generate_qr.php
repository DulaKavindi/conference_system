<?php
// Include the QR Code library (adjust the path based on your setup)
include('C:\wamp64\www\conference_system\libs\phpqrcode\qrlib.php');

// Start session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get participant details from the registration form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $organization = htmlspecialchars($_POST['organization']);
    
    // Combine details to create a unique identifier for the participant (you can customize this)
    $participant_info = "Name: $name, Email: $email, Organization: $organization";
    
    // Define a unique filename for the QR code (e.g., based on email or participant ID)
    $qr_code_filename = 'qrcodes/' . md5($email) . '.png';
    
    // Generate QR code and save it as a PNG file
    QRcodes::png($participant_info, $qr_code_filename, QR_ECLEVEL_L, 4, 4);
    
    // Save participant data to database (example: using a database handler)
    // You can implement database insertion here
    
    // Set session message for success
    $_SESSION['message'] = "Registration successful! Your QR code has been generated.";
    $_SESSION['qr_code'] = $qr_code_filename; // Store the generated QR code path in session
    header("Location: participant_dashboard.php"); // Redirect to dashboard or success page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Registration</title>
</head>
<body>
    <header>
        <h1>Participant Registration</h1>
    </header>
    <main>
        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="organization">Organization:</label>
            <input type="text" id="organization" name="organization" required>

            <button type="submit">Register</button>
        </form>
    </main>
</body>
</html>
