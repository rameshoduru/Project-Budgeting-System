<?php

/*
* This programe is called in appFunctions.js at line# 474 which in turn clled by Save button submit functionality 
* This program also sets heirarchy for each tasks and rolling-up of start and end dates for primary/parent tasks
*/

include('./dbconnect.php');
include('./appconfig.php');
include('./global_functions.php');

$bigData = $_REQUEST["saveJSONData"];

$bigData = explode("@$@*~*@$@", $bigData);

$data = explode("@$@", $bigData[0]);

	$tempdata = array();
		for($i=0; $i<count($data); $i++){
			$tempdata[$i] = $data[$i];
		}
	$temp = array();
	$dataArray = array();
		for($j=0; $j < count($tempdata); $j++){					
			if($tempdata[$j] !=""){
				$temp = explode("~!^", $tempdata[$j]);
				$dataArray[trim($temp[0])] = trim($temp[1]);			
			}
		}

	$proArrlength = count($dataArray);
	
	$sdate = explode(' ', $dataArray['dtTPSD']);
	$sdate = explode('-', $sdate[0]);
	$sdate = date("Y-m-d", mktime(0,0,0,$sdate[1],$sdate[0],$sdate[2])); 

	$edate = explode(' ', $dataArray['dtTPED']);
	$edate = explode('-', $edate[0]);
	$edate = date("Y-m-d", mktime(0,0,0,$edate[1],$edate[0],$edate[2])); 


	if($dataArray['txTaskType'] == "project")
	{		
		$projsql = "SELECT * FROM $project_master WHERE project_id='$dataArray[txProjectID]'"; 
		$conn->query($projsql) or die('Error, query failed');
		$Result = $conn->query($projsql);
		$row = $Result->fetch_array(MYSQLI_ASSOC);
	
		if ($row['project_id'] == $dataArray['txProjectID'] )
		{
			$projsql = "UPDATE $project_master SET activity_type = '$dataArray[txTaskType]', project_id = '$dataArray[txProjectID]', proj_base_line_number = '$dataArray[numBaseLineNumber]', project_number = '$dataArray[txTaskID]', project_Name = '$dataArray[txTaskName]', txTaskParent = '$dataArray[txTaskParent]', proj_actual_start_date='$sdate', proj_actual_end_date='$edate', proj_duration = '$dataArray[numTaskDuration]', proj_manager = '$dataArray[proj_manager]' 
			WHERE project_id = '$dataArray[txProjectID]'";
				
		}else{
			$projsql = "INSERT INTO $project_master ( activity_type, project_id, proj_base_line_number, project_number, project_Name, txTaskParent, proj_plan_start_date, proj_plan_end_date, proj_actual_start_date, proj_actual_end_date, proj_duration, proj_manager, proj_users ) ".
			" VALUES ( '$dataArray[txTaskType]', '$dataArray[txProjectID]', '$dataArray[numBaseLineNumber]', '$dataArray[txTaskID]', '$dataArray[txTaskName]', '$dataArray[txTaskParent]', '$sdate', '$edate', '$sdate', '$edate', '$dataArray[numTaskDuration]', '$dataArray[proj_manager]', '$dataArray[proj_manager]' )";				
		}		
		$conn->query($projsql) or die('Error, project query failed');		
	}

//task updates	
		$project_id = $dataArray['txProjectID'];
		$result = mysqli_query($conn, "SELECT project_number FROM project_master WHERE project_id='$project_id'");
		$task_rows = mysqli_fetch_array($result);
		$project_number = $task_rows['project_number'];
		
	if($dataArray['txTaskType'] == "task")
	{

		$result = mysqli_query($conn, "SELECT COUNT(*) AS `count` FROM task_master WHERE task_parent='$project_number'");
		$task_rows = mysqli_fetch_array($result);
		$task_count = $task_rows['count'];
		
		$tsksql = "SELECT * FROM task_master WHERE project_id='$dataArray[txProjectID]' AND task_id='$dataArray[txTaskID]'"; 
		$conn->query($tsksql) or die('Error, query failed');
		$result = $conn->query($tsksql);
		$row = $result->fetch_array(MYSQLI_ASSOC);
//setting has_child value for new tasks		
		if($dataArray['has_child'] == 'undefined' ){
			$has_child = 0;
		}else{
			$has_child = $dataArray['has_child'];
		}
		
		if ($row['task_id'] == $dataArray['txTaskID'] ){
			$tsksql = "UPDATE $task_master SET activity_type = '$dataArray[txTaskType]', project_id = '$dataArray[txProjectID]', proj_base_line_number = '$dataArray[numBaseLineNumber]', task_id = '$dataArray[txTaskID]', task_name = '$dataArray[txTaskName]', task_parent = '$dataArray[txTaskParent]', has_child = '$has_child', task_actual_start_date = '$sdate', task_actual_end_date = '$edate', numTaskDuration = '$dataArray[numTaskDuration]', proj_manager = '$dataArray[proj_manager]', numManPowerWght = '$dataArray[numManPowerWght]', numMachinaryWght = '$dataArray[numMachinaryWght]', numMaterialWght = '$dataArray[numMaterialWght]'

			WHERE task_id = '$dataArray[txTaskID]'";			
		}
		else
		{
			$tsksql = "INSERT INTO $task_master ( activity_type, project_id, proj_base_line_number, task_id, task_name, task_parent, has_child, task_plan_start_date, task_plan_end_date, task_actual_start_date, task_actual_end_date, numTaskDuration, proj_manager, task_members ) ".
			" VALUES ( '$dataArray[txTaskType]', '$dataArray[txProjectID]', '$dataArray[numBaseLineNumber]', '$dataArray[txTaskID]', '$dataArray[txTaskName]', '$dataArray[txTaskParent]', '$has_child', '$sdate', '$edate', '$sdate', '$edate', '$dataArray[numTaskDuration]', '$dataArray[proj_manager]', '$dataArray[proj_manager]' )";					
		}			
		$conn->query($tsksql) or die('Error, task query failed in save_data.php at line# 96');
		
		$chipData = explode("*$~$*", $bigData[1]);
		for($i=0; $i<count($chipData); $i++){			
			processResData($chipData[$i], $sdate, $edate  );
		}			
	}

//<-------------------------------- Processing of 'saveJSON' ends here ---------------------


//>-------------Processing of resource usage data starts here ------------------------------

	function processResData( $chipData, $sdate, $edate ){
		global $conn;
		global $project_resources;
		
		$resourceJSON = explode("@$@", $chipData);
		$tempdata = array();
			for($i=0; $i<count($resourceJSON); $i++){
				$tempdata[$i] = $resourceJSON[$i];
			}
		$temp = array();
		$dataArray = array();
			for($j=0; $j < count($tempdata); $j++){					
				if($tempdata[$j] !=""){
					$temp = explode("~!^", $tempdata[$j]);
					$dataArray[trim($temp[0])] = trim($temp[1]);			
				}
			}

		if( isset($dataArray['txResName']) ){
			$tableName = $dataArray['txResTable'];
		
			$resSql = "SELECT * FROM $tableName WHERE task_id='$dataArray[txTaskID]' AND resource_name = '$dataArray[txResName]'"; 
			$conn->query($resSql) or die('Error, query failed');
			$result = $conn->query($resSql);
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			
			if ($row['task_id'] == $dataArray['txTaskID'] AND $row['resource_name'] == $dataArray['txResName'] AND $row['resource_type'] == $dataArray['txResType'] AND $row['resource_category'] == $dataArray['txResCat'] )
			{
				$resSql = "UPDATE $tableName SET task_name = '$dataArray[txTaskName]', resource_assigned = '$dataArray[txResQnty]', res_plan_start_date = '$sdate', res_plan_end_date = '$edate'  WHERE task_id='$dataArray[txTaskID]' AND  resource_name = '$dataArray[txResName]'";
			}
			else
			{				
				$resSql = "INSERT INTO $tableName ( task_id, task_name, project_id, task_parent, resource_type, resource_category, resource_name, resource_unit, resource_assigned, res_plan_start_date, res_plan_end_date ) "." VALUES ( '$dataArray[txTaskID]', '$dataArray[txTaskName]', '$dataArray[txProjectID]', '$dataArray[txTaskParent]', '$dataArray[txResType]', '$dataArray[txResCat]', '$dataArray[txResName]', '$dataArray[txResUnit]', '$dataArray[txResQnty]', '$sdate', '$edate')";
			}						
			
			$conn->query($resSql) or die('Error, task query failed....');
		
		//updating resource master table
			$projSql = "SELECT * FROM $project_resources WHERE project_id='$dataArray[txProjectID]' AND resource_type = '$dataArray[txResType]' AND resource_name = '$dataArray[txResName]' AND resource_category = '$dataArray[txResCat]'"; 
			$conn->query($projSql) or die('Error, query failed');
			$row = $conn->query($projSql)->fetch_array(MYSQLI_ASSOC);
			$resource_assigned_master = $row['resource_assigned_master'];	
			
		//	if( $resource_assigned_master != $dataArray['txResQnty'] ){						
				$resource_assigned_master = $resource_assigned_master + $dataArray['txResQnty'];

				$resSql = "UPDATE $project_resources SET resource_assigned_master = '$resource_assigned_master' WHERE project_id='$dataArray[txProjectID]' AND resource_type = '$dataArray[txResType]' AND resource_name = '$dataArray[txResName]' AND resource_category = '$dataArray[txResCat]'"; 
				$conn->query($resSql) or die('Error, query failed');				
		//	}
			
		}	
	}
//---------------Processing of resource usage data ends here ------------------------------

//>------------------ Setting heirarchy levels to tasks-------------------------------------

	/* $task_master - table name.
	* These 2 function are defined in /library/global_functions.php
	*/
	set_heirarchy( $project_id, $project_number, $task_master ); 
	rollup_dates( $project_id, $project_number, $task_master );
	
//<------------------------ Ends here ------------------------------------------------------

?>