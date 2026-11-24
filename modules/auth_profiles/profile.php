<?php
// Profile page: shows the logged-in user's name, email and role.
require_once '../../core/auth_check.php';
require_once '../../core/db_connect.php';

// get the details of the logged-in user (id comes from the session)
$logged_in_user_id = $_SESSION['user_id'];
$get_user_query = "SELECT full_name, email, role, created_at FROM users WHERE id = ?";
$get_user_statement = mysqli_prepare($database_connection, $get_user_query);
mysqli_stmt_bind_param($get_user_statement, "i", $logged_in_user_id);
mysqli_stmt_execute($get_user_statement);
$get_user_result = mysqli_stmt_get_result($get_user_statement);
$user_row = mysqli_fetch_assoc($get_user_result);   // null if the user was deleted
mysqli_stmt_close($get_user_statement);

// the account no longer exists, so log the user out
if ($user_row == null) {
    header("Location: ../../core/logout.php");
    exit();
}

$page_title = "My Profile";
require_once '../../core/header.php';
?>

<section class="page-section">
    <h1>My Profile</h1>

    <div class="profile-card">
        <!-- first letter of the name inside a circle -->
        <div class="profile-avatar"><?php echo htmlspecialchars(strtoupper(substr($user_row['full_name'], 0, 1))); ?></div>

        <dl class="profile-list">
            <dt>Full Name</dt>
            <dd><?php echo htmlspecialchars($user_row['full_name']); ?></dd>

            <dt>Email</dt>
            <dd><?php echo htmlspecialchars($user_row['email']); ?></dd>

            <dt>Role</dt>
            <dd><span class="role-badge"><?php echo htmlspecialchars($user_row['role']); ?></span></dd>

            <dt>Member Since</dt>
            <dd><?php echo date("d M Y", strtotime($user_row['created_at'])); ?></dd>
        </dl>

        <a class="btn" href="<?php echo $website_base_url; ?>core/logout.php">Logout</a>
    </div>
</section>

<?php require_once '../../core/footer.php'; ?>

