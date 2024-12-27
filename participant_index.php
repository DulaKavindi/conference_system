<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['participant_id'])) {
    header("Location: login.php");
    exit();
}

// Include database configuration
include '../config.php';

// Get the participant's ID from the session
$participantId = $_SESSION['participant_id'];

// Fetch participant information
$stmt = $pdo->prepare("SELECT * FROM participants WHERE participant_id = :participant_id");
$stmt->execute(['participant_id' => $participantId]);
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    header("Location: login.php");
    exit();
}

// Get the QR code file path
$qrCodeFile = "../participant/qrcodes/qr_$participantId.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        /* Body and container styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .container {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .container2 {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #B2BEB5
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .qr-code img {
            max-width: 250px;  /* Adjust the size as needed */
            max-height: 250px; /* Adjust the size as needed */
            margin-bottom: 20px;
        }

        .qr-code h3 {
            margin-bottom: 10px;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }

        nav a {
            display: block;
            margin: 10px 0;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Welcome, <?php echo htmlspecialchars($participant['name']); ?></h2>
        </header>

        <!-- QR Code Display -->
        <div class="qr-code">
            <h3>Your QR Code:</h3>
            <img src="<?php echo $qrCodeFile; ?>" alt="QR Code">
        </div>

    </div>
    <div class="container2">
    <header>
            <h2>Partisipant Dashboard</h2>
        </header>
    <nav>
            <a href="register.php">Digital Registration</a><br>
            <a href="manage_sessions.php">Conference Information</a><br>
            <a href="register_sessions.php">Session Registration</a><br>
            <a href="login.php" class="logout-btn">Logout</a><br>
        </nav>
    </div>
</body>
</html>
