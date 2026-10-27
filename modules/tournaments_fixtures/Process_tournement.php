<?p<?php
// Backend for tournaments and fixtures. Admin only.
// Tournament actions go back to manage_tournaments.php, fixture actions go back to manage_fixtures.php.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// block students, otherwise they could add or delete things from the URL
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// ADD A TOURNAMENT
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "add_tournament") {

    $new_tournament_name = isset($_POST['tournament_name']) ? trim($_POST['tournament_name']) : "";
    $new_sport_type = isset($_POST['sport_type']) ? trim($_POST['sport_type']) : "";
    $new_start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : "";

    // strtotime gives false if the date is not valid
    if ($new_tournament_name == "" || $new_sport_type == "" || strtotime($new_start_date) == false) {
        $_SESSION['tournament_message'] = "Please fill in all the fields with valid values.";
        $_SESSION['tournament_message_type'] = "error";
        header("Location: manage_tournaments.php");
        exit();
    }

    $insert_tournament_query = "INSERT INTO tournaments (name, sport_type, start_date) VALUES (?, ?, ?)";
    $insert_tournament_statement = mysqli_prepare($database_connection, $insert_tournament_query);
    mysqli_stmt_bind_param($insert_tournament_statement, "sss", $new_tournament_name, $new_sport_type, $new_start_date);
    $insert_was_successful = mysqli_stmt_execute($insert_tournament_statement);
    mysqli_stmt_close($insert_tournament_statement);

    if ($insert_was_successful) {
        $_SESSION['tournament_message'] = "Tournament added.";
        $_SESSION['tournament_message_type'] = "success";
    } else {
        $_SESSION['tournament_message'] = "Database error. Could not add the tournament.";
        $_SESSION['tournament_message_type'] = "error";
    }

    header("Location: manage_tournaments.php");
    exit();
}

// ---------------------------------------------------------
// DELETE A TOURNAMENT
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "delete_tournament") {

    $tournament_id_to_delete = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    // ON DELETE CASCADE also removes the fixtures of this tournament
    $delete_tournament_query = "DELETE FROM tournaments WHERE id = ?";
    $delete_tournament_statement = mysqli_prepare($database_connection, $delete_tournament_query);
    mysqli_stmt_bind_param($delete_tournament_statement, "i", $tournament_id_to_delete);
    $delete_was_successful = mysqli_stmt_execute($delete_tournament_statement);

    // affected_rows is 0 if no tournament has this id
    if ($delete_was_successful && mysqli_stmt_affected_rows($delete_tournament_statement) > 0) {
        $_SESSION['tournament_message'] = "Tournament deleted.";
        $_SESSION['tournament_message_type'] = "success";
    } else {
        $_SESSION['tournament_message'] = "Could not delete the tournament. It may not exist.";
        $_SESSION['tournament_message_type'] = "error";
    }
    mysqli_stmt_close($delete_tournament_statement);

    header("Location: manage_tournaments.php");
    exit();
}

// ---------------------------------------------------------
// ADD A FIXTURE
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "add_fixture") {

    $new_tournament_id = isset($_POST['tournament_id']) ? (int) $_POST['tournament_id'] : 0;
    $new_club_a_id = isset($_POST['club_a_id']) ? (int) $_POST['club_a_id'] : 0;
    $new_club_b_id = isset($_POST['club_b_id']) ? (int) $_POST['club_b_id'] : 0;
    $new_match_date = isset($_POST['match_date']) ? trim($_POST['match_date']) : "";

    // all fields are needed
    if ($new_tournament_id <= 0 || $new_club_a_id <= 0 || $new_club_b_id <= 0 || strtotime($new_match_date) == false) {
        $_SESSION['tournament_message'] = "Please fill in all the fields with valid values.";
        $_SESSION['tournament_message_type'] = "error";
        header("Location: manage_fixtures.php");
        exit();
    }

    // a club cannot play against itself
    if ($new_club_a_id == $new_club_b_id) {
        $_SESSION['tournament_message'] = "Club A and Club B must be different clubs.";
        $_SESSION['tournament_message_type'] = "error";
        header("Location: manage_fixtures.php");
        exit();
    }

    // status is not in the query, the database sets it to 'scheduled' by default
    $insert_fixture_query = "INSERT INTO match_fixtures (tournament_id, club_a_id, club_b_id, match_date) VALUES (?, ?, ?, ?)";
    $insert_fixture_statement = mysqli_prepare($database_connection, $insert_fixture_query);
    mysqli_stmt_bind_param($insert_fixture_statement, "iiis", $new_tournament_id, $new_club_a_id, $new_club_b_id, $new_match_date);
    $insert_was_successful = mysqli_stmt_execute($insert_fixture_statement);
    mysqli_stmt_close($insert_fixture_statement);

    if ($insert_was_successful) {
        $_SESSION['tournament_message'] = "Match scheduled.";
        $_SESSION['tournament_message_type'] = "success";
    } else {
        // probably a tournament or club id that does not exist
        $_SESSION['tournament_message'] = "Database error. Could not schedule the match.";
        $_SESSION['tournament_message_type'] = "error";
    }

    header("Location: manage_fixtures.php");
    exit();
}

// ---------------------------------------------------------
// DELETE A FIXTURE
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "delete_fixture") {

    $fixture_id_to_delete = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $delete_fixture_query = "DELETE FROM match_fixtures WHERE id = ?";
    $delete_fixture_statement = mysqli_prepare($database_connection, $delete_fixture_query);
    mysqli_stmt_bind_param($delete_fixture_statement, "i", $fixture_id_to_delete);
    $delete_was_successful = mysqli_stmt_execute($delete_fixture_statement);

    if ($delete_was_successful && mysqli_stmt_affected_rows($delete_fixture_statement) > 0) {
        $_SESSION['tournament_message'] = "Fixture deleted.";
        $_SESSION['tournament_message_type'] = "success";
    } else {
        $_SESSION['tournament_message'] = "Could not delete the fixture. It may not exist.";
        $_SESSION['tournament_message_type'] = "error";
    }
    mysqli_stmt_close($delete_fixture_statement);

    header("Location: manage_fixtures.php");
    exit();
}

// anything else goes back to the tournaments page
header("Location: manage_tournaments.php");
exit();hp
// Tournament match CRUD processing.
