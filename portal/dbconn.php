<?php
echo "Database connection : ";
$endpoint = "AAA";
$user = "admin";
$pass = "PasswordMySQL";
$db = "mysql";
$link = mysqli_connect($endpoint,$user,$pass,$db)
or die("**ERROR** " . mysqli_connect_error() . "\n");
echo "SUCCESS\n";
?>
