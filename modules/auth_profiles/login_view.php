<?php
// Login UI. Submits to process_auth.php
$page_title = "Login";
require_once '../../core/header.php';

// Check for session messages (like invalid password) and clear them after reading
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
    <h1>Login</h1>

    <?php if ($auth_message != ""): ?>
        <p class="message-box message-<?php echo htmlspecialchars($auth_message_type); ?>">
            <?php echo htmlspecialchars($auth_message); ?>
        </p>
    <?php endif; ?>

    <form action="process_auth.php" method="post">
        <!-- Identify form action -->
        <input type="hidden" name="form_action" value="login">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p>New here? <a href="register_view.php">Create an account</a></p>
</section>

<?php require_once '../../core/footer.php'; ?>