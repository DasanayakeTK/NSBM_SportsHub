<?php
// Student page: request a facility and see my own bookings.
require_once '../../core/auth_check.php';

// students only, admins go back to home
if ($_SESSION['role'] != "student") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Book a Facility";
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

// my bookings only (student_id comes from the session)
$logged_in_student_id = $_SESSION['user_id'];
$get_bookings_query = "SELECT facility_name, booking_date, time_slot, status
                       FROM facility_bookings
                       WHERE student_id = ?
                       ORDER BY booking_date DESC, id DESC";
$get_bookings_statement = mysqli_prepare($database_connection, $get_bookings_query);
mysqli_stmt_bind_param($get_bookings_statement, "i", $logged_in_student_id);
mysqli_stmt_execute($get_bookings_statement);
$bookings_result = mysqli_stmt_get_result($get_bookings_statement);
?>

<section class="page-section">
    <h1>Book a Facility</h1>

    <?php if ($booking_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($booking_message_type); ?>">
            <?php echo htmlspecialchars($booking_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Request a Booking</h2>
        <form action="process_bookings.php" method="post">
            <input type="hidden" name="action" value="book">

            <div class="form-group">
                <label for="facility_name">Facility</label>
                <select id="facility_name" name="facility_name" required>
                    <option value="">-- Select a facility --</option>
                    <option value="Gymnasium">Gymnasium</option>
                    <option value="Basketball Court">Basketball Court</option>
                    <option value="Swimming Pool">Swimming Pool</option>
                    <option value="Tennis Court">Tennis Court</option>
                </select>
            </div>

            <div class="form-group">
                <label for="booking_date">Date</label>
                <!-- min stops the browser from picking a past date -->
                <input type="date" id="booking_date" name="booking_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="time_slot">Time Slot</label>
                <select id="time_slot" name="time_slot" required>
                    <option value="">-- Select a time slot --</option>
                    <option value="08:00 AM - 10:00 AM">08:00 AM - 10:00 AM</option>
                    <option value="10:00 AM - 12:00 PM">10:00 AM - 12:00 PM</option>
                    <option value="12:00 PM - 02:00 PM">12:00 PM - 02:00 PM</option>
                    <option value="02:00 PM - 04:00 PM">02:00 PM - 04:00 PM</option>
                    <option value="04:00 PM - 06:00 PM">04:00 PM - 06:00 PM</option>
                </select>
            </div>

            <button type="submit" class="btn">Request Booking</button>
        </form>
    </div>

    <h2>My Bookings</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Facility</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($bookings_result) == 0): ?>
                <tr>
                    <td colspan="4">You have no bookings yet.</td>
                </tr>
            <?php else: ?>
                <?php while ($booking_row = mysqli_fetch_assoc($bookings_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking_row['facility_name']); ?></td>
                        <td><?php echo date("D, d M Y", strtotime($booking_row['booking_date'])); ?></td>
                        <td><?php echo htmlspecialchars($booking_row['time_slot']); ?></td>
                        <!-- status text is also a css class (status-pending, status-approved) -->
                        <td class="status-<?php echo htmlspecialchars($booking_row['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($booking_row['status'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php
mysqli_stmt_close($get_bookings_statement);
require_once '../../core/footer.php';
?>