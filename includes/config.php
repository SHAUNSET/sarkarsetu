<?php
// 1. Function to load .env file manually (No Composer required)
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
    }
}

// Load the .env file from the root directory
loadEnv(__DIR__ . '/../.env');

// 2. Database Connection using Environment Variables (Secure)
// Note: You should move your DB credentials to your .env file eventually!
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$password = getenv('MYSQLPASSWORD') ?: "Suchet1234567";
$database = getenv('MYSQLDATABASE') ?: "sarkarsetu";
$port = getenv('MYSQLPORT') ?: "3306";

// Include the port in the connection if necessary
$conn = mysqli_connect($host, $user, $password, $database, $port);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>