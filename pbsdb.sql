-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2017 at 08:30 PM
-- Server version: 10.1.22-MariaDB
-- PHP Version: 7.1.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pbsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities_log`
--

CREATE TABLE `activities_log` (
  `id` int(11) NOT NULL,
  `log_date` datetime NOT NULL,
  `user_id` varchar(150) NOT NULL,
  `proj_id` varchar(150) DEFAULT NULL,
  `task_id` varchar(150) DEFAULT NULL,
  `log_message` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `con_login`
--

CREATE TABLE `con_login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE latin1_bin NOT NULL,
  `firstname` varchar(50) COLLATE latin1_bin NOT NULL,
  `lastname` varchar(50) COLLATE latin1_bin NOT NULL,
  `userpwd` varchar(50) COLLATE latin1_bin NOT NULL,
  `usermail` varchar(50) COLLATE latin1_bin NOT NULL,
  `userrole` varchar(50) COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

--
-- Dumping data for table `con_login`
--

INSERT INTO `con_login` (`id`, `username`, `firstname`, `lastname`, `userpwd`, `usermail`, `userrole`) VALUES
(4, 'rameshor@gmail.com', 'RAMESH', 'ODURU', 'password', 'rameshor@gmail.com', 'ADM'),
(5, 'satishchandkc@gmail.com', 'SATHISH', 'KC', 'password', 'satishchandkc@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `project_master`
--

CREATE TABLE `project_master` (
  `id` int(250) NOT NULL,
  `project_number` varchar(250) COLLATE latin1_bin NOT NULL,
  `project_id` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `project_name` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `activity_type` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `txProjectGanttSet` smallint(6) NOT NULL DEFAULT '0',
  `proj_base_line_number` smallint(6) DEFAULT NULL,
  `proj_plan_start_date` date DEFAULT NULL,
  `proj_plan_end_date` varchar(100) COLLATE latin1_bin DEFAULT NULL,
  `proj_actual_start_date` date DEFAULT NULL,
  `proj_actual_end_date` date DEFAULT NULL,
  `completion_percentage` float(4,2) DEFAULT NULL,
  `proj_duration` int(11) DEFAULT NULL,
  `txTaskParent` varchar(250) COLLATE latin1_bin NOT NULL DEFAULT '0',
  `proj_manager` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `proj_admin` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `proj_users` text CHARACTER SET utf8 COLLATE utf8_bin,
  `resFilePath` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `resFileName` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `txResourceType` varchar(150) COLLATE latin1_bin NOT NULL,
  `txResourceName` varchar(250) COLLATE latin1_bin NOT NULL,
  `txResourceQuantity` varchar(150) COLLATE latin1_bin NOT NULL,
  `txResourceUnit` varchar(150) COLLATE latin1_bin NOT NULL,
  `numManPowerWght` int(11) NOT NULL DEFAULT '0',
  `numMachinaryWght` int(11) NOT NULL DEFAULT '0',
  `numMaterialWght` int(11) NOT NULL DEFAULT '0',
  `txManPowerUsed` int(250) NOT NULL DEFAULT '0',
  `txManPowerCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txWorkDone` int(11) NOT NULL DEFAULT '0',
  `txWorkCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txMachineryUsed` int(11) NOT NULL DEFAULT '0',
  `txMachineryCP` int(11) NOT NULL DEFAULT '0',
  `txMaterialUsed` int(11) NOT NULL DEFAULT '0',
  `txMaterialCP` float(4,2) NOT NULL DEFAULT '0.00',
  `task_overallCP` float(4,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Table structure for table `project_resources`
--

CREATE TABLE `project_resources` (
  `id` int(250) NOT NULL,
  `project_id` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_type` varchar(150) NOT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `resource_quantity` varchar(250) NOT NULL,
  `resource_unit` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_rate` double NOT NULL DEFAULT '0',
  `resource_value` double NOT NULL DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resource_assigned_master` double NOT NULL DEFAULT '0',
  `resource_utilized_master` double NOT NULL DEFAULT '0',
  `projResAvailable` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_task_links`
--

CREATE TABLE `project_task_links` (
  `link_no` int(250) NOT NULL,
  `project_id` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `numBaseLineNumber` int(50) NOT NULL,
  `txSourceID` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `txTargetID` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `txLinkID` varchar(150) COLLATE latin1_bin NOT NULL,
  `txLinkType` varchar(50) COLLATE latin1_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Table structure for table `project_templates`
--

CREATE TABLE `project_templates` (
  `id` int(11) NOT NULL,
  `reference` varchar(150) DEFAULT NULL,
  `name` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `size` varchar(150) DEFAULT NULL,
  `type` varchar(150) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports_resource`
--

CREATE TABLE `reports_resource` (
  `id` int(11) NOT NULL,
  `project_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_id` varchar(100) NOT NULL,
  `task_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_hierarchy` varchar(150) NOT NULL,
  `task_parent` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_type` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(250) DEFAULT NULL,
  `resource_unit` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_assigned` int(11) DEFAULT '0',
  `resource_utilized` int(11) DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resourceCP` float(4,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resource_utilization`
--

CREATE TABLE `resource_utilization` (
  `id` int(11) NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `project_id` varchar(150) DEFAULT NULL,
  `project_name` varchar(250) DEFAULT NULL,
  `task_id` varchar(150) DEFAULT NULL,
  `task_name` varchar(250) DEFAULT NULL,
  `resource_type` varchar(150) DEFAULT NULL,
  `resource_name` varchar(250) DEFAULT NULL,
  `resource_category` varchar(250) DEFAULT NULL,
  `resource_utilized` int(11) DEFAULT NULL,
  `resource_unit` varchar(150) DEFAULT NULL,
  `amount_spent` double DEFAULT NULL,
  `user_id` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_machinery_consumed`
--

CREATE TABLE `task_machinery_consumed` (
  `id` int(11) NOT NULL,
  `task_id` varchar(100) NOT NULL,
  `task_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_hierarchy` varchar(150) NOT NULL,
  `task_parent` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `project_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `resource_type` varchar(250) DEFAULT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(250) DEFAULT NULL,
  `resource_unit` varchar(250) DEFAULT NULL,
  `resource_assigned` int(11) DEFAULT '0',
  `resource_utilized` int(11) DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resourceCP` float(4,2) DEFAULT '0.00',
  `res_plan_start_date` date NOT NULL,
  `res_plan_end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_manpower_consumed`
--

CREATE TABLE `task_manpower_consumed` (
  `id` int(11) NOT NULL,
  `task_id` varchar(100) NOT NULL,
  `task_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_hierarchy` varchar(150) NOT NULL,
  `project_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_parent` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_type` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(250) DEFAULT NULL,
  `resource_unit` varchar(250) DEFAULT NULL,
  `resource_assigned` int(11) DEFAULT '0',
  `resource_utilized` int(11) DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resourceCP` float(4,2) DEFAULT '0.00',
  `res_plan_start_date` date NOT NULL,
  `res_plan_end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_master`
--

CREATE TABLE `task_master` (
  `task_no` int(11) NOT NULL,
  `task_id` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `project_id` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `task_name` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `activity_type` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `task_parent` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `task_hierarchy` varchar(250) COLLATE latin1_bin NOT NULL DEFAULT '0',
  `proj_base_line_number` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `task_priority` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `task_plan_start_date` date DEFAULT NULL,
  `task_plan_end_date` date DEFAULT NULL,
  `task_actual_start_date` date DEFAULT NULL,
  `task_actual_end_date` date DEFAULT NULL,
  `numTaskDuration` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `has_child` varchar(25) COLLATE latin1_bin NOT NULL,
  `task_comp_percent` float(4,2) DEFAULT '0.00',
  `numCompPrsnt` float(4,2) NOT NULL DEFAULT '0.00',
  `numMaterialWght` int(11) DEFAULT '0',
  `numMachinaryWght` int(11) DEFAULT '0',
  `numManPowerWght` int(11) DEFAULT '0',
  `assigned_categories` varchar(250) COLLATE latin1_bin NOT NULL,
  `txMachineryCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txMachineryName` varchar(250) COLLATE latin1_bin NOT NULL,
  `txMachineryQnty` varchar(250) COLLATE latin1_bin NOT NULL,
  `txMachineryType` varchar(250) COLLATE latin1_bin NOT NULL,
  `txMachineryUnit` varchar(250) COLLATE latin1_bin NOT NULL,
  `txMachineryUsed` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `txManPowerCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txManPowerName` varchar(100) COLLATE latin1_bin NOT NULL,
  `txManPowerQnty` varchar(100) COLLATE latin1_bin NOT NULL,
  `txManPowerType` varchar(100) COLLATE latin1_bin NOT NULL,
  `txManPowerUnit` varchar(100) COLLATE latin1_bin NOT NULL,
  `txManPowerUsed` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `txWorkCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txWorkName` varchar(250) COLLATE latin1_bin NOT NULL,
  `txWorkQnty` varchar(250) COLLATE latin1_bin NOT NULL,
  `txWorkType` varchar(250) COLLATE latin1_bin NOT NULL,
  `txWorkUnit` varchar(100) COLLATE latin1_bin NOT NULL,
  `txWorkDone` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `txMaterialCP` float(4,2) NOT NULL DEFAULT '0.00',
  `txMaterialName` varchar(100) COLLATE latin1_bin NOT NULL,
  `txMaterialQnty` varchar(100) COLLATE latin1_bin NOT NULL,
  `txMaterialType` varchar(100) COLLATE latin1_bin NOT NULL,
  `txMaterialUnit` varchar(100) COLLATE latin1_bin NOT NULL,
  `txMaterialUsed` varchar(250) COLLATE latin1_bin NOT NULL DEFAULT '0',
  `task_mach_machineryrcp` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `task_mat_materialrused` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `task_mat_materialrcp` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `task_overallCP` float(4,2) NOT NULL DEFAULT '0.00',
  `task_progress` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `task_open` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `proj_manager` varchar(250) COLLATE latin1_bin DEFAULT NULL,
  `task_members` text CHARACTER SET utf8 COLLATE utf8_bin,
  `task_admin` varchar(50) COLLATE latin1_bin NOT NULL DEFAULT 'ADM'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Table structure for table `task_material_consumed`
--

CREATE TABLE `task_material_consumed` (
  `id` int(11) NOT NULL,
  `project_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_id` varchar(100) NOT NULL,
  `task_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_hierarchy` varchar(150) NOT NULL,
  `task_parent` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_type` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `resource_unit` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_assigned` int(11) DEFAULT '0',
  `resource_utilized` int(11) DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resourceCP` float(4,2) DEFAULT '0.00',
  `res_plan_start_date` date NOT NULL,
  `res_plan_end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_work_consumed`
--

CREATE TABLE `task_work_consumed` (
  `id` int(11) NOT NULL,
  `project_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_id` varchar(100) NOT NULL,
  `task_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `task_hierarchy` varchar(150) NOT NULL,
  `task_parent` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_type` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_category` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `resource_name` varchar(250) DEFAULT NULL,
  `resource_unit` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `resource_assigned` int(11) DEFAULT '0',
  `resource_utilized` int(11) DEFAULT '0',
  `amount_spent` double NOT NULL DEFAULT '0',
  `resourceCP` float(4,2) DEFAULT '0.00',
  `res_plan_start_date` date NOT NULL,
  `res_plan_end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities_log`
--
ALTER TABLE `activities_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `con_login`
--
ALTER TABLE `con_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_master`
--
ALTER TABLE `project_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_resources`
--
ALTER TABLE `project_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_task_links`
--
ALTER TABLE `project_task_links`
  ADD PRIMARY KEY (`link_no`);

--
-- Indexes for table `project_templates`
--
ALTER TABLE `project_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports_resource`
--
ALTER TABLE `reports_resource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource_utilization`
--
ALTER TABLE `resource_utilization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_machinery_consumed`
--
ALTER TABLE `task_machinery_consumed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_manpower_consumed`
--
ALTER TABLE `task_manpower_consumed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_master`
--
ALTER TABLE `task_master`
  ADD PRIMARY KEY (`task_no`);

--
-- Indexes for table `task_material_consumed`
--
ALTER TABLE `task_material_consumed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_work_consumed`
--
ALTER TABLE `task_work_consumed`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities_log`
--
ALTER TABLE `activities_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `con_login`
--
ALTER TABLE `con_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `project_master`
--
ALTER TABLE `project_master`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_resources`
--
ALTER TABLE `project_resources`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_task_links`
--
ALTER TABLE `project_task_links`
  MODIFY `link_no` int(250) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_templates`
--
ALTER TABLE `project_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reports_resource`
--
ALTER TABLE `reports_resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `resource_utilization`
--
ALTER TABLE `resource_utilization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_machinery_consumed`
--
ALTER TABLE `task_machinery_consumed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_manpower_consumed`
--
ALTER TABLE `task_manpower_consumed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_master`
--
ALTER TABLE `task_master`
  MODIFY `task_no` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_material_consumed`
--
ALTER TABLE `task_material_consumed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_work_consumed`
--
ALTER TABLE `task_work_consumed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
