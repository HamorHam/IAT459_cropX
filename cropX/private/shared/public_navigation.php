<nav>
  <ul>
    <li><a href="<?php echo url_for('/index.php'); ?>">Home</a></li>
    <li><a href="<?php echo url_for('/member/login.php'); ?>">Login</a></li>
    <li><a href="<?php echo url_for('/member/register.php'); ?>">Register</a></li>
    <li>
      <form action="<?php echo url_for('/search.php'); ?>" method="get" style="display: flex; gap: 5px;">
        <input type="text" name="q" placeholder="Search plants..." required>
        <button type="submit">Search</button>
      </form>
    </li>
    <li><a href="<?php echo url_for('/filter.php'); ?>">Filter</a></li>
  </ul>
</nav>
