<?php
require_once('../private/initialize.php');

$plant_name = $_GET['plant'] ?? '';

if (empty($plant_name)) {
  redirect_to(url_for('/index.php'));
}

$plant_query = "SELECT * FROM plant WHERE PlantName = '" . mysqli_real_escape_string($db, $plant_name) . "' LIMIT 1";
$plant_result = mysqli_query($db, $plant_query);
$plant = mysqli_fetch_assoc($plant_result);

if (!$plant) {
  $error_message = "Plant not found.";
}

$page_title = $plant ? h($plant['PlantName']) : 'Plant Not Found';
include(SHARED_PATH . '/public_header.php');
include(SHARED_PATH . '/public_navigation.php');
?>

<div id="content">
  <?php if (isset($error_message)): ?>
    <p><?php echo h($error_message); ?></p>
  <?php else: ?>
    <h1><?php echo h($plant['PlantName']); ?></h1>
    
    <?php if ($plant['Image']): ?>
      <div>
        <img src="<?php echo url_for('/img/' . h($plant['Image'])); ?>" alt="<?php echo h($plant['PlantName']); ?>" style="max-width:300px;"/>
      </div>
    <?php endif; ?>
    
    <section>
      <h2>Basic Information</h2>
      <?php if ($plant['Family']): ?>
        <p><strong>Family:</strong> <?php echo h($plant['Family']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Synonyms']): ?>
        <p><strong>Synonyms:</strong> <?php echo h($plant['Synonyms']); ?></p>
      <?php endif; ?>
      <?php if ($plant['CommonNames']): ?>
        <p><strong>Common Names:</strong> <?php echo h($plant['CommonNames']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Description']): ?>
        <p><strong>Description:</strong> <?php echo h($plant['Description']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Uses']): ?>
        <p><strong>Uses:</strong> <?php echo h($plant['Uses']); ?></p>
      <?php endif; ?>
      <?php if ($plant['GrowingPeriod']): ?>
        <p><strong>Growing Period:</strong> <?php echo h($plant['GrowingPeriod']); ?></p>
      <?php endif; ?>
      <?php if ($plant['FurtherInformation']): ?>
        <p><strong>Further Information:</strong> <?php echo h($plant['FurtherInformation']); ?></p>
      <?php endif; ?>
      <?php if ($plant['FinalSource']): ?>
        <p><strong>Final Source:</strong> <?php echo h($plant['FinalSource']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Category']): ?>
        <p><strong>Category:</strong> <?php echo h($plant['Category']); ?></p>
      <?php endif; ?>
      <?php if ($plant['LifeForm']): ?>
        <p><strong>Life Form:</strong> <?php echo h($plant['LifeForm']); ?></p>
      <?php endif; ?>
      <?php if ($plant['LifeSpan']): ?>
        <p><strong>Life Span:</strong> <?php echo h($plant['LifeSpan']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Physiology']): ?>
        <p><strong>Physiology:</strong> <?php echo h($plant['Physiology']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Habit']): ?>
        <p><strong>Habit:</strong> <?php echo h($plant['Habit']); ?></p>
      <?php endif; ?>
      <?php if ($plant['PlantAttributes']): ?>
        <p><strong>Attributes:</strong> <?php echo h($plant['PlantAttributes']); ?></p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Temperature Requirements</h2>
      <?php if ($plant['TempRequiredOptimalMin'] || $plant['TempRequiredOptimalMax']): ?>
        <p><strong>Optimal Temperature:</strong> <?php echo h($plant['TempRequiredOptimalMin']); ?>°C - <?php echo h($plant['TempRequiredOptimalMax']); ?>°C</p>
      <?php endif; ?>
      <?php if ($plant['TempRequiredAbsoluteMin'] || $plant['TempRequiredAbsoluteMax']): ?>
        <p><strong>Absolute Temperature Range:</strong> <?php echo h($plant['TempRequiredAbsoluteMin']); ?>°C - <?php echo h($plant['TempRequiredAbsoluteMax']); ?>°C</p>
      <?php endif; ?>
      <?php if ($plant['KillingTemp_DuringRest']): ?>
        <p><strong>Killing Temperature (During Rest):</strong> <?php echo h($plant['KillingTemp_DuringRest']); ?>°C</p>
      <?php endif; ?>
      <?php if ($plant['KillingTemp_EarlyGrowth']): ?>
        <p><strong>Killing Temperature (Early Growth):</strong> <?php echo h($plant['KillingTemp_EarlyGrowth']); ?>°C</p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Rainfall Requirements</h2>
      <?php if ($plant['RainfallAnnualOptimalMin'] || $plant['RainfallAnnualOptimalMax']): ?>
        <p><strong>Optimal Annual Rainfall:</strong> <?php echo h($plant['RainfallAnnualOptimalMin']); ?>mm - <?php echo h($plant['RainfallAnnualOptimalMax']); ?>mm</p>
      <?php endif; ?>
      <?php if ($plant['RainfallAnnualAbsoluteMin'] || $plant['RainfallAnnualAbsoluteMax']): ?>
        <p><strong>Absolute Annual Rainfall:</strong> <?php echo h($plant['RainfallAnnualAbsoluteMin']); ?>mm - <?php echo h($plant['RainfallAnnualAbsoluteMax']); ?>mm</p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Light Requirements</h2>
      <?php if ($plant['LightIntensityOptimalMin'] || $plant['LightIntensityOptimalMax']): ?>
        <p><strong>Optimal Light Intensity:</strong> <?php echo h($plant['LightIntensityOptimalMin']); ?> - <?php echo h($plant['LightIntensityOptimalMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['LightIntensityAbsoluteMin'] || $plant['LightIntensityAbsoluteMax']): ?>
        <p><strong>Absolute Light Intensity:</strong> <?php echo h($plant['LightIntensityAbsoluteMin']); ?> - <?php echo h($plant['LightIntensityAbsoluteMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['Photoperiod']): ?>
        <p><strong>Photoperiod:</strong> <?php echo h($plant['Photoperiod']); ?></p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Soil and Water Conditions</h2>
      <?php if ($plant['SoilPHOptimalMin'] || $plant['SoilPHOptimalMax']): ?>
        <p><strong>Optimal Soil PH:</strong> <?php echo h($plant['SoilPHOptimalMin']); ?> - <?php echo h($plant['SoilPHOptimalMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilPHAbsoluteMin'] || $plant['SoilPHAbsoluteMax']): ?>
        <p><strong>Absolute Soil PH:</strong> <?php echo h($plant['SoilPHAbsoluteMin']); ?> - <?php echo h($plant['SoilPHAbsoluteMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilDepthOptimal']): ?>
        <p><strong>Optimal Soil Depth:</strong> <?php echo h($plant['SoilDepthOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilDepthAbsolute']): ?>
        <p><strong>Absolute Soil Depth:</strong> <?php echo h($plant['SoilDepthAbsolute']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilTextureOptimal']): ?>
        <p><strong>Optimal Soil Texture:</strong> <?php echo h($plant['SoilTextureOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilTextureAbsolute']): ?>
        <p><strong>Absolute Soil Texture:</strong> <?php echo h($plant['SoilTextureAbsolute']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilFertilityOptimal']): ?>
        <p><strong>Optimal Soil Fertility:</strong> <?php echo h($plant['SoilFertilityOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilFertilityAbsolute']): ?>
        <p><strong>Absolute Soil Fertility:</strong> <?php echo h($plant['SoilFertilityAbsolute']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilSalinityOptimal']): ?>
        <p><strong>Optimal Soil Salinity:</strong> <?php echo h($plant['SoilSalinityOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilSalinityAbsolute']): ?>
        <p><strong>Absolute Soil Salinity:</strong> <?php echo h($plant['SoilSalinityAbsolute']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilDrainageOptimal']): ?>
        <p><strong>Optimal Soil Drainage:</strong> <?php echo h($plant['SoilDrainageOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilDrainageAbsolute']): ?>
        <p><strong>Absolute Soil Drainage:</strong> <?php echo h($plant['SoilDrainageAbsolute']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilAlToxOptimal']): ?>
        <p><strong>Optimal Soil Aluminum Toxicity:</strong> <?php echo h($plant['SoilAlToxOptimal']); ?></p>
      <?php endif; ?>
      <?php if ($plant['SoilAlToxAbsolute']): ?>
        <p><strong>Absolute Soil Aluminum Toxicity:</strong> <?php echo h($plant['SoilAlToxAbsolute']); ?></p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Geographical and Climate Information</h2>
      <?php if ($plant['LatitudeOptimalMin'] || $plant['LatitudeOptimalMax']): ?>
        <p><strong>Optimal Latitude:</strong> <?php echo h($plant['LatitudeOptimalMin']); ?> - <?php echo h($plant['LatitudeOptimalMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['LatitudeAbsoluteMin'] || $plant['LatitudeAbsoluteMax']): ?>
        <p><strong>Absolute Latitude:</strong> <?php echo h($plant['LatitudeAbsoluteMin']); ?> - <?php echo h($plant['LatitudeAbsoluteMax']); ?></p>
      <?php endif; ?>
      <?php if ($plant['AltitudeOptimalMin'] || $plant['AltitudeOptimalMax']): ?>
        <p><strong>Optimal Altitude:</strong> <?php echo h($plant['AltitudeOptimalMin']); ?> - <?php echo h($plant['AltitudeOptimalMax']); ?> meters</p>
      <?php endif; ?>
      <?php if ($plant['AltitudeAbsoluteMin'] || $plant['AltitudeAbsoluteMax']): ?>
        <p><strong>Absolute Altitude:</strong> <?php echo h($plant['AltitudeAbsoluteMin']); ?> - <?php echo h($plant['AltitudeAbsoluteMax']); ?> meters</p>
      <?php endif; ?>
      <?php if ($plant['ClimateZone']): ?>
        <p><strong>Climate Zone:</strong> <?php echo h($plant['ClimateZone']); ?></p>
      <?php endif; ?>
    </section>
    
    <section>
      <h2>Additional Details</h2>
      <?php if ($plant['AbioticTolerance']): ?>
        <p><strong>Abiotic Tolerance:</strong> <?php echo h($plant['AbioticTolerance']); ?></p>
      <?php endif; ?>
      <?php if ($plant['AbioticSuscept']): ?>
        <p><strong>Abiotic Susceptibility:</strong> <?php echo h($plant['AbioticSuscept']); ?></p>
      <?php endif; ?>
      <?php if ($plant['IntroductionRisks']): ?>
        <p><strong>Introduction Risks:</strong> <?php echo h($plant['IntroductionRisks']); ?></p>
      <?php endif; ?>
      <?php if ($plant['ProductSystem']): ?>
        <p><strong>Product System:</strong> <?php echo h($plant['ProductSystem']); ?></p>
      <?php endif; ?>
      <?php if ($plant['CropCycle_Min'] || $plant['CropCycle_Max']): ?>
        <p><strong>Crop Cycle:</strong> <?php echo h($plant['CropCycle_Min']); ?> - <?php echo h($plant['CropCycle_Max']); ?> days</p>
      <?php endif; ?>
    </section>
    
  <?php endif; ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>