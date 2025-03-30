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
  $latitude = $_POST['latitude'] ?? '';
  $longitude = $_POST['longitude'] ?? '';
  $role = 'user'; // default role

  //  Check if passwords match
  if ($password === $password_confirm) {

    //  Check if username already exists
    $existing_query = "SELECT COUNT(*) AS count FROM user WHERE Name = '" . mysqli_real_escape_string($db, $name) . "'";
    $existing_res = mysqli_query($db, $existing_query);

    if (mysqli_fetch_assoc($existing_res)['count'] != 0) {
      $errors[] = 'The username already exists in the database, please try another username.';
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
        $_SESSION['username'] = $name;
        redirect_to(url_for('/member/index.php'));
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
    <input type="text" name="email" required /><br />
    Username:<br />
    <input type="text" name="username" required /><br />
    Password:<br />
    <input type="password" name="password" required /><br />
    Confirm Password:<br />
    <input type="password" name="password_confirm" required /><br />
    Latitude:<br />
    <input type="text" name="latitude" required /><br />
    Longitude:<br />
    <input type="text" name="longitude" required /><br />
    <input type="submit" value="Register" />
  </form>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
