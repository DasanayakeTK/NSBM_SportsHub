<?php
// Backend for facility bookings: students request, admins approve or reject.
// No page is shown here, it always redirects back with a message.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// BOOK A FACILITY (student form, POST action=book)
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "book") {

    // students only
    if ($_SESSION['role'] != "student") {
        header("Location: ../../index.php");
        exit();
    }

    // student id comes from the session, not the form
    $booking_student_id = $_SESSION['user_id'];
    $requested_facility_name = isset($_POST['facility_name']) ? trim($_POST['facility_name']) : "";
    $requested_booking_date = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : "";
    $requested_time_slot = isset($_POST['time_slot']) ? trim($_POST['time_slot']) : "";

    // the facility must be one from our list (the dropdown can be faked)
    $allowed_facilities = array("Gymnasium", "Basketball Court", "Swimming Pool", "Tennis Court");

    if (!in_array($requested_facility_name, $allowed_facilities)) {
        $_SESSION['booking_message'] = "Please choose a facility from the list.";
        $_SESSION['booking_message_type'] = "error";
        header("Location: book_facility.php");
        exit();
    }

    // the time slot must be one of the fixed slots from the dropdown (it can be faked too)
    $allowed_time_slots = array("08:00 AM - 10:00 AM", "10:00 AM - 12:00 PM", "12:00 PM - 02:00 PM", "02:00 PM - 04:00 PM", "04:00 PM - 06:00 PM");

    if (!in_array($requested_time_slot, $allowed_time_slots) || strtotime($requested_booking_date) == false) {
        $_SESSION['booking_message'] = "Please choose a valid date and time slot.";
        $_SESSION['booking_message_type'] = "error";
        header("Location: book_facility.php");
        exit();
    }

    // no bookings in the past (yyyy-mm-dd text can be compared like this)
    if ($requested_booking_date < date("Y-m-d")) {
        $_SESSION['booking_message'] = "You cannot book a date in the past.";
        $_SESSION['booking_message_type'] = "error";
        header("Location: book_facility.php");
        exit();
    }

    // check if someone already has an approved booking for the same facility, date and time slot
    $check_slot_query = "SELECT id FROM facility_bookings
                         WHERE facility_name = ? AND booking_date = ? AND time_slot = ? AND status = 'approved'";
    $check_slot_statement = mysqli_prepare($database_connection, $check_slot_query);
    mysqli_stmt_bind_param($check_slot_statement, "sss", $requested_facility_name, $requested_booking_date, $requested_time_slot);
    mysqli_stmt_execute($check_slot_statement);
    $check_slot_result = mysqli_stmt_get_result($check_slot_statement);
    $slot_is_taken = mysqli_num_rows($check_slot_result) > 0;
    mysqli_stmt_close($check_slot_statement);

    if ($slot_is_taken) {
        $_SESSION['booking_message'] = "That facility is already booked for this date and time slot.";
        $_SESSION['booking_message_type'] = "error";
        header("Location: book_facility.php");
        exit();
    }

    // save the request, it starts as pending
    $insert_booking_query = "INSERT INTO facility_bookings (student_id, facility_name, booking_date, time_slot, status) VALUES (?, ?, ?, ?, 'pending')";
    $insert_booking_statement = mysqli_prepare($database_connection, $insert_booking_query);
    mysqli_stmt_bind_param($insert_booking_statement, "isss", $booking_student_id, $requested_facility_name, $requested_booking_date, $requested_time_slot);
    $insert_was_successful = mysqli_stmt_execute($insert_booking_statement);
    mysqli_stmt_close($insert_booking_statement);

    if ($insert_was_successful) {
        $_SESSION['booking_message'] = "Booking request sent! Please wait for an admin to approve it.";
        $_SESSION['booking_message_type'] = "success";
    } else {
        $_SESSION['booking_message'] = "Database error. Could not send the request.";
        $_SESSION['booking_message_type'] = "error";
    }

    header("Location: book_facility.php");
    exit();
}

// ---------------------------------------------------------
// APPROVE OR REJECT (admin link, GET action=update_status)
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "update_status") {

    // admins only
    if ($_SESSION['role'] != "admin") {
        header("Location: ../../index.php");
        exit();
    }

    $booking_id_to_update = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $new_booking_status = isset($_GET['status']) ? $_GET['status'] : "";

    if ($booking_id_to_update <= 0 || ($new_booking_status != "approved" && $new_booking_status != "rejected")) {
        $_SESSION['booking_message'] = "Invalid request.";
        $_SESSION['booking_message_type'] = "error";
        header("Location: manage_bookings.php");
        exit();
    }

    // before approving, make sure the same slot is not already approved for someone else
    if ($new_booking_status == "approved") {

        // get the facility, date and time slot of the booking we want to approve
        $get_booking_query = "SELECT facility_name, booking_date, time_slot FROM facility_bookings WHERE id = ? AND status = 'pending'";
        $get_booking_statement = mysqli_prepare($database_connection, $get_booking_query);
        mysqli_stmt_bind_param($get_booking_statement, "i", $booking_id_to_update);
        mysqli_stmt_execute($get_booking_statement);
        $get_booking_result = mysqli_stmt_get_result($get_booking_statement);
        $booking_to_approve = mysqli_fetch_assoc($get_booking_result);   // null if not found or not pending
        mysqli_stmt_close($get_booking_statement);

        if ($booking_to_approve != null) {

            // look for ANOTHER approved booking with the same facility, date and time slot
            $find_conflict_query = "SELECT id FROM facility_bookings
                                    WHERE facility_name = ? AND booking_date = ? AND time_slot = ?
                                    AND status = 'approved' AND id != ?";
            $find_conflict_statement = mysqli_prepare($database_connection, $find_conflict_query);
            mysqli_stmt_bind_param($find_conflict_statement, "sssi", $booking_to_approve['facility_name'], $booking_to_approve['booking_date'], $booking_to_approve['time_slot'], $booking_id_to_update);
            mysqli_stmt_execute($find_conflict_statement);
            $find_conflict_result = mysqli_stmt_get_result($find_conflict_statement);
            $slot_is_taken = mysqli_num_rows($find_conflict_result) > 0;
            mysqli_stmt_close($find_conflict_statement);

            // stop here, the admin can reject this request instead
            if ($slot_is_taken) {
                $_SESSION['booking_message'] = "Cannot approve: that facility is already booked for this date and time slot. You can reject this request.";
                $_SESSION['booking_message_type'] = "error";
                header("Location: manage_bookings.php");
                exit();
            }
        }
    }

    if ($new_booking_status == "approved") {
        // only pending bookings can be approved
        $update_booking_query = "UPDATE facility_bookings SET status = 'approved' WHERE id = ? AND status = 'pending'";
    } else {
        // the status column only has 'pending' and 'approved' in our schema,
        // so a rejected booking is deleted instead of updated
        $update_booking_query = "DELETE FROM facility_bookings WHERE id = ? AND status = 'pending'";
    }

    $update_booking_statement = mysqli_prepare($database_connection, $update_booking_query);
    mysqli_stmt_bind_param($update_booking_statement, "i", $booking_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_booking_statement);

    // affected_rows is 0 if the id does not exist or is not pending anymore
    if ($update_was_successful && mysqli_stmt_affected_rows($update_booking_statement) > 0) {
        $_SESSION['booking_message'] = "Booking " . $new_booking_status . ".";
        $_SESSION['booking_message_type'] = "success";
    } else {
        $_SESSION['booking_message'] = "Nothing was changed. The booking may not exist or was already handled.";
        $_SESSION['booking_message_type'] = "error";
    }
    mysqli_stmt_close($update_booking_statement);

    header("Location: manage_bookings.php");
    exit();
}

// anything else sends the user home
header("Location: ../../index.php");
exit();
