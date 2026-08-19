<?php
// Read credentials from environment variables (Railway) with local XAMPP fallback
$host   = getenv('DB_HOST') ?: "localhost";
$port   = getenv('DB_PORT') ?: 3306;
$user   = getenv('DB_USER') ?: "root";
$pass   = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "cycling";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Force UTF-8 so names/text don't get garbled
$conn->set_charset("utf8mb4");
?>