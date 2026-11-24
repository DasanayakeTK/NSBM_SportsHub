<?php
// Student page: apply to a club and see the status of my applications.
require_once '../../core/auth_check.php';

// only students use this page, admins go back to home
if ($_SESSION['role'] != "student") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Join a Club";
require_once '../../core/header.php';
require_once '../../core/db_connect.php';

// show the message from process_applications.php once, then clear it
$membership_message = "";
$membership_message_type = "";
if (isset($_SESSION['membership_message'])) {
    $membership_message = $_SESSION['membership_message'];
    $membership_message_type = $_SESSION['membership_message_type'];
    unset($_SESSION['membership_message']);
    unset($_SESSION['membership_message_type']);
}

// clubs for the dropdown
$get_clubs_query = "SELECT id, name FROM sports_clubs ORDER BY name ASC";
$clubs_result = mysqli_query($database_connection, $get_clubs_query);

// my applications (join with sports_clubs to get the club name)
$logged_in_student_id = $_SESSION['user_id'];
$get_applications_query = "SELECT club_memberships.id, sports_clubs.name AS club_name, club_memberships.status
                           FROM club_memberships
                           JOIN sports_clubs ON club_memberships.club_id = sports_clubs.id
                           WHERE club_memberships.student_id = ?
                           ORDER BY club_memberships.id DESC";
$get_applications_statement = mysqli_prepare($database_connection, $get_applications_query);
mysqli_stmt_bind_param($get_applications_statement, "i", $logged_in_student_id);
mysqli_stmt_execute($get_applications_statement);
$applications_result = mysqli_stmt_get_result($get_applications_statement);
?>

<section class="page-section">
    <h1>Join a Club</h1>

    <?php if ($membership_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($membership_message_type); ?>">
            <?php echo htmlspecialchars($membership_message); ?>
        </p>
    <?php endif; ?>

    <div class="form-card">
        <h2>Apply to a Club</h2>

        <?php if (mysqli_num_rows($clubs_result) == 0): ?>
            <p>There are no clubs to join yet.</p>
        <?php else: ?>
            <form action="process_applications.php" method="post">
                <input type="hidden" name="action" value="apply">

                <div class="form-group">
                    <label for="club_id">Choose a Club</label>
                    <select id="club_id" name="club_id" required>
                        <option value="">-- Select a club --</option>
                        <?php while ($club_row = mysqli_fetch_assoc($clubs_result)): ?>
                            <option value="<?php echo $club_row['id']; ?>">
                                <?php echo htmlspecialchars($club_row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn">Apply</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>My Applications</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Club</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($applications_result) == 0): ?>
                <tr>
                    <td colspan="3">You have not applied to any clubs yet.</td>
                </tr>
            <?php else: ?>
                <?php while ($application_row = mysqli_fetch_assoc($applications_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application_row['club_name']); ?></td>
                        <!-- status text is also used as a css class (status-pending, etc.) -->
                        <td class="status-<?php echo htmlspecialchars($application_row['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($application_row['status'])); ?>
                        </td>
                        <td>
                            <?php if ($application_row['status'] == "pending"): ?>
                                <!-- only pending applications can be withdrawn -->
                                <a href="process_applications.php?action=withdraw&id=<?php echo $application_row['id']; ?>"
                                   onclick="return confirm('Withdraw this application?');">Withdraw</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php
mysqli_stmt_close($get_applications_statement);
require_once '../../core/footer.php';
?>
