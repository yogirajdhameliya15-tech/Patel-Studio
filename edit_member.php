<?php
// edit_member.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once "db.php"; // expects $conn = new mysqli(...)

if (!isset($_GET['id'])) {
    header("Location: manage_members.php");
    exit;
}
$id = intval($_GET['id']);

// fetch current member
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Member not found");
}
$member = $res->fetch_assoc();

// handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? $member['username']);
    $fullname = trim($_POST['fullname'] ?? $member['fullname']);
    $email    = trim($_POST['email'] ?? $member['email']);
    $mobile   = trim($_POST['mobile'] ?? $member['mobile']);
    $password = $_POST['password'] ?? ''; // if provided -> update

    // file upload
    if (!empty($_FILES['photo']['name'])) {
        $uploaddir = __DIR__ . "/uploads/";
        if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $newname = "member_{$id}_" . time() . "." . $ext;
        $target = $uploaddir . $newname;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            // delete old file if exists
            if (!empty($member['photo']) && file_exists($uploaddir . $member['photo'])) {
                @unlink($uploaddir . $member['photo']);
            }
            $photo_to_save = $newname;
        } else {
            $photo_to_save = $member['photo'];
        }
    } else {
        $photo_to_save = $member['photo'];
    }

    if ($password !== '') {
        // update both hashed and plain (plain is insecure — for admin viewing only)
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $u = $conn->prepare("UPDATE members SET username=?, fullname=?, email=?, mobile=?, photo=?, password_hash=?, password_plain=? WHERE id=?");
        $u->bind_param("sssssssi", $username, $fullname, $email, $mobile, $photo_to_save, $hashed, $password, $id);
    } else {
        $u = $conn->prepare("UPDATE members SET username=?, fullname=?, email=?, mobile=?, photo=? WHERE id=?");
        $u->bind_param("sssssi", $username, $fullname, $email, $mobile, $photo_to_save, $id);
    }

    if ($u->execute()) {
        header("Location: manage_members.php?msg=updated");
        exit;
    } else {
        $error = "Update failed: " . $conn->error;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Member — Patel Studio</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
  <h1>Edit Member</h1>

  <?php if (!empty($error)) echo "<p class='alert error'>".htmlspecialchars($error)."</p>"; ?>

  <div class="card">
    <form method="post" enctype="multipart/form-data">
      <label>Username</label>
      <input type="text" name="username" value="<?=htmlspecialchars($member['username']);?>" required>

      <label>Full name</label>
      <input type="text" name="fullname" value="<?=htmlspecialchars($member['fullname']);?>">

      <label>Email</label>
      <input type="email" name="email" value="<?=htmlspecialchars($member['email']);?>">

      <!-- <label>Mobile</label>
      <input type="text" name="mobile" value="<?=htmlspecialchars($member['mobile']);?>"> -->

      <label>New Password (leave blank to keep old)</label>
      <input type="password" name="password" placeholder="New password">

      <label>Profile Photo (optional)</label>
      <input type="file" name="photo" accept="image/*">

      <?php if(!empty($member['photo']) && file_exists(__DIR__."/uploads/".$member['photo'])): ?>
        <p>Current:</p>
        <img src="uploads/<?=htmlspecialchars($member['photo']);?>" alt="photo" style="width:100px;border-radius:8px;border:2px solid #00eaff">
      <?php endif; ?>

      <button type="submit" class="btn">Update Member</button>
      <a class="btn ghost" href="manage_members.php">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>
