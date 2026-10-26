<?php
// Admin dashboard: add tournaments and delete them.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Manage Tournaments";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_tournaments.php once, then clear it
$tournament_message = "";
$tournament_message_type = "";
if (isset($_SESSION['tournament_message'])) {
    $tournament_message = $_SESSION['tournament_message'];
    $tournament_message_type = $_SESSION['tournament_message_type'];
    unset($_SESSION['tournament_message']);
    unset($_SESSION['tournament_message_type']);
}

// all tournaments for the table
$get_tournaments_query = "SELECT * FROM tournaments ORDER BY start_date ASC";
$tournaments_result = mysqli_query($database_connection, $get_tournaments_query);
?>

<section class="page-section">
    <h1>Manage Tournaments</h1>

    <p><a href="manage_fixtures.php">Go to Manage Fixtures</a></p>

    <?php if ($tournament_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($tournament_message_type); ?>">
            <?php echo htmlspecialchars($tournament_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Add a Tournament</h2>
        <form action="process_tournaments.php" method="post">
            <input type="hidden" name="action" value="add_tournament">

            <div class="form-group">
                <label for="tournament_name">Tournament Name</label>
                <input type="text" id="tournament_name" name="tournament_name" maxlength="150" required>
            </div>

            <div class="form-group">
                <label for="sport_type">Sport Type</label>
                <input type="text" id="sport_type" name="sport_type" maxlength="50" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <button type="submit" class="btn">Add Tournament</button>
        </form>
    </div>

    <h2>All Tournaments</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Sport</th>
                <th>Start Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($tournaments_result) == 0): ?>
                <tr>
                    <td colspan="5">No tournaments found.</td>
                </tr>
            <?php else: ?>
                <?php while ($tournament_row = mysqli_fetch_assoc($tournaments_result)): ?>
                    <tr>
                        <td><?php echo $tournament_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($tournament_row['name']); ?></td>
                        <td><?php echo htmlspecialchars($tournament_row['sport_type']); ?></td>
                        <td><?php echo date("d M Y", strtotime($tournament_row['start_date'])); ?></td>
                        <td>
                            <a href="process_tournaments.php?action=delete_tournament&id=<?php echo $tournament_row['id']; ?>"
                               onclick="return confirm('Delete this tournament? All its match fixtures will also be deleted.');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>
