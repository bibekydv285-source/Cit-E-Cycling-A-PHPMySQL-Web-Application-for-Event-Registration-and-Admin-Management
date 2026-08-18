<?php
// Read credentials from environment variables (Vercel) with local XAMPP fallback
$servername = getenv('DB_HOST') ?: "localhost";
$username   = getenv('DB_USER') ?: "root";
$password   = getenv('DB_PASS') ?: "";
$database   = getenv('DB_NAME') ?: "cycling";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: force UTF-8 so names/text don't get garbled
mysqli_set_charset($conn, "utf8mb4");
?>