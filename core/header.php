<?php
// header.php - Included at the top of every page for global navigation
// Sets up sessions, dynamic page titles, and the navbar.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$website_base_url = "/NSBM_SportsHub/";

if (!isset($page_title)) {
    $page_title = "Home";
}

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

                <?php if ($is_user_logged_in): ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/auth_profiles/profile.php">Profile</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $website_base_url; ?>modules/auth_profiles/login_view.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="page-content">