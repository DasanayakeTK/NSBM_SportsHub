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

    <script>
        // Hide the loading screen when the whole page has finished loading.
        // First fade it out (opacity 0), then remove it after 500ms (same time as the css transition).
        window.addEventListener('load', function () {
            var pageLoader = document.getElementById('page-loader');
            pageLoader.style.opacity = '0';
            setTimeout(function () {
                pageLoader.style.display = 'none';
            }, 500);
        });
    </script>

    <!-- Link external JavaScript -->
    <script src="<?php echo $website_base_url; ?>assets/js/main.js"></script>
    
    <!-- Existing Loader Script -->
    <script>
        window.addEventListener('load', function() {
            var loader = document.getElementById('page-loader');
            if(loader) {
                loader.style.opacity = '0';
                setTimeout(function() { loader.style.display = 'none'; }, 500);
            }
        });
    </script>
</body>
</html>