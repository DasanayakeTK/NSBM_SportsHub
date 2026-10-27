<?php
// Backend for match results. Admin only.
// It changes the status of a fixture and then goes back to manage_results.php.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// block students, otherwise they could send the form themselves
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

// ---------------------------------------------------------
// UPDATE THE STATUS OF A FIXTURE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == "update_status") {

    $fixture_id_to_update = isset($_POST['fixture_id']) ? (int) $_POST['fixture_id'] : 0;
    $new_fixture_status = isset($_POST['new_status']) ? $_POST['new_status'] : "";

    // only these two values are allowed from this page
    if ($fixture_id_to_update <= 0 || ($new_fixture_status != "completed" && $new_fixture_status != "cancelled")) {
        $_SESSION['result_message'] = "Invalid request.";
        $_SESSION['result_message_type'] = "error";
        header("Location: manage_results.php");
        exit();
    }

    // status = 'scheduled' in the WHERE part so a finished match cannot be changed by mistake
    $update_status_query = "UPDATE match_fixtures SET status = ? WHERE id = ? AND status = 'scheduled'";
    $update_status_statement = mysqli_prepare($database_connection, $update_status_query);
    mysqli_stmt_bind_param($update_status_statement, "si", $new_fixture_status, $fixture_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_status_statement);

    // affected_rows is 0 if the match does not exist or is not scheduled anymore
    if ($update_was_successful && mysqli_stmt_affected_rows($update_status_statement) > 0) {
        $_SESSION['result_message'] = "Match marked as " . $new_fixture_status . ".";
        $_SESSION['result_message_type'] = "success";
    } else {
        $_SESSION['result_message'] = "Nothing was changed. The match may not exist or is already updated.";
        $_SESSION['result_message_type'] = "error";
    }
    mysqli_stmt_close($update_status_statement);

    header("Location: manage_results.php");
    exit();
}

// anything else goes back to the dashboard
header("Location: manage_results.php");
exit();