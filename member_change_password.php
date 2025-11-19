<?php
session_start();
require_once "db.php";

// user must login
if (!isset($_SESSION['member_id'])) {
    header("Location: member_login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$message = "";

// fetch stored password
$stmt = $conn->prepare("SELECT password_plain, password_hash FROM members WHERE id=? LIMIT 1");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

// ------------------------------------
// SUBMIT PASSWORD CHANGE
// ------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "<p class='error'>❌ New passwords do not match!</p>";
    } else if (!password_verify($old, $user['password_hash']) && $old !== $user['password_plain']) {
        $message = "<p class='error'>❌ Old password is incorrect!</p>";
    } else {
        // update password
        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE members SET password_hash=?, password_plain=? WHERE id=?");
        $update->bind_param("ssi", $new_hash, $new, $member_id);
        $update->execute();

        $message = "<p class='success'>✔ Password updated successfully!</p>";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Change Password — Patel Studio</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#021024;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    width:360px;
    background:#062445;
    padding:25px;
    border-radius:12px;
    box-shadow:0 0 20px rgba(0,255,255,0.25);
}

h2{
    text-align:center;
    margin-top:0;
    color:#00eaff;
}

label{
    font-size:14px;
    display:block;
    margin-top:15px;
}

input{
    width:100%;
    padding:12px;
    margin-top:5px;
    border-radius:6px;
    border:1px solid #00eaff;
    background:rgba(255,255,255,0.05);
    color:#fff;
}

button{
    width:100%;
    padding:12px;
    margin-top:20px;
    background:#00eaff;
    color:#001;
    font-weight:700;
    border:none;
    border-radius:6px;
    cursor:pointer;
    transition:0.2s;
}

button:hover{
    background:#00c2d6;
}

.success{
    background:#00cc66;
    padding:10px;
    border-radius:5px;
    text-align:center;
}

.error{
    background:#ff0033;
    padding:10px;
    border-radius:5px;
    text-align:center;
}

.back-link{
    display:block;
    text-align:center;
    color:#00eaff;
    margin-top:15px;
    text-decoration:none;
}
</style>

</head>
<body>

<div class="box">
    <h2>Change Password</h2>

    <?= $message; ?>

    <form method="POST">

        <label>Old Password</label>
        <input type="password" name="old_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    </form>

    <a href="member_home.php" class="back-link">← Back to Home</a>
</div>

</body>
</html>
