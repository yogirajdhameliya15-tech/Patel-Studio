<?php
session_start();

// Must be logged in as admin before verify
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['verify_id'] ?? '';
    $pw = $_POST['verify_pw'] ?? '';

    // Correct Admin Secondary Verification
    if ($id === 'psadmin' && $pw === 'PS@dmin') {

        $_SESSION['admin_verify'] = true;

        // Redirect to Admin Dashboard
        header("Location: admin_home.php");
        exit;

    } else {
        $error = "❌ Verification Failed!";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Verification — Patel Studio</title>
<link rel="stylesheet" href="admin_verify.css">
</head>

<body>

<div class="verify-box">

  <div class="logo-frame">
      <img src="assets/logo.png" alt="Patel Studio">
  </div>

  <h2>Admin Verification</h2>
  <p style="color:#666;">Enter secondary admin credentials</p>

  <form method="post">
      <input name="verify_id" placeholder="Verification ID" required>
      <input name="verify_pw" placeholder="Verification Password" type="password" required>
      <button>Verify</button>
  </form>

  <?php if($error) echo "<p class='error'>$error</p>"; ?>

</div>

</body>
</html>
