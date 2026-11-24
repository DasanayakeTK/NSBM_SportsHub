<?php
// Backend CRUD logic for clubs. Redirects back to manage_clubs.php.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// Enforce admin permissions on the backend script
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// ADD A CLUB
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "add") {

    $new_club_name = isset($_POST['club_name']) ? trim($_POST['club_name']) : "";
    $new_club_description = isset($_POST['club_description']) ? trim($_POST['club_description']) : "";

    if ($new_club_name == "") {
        $_SESSION['club_message'] = "Please enter a club name.";
        $_SESSION['club_message_type'] = "error";
        header("Location: manage_clubs.php");
        exit();
    }

    $insert_club_query = "INSERT INTO sports_clubs (name, description) VALUES (?, ?)";
    $insert_club_statement = mysqli_prepare($database_connection, $insert_club_query);
    mysqli_stmt_bind_param($insert_club_statement, "ss", $new_club_name, $new_club_description);
    $insert_was_successful = mysqli_stmt_execute($insert_club_statement);

    if ($insert_was_successful) {
        $_SESSION['club_message'] = "Club added successfully.";
        $_SESSION['club_message_type'] = "success";
    } else if (mysqli_stmt_errno($insert_club_statement) == 1062) {
        // Error 1062: Duplicate entry
        $_SESSION['club_message'] = "A club with that name already exists.";
        $_SESSION['club_message_type'] = "error";
    } else {
        $_SESSION['club_message'] = "Database error. Could not add the club.";
        $_SESSION['club_message_type'] = "error";
    }
    mysqli_stmt_close($insert_club_statement);
    header("Location: manage_clubs.php");
    exit();
}

// ---------------------------------------------------------
// UPDATE A CLUB
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "update") {

    $club_id_to_update = isset($_POST['club_id']) ? (int) $_POST['club_id'] : 0;
    $updated_club_name = isset($_POST['club_name']) ? trim($_POST['club_name']) : "";
    $updated_club_description = isset($_POST['club_description']) ? trim($_POST['club_description']) : "";

    if ($updated_club_name == "") {
        $_SESSION['club_message'] = "Please enter a club name.";
        $_SESSION['club_message_type'] = "error";
        header("Location: edit_club.php?id=" . $club_id_to_update);
        exit();
    }

    $update_club_query = "UPDATE sports_clubs SET name = ?, description = ? WHERE id = ?";
    $update_club_statement = mysqli_prepare($database_connection, $update_club_query);
    mysqli_stmt_bind_param($update_club_statement, "ssi", $updated_club_name, $updated_club_description, $club_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_club_statement);

    if ($update_was_successful) {
        mysqli_stmt_close($update_club_statement);
        $_SESSION['club_message'] = "Club updated successfully.";
        $_SESSION['club_message_type'] = "success";
        header("Location: manage_clubs.php");
        exit();
    } else if (mysqli_stmt_errno($update_club_statement) == 1062) {
        mysqli_stmt_close($update_club_statement);
        $_SESSION['club_message'] = "A club with that name already exists.";
        $_SESSION['club_message_type'] = "error";
        header("Location: edit_club.php?id=" . $club_id_to_update);
        exit();
    } else {
        mysqli_stmt_close($update_club_statement);
        $_SESSION['club_message'] = "Database error. Could not update the club.";
        $_SESSION['club_message_type'] = "error";
        header("Location: manage_clubs.php");
        exit();
    }
}

// ---------------------------------------------------------
// DELETE A CLUB
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {

    $club_id_to_delete = (int) $_GET['id'];

    $delete_club_query = "DELETE FROM sports_clubs WHERE id = ?";
    $delete_club_statement = mysqli_prepare($database_connection, $delete_club_query);
    mysqli_stmt_bind_param($delete_club_statement, "i", $club_id_to_delete);
    $delete_was_successful = mysqli_stmt_execute($delete_club_statement);

    if ($delete_was_successful && mysqli_stmt_affected_rows($delete_club_statement) > 0) {
        $_SESSION['club_message'] = "Club deleted successfully.";
        $_SESSION['club_message_type'] = "success";
    } else {
        $_SESSION['club_message'] = "Could not delete the club. It may not exist.";
        $_SESSION['club_message_type'] = "error";
    }
    mysqli_stmt_close($delete_club_statement);
    header("Location: manage_clubs.php");
    exit();
}

header("Location: manage_clubs.php");
exit();
