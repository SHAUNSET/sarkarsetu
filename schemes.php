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

// 1. Pagination & Search/Filter Setup
$results_per_page = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$category_filter = isset($_GET['cat']) ? trim($_GET['cat']) : "";

// Map URL short-names to database keywords
$cat_map = [
    'Education' => 'Education',
    'Farmers'   => 'Agriculture',
    'Healthcare' => 'Health',
    'Women'      => 'Women'
];

// 2. Fetch results with logic
if($search != "" || $category_filter != ""){
    $where = [];
    $params = [];
    $types = "";

    if($search != ""){
        $where[] = "(title LIKE ? OR description LIKE ? OR category LIKE ? OR eligibility LIKE ? OR ministry LIKE ?)";
        $searchTerm = "%" . $search . "%";
        for($i=0; $i<5; $i++) { $params[] = $searchTerm; $types .= "s"; }
    }
    
    if($category_filter != "" && array_key_exists($category_filter, $cat_map)){
        $where[] = "category LIKE ?";
        $params[] = "%" . $cat_map[$category_filter] . "%";
        $types .= "s";
    }

    $where_sql = implode(" AND ", $where);

    $sql_count = "SELECT COUNT(*) FROM schemes WHERE $where_sql";
    $stmt_count = mysqli_prepare($conn, $sql_count);
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
    mysqli_stmt_execute($stmt_count);
    $total_results = mysqli_stmt_get_result($stmt_count)->fetch_row()[0];
    
    $sql = "SELECT * FROM schemes WHERE $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
    $params[] = $results_per_page; $params[] = $offset;
    $types .= "ii";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $total_results = mysqli_query($conn, "SELECT COUNT(*) FROM schemes")->fetch_row()[0];
    $sql = "SELECT * FROM schemes ORDER BY id DESC LIMIT $results_per_page OFFSET $offset";
    $result = mysqli_query($conn, $sql);
}

$total_pages = max(1, ceil($total_results / $results_per_page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schemes - SarkarSetu AI</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        header { background: #0d1b2a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        header nav a { color: white; margin-left: 20px; text-decoration: none; }
        .schemes-container{ max-width: 1100px; margin: auto; padding: 30px; }
        
        /* Updated Card Styling */
        .scheme-card { 
            background: white; 
            padding: 25px; 
            margin: 20px 0; 
            border-radius: 12px; 
            border: 1px solid #eee;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            transition: 0.3s;
        }
        .scheme-card:hover { box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .scheme-title { margin-top: 0; color: #0d1b2a; font-size: 1.5rem; }
        .scheme-meta { margin-top: 15px; color: #444; line-height: 1.7; }
        
        .page-title { text-align: center; margin-top: 30px; color: #0d1b2a; }
        .search-box { display: flex; gap: 10px; justify-content: center; margin: 25px auto; max-width: 600px; }
        .search-box input { flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ccc; }
        .search-box button { padding: 12px 18px; background: #1b6ca8; color: white; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<header>
    <h1>SarkarSetu AI</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

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
                    <p><b>Category:</b> <?php echo highlight($row['category'], $search); ?></p>
                    <p><?php echo highlight($row['description'], $search); ?></p>
                    <p><b>Eligibility:</b> <?php echo highlight($row['eligibility'], $search); ?></p>
                    <p><b>Ministry:</b> <?php echo highlight($row['ministry'], $search); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style='text-align:center;'>No schemes found for your criteria.</p>
    <?php endif; ?>

    <div style="text-align:center; margin: 30px 0;">
        <?php 
        $query_string = "search=" . urlencode($search) . "&cat=" . urlencode($category_filter);
        if($page > 1): ?>
            <a href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>">« Prev</a>
        <?php endif; ?>
        <span style="margin: 0 15px;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
        <?php if($page < $total_pages): ?>
            <a href="?<?php echo $query_string; ?>&page=<?php echo $page + 1; ?>">Next »</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>