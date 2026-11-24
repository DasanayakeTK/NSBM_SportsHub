<?php
// Admin interface to edit an existing club's details.
require_once '../../core/auth_check.php';

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

require_once '../../core/db_connect.php';

// Capture club ID from URL parameter safely
$club_id_to_edit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch club details using prepared statement
$get_club_query = "SELECT id, name, description FROM sports_clubs WHERE id = ?";
$get_club_statement = mysqli_prepare($database_connection, $get_club_query);
mysqli_stmt_bind_param($get_club_statement, "i", $club_id_to_edit);
mysqli_stmt_execute($get_club_statement);
$get_club_result = mysqli_stmt_get_result($get_club_statement);
$club_row = mysqli_fetch_assoc($get_club_result);
mysqli_stmt_close($get_club_statement);

// Redirect if ID is invalid
if ($club_row == null) {
    $_SESSION['club_message'] = "That club could not be found.";
    $_SESSION['club_message_type'] = "error";
    header("Location: manage_clubs.php");
    exit();
}

$page_title = "Edit Club";
require_once '../../core/header.php';

// Check for validation errors from process_clubs.php
$club_message = "";
$club_message_type = "";
if (isset($_SESSION['club_message'])) {
    $club_message = $_SESSION['club_message'];
    $club_message_type = $_SESSION['club_message_type'];
    unset($_SESSION['club_message']);
    unset($_SESSION['club_message_type']);
}
?>

<section class="form-card">
    <h1>Edit Club</h1>

    <?php if ($club_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($club_message_type); ?>">
            <?php echo htmlspecialchars($club_message); ?>
        </p>
    <?php endif; ?>

    <!-- Pre-fill form with existing DB values -->
    <form action="process_clubs.php" method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="club_id" value="<?php echo $club_row['id']; ?>">

        <div class="form-group">
            <label for="club_name">Club Name</label>
            <input type="text" id="club_name" name="club_name" maxlength="100"
                   value="<?php echo htmlspecialchars($club_row['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="club_description">Description</label>
            <textarea id="club_description" name="club_description" rows="4"><?php echo htmlspecialchars($club_row['description']); ?></textarea>
        </div>

        <button type="submit" class="btn">Save Changes</button>
        <a href="manage_clubs.php">Cancel</a>
    </form>
</section>

<?php require_once '../../core/footer.php'; ?>