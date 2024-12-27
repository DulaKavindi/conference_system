<?php
session_start();

// Include database configuration
include '../config.php';

// Include the QR code library
include '../libs/phpqrcode/qrlib.php';

$message = ""; // Message to show feedback to users

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (!empty($name) && !empty($email) && !empty($password)) {
        try {
            // Check if the email already exists in the database
            $stmt = $pdo->prepare("SELECT * FROM participants WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $existingParticipant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingParticipant) {
                $message = "This email is already registered. Please log in.";
            } else {
                // Hash the password before storing it
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the participant into the database
                $stmt = $pdo->prepare("INSERT INTO participants (name, email, password) VALUES (:name, :email, :password)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);

                // Get the participant's ID
                $participantId = $pdo->lastInsertId();

                // Set session variables
                $_SESSION['participant_id'] = $participantId;
                $_SESSION['name'] = $name;

                // Generate the QR code
                $qrCodeText = "Participant: $name | ID: $participantId"; // You can adjust this text
                $qrCodeFile = "../participant/qrcodes/qr_$participantId.png"; // Save the QR code in the qrcodes folder

                // Generate and save the QR code
                QRcode::png($qrCodeText, $qrCodeFile);

                // Redirect to participant dashboard
                header("Location: participant_index.php");
                exit();
            }
        } catch (PDOException $e) {
            $message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
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
        <h2>Participant Registration</h2>
    </header>

    <div class="container">
        <!-- Display error message if any -->
        <?php if ($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
        
        <div class="message">
            
        </div>
    </div>
</body>
</html>
