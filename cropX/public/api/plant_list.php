<?php
require_once('../../private/initialize.php');

header('Content-Type: application/json');

// retrieve page and limit parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if ($page < 1) { $page = 1; }
if ($limit < 1) { $limit = 10; }
$offset = ($page - 1) * $limit;

// get total count of plants for pagination
$count_query = "SELECT COUNT(*) AS count FROM plant";
$count_result = mysqli_query($db, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$totalCount = (int)$count_row['count'];
$totalPages = ceil($totalCount / $limit);

// retrieve plants for the current page
$query = "SELECT PlantName, Family FROM plant ORDER BY PlantName LIMIT $limit OFFSET $offset";
$result = mysqli_query($db, $query);

$plants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $plants[] = $row;
}

// return the JSON response with plant data and pagination info
// https://www.w3schools.com/php/func_json_encode.asp
echo json_encode([
   "plants" => $plants,
   "currentPage" => $page,
   "totalPages" => $totalPages,
]);