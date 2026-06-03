<?php
session_start();
require_once 'includes/config.php';
$today = date('Y-m-d');

// 1. REFRESH LOGIC
$check_date = mysqli_query($conn, "SELECT target_date FROM daily_targets LIMIT 1");
$last_entry = mysqli_fetch_assoc($check_date);

if (!$last_entry || $last_entry['target_date'] !== $today) {
    mysqli_query($conn, "TRUNCATE TABLE daily_targets");
    
    // FETCH 5 UNIQUE SCHEMES: Exclude anything that has EVER been seen
    $query = "SELECT id, title, description, 'GENERAL' as type FROM schemes 
              WHERE id NOT IN (SELECT scheme_id FROM target_history) 
              ORDER BY RAND() LIMIT 5";
    
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $sid = $row['id'];
        $name = mysqli_real_escape_string($conn, $row['title']);
        $desc = mysqli_real_escape_string($conn, $row['description']);
        
        mysqli_query($conn, "INSERT INTO daily_targets (scheme_name, description, type, target_date, relevance_score) 
                  VALUES ('$name', '$desc', '{$row['type']}', '$today', 5)");
        
        // Log to history immediately to prevent future repetition
        mysqli_query($conn, "INSERT INTO target_history (scheme_id, date_seen) VALUES ($sid, '$today')");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        $id_lookup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM schemes WHERE title = '".mysqli_real_escape_string($conn, $row['scheme_name'])."' LIMIT 1"));
        $s_id = $id_lookup['id'] ?? 0;

        echo "<tr style='border-bottom: 1px solid #ddd;'>
                <td style='padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px;'>
                    <div style='flex: 1;'>
                        <h3 style='margin: 0 0 10px 0;'>{$row['scheme_name']}</h3>
                        <p style='color: #666; margin: 0 0 10px 0;'>".htmlspecialchars(substr($row['description'], 0, 150))."...</p>
                        <span class='update-tag'>{$row['type']}</span>
                    </div>
                    <div style='flex-shrink: 0;'>
                        <a href='auth/scheme_details.php?id={$s_id}' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; white-space: nowrap;'>Ask AI</a>
                    </div>
                </td>
              </tr>";
    }
    ?>
</table>
    </section>
</main>
</body>
</html>