<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: member_login.php");
    exit;
}

include("db.php");

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_FILES['photo']['name'])) {

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $newName = "member_" . $member_id . "_" . time() . "." . $ext;

        $uploadPath = "uploads/" . $newName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {

            // update DB photo field
            $stmt = $conn->prepare("UPDATE members SET photo=? WHERE id=?");
            $stmt->bind_param("si", $newName, $member_id);
            $stmt->execute();

            echo "<script>alert('Profile photo updated');location='member_home.php';</script>";
            exit;
        } else {
            echo "<script>alert('Upload error');</script>";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload Photo — Patel Studio</title>

<style>
body{
    margin:0;
    background:#021024;
    font-family:Arial;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    width:400px;
    padding:20px;
    background:rgba(255,255,255,0.06);
    border-radius:12px;
    text-align:center;
}
input[type=file]{
    margin:12px 0;
    width:100%;
    padding:10px;
    background:rgba(255,255,255,0.1);
    border:1px solid #00eaff;
    border-radius:8px;
    color:#fff;
}
button{
    padding:12px;
    width:100%;
    background:#00eaff;
    border:none;
    border-radius:8px;
    font-weight:700;
    cursor:pointer;
    color:#001;
}
.back{color:#00eaff;text-decoration:none;display:block;margin-top:12px}
</style>

</head>
<body>

<div class="box">
    <h2>Upload Profile Photo</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="photo" required>
        <button type="submit">Upload</button>
    </form>
    <a class="back" href="member_home.php">← Back</a>
</div>

</body>
</html>
