<?php
// db_config.php - Database configuration for HeidiSQL with your settings

$servername = "localhost:3308";  // Your specific port
$username = "root";               // Your username
$password = "root";               // Your password
$dbname = "bankifsc";             // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF8
mysqli_set_charset($conn, "utf8mb4");

// Function to get connection (for other files)
function getDBConnection() {
    global $conn;
    return $conn;
}

// echo "Connected successfully to HeidiSQL!"; // Uncomment to test
?>