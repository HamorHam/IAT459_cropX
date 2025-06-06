<?php
require_once('../private/initialize.php');

$page_title = 'Search Plant';

if (isset($_SESSION['username'])) {
  include(SHARED_PATH . '/member_header.php');
} else {
  include(SHARED_PATH . '/public_header.php');
}

$search_term = $_GET['q'] ?? '';
$results = [];

// https://www.w3schools.com/sql/sql_like.asp
if (!is_blank($search_term)) {
  $safe_search = "%" . mysqli_real_escape_string($db, $search_term) . "%";
  $plant_query = "SELECT PlantName FROM plant 
                  WHERE PlantName LIKE '$safe_search' 
                     OR CommonNames LIKE '$safe_search'";
  $plant_result = mysqli_query($db, $plant_query);
  confirm_result_set($plant_result);
  $results = $plant_result;
}
?>

<div id="content">
  <h1>Search for a Plant</h1>

  <?php if ($search_term != ''): ?>
    <h2>Search Results for "<?php echo h($search_term); ?>"</h2>

    <?php if (mysqli_num_rows($results) > 0): ?>
      <ul>
        <?php while ($plant = mysqli_fetch_assoc($results)): ?>
          <li>
            <a href="<?php echo url_for('/plant.php'); ?>?plant=<?php echo urlencode($plant['PlantName']); ?>">
              <?php echo h($plant['PlantName']); ?>
            </a>
          </li>
        <?php endwhile; ?>
        <?php mysqli_free_result($results); ?>
      </ul>
    <?php else: ?>
      <p>No plants found.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
