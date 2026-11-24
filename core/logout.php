<?php
// Logout: clear the session and send the user back to the home page.
// Nothing is printed here, so the redirect at the bottom works.

// we need the session running before we can destroy it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// remove all the session variables (user_id, role, full_name ...)
$_SESSION = array();

// also delete the session cookie in the browser
if (ini_get("session.use_cookies")) {
    $cookie_settings = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $cookie_settings["path"], $cookie_settings["domain"],
        $cookie_settings["secure"], $cookie_settings["httponly"]);
}

// destroy the session on the server
session_destroy();

// go to the home page (this file is in core/, so one folder up)
header("Location: ../index.php");
exit();

