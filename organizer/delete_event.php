<?php
/**
 * Delete Event
 *
 * Deletes an event and its associated image file.
 */

session_start();
require_once '../includes/db.php';

// Access control: Ensure user is logged in and is an organizer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php?error=Access denied.");
    exit();
}

$organizer_id = $_SESSION['user_id'];
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    header("Location: dashboard.php");
    exit();
}

try {
    // 1. Fetch event to confirm ownership and get image filename
    $stmt = $conn->prepare("SELECT image FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->bind_param("ii", $event_id, $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    if (!$event) {
        header("Location: dashboard.php?error=Event not found or access denied.");
        exit();
    }

    // 2. Delete the database record
    $del_stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND organizer_id = ?");
    $del_stmt->bind_param("ii", $event_id, $organizer_id);

    if ($del_stmt->execute()) {
        // 3. Delete the image file if it exists
        if ($event['image'] && file_exists('../uploads/' . $event['image'])) {
            unlink('../uploads/' . $event['image']);
        }
        header("Location: dashboard.php?success=Event deleted successfully!");
    } else {
        header("Location: dashboard.php?error=Failed to delete event.");
    }
    $del_stmt->close();

} catch (mysqli_sql_exception $e) {
    error_log("Error deleting event: " . $e->getMessage());
    header("Location: dashboard.php?error=An unexpected error occurred.");
} finally {
    $conn->close();
}
