<?php
session_start();
include("db.php");
if (!isset($_SESSION['member_id'])) exit;
$mid = (int)$_SESSION['member_id'];
$action = trim($_POST['action'] ?? '');
if ($action !== '') {
    $stmt = $conn->prepare("INSERT INTO activity_log (member_id, action, time) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $mid, $action);
    $stmt->execute();
    echo "OK";
}
?>
