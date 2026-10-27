<?php
// Public page: matches that are already finished (completed) or cancelled.
$page_title = "Match Results";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// club_a and club_b are two joins on sports_clubs, one for each team
$get_results_query = "SELECT match_fixtures.match_date, match_fixtures.status,
                             tournaments.name AS tournament_name,
                             club_a.name AS club_a_name,
                             club_b.name AS club_b_name
                      FROM match_fixtures
                      JOIN tournaments ON match_fixtures.tournament_id = tournaments.id
                      JOIN sports_clubs AS club_a ON match_fixtures.club_a_id = club_a.id
                      JOIN sports_clubs AS club_b ON match_fixtures.club_b_id = club_b.id
                      WHERE match_fixtures.status = 'completed' OR match_fixtures.status = 'cancelled'
                      ORDER BY match_fixtures.match_date DESC";
$results_result = mysqli_query($database_connection, $get_results_query);
?>

<section class="page-section">
    <h1>Match Results</h1>

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
            <?php if (mysqli_num_rows($results_result) == 0): ?>
                <tr>
                    <td colspan="4">No results yet.</td>
                </tr>
            <?php else: ?>
                <?php while ($result_row = mysqli_fetch_assoc($results_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($result_row['tournament_name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($result_row['club_a_name']); ?>
                            vs
                            <?php echo htmlspecialchars($result_row['club_b_name']); ?>
                        </td>
                        <td><?php echo date("D, d M Y", strtotime($result_row['match_date'])); ?></td>
                        <!-- status text is also a css class (status-completed, status-cancelled) -->
                        <td class="status-<?php echo htmlspecialchars($result_row['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($result_row['status'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>