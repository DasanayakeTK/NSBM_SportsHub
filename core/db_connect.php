<?php
// db_connect.php - Connects the application to the MySQL database
// Require this file at the top of any page that needs database access.

// Database configuration: automatically switches between local XAMPP and live server
$current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
if (
    $current_host == 'localhost' ||
    $current_host == '127.0.0.1' ||
    strpos($current_host, 'localhost:') === 0 ||
    strpos($current_host, '127.0.0.1:') === 0
) {
    // Local XAMPP credentials
    $database_host     = "localhost";    
    $database_username = "root";         
    $database_password = "";             
    $database_name     = "sportshub_db"; 
    $website_base_url  = '/NSBM_SportsHub/';
} else {
    // Live server credentials (replace these placeholders on the live host after uploading)
    $database_host     = 'LIVE_DB_HOST';
    $database_username = 'LIVE_DB_USER';
    $database_password = 'LIVE_DB_PASS';
    $database_name     = 'LIVE_DB_NAME';
    $website_base_url  = '/';
}

// Disable default mysqli exceptions to handle errors manually below
mysqli_report(MYSQLI_REPORT_OFF);

// Attempt database connection
$database_connection = mysqli_connect(
    $database_host,
    $database_username,
    $database_password,
    $database_name
);

// Check if connection failed and show a clean error message
if (!$database_connection) {
    die(
        "<h2>Database Connection Failed</h2>" .
        "<p>Error: " . mysqli_connect_error() . "</p>" .
        "<p>Check if XAMPP MySQL is running and if sportshub_schema.sql was imported.</p>"
    );
}

// Ensure special characters display correctly
mysqli_set_charset($database_connection, "utf8mb4");

// Closing PHP tag intentionally omitted to prevent whitespace issues