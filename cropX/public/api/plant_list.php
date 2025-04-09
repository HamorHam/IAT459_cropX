<?php
require_once('../../private/initialize.php');
header('Content-Type: application/json');

// Ensure clean output
//https://www.php.net/manual/en/function.ob-clean.php
ob_clean();

// Pagination parameters
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int) $_GET['limit'] : 12;
$page = max($page, 1);
$limit = max($limit, 1);
$offset = ($page - 1) * $limit;

// latitude for sorting
$user_latitude = null;
if (isset($_GET['latitude']) && is_numeric($_GET['latitude'])) {
  $user_latitude = (float) $_GET['latitude'];
}

// Determine ORDER BY clause
$order_clause = "ORDER BY PlantName ASC"; // default alphabetical
if (!is_null($user_latitude)) {
  $order_clause = "ORDER BY ABS(((LatitudeOptimalMin + LatitudeOptimalMax) / 2) - {$user_latitude})";
}

// Query total count for pagination
$count_sql = "SELECT COUNT(*) AS count FROM plant";
$count_result = mysqli_query($db, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$totalCount = (int) $count_row['count'];
$totalPages = ceil($totalCount / $limit);

// Query actual plant data
$data_sql = "SELECT PlantName, Family, Image FROM plant $order_clause LIMIT $limit OFFSET $offset";
$data_result = mysqli_query($db, $data_sql);

$plants = [];
while ($row = mysqli_fetch_assoc($data_result)) {
  $plants[] = $row;
}

// Output JSON response
echo json_encode([
  "plants" => $plants,
  "currentPage" => $page,
  "totalPages" => $totalPages
]);
?>
