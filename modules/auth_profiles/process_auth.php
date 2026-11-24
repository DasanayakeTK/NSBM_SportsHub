<?php
// Backend logic for login and registration forms
// No HTML output here, just redirects back to the views or index

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../core/db_connect.php';

// Kick user back if they try to access this file directly via URL
if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: login_view.php");
    exit();
}

$submitted_form_type = isset($_POST['form_action']) ? $_POST['form_action'] : "";

// ---------------------------------------------------------
// REGISTRATION LOGIC
// ---------------------------------------------------------
if ($submitted_form_type == "register") {
    
    // Capture form data
    $student_full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : "";
    $student_email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $student_plain_password = isset($_POST['password']) ? $_POST['password'] : "";
    $selected_role = "student";

    // Basic validation checks
    $error_message = "";
    if ($student_full_name == "" || $student_email == "" || $student_plain_password == "") {
        $error_message = "Please fill in all the fields.";
    } else if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else if (strlen($student_plain_password) < 6) {
        $error_message = "The password must be at least 6 characters long.";
    }

    // Redirect back to form on validation failure
    if ($error_message != "") {
        $_SESSION['auth_message'] = $error_message;
        $_SESSION['auth_message_type'] = "error";
        header("Location: register_view.php");
        exit();
    }

    // Check if email already exists in DB to prevent duplicates
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $check_email_statement = mysqli_prepare($database_connection, $check_email_query);
    mysqli_stmt_bind_param($check_email_statement, "s", $student_email);
    mysqli_stmt_execute($check_email_statement);
    $check_email_result = mysqli_stmt_get_result($check_email_statement);

    if (mysqli_num_rows($check_email_result) > 0) {
        mysqli_stmt_close($check_email_statement);
        $_SESSION['auth_message'] = "That email address is already registered.";
        $_SESSION['auth_message_type'] = "error";
        header("Location: register_view.php");
        exit();
    }
    mysqli_stmt_close($check_email_statement);

    // Hash password before storing
    $hashed_password = password_hash($student_plain_password, PASSWORD_DEFAULT);

    // Insert new user
    $insert_user_query = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_user_statement = mysqli_prepare($database_connection, $insert_user_query);
    mysqli_stmt_bind_param($insert_user_statement, "ssss", $student_full_name, $student_email, $hashed_password, $selected_role);
    $insert_was_successful = mysqli_stmt_execute($insert_user_statement);
    mysqli_stmt_close($insert_user_statement);

    if ($insert_was_successful) {
        $_SESSION['auth_message'] = "Registration successful! You can now log in.";
        $_SESSION['auth_message_type'] = "success";
        header("Location: login_view.php");
    } else {
        $_SESSION['auth_message'] = "Database error. Could not create account.";
        $_SESSION['auth_message_type'] = "error";
        header("Location: register_view.php");
    }
    exit();
}

// ---------------------------------------------------------
// LOGIN LOGIC
// ---------------------------------------------------------
if ($submitted_form_type == "login") {
    
    // Capture form data
    $login_email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $login_plain_password = isset($_POST['password']) ? $_POST['password'] : "";

    if ($login_email == "" || $login_plain_password == "") {
        $_SESSION['auth_message'] = "Please enter your email and password.";
        $_SESSION['auth_message_type'] = "error";
        header("Location: login_view.php");
        exit();
    }

    // Fetch user record by email
    $find_user_query = "SELECT id, full_name, password_hash, role FROM users WHERE email = ?";
    $find_user_statement = mysqli_prepare($database_connection, $find_user_query);
    mysqli_stmt_bind_param($find_user_statement, "s", $login_email);
    mysqli_stmt_execute($find_user_statement);
    $find_user_result = mysqli_stmt_get_result($find_user_statement);
    $user_row = mysqli_fetch_assoc($find_user_result);
    mysqli_stmt_close($find_user_statement);

    // Verify password hash against input
    if ($user_row != null && password_verify($login_plain_password, $user_row['password_hash'])) {
        // Prevent session fixation
        session_regenerate_id(true);

        // Store user details in session vars for global access
        $_SESSION['user_id']   = $user_row['id'];
        $_SESSION['role']      = $user_row['role'];
        $_SESSION['full_name'] = $user_row['full_name'];

        header("Location: ../../index.php");
        exit();
    } else {
        // Generic error message for both wrong email and wrong password
        $_SESSION['auth_message'] = "Invalid email or password.";
        $_SESSION['auth_message_type'] = "error";
        header("Location: login_view.php");
        exit();
    }
}

// Fallback redirect if something weird happens
header("Location: login_view.php");
exit();