<?php
require_once('../../private/initialize.php');

header('Content-Type: application/json');

// Retrieve page and limit parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
if ($page < 1) { $page = 1; }
if ($limit < 1) { $limit = 12; }
$offset = ($page - 1) * $limit;

// Get total count of plants for pagination
$count_query = "SELECT COUNT(*) AS count FROM plant";
$count_result = mysqli_query($db, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$totalCount = (int)$count_row['count'];
$totalPages = ceil($totalCount / $limit);

$query = "SELECT PlantName, Family, Image FROM plant ORDER BY PlantName LIMIT $limit OFFSET $offset";
$result = mysqli_query($db, $query);

$plants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $plants[] = $row;
}

// Return the JSON response with plant data and pagination info
echo json_encode([
   "plants" => $plants,
   "currentPage" => $page,
   "totalPages" => $totalPages,
]);