<?php
require_once('../../private/initialize.php');

// Redirect user to dashboard if already logged in
$errors = [];

if (isset($_SESSION['username'])) {
  redirect_to(url_for('/member/index.php'));
}

// Handle form submission
if (is_post_request()) {
  $name = $_POST['username'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';
  $latitude = $_POST['latitude'] ?? '';
  $longitude = $_POST['longitude'] ?? '';
  $role = 'user';
  $verification_code = strval(rand(100000, 999999));

  // Validate passwords match
  if ($password === $password_confirm) {
    // Check if the email and username already exist
    // Use prepared statements to prevent SQL injection
    $username_query = "SELECT COUNT(*) AS count FROM user WHERE Name = '" . mysqli_real_escape_string($db, $name) . "'";
    $username_res = mysqli_query($db, $username_query);
    $username_count = mysqli_fetch_assoc($username_res)['count'];

    $email_query = "SELECT COUNT(*) AS count FROM user WHERE Email = '" . mysqli_real_escape_string($db, $email) . "'";
    $email_res = mysqli_query($db, $email_query);
    $email_count = mysqli_fetch_assoc($email_res)['count'];
    // Check if the email is already registered
    // Use prepared statements to prevent SQL injection
    if ($username_count != 0) {
      $errors[] = 'The username already exists. Please try another.';
    } elseif ($email_count != 0) {
      $errors[] = 'This email is already registered. Please use another email.';
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert_user_query = "INSERT INTO user (Role, Name, Password, Email, Latitude, Longitude, VerificationCode, IsVerified)
                            VALUES (
                              '" . mysqli_real_escape_string($db, $role) . "',
                              '" . mysqli_real_escape_string($db, $name) . "',
                              '" . mysqli_real_escape_string($db, $hashed_password) . "',
                              '" . mysqli_real_escape_string($db, $email) . "',
                              '" . mysqli_real_escape_string($db, $latitude) . "',
                              '" . mysqli_real_escape_string($db, $longitude) . "',
                              '" . mysqli_real_escape_string($db, $verification_code) . "',
                              0
                            )";
      // Prepare the SQL statement
      if (mysqli_query($db, $insert_user_query)) {
        $to = $email;
        $subject = "Your CropX Verification Code";
        $message = "Hello $name,\n\nYour verification code is: $verification_code\nPlease enter this code to verify your account.";
        $headers = "From: no-reply@cropx.com";

        mail($to, $subject, $message, $headers);
        // Redirect to the verification page with the email as a query parameter
        redirect_to(url_for('/member/verify_code.php?email=' . urlencode($email)));
      } else {
        $errors[] = 'Registration failed: ' . mysqli_error($db);
      }
    }
  } else {
    $errors[] = 'Passwords do not match.';
  }
}
?>
<!-- Registration form -->
<?php $page_title = 'Register'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="content" style="display:flex;align-items:center;justify-content:center;flex-direction:column">
  <h1>Register</h1>

  <?php echo display_errors($errors); ?>

  <form id="registration-form" action="register.php" method="post"
    style="margin-bottom:6em;display:flex;flex-direction:column;gap:6px;width:100%;max-width:400px">
    
    <label for="email">Email:</label>
    <input type="text" name="email" required />
    
    <label for="username">Username:</label>
    <input type="text" name="username" required />
    
    <label for="password">Password:</label>
    <input type="password" name="password" required />
    
    <label for="password_confirm">Confirm Password:</label>
    <input type="password" name="password_confirm" required />
    
    <label for="address">Address:</label>
    <input type="text" name="address" id="address" required />

    <input type="hidden" name="latitude" id="latitude" />
    <input type="hidden" name="longitude" id="longitude" />

    <button type="button" onclick="fetchCoordinatesAndSubmit()">Register</button>
  </form>
</div>

<script>
// Function to fetch coordinates from the address input and submit the form
function fetchCoordinatesAndSubmit() {
  const address = document.getElementById('address').value;
  const latInput = document.getElementById('latitude');
  const lonInput = document.getElementById('longitude');

  // Check if address is empty
  if (!address) {
    alert("Please enter an address.");
    return;
  }
  // Use Nominatim API to get latitude and longitude from the address
  // Note: Nominatim usage policy requires a user agent to be set in the request header
  const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;

  // Fetch the coordinates from the Nominatim API
  fetch(url)
    .then(res => res.json())
    .then(data => {
      if (data.length > 0) {
        // If data is found, set the latitude and longitude inputs
        latInput.value = data[0].lat;
        lonInput.value = data[0].lon;
        // Submit the form
        document.getElementById('registration-form').submit();
      } else {
        // If no data is found, alert the user
        alert("Address not found. Try to be more specific.");
      }
    })
    // Handle errors in fetching the coordinates
    .catch(err => {
      console.error("Error fetching geolocation:", err);
      alert("Could not fetch location from address.");
    });
}
</script>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
