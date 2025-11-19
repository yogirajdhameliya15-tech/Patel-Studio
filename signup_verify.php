<?php
// Get form data from signup page
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');

// if missing important data → block
if ($username === '' || $password === '') {
    die("Required fields missing!");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify Details — Patel Studio</title>
<style>
body{font-family:Arial;background:#021024;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;color:#fff}
.box{background:#04162f;padding:25px;width:420px;border-radius:14px;box-shadow:0 0 20px rgba(0,255,255,0.2)}
h2{text-align:center;color:#00eaff}
table{width:100%;font-size:16px;margin-bottom:20px}
td{padding:6px 0}
button{width:100%;padding:12px;border:none;border-radius:8px;background:#00eaff;font-weight:700;font-size:16px;color:#001;cursor:pointer}
</style>
</head>
<body>

<div class="box">
    <h2>Verify Your Details</h2>

    <table>
        <tr><td><b>Username:</b></td><td><?= htmlspecialchars($username) ?></td></tr>
        <tr><td><b>Password:</b></td><td><?= htmlspecialchars($password) ?></td></tr>
        <tr><td><b>Full Name:</b></td><td><?= htmlspecialchars($fullname) ?></td></tr>
        <tr><td><b>Email:</b></td><td><?= htmlspecialchars($email) ?></td></tr>
    </table>

    <form method="POST" action="signup_process.php">
        <!-- Hidden fields to send again -->
        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
        <input type="hidden" name="password" value="<?= htmlspecialchars($password) ?>">
        <input type="hidden" name="fullname" value="<?= htmlspecialchars($fullname) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

        <button type="submit">Confirm & Create Account</button>
    </form>
</div>

</body>
</html>
