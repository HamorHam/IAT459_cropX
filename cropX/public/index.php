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
  const pageSize = 12;
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
            listHtml += '<li><a href="<?php echo url_for('/plant.php'); ?>?plant=' + encodeURIComponent(plant.PlantName) + '">';
            // If plant image exists (non-empty), use it... otherwise use default image.
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

        // Build pagination controls
        let paginationHtml = '';
        // "First" and "Previous" buttons
        if (currentPage === 1) {
          paginationHtml += '<button class="disabled" disabled>First</button> ';
          paginationHtml += '<button class="disabled" disabled>Previous</button> ';
        } else {
          paginationHtml += '<button onclick="loadPlants(1)">First</button> ';
          paginationHtml += '<button onclick="loadPlants(' + (currentPage - 1) + ')">Previous</button> ';
        }
        // Calculate page number range (display 5 pages including the current page)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = startPage + 4;
        if (endPage > totalPages) {
          endPage = totalPages;
          startPage = Math.max(1, endPage - 4);
        }
        // Display page number buttons
        for (let i = startPage; i <= endPage; i++) {
          if (i === currentPage) {
            paginationHtml += '<button id="current" class="disabled" disabled>' + i + '</button> ';
          } else {
            paginationHtml += '<button onclick="loadPlants(' + i + ')">' + i + '</button> ';
          }
        }
        // "Next" and "Last" buttons
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