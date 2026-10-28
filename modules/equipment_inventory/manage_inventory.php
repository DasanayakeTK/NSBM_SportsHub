<?php
// Admin dashboard: add equipment and list all items with Edit and Delete links.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Manage Equipment";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_inventory.php once, then clear it
$inventory_message = "";
$inventory_message_type = "";
if (isset($_SESSION['inventory_message'])) {
    $inventory_message = $_SESSION['inventory_message'];
    $inventory_message_type = $_SESSION['inventory_message_type'];
    unset($_SESSION['inventory_message']);
    unset($_SESSION['inventory_message_type']);
}

// all equipment for the table
$get_equipment_query = "SELECT * FROM equipment_inventory ORDER BY item_name ASC";
$equipment_result = mysqli_query($database_connection, $get_equipment_query);
?>

<section class="page-section">
    <h1>Manage Equipment</h1>

    <?php if ($inventory_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($inventory_message_type); ?>">
            <?php echo htmlspecialchars($inventory_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Add Equipment</h2>
        <form action="process_inventory.php" method="post">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="total_quantity">Total Quantity</label>
                <input type="number" id="total_quantity" name="total_quantity" min="0" required>
            </div>

            <div class="form-group">
                <label for="available_quantity">Available Quantity</label>
                <input type="number" id="available_quantity" name="available_quantity" min="0" required>
            </div>

            <button type="submit" class="btn">Add Equipment</button>
        </form>
    </div>

    <h2>All Equipment</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Total</th>
                <th>Available</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($equipment_result) == 0): ?>
                <tr>
                    <td colspan="5">No equipment found.</td>
                </tr>
            <?php else: ?>
                <?php while ($equipment_row = mysqli_fetch_assoc($equipment_result)): ?>
                    <tr>
                        <td><?php echo $equipment_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($equipment_row['item_name']); ?></td>
                        <td><?php echo $equipment_row['total_quantity']; ?></td>
                        <td><?php echo $equipment_row['available_quantity']; ?></td>
                        <td>
                            <a href="edit_inventory.php?id=<?php echo $equipment_row['id']; ?>">Edit</a> |
                            <a href="process_inventory.php?action=delete&id=<?php echo $equipment_row['id']; ?>"
                               onclick="return confirm('Delete this equipment item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>