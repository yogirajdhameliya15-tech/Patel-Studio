<?php
session_start();
require_once "db.php";

// User must login
if (!isset($_SESSION['member_id'])) {
    header("Location: member_login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$message = "";

// Fetch current member data
$stmt = $conn->prepare("SELECT * FROM members WHERE id=? LIMIT 1");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$res = $stmt->get_result();
$member = $res->fetch_assoc();

// ------------------------------------
// UPDATE PROFILE PROCESS
// ------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);

    $photo_to_save = $member['photo'];

    // IMAGE Upload
    if (!empty($_FILES['photo']['name'])) {
        $folder = "uploads/";
        if (!is_dir($folder)) mkdir($folder);

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $newfile = "member_".$member_id."_".time().".".$ext;
        $target = $folder . $newfile;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {

            // delete old photo
            if (!empty($member['photo']) && file_exists($folder.$member['photo'])) {
                unlink($folder.$member['photo']);
            }

            $photo_to_save = $newfile;
        }
    }

    // update query
    $update = $conn->prepare("UPDATE members SET username=?, fullname=?, email=?, mobile=?, photo=? WHERE id=?");
    $update->bind_param("sssssi", $username, $fullname, $email, $mobile, $photo_to_save, $member_id);

    if ($update->execute()) {
        $message = "<p class='success'>✔ Profile updated successfully!</p>";

        // Refresh updated data
        $stmt = $conn->prepare("SELECT * FROM members WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $member = $stmt->get_result()->fetch_assoc();

    } else {
        $message = "<p class='error'>❌ Update failed: ".$conn->error."</p>";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Profile — Patel Studio</title>

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
    width:420px;
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
    margin-top:12px;
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
    margin-top:18px;
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

.photo-box{
    text-align:center;
    margin-top:10px;
}

.photo-box img{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #00eaff;
}

.back{
    display:block;
    text-align:center;
    margin-top:12px;
    color:#00eaff;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="box">
    <h2>Edit Profile</h2>

    <?= $message; ?>

    <div class="photo-box">
        <?php if (!empty($member['photo'])): ?>
            <img src="uploads/<?= $member['photo']; ?>" alt="Profile">
        <?php else: ?>
            <img src="assets/default.png" alt="No Photo">
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($member['username']); ?>" required>

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($member['fullname']); ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($member['email']); ?>">

        <!-- <label>Mobile</label>
        <input type="text" name="mobile" value="<?= htmlspecialchars($member['mobile']); ?>"> -->

        <label>Change Profile Photo</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit">Update Profile</button>
    </form>

    <a href="member_home.php" class="back">← Back to Member Home</a>
</div>

</body>
</html>
