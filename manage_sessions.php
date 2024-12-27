<?php
session_start();
include '../config.php'; // Include the database connection file

// Redirect non-admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Add a new session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_session'])) {
    $track_id = $_POST['track_id'];
    $title = $_POST['title'];
    $speaker = $_POST['speaker'];
    $time = $_POST['time'];
    $venue = $_POST['venue'];
    $capacity = $_POST['capacity'];

    // Insert new session into the database
    $query = "INSERT INTO sessions (track_id, title, speaker, time, venue, capacity) 
              VALUES (:track_id, :title, :speaker, :time, :venue, :capacity)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':track_id', $track_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':speaker', $speaker);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':venue', $venue);
    $stmt->bindParam(':capacity', $capacity);

    if ($stmt->execute()) {
        $success = "Session added successfully!";
    } else {
        $error = "Error adding session.";
    }
}

// Delete a session
if (isset($_GET['delete'])) {
    $session_id = $_GET['delete'];
    $query = "DELETE FROM sessions WHERE session_id = :session_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':session_id', $session_id);

    if ($stmt->execute()) {
        $success = "Session deleted successfully!";
    } else {
        $error = "Error deleting session.";
    }
}

// Fetch all sessions with track details
$sessions_query = "SELECT sessions.*, tracks.title AS track_title 
                   FROM sessions INNER JOIN tracks ON sessions.track_id = tracks.track_id";
$sessions_stmt = $conn->query($sessions_query);
if ($sessions_stmt) {
    $sessions_result = $sessions_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sessions_result = [];
    $error = "Error fetching session data.";
}

// Fetch all tracks for dropdown
$tracks_query = "SELECT * FROM tracks";
$tracks_stmt = $conn->query($tracks_query);
$tracks_result = $tracks_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sessions</title>
    <link rel="stylesheet" href="styles1.css">
    <style>
        .message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success a, .error a {
            color: inherit;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Sessions</h1>
    </header>
    <div class="container">

        <?php if (isset($success)) { echo "<div class='message success'>$success</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='message error'>$error</div>"; } ?>

        <h2>Add New Session</h2>
        <form method="POST" action="">
            <label for="track_id">Track</label>
            <select name="track_id" id="track_id" required>
                <option value="">Select Track</option>
                <option value="">Track 1</option>
                <option value="">Track 2</option>
                <option value="">Track 3</option>
                <?php foreach ($tracks_result as $track) { ?>
                    <option value="<?= $track['track_id']; ?>"><?= $track['title']; ?></option>
                <?php } ?>
            </select>

            <label for="title">Session Title</label>
            <input type="text" id="title" name="title" required>

            <label for="speaker">Speaker</label>
            <input type="text" id="speaker" name="speaker" required>

            <label for="time">Time</label>
            <input type="datetime-local" id="time" name="time" required>

                </br>
                </br>

            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue" required>

            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" required>

            <button type="submit" name="add_session">Add Session</button>
        </form>

        <h2>Existing Sessions</h2>
        <table>
            <thead>
                <tr>
                    <th>Session ID</th>
                    <th>Track</th>
                    <th>Title</th>
                    <th>Speaker</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Check if sessions_result is empty
                if (empty($sessions_result)) {
                    echo "<tr><td colspan='8' class='error'>No sessions found.</td></tr>";
                } else {
                    foreach ($sessions_result as $session) { ?>
                        <tr>
                            <td><?= $session['session_id']; ?></td>
                            <td><?= $session['track_title']; ?></td>
                            <td><?= $session['title']; ?></td>
                            <td><?= $session['speaker']; ?></td>
                            <td><?= $session['time']; ?></td>
                            <td><?= $session['venue']; ?></td>
                            <td><?= $session['capacity']; ?></td>
                            <td>
                                <a href="manage_sessions.php?delete=<?= $session['session_id']; ?>" onclick="return confirm('Are you sure you want to delete this session?')">Delete</a>
                            </td>
                        </tr>
                    <?php }
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="index.php">Back to Admin Dashboard</a>
</body>
</html>
