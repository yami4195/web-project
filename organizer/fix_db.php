<?php
/**
 * DB Migration Helper
 * 
 * Run this script to ensure your database has the required 'image' column.
 */

require_once '../includes/db.php';

try {
    // Check if column exists
    $result = $conn->query("SHOW COLUMNS FROM events LIKE 'image'");

    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE events ADD COLUMN image VARCHAR(255) AFTER price");
        echo "Success: 'image' column added successfully.";
    } else {
        echo "Note: 'image' column already exists.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
<br><br>
<a href="dashboard.php">Back to Dashboard</a>