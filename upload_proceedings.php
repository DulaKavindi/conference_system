<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileName = $_FILES['proceeding']['name'];
    $filePath = "../uploads/" . $fileName;

    if (move_uploaded_file($_FILES['proceeding']['tmp_name'], $filePath)) {
        $query = "INSERT INTO proceedings (file_name, file_path) VALUES ('$fileName', '$filePath')";
        mysqli_query($conn, $query);
        $success = "Proceeding uploaded successfully!";
    } else {
        $error = "Failed to upload the proceeding.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Proceedings</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <header>
    <h1>Upload Proceedings</h1>
</header>
    <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="proceeding">Select File:</label>
        <input type="file" name="proceeding" id="proceeding" required>
        <button type="submit">Upload</button>
    </form>
</br>
</br>
    <a href="index.php" class="back-btn">Back to Dashboard</a>
</body>
</html>
