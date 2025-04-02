<?php
require_once('../../private/initialize.php');

// Ensure user is logged in.
if (!isset($_SESSION['username']) || empty($_SESSION['user_id'])) {
  redirect_to(url_for('/member/login.php'));
}

$errors = [];
$message = '';

// Get the plant name from GET parameter (if any).
$plant_name = $_GET['plant'] ?? '';
$current_plant = null;
if (!empty($plant_name)) {
  $plant_query  = "SELECT * FROM plant WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' LIMIT 1";
  $plant_result = mysqli_query($db, $plant_query);
  $current_plant = mysqli_fetch_assoc($plant_result);
}

if (is_post_request()) {
  // Gather the basic plant fields.
  $fields = [
    'PlantName'              => $_POST['PlantName'] ?? '',
    'Family'                 => $_POST['Family'] ?? '',
    'Synonyms'               => $_POST['Synonyms'] ?? '',
    'CommonNames'            => $_POST['CommonNames'] ?? '',
    'Description'            => $_POST['Description'] ?? '',
    'Uses'                   => $_POST['Uses'] ?? '', // Optional: if you want a summary field.
    'GrowingPeriod'          => $_POST['GrowingPeriod'] ?? '',
    'FurtherInformation'     => $_POST['FurtherInformation'] ?? '',
    'FinalSource'            => $_POST['FinalSource'] ?? '',
    'Image'                  => $_POST['Image'] ?? '',
    'LifeForm'               => $_POST['LifeForm'] ?? '',
    'Physiology'             => $_POST['Physiology'] ?? '',
    'Habit'                  => $_POST['Habit'] ?? '',
    'Category'               => $_POST['Category'] ?? '',
    'LifeSpan'               => $_POST['LifeSpan'] ?? '',
    'PlantAttributes'        => $_POST['PlantAttributes'] ?? '',
    // Temperature Requirements
    'TempRequiredOptimalMin'  => $_POST['TempRequiredOptimalMin'] ?? '',
    'TempRequiredOptimalMax'  => $_POST['TempRequiredOptimalMax'] ?? '',
    'TempRequiredAbsoluteMin' => $_POST['TempRequiredAbsoluteMin'] ?? '',
    'TempRequiredAbsoluteMax' => $_POST['TempRequiredAbsoluteMax'] ?? '',
    'KillingTemp_DuringRest'  => $_POST['KillingTemp_DuringRest'] ?? '',
    'KillingTemp_EarlyGrowth' => $_POST['KillingTemp_EarlyGrowth'] ?? '',
    // Rainfall Requirements
    'RainfallAnnualOptimalMin' => $_POST['RainfallAnnualOptimalMin'] ?? '',
    'RainfallAnnualOptimalMax' => $_POST['RainfallAnnualOptimalMax'] ?? '',
    'RainfallAnnualAbsoluteMin' => $_POST['RainfallAnnualAbsoluteMin'] ?? '',
    'RainfallAnnualAbsoluteMax' => $_POST['RainfallAnnualAbsoluteMax'] ?? '',
    // Light Requirements
    'LightIntensityOptimalMin' => $_POST['LightIntensityOptimalMin'] ?? '',
    'LightIntensityOptimalMax' => $_POST['LightIntensityOptimalMax'] ?? '',
    'LightIntensityAbsoluteMin' => $_POST['LightIntensityAbsoluteMin'] ?? '',
    'LightIntensityAbsoluteMax' => $_POST['LightIntensityAbsoluteMax'] ?? '',
    'Photoperiod'               => $_POST['Photoperiod'] ?? '',
    // Soil and Water Conditions
    'SoilPHOptimalMin'      => $_POST['SoilPHOptimalMin'] ?? '',
    'SoilPHOptimalMax'      => $_POST['SoilPHOptimalMax'] ?? '',
    'SoilDepthOptimal'      => $_POST['SoilDepthOptimal'] ?? '',
    'SoilDepthAbsolute'     => $_POST['SoilDepthAbsolute'] ?? '',
    'SoilTextureOptimal'    => $_POST['SoilTextureOptimal'] ?? '',
    'SoilTextureAbsolute'   => $_POST['SoilTextureAbsolute'] ?? '',
    'SoilFertilityOptimal'  => $_POST['SoilFertilityOptimal'] ?? '',
    'SoilFertilityAbsolute' => $_POST['SoilFertilityAbsolute'] ?? '',
    'SoilSalinityOptimal'   => $_POST['SoilSalinityOptimal'] ?? '',
    'SoilSalinityAbsolute'  => $_POST['SoilSalinityAbsolute'] ?? '',
    'SoilDrainageOptimal'   => $_POST['SoilDrainageOptimal'] ?? '',
    'SoilDrainageAbsolute'  => $_POST['SoilDrainageAbsolute'] ?? '',
    'SoilAlToxOptimal'      => $_POST['SoilAlToxOptimal'] ?? '',
    'SoilAlToxAbsolute'     => $_POST['SoilAlToxAbsolute'] ?? '',
    // Geographical and Climate Information
    'LatitudeOptimalMin'    => $_POST['LatitudeOptimalMin'] ?? '',
    'LatitudeAbsoluteMin'   => $_POST['LatitudeAbsoluteMin'] ?? '',
    'LatitudeOptimalMax'    => $_POST['LatitudeOptimalMax'] ?? '',
    'LatitudeAbsoluteMax'   => $_POST['LatitudeAbsoluteMax'] ?? '',
    'AltitudeOptimalMin'    => $_POST['AltitudeOptimalMin'] ?? '',
    'AltitudeOptimalMax'    => $_POST['AltitudeOptimalMax'] ?? '',
    'ClimateZone'           => $_POST['ClimateZone'] ?? '',
    'AltitudeAbsoluteMin'   => $_POST['AltitudeAbsoluteMin'] ?? '',
    'AltitudeAbsoluteMax'   => $_POST['AltitudeAbsoluteMax'] ?? '',
    'SoilPHAbsoluteMin'     => $_POST['SoilPHAbsoluteMin'] ?? '',
    'SoilPHAbsoluteMax'     => $_POST['SoilPHAbsoluteMax'] ?? '',
    // Additional Details
    'AbioticTolerance'      => $_POST['AbioticTolerance'] ?? '',
    'AbioticSuscept'        => $_POST['AbioticSuscept'] ?? '',
    'IntroductionRisks'     => $_POST['IntroductionRisks'] ?? '',
    'ProductSystem'         => $_POST['ProductSystem'] ?? '',
    'CropCycle_Min'         => $_POST['CropCycle_Min'] ?? '',
    'CropCycle_Max'         => $_POST['CropCycle_Max'] ?? ''
  ];

  // Validate required key field (at least PlantName).
  if (empty($fields['PlantName'])) {
    $errors[] = "Plant Name is required.";
  }
  
  // Process Plant Uses (one-to-many)
  $uses = [];
  // Allow up to 3 uses to be proposed.
  for ($i = 1; $i <= 3; $i++) {
    $main    = $_POST["UseMain_$i"] ?? '';
    $detailed = $_POST["UseDetailed_$i"] ?? '';
    $part    = $_POST["UsePart_$i"] ?? '';
    if (!empty($main) || !empty($detailed) || !empty($part)) {
      $uses[] = [
        "MainUse"     => $main,
        "DetailedUse" => $detailed,
        "UsedPart"    => $part
      ];
    }
  }
  $fields['PlantUses'] = $uses;
  
  // Process Cultivation details (one-to-many)
  $cultivation = [];
  // Allow up to 3 cultivation proposals.
  for ($i = 1; $i <= 3; $i++) {
    $method  = $_POST["CultivationMethod_$i"] ?? '';
    $details = $_POST["CultivationDetails_$i"] ?? '';
    if (!empty($method) || !empty($details)) {
      $cultivation[] = [
        "Method"  => $method,
        "Details" => $details
      ];
    }
  }
  $fields['Cultivation'] = $cultivation;
  
  if (empty($errors)) {
    // Convert the fields array into formatted JSON.
    $json_changes = json_encode($fields, JSON_PRETTY_PRINT);
    $user_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO plant_edit_proposal (PlantName, UserID, ProposedChanges)
              VALUES (
                '" . mysqli_real_escape_string($db, $fields['PlantName']) . "',
                '" . mysqli_real_escape_string($db, $user_id) . "',
                '" . mysqli_real_escape_string($db, $json_changes) . "'
              )";
              
    if (mysqli_query($db, $query)) {
      $message = "Edit proposal submitted successfully.";
    } else {
      $errors[] = "Failed to submit proposal: " . mysqli_error($db);
    }
  }
}

$page_title = 'Propose Edit';
include(SHARED_PATH . '/member_header.php');
?>

<div id="content">
  <h1>Propose Changes for a Plant</h1>
  
  <?php echo display_errors($errors); ?>
  <?php if ($message): ?>
    <p><?php echo h($message); ?></p>
  <?php endif; ?>
  
  <form action="propose_edit.php<?php echo (!empty($plant_name)) ? '?plant=' . urlencode($plant_name) : ''; ?>" method="post">
    <!-- Basic Information -->
    <fieldset>
      <legend>Basic Information</legend>
      <label for="PlantName">Plant Name:</label>
      <input type="text" name="PlantName" value="<?php echo h($current_plant['PlantName'] ?? ''); ?>" required /><br />
      
      <label for="Family">Family:</label>
      <input type="text" name="Family" value="<?php echo h($current_plant['Family'] ?? ''); ?>" /><br />
      
      <label for="Synonyms">Synonyms:</label>
      <textarea name="Synonyms"><?php echo h($current_plant['Synonyms'] ?? ''); ?></textarea><br />
      
      <label for="CommonNames">Common Names:</label>
      <textarea name="CommonNames"><?php echo h($current_plant['CommonNames'] ?? ''); ?></textarea><br />
      
      <label for="Description">Description:</label>
      <textarea name="Description"><?php echo h($current_plant['Description'] ?? ''); ?></textarea><br />
      
      <label for="Uses">Uses (Summary):</label>
      <textarea name="Uses"><?php echo h($current_plant['Uses'] ?? ''); ?></textarea><br />
      
      <label for="GrowingPeriod">Growing Period:</label>
      <input type="text" name="GrowingPeriod" value="<?php echo h($current_plant['GrowingPeriod'] ?? ''); ?>" /><br />
      
      <label for="FurtherInformation">Further Information:</label>
      <textarea name="FurtherInformation"><?php echo h($current_plant['FurtherInformation'] ?? ''); ?></textarea><br />
      
      <label for="FinalSource">Final Source:</label>
      <textarea name="FinalSource"><?php echo h($current_plant['FinalSource'] ?? ''); ?></textarea><br />
      
      <label for="Image">Image (filename):</label>
      <input type="text" name="Image" value="<?php echo h($current_plant['Image'] ?? ''); ?>" /><br />
      
      <label for="LifeForm">Life Form:</label>
      <input type="text" name="LifeForm" value="<?php echo h($current_plant['LifeForm'] ?? ''); ?>" /><br />
      
      <label for="Physiology">Physiology:</label>
      <input type="text" name="Physiology" value="<?php echo h($current_plant['Physiology'] ?? ''); ?>" /><br />
      
      <label for="Habit">Habit:</label>
      <input type="text" name="Habit" value="<?php echo h($current_plant['Habit'] ?? ''); ?>" /><br />
      
      <label for="Category">Category:</label>
      <input type="text" name="Category" value="<?php echo h($current_plant['Category'] ?? ''); ?>" /><br />
      
      <label for="LifeSpan">Life Span:</label>
      <input type="text" name="LifeSpan" value="<?php echo h($current_plant['LifeSpan'] ?? ''); ?>" /><br />
      
      <label for="PlantAttributes">Attributes:</label>
      <textarea name="PlantAttributes"><?php echo h($current_plant['PlantAttributes'] ?? ''); ?></textarea><br />
    </fieldset>
    
    <!-- Temperature Requirements -->
    <fieldset>
      <legend>Temperature Requirements</legend>
      <label for="TempRequiredOptimalMin">Optimal Temp Min:</label>
      <input type="text" name="TempRequiredOptimalMin" value="<?php echo h($current_plant['TempRequiredOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="TempRequiredOptimalMax">Optimal Temp Max:</label>
      <input type="text" name="TempRequiredOptimalMax" value="<?php echo h($current_plant['TempRequiredOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="TempRequiredAbsoluteMin">Absolute Temp Min:</label>
      <input type="text" name="TempRequiredAbsoluteMin" value="<?php echo h($current_plant['TempRequiredAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="TempRequiredAbsoluteMax">Absolute Temp Max:</label>
      <input type="text" name="TempRequiredAbsoluteMax" value="<?php echo h($current_plant['TempRequiredAbsoluteMax'] ?? ''); ?>" /><br />
      
      <label for="KillingTemp_DuringRest">Killing Temp (During Rest):</label>
      <input type="text" name="KillingTemp_DuringRest" value="<?php echo h($current_plant['KillingTemp_DuringRest'] ?? ''); ?>" /><br />
      
      <label for="KillingTemp_EarlyGrowth">Killing Temp (Early Growth):</label>
      <input type="text" name="KillingTemp_EarlyGrowth" value="<?php echo h($current_plant['KillingTemp_EarlyGrowth'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Rainfall Requirements -->
    <fieldset>
      <legend>Rainfall Requirements</legend>
      <label for="RainfallAnnualOptimalMin">Optimal Annual Rainfall Min:</label>
      <input type="text" name="RainfallAnnualOptimalMin" value="<?php echo h($current_plant['RainfallAnnualOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="RainfallAnnualOptimalMax">Optimal Annual Rainfall Max:</label>
      <input type="text" name="RainfallAnnualOptimalMax" value="<?php echo h($current_plant['RainfallAnnualOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="RainfallAnnualAbsoluteMin">Absolute Annual Rainfall Min:</label>
      <input type="text" name="RainfallAnnualAbsoluteMin" value="<?php echo h($current_plant['RainfallAnnualAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="RainfallAnnualAbsoluteMax">Absolute Annual Rainfall Max:</label>
      <input type="text" name="RainfallAnnualAbsoluteMax" value="<?php echo h($current_plant['RainfallAnnualAbsoluteMax'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Light Requirements -->
    <fieldset>
      <legend>Light Requirements</legend>
      <label for="LightIntensityOptimalMin">Optimal Light Intensity Min:</label>
      <input type="text" name="LightIntensityOptimalMin" value="<?php echo h($current_plant['LightIntensityOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="LightIntensityOptimalMax">Optimal Light Intensity Max:</label>
      <input type="text" name="LightIntensityOptimalMax" value="<?php echo h($current_plant['LightIntensityOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="LightIntensityAbsoluteMin">Absolute Light Intensity Min:</label>
      <input type="text" name="LightIntensityAbsoluteMin" value="<?php echo h($current_plant['LightIntensityAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="LightIntensityAbsoluteMax">Absolute Light Intensity Max:</label>
      <input type="text" name="LightIntensityAbsoluteMax" value="<?php echo h($current_plant['LightIntensityAbsoluteMax'] ?? ''); ?>" /><br />
      
      <label for="Photoperiod">Photoperiod:</label>
      <input type="text" name="Photoperiod" value="<?php echo h($current_plant['Photoperiod'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Soil and Water Conditions -->
    <fieldset>
      <legend>Soil and Water Conditions</legend>
      <label for="SoilPHOptimalMin">Optimal Soil PH Min:</label>
      <input type="text" name="SoilPHOptimalMin" value="<?php echo h($current_plant['SoilPHOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="SoilPHOptimalMax">Optimal Soil PH Max:</label>
      <input type="text" name="SoilPHOptimalMax" value="<?php echo h($current_plant['SoilPHOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="SoilDepthOptimal">Optimal Soil Depth:</label>
      <input type="text" name="SoilDepthOptimal" value="<?php echo h($current_plant['SoilDepthOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilDepthAbsolute">Absolute Soil Depth:</label>
      <input type="text" name="SoilDepthAbsolute" value="<?php echo h($current_plant['SoilDepthAbsolute'] ?? ''); ?>" /><br />
      
      <label for="SoilTextureOptimal">Optimal Soil Texture:</label>
      <input type="text" name="SoilTextureOptimal" value="<?php echo h($current_plant['SoilTextureOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilTextureAbsolute">Absolute Soil Texture:</label>
      <input type="text" name="SoilTextureAbsolute" value="<?php echo h($current_plant['SoilTextureAbsolute'] ?? ''); ?>" /><br />
      
      <label for="SoilFertilityOptimal">Optimal Soil Fertility:</label>
      <input type="text" name="SoilFertilityOptimal" value="<?php echo h($current_plant['SoilFertilityOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilFertilityAbsolute">Absolute Soil Fertility:</label>
      <input type="text" name="SoilFertilityAbsolute" value="<?php echo h($current_plant['SoilFertilityAbsolute'] ?? ''); ?>" /><br />
      
      <label for="SoilSalinityOptimal">Optimal Soil Salinity:</label>
      <input type="text" name="SoilSalinityOptimal" value="<?php echo h($current_plant['SoilSalinityOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilSalinityAbsolute">Absolute Soil Salinity:</label>
      <input type="text" name="SoilSalinityAbsolute" value="<?php echo h($current_plant['SoilSalinityAbsolute'] ?? ''); ?>" /><br />
      
      <label for="SoilDrainageOptimal">Optimal Soil Drainage:</label>
      <input type="text" name="SoilDrainageOptimal" value="<?php echo h($current_plant['SoilDrainageOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilDrainageAbsolute">Absolute Soil Drainage:</label>
      <input type="text" name="SoilDrainageAbsolute" value="<?php echo h($current_plant['SoilDrainageAbsolute'] ?? ''); ?>" /><br />
      
      <label for="SoilAlToxOptimal">Optimal Soil Aluminum Toxicity:</label>
      <input type="text" name="SoilAlToxOptimal" value="<?php echo h($current_plant['SoilAlToxOptimal'] ?? ''); ?>" /><br />
      
      <label for="SoilAlToxAbsolute">Absolute Soil Aluminum Toxicity:</label>
      <input type="text" name="SoilAlToxAbsolute" value="<?php echo h($current_plant['SoilAlToxAbsolute'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Geographical and Climate Information -->
    <fieldset>
      <legend>Geographical and Climate Information</legend>
      <label for="LatitudeOptimalMin">Optimal Latitude Min:</label>
      <input type="text" name="LatitudeOptimalMin" value="<?php echo h($current_plant['LatitudeOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="LatitudeAbsoluteMin">Absolute Latitude Min:</label>
      <input type="text" name="LatitudeAbsoluteMin" value="<?php echo h($current_plant['LatitudeAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="LatitudeOptimalMax">Optimal Latitude Max:</label>
      <input type="text" name="LatitudeOptimalMax" value="<?php echo h($current_plant['LatitudeOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="LatitudeAbsoluteMax">Absolute Latitude Max:</label>
      <input type="text" name="LatitudeAbsoluteMax" value="<?php echo h($current_plant['LatitudeAbsoluteMax'] ?? ''); ?>" /><br />
      
      <label for="AltitudeOptimalMin">Optimal Altitude Min:</label>
      <input type="text" name="AltitudeOptimalMin" value="<?php echo h($current_plant['AltitudeOptimalMin'] ?? ''); ?>" /><br />
      
      <label for="AltitudeOptimalMax">Optimal Altitude Max:</label>
      <input type="text" name="AltitudeOptimalMax" value="<?php echo h($current_plant['AltitudeOptimalMax'] ?? ''); ?>" /><br />
      
      <label for="ClimateZone">Climate Zone:</label>
      <input type="text" name="ClimateZone" value="<?php echo h($current_plant['ClimateZone'] ?? ''); ?>" /><br />
      
      <label for="AltitudeAbsoluteMin">Absolute Altitude Min:</label>
      <input type="text" name="AltitudeAbsoluteMin" value="<?php echo h($current_plant['AltitudeAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="AltitudeAbsoluteMax">Absolute Altitude Max:</label>
      <input type="text" name="AltitudeAbsoluteMax" value="<?php echo h($current_plant['AltitudeAbsoluteMax'] ?? ''); ?>" /><br />
      
      <label for="SoilPHAbsoluteMin">Absolute Soil PH Min:</label>
      <input type="text" name="SoilPHAbsoluteMin" value="<?php echo h($current_plant['SoilPHAbsoluteMin'] ?? ''); ?>" /><br />
      
      <label for="SoilPHAbsoluteMax">Absolute Soil PH Max:</label>
      <input type="text" name="SoilPHAbsoluteMax" value="<?php echo h($current_plant['SoilPHAbsoluteMax'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Additional Details -->
    <fieldset>
      <legend>Additional Details</legend>
      <label for="AbioticTolerance">Abiotic Tolerance:</label>
      <textarea name="AbioticTolerance"><?php echo h($current_plant['AbioticTolerance'] ?? ''); ?></textarea><br />
      
      <label for="AbioticSuscept">Abiotic Susceptibility:</label>
      <textarea name="AbioticSuscept"><?php echo h($current_plant['AbioticSuscept'] ?? ''); ?></textarea><br />
      
      <label for="IntroductionRisks">Introduction Risks:</label>
      <textarea name="IntroductionRisks"><?php echo h($current_plant['IntroductionRisks'] ?? ''); ?></textarea><br />
      
      <label for="ProductSystem">Product System:</label>
      <input type="text" name="ProductSystem" value="<?php echo h($current_plant['ProductSystem'] ?? ''); ?>" /><br />
      
      <label for="CropCycle_Min">Crop Cycle (Min days):</label>
      <input type="text" name="CropCycle_Min" value="<?php echo h($current_plant['CropCycle_Min'] ?? ''); ?>" /><br />
      
      <label for="CropCycle_Max">Crop Cycle (Max days):</label>
      <input type="text" name="CropCycle_Max" value="<?php echo h($current_plant['CropCycle_Max'] ?? ''); ?>" /><br />
    </fieldset>
    
    <!-- Plant Uses (one-to-many relationship) -->
    <fieldset>
      <legend>Plant Uses</legend>
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div>
          <h3>Use <?php echo $i; ?></h3>
          <label for="UseMain_<?php echo $i; ?>">Main Use:</label>
          <input type="text" name="UseMain_<?php echo $i; ?>" /><br />
          
          <label for="UseDetailed_<?php echo $i; ?>">Detailed Use:</label>
          <textarea name="UseDetailed_<?php echo $i; ?>"></textarea><br />
          
          <label for="UsePart_<?php echo $i; ?>">Used Part:</label>
          <input type="text" name="UsePart_<?php echo $i; ?>" /><br />
        </div>
      <?php endfor; ?>
    </fieldset>
    
    <!-- Cultivation Details (one-to-many relationship) -->
    <fieldset>
      <legend>Cultivation Details</legend>
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div>
          <h3>Cultivation <?php echo $i; ?></h3>
          <label for="CultivationMethod_<?php echo $i; ?>">Cultivation Method:</label>
          <input type="text" name="CultivationMethod_<?php echo $i; ?>" /><br />
          
          <label for="CultivationDetails_<?php echo $i; ?>">Cultivation Details:</label>
          <textarea name="CultivationDetails_<?php echo $i; ?>"></textarea><br />
        </div>
      <?php endfor; ?>
    </fieldset>
    
    <input type="submit" value="Submit Proposal" />
  </form>
</div>

<?php include(SHARED_PATH . '/member_footer.php'); ?>