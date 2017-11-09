<?php
include('../dbconnect.php');
include('../appconfig.php');

//This function is called at the end of form submit action in appFunction.js
//'task_hierarchy' column for each task in task_master table will be updated here.

$project_id = $_REQUEST["project_id"];

//Fetchin project number_format
	$result = mysqli_query($conn, "SELECT project_number FROM project_master WHERE project_id='$project_id'");
	$task_rows = mysqli_fetch_array($result);
	$project_number = $task_rows['project_number'];
	
set_heirarchy( $project_id, $project_number, $task_master );

function set_heirarchy( $project_id, $project_number, $task_master )
{
	global $conn;
	
	try{
		$task_list_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE project_id='$project_id'");
		$task_count = 0;
		while( $row = mysqli_fetch_array( $task_list_result )) 
		{		
			if($row['task_parent'] != $project_number)
			{			
				$child_tasks_list[$task_count] = $row['task_parent'];
				$task_count++;
			}
		}
		
		foreach( array_unique($child_tasks_list) as $child_task )
		{
			$parent_task_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_id=$child_task");
			$parent_task_row = mysqli_fetch_array($parent_task_result);
			$parent_heirarchy = $parent_task_row['task_hierarchy'];
			//echo $child_task.' -- '.$parent_heirarchy.'<br>';
			
			$child_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_parent=$child_task");
			$j=1;
			while( $child_result_row = mysqli_fetch_array($child_result) )
			{	
				$task_hierarchy = $parent_heirarchy.'.'.$j;
				$set_hierarchy_sql = "UPDATE $task_master SET task_hierarchy='$task_hierarchy' WHERE task_id='$child_result_row[task_id]'";
				mysqli_query($conn, $set_hierarchy_sql);
				
				$j++;
			}
		}	
	}
	catch (Exception $e) 
	{
		die($logmsg = $e->getMessage());
	}
}


?>

