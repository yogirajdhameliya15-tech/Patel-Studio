<?php
// save_image.php
session_start();
include("db.php");

// require member logged in
if (!isset($_SESSION['member_id'])) {
    http_response_code(401);
    echo json_encode(["error"=>"Unauthorized"]);
    exit;
}

$data = $_POST['data'] ?? null;
$tag  = preg_replace('/[^a-z0-9_\-]/i','', ($_POST['tag'] ?? 'image') );

if (!$data) {
    http_response_code(400);
    echo json_encode(["error"=>"No data"]);
    exit;
}

// data is like "data:image/png;base64,...."
if (preg_match('/^data:image\/png;base64,/', $data)) {
    $base64 = substr($data, strlen('data:image/png;base64,'));
    $bin = base64_decode($base64);
    if ($bin === false) {
        http_response_code(400);
        echo json_encode(["error"=>"Invalid base64"]);
        exit;
    }
    // ensure uploads dir exists
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = $tag . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.png';
    $path = $dir . '/' . $filename;
    if (file_put_contents($path, $bin) === false) {
        http_response_code(500);
        echo json_encode(["error"=>"Failed to save"]);
        exit;
    }

    // optional: record in activity_log / gallery tables
    $member_id = (int)$_SESSION['member_id'];
    $action = "Saved {$tag}";
    $stmt = $conn->prepare("INSERT INTO activity_log (member_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $member_id, $action);
    $stmt->execute();

    echo json_encode(["ok"=>true, "file"=>"uploads/".$filename]);
    exit;
}

http_response_code(400);
echo json_encode(["error"=>"Unsupported format"]);
exit;
?>
