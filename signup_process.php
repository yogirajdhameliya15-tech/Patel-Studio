<?php
include("db.php");

// basic validation
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');

if ($username === '' || $password === '') {
    die("Fill required fields");
}

// hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert user
$stmt = $conn->prepare("INSERT INTO members (username, password, fullname, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hash, $fullname, $email);

if ($stmt->execute()) {
    echo "<script>alert('Account created successfully!'); window.location='member_login.php';</script>";
    exit;
} else {
    die('Database Error: ' . $conn->error);
}
?>
