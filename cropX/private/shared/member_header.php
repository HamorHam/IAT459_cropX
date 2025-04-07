<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo h($page_title); ?> - CropX</title>
  <link rel="stylesheet" href="<?php echo url_for('/css/normalize.css'); ?>" />
  <link rel="stylesheet" href="<?php echo url_for('/css/style.css'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
  <div class="navbar">
    <div class="logo">
      <a href="<?php echo url_for('/index.php'); ?>">
        <img src="<?php echo url_for('/img/cropx-logo.svg'); ?>" alt="CropX Logo">
      </a>
    </div>
    <input type="checkbox" id="nav-toggle" class="nav-toggle">
    <nav>
      <ul>
        <li>
          <form action="<?php echo url_for('/search.php'); ?>" method="get" class="nav-search">
            <input type="text" name="q" placeholder="Search plants..." required>
            <button type="submit">Search</button>
          </form>
        </li>
        <li><a href="<?php echo url_for('/member/index.php'); ?>">Dashboard</a></li>
        <li><a href="<?php echo url_for('/member/logout.php'); ?>">Logout</a></li>
        <!--<li><a href="<?php echo url_for('/filter.php'); ?>">Filter</a></li>-->
      </ul>
    </nav>
    <label for="nav-toggle" class="nav-toggle-label">
      <span></span>
      <span></span>
      <span></span>
    </label>
  </div>
</header>