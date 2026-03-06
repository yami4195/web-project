<?php
/**
 * Edit Event
 *
 * Form for organizers to edit existing events.
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

// Fetch event details and verify ownership
try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->bind_param("ii", $event_id, $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching event for edit: " . $e->getMessage());
}

if (!$event) {
    header("Location: dashboard.php?error=Event not found or access denied.");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $price = $_POST['price'] ?? 0;

    // Basic validation
    if (empty($title) || empty($description) || empty($location) || empty($event_date)) {
        $error = "All fields are required.";
    } else {
        $image_name = $event['image'] ?? null; // Keep existing image by default

        // Handle New Image Upload if provided
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['event_image']['tmp_name'];
            $file_name = $_FILES['event_image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $new_file_name = uniqid('event_', true) . '.' . $file_ext;
                $upload_dir = '../uploads/';

                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    // Delete old image file if it exists
                    if ($event['image'] && file_exists($upload_dir . $event['image'])) {
                        unlink($upload_dir . $event['image']);
                    }
                    $image_name = $new_file_name;
                }
            }
        }

        try {
            $update_stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, location = ?, event_date = ?, price = ?, image = ? WHERE id = ? AND organizer_id = ?");
            $update_stmt->bind_param("ssssdsii", $title, $description, $location, $event_date, $price, $image_name, $event_id, $organizer_id);

            if ($update_stmt->execute()) {
                header("Location: dashboard.php?success=Event updated successfully!");
                exit();
            } else {
                $error = "Failed to update event.";
            }
            $update_stmt->close();
        } catch (mysqli_sql_exception $e) {
            error_log("Error updating event: " . $e->getMessage());
            $error = "An unexpected error occurred.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Evently</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f9fafb;
            --card-bg: #ffffff;
            --text: #111827;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        nav {
            background-color: white;
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav h2 {
            font-size: 1.25rem;
            color: var(--primary);
        }

        .container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 2.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
        }

        .btn-submit {
            background-color: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }

        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .current-img {
            display: block;
            max-width: 200px;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <nav>
        <h2>Evently.</h2>
    </nav>

    <div class="container">
        <h1 style="margin-bottom: 2rem;">Edit Event</h1>

        <?php if (isset($error)): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Event Title</label>
                    <input type="text" name="title" class="form-input"
                        value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"
                        required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-input"
                        value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Event Date</label>
                        <input type="datetime-local" name="event_date" class="form-input"
                            value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" step="0.01" class="form-input"
                            value="<?php echo htmlspecialchars($event['price']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Event Image</label>
                    <?php if (isset($event['image']) && $event['image']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="Current Image"
                            class="current-img">
                    <?php endif; ?>
                    <input type="file" name="event_image" class="form-input" accept="image/*">
                    <?php if (!isset($event['image'])): ?>
                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.5rem;">Warning: 'image' column missing
                            in database. Please run the SQL migration.</p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">Update Event</button>
            </form>
        </div>
    </div>
</body>

</html>
<?php $conn->close(); ?>