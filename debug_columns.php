<?php
require_once 'includes/config.php';

// This will list every single column name in your 'schemes' table
$res = mysqli_query($conn, "SHOW COLUMNS FROM schemes");
echo "<h3>Found these columns in 'schemes':</h3><ul>";
while ($row = mysqli_fetch_assoc($res)) {
    echo "<li>" . $row['Field'] . "</li>";
}
echo "</ul>";
?>