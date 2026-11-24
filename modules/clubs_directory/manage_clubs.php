<?php
// Admin dashboard for managing (adding/deleting) sports clubs.
require_once '../../core/auth_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Manage Clubs";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// Session message handling
$club_message = "";
$club_message_type = "";
if (isset($_SESSION['club_message'])) {
    $club_message = $_SESSION['club_message'];
    $club_message_type = $_SESSION['club_message_type'];
    unset($_SESSION['club_message']);
    unset($_SESSION['club_message_type']);
}

// Fetch all clubs for the data table
$get_clubs_query = "SELECT * FROM sports_clubs ORDER BY id ASC";
$clubs_result = mysqli_query($database_connection, $get_clubs_query);
?>

<section class="page-section">
    <h1>Manage Sports Clubs</h1>

    <?php if ($club_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($club_message_type); ?>">
            <?php echo htmlspecialchars($club_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Add a New Club</h2>
        <form action="process_clubs.php" method="post">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="club_name">Club Name</label>
                <input type="text" id="club_name" name="club_name" maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="club_description">Description</label>
                <textarea id="club_description" name="club_description" rows="4"></textarea>
            </div>

            <button type="submit" class="btn">Add Club</button>
        </form>
    </div>

    <h2>All Clubs</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Club Name</th>
                <th>Description</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($clubs_result) == 0): ?>
                <tr>
                    <td colspan="5">No clubs found.</td>
                </tr>
            <?php else: ?>
                <?php while ($club_row = mysqli_fetch_assoc($clubs_result)): ?>
                    <tr>
                        <td><?php echo $club_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($club_row['name']); ?></td>
                        <td><?php echo htmlspecialchars($club_row['description']); ?></td>
                        <td><?php echo $club_row['created_at']; ?></td>
                        <td>
                            <a href="edit_club.php?id=<?php echo $club_row['id']; ?>">Edit</a> |
                            <!-- JS confirmation before executing delete via GET request -->
                            <a href="process_clubs.php?action=delete&id=<?php echo $club_row['id']; ?>"
                               onclick="return confirm('Delete this club? Related data will also be removed.');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>