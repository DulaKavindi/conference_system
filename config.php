<?php
// Database connection details
define('DB_HOST', 'localhost'); // Your database host
define('DB_NAME', 'conference_db'); // Your database name
define('DB_USER', 'root'); // Your database username (default for WAMP is root)
define('DB_PASS', ''); // Your database password (default for WAMP is an empty string)

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optionally, set the default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there's an error, display a message and stop execution
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
