<<<<<<< Updated upstream
<?php 
require_once('../../private/initialize.php'); 

// ensure user is logged in... otherwise redirect to login
if (!isset($_SESSION['username'])) {
  redirect_to(url_for('/member/login.php'));
}

$page_title = 'Member Dashboard';
include(SHARED_PATH . '/member_header.php'); 
?>

<div id="content">
  <h1>Dashboard</h1>
  <p>Welcome, <?php echo h($_SESSION['username']); ?>!</p>
  <ul>
    <li><a href="<?php echo url_for('/member/logout.php'); ?>">Logout</a></li>
    <!-- additional member functionality will go here -->
  </ul>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
=======
<?php require_once('../../private/initialize.php'); ?>

<?php $page_title = 'Member Menu'; ?>

<?php
is_logged_in();
?>
<?php include(SHARED_PATH . '/member_header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Main Menu</h2>
    <ul>
      <li><a href="<?php echo url_for('/staff/subjects/index.php'); ?>">Subjects</a></li>
      <li><a href="<?php echo url_for('/staff/pages/index.php'); ?>">Pages</a></li>
    </ul>
  </div>

</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>
>>>>>>> Stashed changes
