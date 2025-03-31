<?php
require_once('../../private/initialize.php');

//ensure user is logged in.
if (!isset($_SESSION['username'])) {
  redirect_to(url_for('/member/login.php'));
}

$errors = [];
$message = '';

if (is_post_request()) {
  $plant_name = $_POST['plant_name'] ?? '';
  $content = $_POST['content'] ?? '';
  $parent_comment_id = $_POST['parent_comment_id'] ?? null;
  $user_id = $_SESSION['user_id'] ?? null;
  
  if (empty($plant_name) || empty($content)) {
    $errors[] = "Plant name and comment content are required.";
  }
  
  if (empty($errors)) {
    $query = "INSERT INTO comments (PlantName, UserID, CommentDate, IsApproved, Content, ParentCommentID)
              VALUES (
                '" . mysqli_real_escape_string($db, $plant_name) . "',
                '" . mysqli_real_escape_string($db, $user_id) . "',
                NOW(),
                0,
                '" . mysqli_real_escape_string($db, $content) . "',
                " . (empty($parent_comment_id) ? "NULL" : "'" . mysqli_real_escape_string($db, $parent_comment_id) . "'") . "
              )";
              
    if (mysqli_query($db, $query)) {
      $message = "Comment submitted and pending approval.";
    } else {
      $errors[] = "Failed to submit comment: " . mysqli_error($db);
    }
  }
}

$page_title = 'Add Comment';
include(SHARED_PATH . '/member_header.php');
?>

<div id="content">
  <h1>Add Comment</h1>
  
  <?php echo display_errors($errors); ?>
  
  <?php if ($message): ?>
    <p><?php echo h($message); ?></p>
  <?php endif; ?>
  
  <form action="add_comment.php" method="post">
    <label for="plant_name">Plant Name:</label>
    <input type="text" name="plant_name" required /><br />
    
    <label for="content">Comment:</label>
    <textarea name="content" required></textarea><br />
    
    <label for="parent_comment_id">Parent Comment ID (optional):</label>
    <input type="text" name="parent_comment_id" /><br />
    
    <input type="submit" value="Submit Comment" />
  </form>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
