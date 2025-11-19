<?php
session_start();
if (!isset($_SESSION['admin_verify'])) {
    header("Location: login.php");
    exit;
}

include("../db.php");

// -------------------- GET MEMBER DATA --------------------
if (!isset($_GET['id'])) {
    header("Location: manage_members.php");
    exit;
}

$member_id = intval($_GET['id']);

$q = $conn->prepare("SELECT * FROM members WHERE id=?");
$q->bind_param("i", $member_id);
$q->execute();
$res = $q->get_result();

if ($res->num_rows === 0) {
    echo "<h2>Member not found</h2>";
    exit;
}

$data = $res->fetch_assoc();

// -------------------- UPDATE MEMBER --------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // optional

    if ($password === "") {
        $u = $conn->prepare("UPDATE members SET fullname=?, email=?, username=? WHERE id=?");
        $u->bind_param("sssi", $fullname, $email, $username, $member_id);
    } else {
        $u = $conn->prepare("UPDATE members SET fullname=?, email=?, username=?, password=? WHERE id=?");
        $u->bind_param("ssssi", $fullname, $email, $username, $password, $member_id);
    }

    if ($u->execute()) {
        header("Location: manage_members.php?msg=updated");
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Update failed!</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Member — Patel Studio</title>
<link rel="stylesheet" href="../assets/css/edit_member.css">
</head>

<body>

<div class="edit-container">

    <div class="header">
        <h2>Edit Member</h2>
        <a href="manage_members.php" class="back">← Back</a>
    </div>

    <form method="POST" class="edit-form">

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($data['fullname']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>

        <label>Password (leave blank to keep same)</label>
        <input type="password" name="password" placeholder="Enter new password">

        <button type="submit" class="btn">Update Member</button>

    </form>

</div>

</body>
</html>
