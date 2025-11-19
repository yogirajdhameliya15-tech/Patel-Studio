<?php
session_start();
if (!isset($_SESSION['member'])) { header("Location: member_login.php"); exit; }
include("db.php");
$member_id = $_SESSION['member_id'];
$username = $_SESSION['member'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Member Home — Patel Studio</title>
<link rel="stylesheet" href="member_style.css">
</head>
<body>
<?php include 'member_topbar.php'; ?>
<div class="layout">
  <?php include 'member_sidebar.php'; ?>
  <main class="main">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>

    <section class="cards">
      <a class="card" href="qr_generator.php">QR Generator</a>
      <a class="card" href="poster_maker.php">Poster Maker</a>
      <a class="card" href="inst_post.php">Instagram Post Maker</a>
      <a class="card" href="reels_maker.php">Reel Maker</a>
    </section>

    <section class="recent">
      <h2>Recent Activity</h2>
      <table>
        <thead><tr><th>Action</th><th>Time</th></tr></thead>
        <tbody>
<?php
$q = $conn->prepare("SELECT action, time FROM activity_log WHERE member_id=? ORDER BY time DESC LIMIT 8");
$q->bind_param("i", $member_id);
$q->execute();
$res = $q->get_result();
while($r = $res->fetch_assoc()){
  echo "<tr><td>".htmlspecialchars($r['action'])."</td><td>".htmlspecialchars($r['time'])."</td></tr>";
}
?>
        </tbody>
      </table>
    </section>
  </main>
</div>
</body>
</html>
