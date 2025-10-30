<?php
echo "Database connection : ";
$endpoint = "AAA";
$user = "admin";
$pass = "PasswordMySQL";
$db = "insurance_portal";
$link = mysqli_connect($endpoint,$user,$pass,$db)
or die("**ERROR** " . mysqli_connect_error() . "\n");
echo "SUCCESS\n";
?>
