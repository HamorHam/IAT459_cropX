<?php
require_once('../private/initialize.php');
$page_title = 'Advanced Plant Filter';
include(SHARED_PATH . '/public_header.php');
include(SHARED_PATH . '/public_navigation.php');

// Capture user input
$family     = $_GET['family'] ?? '';
$category   = $_GET['category'] ?? '';
$climate    = $_GET['climate'] ?? '';

$temp_min   = $_GET['temp_min'] ?? '';
$temp_max   = $_GET['temp_max'] ?? '';
$rain_min   = $_GET['rain_min'] ?? '';
$rain_max   = $_GET['rain_max'] ?? '';
$ph_min     = $_GET['ph_min'] ?? '';
$ph_max     = $_GET['ph_max'] ?? '';
$alt_min    = $_GET['alt_min'] ?? '';
$alt_max    = $_GET['alt_max'] ?? '';

$where_clauses = [];

if (!is_blank($family)) {
  $where_clauses[] = "Family = '" . mysqli_real_escape_string($db, $family) . "'";
}
if (!is_blank($category)) {
  $where_clauses[] = "Category = '" . mysqli_real_escape_string($db, $category) . "'";
}
if (!is_blank($climate)) {
  $where_clauses[] = "ClimateZone = '" . mysqli_real_escape_string($db, $climate) . "'";
}

if (is_numeric($temp_min)) {
  $where_clauses[] = "TempRequiredOptimalMin >= " . floatval($temp_min);
}
if (is_numeric($temp_max)) {
  $where_clauses[] = "TempRequiredOptimalMax <= " . floatval($temp_max);
}
if (is_numeric($rain_min)) {
  $where_clauses[] = "RainfallAnnualOptimalMin >= " . floatval($rain_min);
}
if (is_numeric($rain_max)) {
  $where_clauses[] = "RainfallAnnualOptimalMax <= " . floatval($rain_max);
}
if (is_numeric($ph_min)) {
  $where_clauses[] = "SoilPHOptimalMin >= " . floatval($ph_min);
}
if (is_numeric($ph_max)) {
  $where_clauses[] = "SoilPHOptimalMax <= " . floatval($ph_max);
}
if (is_numeric($alt_min)) {
  $where_clauses[] = "AltitudeOptimalMin >= " . intval($alt_min);
}
if (is_numeric($alt_max)) {
  $where_clauses[] = "AltitudeOptimalMax <= " . intval($alt_max);
}

// Build and execute the SQL
$sql = "SELECT PlantName FROM plant";
if (!empty($where_clauses)) {
  $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY PlantName ASC";

$result = mysqli_query($db, $sql);
confirm_result_set($result);
?>

<div id="content">
  <h1>Filter Plants</h1>

  <form method="get" action="filter.php">
    <label>Family:</label><br>
    <input type="text" name="family" value="<?php echo h($family); ?>"><br><br>

    <label>Category:</label><br>
    <input type="text" name="category" value="<?php echo h($category); ?>"><br><br>

    <label>Climate Zone:</label><br>
    <input type="text" name="climate" value="<?php echo h($climate); ?>"><br><br>

    <label>Temperature Optimal (°C):</label><br>
    Min: <input type="number" step="0.1" name="temp_min" value="<?php echo h($temp_min); ?>">
    Max: <input type="number" step="0.1" name="temp_max" value="<?php echo h($temp_max); ?>"><br><br>

    <label>Rainfall Optimal (mm):</label><br>
    Min: <input type="number" name="rain_min" value="<?php echo h($rain_min); ?>">
    Max: <input type="number" name="rain_max" value="<?php echo h($rain_max); ?>"><br><br>

    <label>Soil pH Optimal:</label><br>
    Min: <input type="number" step="0.1" name="ph_min" value="<?php echo h($ph_min); ?>">
    Max: <input type="number" step="0.1" name="ph_max" value="<?php echo h($ph_max); ?>"><br><br>

    <label>Altitude Optimal (m):</label><br>
    Min: <input type="number" name="alt_min" value="<?php echo h($alt_min); ?>">
    Max: <input type="number" name="alt_max" value="<?php echo h($alt_max); ?>"><br><br>

    <input type="submit" value="Filter Plants">
  </form>

  <h2>Matching Plants</h2>
  <?php if (mysqli_num_rows($result) > 0): ?>
    <ul>
      <?php while ($plant = mysqli_fetch_assoc($result)): ?>
        <li>
          <a href="<?php echo url_for('/plant.php'); ?>?plant=<?php echo urlencode($plant['PlantName']); ?>">
            <?php echo h($plant['PlantName']); ?>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>No matching plants found.</p>
  <?php endif; ?>
  <?php mysqli_free_result($result); ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
