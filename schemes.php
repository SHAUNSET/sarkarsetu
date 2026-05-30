<?php
session_start();
include("includes/config.php");

// Safe Highlight Utility
function highlight($text, $word){
    if(empty($word)){ return htmlspecialchars($text); }
    return preg_replace(
        "/(" . preg_quote(htmlspecialchars($word), '/') . ")/i",
        "<span style='background:yellow;padding:2px 4px;border-radius:4px;'>$1</span>",
        htmlspecialchars($text)
    );
}

// Pagination & Search Setup
$results_per_page = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Fetch results with pagination
if($search != ""){
    $searchTerm = "%" . $search . "%";
    
    // Count total matches
    $sql_count = "SELECT COUNT(*) FROM schemes WHERE title LIKE ? OR description LIKE ? OR category LIKE ? OR eligibility LIKE ? OR ministry LIKE ?";
    $stmt_count = mysqli_prepare($conn, $sql_count);
    mysqli_stmt_bind_param($stmt_count, "sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt_count);
    $total_results = mysqli_stmt_get_result($stmt_count)->fetch_row()[0];
    
    // Fetch paginated slice
    $sql = "SELECT * FROM schemes WHERE title LIKE ? OR description LIKE ? OR category LIKE ? OR eligibility LIKE ? OR ministry LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $results_per_page, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $total_results = mysqli_query($conn, "SELECT COUNT(*) FROM schemes")->fetch_row()[0];
    $sql = "SELECT * FROM schemes ORDER BY id DESC LIMIT $results_per_page OFFSET $offset";
    $result = mysqli_query($conn, $sql);
}

$total_pages = ceil($total_results / $results_per_page);
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
        .scheme-card{ background: white; padding: 20px; margin: 15px 0; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .scheme-title{ margin: 0; color: #0d1b2a; }
        .scheme-meta{ margin-top: 10px; color: #555; line-height: 1.7; }
        .page-title{ text-align: center; margin-top: 30px; color: #0d1b2a; }
        .search-box{ display: flex; gap: 10px; justify-content: center; margin: 25px auto; max-width: 600px; }
        .search-box input{ flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ccc; }
        .search-box button{ padding: 12px 18px; background: #1b6ca8; color: white; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<h1 class="page-title">Government Schemes</h1>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search schemes..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<div class="schemes-container">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="scheme-card">
                <h2 class="scheme-title"><?php echo highlight($row['title'], $search); ?></h2>
                <div class="scheme-meta">
                    <p><?php echo highlight($row['description'], $search); ?></p>
                    <p><b>Category:</b> <?php echo highlight($row['category'], $search); ?></p>
                    <p><b>Eligibility:</b> <?php echo highlight($row['eligibility'], $search); ?></p>
                    <p><b>Ministry:</b> <?php echo highlight($row['ministry'], $search); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style='text-align:center;'>No schemes found.</p>
    <?php endif; ?>

    <div style="text-align:center; margin: 30px 0;">
        <?php if($page > 1): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" style="padding: 8px 16px; border: 1px solid #1b6ca8; text-decoration: none; color: #1b6ca8; border-radius: 4px;">« Prev</a>
        <?php endif; ?>

        <span style="padding: 8px 16px; margin: 0 5px; color: #555;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

        <?php if($page < $total_pages): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" style="padding: 8px 16px; border: 1px solid #1b6ca8; text-decoration: none; color: #1b6ca8; border-radius: 4px;">Next »</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>