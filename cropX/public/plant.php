<?php
require_once('../private/initialize.php');

$plant_name = $_GET['plant'] ?? '';

if (empty($plant_name)) {
  redirect_to(url_for('/index.php'));
}

// get plant details
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

//get users
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE UserID = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
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
        <hr class="line" />
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
        <img class="imgCard" style="grid-column:span 2" src="<?php echo h($plant['Image']); ?>"
          alt="<?php echo h($plant['PlantName']); ?>">
      <?php else: ?>
        <img class="imgCard" style="grid-column:span 2" src="<?php echo url_for('/img/default.jpeg'); ?>"
          alt="Default Image">
      <?php endif; ?>

      <div class="card" style="grid-column:span 2">
        <h3>Temperature</h3>
        <?php
        $absMin = $plant['TempRequiredAbsoluteMin'];
        $optMin = $plant['TempRequiredOptimalMin'];
        $optMax = $plant['TempRequiredOptimalMax'];
        $absMax = $plant['TempRequiredAbsoluteMax'];

        if (
          is_numeric($absMin) && is_numeric($optMin) &&
          is_numeric($optMax) && is_numeric($absMax) &&
          ($absMax - $absMin) > 0
        ) {
          $absMin = ($absMin == intval($absMin)) ? intval($absMin) : $absMin;
          $optMin = ($optMin == intval($optMin)) ? intval($optMin) : $optMin;
          $optMax = ($optMax == intval($optMax)) ? intval($optMax) : $optMax;
          $absMax = ($absMax == intval($absMax)) ? intval($absMax) : $absMax;

          $range = $absMax - $absMin;
          $leftOpt = (($optMin - $absMin) / $range) * 100;
          $optWidth = (($optMax - $optMin) / $range) * 100;
          ?>
          <div class="mainBar">
            <div class="optimal-range" style="left: <?php echo $leftOpt; ?>%; width: <?php echo $optWidth; ?>%"></div>
          </div>
          <div class="rangeLabels">
            <span><?php echo h($absMin); ?>°C</span>
            <span><?php echo h($optMin); ?>°C</span>
            <span><?php echo h($optMax); ?>°C</span>
            <span><?php echo h($absMax); ?>°C</span>
          </div>
          <table>
            <tr>
              <th>Optimal</th>
              <td class="rightAlign"><?php echo h($optMin); ?> - <?php echo h($optMax); ?>°C</td>
            </tr>
            <tr>
              <th>Absolute</th>
              <td class="rightAlign"><?php echo h($absMin); ?> - <?php echo h($absMax); ?>°C</td>
            </tr>
          </table>
        <?php } else { ?>
          <p>No temperature information available for this plant.</p>
        <?php } ?>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Rainfall</h3>
        <?php
        $absMin = $plant['RainfallAnnualAbsoluteMin'];
        $optMin = $plant['RainfallAnnualOptimalMin'];
        $optMax = $plant['RainfallAnnualOptimalMax'];
        $absMax = $plant['RainfallAnnualAbsoluteMax'];

        if (
          is_numeric($absMin) && is_numeric($optMin) &&
          is_numeric($optMax) && is_numeric($absMax) &&
          ($absMax - $absMin) > 0
        ) {
          $absMin = ($absMin == intval($absMin)) ? intval($absMin) : $absMin;
          $optMin = ($optMin == intval($optMin)) ? intval($optMin) : $optMin;
          $optMax = ($optMax == intval($optMax)) ? intval($optMax) : $optMax;
          $absMax = ($absMax == intval($absMax)) ? intval($absMax) : $absMax;

          $range = $absMax - $absMin;
          $leftOpt = (($optMin - $absMin) / $range) * 100;
          $optWidth = (($optMax - $optMin) / $range) * 100;
          ?>
          <div class="mainBar">
            <div class="optimal-range" style="left: <?php echo $leftOpt; ?>%; width: <?php echo $optWidth; ?>%"></div>
          </div>
          <div class="rangeLabels">
            <span><?php echo h($absMin); ?> mm</span>
            <span><?php echo h($optMin); ?> mm</span>
            <span><?php echo h($optMax); ?> mm</span>
            <span><?php echo h($absMax); ?> mm</span>
          </div>
          <table>
            <tr>
              <th>Optimal</th>
              <td class="rightAlign"><?php echo h($optMin); ?> - <?php echo h($optMax); ?> mm</td>
            </tr>
            <tr>
              <th>Absolute</th>
              <td class="rightAlign"><?php echo h($absMin); ?> - <?php echo h($absMax); ?> mm</td>
            </tr>
          </table>
        <?php } else { ?>
          <p>No rainfall information available for this plant.</p>
        <?php } ?>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Light Intensity</h3>
        <?php
        $absMin = $plant['LightIntensityAbsoluteMin'];
        $optMin = $plant['LightIntensityOptimalMin'];
        $optMax = $plant['LightIntensityOptimalMax'];
        $absMax = $plant['LightIntensityAbsoluteMax'];

        if (
          is_numeric($absMin) && is_numeric($optMin) &&
          is_numeric($optMax) && is_numeric($absMax) &&
          ($absMax - $absMin) > 0
        ) {
          $absMin = ($absMin == intval($absMin)) ? intval($absMin) : $absMin;
          $optMin = ($optMin == intval($optMin)) ? intval($optMin) : $optMin;
          $optMax = ($optMax == intval($optMax)) ? intval($optMax) : $optMax;
          $absMax = ($absMax == intval($absMax)) ? intval($absMax) : $absMax;

          $range = $absMax - $absMin;
          $leftOpt = (($optMin - $absMin) / $range) * 100;
          $optWidth = (($optMax - $optMin) / $range) * 100;
          ?>
          <div class="mainBar">
            <div class="optimal-range" 
                style="left: <?php echo $leftOpt; ?>%; width: <?php echo $optWidth; ?>%">
            </div>
          </div>
          <div class="rangeLabels">
            <span><?php echo h($absMin); ?> µmol/m²/s</span>
            <span><?php echo h($optMin); ?> µmol/m²/s</span>
            <span><?php echo h($optMax); ?> µmol/m²/s</span>
            <span><?php echo h($absMax); ?> µmol/m²/s</span>
          </div>
          <table>
            <tr>
              <th>Optimal</th>
              <td class="rightAlign"><?php echo h($optMin); ?> - <?php echo h($optMax); ?> µmol/m²/s</td>
            </tr>
            <tr>
              <th>Absolute</th>
              <td class="rightAlign"><?php echo h($absMin); ?> - <?php echo h($absMax); ?> µmol/m²/s</td>
            </tr>
          </table>
        <?php
        } else {
          echo "<p>No light intensity information available for this plant.</p>";
        }
        ?>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Latitude</h3>
        <?php
          $absMin = $plant['LatitudeAbsoluteMin'];
          $absMax = $plant['LatitudeAbsoluteMax'];
          $optMin = $plant['LatitudeOptimalMin'];
          $optMax = $plant['LatitudeOptimalMax'];

          $has_any_data = is_numeric($absMin) && is_numeric($absMax) && is_numeric($optMin) && is_numeric($optMax);

          $show_abs = ($absMin != 0 || $absMax != 0);
          $show_opt = ($optMin != 0 || $optMax != 0);
        ?>

        <?php if ($has_any_data && ($show_abs || $show_opt)): ?>
          <div class="map-wrapper">
            <img src="/IAT459_CROPX/cropX/public/img/lat-map.png" alt="World Map" class="map-image" />
            <?php if ($show_abs): ?>
              <div class="lat-band abs-range" 
                  style="top: <?php echo 50 - ($absMax * 50 / 90); ?>%; 
                        height: <?php echo ($absMax - $absMin) * 50 / 90; ?>%;">
              </div>
            <?php endif; ?>
            <?php if ($show_opt): ?>
              <div class="lat-band opt-range" 
                  style="top: <?php echo 50 - ($optMax * 50 / 90); ?>%; 
                        height: <?php echo ($optMax - $optMin) * 50 / 90; ?>%;">
              </div>
            <?php endif; ?>
            <div class="user-latitude" 
              style="top: <?php echo 50 - ($user['Latitude'] * 50/90); ?>%;">
            </div>
          </div>
          <table>
            <?php if ($show_opt): ?>
              <tr>
                <th>Optimal</th>
                <td class="rightAlign"><?php echo h($optMin); ?> - <?php echo h($optMax); ?>°</td>
              </tr>
            <?php endif; ?>
            <?php if ($show_abs): ?>
              <tr>
                <th>Absolute</th>
                <td class="rightAlign"><?php echo h($absMin); ?> - <?php echo h($absMax); ?>°</td>
              </tr>
            <?php endif; ?>
            <?php if ($user['Latitude']): ?>
              <tr>
                <th>Your Latitude</th>
                <td class="rightAlign"><?php echo $user['Latitude']; ?>°</td>
              </tr>
            <?php endif; ?>
          </table>
        <?php else: ?>
          <p>No latitude information available for this plant.</p>
        <?php endif; ?>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Environmental Conditions</h3>
        <hr class="line"/>
        <table>
          <?php if ($plant['KillingTemp_DuringRest'] !== null): ?>
            <tr>
              <th>Killing Temp (During Rest)</th>
              <td><?php echo h($plant['KillingTemp_DuringRest']); ?> °C</td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['KillingTemp_EarlyGrowth'] !== null): ?>
            <tr>
              <th>Killing Temp (Early Growth)</th>
              <td><?php echo h($plant['KillingTemp_EarlyGrowth']); ?> °C</td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['Photoperiod']): ?>
            <tr>
              <th>Photoperiod</th>
              <td><?php echo h($plant['Photoperiod']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['ClimateZone']): ?>
            <tr>
              <th>Climate Zone</th>
              <td><?php echo h($plant['ClimateZone']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['AltitudeAbsoluteMin'] !== null && $plant['AltitudeAbsoluteMax'] !== null): ?>
            <tr>
              <th>Altitude (Absolute)</th>
              <td><?php echo h($plant['AltitudeAbsoluteMin']); ?> - <?php echo h($plant['AltitudeAbsoluteMax']); ?> m</td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilAlToxOptimal']): ?>
            <tr>
              <th>Soil Al Toxicity (Optimal)</th>
              <td><?php echo h($plant['SoilAlToxOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilAlToxAbsolute']): ?>
            <tr>
              <th>Soil Al Toxicity (Absolute)</th>
              <td><?php echo h($plant['SoilAlToxAbsolute']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['AbioticTolerance']): ?>
            <tr>
              <th>Abiotic Tolerance</th>
              <td><?php echo h($plant['AbioticTolerance']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['AbioticSuscept']): ?>
            <tr>
              <th>Abiotic Susceptibility</th>
              <td><?php echo h($plant['AbioticSuscept']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['IntroductionRisks']): ?>
            <tr>
              <th>Introduction Risks</th>
              <td><?php echo h($plant['IntroductionRisks']); ?></td>
            </tr>
          <?php endif; ?>
        </table>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Soil Properties</h3>
        <hr class="line"/>
        <table>
          <?php if ($plant['SoilPHOptimalMin'] !== null && $plant['SoilPHOptimalMax'] !== null): ?>
            <tr>
              <th>Soil pH (Optimal)</th>
              <td><?php echo h($plant['SoilPHOptimalMin']); ?> - <?php echo h($plant['SoilPHOptimalMax']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilPHAbsoluteMin'] !== null && $plant['SoilPHAbsoluteMax'] !== null): ?>
            <tr>
              <th>Soil pH (Absolute)</th>
              <td><?php echo h($plant['SoilPHAbsoluteMin']); ?> - <?php echo h($plant['SoilPHAbsoluteMax']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilDepthOptimal']): ?>
            <tr>
              <th>Soil Depth (Optimal)</th>
              <td><?php echo h($plant['SoilDepthOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilDepthAbsolute']): ?>
            <tr>
              <th>Soil Depth (Absolute)</th>
              <td><?php echo h($plant['SoilDepthAbsolute']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilTextureOptimal']): ?>
            <tr>
              <th>Soil Texture (Optimal)</th>
              <td><?php echo h($plant['SoilTextureOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilTextureAbsolute']): ?>
            <tr>
              <th>Soil Texture (Absolute)</th>
              <td><?php echo h($plant['SoilTextureAbsolute']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilFertilityOptimal']): ?>
            <tr>
              <th>Soil Fertility (Optimal)</th>
              <td><?php echo h($plant['SoilFertilityOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilFertilityAbsolute']): ?>
            <tr>
              <th>Soil Fertility (Absolute)</th>
              <td><?php echo h($plant['SoilFertilityAbsolute']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilSalinityOptimal']): ?>
            <tr>
              <th>Soil Salinity (Optimal)</th>
              <td><?php echo h($plant['SoilSalinityOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilSalinityAbsolute']): ?>
            <tr>
              <th>Soil Salinity (Absolute)</th>
              <td><?php echo h($plant['SoilSalinityAbsolute']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilDrainageOptimal']): ?>
            <tr>
              <th>Soil Drainage (Optimal)</th>
              <td><?php echo h($plant['SoilDrainageOptimal']); ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($plant['SoilDrainageAbsolute']): ?>
            <tr>
              <th>Soil Drainage (Absolute)</th>
              <td><?php echo h($plant['SoilDrainageAbsolute']); ?></td>
            </tr>
          <?php endif; ?>
        </table>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>General Plant Info</h3>
        <hr class="line"/>
        <table>
          <?php if ($plant['LifeForm']): ?>
            <tr><th>Life Form</th><td><?php echo h($plant['LifeForm']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['Physiology']): ?>
            <tr><th>Physiology</th><td><?php echo h($plant['Physiology']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['Habit']): ?>
            <tr><th>Habit</th><td><?php echo h($plant['Habit']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['Category']): ?>
            <tr><th>Category</th><td><?php echo h($plant['Category']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['LifeSpan']): ?>
            <tr><th>Life Span</th><td><?php echo h($plant['LifeSpan']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['PlantAttributes']): ?>
            <tr><th>Plant Attributes</th><td><?php echo h($plant['PlantAttributes']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['GrowingPeriod']): ?>
            <tr><th>Growing Period</th><td><?php echo h($plant['GrowingPeriod']); ?></td></tr>
          <?php endif; ?>

          <?php if ($plant['CropCycle_Min'] !== null && $plant['CropCycle_Max'] !== null): ?>
            <tr><th>Crop Cycle</th><td><?php echo h($plant['CropCycle_Min']); ?> - <?php echo h($plant['CropCycle_Max']); ?> days</td></tr>
          <?php endif; ?>

          <?php if ($plant['ProductSystem']): ?>
            <tr><th>Product System</th><td><?php echo h($plant['ProductSystem']); ?></td></tr>
          <?php endif; ?>
        </table>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Further Information</h3>
        <hr class="line"/>
        <?php if ($plant['FurtherInformation']): ?>
          <p><?php echo nl2br(h($plant['FurtherInformation'])); ?></p>
        <?php else: ?>
          <p>No additional background information available for this plant.</p>
        <?php endif; ?>
      </div>

      <div class="card" style="grid-column:span 2">
        <h3>Sources</h3>
        <hr class="line"/>
        <?php if ($plant['FinalSource']): ?>
          <p><?php echo nl2br(h($plant['FinalSource'])); ?></p>
        <?php endif; ?>
      </div>

      <div class="card" style="grid-column:span 3">
        <h3>Uses</h3>
        <hr class="line"/>
        <?php
          // Retrieve uses details
          $uses_query = "SELECT UseID, MainUse, DetailedUse, UsedPart FROM plant_uses 
                        WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' 
                        ORDER BY UseID";
          $uses_result = mysqli_query($db, $uses_query);
          if (mysqli_num_rows($uses_result) > 0 || $plant['Uses'] != NULL):
            ?>
            <?php echo "<p>" . ($plant['Uses']) . "</p>"; ?>
            <table>
              <tr>
                <th>Main Use</th>
                <th>Detailed Use</th>
                <th>Used Part</th>
              </tr>
              <?php while ($use = mysqli_fetch_assoc($uses_result)): ?>
                <tr>
                  <td><?php echo h($use['MainUse']); ?></td>
                  <td><?php echo h($use['DetailedUse']); ?></td>
                  <td><?php echo h($use['UsedPart']); ?></td>
                </tr>
              <?php endwhile; ?>
            </table>
          <?php else: ?>
            <p>No uses available for this plant.</p>
          <?php endif; ?>
      </div>

      <div class="card" style="grid-column:span 3">
        <h3>Specific Cultivation</h3>
        <hr class="line"/>
        <?php
          // Retrieve specific cultivation data
          $cultivation_query = "SELECT Subsystem, CompanionSpecies, LevelOfMechanization, LabourIntensity 
                                FROM specific_cultivation 
                                WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' 
                                ORDER BY CultivationID";
          $cultivation_result = mysqli_query($db, $cultivation_query);

          if (mysqli_num_rows($cultivation_result) > 0):
        ?>
          <table>
            <tr>
              <th>Subsystem</th>
              <th>Companion Species</th>
              <th>Level of Mechanization</th>
              <th>Labour Intensity</th>
            </tr>
            <?php while ($cult = mysqli_fetch_assoc($cultivation_result)): ?>
              <tr>
                <td><?php echo h($cult['Subsystem']); ?></td>
                <td><?php echo h($cult['CompanionSpecies']); ?></td>
                <td><?php echo h($cult['LevelOfMechanization']); ?></td>
                <td><?php echo h($cult['LabourIntensity']); ?></td>
              </tr>
            <?php endwhile; ?>
          </table>
        <?php else: ?>
          <p>No specific cultivation information available for this plant.</p>
        <?php endif; ?>
      </div>
    </div>

    <br/>
    <br/>
    <h3>Comments</h3>
    <hr class="line"/>
    <?php
    // Fetch all approved comments for this plant
    $comments_query = "SELECT c.*, u.Name AS UserName FROM comments c
                   LEFT JOIN user u ON c.UserID = u.UserID
                   WHERE c.PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "'
                     AND c.IsApproved = 1
                   ORDER BY c.CommentDate ASC";
    $comments_result = mysqli_query($db, $comments_query);

    // Organize comments by their parent ID to support nesting
    $comments_by_parent = [];
    while ($comment = mysqli_fetch_assoc($comments_result)) {
      $parent_id = $comment['ParentCommentID'] ?? 0;
      if (!isset($comments_by_parent[$parent_id])) {
        $comments_by_parent[$parent_id] = [];
      }
      $comments_by_parent[$parent_id][] = $comment;
    }

    // Recursive function to display comments and their replies
    function render_comments($parent_id, $comments_by_parent)
    {
      if (!isset($comments_by_parent[$parent_id]))
        return;
      echo "<ul>";
      foreach ($comments_by_parent[$parent_id] as $comment) {
        echo "<li>";
        echo "<p><strong>" . h($comment['UserName'] ?? 'Anonymous') . "</strong> on " . h($comment['CommentDate']) . "</p>";
        echo "<p>" . nl2br(h($comment['Content'])) . "</p>";
        echo "<button class='reply-btn' data-id='" . h($comment['CommentID']) . "'>Reply</button>";
        // Render nested replies
        render_comments($comment['CommentID'], $comments_by_parent);
        echo "</li>";
      }
      echo "</ul>";
    }

    // Start rendering from top-level comments (ParentCommentID = 0)
    if (isset($comments_by_parent[0])) {
      render_comments(0, $comments_by_parent);
    } else {
      echo "<p>No comments yet. Be the first to comment!</p>";
    }
    ?>

    <h3>Add a Comment</h3>
    <?php if (isset($_SESSION['username'])): ?>
      <div id="comment-response" style="color: green;"></div>

      <!-- Comment form for both top-level and reply submissions -->
      <form id="comment-form">
        <input type="hidden" name="plant_name" value="<?php echo h($plant_name); ?>" />
        <input type="hidden" name="parent_comment_id" id="parent_comment_id" value="0" />
        <label for="content">Comment:</label><br />
        <textarea name="content" required rows="5" cols="50"></textarea><br />
        <input type="submit" value="Submit Comment" />
      </form>
    <?php else: ?>
      <p>Please <a href="<?php echo url_for('/member/login.php'); ?>">login</a> to add a comment.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- JavaScript to handle comment submission and reply setup -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("comment-form");
    const responseDiv = document.getElementById("comment-response");
    const parentInput = document.getElementById("parent_comment_id");

    if (form) {
      document.querySelectorAll(".reply-btn").forEach(button => {
        button.addEventListener("click", function () {
          const commentID = this.getAttribute("data-id");
          parentInput.value = commentID;
          form.scrollIntoView({ behavior: "smooth" });
        });
      });

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
              parentInput.value = 0;
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