<?php
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Correct Admin Credentials
    if ($user === 'admin' && $pass === '@dmin12345') {

        // Admin logged in → move to verification page
        $_SESSION['admin'] = true;
        header("Location: admin_verify.php");
        exit;

    } else {
        $error = "❌ Invalid Admin Login!";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Login — Patel Studio</title>
<link rel="stylesheet" href="admin_login.css">
</head>

<body>

<div class="login-box">

  <div class="logo-frame">
      <img src="assets/logo.png" alt="Patel Studio">
  </div>

  <h2>Admin Login</h2>

  <form method="post">
      <input name="username" placeholder="Admin Username" required>
      <input name="password" placeholder="Password" type="password" required>
      <button>Login</button>
  </form>

  <?php if($error) echo "<p class='error'>$error</p>"; ?>

</div>

</body>
</html>
