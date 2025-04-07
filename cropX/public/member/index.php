<?php 
require_once('../../private/initialize.php'); 

// Ensure user is logged in... otherwise redirect to login
if (!isset($_SESSION['username'])) {
  redirect_to(url_for('/member/login.php'));
}

$user_id = $_SESSION['user_id'];

// Retrieve current user info
$query  = "SELECT * FROM user WHERE UserID = " . intval($user_id) . " LIMIT 1";
$result = mysqli_query($db, $query);
$current_user = mysqli_fetch_assoc($result);

$errors = [];
$message = '';

// Process form submissions
if (is_post_request()) {

  // Process account deletion
  if (isset($_POST['delete_account'])) {
    // Remove user from `user` table
    $delete_query = "DELETE FROM user WHERE UserID = " . intval($user_id);
    
    if (mysqli_query($db, $delete_query)) {
        // The foreign key on comments must be ON DELETE SET NULL
        // so the comment still exists but UserID becomes NULL
        session_destroy();
        redirect_to(url_for('/index.php'));
    } else {
        $errors[] = "Failed to delete account: " . mysqli_error($db);
    }
}

  // Process profile update
  if (isset($_POST['update_info'])) {
    // Get fields from POST
    $username  = $_POST['username'] ?? '';
    $email     = $_POST['email'] ?? '';
    $latitude  = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';

    // If username has changed, check for uniqueness.
    if ($username !== $current_user['Name']) {
      $username_query = "SELECT COUNT(*) AS count FROM user WHERE Name = '" . mysqli_real_escape_string($db, $username) . "' AND UserID != " . intval($user_id);
      $username_result = mysqli_query($db, $username_query);
      $username_count = mysqli_fetch_assoc($username_result)['count'];
      if ($username_count != 0) {
        $errors[] = "The username is already taken. Please choose another.";
      }
    }

    // If no errors, update the user's info.
    if (empty($errors)) {
      $update_query = "UPDATE user SET 
        Name = '" . mysqli_real_escape_string($db, $username) . "',
        Email = '" . mysqli_real_escape_string($db, $email) . "',
        Latitude = '" . mysqli_real_escape_string($db, $latitude) . "',
        Longitude = '" . mysqli_real_escape_string($db, $longitude) . "'
        WHERE UserID = " . intval($user_id);
      if (mysqli_query($db, $update_query)) {
        $message = "Information updated successfully.";
        // Update session if username changed.
        $_SESSION['username'] = $username;
        // Refresh current user info
        $result = mysqli_query($db, $query);
        $current_user = mysqli_fetch_assoc($result);
      } else {
        $errors[] = "Update failed: " . mysqli_error($db);
      }
    }
  }
}

$page_title = 'Member Dashboard';
include(SHARED_PATH . '/member_header.php'); 
?>

<div id="content">
  <h1>Dashboard</h1>
  <h4>Welcome, <?php echo h($_SESSION['username']); ?>!</h4>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <?php foreach ($errors as $error) {
        echo "<p>" . h($error) . "</p>";
      } ?>
    </div>
  <?php endif; ?>
  
  <?php if ($message): ?>
    <p><?php echo h($message); ?></p>
  <?php endif; ?>

  <h3 style="margin-top:2em">Update Your Information</h3>
  <hr/>
  <form style="display:grid;grid-template-columns:repeat(2, 50%);grid-template-rows:repeat(4, fit-content);gap:6px;max-width:800px" action="index.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo h($current_user['Name']); ?>" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo h($current_user['Email']); ?>" required>
    
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" value="<?php echo h($current_user['Latitude']); ?>">
    
    <label for="longitude">Longitude:</label>
    <input type="text" name="longitude" value="<?php echo h($current_user['Longitude']); ?>">
    
    <button style="grid-column:1/span 2;" type="submit" name="update_info">Save</button>
  </form>
  
  <hr>
  
  <h3 style="margin-top:2em">Delete Your Account</h3>
  <form style="margin-bottom:3em" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
    <button type="submit" name="delete_account">Delete Account</button>
  </form>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>