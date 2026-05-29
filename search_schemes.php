<?php

include("includes/config.php");

$search = isset($_GET['q']) ? trim($_GET['q']) : "";

// empty search
if($search == ""){
    echo json_encode([]);
    exit;
}

// search query
$sql = "SELECT * FROM schemes
        WHERE title LIKE '%$search%'
        OR description LIKE '%$search%'
        OR category LIKE '%$search%'
        OR eligibility LIKE '%$search%'
        OR ministry LIKE '%$search%'";

$result = mysqli_query($conn, $sql);

$data = [];

while($row = mysqli_fetch_assoc($result)){

    $data[] = [
        "title" => $row['title'],
        "description" => $row['description'],
        "category" => $row['category'],
        "eligibility" => $row['eligibility'],
        "ministry" => $row['ministry']
    ];

}

// return json
header('Content-Type: application/json');

echo json_encode($data);

?>