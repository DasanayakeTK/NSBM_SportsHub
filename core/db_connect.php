<?php
// db_connect.php - Connects the application to the MySQL database
// Require this file at the top of any page that needs database access.

// Database configuration (Update these if your XAMPP setup is different)
$database_host     = "localhost";    
$database_username = "root";         
$database_password = "";             
$database_name     = "sportshub_db"; 

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