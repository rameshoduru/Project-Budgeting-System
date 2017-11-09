<?php

function set_heirarchy( $project_id, $project_number, $task_master )
{
	global $conn;
	
	/*
	* This block of code sets heirarchy to all parent tasks at top most level
	* Then it creates an array of child tasks which is set with relavant heirarchy levels
	*/
	$task_list_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE project_id='$project_id'");
	$child_tasks_list = array();
	$task_count = 0;	
	$top_heirarchy = 1;
	while( $row = mysqli_fetch_array( $task_list_result )) 
	{		
		if( $row['task_parent'] != $project_number )
		{			
			$child_tasks_list[$task_count] = $row['task_parent'];
			$task_count++;
		}else{
			$set_hierarchy_sql = "UPDATE $task_master SET task_hierarchy='$top_heirarchy' WHERE task_id = '$row[task_id]'";
			mysqli_query($conn, $set_hierarchy_sql);	
			$top_heirarchy = $top_heirarchy + 1;
		}
	}
	
	/*
	* Child tasks are processed to assign heirarchy levels
	*/
	
	foreach( array_unique($child_tasks_list) as $child_task )
	{
		$parent_task_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_id=$child_task");
		$parent_task_row = mysqli_fetch_array($parent_task_result);
		$parent_heirarchy = $parent_task_row['task_hierarchy'];
	
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


/*
* Adjusting or updating task start date and end date to the parent/primary tasks
* This function is called in save_data.php file
*/

function rollup_dates( $project_id, $project_number, $task_master )
{
	global $conn;
	//fetching primary tasks from task master table
	$tasks_primary_result = mysqli_query( $conn, "SELECT * FROM $task_master WHERE task_parent='$project_number' AND project_id='$project_id'" );
	$task_parent = array();		
	while( $row = mysqli_fetch_array( $tasks_primary_result ))
	{
		//Processing each task by fetching all sub tasks associated with the it
		$her = $row['task_hierarchy'].'%';
		$task_list_result = mysqli_query( $conn, "SELECT * FROM $task_master WHERE task_hierarchy LIKE '$her'" );
		$task_count = 0;
		//collecting parent task ids 
		while( $row = mysqli_fetch_array( $task_list_result )) 
		{	
			if( $row['task_parent'] == $project_number )
			{
				$task_parent[] = $row['task_id'];
			}else{
				$task_parent[] = $row['task_parent'];
			}			
		}		
	}
	
	$task_parent = array_unique( $task_parent );
	for ($x = 0; $x <= count( $task_parent ); $x++)
	{
		foreach ( $task_parent as $tskp )
		{
			$task_end_dates = array();
			$task_start_dates = array();
			$task_list_result = mysqli_query( $conn, "SELECT * FROM $task_master WHERE task_parent = '$tskp'" );
			while( $row = mysqli_fetch_array( $task_list_result )) 
			{	
				$task_end_dates[] = $row['task_actual_end_date'];
				$task_start_dates[] = $row['task_actual_start_date'];
			}
			if( count( $task_end_dates ) != 0 )
			{
				sort( $task_end_dates );
				sort( $task_start_dates );
				$end_date =  end( $task_end_dates );
				$start_date = current( $task_start_dates );			
				$task_updt_sql = mysqli_query( $conn, "UPDATE $task_master SET has_child='1', task_actual_end_date='$end_date' , task_actual_start_date='$start_date' WHERE task_id = '$tskp'" );			
			}
		}		
	}
}

?>