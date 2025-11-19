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

// Ensure uploads/member_gallery exists
$gallery_folder = "uploads/gallery/";
if (!is_dir($gallery_folder)) {
    mkdir($gallery_folder, 0777, true);
}

// ==================================
// UPLOAD IMAGE
// ==================================
if (isset($_POST['upload'])) {

    if (!empty($_FILES['photo']['name'])) {

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $newname = "g_" . $member_id . "_" . time() . "." . $ext;
        $target = $gallery_folder . $newname;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            
            $stmt = $conn->prepare("INSERT INTO member_gallery (member_id, photo) VALUES (?, ?)");
            $stmt->bind_param("is", $member_id, $newname);
            $stmt->execute();

            $message = "<p class='success'>✔ Photo uploaded!</p>";
        } else {
            $message = "<p class='error'>❌ Upload failed!</p>";
        }
    }
}

// ==================================
// DELETE PHOTO
// ==================================
if (isset($_GET['delete'])) {
    $photo_id = intval($_GET['delete']);

    $q = $conn->prepare("SELECT photo FROM member_gallery WHERE id=? AND member_id=? LIMIT 1");
    $q->bind_param("ii", $photo_id, $member_id);
    $q->execute();
    $res = $q->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file = $gallery_folder . $row['photo'];

        if (file_exists($file)) unlink($file);

        $del = $conn->prepare("DELETE FROM member_gallery WHERE id=?");
        $del->bind_param("i", $photo_id);
        $del->execute();

        $message = "<p class='success'>✔ Photo deleted!</p>";
    }
}

// Fetch gallery items
$photos = $conn->query("SELECT * FROM member_gallery WHERE member_id='$member_id' ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
<title>Member Gallery — Patel Studio</title>
<meta charset="utf-8">

<style>
body{
    margin:0;
    font-family:Arial;
    background:#021024;
    color:#fff;
}

.top{
    background:#001a33;
    padding:20px;
    text-align:center;
    box-shadow:0 0 20px rgba(0,255,255,0.2);
}

.top h2{
    color:#00eaff;
    margin:0;
}

.container{
    width:90%;
    max-width:1100px;
    margin:20px auto;
    text-align:center;
}

form{
    margin-bottom:20px;
}

input[type=file]{
    padding:10px;
    border-radius:6px;
    background:#062445;
    border:1px solid #00eaff;
    color:#fff;
}

button{
    padding:10px 20px;
    border:none;
    background:#00eaff;
    color:#001;
    font-weight:bold;
    border-radius:6px;
    cursor:pointer;
    margin-left:10px;
}

button:hover{
    background:#00c2d6;
}

.gallery{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));
    gap:18px;
    margin-top:20px;
}

.item{
    background:#062445;
    padding:10px;
    border-radius:10px;
    box-shadow:0 0 15px rgba(0,255,255,0.2);
}

.item img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:8px;
}

.delete-btn{
    display:block;
    margin-top:10px;
    padding:8px;
    background:#ff0033;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
}

.delete-btn:hover{
    background:#d6002b;
}

.success{
    background:#00cc66;
    padding:10px;
    border-radius:6px;
}

.error{
    background:#ff0033;
    padding:10px;
    border-radius:6px;
}

.back{
    margin-top:20px;
    display:inline-block;
    text-decoration:none;
    color:#00eaff;
}
</style>

</head>
<body>

<div class="top">
    <h2>📸 Member Gallery</h2>
</div>

<div class="container">

    <?= $message; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="photo" required>
        <button type="submit" name="upload">Upload Photo</button>
    </form>

    <div class="gallery">
        <?php while ($row = $photos->fetch_assoc()): ?>
            <div class="item">
                <img src="uploads/gallery/<?= $row['photo']; ?>">
                <a class="delete-btn" href="?delete=<?= $row['id']; ?>" onclick="return confirm('Delete photo?');">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>

    <a href="member_home.php" class="back">← Back to Member Home</a>

</div>

</body>
</html>
