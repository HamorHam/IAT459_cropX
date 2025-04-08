<?php
require_once('../../private/initialize.php');

// Ensure user is logged in and is a moderator.
if (!isset($_SESSION['username']) || (($_SESSION['role'] ?? '') !== 'moderator')) {
  redirect_to(url_for('/member/login.php'));
}

$errors = [];
$message = '';

// Process approval or rejection actions.
if (is_post_request() && isset($_POST['comment_id'])) {
  $comment_id = $_POST['comment_id'];
  
  if (isset($_POST['approve'])) {
    $update_query = "UPDATE comments 
                     SET IsApproved = 1 
                     WHERE CommentID = '" . mysqli_real_escape_string($db, $comment_id) . "'";
    if (mysqli_query($db, $update_query)) {
      $message = "Comment approved.";
    } else {
      $errors[] = "Failed to approve comment: " . mysqli_error($db);
    }
  }
  
  if (isset($_POST['reject'])) {
    $delete_query = "DELETE FROM comments 
                     WHERE CommentID = '" . mysqli_real_escape_string($db, $comment_id) . "'";
    if (mysqli_query($db, $delete_query)) {
      $message = "Comment rejected and deleted.";
    } else {
      $errors[] = "Failed to delete comment: " . mysqli_error($db);
    }
  }
}

// Retrieve pending comments.
$query = "SELECT c.*, u.Name AS UserName FROM comments c
          LEFT JOIN user u ON c.UserID = u.UserID
          WHERE c.IsApproved = 0
          ORDER BY c.CommentDate ASC";
$result = mysqli_query($db, $query);

$page_title = 'Review Comments';
include(SHARED_PATH . '/member_header.php');
?>

<div id="content">
  <h1>Review Comments</h1>
  
  <?php echo display_errors($errors); ?>
  <?php if (!empty($message)) echo "<p>" . h($message) . "</p>"; ?>
  
  <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <tr>
          <th>Comment ID</th>
          <th>Plant</th>
          <th>User</th>
          <th>Date</th>
          <th>Content</th>
          <th>Actions</th>
        </tr>
      <?php while ($comment = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo h($comment['CommentID']); ?></td>
          <td><?php echo h($comment['PlantName']); ?></td>
          <td><?php echo h($comment['UserName'] ?? 'Anonymous'); ?></td>
          <td><?php echo h($comment['CommentDate']); ?></td>
          <td><?php echo nl2br(h($comment['Content'])); ?></td>
          <td>
            <form action="review_comments.php" method="post" style="display:inline;">
              <input type="hidden" name="comment_id" value="<?php echo h($comment['CommentID']); ?>">
              <button class="reply-btn" type="submit" name="approve" value="Approve">Approve</button>
            </form>
            <form action="review_comments.php" method="post" style="display:inline;">
              <input type="hidden" name="comment_id" value="<?php echo h($comment['CommentID']); ?>">
              <button class="reply-btn" type="submit" name="reject" value="Reject">Reject</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No pending comments for review.</p>
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>