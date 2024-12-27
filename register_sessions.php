<?php
session_start();
include '../config.php'; // Include the config.php to access the $conn variable

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($name) && !empty($email) && !empty($organization) && !empty($password)) {
        try {
            // Check if the email is already registered
            $stmt = $conn->prepare("SELECT email FROM participants WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $message = "This email is already registered.";
            } else {
                // Generate QR Code content
                $qrContent = "Name: $name\nEmail: $email\nOrganization: $organization";

                // Generate QR Code and save it as an image
                $qrFile = "../qrcodes/" . uniqid() . ".png";
                include_once '../libs/phpqrcode/qrlib.php';
                QRcode::png($qrContent, $qrFile);

                // Hash the password for security
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insert participant data into the database
                $stmt = $conn->prepare("INSERT INTO participants (name, email, organization, password, QR_code) 
                                        VALUES (:name, :email, :organization, :password, :qr_code)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'organization' => $organization,
                    'password' => $hashedPassword,
                    'qr_code' => $qrFile
                ]);

                $message = "Registration successful! Your QR code has been generated.";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Registration</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <header>
    <h1>Register Session</h1>
</header>
    <?php if (!empty($message)) : ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="organization">Organization:</label>
        <input type="text" id="organization" name="organization" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="session">Choose a session:</label>
        <select name="session_id" id="session">
            <option value="1">Session 1: Introduction to AI</option>
            <option value="2">Session 2: Data Science Trends</option>
            <option value="3">Session 3: Web Development</option>
            <option value="4">Session 4: Machine Learning in Practice</option>
            <!-- Add more sessions as needed -->
        </select>

        <select

        <button type="submit">Register</button>
    </form>
</body>
</html>
