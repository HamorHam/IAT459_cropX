<?php
require_once('../../private/initialize.php');

$errors = [];

// Redirect if session already exists
if (isset($_SESSION['username'])) {
  redirect_to(url_for('/public/index.php'));
}

// Handle registration
if (is_post_request()) {
  $name = $_POST['username'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';
  $role = 'user'; // default role for all new registrations

  // ✅ Check if passwords match
  if ($password === $password_confirm) {

    // ✅ Check if username already exists
    $existing_query = "SELECT COUNT(*) AS count FROM user WHERE Name = '" . mysqli_real_escape_string($db, $name) . "'";
    $existing_res = mysqli_query($db, $existing_query);

    if (mysqli_fetch_assoc($existing_res)['count'] != 0) {
      $errors[] = 'The username already exists in the database, please try another username.';
    } else {
      // ✅ Hash password and insert new user
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert_user_query = "INSERT INTO user (Role, Name, Password, Email) VALUES (
        '" . mysqli_real_escape_string($db, $role) . "',
        '" . mysqli_real_escape_string($db, $name) . "',
        '" . mysqli_real_escape_string($db, $hashed_password) . "',
        '" . mysqli_real_escape_string($db, $email) . "'
      )";

      if (mysqli_query($db, $insert_user_query)) {
        // ✅ Registration successful
        $_SESSION['username'] = $name;
        redirect_to(url_for('/staff/index.php'));
      } else {
        $errors[] = 'Registration failed: ' . mysqli_error($db);
      }
    }
  } else {
    $errors[] = 'Passwords do not match.';
  }
}
?>

<?php $page_title = 'Register'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <h1>Register</h1>

  <?php echo display_errors($errors); ?>

  <form action="register.php" method="post">
    Email:<br />
    <input type="text" name="email" value="" required /><br />
    Username:<br />
    <input type="text" name="username" value="" required /><br />
    Password:<br />
    <input type="password" name="password" value="" required /><br />
    Confirm Password:<br />
    <input type="password" name="password_confirm" value="" required /><br />
    <input type="submit" value="Register" />
  </form>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
