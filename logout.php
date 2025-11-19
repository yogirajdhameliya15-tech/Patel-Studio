<?php
session_start();
include("db.php");
if(isset($_SESSION['member_id'])){
  $mid = (int)$_SESSION['member_id'];
  // update last login_log record with logout time
  mysqli_query($conn, "UPDATE login_log SET logout_time=NOW() WHERE member_id='$mid' AND logout_time IS NULL ORDER BY id DESC LIMIT 1");
}
session_unset();
session_destroy();
header("Location: member_login.php");
exit;
?>
