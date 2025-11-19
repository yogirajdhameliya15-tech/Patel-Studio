<?php
session_start();

// ADMIN CHECK
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "patel_studio_member");

if ($conn->connect_error) {
    die("Database Connection Failed!");
}

// DELETE MEMBER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete old photo
    $photo_q = $conn->query("SELECT photo FROM members WHERE id=$id");
    if ($photo_q && $photo_q->num_rows > 0) {
        $old = $photo_q->fetch_assoc()['photo'];
        if ($old && file_exists("uploads/" . $old)) {
            unlink("uploads/" . $old);
        }
    }

    // Delete member row
    $conn->query("DELETE FROM members WHERE id=$id");

    header("Location: manage_members.php");
    exit;
}

// FETCH MEMBERS
$result = $conn->query("SELECT * FROM members ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Members — Patel Studio</title>
    <link rel="stylesheet" href="manage_members.css">
</head>

<body>

<!-- SIDEBAR -->
<?php include("sidebar.php"); ?>

<div class="main">

    <h1 class="page-title">👥 Manage Members</h1>

    <table class="member-table">
        <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>UserName</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id']; ?></td>

            <td>
                <?php if (!empty($row['photo'])) { ?>
                    <img src="uploads/<?= $row['photo']; ?>" class="profile-img">
                <?php } else { ?>
                    <img src="assets/default_profile.png" class="profile-img">
                <?php } ?>
            </td>

            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['fullname']); ?></td>
            <td><?= $row['created_at']; ?></td>

            <td>
                <a class="btn edit" href="edit_member.php?id=<?= $row['id']; ?>">Edit</a>

                <a class="btn delete"
                   onclick="return confirm('Are you sure you want to delete this member?');"
                   href="manage_members.php?delete=<?= $row['id']; ?>">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>

    </table>

</div>

</body>
</html>
