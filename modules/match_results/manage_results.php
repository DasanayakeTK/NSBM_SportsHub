<?php
// Admin dashboard: scheduled matches that still need a result.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Update Results";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_results.php once, then clear it
$result_message = "";
$result_message_type = "";
if (isset($_SESSION['result_message'])) {
    $result_message = $_SESSION['result_message'];
    $result_message_type = $_SESSION['result_message_type'];
    unset($_SESSION['result_message']);
    unset($_SESSION['result_message_type']);
}

// only matches that are still scheduled
$get_scheduled_query = "SELECT match_fixtures.id, match_fixtures.match_date,
                               tournaments.name AS tournament_name,
                               club_a.name AS club_a_name,
                               club_b.name AS club_b_name
                        FROM match_fixtures
                        JOIN tournaments ON match_fixtures.tournament_id = tournaments.id
                        JOIN sports_clubs AS club_a ON match_fixtures.club_a_id = club_a.id
                        JOIN sports_clubs AS club_b ON match_fixtures.club_b_id = club_b.id
                        WHERE match_fixtures.status = 'scheduled'
                        ORDER BY match_fixtures.match_date ASC";
$scheduled_result = mysqli_query($database_connection, $get_scheduled_query);
?>

<section class="page-section">
    <h1>Update Match Results</h1>

    <?php if ($result_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($result_message_type); ?>">
            <?php echo htmlspecialchars($result_message); ?>
        </p>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tournament</th>
                <th>Match</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($scheduled_result) == 0): ?>
                <tr>
                    <td colspan="5">No scheduled matches waiting for a result.</td>
                </tr>
            <?php else: ?>
                <?php while ($fixture_row = mysqli_fetch_assoc($scheduled_result)): ?>
                    <tr>
                        <td><?php echo $fixture_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($fixture_row['tournament_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($fixture_row['club_a_name']); ?>
                            vs
                            <?php echo htmlspecialchars($fixture_row['club_b_name']); ?>
                        </td>
                        <td><?php echo date("D, d M Y", strtotime($fixture_row['match_date'])); ?></td>
                        <td>
                            <!-- small form in each row, the hidden fixture_id says which match to update -->
                            <form action="process_results.php" method="post" class="inline-form">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="fixture_id" value="<?php echo $fixture_row['id']; ?>">

                                <select name="new_status" required>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" class="btn">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>