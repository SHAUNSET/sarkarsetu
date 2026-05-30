<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include("../includes/config.php");

$user_id = $_SESSION['user_id'];

$profile_query = mysqli_query(
    $conn,
    "SELECT * FROM user_profiles WHERE user_id='$user_id'"
);

$profile = mysqli_fetch_assoc($profile_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - SarkarSetu</title>

    <link rel="stylesheet" href="../css/dashboard.css">

</head>
<body>

    <div class="dashboard-container">

        <!-- TOP NAVBAR -->

        <header class="dashboard-navbar">

            <h2>SarkarSetu AI</h2>

            <nav>

                <a href="../index.php">
                    Home
                </a>

                <a href="logout.php">
                    Logout
                </a>

            </nav>

        </header>

        <!-- MAIN -->

        <main class="dashboard-main">

            <div class="welcome-box">

                <h1>
                    Welcome, <?php echo $_SESSION['fullname']; ?>
                </h1>

                <p>
                    Complete your profile to get personalized government scheme recommendations.
                </p>

            </div>

            <!-- PROFILE -->

            <section class="dashboard-section">

                <h2>
                    Profile Information
                </h2>

                <div class="dashboard-card">

                <?php if($profile){ ?>

                    <div class="profile-details">

                        <p>
                            <strong>Age:</strong>
                            <?php echo $profile['age']; ?>
                        </p>

                        <p>
                            <strong>State:</strong>
                            <?php echo $profile['state']; ?>
                        </p>

                        <p>
                            <strong>Occupation:</strong>
                            <?php echo $profile['occupation']; ?>
                        </p>

                        <p>
                            <strong>Income:</strong>
                            <?php echo $profile['income']; ?>
                        </p>

                        <p>
                            <strong>Education:</strong>
                            <?php echo $profile['education']; ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            ✅ Profile Completed
                        </p>

                    </div>

                <?php } else { ?>

                    <form action="save_profile.php" method="POST">

                        <label>Age</label>

                        <input
                            type="number"
                            name="age"
                            required
                        >

                        <label>State</label>

                        <input
                            type="text"
                            name="state"
                            placeholder="Maharashtra"
                            required
                        >

                        <label>Gender</label>
<select name="gender" required>
    <option value="">Select</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Other">Other</option>
</select>

                        <label>Occupation</label>
<select name="occupation" required>
    <option value="">Select</option>
    <option value="Student">Student</option>
    <option value="Farmer">Farmer</option>
    <option value="Teacher">Teacher</option> <option value="Employee">Employee</option>
    <option value="Business">Business</option>
    <option value="Homemaker">Homemaker</option> </select>


                        <label>Income</label>

                        <select name="income" required>
                            <option value="">Select</option>
                            <option value="Below 2 Lakh">Below 2 Lakh</option>
                            <option value="2-5 Lakh">2-5 Lakh</option>
                            <option value="5-10 Lakh">5-10 Lakh</option>
                            <option value="Above 10 Lakh">Above 10 Lakh</option>
                        </select>

                        <label>Education</label>

                        <input
                            type="text"
                            name="education"
                            placeholder="Engineering"
                        >

                        <button type="submit">
                            Save Profile
                        </button>

                    </form>

                <?php } ?>

                </div>

            </section>

            <!-- RECOMMENDATIONS -->

<section class="dashboard-section">
    <h2>Recommended Schemes</h2>
    <div class="dashboard-card">
<?php
if($profile){
    // Sanitize variables
    $occ = mysqli_real_escape_string($conn, $profile['occupation']);
    $inc = mysqli_real_escape_string($conn, $profile['income']);
    $state = mysqli_real_escape_string($conn, $profile['state']);
    $gen = mysqli_real_escape_string($conn, $profile['gender']);

    // The Scoring Engine: Assigns points for every match
    $sql = "SELECT *, 
            ((CASE WHEN (category LIKE '%$occ%' OR description LIKE '%$occ%') THEN 5 ELSE 0 END) +
             (CASE WHEN income_level = '$inc' THEN 3 ELSE 0 END) +
             (CASE WHEN (description LIKE '%$state%' OR description LIKE '%All India%') THEN 2 ELSE 0 END) +
             (CASE WHEN (description LIKE '%$gen%' OR description LIKE '%General%') THEN 1 ELSE 0 END)) AS match_score
            FROM schemes 
            HAVING match_score > 0
            ORDER BY match_score DESC 
            LIMIT 10";
            
    $rec_query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($rec_query) > 0) {
        while($rec = mysqli_fetch_assoc($rec_query)) {
            echo "<div style='margin-bottom:15px; border-bottom:1px solid #ddd; padding-bottom:10px;'>";
            echo "<strong>" . htmlspecialchars($rec['title']) . "</strong> (Score: " . $rec['match_score'] . ")<br>";
            echo "<small>" . htmlspecialchars(substr($rec['description'], 0, 80)) . "...</small><br>";
            echo "<a href='scheme_details.php?id=" . $rec['id'] . "' style='color:blue;'>View Details</a>";
            echo "</div>";
        }
    } else {
        echo "No specific matches found.";
    }
}
?>
    </div>
</section>

            <!-- SAVED -->

            <section class="dashboard-section">

                <h2>
                    Saved Schemes
                </h2>

                <div class="dashboard-card">

                    Schemes you bookmark will appear here.

                </div>

            </section>

        </main>

    </div>

</body>
</html>