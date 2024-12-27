<?php
session_start();
require_once('../config.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['participant_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch the participant's information from the database
$participant_id = $_SESSION['participant_id'];
$stmt = $conn->prepare("SELECT * FROM participants WHERE participant_id = :participant_id");
$stmt->execute(['participant_id' => $participant_id]);
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch conference schedule and other details
$stmt_schedule = $conn->prepare("SELECT * FROM sessions ORDER BY time ASC");
$stmt_schedule->execute();
$sessions = $stmt_schedule->fetchAll(PDO::FETCH_ASSOC);

// Fetch keynote speakers information
$stmt_speakers = $conn->prepare("SELECT * FROM keynote_speakers");
$stmt_speakers->execute();
$speakers = $stmt_speakers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <link rel="stylesheet" href="styles2.css"> <!-- Add your custom CSS -->
</head>
<body>

    <!-- Participant Dashboard Header -->
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($participant['name']); ?></h1>
        <nav>
            <ul>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="update_profile.php">Update Profile</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <h2>Conference Information</h2>

        <!-- Keynote Speakers Section -->
        <div class="section">
            <h3>Keynote Speakers</h3>
            <ul>
                <?php foreach ($speakers as $speaker): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($speaker['name']); ?></strong> - <?php echo htmlspecialchars($speaker['topic']); ?><br>
                        <em><?php echo htmlspecialchars($speaker['time']); ?></em>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Conference Schedule Section -->
        <div class="section">
            <h3>Conference Schedule</h3>
            <table>
                <tr>
                    <th>Track</th>
                    <th>Session Title</th>
                    <th>Speaker</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Register</th>
                </tr>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session['track']); ?></td>
                        <td><?php echo htmlspecialchars($session['title']); ?></td>
                        <td><?php echo htmlspecialchars($session['speaker']); ?></td>
                        <td><?php echo htmlspecialchars($session['time']); ?></td>
                        <td><?php echo htmlspecialchars($session['venue']); ?></td>
                        <td>
                            <form method="post" action="register_session.php">
                                <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                <button type="submit">Register</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- QR Code Section -->
        <div class="section">
            <h3>Your QR Code</h3>
            <?php if (!empty($participant['qr_code'])): ?>
                <img src="../participant/qrcodes/<?php echo $participant['qr_code']; ?>" alt="QR Code" />
                <p>Scan this QR code for event entry.</p>
            <?php else: ?>
                <p>You haven't registered yet. Please register to receive a QR code.</p>
            <?php endif; ?>
        </div>

        <!-- QR-based Check-In Section -->
        <div class="section">
            <h3>Session Check-In</h3>
            <form method="post" action="mark_attendance.php">
                <label for="qrData">QR Code Data:</label>
                <input type="text" name="qrData" id="qrData" required>
                <label for="session_id">Session ID:</label>
                <input type="text" name="session_id" id="session_id" required>
                <button type="submit">Check In</button>
            </form>
        </div>

    </div>

</body>
</html>
