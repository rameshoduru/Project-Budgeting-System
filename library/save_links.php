<?php
include('./dbconnect.php');

$data = $_REQUEST["saveJSONLinks"];

$data = array(explode("@$@", $data));

	$linkArray = array();
	foreach ($data as $value) {
		   foreach($value as $x => $x_value) {
			$temp = explode("~!^", $x_value);
			$linkArray[] = $temp[1];
		}
	}

	$sql = "INSERT INTO project_task_links ( project_id, numBaseLineNumber, txLinkID, txSourceID, txTargetID, txLinkType ) ".
			" VALUES ( '$linkArray[0]', '$linkArray[1]', '$linkArray[2]', '$linkArray[3]', '$linkArray[4]', '$linkArray[5]' )";	

	$conn->query($sql) or die('Error, query failed');
	
	
	//Re-arranging dates of tasks according to the links

		$linksql = "SELECT * FROM project_task_links WHERE project_id = '$linkArray[0]'"; 
		$conn->query($linksql) or die('Error, query failed');
		$links = $conn->query($linksql);
		
		if ( $links->num_rows > 0 ) {
			while($row = $links->fetch_array(MYSQLI_ASSOC)){
				$sourcetid = $linkArray[3];
				$targetid = $linkArray[4];
			
				$tsksql = "SELECT * FROM task_master WHERE task_id = '$sourcetid'"; 
				$conn->query($tsksql) or die('Error, query failed');				
				$stskresult = $conn->query($tsksql);
				$stskrow = $stskresult->fetch_array(MYSQLI_ASSOC);
				
				if ($stskresult->num_rows > 0) {
					$tskstart = $stskrow['task_actual_end_date'];
					
					$tsksql = "SELECT * FROM task_master WHERE task_id = '$targetid'"; 
					$conn->query($tsksql) or die('Error, query failed');				
					$ttskresult = $conn->query($tsksql);
					$ttskrow = $ttskresult->fetch_array(MYSQLI_ASSOC);
					$task_duration = $ttskrow['numTaskDuration'];					
					
					$date=date_create($tskstart);
					date_add($date,date_interval_create_from_date_string( $task_duration." days" ) );
					$tskend = date_format($date,"Y-m-d");					
					
					$sql = "UPDATE task_master SET task_actual_start_date = '$tskstart', task_actual_end_date = '$tskend' WHERE task_id = '$targetid'";
					$conn->query($sql) or die('Error, query failed');
				}				
			}
		}
		
?>