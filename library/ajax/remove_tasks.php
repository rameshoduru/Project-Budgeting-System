<?php
include('../dbconnect.php');
include('../appconfig.php');

//Removing tasks and related assigned resource values if the task is removed by the user
//CREATE TABLE IF NOT EXISTS log_records( id int(11) AUTO_INCREMENT PRIMARY KEY, project_id varchar(150), task_id varchar(150) ) - For testing
//ALTER TABLE `task_work_consumed` ADD `resCategory` VARCHAR(100) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL AFTER `resourceType`;

$taskListUpdated = $_REQUEST["tskList"];
$taskListUpdated = array_map('trim', explode("~", $taskListUpdated));
$project_id = $_REQUEST["pid"];


//This block of code is used to remove tasks those are deleted by the user from the browser	
		$sql_ta = "SELECT * FROM $task_master WHERE project_id='$project_id'"; 
		$conn->query($sql_ta) or die('Error, query failed');
		$result = $conn->query($sql_ta);

		$taskListActual = Array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)){
			$taskListActual[] = $row['task_id'];
		}
		
		if( $taskListUpdated !="" ){
			$taskRemoved = array_diff( $taskListActual , $taskListUpdated );
		}else{
			$taskRemoved[0] = $taskListActual[0];	
		}


	foreach( $taskRemoved as $tr ){
		if( $tr != ""){							
			$sql_td = "DELETE FROM $task_master WHERE task_id = '$tr'"; 
			$conn->query($sql_td) or die('Error, query failed');

			resetAssingedValues( $task_work_consumed, $project_id, $tr );
			resetAssingedValues( $task_manpower_consumed, $project_id, $tr );
			resetAssingedValues( $task_material_consumed, $project_id, $tr );
			resetAssingedValues( $task_machinery_consumed, $project_id, $tr );
		}
	}
		
	
	function resetAssingedValues($resTable, $project_id, $tr ){
		
		global $conn;
		
		$sql = "SELECT * FROM $resTable WHERE task_id = '$tr'"; 
		$conn->query($sql) or die('Error, query failed');
		$result = $conn->query($sql);
		
		while ($row = $result->fetch_array(MYSQLI_ASSOC)){

			$sql_tbd = "DELETE FROM $resTable WHERE task_id = '$tr' AND resourceType='$row[resourceType]' AND resourceName = '$row[resourceName]' AND resCategory='$row[resCategory]'"; 
			$conn->query($sql_tbd) or die('Error, query failed');
					
		}		

	}
			
?>