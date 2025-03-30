<footer>
  &copy; <?php echo date('Y'); ?> <a style="color: white;" href="<?php echo url_for('/index.php?') ?>">Globe Bank</a>
</footer>

</body>
</html>

<?php
  db_disconnect($db);
?>
