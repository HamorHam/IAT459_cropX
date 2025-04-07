<?php 
require_once('../private/initialize.php'); 
$page_title = 'CropX Home';

if (isset($_SESSION['username'])) {
  include(SHARED_PATH . '/member_header.php');
} else {
  include(SHARED_PATH . '/public_header.php');
}
?>


<div id="content">
  <h1>Welcome to CropX</h1>
  <p>This is the crop wiki, where you can browse information about various crops.</p>
  
  <div id="plant-list">
    <p>Loading plants...</p>
  </div>
  
  <div id="pagination">
    <!-- pagination buttons will show here -->
  </div>
</div>

<script>
// js to load plants with pagination controls
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 1;
  const pageSize = 10;
  const plantListDiv = document.getElementById('plant-list');
  const paginationDiv = document.getElementById('pagination');

  // to load plants for a given page
  function loadPlants(page) {
    fetch("<?php echo url_for('/api/plant_list.php'); ?>?page=" + page + "&limit=" + pageSize)
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
            listHtml += '<li><a href="<?php echo url_for('/plant.php'); ?>?plant=' + 
                        encodeURIComponent(plant.PlantName) + '">' + 
                        plant.PlantName + ' (' + plant.Family + ')</a></li>';
          });
          listHtml += '</ul>';
          plantListDiv.innerHTML = listHtml;
        }

        // build pagination controls
        let paginationHtml = '';
        if (currentPage > 1) {
          paginationHtml += '<button onclick="loadPlants(1)">First</button> ';
          paginationHtml += '<button onclick="loadPlants(' + (currentPage - 1) + ')">Previous</button> ';
        }
        if (currentPage < totalPages) {
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