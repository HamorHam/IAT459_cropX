<?php
require_once('../private/initialize.php');

header('Content-Type: application/json');

if (!is_post_request()) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
  exit;
}

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
  exit;
}

$plant_name = $_POST['plant_name'] ?? '';
$content = $_POST['content'] ?? '';

if (is_blank($plant_name) || is_blank($content)) {
  echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
  exit;
}

$user_id = $_SESSION['user_id'];
$sql = "INSERT INTO comments (PlantName, UserID, CommentDate, IsApproved, Content) VALUES (
  '" . mysqli_real_escape_string($db, $plant_name) . "',
  '" . mysqli_real_escape_string($db, $user_id) . "',
  NOW(),
  0,
  '" . mysqli_real_escape_string($db, $content) . "'
)";

if (mysqli_query($db, $sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Comment submitted and pending approval.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($db)]);
}
