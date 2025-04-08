<?php
require_once('../../private/initialize.php');

$errors = [];
$success = false;
$email = $_GET['email'] ?? '';
$entered_code = $_POST['verification_code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and query user
  $safe_email = mysqli_real_escape_string($db, $email);
  $safe_code = mysqli_real_escape_string($db, $entered_code);

  $query = "SELECT * FROM user WHERE Email = '$safe_email' AND IsVerified = 0 LIMIT 1";
  $result = mysqli_query($db, $query);

  if ($user = mysqli_fetch_assoc($result)) {
    if ($user['VerificationCode'] === $safe_code) {
      // Mark user as verified
      $update = "UPDATE user SET IsVerified = 1, VerificationCode = NULL WHERE Email = '$safe_email'";
      if (mysqli_query($db, $update)) {
        $success = true;
        redirect_to(url_for('/member/login.php?verified=1'));
      } else {
        $errors[] = "Verification failed. Please try again later.";
      }
    } else {
      $errors[] = "Incorrect verification code.";
    }
  } else {
    $errors[] = "Email not found or already verified.";
  }
}
?>

<?php $page_title = 'Verify Email'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="content" style="display:flex;flex-direction:column;align-items:center;justify-content:center;margin-top:3em">
  <h1>Verify Your Email</h1>

  <?php if (!empty($errors)): ?>
    <div style="color:red;">
      <?php foreach ($errors as $error): ?>
        <p><?php echo h($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!$success): ?>
    <p>Please enter the 6-digit code sent to <strong><?php echo h($email); ?></strong>.</p>

    <form method="post" action="verify_code.php?email=<?php echo urlencode($email); ?>" style="display:flex;flex-direction:column;gap:10px;width:300px;">
      <label for="verification_code">Verification Code:</label>
      <input type="text" name="verification_code" maxlength="6" pattern="\d{6}" required />
      <button type="submit">Verify</button>
    </form>
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
