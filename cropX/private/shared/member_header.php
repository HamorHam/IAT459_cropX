<?php
  if(!isset($page_title)) { $page_title = 'Member Profile'; }
?>

<!doctype html>

<html lang="en">
  <head>
    <title>CropX - <?php echo h($page_title); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" media="all" href="<?php echo url_for('/css/member_style.css'); ?>" />
  </head>

  <body>
    <header>
      <h1>CropX Member Profile</h1>
    </header>

    <nav>
      <ul>
        <li>User:
          <?php
            echo $_SESSION['username'] ?? '';
          ?>

        </li>
        <li><a href="<?php echo url_for('/member/index.php'); ?>">Menu</a></li>
        <li><a href="<?php echo url_for('/member/register.php'); ?>">Register</a></li>
        <li><a href="<?php echo url_for('/member/logout.php'); ?>">Logout</a></li>
      </ul>
    </nav>
