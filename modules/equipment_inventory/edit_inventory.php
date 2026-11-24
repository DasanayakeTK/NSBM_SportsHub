<?php
// Admin page: edit one equipment item. The form is pre-filled with the current values.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

require_once '../../core/db_connect.php';

// item id from the link (edit_inventory.php?id=3)
$item_id_to_edit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// fetch that item
$get_item_query = "SELECT id, item_name, total_quantity, available_quantity FROM equipment_inventory WHERE id = ?";
$get_item_statement = mysqli_prepare($database_connection, $get_item_query);
mysqli_stmt_bind_param($get_item_statement, "i", $item_id_to_edit);
mysqli_stmt_execute($get_item_statement);
$get_item_result = mysqli_stmt_get_result($get_item_statement);
$item_row = mysqli_fetch_assoc($get_item_result);
mysqli_stmt_close($get_item_statement);

// go back if the item does not exist
if ($item_row == null) {
    $_SESSION['inventory_message'] = "That equipment item could not be found.";
    $_SESSION['inventory_message_type'] = "error";
    header("Location: manage_inventory.php");
    exit();
}

$page_title = "Edit Equipment";
require_once '../../core/header.php';

// validation message from process_inventory.php (if any)
$inventory_message = "";
$inventory_message_type = "";
if (isset($_SESSION['inventory_message'])) {
    $inventory_message = $_SESSION['inventory_message'];
    $inventory_message_type = $_SESSION['inventory_message_type'];
    unset($_SESSION['inventory_message']);
    unset($_SESSION['inventory_message_type']);
}
?>

<section class="form-card">
    <h1>Edit Equipment</h1>

    <?php if ($inventory_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($inventory_message_type); ?>">
            <?php echo htmlspecialchars($inventory_message); ?>
        </p>
    <?php endif; ?>

    <form action="process_inventory.php" method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="item_id" value="<?php echo $item_row['id']; ?>">

        <div class="form-group">
            <label for="item_name">Item Name</label>
            <input type="text" id="item_name" name="item_name" maxlength="100"
                   value="<?php echo htmlspecialchars($item_row['item_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="total_quantity">Total Quantity</label>
            <input type="number" id="total_quantity" name="total_quantity" min="0"
                   value="<?php echo $item_row['total_quantity']; ?>" required>
        </div>

        <div class="form-group">
            <label for="available_quantity">Available Quantity</label>
            <input type="number" id="available_quantity" name="available_quantity" min="0"
                   value="<?php echo $item_row['available_quantity']; ?>" required>
        </div>

        <button type="submit" class="btn">Save Changes</button>
        <a href="manage_inventory.php">Cancel</a>
    </form>
</section>

<?php require_once '../../core/footer.php'; ?>