<?php
require_once('../../private/initialize.php');

$errors = [];

// Redirect if session already exists
if (isset($_SESSION['username'])) {
  redirect_to(url_for('/member/index.php'));
}

// Handle registration
if (is_post_request()) {
  $name = $_POST['username'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';
  $latitude = $_POST['latitude'] ?? '';
  $longitude = $_POST['longitude'] ?? '';
  $role = 'user'; // default role

  //  Check if passwords match
  if ($password === $password_confirm) {

    //  Check if username already exists
    $username_query = "SELECT COUNT(*) AS count FROM user WHERE Name = '" . mysqli_real_escape_string($db, $name) . "'";
    $username_res = mysqli_query($db, $username_query);
    $username_count = mysqli_fetch_assoc($username_res)['count'];

    //  Check if email already exists
    $email_query = "SELECT COUNT(*) AS count FROM user WHERE Email = '" . mysqli_real_escape_string($db, $email) . "'";
    $email_res = mysqli_query($db, $email_query);
    $email_count = mysqli_fetch_assoc($email_res)['count'];

    if ($username_count != 0) {
      $errors[] = 'The username already exists. Please try another.';
    } elseif ($email_count != 0) {
      $errors[] = 'This email is already registered. Please use another email.';

    } else {
      //  Hash password and insert user
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert_user_query = "INSERT INTO user (Role, Name, Password, Email, Latitude, Longitude) VALUES (
        '" . mysqli_real_escape_string($db, $role) . "',
        '" . mysqli_real_escape_string($db, $name) . "',
        '" . mysqli_real_escape_string($db, $hashed_password) . "',
        '" . mysqli_real_escape_string($db, $email) . "',
        '" . mysqli_real_escape_string($db, $latitude) . "',
        '" . mysqli_real_escape_string($db, $longitude) . "'
      )";

      if (mysqli_query($db, $insert_user_query)) {
        // Redirect to login page after successful registration
        redirect_to(url_for('/member/login.php?registered=1'));
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
<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="content" style="display:flex;align-items:center;justify-content:center;flex-direction:column">
  <h1>Register</h1>

  <?php echo display_errors($errors); ?>

  <form action="register.php" method="post"
    style="margin-bottom:6em;display:flex;flex-direction:column;gap:6px;width:100%;max-width:400px">
    <label for="email">Email:</label>
    <input type="text" name="email" required />
    <label for="username">Username:</label>
    <input type="text" name="username" required />
    <label for="password">Password:</label>
    <input type="password" name="password" required />
    <label for="password_confirm">Confirm Password:</label>
    <input type="password" name="password_confirm" required />
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" required />
    <label for="longitude">Longitude:</label>
    <input style="margin-bottom:.5em" type="text" name="longitude" required />
    <button type="submit" value="Register">Register</button>
  </form>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>