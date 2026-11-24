<?php
// Admin dashboard: add training sessions and list all of them (past and upcoming).
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Manage Schedules";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_schedules.php once, then clear it
$schedule_message = "";
$schedule_message_type = "";
if (isset($_SESSION['schedule_message'])) {
    $schedule_message = $_SESSION['schedule_message'];
    $schedule_message_type = $_SESSION['schedule_message_type'];
    unset($_SESSION['schedule_message']);
    unset($_SESSION['schedule_message_type']);
}

// clubs for the dropdown
$get_clubs_query = "SELECT id, name FROM sports_clubs ORDER BY name ASC";
$clubs_result = mysqli_query($database_connection, $get_clubs_query);

// all sessions with club names
$get_sessions_query = "SELECT training_sessions.id, training_sessions.session_date, training_sessions.start_time,
                              training_sessions.venue, sports_clubs.name AS club_name
                       FROM training_sessions
                       JOIN sports_clubs ON training_sessions.club_id = sports_clubs.id
                       ORDER BY training_sessions.session_date ASC, training_sessions.start_time ASC";
$sessions_result = mysqli_query($database_connection, $get_sessions_query);
?>

<section class="page-section">
    <h1>Manage Training Schedules</h1>

    <?php if ($schedule_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($schedule_message_type); ?>">
            <?php echo htmlspecialchars($schedule_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Add a Training Session</h2>

        <?php if (mysqli_num_rows($clubs_result) == 0): ?>
            <p>Please add a club first before creating sessions.</p>
        <?php else: ?>
            <form action="process_schedules.php" method="post">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label for="club_id">Club</label>
                    <select id="club_id" name="club_id" required>
                        <option value="">-- Select a club --</option>
                        <?php while ($club_row = mysqli_fetch_assoc($clubs_result)): ?>
                            <option value="<?php echo $club_row['id']; ?>">
                                <?php echo htmlspecialchars($club_row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="session_date">Date</label>
                    <input type="date" id="session_date" name="session_date" required>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" id="start_time" name="start_time" required>
                </div>

                <div class="form-group">
                    <label for="venue">Venue</label>
                    <input type="text" id="venue" name="venue" maxlength="100" required>
                </div>

                <button type="submit" class="btn">Add Session</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>All Sessions</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Club</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>Venue</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($sessions_result) == 0): ?>
                <tr>
                    <td colspan="6">No sessions found.</td>
                </tr>
            <?php else: ?>
                <?php while ($session_row = mysqli_fetch_assoc($sessions_result)): ?>
                    <tr>
                        <td><?php echo $session_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($session_row['club_name']); ?></td>
                        <td><?php echo date("D, d M Y", strtotime($session_row['session_date'])); ?></td>
                        <td><?php echo date("h:i A", strtotime($session_row['start_time'])); ?></td>
                        <td><?php echo htmlspecialchars($session_row['venue']); ?></td>
                        <td>
                            <a href="edit_schedule.php?id=<?php echo $session_row['id']; ?>">Edit</a> |
                            <a href="process_schedules.php?action=delete&id=<?php echo $session_row['id']; ?>"
                               onclick="return confirm('Delete this training session?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>