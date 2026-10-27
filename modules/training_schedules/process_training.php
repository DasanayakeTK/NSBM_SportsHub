<?php
// Backend for training sessions (add, update, delete). Admin only.
// No page is shown, it always redirects back with a message.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// block students, otherwise they could add or delete sessions from the URL
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// ADD A SESSION
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "add") {

    $new_club_id = isset($_POST['club_id']) ? (int) $_POST['club_id'] : 0;
    $new_session_date = isset($_POST['session_date']) ? trim($_POST['session_date']) : "";
    $new_start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : "";
    $new_venue = isset($_POST['venue']) ? trim($_POST['venue']) : "";

    // check the inputs (strtotime gives false if the date or time is not valid)
    if ($new_club_id <= 0 || $new_venue == "" || strtotime($new_session_date) == false || strtotime($new_start_time) == false) {
        $_SESSION['schedule_message'] = "Please fill in all the fields with valid values.";
        $_SESSION['schedule_message_type'] = "error";
        header("Location: manage_schedules.php");
        exit();
    }

    $insert_session_query = "INSERT INTO training_sessions (club_id, session_date, start_time, venue) VALUES (?, ?, ?, ?)";
    $insert_session_statement = mysqli_prepare($database_connection, $insert_session_query);
    mysqli_stmt_bind_param($insert_session_statement, "isss", $new_club_id, $new_session_date, $new_start_time, $new_venue);
    $insert_was_successful = mysqli_stmt_execute($insert_session_statement);
    mysqli_stmt_close($insert_session_statement);

    if ($insert_was_successful) {
        $_SESSION['schedule_message'] = "Training session added.";
        $_SESSION['schedule_message_type'] = "success";
    } else {
        // most likely the club id does not exist
        $_SESSION['schedule_message'] = "Database error. Could not add the session.";
        $_SESSION['schedule_message_type'] = "error";
    }

    header("Location: manage_schedules.php");
    exit();
}

// ---------------------------------------------------------
// UPDATE A SESSION
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "update") {

    $session_id_to_update = isset($_POST['session_id']) ? (int) $_POST['session_id'] : 0;
    $updated_club_id = isset($_POST['club_id']) ? (int) $_POST['club_id'] : 0;
    $updated_session_date = isset($_POST['session_date']) ? trim($_POST['session_date']) : "";
    $updated_start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : "";
    $updated_venue = isset($_POST['venue']) ? trim($_POST['venue']) : "";

    // on a mistake, go back to the edit form so the admin can fix it
    if ($updated_club_id <= 0 || $updated_venue == "" || strtotime($updated_session_date) == false || strtotime($updated_start_time) == false) {
        $_SESSION['schedule_message'] = "Please fill in all the fields with valid values.";
        $_SESSION['schedule_message_type'] = "error";
        header("Location: edit_schedule.php?id=" . $session_id_to_update);
        exit();
    }

    $update_session_query = "UPDATE training_sessions SET club_id = ?, session_date = ?, start_time = ?, venue = ? WHERE id = ?";
    $update_session_statement = mysqli_prepare($database_connection, $update_session_query);
    mysqli_stmt_bind_param($update_session_statement, "isssi", $updated_club_id, $updated_session_date, $updated_start_time, $updated_venue, $session_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_session_statement);
    mysqli_stmt_close($update_session_statement);

    // affected_rows is not checked here, it is 0 when nothing was changed and that is fine
    if ($update_was_successful) {
        $_SESSION['schedule_message'] = "Training session updated.";
        $_SESSION['schedule_message_type'] = "success";
    } else {
        $_SESSION['schedule_message'] = "Database error. Could not update the session.";
        $_SESSION['schedule_message_type'] = "error";
    }

    header("Location: manage_schedules.php");
    exit();
}

// ---------------------------------------------------------
// DELETE A SESSION
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "delete") {

    $session_id_to_delete = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $delete_session_query = "DELETE FROM training_sessions WHERE id = ?";
    $delete_session_statement = mysqli_prepare($database_connection, $delete_session_query);
    mysqli_stmt_bind_param($delete_session_statement, "i", $session_id_to_delete);
    $delete_was_successful = mysqli_stmt_execute($delete_session_statement);

    // affected_rows is 0 if no session has this id
    if ($delete_was_successful && mysqli_stmt_affected_rows($delete_session_statement) > 0) {
        $_SESSION['schedule_message'] = "Training session deleted.";
        $_SESSION['schedule_message_type'] = "success";
    } else {
        $_SESSION['schedule_message'] = "Could not delete the session. It may not exist.";
        $_SESSION['schedule_message_type'] = "error";
    }
    mysqli_stmt_close($delete_session_statement);

    header("Location: manage_schedules.php");
    exit();
}

// anything else goes back to the dashboard
header("Location: manage_schedules.php");
exit();
