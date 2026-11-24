<?php
// Backend for equipment (add, update, delete). Admin only.
// No page is shown, it always redirects with a message.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// block students, otherwise they could change the inventory from the URL
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$request_method = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------
// ADD EQUIPMENT
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "add") {

    $new_item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : "";
    $new_total_text = isset($_POST['total_quantity']) ? trim($_POST['total_quantity']) : "";
    $new_available_text = isset($_POST['available_quantity']) ? trim($_POST['available_quantity']) : "";

    // ctype_digit is true only for whole numbers like 0, 5, 12 (no minus sign, no decimals)
    if ($new_item_name == "" || !ctype_digit($new_total_text) || !ctype_digit($new_available_text)) {
        $_SESSION['inventory_message'] = "Please enter an item name and whole numbers for the quantities.";
        $_SESSION['inventory_message_type'] = "error";
        header("Location: manage_inventory.php");
        exit();
    }

    $new_total_quantity = (int) $new_total_text;
    $new_available_quantity = (int) $new_available_text;

    // we cannot have more available than we own
    if ($new_available_quantity > $new_total_quantity) {
        $_SESSION['inventory_message'] = "Available quantity cannot be more than the total quantity.";
        $_SESSION['inventory_message_type'] = "error";
        header("Location: manage_inventory.php");
        exit();
    }

    $insert_item_query = "INSERT INTO equipment_inventory (item_name, total_quantity, available_quantity) VALUES (?, ?, ?)";
    $insert_item_statement = mysqli_prepare($database_connection, $insert_item_query);
    mysqli_stmt_bind_param($insert_item_statement, "sii", $new_item_name, $new_total_quantity, $new_available_quantity);
    $insert_was_successful = mysqli_stmt_execute($insert_item_statement);
    mysqli_stmt_close($insert_item_statement);

    if ($insert_was_successful) {
        $_SESSION['inventory_message'] = "Equipment added.";
        $_SESSION['inventory_message_type'] = "success";
    } else {
        $_SESSION['inventory_message'] = "Database error. Could not add the equipment.";
        $_SESSION['inventory_message_type'] = "error";
    }

    header("Location: manage_inventory.php");
    exit();
}

// ---------------------------------------------------------
// UPDATE EQUIPMENT
// ---------------------------------------------------------
if ($request_method == "POST" && isset($_POST['action']) && $_POST['action'] == "update") {

    $item_id_to_update = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
    $updated_item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : "";
    $updated_total_text = isset($_POST['total_quantity']) ? trim($_POST['total_quantity']) : "";
    $updated_available_text = isset($_POST['available_quantity']) ? trim($_POST['available_quantity']) : "";

    // on a mistake, go back to the edit form so the admin can fix it
    if ($updated_item_name == "" || !ctype_digit($updated_total_text) || !ctype_digit($updated_available_text)) {
        $_SESSION['inventory_message'] = "Please enter an item name and whole numbers for the quantities.";
        $_SESSION['inventory_message_type'] = "error";
        header("Location: edit_inventory.php?id=" . $item_id_to_update);
        exit();
    }

    $updated_total_quantity = (int) $updated_total_text;
    $updated_available_quantity = (int) $updated_available_text;

    if ($updated_available_quantity > $updated_total_quantity) {
        $_SESSION['inventory_message'] = "Available quantity cannot be more than the total quantity.";
        $_SESSION['inventory_message_type'] = "error";
        header("Location: edit_inventory.php?id=" . $item_id_to_update);
        exit();
    }

    $update_item_query = "UPDATE equipment_inventory SET item_name = ?, total_quantity = ?, available_quantity = ? WHERE id = ?";
    $update_item_statement = mysqli_prepare($database_connection, $update_item_query);
    mysqli_stmt_bind_param($update_item_statement, "siii", $updated_item_name, $updated_total_quantity, $updated_available_quantity, $item_id_to_update);
    $update_was_successful = mysqli_stmt_execute($update_item_statement);
    mysqli_stmt_close($update_item_statement);

    // affected_rows is not checked, it is 0 when nothing was changed and that is fine
    if ($update_was_successful) {
        $_SESSION['inventory_message'] = "Equipment updated.";
        $_SESSION['inventory_message_type'] = "success";
    } else {
        $_SESSION['inventory_message'] = "Database error. Could not update the equipment.";
        $_SESSION['inventory_message_type'] = "error";
    }

    header("Location: manage_inventory.php");
    exit();
}

// ---------------------------------------------------------
// DELETE EQUIPMENT
// ---------------------------------------------------------
if ($request_method == "GET" && isset($_GET['action']) && $_GET['action'] == "delete") {

    $item_id_to_delete = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $delete_item_query = "DELETE FROM equipment_inventory WHERE id = ?";
    $delete_item_statement = mysqli_prepare($database_connection, $delete_item_query);
    mysqli_stmt_bind_param($delete_item_statement, "i", $item_id_to_delete);
    $delete_was_successful = mysqli_stmt_execute($delete_item_statement);

    // affected_rows is 0 if no item has this id
    if ($delete_was_successful && mysqli_stmt_affected_rows($delete_item_statement) > 0) {
        $_SESSION['inventory_message'] = "Equipment deleted.";
        $_SESSION['inventory_message_type'] = "success";
    } else {
        $_SESSION['inventory_message'] = "Could not delete the item. It may not exist.";
        $_SESSION['inventory_message_type'] = "error";
    }
    mysqli_stmt_close($delete_item_statement);

    header("Location: manage_inventory.php");
    exit();
}

// anything else goes back to the dashboard
header("Location: manage_inventory.php");
exit();