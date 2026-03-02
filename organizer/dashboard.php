<?php
/**
 * Organizer Dashboard
 *
 * Management interface for event organizers to create and track events.
 */

session_start();

// Access control: Ensure user is logged in and is an organizer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php?error=Access denied. Organizers only.");
    exit();
}

$organizer_name = $_SESSION['user_name'] ?? 'Organizer';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Event Booking</title>
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

        .event-info h4 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .event-info p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-val {
            display: block;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <nav>
        <h2>Evently.</h2>
        <ul class="nav-menu">
            <li><a href="#" style="color: var(--primary);">My Events</a></li>
            <li><a href="#">Create Event</a></li>
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
            <a href="#" class="btn-primary">+ New Event</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1.5rem;">Your Events</h3>
            <ul class="event-list">
                <li class="event-item">
                    <div class="event-info">
                        <h4>Tech Summit 2026</h4>
                        <p>March 15, 2026 • Convention Center</p>
                    </div>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="stat-val">120</span>
                            <span class="stat-label">Sales</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-val">$2.4k</span>
                            <span class="stat-label">Revenue</span>
                        </div>
                    </div>
                </li>
                <li class="event-item">
                    <div class="event-info">
                        <h4>Acoustic Night</h4>
                        <p>April 02, 2026 • The Blue Room</p>
                    </div>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="stat-val">45</span>
                            <span class="stat-label">Sales</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-val">$900</span>
                            <span class="stat-label">Revenue</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</body>

</html>