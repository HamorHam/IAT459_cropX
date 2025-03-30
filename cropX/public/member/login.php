<?php
require_once('../../private/initialize.php');

$errors = [];

// If already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
  redirect_to(url_for('/member/index.php'));
}

// Process login form
if (is_post_request()) {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // Look up the user by username
  $query = "SELECT * FROM user WHERE Name = ?";
  $stmt = $db->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($user = $result->fetch_assoc()) {
    // Verify password
    if (password_verify($password, $user['Password'])) {
      //  Password is correct, set session
      $_SESSION['username'] = $user['Name'];
      $_SESSION['user_id'] = $user['UserID'];
      $_SESSION['role'] = $user['Role'];
      redirect_to(url_for('/member/index.php'));
    } else {
      $errors[] = "Incorrect password.";
    }
  } else {
    $errors[] = "Username not found.";
  }
}
?>

<?php $page_title = 'Login'; ?>
<?php include(SHARED_PATH . '/member_header.php'); ?>

<div id="content">
  <h1>Login</h1>

  <?php echo display_errors($errors); ?>

  <form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" value="" required /><br />
    <label for="password">Password:</label>
    <input type="password" name="password" value="" required /><br />
    <input type="submit" value="Login" />
  </form>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
