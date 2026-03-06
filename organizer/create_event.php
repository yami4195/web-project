<?php
/**
 * Create New Event
 *
 * Form for organizers to create new events.
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
    <title>Create New Event - Evently</title>
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
            --input-focus: #4f46e5;
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
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        .header-section {
            margin-bottom: 2rem;
        }

        .header-section h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header-section p {
            color: var(--text-light);
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 2.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background-color: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav>
        <h2>Evently.</h2>
        <ul class="nav-menu">
            <li><a href="my_event.php">My Events</a></li>

            <li><a href="#">Bookings</a></li>
            <li><a href="../logout.php" class="logout-link">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <a href="dashboard.php" class="back-link">
            &larr; Back to Dashboard
        </a>

        <div class="header-section">
            <h1>Create New Event</h1>
            <p>Fill in the details below to publish your new event.</p>
        </div>

        <div class="card">
            <form action="save_event.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title" class="form-label">Event Title</label>
                    <input type="text" id="title" name="title" class="form-input"
                        placeholder="e.g. Modern Web Development Workshop" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea"
                        placeholder="Describe what your event is about..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-input"
                        placeholder="e.g. Grand Plaza or Online" required>
                </div>

                <div class="grid">
                    <div class="form-group">
                        <label for="event_date" class="form-label">Event Date & Time</label>
                        <input type="datetime-local" id="event_date" name="event_date" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Ticket Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="form-input"
                            placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="event_image" class="form-label">Event Image</label>
                    <input type="file" id="event_image" name="event_image" class="form-input" accept="image/*">
                    <p class="event-meta" style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--text-light);">
                        Supported formats: JPG, PNG, WEBP. Max size: 2MB.</p>
                </div>

                <button type="submit" class="btn-submit">Publish Event</button>
            </form>
        </div>
    </div>
</body>

</html>