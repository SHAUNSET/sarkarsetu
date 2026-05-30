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

                        <label>Occupation</label>

                        <select name="occupation" required>
                            <option value="">Select</option>
                            <option value="Student">Student</option>
                            <option value="Farmer">Farmer</option>
                            <option value="Employee">Employee</option>
                            <option value="Business">Business</option>
                        </select>

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

                <h2>
                    Recommended Schemes
                </h2>

                <div class="dashboard-card">

                    <?php

                    if($profile){

                        echo "Personalized recommendations coming next.";

                    }else{

                        echo "Complete your profile to get recommendations.";

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