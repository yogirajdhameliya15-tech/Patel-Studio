<?php
// upload_photo.php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: member_login.php");
    exit;
}
require_once "db.php";

$member_id = intval($_SESSION['member_id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['photo']['name'])) {

    $uploaddir = __DIR__ . "/uploads/";
    if (!is_dir($uploaddir)) mkdir($uploaddir,0755,true);
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $newname = "member_{$member_id}_" . time() . "." . $ext;
    $target = $uploaddir . $newname;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        // remove old
        $q = $conn->prepare("SELECT photo FROM members WHERE id=?");
        $q->bind_param("i",$member_id); $q->execute(); $r=$q->get_result()->fetch_assoc();
        if (!empty($r['photo']) && file_exists($uploaddir.$r['photo'])) @unlink($uploaddir.$r['photo']);

        $u = $conn->prepare("UPDATE members SET photo = ? WHERE id = ?");
        $u->bind_param("si", $newname, $member_id);
        $u->execute();
        header("Location: member_home.php?msg=photo_updated");
        exit;
    } else {
        $error = "Upload failed";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload Photo</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'member_topbar.php'; ?>

<div class="main">
  <h1>Upload Profile Photo</h1>

  <?php if (!empty($error)) echo "<p class='alert error'>".htmlspecialchars($error)."</p>"; ?>

  <div class="card">
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="photo" accept="image/*" required>
      <button class="btn" type="submit">Upload</button>
    </form>
    <a class="btn ghost" href="member_home.php">Cancel</a>
  </div>
</div>

</body>
</html>
