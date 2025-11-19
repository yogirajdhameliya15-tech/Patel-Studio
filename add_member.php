<?php
// add_member.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username and password required";
    } else {
        // handle photo
        $photo_name = '';
        if (!empty($_FILES['photo']['name'])) {
            $uploaddir = __DIR__ . "/uploads/";
            if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $photo_name = "member_" . time() . "." . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploaddir . $photo_name);
        }

        // store both hashed and plain (plain insecure)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO members (username, fullname, email, mobile, photo, password_hash, password_plain) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss", $username, $fullname, $email, $mobile, $photo_name, $hash, $password);

        if ($stmt->execute()) {
            header("Location: manage_members.php?msg=added");
            exit;
        } else {
            $error = "DB error: ".$conn->error;
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Member</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
  <h1>Add Member</h1>

  <?php if (!empty($error)) echo "<p class='alert error'>".htmlspecialchars($error)."</p>"; ?>

  <div class="card">
    <form method="post" enctype="multipart/form-data">
      <label>Username</label>
      <input name="username" required>

      <label>Full name</label>
      <input name="fullname">

      <label>Email</label>
      <input name="email" type="email">

      <label>Mobile</label>
      <input name="mobile">

      <label>Password</label>
      <input name="password" type="password" required>

      <label>Photo</label>
      <input type="file" name="photo" accept="image/*">

      <button class="btn" type="submit">Create Member</button>
      <a class="btn ghost" href="manage_members.php">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>
