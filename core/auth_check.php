<?php
// Session guard script. 
// Require this at the top of pages that should only be accessible to logged-in users.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If no user_id is found in the session, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: /NSBM_SportsHub/modules/auth_profiles/login_view.php");
    exit();
}