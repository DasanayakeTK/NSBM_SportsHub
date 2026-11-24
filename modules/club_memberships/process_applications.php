<?php
// Backend for club memberships: students apply, admins approve or reject.
// No page is shown here, it always redirects back.

// must be logged in (this also starts the session)
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// APPLY TO A CLUB (student form, POST action=apply)
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "apply") {

    // only students can apply
    if ($_SESSION['role'] != "student") {
        header("Location: ../../index.php");
        exit();
    }

    // student id comes from the session (not the form) so nobody can apply for someone else
    $applying_student_id = $_SESSION['user_id'];
    $selected_club_id = isset($_POST['club_id']) ? (int) $_POST['club_id'] : 0;

    if ($selected_club_id <= 0) {
        $_SESSION['membership_message'] = "Please choose a club.";
        $_SESSION['membership_message_type'] = "error";
        header("Location: apply_club.php");
        exit();
    }

    // check if this student already applied to this club
    $check_application_query = "SELECT id FROM club_memberships WHERE student_id = ? AND club_id = ?";
    $check_application_statement = mysqli_prepare($database_connection, $check_application_query);
    mysqli_stmt_bind_param($check_application_statement, "ii", $applying_student_id, $selected_club_id);
    mysqli_stmt_execute($check_application_statement);
    $check_application_result = mysqli_stmt_get_result($check_application_statement);
    $already_applied = mysqli_num_rows($check_application_result) > 0;
    mysqli_stmt_close($check_application_statement);

    if ($already_applied) {
        $_SESSION['membership_message'] = "You have already applied to this club.";
        $_SESSION['membership_message_type'] = "error";
        header("Location: apply_club.php");
        exit();
    }

    // save the new application, status starts as pending
    $insert_application_query = "INSERT INTO club_memberships (student_id, club_id, status) VALUES (?, ?, 'pending')";
    $insert_application_statement = mysqli_prepare($database_connection, $insert_application_query);
    mysqli_stmt_bind_param($insert_application_statement, "ii", $applying_student_id, $selected_club_id);
    $insert_was_successful = mysqli_stmt_execute($insert_application_statement);
    mysqli_stmt_close($insert_application_statement);

    if ($insert_was_successful) {
        $_SESSION['membership_message'] = "Application sent! Please wait for an admin to review it.";
        $_SESSION['membership_message_type'] = "success";
    } else {
        // usually means the club id does not exist
        $_SESSION['membership_message'] = "Could not send the application. Please try again.";
        $_SESSION['membership_message_type'] = "error";
    }

    header("Location: apply_club.php");
    exit();
}


// ---------------------------------------------------------
// APPROVE OR REJECT (admin link, GET action=update_status)
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "update_status") {

    // admins only, otherwise a student could approve their own request from the URL
    if ($_SESSION['role'] != "admin") {
        header("Location: ../../index.php");
        exit();
    }

    $membership_id_to_update = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $new_membership_status = isset($_GET['status']) ? $_GET['status'] : "";

    // only these two values are allowed
    if ($membership_id_to_update <= 0 || ($new_membership_status != "approved" && $new_membership_status != "rejected")) {
        $_SESSION['membership_message'] = "Invalid request.";
        $_SESSION['membership_message_type'] = "error";
        header("Location: approve_members.php");
        exit();
    }

    // change the status of this one membership row
    $update_status_query = "UPDATE club_memberships SET status = ? WHERE id = ?";
    $update_status_statement = mysqli_prepare($database_connection, $update_status_query);
    mysqli_stmt_bind_param($update_status_statement, "si", $new_membership_status, $membership_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_status_statement);

    // affected_rows is 0 if the id does not exist
    if ($update_was_successful && mysqli_stmt_affected_rows($update_status_statement) > 0) {
        $_SESSION['membership_message'] = "Request marked as " . $new_membership_status . ".";
        $_SESSION['membership_message_type'] = "success";
    } else {
        $_SESSION['membership_message'] = "Nothing was changed. The request may not exist.";
        $_SESSION['membership_message_type'] = "error";
    }
    mysqli_stmt_close($update_status_statement);

    header("Location: approve_members.php");
    exit();
}

// ---------------------------------------------------------
// WITHDRAW AN APPLICATION (student link, GET action=withdraw)
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "withdraw") {

    // students only
    if ($_SESSION['role'] != "student") {
        header("Location: ../../index.php");
        exit();
    }

    $membership_id_to_withdraw = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $withdrawing_student_id = $_SESSION['user_id'];

    // student_id is in the WHERE part too, so a student can only delete their own row.
    // status = 'pending' stops people removing approved/rejected records.
    $withdraw_query = "DELETE FROM club_memberships WHERE id = ? AND student_id = ? AND status = 'pending'";
    $withdraw_statement = mysqli_prepare($database_connection, $withdraw_query);
    mysqli_stmt_bind_param($withdraw_statement, "ii", $membership_id_to_withdraw, $withdrawing_student_id);
    $withdraw_was_successful = mysqli_stmt_execute($withdraw_statement);

    // affected_rows is 0 if the id is not theirs, not pending, or does not exist
    if ($withdraw_was_successful && mysqli_stmt_affected_rows($withdraw_statement) > 0) {
        $_SESSION['membership_message'] = "Your application was withdrawn.";
        $_SESSION['membership_message_type'] = "success";
    } else {
        $_SESSION['membership_message'] = "Could not withdraw that application.";
        $_SESSION['membership_message_type'] = "error";
    }
    mysqli_stmt_close($withdraw_statement);

    header("Location: apply_club.php");
    exit();
}

// anything else: send the user home
header("Location: ../../index.php");
exit();