<?php
// Admin page: approve or reject pending facility bookings.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Approve Bookings";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_bookings.php once, then clear it
$booking_message = "";
$booking_message_type = "";
if (isset($_SESSION['booking_message'])) {
    $booking_message = $_SESSION['booking_message'];
    $booking_message_type = $_SESSION['booking_message_type'];
    unset($_SESSION['booking_message']);
    unset($_SESSION['booking_message_type']);
}

// pending bookings, joined with users to get the student name
$get_pending_query = "SELECT facility_bookings.id, facility_bookings.facility_name, facility_bookings.booking_date,
                             facility_bookings.time_slot, users.full_name
                      FROM facility_bookings
                      JOIN users ON facility_bookings.student_id = users.id
                      WHERE facility_bookings.status = 'pending'
                      ORDER BY facility_bookings.booking_date ASC, facility_bookings.id ASC";
$pending_result = mysqli_query($database_connection, $get_pending_query);
?>

<section class="page-section">
    <h1>Pending Facility Bookings</h1>

    <?php if ($booking_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($booking_message_type); ?>">
            <?php echo htmlspecialchars($booking_message); ?>
        </p>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Facility</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($pending_result) == 0): ?>
                <tr>
                    <td colspan="6">No pending bookings right now.</td>
                </tr>
            <?php else: ?>
                <?php while ($booking_row = mysqli_fetch_assoc($pending_result)): ?>
                    <tr>
                        <td><?php echo $booking_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking_row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking_row['facility_name']); ?></td>
                        <td><?php echo date("D, d M Y", strtotime($booking_row['booking_date'])); ?></td>
                        <td><?php echo htmlspecialchars($booking_row['time_slot']); ?></td>
                        <td>
                            <a href="process_bookings.php?action=update_status&id=<?php echo $booking_row['id']; ?>&status=approved">Approve</a> |
                            <a href="process_bookings.php?action=update_status&id=<?php echo $booking_row['id']; ?>&status=rejected"
                               onclick="return confirm('Reject this booking? It will be removed.');">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>