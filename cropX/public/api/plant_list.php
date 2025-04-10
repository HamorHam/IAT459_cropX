<?php
// Returns plant data as JSON
require_once('../../private/initialize.php');

// Set header type for JSON output
header('Content-Type: application/json');

// Clean output buffer (if any)
ob_clean();

// Get and validate pagination parameters
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 12;
$page = max($page, 1);
$limit = max($limit, 1);
$offset = ($page - 1) * $limit;

// Get users latitude for sorting
$user_latitude = null;
if (isset($_GET['latitude']) && is_numeric($_GET['latitude'])) {
    $user_latitude = (float) $_GET['latitude'];
}

// Set default order by clause (alphabetical by plant name)
$order_clause = "ORDER BY PlantName ASC";
// If user latitude is provided, order by the distance of the average optimal latitude from user's latitude
if (!is_null($user_latitude)) {
    $order_clause = "ORDER BY ABS(((LatitudeOptimalMin + LatitudeOptimalMax) / 2) - {$user_latitude})";
}

// Get total plant count for pagination
$count_sql = "SELECT COUNT(*) AS count FROM plant";
$count_result = mysqli_query($db, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$totalCount = (int) $count_row['count'];
$totalPages = ceil($totalCount / $limit);

// Query plant data (only basic fields for now)
$data_sql = "SELECT PlantName, Family, Image FROM plant $order_clause LIMIT $limit OFFSET $offset";
$data_result = mysqli_query($db, $data_sql);

$plants = [];
while ($row = mysqli_fetch_assoc($data_result)) {
    $plants[] = $row;
}

// Return JSON response with plant data and pagination info
// https://www.w3schools.com/js/js_json_php.asp
echo json_encode([
  "plants"      => $plants,
  "currentPage" => $page,
  "totalPages"  => $totalPages
]);
?>