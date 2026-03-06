<?php
/**
 * Organizer Dashboard
 *
 * Management interface for event organizers to create and track events.
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

// Fetch summary metrics (total events, total revenue)
try {
    $metrics_stmt = $conn->prepare("SELECT COUNT(*) as event_count, SUM(price) as projected_revenue FROM events WHERE organizer_id = ?");
    $metrics_stmt->bind_param("i", $organizer_id);
    $metrics_stmt->execute();
    $metrics = $metrics_stmt->get_result()->fetch_assoc();
    $metrics_stmt->close();
} catch (Exception $e) {
    $metrics = ['event_count' => 0, 'projected_revenue' => 0];
}

// Fetch recent events for the dashboard
$events = [];
try {
    $stmt = $conn->prepare("SELECT id, title, location, event_date, image FROM events WHERE organizer_id = ? ORDER BY event_date DESC LIMIT 5");
    $stmt->bind_param("i", $organizer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching dashboard events: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Evently</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f9fafb;
            --nav-bg: #ffffff;
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
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .header-section h1 {
            font-size: 1.75rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .event-list {
            list-style: none;
        }

        .event-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border);
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .event-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #f1f5f9;
        }

        .event-info h4 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .event-info p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .event-actions {
            display: flex;
            gap: 1rem;
        }

        .action-link {
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }

        .edit-btn {
            background: #eef2ff;
            color: var(--primary);
        }

        .delete-btn {
            background: #fef2f2;
            color: #dc2626;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .stat-val {
            display: block;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 0;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <nav>
        <h2>Evently.</h2>
        <ul class="nav-menu">
            <li><a href="my_event.php">My Events</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="#">Bookings</a></li>
            <li><a href="../logout.php" class="logout-link">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <div>
                <p style="color: #6b7280; margin-bottom: 0.25rem;">Organizer Portal</p>
                <h1>Howdy, <?php echo htmlspecialchars($organizer_name); ?>!</h1>
            </div>

            <a href="create_event.php" class="btn-primary">+ New Event</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <span class="stat-val"><?php echo $metrics['event_count']; ?></span>
                <span class="stat-label">Total Events</span>
            </div>
            <div class="stat-card">
                <span class="stat-val">$<?php echo number_format($metrics['projected_revenue'] ?? 0, 2); ?></span>
                <span class="stat-label">Projected Revenue</span>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1.5rem;">Recent Events</h3>
            <ul class="event-list">
                <?php if (empty($events)): ?>
                    <div class="empty-state">
                        <p>No events found. Start by creating your first event!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <li class="event-item">
                            <div class="event-content">
                                <img src="../uploads/<?php echo $event['image'] ? htmlspecialchars($event['image']) : 'default_event.webp'; ?>"
                                    alt="event" class="event-img" onerror="this.src='https://via.placeholder.com/50'">
                                <div class="event-info">
                                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <p><?php echo date('M d, Y', strtotime($event['event_date'])); ?> •
                                        <?php echo htmlspecialchars($event['location']); ?></p>
                                </div>
                            </div>
                            <div class="event-actions">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="action-link edit-btn">Edit</a>
                                <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="action-link delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="my_event.php"
                    style="color: var(--primary); font-size: 0.875rem; text-decoration: none; font-weight: 600;">View
                    All Events &rarr;</a>
            </div>
        </div>
    </div>
</body>

</html>
<?php $conn->close(); ?>