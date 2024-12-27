<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register.php"); // Redirect to register if not logged in
    exit();
}

include '../config.php'; // Include your database connection file

// Fetch participant details from the participants table
$query = "SELECT QR_code FROM participants WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
$stmt->execute();
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    echo "Participant not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <header>
        <h1>Welcome to the Conference</h1>
    </header>
    <main>
        <h2>Your QR Code</h2>
        <?php if (isset($participant['QR_code']) && $participant['QR_code']): ?>
            <img src="../qrcodes/<?php echo htmlspecialchars($participant['QR_code']); ?>" alt="QR Code">
        <?php else: ?>
            <p>QR code not found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
