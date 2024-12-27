<?php
// Include database connection file
require_once('../config.php');

// Get QR data and session ID from POST request
$qrData = $_POST['qrData'] ?? null;
$session_id = $_POST['session_id'] ?? null;

if (!$qrData || !$session_id) {
    echo json_encode(["error" => "QR data or Session ID is missing."]);
    exit;
}

try {
    // Step 1: Verify the QR code (find participant_id based on the QR code)
    $stmt = $conn->prepare("SELECT participant_id FROM participants WHERE qr_code = :qr_code");
    $stmt->bindParam(':qr_code', $qrData, PDO::PARAM_STR);
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($participant) {
        // Step 2: Check if the participant is registered for the session
        $stmt = $conn->prepare(
            "SELECT p.participant_id, s.session_id
            FROM participants p
            JOIN session_registrations sr ON p.participant_id = sr.participant_id
            JOIN sessions s ON s.session_id = sr.session_id
            WHERE p.participant_id = :participant_id AND s.session_id = :session_id"
        );
        
        $stmt->bindParam(':participant_id', $participant['participant_id'], PDO::PARAM_INT);
        $stmt->bindParam(':session_id', $session_id, PDO::PARAM_INT);
        $stmt->execute();

        // Step 3: Check if the participant is registered for the session
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registration) {
            // Step 4: Mark attendance if the participant is registered for the session
            $stmt = $conn->prepare("INSERT INTO attendance (participant_id, session_id, check_in_time) VALUES (:participant_id, :session_id, NOW())");
            $stmt->bindParam(':participant_id', $participant['participant_id'], PDO::PARAM_INT);
            $stmt->bindParam(':session_id', $session_id, PDO::PARAM_INT);

            // Execute attendance insertion
            if ($stmt->execute()) {
                echo json_encode(["message" => "Attendance marked successfully."]);
            } else {
                echo json_encode(["error" => "Failed to mark attendance."]);
            }
        } else {
            // If the participant is not registered for the session
            echo json_encode(["error" => "Participant is not registered for this session."]);
        }
    } else {
        // If the QR code is invalid
        echo json_encode(["error" => "Invalid QR code."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
