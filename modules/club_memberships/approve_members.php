<?php
// Admin page: approve or reject pending club membership requests.
require_once '../../core/auth_check.php';

// admins only
if ($_SESSION['role'] != "admin") {
    header("Location: ../../index.php");
    exit();
}

$page_title = "Approve Members";
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

// all pending requests, joined with users (student name) and sports_clubs (club name)
$get_pending_query = "SELECT club_memberships.id, users.full_name, users.email, sports_clubs.name AS club_name
                      FROM club_memberships
                      JOIN users ON club_memberships.student_id = users.id
                      JOIN sports_clubs ON club_memberships.club_id = sports_clubs.id
                      WHERE club_memberships.status = 'pending'
                      ORDER BY club_memberships.id ASC";
$pending_result = mysqli_query($database_connection, $get_pending_query);
?>

<section class="page-section">
    <h1>Pending Membership Requests</h1>

    <?php if ($membership_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($membership_message_type); ?>">
            <?php echo htmlspecialchars($membership_message); ?>
        </p>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Student</th>
                <th>Email</th>
                <th>Club</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($pending_result) == 0): ?>
                <tr>
                    <td colspan="5">No pending requests right now.</td>
                </tr>
            <?php else: ?>
                <?php while ($request_row = mysqli_fetch_assoc($pending_result)): ?>
                    <tr>
                        <td><?php echo $request_row['id']; ?></td>
                        <td><?php echo htmlspecialchars($request_row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($request_row['email']); ?></td>
                        <td><?php echo htmlspecialchars($request_row['club_name']); ?></td>
                        <td>
                            <a href="process_applications.php?action=update_status&id=<?php echo $request_row['id']; ?>&status=approved">Approve</a> |
                            <a href="process_applications.php?action=update_status&id=<?php echo $request_row['id']; ?>&status=rejected">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once '../../core/footer.php'; ?>
