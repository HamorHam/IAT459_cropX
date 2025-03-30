-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 18, 2025 at 02:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cropx`
--

-- --------------------------------------------------------

--
-- Table structure for table `BOOKMARK`
--

CREATE TABLE `BOOKMARK` (
  `UserID` int(11) NOT NULL,
  `PlantName` varchar(255) NOT NULL,
  `DateAdded` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `COMMENTS`
--

CREATE TABLE `COMMENTS` (
  `CommentID` int(11) NOT NULL,
  `PlantName` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `IsApproved` tinyint(1) DEFAULT 0,
  `Content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CULTIVATION_INFO`
--

CREATE TABLE `CULTIVATION_INFO` (
  `PlantName` varchar(255) NOT NULL,
  `AbioticTolerance` varchar(255) DEFAULT NULL,
  `AbioticSuscept` varchar(255) DEFAULT NULL,
  `IntroductionRisks` varchar(255) DEFAULT NULL,
  `ProductSystem` varchar(255) DEFAULT NULL,
  `CropCycle_Min` int(11) DEFAULT NULL,
  `CropCycle_Max` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `GEOGRAPHIC_DISTRIBUTION`
--

CREATE TABLE `GEOGRAPHIC_DISTRIBUTION` (
  `PlantName` varchar(255) NOT NULL,
  `Latitude_O1` float DEFAULT NULL,
  `Latitude_O2` float DEFAULT NULL,
  `Latitude_A1` float DEFAULT NULL,
  `Latitude_A2` float DEFAULT NULL,
  `Altitude_O1` int(11) DEFAULT NULL,
  `Altitude_O2` int(11) DEFAULT NULL,
  `Altitude_A1` int(11) DEFAULT NULL,
  `Altitude_A2` int(11) DEFAULT NULL,
  `ClimateZone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `LIGHT_REQUIREMENTS`
--

CREATE TABLE `LIGHT_REQUIREMENTS` (
  `PlantName` varchar(255) NOT NULL,
  `LightIntensity_O1` varchar(100) DEFAULT NULL,
  `LightIntensity_O2` varchar(100) DEFAULT NULL,
  `LightIntensity_A1` varchar(100) DEFAULT NULL,
  `LightIntensity_A2` varchar(100) DEFAULT NULL,
  `Photoperiod` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `LOCATION`
--

CREATE TABLE `LOCATION` (
  `LocationID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PHYSICAL_CHARACTERISTICS`
--

CREATE TABLE `PHYSICAL_CHARACTERISTICS` (
  `PlantName` varchar(255) NOT NULL,
  `LifeForm` varchar(100) DEFAULT NULL,
  `Physiology` varchar(100) DEFAULT NULL,
  `Habit` varchar(100) DEFAULT NULL,
  `Category` varchar(255) DEFAULT NULL,
  `LifeSpan` varchar(100) DEFAULT NULL,
  `PlantAttributes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PLANT`
--

CREATE TABLE `PLANT` (
  `Name` varchar(255) NOT NULL,
  `Family` varchar(255) DEFAULT NULL,
  `Synonyms` text DEFAULT NULL,
  `CommonNames` text DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Uses` text DEFAULT NULL,
  `GrowingPeriod` varchar(255) DEFAULT NULL,
  `FurtherInformation` text DEFAULT NULL,
  `KillingTemp` varchar(100) DEFAULT NULL,
  `FinalSource` text DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `RAINFALL_REQUIREMENTS`
--

CREATE TABLE `RAINFALL_REQUIREMENTS` (
  `PlantName` varchar(255) NOT NULL,
  `RainfallAnnual_O1` int(11) DEFAULT NULL,
  `RainfallAnnual_O2` int(11) DEFAULT NULL,
  `RainfallAnnual_A1` int(11) DEFAULT NULL,
  `RainfallAnnual_A2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `REVIEW`
--

CREATE TABLE `REVIEW` (
  `ReviewID` int(11) NOT NULL,
  `PlantName` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Yield` int(11) DEFAULT NULL CHECK (`Yield` between 1 and 5),
  `Resistance` int(11) DEFAULT NULL CHECK (`Resistance` between 1 and 5),
  `EaseOfGrowth` int(11) DEFAULT NULL CHECK (`EaseOfGrowth` between 1 and 5),
  `Quality` int(11) DEFAULT NULL CHECK (`Quality` between 1 and 5),
  `Profitability` int(11) DEFAULT NULL CHECK (`Profitability` between 1 and 5),
  `Note` text DEFAULT NULL,
  `DateSubmitted` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SOIL_REQUIREMENTS`
--

CREATE TABLE `SOIL_REQUIREMENTS` (
  `PlantName` varchar(255) NOT NULL,
  `SoilPH_O1` float DEFAULT NULL,
  `SoilPH_O2` float DEFAULT NULL,
  `SoilPH_A1` float DEFAULT NULL,
  `SoilPH_A2` float DEFAULT NULL,
  `SoilDepth_O` varchar(100) DEFAULT NULL,
  `SoilDepth_A` varchar(100) DEFAULT NULL,
  `SoilTexture_O` varchar(255) DEFAULT NULL,
  `SoilTexture_A` varchar(255) DEFAULT NULL,
  `SoilFertility_O` varchar(100) DEFAULT NULL,
  `SoilFertility_A` varchar(100) DEFAULT NULL,
  `SoilAlTox_O` varchar(100) DEFAULT NULL,
  `SoilAlTox_A` varchar(100) DEFAULT NULL,
  `SoilSalinity_O` varchar(100) DEFAULT NULL,
  `SoilSalinity_A` varchar(100) DEFAULT NULL,
  `SoilDrainage_O` varchar(100) DEFAULT NULL,
  `SoilDrainage_A` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SPECIFIC_CULTIVATION`
--

CREATE TABLE `SPECIFIC_CULTIVATION` (
  `CultivationID` int(11) NOT NULL,
  `PlantName` varchar(255) NOT NULL,
  `Subsystem` varchar(255) DEFAULT NULL,
  `CompanionSpecies` varchar(255) DEFAULT NULL,
  `LevelOfMechanization` varchar(100) DEFAULT NULL,
  `LabourIntensity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `TEMPERATURE_REQUIREMENTS`
--

CREATE TABLE `TEMPERATURE_REQUIREMENTS` (
  `PlantName` varchar(255) NOT NULL,
  `TempRequired_O1` float DEFAULT NULL,
  `TempRequired_O2` float DEFAULT NULL,
  `TempRequired_A1` float DEFAULT NULL,
  `TempRequired_A2` float DEFAULT NULL,
  `KillingTemp_DuringRest` float DEFAULT NULL,
  `KillingTemp_EarlyGrowth` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `USER`
--

CREATE TABLE `USER` (
  `UserID` int(11) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `USES`
--

CREATE TABLE `USES` (
  `UseID` int(11) NOT NULL,
  `PlantName` varchar(255) NOT NULL,
  `MainUse` varchar(255) DEFAULT NULL,
  `DetailedUse` text DEFAULT NULL,
  `UsedPart` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `BOOKMARK`
--
ALTER TABLE `BOOKMARK`
  ADD PRIMARY KEY (`UserID`,`PlantName`),
  ADD KEY `PlantName` (`PlantName`);

--
-- Indexes for table `COMMENTS`
--
ALTER TABLE `COMMENTS`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `idx_comments_plant` (`PlantName`),
  ADD KEY `idx_comments_user` (`UserID`);

--
-- Indexes for table `CULTIVATION_INFO`
--
ALTER TABLE `CULTIVATION_INFO`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `GEOGRAPHIC_DISTRIBUTION`
--
ALTER TABLE `GEOGRAPHIC_DISTRIBUTION`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `LIGHT_REQUIREMENTS`
--
ALTER TABLE `LIGHT_REQUIREMENTS`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `LOCATION`
--
ALTER TABLE `LOCATION`
  ADD PRIMARY KEY (`LocationID`),
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- Indexes for table `PHYSICAL_CHARACTERISTICS`
--
ALTER TABLE `PHYSICAL_CHARACTERISTICS`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `PLANT`
--
ALTER TABLE `PLANT`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `idx_plant_family` (`Family`);

--
-- Indexes for table `RAINFALL_REQUIREMENTS`
--
ALTER TABLE `RAINFALL_REQUIREMENTS`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `REVIEW`
--
ALTER TABLE `REVIEW`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `idx_review_plant` (`PlantName`),
  ADD KEY `idx_review_user` (`UserID`);

--
-- Indexes for table `SOIL_REQUIREMENTS`
--
ALTER TABLE `SOIL_REQUIREMENTS`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `SPECIFIC_CULTIVATION`
--
ALTER TABLE `SPECIFIC_CULTIVATION`
  ADD PRIMARY KEY (`CultivationID`),
  ADD KEY `idx_specific_cultivation_plant` (`PlantName`);

--
-- Indexes for table `TEMPERATURE_REQUIREMENTS`
--
ALTER TABLE `TEMPERATURE_REQUIREMENTS`
  ADD PRIMARY KEY (`PlantName`);

--
-- Indexes for table `USER`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `USES`
--
ALTER TABLE `USES`
  ADD PRIMARY KEY (`UseID`),
  ADD KEY `idx_uses_plant` (`PlantName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `COMMENTS`
--
ALTER TABLE `COMMENTS`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `LOCATION`
--
ALTER TABLE `LOCATION`
  MODIFY `LocationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `REVIEW`
--
ALTER TABLE `REVIEW`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SPECIFIC_CULTIVATION`
--
ALTER TABLE `SPECIFIC_CULTIVATION`
  MODIFY `CultivationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `USER`
--
ALTER TABLE `USER`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `USES`
--
ALTER TABLE `USES`
  MODIFY `UseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `BOOKMARK`
--
ALTER TABLE `BOOKMARK`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `USER` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `COMMENTS`
--
ALTER TABLE `COMMENTS`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `USER` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `CULTIVATION_INFO`
--
ALTER TABLE `CULTIVATION_INFO`
  ADD CONSTRAINT `cultivation_info_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `GEOGRAPHIC_DISTRIBUTION`
--
ALTER TABLE `GEOGRAPHIC_DISTRIBUTION`
  ADD CONSTRAINT `geographic_distribution_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `LIGHT_REQUIREMENTS`
--
ALTER TABLE `LIGHT_REQUIREMENTS`
  ADD CONSTRAINT `light_requirements_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `LOCATION`
--
ALTER TABLE `LOCATION`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `USER` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `PHYSICAL_CHARACTERISTICS`
--
ALTER TABLE `PHYSICAL_CHARACTERISTICS`
  ADD CONSTRAINT `physical_characteristics_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `RAINFALL_REQUIREMENTS`
--
ALTER TABLE `RAINFALL_REQUIREMENTS`
  ADD CONSTRAINT `rainfall_requirements_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `REVIEW`
--
ALTER TABLE `REVIEW`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `USER` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `SOIL_REQUIREMENTS`
--
ALTER TABLE `SOIL_REQUIREMENTS`
  ADD CONSTRAINT `soil_requirements_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `SPECIFIC_CULTIVATION`
--
ALTER TABLE `SPECIFIC_CULTIVATION`
  ADD CONSTRAINT `specific_cultivation_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `TEMPERATURE_REQUIREMENTS`
--
ALTER TABLE `TEMPERATURE_REQUIREMENTS`
  ADD CONSTRAINT `temperature_requirements_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;

--
-- Constraints for table `USES`
--
ALTER TABLE `USES`
  ADD CONSTRAINT `uses_ibfk_1` FOREIGN KEY (`PlantName`) REFERENCES `PLANT` (`Name`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
