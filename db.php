<?php
// db.php
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP default
$db   = "patel_studio_member";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
