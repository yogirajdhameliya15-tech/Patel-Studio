<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
include("db.php");
$stmt = $conn->prepare("SELECT username, fullname, email, avatar FROM members WHERE id=?");
$stmt->bind_param("i", $_SESSION['member_id']);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Profile</title><link rel="stylesheet" href="member_style.css"></head>
<body>
<?php include 'member_topbar.php'; include 'member_sidebar.php'; ?>
<div class="layout"><main class="main">
  <h1>Your Profile</h1>
  <div class="profileCard">
    <img class="bigAvatar" src="<?php echo $profile['avatar'] ? 'uploads/'.htmlspecialchars($profile['avatar']) : 'assets/profile_icon.png'; ?>" alt="avatar">
    <div><strong>Username:</strong> <?php echo htmlspecialchars($profile['username']); ?></div>
    <div><strong>Full name:</strong> <?php echo htmlspecialchars($profile['fullname']); ?></div>
    <div><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></div>
    <div style="margin-top:12px;">
      <a class="btn" href="member_edit_profile.php">Edit Profile</a>
      <a class="btn" href="member_upload_photo.php">Upload Photo</a>
    </div>
  </div>
</main></div>
</body></html>
