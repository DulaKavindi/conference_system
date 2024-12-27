<?php
session_start();

// Ensure the participant is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'participant') {
    header("Location: login.php"); // Redirect to login if not logged in as a participant
    exit();
}

require_once('../config.php'); // Database connection

// Fetch sessions sorted by track and time
$query = "SELECT * FROM sessions ORDER BY track_id, time";
$stmt = $conn->prepare($query);
$stmt->execute();
$sessions = $stmt->fetchAll();

// Fetch the participant's QR code
$qr_code = $_SESSION['qr_code'] ?? '';

// Fetch registered sessions for the participant
$participant_email = $_SESSION['email'];
$session_reg_query = "SELECT session_id FROM session_registrations WHERE participant_email = :email";
$stmt_reg = $conn->prepare($session_reg_query);
$stmt_reg->bindParam(':email', $participant_email);
$stmt_reg->execute();
$registered_sessions = $stmt_reg->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <section>
            <h2>Your Digital Registration</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Organization:</strong> <?php echo htmlspecialchars($_SESSION['organization']); ?></p>
            <p><strong>Your QR Code:</strong></p>
            <img src="../qrcodes/<?php echo htmlspecialchars($qr_code); ?>" alt="QR Code" />
        </section>

        <section>
            <h2>Conference Schedule</h2>
            <?php if ($sessions): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Track</th>
                            <th>Title</th>
                            <th>Speaker</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Register</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($session['track_id']); ?></td>
                                <td><?php echo htmlspecialchars($session['title']); ?></td>
                                <td><?php echo htmlspecialchars($session['speaker']); ?></td>
                                <td><?php echo htmlspecialchars($session['time']); ?></td>
                                <td><?php echo htmlspecialchars($session['venue']); ?></td>
                                <td>
                                    <?php if (in_array($session['session_id'], $registered_sessions)): ?>
                                        <span>Already Registered</span>
                                    <?php else: ?>
                                        <form method="POST" action="register_session.php">
                                            <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                            <button type="submit">Register</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No sessions available at the moment.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>QR Code Check-in</h2>
            <p>If you're attending a session, use your QR code to check-in at the venue.</p>
            <form method="POST" action="check_in.php">
                <label for="session_id">Enter Session ID to check-in:</label>
                <input type="number" id="session_id" name="session_id" required>
                <button type="submit">Check-in</button>
            </form>
        </section>
    </main>
</body>
</html>
