<?php
// Public view to list all sports clubs. No login required.
$page_title = "Browse Clubs";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// Fetch all clubs ordered alphabetically
$get_clubs_query = "SELECT * FROM sports_clubs ORDER BY name ASC";
$clubs_result = mysqli_query($database_connection, $get_clubs_query);
?>

<section class="page-section">
    <h1>Sports Clubs</h1>
    <p>Find a club that matches your interests.</p>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
        <p><a class="btn" href="manage_clubs.php">Manage Clubs</a></p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($clubs_result) == 0): ?>
        <p>No clubs have been added yet.</p>
    <?php else: ?>
        <div class="club-list">
            <?php while ($club_row = mysqli_fetch_assoc($clubs_result)): ?>
                <article class="club-card">
                    <!-- Use htmlspecialchars to prevent XSS -->
                    <h2><?php echo htmlspecialchars($club_row['name']); ?></h2>
                    <p><?php echo htmlspecialchars($club_row['description']); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once '../../core/footer.php'; ?>