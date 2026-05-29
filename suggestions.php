<?php
include("includes/config.php");

$q = isset($_GET['q']) ? trim($_GET['q']) : "";

if($q == ""){
    header("Content-Type: application/json");
    echo json_encode([]);
    exit;
}

// Secure Prepared Statements Configuration
$sql = "SELECT title FROM schemes WHERE title LIKE ? LIMIT 8";
$stmt = mysqli_prepare($conn, $sql);
$likeParam = "%" . $q . "%";
mysqli_stmt_bind_param($stmt, "s", $likeParam);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$suggestions = [];
while($row = mysqli_fetch_assoc($result)){
    $suggestions[] = $row['title'];
}

header("Content-Type: application/json");
echo json_encode($suggestions);
exit;
?>