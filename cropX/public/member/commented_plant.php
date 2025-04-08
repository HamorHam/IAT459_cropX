<?php
require_once('../../private/initialize.php');

// Ensure user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
  redirect_to(url_for('/member/login.php'));
}

$user_id = $_SESSION['user_id'];

// Get plants commented by this user
$commented_plants = find_commented_plants_by_user($user_id);

$page_title = 'My Commented Plants';
include(SHARED_PATH . '/member_header.php');
?>

<div id="content">
  <h1>My Commented Plants</h1>

  <?php if (mysqli_num_rows($commented_plants) > 0): ?>
    <ul>
      <?php while ($plant = mysqli_fetch_assoc($commented_plants)): ?>
        <li>
          <a href="<?php echo url_for('/plant.php?plant=' . urlencode($plant['PlantName'])); ?>">
            <?php echo h($plant['PlantName']); ?>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>You haven't commented on any plants yet.</p>
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
