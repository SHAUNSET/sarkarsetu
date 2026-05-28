<?php

session_start();

include("../includes/config.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];

            header("Location: ../index.php");
            exit();

        }else{

            $error = "Incorrect Password";
        }

    }else{

        $error = "User Not Found";
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - SarkarSetu</title>

    <link rel="stylesheet" href="../css/auth.css">

</head>
<body>

    <div class="auth-container">

        <div class="auth-box">

            <h1>SarkarSetu AI</h1>

            <h2>Login</h2>

            <?php
                if(isset($error)){
                    echo "<p class='error'>$error</p>";
                }
            ?>

            <form method="POST">

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

                <button type="submit" name="login">
                    Login
                </button>

            </form>

            <p class="switch-link">
                Don’t have an account?
                <a href="signup.php">Signup</a>
            </p>

        </div>

    </div>

</body>
</html>