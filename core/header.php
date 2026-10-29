<?php
// header.php - Included at the top of every page for the loader and the navigation menu.
// Sets up the session, the page title and the role-based menu links.

// Start the session only if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL for links across different module folders
// Update this if the project folder name changes
$website_base_url = "/NSBM_SportsHub/";

// Fallback title if the current page doesn't define one
if (!isset($page_title)) {
    $page_title = "Home";
}

// Check if a user is logged in and what their role is (guest = not logged in)
$is_user_logged_in = isset($_SESSION['user_id']) ? true : false;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : "guest";

// Choose where each menu item goes. Admins get the manage pages,
// students and guests get the normal pages.
// (Guests who click Memberships or Bookings are sent to the login page by auth_check.php)
if ($user_role == "admin") {
    $clubs_link         = "modules/clubs_directory/manage_clubs.php";
    $memberships_link   = "modules/club_memberships/approve_members.php";
    $schedules_link     = "modules/training_schedules/manage_schedules.php";
    $tournaments_link   = "modules/tournaments_fixtures/manage_tournaments.php";
    $results_link       = "modules/match_results/manage_results.php";
    $bookings_link      = "modules/facility_bookings/manage_bookings.php";
    $equipment_link     = "modules/equipment_inventory/manage_inventory.php";
} else {
    $clubs_link         = "modules/clubs_directory/browse_clubs.php";
    $memberships_link   = "modules/club_memberships/apply_club.php";
    $schedules_link     = "modules/training_schedules/view_schedules.php";
    $tournaments_link   = "modules/tournaments_fixtures/fixture_list.php";
    $results_link       = "modules/match_results/completed_matches.php";
    $bookings_link      = "modules/facility_bookings/book_facility.php";
    $equipment_link     = "modules/equipment_inventory/inventory_list.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NSBM SportsHub - Club &amp; Facility Management">
    <title><?php echo htmlspecialchars($page_title); ?> | NSBM SportsHub</title>

    <!-- Custom CSS (No frameworks) -->
    <link rel="stylesheet" href="<?php echo $website_base_url; ?>assets/css/style.css">

    <!-- If JavaScript is turned off the loader would never hide, so hide it here -->
    <noscript><style>#page-loader { display: none; }</style></noscript>
</head>
<body>
<div id="page-loader"><video src="<?php echo $website_base_url; ?>assets/videos/loader.mp4" autoplay loop muted playsinline class="loader-video"></video></div>

    <header class="site-header">
        
        <a class="site-logo" href="<?php echo $website_base_url; ?>index.php">
            <img src="<?php echo $website_base_url; ?>assets/images/logo.jpg" alt="NSBM SportsHub Logo" class="logo-img">
        </a>

        <nav class="main-navigation">
            <ul>
                <li><a href="<?php echo $website_base_url; ?>index.php">Home</a></li>
                <li><a href="<?php echo $website_base_url . $clubs_link; ?>">Clubs</a></li>
                <li><a href="<?php echo $website_base_url . $memberships_link; ?>">Memberships</a></li>
                <li><a href="<?php echo $website_base_url . $schedules_link; ?>">Schedules</a></li>
                <li><a href="<?php echo $website_base_url . $tournaments_link; ?>">Tournaments</a></li>
                <li><a href="<?php echo $website_base_url . $results_link; ?>">Results</a></li>
                <li><a href="<?php echo $website_base_url . $bookings_link; ?>">Bookings</a></li>
                <li><a href="<?php echo $website_base_url . $equipment_link; ?>">Equipment</a></li>

                <?php if ($is_user_logged_in): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/auth_profiles/profile.php">Profile</a></li>
                    <li><a class="nav-logout" href="<?php echo $website_base_url; ?>core/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="nav-login" href="<?php echo $website_base_url; ?>modules/auth_profiles/login_view.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Main content starts here, closed in footer.php -->
    <main class="page-content">
