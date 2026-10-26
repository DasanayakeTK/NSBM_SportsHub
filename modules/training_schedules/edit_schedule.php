<?php
// Admin page: edit one training session. The form is pre-filled with the current values.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

require_once '../../core/db_connect.php';

// get the session id from the link (edit_schedule.php?id=3)
$session_id_to_edit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// fetch that session
$get_session_query = "SELECT id, club_id, session_date, start_time, venue FROM training_sessions WHERE id = ?";
$get_session_statement = mysqli_prepare($database_connection, $get_session_query);
mysqli_stmt_bind_param($get_session_statement, "i", $session_id_to_edit);
mysqli_stmt_execute($get_session_statement);
$get_session_result = mysqli_stmt_get_result($get_session_statement);
$session_row = mysqli_fetch_assoc($get_session_result);
mysqli_stmt_close($get_session_statement);

// go back if the session does not exist
if ($session_row == null) {
    $_SESSION['schedule_message'] = "That training session could not be found.";
    $_SESSION['schedule_message_type'] = "error";
    header("Location: manage_schedules.php");
    exit();
}

$page_title = "Edit Training Session";
require_once '../../core/header.php';

// validation message from process_schedules.php (if any)
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
?>

<section class="form-card">
    <h1>Edit Training Session</h1>

    <?php if ($schedule_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($schedule_message_type); ?>">
            <?php echo htmlspecialchars($schedule_message); ?>
        </p>
    <?php endif; ?>

    <form action="process_schedules.php" method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="session_id" value="<?php echo $session_row['id']; ?>">

        <div class="form-group">
            <label for="club_id">Club</label>
            <select id="club_id" name="club_id" required>
                <?php while ($club_row = mysqli_fetch_assoc($clubs_result)): ?>
                    <!-- selected makes the dropdown start on the session's current club -->
                    <option value="<?php echo $club_row['id']; ?>"
                        <?php if ($club_row['id'] == $session_row['club_id']) { echo "selected"; } ?>>
                        <?php echo htmlspecialchars($club_row['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="session_date">Date</label>
            <input type="date" id="session_date" name="session_date"
                   value="<?php echo $session_row['session_date']; ?>" required>
        </div>

        <div class="form-group">
            <label for="start_time">Start Time</label>
            <!-- the database gives 16:00:00, the time input only needs the first 5 characters -->
            <input type="time" id="start_time" name="start_time"
                   value="<?php echo substr($session_row['start_time'], 0, 5); ?>" required>
        </div>

        <div class="form-group">
            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue" maxlength="100"
                   value="<?php echo htmlspecialchars($session_row['venue']); ?>" required>
        </div>

        <button type="submit" class="btn">Save Changes</button>
        <a href="manage_schedules.php">Cancel</a>
    </form>
</section>

<?php require_once '../../core/footer.php'; ?>