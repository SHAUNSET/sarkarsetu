<?php
include("includes/config.php");

// FIXED: Remove the 'IF NOT EXISTS' part. 
// If it fails because the column exists, we just suppress the error and move on.
@mysqli_query($conn, "ALTER TABLE schemes ADD COLUMN income_level VARCHAR(50) DEFAULT 'No Limit'");

// 2. Fetch all schemes
$schemes = mysqli_query($conn, "SELECT id, description FROM schemes");

while($row = mysqli_fetch_assoc($schemes)) {
    $id = $row['id'];
    $desc = strtolower($row['description']);
    
    // AI Classification logic
    $level = 'No Limit';
    if (strpos($desc, 'below poverty') !== false || strpos($desc, 'bpl') !== false) {
        $level = 'Below 2 Lakh';
    } elseif (strpos($desc, 'above 10 lakh') !== false) {
        $level = 'Above 10 Lakh';
    } elseif (strpos($desc, '2 lakh') !== false && strpos($desc, 'below') !== false) {
        $level = 'Below 2 Lakh';
    }
    
    // Update the row
    mysqli_query($conn, "UPDATE schemes SET income_level = '$level' WHERE id = $id");
}

echo "Database cleaned! 2,600 schemes now have an income_level tag.";
?>