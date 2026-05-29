<?php
session_start();
include("includes/config.php");

// Safe Highlight Utility
function highlight($text, $word){
    if(empty($word)){
        return htmlspecialchars($text);
    }
    return preg_replace(
        "/(" . preg_quote(htmlspecialchars($word), '/') . ")/i",
        "<span style='background:yellow;padding:2px 4px;border-radius:4px;'>$1</span>",
        htmlspecialchars($text)
    );
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schemes - SarkarSetu AI</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .schemes-container{ max-width: 1100px; margin: auto; padding: 30px; }
        .scheme-card{ background: white; padding: 20px; margin: 15px 0; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: 0.3s; }
        .scheme-card:hover{ transform: translateY(-3px); }
        .scheme-title{ margin: 0; color: #0d1b2a; }
        .scheme-meta{ margin-top: 10px; color: #555; line-height: 1.7; }
        .page-title{ text-align: center; margin-top: 30px; color: #0d1b2a; }
        .back-home{ display: inline-block; margin: 20px; text-decoration: none; color: #1b6ca8; font-weight: bold; }
        .search-box{ display: flex; gap: 10px; justify-content: center; margin: 25px auto; max-width: 600px; }
        .search-box input{ flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; outline: none; }
        .search-box input:focus{ border-color: #1b6ca8; }
        .search-box button{ padding: 12px 18px; border: none; background: #1b6ca8; color: white; border-radius: 8px; cursor: pointer; }
        .search-box button:hover{ background: #14507d; }
        @media(max-width:768px){
            .search-box{ flex-direction: column; }
            .search-box button{ width: 100%; }
            .schemes-container{ padding: 20px; }
        }
    </style>
</head>
<body>

<a class="back-home" href="index.php">← Back to Home</a>
<h1 class="page-title">Government Schemes</h1>

<form method="GET" class="search-box">
    <input 
        type="text" 
        id="searchInput" 
        name="search" 
        placeholder="Search schemes, ministry, category..." 
        value="<?php echo htmlspecialchars($search); ?>"
    >
    <button type="submit">Search</button>
</form>

<div class="schemes-container">
<?php
if($search != ""){
    // Secure SQL using prepared statements
    $sql = "SELECT * FROM schemes WHERE title LIKE ? OR description LIKE ? OR category LIKE ? OR eligibility LIKE ? OR ministry LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    $searchTerm = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT * FROM schemes ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
}

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
?>
    <div class="scheme-card">
        <h2 class="scheme-title"><?php echo highlight($row['title'], $search); ?></h2>
        <div class="scheme-meta">
            <p><?php echo highlight($row['description'], $search); ?></p>
            <p><b>Category:</b> <?php echo highlight($row['category'], $search); ?></p>
            <p><b>Eligibility:</b> <?php echo highlight($row['eligibility'], $search); ?></p>
            <p><b>Ministry:</b> <?php echo highlight($row['ministry'], $search); ?></p>
        </div>
    </div>
<?php
    }
} else {
    echo "<p style='text-align:center;'>No schemes found.</p>";
}
?>
</div>
</body>
</html>