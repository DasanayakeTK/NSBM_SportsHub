<?php
// Admin dashboard: schedule matches (fixtures) and delete them.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Manage Fixtures";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_tournaments.php once, then clear it
$fixture_message = "";
$fixture_message_type = "";
if (isset($_SESSION['tournament_message'])) {
    $fixture_message = $_SESSION['tournament_message'];
    $fixture_message_type = $_SESSION['tournament_message_type'];
    unset($_SESSION['tournament_message']);
    unset($_SESSION['tournament_message_type']);
}

// tournaments for the dropdown
$get_tournaments_query = "SELECT id, name FROM tournaments ORDER BY start_date ASC";
$tournaments_result = mysqli_query($database_connection, $get_tournaments_query);

// the clubs are needed in two dropdowns (Club A and Club B), so I run the query two times
$get_clubs_query = "SELECT id, name FROM sports_clubs ORDER BY name ASC";
$club_a_options_result = mysqli_query($database_connection, $get_clubs_query);
$club_b_options_result = mysqli_query($database_connection, $get_clubs_query);

// all fixtures (same double join as fixture_list.php)
$get_fixtures_query = "SELECT match_fixtures.id, match_fixtures.match_date, match_fixtures.status,
                              tournaments.name AS tournament_name,
                              club_a.name AS club_a_name,
                              club_b.name AS club_b_name
                       FROM match_fixtures
                       JOIN tournaments ON match_fixtures.tournament_id = tournaments.id
                       JOIN sports_clubs AS club_a ON match_fixtures.club_a_id = club_a.id
                       JOIN sports_clubs AS club_b ON match_fixtures.club_b_id = club_b.id
                       ORDER BY match_fixtures.match_date ASC";
$fixtures_result = mysqli_query($database_connection, $get_fixtures_query);
?>

<section class="page-section">
    <h1>Manage Match Fixtures</h1>

    <p><a href="manage_tournaments.php">Go to Manage Tournaments</a></p>

    <?php if ($fixture_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($fixture_message_type); ?>">
            <?php echo htmlspecialchars($fixture_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Schedule a Match</h2>

        <?php if (mysqli_num_rows($tournaments_result) == 0 || mysqli_num_rows($club_a_options_result) < 2): ?>
            <!-- a match needs a tournament and two different clubs -->
            <p>You need at least one tournament and two clubs before scheduling a match.</p>
        <?php else: ?>
            <form action="process_tournaments.php" method="post">
                <input type="hidden" name="action" value="add_fixture">

                <div class="form-group">
                    <label for="tournament_id">Tournament</label>
                    <select id="tournament_id" name="tournament_id" required>
                        <option value="">-- Select a tournament --</option>
                        <?php while ($tournament_row = mysqli_fetch_assoc($tournaments_result)): ?>
                            <option value="<?php echo $tournament_row['id']; ?>">
                                <?php echo htmlspecialchars($tournament_row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="club_a_id">Club A</label>
                    <select id="club_a_id" name="club_a_id" required>
                        <option value="">-- Select Club A --</option>
                        <?php while ($club_a_row = mysqli_fetch_assoc($club_a_options_result)): ?>
                            <option value="<?php echo $club_a_row['id']; ?>">
                                <?php echo htmlspecialchars($club_a_row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="club_b_id">Club B</label>
                    <select id="club_b_id" name="club_b_id" required>
                        <option value="">-- Select Club B --</option>
                        <?php while ($club_b_row = mysqli_fetch_assoc($club_b_options_result)): ?>
                            <option value="<?php echo $club_b_row['id']; ?>">
                                <?php echo htmlspecialchars($club_b_row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="match_date">Match Date</label>
                    <input type="date" id="match_date" name="match_date" required>
                </div>

                <button type="submit" class="btn">Schedule Match</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>All Fixtures</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tournament</th>
                <th>Match</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($fixtures_result) == 0): ?>
                <tr>
                    <td colspan="6">No fixtures found.</td>
                </tr>
            <?php else: ?>
                <?php while ($fixture_row = mysqli_fetch_assoc($fixtures_result)): ?>
                    <tr>
                        <td><?php echo $fixture_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($fixture_row['tournament_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($fixture_row['club_a_name']); ?>
                            vs
                            <?php echo htmlspecialchars($fixture_row['club_b_name']); ?>
                        </td>
                        <td><?php echo date("D, d M Y", strtotime($fixture_row['match_date'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($fixture_row['status'])); ?></td>
                        <td>
                            <a href="process_tournaments.php?action=delete_fixture&id=<?php echo $fixture_row['id']; ?>"
                               onclick="return confirm('Delete this fixture?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>
