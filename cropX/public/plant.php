<?php
require_once('../private/initialize.php');

$plant_name = $_GET['plant'] ?? '';

if (empty($plant_name)) {
  redirect_to(url_for('/index.php'));
}

// Retrieve plant details
$plant_query = "SELECT * FROM plant WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' LIMIT 1";
$plant_result = mysqli_query($db, $plant_query);
$plant = mysqli_fetch_assoc($plant_result);

if (!$plant) {
  $error_message = "Plant not found.";
}

$page_title = $plant ? h($plant['PlantName']) : 'Plant Not Found';

if (isset($_SESSION['username'])) {
  include(SHARED_PATH . '/member_header.php');
} else {
  include(SHARED_PATH . '/public_header.php');
}
?>

<?php
// Process comment submission on the same page
$comment_message = '';
if (is_post_request() && isset($_POST['content'])) {
  // Check if user is logged in
  if (isset($_SESSION['username']) && !empty($_SESSION['user_id'])) {
    $plant_name = $_POST['plant_name'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($plant_name) || empty($content)) {
      $comment_message = "Plant name and comment content are required.";
    } else {
      // Insert the comment (default IsApproved = 0)
      $user_id = $_SESSION['user_id'];
      $query = "INSERT INTO comments (PlantName, UserID, CommentDate, IsApproved, Content)
                VALUES (
                  '" . mysqli_real_escape_string($db, $plant_name) . "',
                  '" . mysqli_real_escape_string($db, $user_id) . "',
                  NOW(),
                  0,
                  '" . mysqli_real_escape_string($db, $content) . "'
                )";
      if (mysqli_query($db, $query)) {
        $comment_message = "Comment submitted and pending approval.";
      } else {
        $comment_message = "Failed to submit comment: " . mysqli_error($db);
      }
    }
  } else {
    $comment_message = "You must be logged in to submit a comment.";
  }
}
?>

<div id="content">
  <?php if (isset($error_message)): ?>
    <p><?php echo h($error_message); ?></p>
  <?php else: ?>
    <h1><?php echo h($plant['PlantName']); ?></h1>

    <div id="detail-cards">
      <div class="card" style="grid-column:span 4">
        <h3>General Information</h3>
        <hr class="line"/>
        <table>
          <tr>
            <th>Plant Name</th>
            <td><?php echo h($plant['PlantName']); ?></td>
          </tr>
          <tr>
            <th>Family</th>
            <td><?php echo h($plant['Family']); ?></td>
          </tr>
          <tr>
            <th>Synonyms</th>
            <td><?php echo h($plant['Synonyms']); ?></td>
          </tr>
          <tr>
            <th>Common Names</th>
            <td><?php echo h($plant['CommonNames']); ?></td>
          </tr>
          <tr>
            <th>Description</th>
            <td><?php echo h($plant['Description']); ?></td>
          </tr>
        </table>
      </div>

      <?php if (isset($plant['Image']) && trim($plant['Image']) !== ""): ?>
        <img class="imgCard" style="grid-column:span 2" src="<?php echo h($plant['Image']); ?>" alt="<?php echo h($plant['PlantName']); ?>">
      <?php else: ?>
        <img class="imgCard" style="grid-column:span 2" src="<?php echo url_for('/img/default.jpeg'); ?>" alt="Default Image">
      <?php endif; ?>
    </div>







    <h2>General Information</h2>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>Uses</th>
        <td><?php echo h($plant['Uses']); ?></td>
      </tr>
      <tr>
        <th>Growing Period</th>
        <td><?php echo h($plant['GrowingPeriod']); ?></td>
      </tr>
      <tr>
        <th>Further Information</th>
        <td><?php echo h($plant['FurtherInformation']); ?></td>
      </tr>
      <tr>
        <th>Final Source</th>
        <td><?php echo h($plant['FinalSource']); ?></td>
      </tr>
      <tr>
        <th>Image</th>
        <td><?php echo h($plant['Image']); ?></td>
      </tr>
      <tr>
        <th>Life Form</th>
        <td><?php echo h($plant['LifeForm']); ?></td>
      </tr>
      <tr>
        <th>Physiology</th>
        <td><?php echo h($plant['Physiology']); ?></td>
      </tr>
      <tr>
        <th>Habit</th>
        <td><?php echo h($plant['Habit']); ?></td>
      </tr>
      <tr>
        <th>Category</th>
        <td><?php echo h($plant['Category']); ?></td>
      </tr>
      <tr>
        <th>Life Span</th>
        <td><?php echo h($plant['LifeSpan']); ?></td>
      </tr>
      <tr>
        <th>Plant Attributes</th>
        <td><?php echo h($plant['PlantAttributes']); ?></td>
      </tr>
      <tr>
        <th>Temp Required Optimal (Min)</th>
        <td><?php echo h($plant['TempRequiredOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Temp Required Optimal (Max)</th>
        <td><?php echo h($plant['TempRequiredOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Temp Required Absolute (Min)</th>
        <td><?php echo h($plant['TempRequiredAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Temp Required Absolute (Max)</th>
        <td><?php echo h($plant['TempRequiredAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Killing Temp During Rest</th>
        <td><?php echo h($plant['KillingTemp_DuringRest']); ?></td>
      </tr>
      <tr>
        <th>Killing Temp Early Growth</th>
        <td><?php echo h($plant['KillingTemp_EarlyGrowth']); ?></td>
      </tr>
      <tr>
        <th>Rainfall Annual Optimal (Min)</th>
        <td><?php echo h($plant['RainfallAnnualOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Rainfall Annual Optimal (Max)</th>
        <td><?php echo h($plant['RainfallAnnualOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Rainfall Annual Absolute (Min)</th>
        <td><?php echo h($plant['RainfallAnnualAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Rainfall Annual Absolute (Max)</th>
        <td><?php echo h($plant['RainfallAnnualAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Light Intensity Optimal (Min)</th>
        <td><?php echo h($plant['LightIntensityOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Light Intensity Optimal (Max)</th>
        <td><?php echo h($plant['LightIntensityOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Light Intensity Absolute (Min)</th>
        <td><?php echo h($plant['LightIntensityAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Light Intensity Absolute (Max)</th>
        <td><?php echo h($plant['LightIntensityAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Photoperiod</th>
        <td><?php echo h($plant['Photoperiod']); ?></td>
      </tr>
      <tr>
        <th>Soil pH Optimal (Min)</th>
        <td><?php echo h($plant['SoilPHOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Soil pH Optimal (Max)</th>
        <td><?php echo h($plant['SoilPHOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Soil Depth Optimal</th>
        <td><?php echo h($plant['SoilDepthOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Depth Absolute</th>
        <td><?php echo h($plant['SoilDepthAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Soil Texture Optimal</th>
        <td><?php echo h($plant['SoilTextureOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Texture Absolute</th>
        <td><?php echo h($plant['SoilTextureAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Soil Fertility Optimal</th>
        <td><?php echo h($plant['SoilFertilityOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Fertility Absolute</th>
        <td><?php echo h($plant['SoilFertilityAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Soil Salinity Optimal</th>
        <td><?php echo h($plant['SoilSalinityOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Salinity Absolute</th>
        <td><?php echo h($plant['SoilSalinityAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Soil Drainage Optimal</th>
        <td><?php echo h($plant['SoilDrainageOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Drainage Absolute</th>
        <td><?php echo h($plant['SoilDrainageAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Latitude Optimal (Min)</th>
        <td><?php echo h($plant['LatitudeOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Latitude Absolute (Min)</th>
        <td><?php echo h($plant['LatitudeAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Latitude Optimal (Max)</th>
        <td><?php echo h($plant['LatitudeOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Latitude Absolute (Max)</th>
        <td><?php echo h($plant['LatitudeAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Altitude Optimal (Min)</th>
        <td><?php echo h($plant['AltitudeOptimalMin']); ?></td>
      </tr>
      <tr>
        <th>Altitude Optimal (Max)</th>
        <td><?php echo h($plant['AltitudeOptimalMax']); ?></td>
      </tr>
      <tr>
        <th>Climate Zone</th>
        <td><?php echo h($plant['ClimateZone']); ?></td>
      </tr>
      <tr>
        <th>Altitude Absolute (Min)</th>
        <td><?php echo h($plant['AltitudeAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Altitude Absolute (Max)</th>
        <td><?php echo h($plant['AltitudeAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Soil pH Absolute (Min)</th>
        <td><?php echo h($plant['SoilPHAbsoluteMin']); ?></td>
      </tr>
      <tr>
        <th>Soil pH Absolute (Max)</th>
        <td><?php echo h($plant['SoilPHAbsoluteMax']); ?></td>
      </tr>
      <tr>
        <th>Soil Al Tox Optimal</th>
        <td><?php echo h($plant['SoilAlToxOptimal']); ?></td>
      </tr>
      <tr>
        <th>Soil Al Tox Absolute</th>
        <td><?php echo h($plant['SoilAlToxAbsolute']); ?></td>
      </tr>
      <tr>
        <th>Abiotic Tolerance</th>
        <td><?php echo h($plant['AbioticTolerance']); ?></td>
      </tr>
      <tr>
        <th>Abiotic Suscept</th>
        <td><?php echo h($plant['AbioticSuscept']); ?></td>
      </tr>
      <tr>
        <th>Introduction Risks</th>
        <td><?php echo h($plant['IntroductionRisks']); ?></td>
      </tr>
      <tr>
        <th>Product System</th>
        <td><?php echo h($plant['ProductSystem']); ?></td>
      </tr>
      <tr>
        <th>Crop Cycle (Min)</th>
        <td><?php echo h($plant['CropCycle_Min']); ?></td>
      </tr>
      <tr>
        <th>Crop Cycle (Max)</th>
        <td><?php echo h($plant['CropCycle_Max']); ?></td>
      </tr>
    </table>

    <h2>Uses</h2>
    <?php
    // Retrieve uses details for this plant
    $uses_query = "SELECT UseID, MainUse, DetailedUse, UsedPart FROM plant_uses 
                   WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' 
                   ORDER BY UseID";
    $uses_result = mysqli_query($db, $uses_query);
    if (mysqli_num_rows($uses_result) > 0):
      ?>
      <table border="1" cellspacing="0" cellpadding="5">
        <tr>
          <th>Use ID</th>
          <th>Main Use</th>
          <th>Detailed Use</th>
          <th>Used Part</th>
        </tr>
        <?php while ($use = mysqli_fetch_assoc($uses_result)): ?>
          <tr>
            <td><?php echo h($use['UseID']); ?></td>
            <td><?php echo h($use['MainUse']); ?></td>
            <td><?php echo h($use['DetailedUse']); ?></td>
            <td><?php echo h($use['UsedPart']); ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No uses available for this plant.</p>
    <?php endif; ?>

    <h2>Specific Cultivation Details</h2>
    <?php
    // Retrieve cultivation details for this plant
    $cultivation_query = "SELECT * FROM specific_cultivation 
                          WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' 
                          ORDER BY CultivationID";
    $cultivation_result = mysqli_query($db, $cultivation_query);
    if (mysqli_num_rows($cultivation_result) > 0):
      // Assuming additional columns exist in specific_cultivation aside from PlantName
      // Here we build a header manually using the keys from the first row (except PlantName)
      $firstCultivation = mysqli_fetch_assoc($cultivation_result);
      ?>
      <table border="1" cellspacing="0" cellpadding="5">
        <tr>
          <?php if (isset($firstCultivation['CultivationID'])): ?>
            <th>Cultivation ID</th>
          <?php endif; ?>
          <?php
          foreach ($firstCultivation as $field => $value) {
            if ($field == 'PlantName' || $field == 'CultivationID') {
              continue;
            }
            echo "<th>" . h($field) . "</th>";
          }
          ?>
        </tr>
        <tr>
          <?php if (isset($firstCultivation['CultivationID'])): ?>
            <td><?php echo h($firstCultivation['CultivationID']); ?></td>
          <?php endif; ?>
          <?php
          foreach ($firstCultivation as $field => $value) {
            if ($field == 'PlantName' || $field == 'CultivationID') {
              continue;
            }
            echo "<td>" . h($value) . "</td>";
          }
          ?>
        </tr>
        <?php while ($cultivation = mysqli_fetch_assoc($cultivation_result)): ?>
          <tr>
            <?php if (isset($cultivation['CultivationID'])): ?>
              <td><?php echo h($cultivation['CultivationID']); ?></td>
            <?php endif; ?>
            <?php
            foreach ($cultivation as $field => $value) {
              if ($field == 'PlantName' || $field == 'CultivationID') {
                continue;
              }
              echo "<td>" . h($value) . "</td>";
            }
            ?>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No cultivation details available for this plant.</p>
    <?php endif; ?>

    <h2>Comments</h2>
    <?php
    // Retrieve approved comments for this plant
    $comments_query = "SELECT c.*, u.Name AS UserName FROM comments c 
                       LEFT JOIN user u ON c.UserID = u.UserID 
                       WHERE c.PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' 
                         AND c.IsApproved = 1
                       ORDER BY c.CommentDate ASC";
    $comments_result = mysqli_query($db, $comments_query);
    if (mysqli_num_rows($comments_result) > 0):
      ?>
      <ul>
        <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
          <li>
            <p><strong><?php echo h($comment['UserName'] ?? 'Anonymous'); ?></strong> on
              <?php echo h($comment['CommentDate']); ?></p>
            <p><?php echo nl2br(h($comment['Content'])); ?></p>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p>No comments yet. Be the first to comment!</p>
    <?php endif; ?>

    <h2>Add a Comment</h2>
    <?php if (isset($_SESSION['username'])): ?>
      <div id="comment-response" style="color: green;"></div>
      <form id="comment-form">
        <input type="hidden" name="plant_name" value="<?php echo h($plant_name); ?>" />
        <label for="content">Comment:</label><br />
        <textarea name="content" required rows="5" cols="50"></textarea><br />
        <input type="submit" value="Submit Comment" />
      </form>
    <?php else: ?>
      <p>Please <a href="<?php echo url_for('/member/login.php'); ?>">login</a> to add a comment.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("comment-form");
  const responseDiv = document.getElementById("comment-response");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch("<?php echo url_for('/submit_comment.php'); ?>", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        responseDiv.style.color = data.status === "success" ? "green" : "red";
        responseDiv.textContent = data.message;
        if (data.status === "success") {
          form.reset();
        }
      })
      .catch(() => {
        responseDiv.style.color = "red";
        responseDiv.textContent = "Something went wrong. Please try again.";
      });
    });
  }
});
</script>

<?php include(SHARED_PATH . '/public_footer.php'); ?>