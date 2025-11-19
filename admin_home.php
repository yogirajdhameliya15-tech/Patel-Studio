<?php
session_start();
if (!isset($_SESSION['admin_verify'])) {
    header("Location: admin_login.php");
    exit;
}

include("db.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard — Patel Studio</title>
<link rel="stylesheet" href="admin_home.css">
</head>

<body>

<?php include "admin_topbar.php"; ?>

<div class="layout">

    <?php include "admin_sidebar.php"; ?>

    <main class="main">

        <h1>Admin Dashboard</h1>
        <p class="subtitle">All Member Activities</p>

        <div class="tableBox">
            <table>
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Activity</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "
                    SELECT members.fullname, activity_log.action, activity_log.time
                    FROM activity_log 
                    JOIN members ON members.id = activity_log.member_id
                    ORDER BY activity_log.time DESC
                ";

                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['fullname'])."</td>
                            <td>".htmlspecialchars($row['action'])."</td>
                            <td>".htmlspecialchars($row['time'])."</td>
                          </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

    </main>

</div>
</body>
</html>
