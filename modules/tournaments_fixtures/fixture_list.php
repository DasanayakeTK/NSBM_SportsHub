<?php
// Public page: lists all match fixtures with the tournament and both team names.
$page_title = "Match Fixtures";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// sports_clubs is joined two times, so club_a and club_b are aliases for the two teams
$get_fixtures_query = "SELECT match_fixtures.match_date, match_fixtures.status,
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
    <h1>Match Fixtures</h1>

    <table class="data-table">
        <thead>
            <tr>
                <th>Tournament</th>
                <th>Match</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($fixtures_result) == 0): ?>
                <tr>
                    <td colspan="4">No matches have been scheduled yet.</td>
                </tr>
            <?php else: ?>
                <?php while ($fixture_row = mysqli_fetch_assoc($fixtures_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fixture_row['tournament_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($fixture_row['club_a_name']); ?>
                            vs
                            <?php echo htmlspecialchars($fixture_row['club_b_name']); ?>
                        </td>
                        <td><?php echo date("D, d M Y", strtotime($fixture_row['match_date'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($fixture_row['status'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>
