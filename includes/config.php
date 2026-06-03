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

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE');
$port = (int)getenv('MYSQLPORT');

// Check if credentials are loaded
if (!$host || !$user || !$password || !$database) {
    die("Error: Database environment variables are not set correctly on the server.");
}

$conn = mysqli_connect($host, $user, $password, $database, $port);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>