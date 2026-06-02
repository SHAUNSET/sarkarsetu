<?php
session_start();
require_once 'includes/config.php';
$today = date('Y-m-d');

// 1. LAZY REFRESH LOGIC
// Check if the table is empty or if the date in the table is NOT today
$check_date = mysqli_query($conn, "SELECT target_date FROM daily_targets LIMIT 1");
$last_entry = mysqli_fetch_assoc($check_date);

if (!$last_entry || $last_entry['target_date'] !== $today) {
    // Truncate existing data
    mysqli_query($conn, "TRUNCATE TABLE daily_targets");
    
    // FETCH PRIORITY ANCHORS
    $anchors = mysqli_query($conn, "SELECT title, description FROM schemes WHERE category = 'Important' LIMIT 5");
    while ($s = mysqli_fetch_assoc($anchors)) {
        $name = mysqli_real_escape_string($conn, $s['title']);
        $desc = mysqli_real_escape_string($conn, $s['description']);
        mysqli_query($conn, "INSERT INTO daily_targets (scheme_name, description, type, target_date, relevance_score) 
                  VALUES ('$name', '$desc', 'PRIORITY', '$today', 5)");
    }

    // SMART KEYWORD MATCHING
    $themes = file('ml_engine/pyq_themes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($themes as $theme) {
        $theme = mysqli_real_escape_string($conn, trim($theme));
        $query = "SELECT title, description FROM schemes 
                  WHERE (title LIKE '%$theme%' OR description LIKE '%$theme%') 
                  AND title NOT IN (SELECT scheme_name FROM daily_targets) LIMIT 2";
        $result = mysqli_query($conn, $query);
        while ($s = mysqli_fetch_assoc($result)) {
            $name = mysqli_real_escape_string($conn, $s['title']);
            $desc = mysqli_real_escape_string($conn, $s['description']);
            mysqli_query($conn, "INSERT INTO daily_targets (scheme_name, description, type, target_date, relevance_score) 
                      VALUES ('$name', '$desc', 'PYQ_MATCH', '$today', 3)");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Daily Study Targets - SarkarSetu AI</title>
</head>
<body>

<header>
    <h1>SarkarSetu AI</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="auth/dashboard.php">Dashboard</a>
        <a href="auth/logout.php">Logout</a>
    </nav>
</header>

<main>
    <section class="ai-box">
        <h2 style="color: #ffffff;">Daily Study Targets: <?php echo $today; ?></h2>
    </section>

    <section style="margin-top: 30px;">
        <table border="0" class="update-card" style="width: 100%; border-collapse: collapse;">
            <?php
            $targets = mysqli_query($conn, "SELECT * FROM daily_targets ORDER BY relevance_score DESC");
            while ($row = mysqli_fetch_assoc($targets)) {
                echo "<tr style='border-bottom: 1px solid #ddd;'>
                        <td style='padding: 20px;'>
                            <h3>{$row['scheme_name']}</h3>
                            <p style='color: #666;'>".htmlspecialchars(substr($row['description'], 0, 150))."...</p>
                            <span class='update-tag'>{$row['type']}</span>
                        </td>
                        <td style='padding: 20px; text-align: right;'>
                            <form action='ask_scheme_ai.php' method='GET'>
                                <input type='hidden' name='scheme' value='{$row['scheme_name']}'>
                                <button type='submit'>Ask AI</button>
                            </form>
                        </td>
                      </tr>";
            }
            ?>
        </table>
    </section>
</main>
</body>
</html>