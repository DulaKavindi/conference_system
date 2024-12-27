<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

// Use PDO for the query
$query = "SELECT * FROM participants";
$stmt = $conn->query($query);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participants</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <header>
        <h1>Manage Participants</h1>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Sessions Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($participants as $participant) { ?>
                <tr>
                    <td><?= $participant['participant_id'] ?></td>
                    <td><?= $participant['name'] ?></td>
                    <td><?= $participant['email'] ?></td>
                    <td><?= $participant['sessions_registered'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="index.php" class="back-btn">Back to Dashboard</a>
</body>
</html>
