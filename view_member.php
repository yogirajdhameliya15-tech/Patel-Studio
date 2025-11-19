<?php
// view_member.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
require_once "db.php";

if (!isset($_GET['id'])) {
    header("Location: manage_members.php");
    exit;
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
if (!$member) die("Member not found");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>View Member</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
  <h1>Member Details</h1>

  <div class="card">
    <div style="display:flex;gap:20px;align-items:center">
      <?php if (!empty($member['photo']) && file_exists(__DIR__."/uploads/".$member['photo'])): ?>
        <img src="uploads/<?=htmlspecialchars($member['photo']);?>" style="width:120px;border-radius:10px">
      <?php endif; ?>
      <div>
        <p><strong>Username:</strong> <?=htmlspecialchars($member['username']);?></p>
        <p><strong>Full name:</strong> <?=htmlspecialchars($member['fullname']);?></p>
        <p><strong>Email:</strong> <?=htmlspecialchars($member['email']);?></p>
        <p><strong>Mobile:</strong> <?=htmlspecialchars($member['mobile']);?></p>
        <p><strong>Created:</strong> <?=htmlspecialchars($member['created_at']);?></p>
        <p><strong>Plain password (admin only):</strong> <span style="color:#d33;font-weight:700"><?=htmlspecialchars($member['password_plain']);?></span></p>
      </div>
    </div>

    <p style="margin-top:12px">
      <a class="btn" href="edit_member.php?id=<?=$member['id']?>">Edit</a>
      <a class="btn ghost" href="manage_members.php">Back</a>
    </p>
  </div>
</div>

</body>
</html>
