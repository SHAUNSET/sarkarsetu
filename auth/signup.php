<?php

session_start();

include("../includes/config.php");

if(isset($_POST['signup'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkEmail);

    if(mysqli_num_rows($result) > 0){

        $error = "Email already exists";

    }else{

        $sql = "INSERT INTO users(fullname, email, password)
                VALUES('$fullname', '$email', '$hashedPassword')";

        if(mysqli_query($conn, $sql)){

            $userId = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $userId;
            $_SESSION['fullname'] = $fullname;

            header("Location: ../index.php");
            exit();

        }else{

            $error = "Something went wrong";
        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Signup - SarkarSetu</title>

    <link rel="stylesheet" href="../css/auth.css">

</head>
<body>

    <div class="auth-container">

        <div class="auth-box">

            <h1>SarkarSetu AI</h1>

            <h2>Create Account</h2>

            <?php
                if(isset($error)){
                    echo "<p class='error'>$error</p>";
                }
            ?>

            <form method="POST">

                <input 
                    type="text"
                    name="fullname"
                    placeholder="Full Name"
                    required
                >

                <input 
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                >

                <input 
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >

                <button type="submit" name="signup">
                    Signup
                </button>

            </form>

            <p class="switch-link">
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>

    </div>

</body>
</html>