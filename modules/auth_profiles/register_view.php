<?php
// Registration UI. Sends POST data to process_auth.php to save the user.
$page_title = "Register";
require_once '../../core/header.php';

// Grab error/success messages from session if they exist, then clear them
$auth_message = "";
$auth_message_type = "";
if (isset($_SESSION['auth_message'])) {
    $auth_message = $_SESSION['auth_message'];
    $auth_message_type = $_SESSION['auth_message_type'];
    unset($_SESSION['auth_message']);
    unset($_SESSION['auth_message_type']);
}
?>

<section class="form-card">
    <h1>Create an Account</h1>

    <?php if ($auth_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($auth_message_type); ?>">
            <?php echo htmlspecialchars($auth_message); ?>
        </p>
    <?php endif; ?>

    <form action="process_auth.php" method="post">
        <!-- Hidden input so the processor knows which form this is -->
        <input type="hidden" name="form_action" value="register">

        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>

    <p>Already have an account? <a href="login_view.php">Login here</a></p>
</section>

<?php require_once '../../core/footer.php'; ?>