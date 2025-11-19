<?php
session_start();
if (!isset($_SESSION['admin']) || !isset($_SESSION['admin_verify']) || $_SESSION['admin_verify'] !== true) {
    header("Location: admin_login.php"); exit;
}
include("db.php");
$q = "SELECT m.username, a.action, a.time, l.login_time, l.logout_time
      FROM activity_log a
      JOIN members m ON a.member_id = m.id
      LEFT JOIN login_log l ON l.member_id = m.id
      ORDER BY a.time DESC";
$res = mysqli_query($conn, $q);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Activity</title></head><body>
<h1>Activity</h1>
<table border="1" cellpadding="6"><tr><th>User</th><th>Action</th><th>Time</th><th>Login</th><th>Logout</th></tr>
<?php while($r = mysqli_fetch_assoc($res)){ ?>
<tr>
  <td><?php echo htmlspecialchars($r['username']);?></td>
  <td><?php echo htmlspecialchars($r['action']);?></td>
  <td><?php echo htmlspecialchars($r['time']);?></td>
  <td><?php echo $r['login_time']?:'—';?></td>
  <td><?php echo $r['logout_time']?:'—';?></td>
</tr>
<?php } ?>
</table>
</body></html>
