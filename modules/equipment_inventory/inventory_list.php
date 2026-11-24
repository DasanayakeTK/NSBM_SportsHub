<?php
// Public page: shows all sports equipment and how many are available.
$page_title = "Equipment";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// get all equipment
$get_equipment_query = "SELECT * FROM equipment_inventory ORDER BY item_name ASC";
$equipment_result = mysqli_query($database_connection, $get_equipment_query);
?>

<section class="page-section">
    <h1>Sports Equipment</h1>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
        <p><a class="btn" href="manage_inventory.php">Manage Equipment</a></p>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Total Quantity</th>
                <th>Available Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($equipment_result) == 0): ?>
                <tr>
                    <td colspan="3">No equipment has been added yet.</td>
                </tr>
            <?php else: ?>
                <?php while ($equipment_row = mysqli_fetch_assoc($equipment_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($equipment_row['item_name']); ?></td>
                        <td><?php echo $equipment_row['total_quantity']; ?></td>
                        <td><?php echo $equipment_row['available_quantity']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>