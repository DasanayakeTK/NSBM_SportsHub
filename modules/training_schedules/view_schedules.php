<?php
// Public page: shows upcoming training sessions only. No login needed.
$page_title = "Training Schedules";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// upcoming sessions only (today or later), join with sports_clubs to get the club name
$get_sessions_query = "SELECT training_sessions.session_date, training_sessions.start_time, training_sessions.venue,
                              sports_clubs.name AS club_name
                       FROM training_sessions
                       JOIN sports_clubs ON training_sessions.club_id = sports_clubs.id
                       WHERE training_sessions.session_date >= CURDATE()
                       ORDER BY training_sessions.session_date ASC, training_sessions.start_time ASC";
$sessions_result = mysqli_query($database_connection, $get_sessions_query);
?>

<section class="page-section">
    <h1>Upcoming Training Sessions</h1>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
        <p><a class="btn" href="manage_schedules.php">Manage Schedules</a></p>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Club</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>Venue</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($sessions_result) == 0): ?>
                <tr>
                    <td colspan="4">No upcoming training sessions.</td>
                </tr>
            <?php else: ?>
                <?php while ($session_row = mysqli_fetch_assoc($sessions_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session_row['club_name']); ?></td>
                        <!-- strtotime + date turns 2026-11-02 into Mon, 02 Nov 2026 -->
                        <td><?php echo date("D, d M Y", strtotime($session_row['session_date'])); ?></td>
                        <!-- 16:00:00 becomes 04:00 PM -->
                        <td><?php echo date("h:i A", strtotime($session_row['start_time'])); ?></td>
                        <td><?php echo htmlspecialchars($session_row['venue']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>