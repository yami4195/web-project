<?php
/**
 * My Events
 *
 * Displays a list of events created by the logged-in organizer.
 */

session_start();
require_once '../includes/db.php';

// Access control: Ensure user is logged in and is an organizer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php?error=Access denied. Organizers only.");
    exit();
}

$organizer_id = $_SESSION['user_id'];
$organizer_name = $_SESSION['user_name'] ?? 'Organizer';

// Fetch events for this organizer
try {
    $stmt = $conn->prepare("SELECT id, title, description, location, event_date, price, created_at FROM events WHERE organizer_id = ? ORDER BY event_date ASC");
    $stmt->bind_param("i", $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching events: " . $e->getMessage());
    $events = [];
    $error = "Failed to load events.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Evently</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f9fafb;
            --nav-bg: #ffffff;
            --card-bg: #ffffff;
            --text: #111827;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --success: #059669;
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
            background-color: var(--nav-bg);
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

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .nav-menu a:hover {
            color: var(--primary);
        }

        .logout-link {
            color: #dc2626 !important;
        }

        .container {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header-section h1 {
            font-size: 1.875rem;
            font-weight: 700;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .event-table th {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-light);
            border-bottom: 1px solid var(--border);
        }

        .event-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }

        .event-table tr:last-child td {
            border-bottom: none;
        }

        .event-title {
            font-weight: 600;
            color: var(--text);
            display: block;
        }

        .event-meta {
            color: var(--text-light);
            font-size: 0.75rem;
        }

        .price-badge {
            background-color: #ecfdf5;
            color: var(--success);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 1rem;
        }

        .action-link {
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .edit-link {
            color: var(--primary);
        }

        .delete-link {
            color: #dc2626;
        }

        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: var(--text-light);
        }

        .empty-state h3 {
            color: var(--text);
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <nav>
        <h2>Evently.</h2>
        <ul class="nav-menu">
            <li><a href="my_event.php" style="color: var(--primary);">My Events</a></li>

            <li><a href="#">Bookings</a></li>
            <li><a href="../logout.php" class="logout-link">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>My Events</h1>
            <a href="create_event.php" class="btn-primary">+ New Event</a>
        </div>
        <div class="container">
            <a href="dashboard.php" class="back-link">
                &larr; Back to Dashboard
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div
                style="background-color: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0; font-size: 0.875rem;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <?php if (empty($events)): ?>
                <div class="empty-state">
                    <h3>No events found</h3>
                    <p>You haven't created any events yet. Click the button above to get started!</p>
                </div>
            <?php else: ?>
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Event Details</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <span class="event-title">
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </span>
                                    <span class="event-meta">Created on
                                        <?php echo date('M d, Y', strtotime($event['created_at'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($event['event_date'])); ?><br>
                                    <span class="event-meta">
                                        <?php echo date('h:i A', strtotime($event['event_date'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </td>
                                <td>
                                    <span class="price-badge">
                                        <?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'Free'; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>"
                                        class="action-link edit-link">Edit</a>
                                    <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="action-link delete-link"
                                        onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>