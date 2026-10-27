<?php
// header.php - Included at the top of every page for global navigation
// Sets up sessions, dynamic page titles, and the navbar.

// Start the session only if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL for relative links across different module folders
// Update this if the repository folder name changes
$website_base_url = "/NSBM_SportsHub/";

// Fallback title if the current page doesn't define one
if (!isset($page_title)) {
    $page_title = "Home";
}

// Check if a user is currently logged in to toggle navbar links
$is_user_logged_in = isset($_SESSION['user_id']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NSBM SportsHub - Club & Facility Management">
    <title><?php echo htmlspecialchars($page_title); ?> | NSBM SportsHub</title>
    <link rel="stylesheet" href="<?php echo $website_base_url; ?>assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <a class="site-logo" href="<?php echo $website_base_url; ?>index.php">NSBM SportsHub</a>

        <nav class="main-navigation">
            <ul>
                <li><a href="<?php echo $website_base_url; ?>index.php">Home</a></li>
                <li><a href="<?php echo $website_base_url; ?>modules/clubs_directory/browse_clubs.php">Clubs</a></li>
                <li><a href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/fixture_list.php">Matches</a></li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "student"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/club_memberships/apply_club.php">Join a Club</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/club_memberships/approve_members.php">Approve Members</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "student"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/training_schedules/view_schedules.php">Schedules</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/training_schedules/manage_schedules.php">Manage Schedules</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/manage_tournaments.php">Tournaments</a></li>
                    <li><a href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/manage_fixtures.php">Manage Fixtures</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "student"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/match_results/completed_matches.php">Results</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/match_results/manage_results.php">Update Results</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "student"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/facility_bookings/book_facility.php">Book Facility</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/facility_bookings/manage_bookings.php">Approve Bookings</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/equipment_inventory/manage_inventory.php">Manage Equipment</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/equipment_inventory/inventory_list.php">Equipment</a></li>
                <?php endif; ?>

                <?php if ($is_user_logged_in): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/auth_profiles/profile.php">Profile</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/auth_profiles/login_view.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Main content starts here, closed in footer.php -->
    <main class="page-content">