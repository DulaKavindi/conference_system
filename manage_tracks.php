<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

// Use PDO for querying the database
$query = "SELECT * FROM tracks";
$stmt = $conn->query($query);
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tracks</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <header>
        <h1>Manage Tracks</h1>
    </header>
    <table>
        <thead>
            <tr>
                <th>Track ID</th>
                <th>Title</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tracks as $track) { ?>
                <tr>
                    <td><?= $track['track_id'] ?></td>
                    <td><?= $track['title'] ?></td>
                    <td><?= $track['description'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="index.php" class="back-btn">Back to Dashboard</a>
</body>
</html>
