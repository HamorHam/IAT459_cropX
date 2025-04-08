<?php
require_once('../private/initialize.php');
$page_title = 'CropX Home';

$latitude = 'null';
if (isset($_SESSION['username'])) {
  include(SHARED_PATH . '/member_header.php');
  $user_id = $_SESSION['user_id'];
  $user_query = "SELECT Latitude FROM user WHERE UserID = " . intval($user_id) . " LIMIT 1";
  $user_result = mysqli_query($db, $user_query);
  $user_data = mysqli_fetch_assoc($user_result);
  $latitude = isset($user_data['Latitude']) ? floatval($user_data['Latitude']) : 'null';
} else {
  include(SHARED_PATH . '/public_header.php');
}
?>


<div id="content">
  
  <div id="plant-list">
    <p>Loading plants...</p>
  </div>
  
  <div id="pagination">
    <!-- pagination buttons will show here -->
  </div>
</div>

<script>
// js to load plants with pagination controls
const userLatitude = <?php echo json_encode($latitude); ?>;
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 1;
  const pageSize = 12;
  const plantListDiv = document.getElementById('plant-list');
  const paginationDiv = document.getElementById('pagination');

  // to load plants for a given page
  function loadPlants(page) {
  const baseUrl = "<?php echo url_for('/api/plant_list.php'); ?>";
  let url = `${baseUrl}?page=${page}&limit=${pageSize}`;
  if (userLatitude !== null) {
    url += `&latitude=${userLatitude}`;
  }

  fetch(url)
    .then(response => response.json())
    .then(data => {
      currentPage = data.currentPage;
      const totalPages = data.totalPages;
      const plants = data.plants;

      // build plant list HTML
      if (plants.length === 0) {
        plantListDiv.innerHTML = '<p>No plants found.</p>';
      } else {
        let listHtml = '<ul>';
        plants.forEach(plant => {
          listHtml += '<li><a href="<?php echo url_for('/plant.php'); ?>?plant=' + encodeURIComponent(plant.PlantName) + '">';
          if (plant.Image && plant.Image.trim() !== "") {
            listHtml += '<img src="' + plant.Image + '" alt="' + plant.PlantName + '"> ';
          } else {
            listHtml += '<img src="<?php echo url_for('/img/default.jpeg'); ?>" alt="' + plant.PlantName + '"> ';
          }
          listHtml += '<div class="info"><h3>' + plant.PlantName + '</h3><h4>' + plant.Family + '</h4></div></a></li>';
        });
        listHtml += '</ul>';
        plantListDiv.innerHTML = listHtml;
      }

      // pagination buttons...
      let paginationHtml = '';
      if (currentPage === 1) {
        paginationHtml += '<button class="disabled" disabled>First</button> ';
        paginationHtml += '<button class="disabled" disabled>Previous</button> ';
      } else {
        paginationHtml += '<button onclick="loadPlants(1)">First</button> ';
        paginationHtml += '<button onclick="loadPlants(' + (currentPage - 1) + ')">Previous</button> ';
      }

      let startPage = Math.max(1, currentPage - 2);
      let endPage = startPage + 4;
      if (endPage > totalPages) {
        endPage = totalPages;
        startPage = Math.max(1, endPage - 4);
      }

      for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
          paginationHtml += '<button id="current" class="disabled" disabled>' + i + '</button> ';
        } else {
          paginationHtml += '<button onclick="loadPlants(' + i + ')">' + i + '</button> ';
        }
      }

      if (currentPage === totalPages) {
        paginationHtml += '<button class="disabled" disabled>Next</button> ';
        paginationHtml += '<button class="disabled" disabled>Last</button>';
      } else {
        paginationHtml += '<button onclick="loadPlants(' + (currentPage + 1) + ')">Next</button> ';
        paginationHtml += '<button onclick="loadPlants(' + totalPages + ')">Last</button>';
      }

      paginationDiv.innerHTML = paginationHtml;
    })
    .catch(error => {
      plantListDiv.innerHTML = '<p>Error loading plants.</p>';
    });
}


  // global function so pagination buttons can call it
  window.loadPlants = loadPlants;
  
  // Load the first page on initial load
  loadPlants(currentPage);
});
</script>

<?php include(SHARED_PATH . '/public_footer.php'); ?>