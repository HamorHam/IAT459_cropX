<?php
require_once('../private/initialize.php');

$plant_name = $_GET['plant'] ?? '';

if (empty($plant_name)) {
  redirect_to(url_for('/index.php'));
}

$plant_query = "SELECT * FROM plant WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' LIMIT 1";
$plant_result = mysqli_query($db, $plant_query);
$plant = mysqli_fetch_assoc($plant_result);

if (!$plant) {
  $error_message = "Plant not found.";
}

$page_title = $plant ? h($plant['PlantName']) : 'Plant Not Found';
include(SHARED_PATH . '/public_header.php');
include(SHARED_PATH . '/public_navigation.php');
?>

<div id="content">
  <?php if (isset($error_message)): ?>
    <p><?php echo h($error_message); ?></p>
  <?php else: ?>
    <h1><?php echo h($plant['PlantName']); ?></h1>
    <?php if ($plant['Family']): ?>
      <p><strong>Family:</strong> <?php echo h($plant['Family']); ?></p>
    <?php endif; ?>
    <?php if ($plant['Description']): ?>
      <p><strong>Description:</strong> <?php echo h($plant['Description']); ?></p>
    <?php endif; ?>
    <!-- we need to add more stuff here... probably better ui too -->
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
