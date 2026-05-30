<?php
session_start();
include("../includes/config.php");

// 1. Security Check
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Sanitize and validate all inputs
// Using mysqli_real_escape_string to prevent SQL Injection
$age        = (int)$_POST['age'];
$state      = mysqli_real_escape_string($conn, $_POST['state']);
$gender     = mysqli_real_escape_string($conn, $_POST['gender']);
$occupation = mysqli_real_escape_string($conn, $_POST['occupation']);
$income     = mysqli_real_escape_string($conn, $_POST['income']);
$education  = mysqli_real_escape_string($conn, $_POST['education']);

// 3. Check if profile already exists
$check = mysqli_query($conn, "SELECT user_id FROM user_profiles WHERE user_id='$user_id'");

if(mysqli_num_rows($check) > 0){
    // Update existing profile (including the new gender field)
    $sql = "UPDATE user_profiles SET 
            age='$age', 
            state='$state', 
            gender='$gender', 
            occupation='$occupation', 
            income='$income', 
            education='$education' 
            WHERE user_id='$user_id'";
} else {
    // Create new profile (including the new gender field)
    $sql = "INSERT INTO user_profiles 
            (user_id, age, state, gender, occupation, income, education) 
            VALUES 
            ('$user_id', '$age', '$state', '$gender', '$occupation', '$income', '$education')";
}

// 4. Execute and handle errors
if(mysqli_query($conn, $sql)){
    header("Location: dashboard.php");
    exit();
} else {
    // Log the error for debugging
    die("Database Error: " . mysqli_error($conn));
}
?>