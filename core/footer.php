</main>
    <!-- Closes the <main> tag opened in header.php -->

    <?php
    // footer.php - Included at the bottom of every page
    // Automatically fetch the current year for the copyright
    $current_year = date("Y");
    ?>

    <footer class="site-footer">
        <p>&copy; <?php echo $current_year; ?> NSBM SportsHub. All rights reserved.</p>
    </footer>

</body>
</html>