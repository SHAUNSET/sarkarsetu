<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
}

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

            <!-- PROFILE STATUS -->

            <section class="dashboard-section">

                <h2>
                    Profile Completion
                </h2>

                <div class="dashboard-card">

                    Your profile is incomplete.

                    Add details like age, occupation, income and location
                    for AI-powered recommendations.

                </div>

            </section>

            <!-- RECOMMENDATIONS -->

            <section class="dashboard-section">

                <h2>
                    Recommended Schemes
                </h2>

                <div class="dashboard-card">

                    Personalized recommendations will appear here.

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