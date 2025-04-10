<?php
// index.php - CropX Home
require_once('../private/initialize.php');

// Set page title
$page_title = 'CropX Home';

// Check if user is logged in (session username exists)
if (isset($_SESSION['username'])) {
    include(SHARED_PATH . '/member_header.php');
    $user_id = $_SESSION['user_id'];
    // Get user's latitude from database
    $user_query = "SELECT Latitude FROM user WHERE UserID = " . intval($user_id) . " LIMIT 1";
    $user_result = mysqli_query($db, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $latitude = isset($user_data['Latitude']) ? floatval($user_data['Latitude']) : null;
} else {
    include(SHARED_PATH . '/public_header.php');
    $latitude = null;
}
?>

<div id="content">
  <!-- Filter button -->
  <a class="btn" style="margin-top:2em" href="<?php echo url_for('/filter.php'); ?>">Filter</a>
  
  <!-- Plant list container -->
  <div id="plant-list">
    <p>Loading plants...</p>
  </div>
  
  <!-- Pagination container -->
  <div id="pagination">
    <!-- Pagination buttons will be inserted here -->
  </div>
</div>

<script>
// Set user's latitude for sorting (if available)
const userLatitude = <?php echo json_encode($latitude); ?>;

// When DOM is loaded, begin fetching plant data
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 1;
  const pageSize = 12;
  const plantListDiv = document.getElementById('plant-list');
  const paginationDiv = document.getElementById('pagination');

  // Function to load plants for the given page
  function loadPlants(page) {
    const baseUrl = "<?php echo url_for('/api/plant_list.php'); ?>";
    let url = baseUrl + "?page=" + page + "&limit=" + pageSize;
    // Append latitude if provided
    if (userLatitude !== null) {
      url += "&latitude=" + userLatitude;
    }
    
    // Fetch JSON from the API
    fetch(url)
      .then(response => response.json())
      .then(data => {
        // Update current page and total pages
        currentPage = data.currentPage;
        const totalPages = data.totalPages;
        const plants = data.plants;

        // Build plant list HTML
        if (plants.length === 0) {
          plantListDiv.innerHTML = '<p>No plants found.</p>';
        } else {
          let listHtml = '<ul>';
          plants.forEach(plant => {
            listHtml += '<li><a href="<?php echo url_for('/plant.php'); ?>?plant=' + encodeURIComponent(plant.PlantName) + '">';
            // Display plant image if provided, else a default image
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
        // First & Previous buttons
        if (currentPage === 1) {
          paginationHtml += '<button class="disabled" disabled>First</button> ';
          paginationHtml += '<button class="disabled" disabled>Previous</button> ';
        } else {
          paginationHtml += '<button onclick="loadPlants(1)">First</button> ';
          paginationHtml += '<button onclick="loadPlants(' + (currentPage - 1) + ')">Previous</button> ';
        }
        
        // Display 5 page numbers centered on the current page
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
        
        // Next & Last buttons
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
  
  // Expose loadPlants globally so pagination buttons can call it
  window.loadPlants = loadPlants;
  
  // Load first page on initial load
  loadPlants(currentPage);
});
</script>

<?php include(SHARED_PATH . '/public_footer.php'); ?>