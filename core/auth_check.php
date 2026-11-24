<?php
// Session guard script. 
// Require this at the top of pages that should only be accessible to logged-in users.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detect if we are on local XAMPP or a live server so redirects work in both places
$current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
if (
    $current_host == 'localhost' ||
    $current_host == '127.0.0.1' ||
    strpos($current_host, 'localhost:') === 0 ||
    strpos($current_host, '127.0.0.1:') === 0
) {
    $website_base_url = '/NSBM_SportsHub/';
} else {
    $website_base_url = '/';
}

// If no user_id is found in the session, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $website_base_url . "modules/auth_profiles/login_view.php");
    exit();
}