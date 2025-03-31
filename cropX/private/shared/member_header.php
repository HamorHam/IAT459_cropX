<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo h($page_title); ?> - CropX Member Area</title>
  <link rel="stylesheet" href="<?php echo url_for('/css/member_style.css'); ?>" />
</head>
<body>
  <header>
    <h1>CropX Member Area</h1>
    <nav>
      <ul>
        <li><a href="<?php echo url_for('/member/index.php'); ?>">Dashboard</a></li>
        <li><a href="<?php echo url_for('/member/logout.php'); ?>">Logout</a></li>
      </ul>
    </nav>
  </header>
