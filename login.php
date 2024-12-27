<?php
session_start();

// Include database configuration
include '../config.php';

$message = ""; // Message to show feedback to users

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            // Query to check if the participant exists
            $stmt = $pdo->prepare("SELECT * FROM participants WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($participant) {
                // Verify password
                if (password_verify($password, $participant['password'])) {
                    $_SESSION['participant_id'] = $participant['participant_id']; // Save participant ID to session
                    $_SESSION['name'] = $participant['name'];
                    header("Location: dashboard.php"); // Redirect to participant dashboard
                    exit;
                } else {
                    $message = "Invalid password. Please try again.";
                }
            } else {
                $message = "No account found with this email. Please register.";
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
    <title>Participant Login</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        body { font-family: Arial, sans-serif;
             margin: 20px; }
        
        .container { max-width: 400px; 
            margin: auto; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 5px; }

        h2 { text-align: center; }

        .error { color: red; }

        .form-group { margin-bottom: 15px; }

        label { display: block; 
            margin-bottom: 5px; }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        }

        button { padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            background-color: #28a745; 
            color: #fff; 
            cursor: pointer; }

        button:hover { background-color: #218838; }

        .message { margin-top: 10px; }
        
        p{color:white;}
    </style>
</head>
<body>
<header>
        <h2>Participant Login</h2>
    </header>
    <div class="container">
        
        <?php if ($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="message">
            <p><b>Don't have an account?</b> <a href="register.php">Register here</a>.</p>
        </div>
        
    </div>
</body>
</html>
