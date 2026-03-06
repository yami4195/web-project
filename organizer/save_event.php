<?php
/**
 * Save Event Handler
 *
 * Processes the POST request from create_event.php and saves the event to the database.
 */

session_start();
require_once '../includes/db.php';

// Access control: Ensure user is logged in and is an organizer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php?error=Access denied.");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $price = $_POST['price'] ?? 0;

    // Get organizer ID from session
    $organizer_id = $_SESSION['user_id'] ?? null;

    // Basic validation
    if (empty($title) || empty($description) || empty($location) || empty($event_date) || is_null($organizer_id)) {
        header("Location: create_event.php?error=All fields are required.");
        exit();
    }

    // Handle Image Upload
    $image_name = null;
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['event_image']['tmp_name'];
        $file_name = $_FILES['event_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = uniqid('event_', true) . '.' . $file_ext;
            $upload_dir = '../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $image_name = $new_file_name;
            }
        }
    }

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO events (title, description, location, event_date, price, image, organizer_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param("ssssdsi", $title, $description, $location, $event_date, $price, $image_name, $organizer_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Success: Redirect to my_event.php with success message
            header("Location: my_event.php?success=Event created successfully!");
            exit();
        } else {
            // Failure
            header("Location: create_event.php?error=Failed to save event. Please try again.");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Error saving event: " . $e->getMessage());
        header("Location: create_event.php?error=An unexpected error occurred.");
        exit();
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    // If not a POST request, redirect back to create_event.php
    header("Location: create_event.php");
    exit();
}
