<?php


	$todaysTasks="";
	
	if(isset($_REQUEST['pid']) ){
		
		if ($_REQUEST['pid'] != ""){
			$project_id = $_REQUEST['pid'];
		}
			
		$query = "SELECT * FROM $task_master WHERE project_id='$project_id'";
		$conn->query($query) or die('Error, query failed');
		$result = $conn->query($query);
		
		$today = date("Y-m-d");
		$today_dt = date_create($today);
		$todaysTasks = "	
		 <table class='table table-hover'>
		<thead>
		  <tr>
			<th>#</th>
			<th>Task Name</th>
			<th>Planned Start</th>
			<th>Planned End</th>
			<th>Duration</th>
		  </tr>
		</thead>
		<tbody>";
			
		$i=1;
		while ($row = $result->fetch_array(MYSQLI_ASSOC)){

			$expire_dt=date_create($row['task_actual_start_date']);
			if ($expire_dt == $today_dt) {
				$todaysTasks=$todaysTasks."<tr><td>".$i."</td><td>".$row['task_name']."</td>".
				"<td>".date_format(date_create($row['task_actual_start_date']),"d-M-Y")."</td>".
				"<td>".date_format(date_create($row['task_actual_end_date']),"d-M-Y")."</td>".
				"<td>".$row['numTaskDuration']."</td></tr>";
				$i++;
			}		
		}		
		$todaysTasks = $todaysTasks."</tbody></table>";	
		
	}




	
?>