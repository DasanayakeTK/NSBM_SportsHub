<?php
// Landing page. Logged-out visitors see a welcome message,
// logged-in users see a dashboard that depends on their role.
$page_title = "Home";
require_once 'core/header.php';

// name saved in the session at login (process_auth.php)
$welcome_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "";
?>

<?php if (!$is_user_logged_in): ?>

    <!-- ===== Visitor (not logged in) ===== -->
    <section class="hero page-section">
        <h1>Welcome to NSBM SportsHub</h1>
        <p>Join university sports clubs, check training schedules and match results, book facilities and see what equipment is available, all in one place.</p>
        <a class="btn" href="<?php echo $website_base_url; ?>modules/auth_profiles/login_view.php">Login</a>
        <a class="btn" href="<?php echo $website_base_url; ?>modules/auth_profiles/register_view.php">Register</a>
    </section>

    <section class="page-section">
        <h2>Have a look around</h2>
        <div class="dashboard-grid">
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/clubs_directory/browse_clubs.php">
                <h3>Sports Clubs</h3>
                <p>Find a club that matches your interests.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/training_schedules/view_schedules.php">
                <h3>Training Schedules</h3>
                <p>See upcoming training sessions.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/fixture_list.php">
                <h3>Match Fixtures</h3>
                <p>Who is playing and when.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/equipment_inventory/inventory_list.php">
                <h3>Equipment</h3>
                <p>Check the available sports equipment.</p>
            </a>
        </div>
    </section>

<?php elseif ($user_role == "admin"): ?>

    <!-- ===== Admin dashboard ===== -->
    <section class="hero page-section">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($welcome_name); ?>. Choose something to manage.</p>
    </section>

    <section class="page-section">
        <div class="dashboard-grid">
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/clubs_directory/manage_clubs.php">
                <h3>Manage Clubs</h3>
                <p>Add, edit and delete sports clubs.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/club_memberships/approve_members.php">
                <h3>Approve Members</h3>
                <p>Review club membership requests.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/training_schedules/manage_schedules.php">
                <h3>Manage Schedules</h3>
                <p>Plan training sessions.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/manage_tournaments.php">
                <h3>Tournaments</h3>
                <p>Create and delete tournaments.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/manage_fixtures.php">
                <h3>Manage Fixtures</h3>
                <p>Schedule matches between clubs.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/match_results/manage_results.php">
                <h3>Update Results</h3>
                <p>Mark matches as completed or cancelled.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/facility_bookings/manage_bookings.php">
                <h3>Approve Bookings</h3>
                <p>Approve or reject facility requests.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/equipment_inventory/manage_inventory.php">
                <h3>Manage Equipment</h3>
                <p>Keep the inventory up to date.</p>
            </a>
        </div>
    </section>

<?php else: ?>

    <!-- ===== Student dashboard ===== -->
    <section class="hero page-section">
        <h1>Welcome back, <?php echo htmlspecialchars($welcome_name); ?>!</h1>
        <p>Here is everything you need for university sports.</p>
    </section>

    <section class="page-section">
        <div class="dashboard-grid">
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/club_memberships/apply_club.php">
                <h3>Join a Club</h3>
                <p>Apply to a club and check your applications.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/clubs_directory/browse_clubs.php">
                <h3>Browse Clubs</h3>
                <p>See all the sports clubs.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/training_schedules/view_schedules.php">
                <h3>Training Schedules</h3>
                <p>Upcoming training sessions.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/tournaments_fixtures/fixture_list.php">
                <h3>Match Fixtures</h3>
                <p>Who is playing and when.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/match_results/completed_matches.php">
                <h3>Results</h3>
                <p>Completed and cancelled matches.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/facility_bookings/book_facility.php">
                <h3>Book a Facility</h3>
                <p>Request the gym, courts or pool.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/equipment_inventory/inventory_list.php">
                <h3>Equipment</h3>
                <p>Check what is available.</p>
            </a>
            <a class="dashboard-card" href="<?php echo $website_base_url; ?>modules/auth_profiles/profile.php">
                <h3>My Profile</h3>
                <p>See your account details.</p>
            </a>
        </div>
    </section>

<?php endif; ?>

<?php require_once 'core/footer.php'; ?>
