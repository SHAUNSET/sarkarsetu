<?php

session_start();
include("../includes/config.php");

// User must be logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$age = $_POST['age'];
$state = $_POST['state'];
$occupation = $_POST['occupation'];
$income = $_POST['income'];
$education = $_POST['education'];

// Check if profile already exists
$check = mysqli_query(
    $conn,
    "SELECT * FROM user_profiles WHERE user_id='$user_id'"
);

if(mysqli_num_rows($check) > 0){

    // Update existing profile
    $sql = "UPDATE user_profiles SET

            age='$age',
            state='$state',
            occupation='$occupation',
            income='$income',
            education='$education'

            WHERE user_id='$user_id'";

}else{

    // Create new profile
    $sql = "INSERT INTO user_profiles
            (user_id, age, state, occupation, income, education)

            VALUES

            ('$user_id',
             '$age',
             '$state',
             '$occupation',
             '$income',
             '$education')";
}

if(mysqli_query($conn, $sql)){

    header("Location: dashboard.php");
    exit();

}else{

    echo "Error: " . mysqli_error($conn);
}

?>