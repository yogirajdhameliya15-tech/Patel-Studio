<?php
session_start();
include("db.php");

// check required fields
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    die("Invalid submission");
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// prepare & fetch by USERNAME
$stmt = $conn->prepare("SELECT id, username, password FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {

    $row = $res->fetch_assoc();
    $dbpass = $row['password'];

    // verify password (hashed or plain)
    if (password_verify($password, $dbpass) || $password === $dbpass) {

        // save session
        $_SESSION['member'] = $row['username'];
        $_SESSION['member_id'] = $row['id'];

        // insert login log
        $ins = $conn->prepare("INSERT INTO login_log (member_id, login_time) VALUES (?, NOW())");
        $ins->bind_param("i", $row['id']);
        $ins->execute();

        // redirect
        header("Location: member_home.php");
        exit;

    } else {
        echo "<h3 style='color:red;text-align:center;'>Incorrect password</h3>";
    }

} else {
    echo "<h3 style='color:red;text-align:center;'>Username not found</h3>";
}
?>
