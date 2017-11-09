<?php

//This function is called in resource_rollup.php
	
function process_reports($task_parent , $resource_table)
{
	global $conn;
	global $task_master;
	global $project_number;
	global $reports_resource;
	
	$task_resource_assigned = array();
	$task_resource_spent = array();
	$task_resource_utilized = array();
			
	foreach( array_unique($task_parent) as $tsk_parent )
	{
		//echo $tsk_parent.'<br>';
		$resource_list_result = mysqli_query($conn, "SELECT * FROM $resource_table WHERE task_parent='$tsk_parent'");
	
		while( $res_row = mysqli_fetch_array( $resource_list_result )) 
		{	
			//Fetching parent task id of the parent of child resource tasks
			if( $res_row['task_parent'] == $project_number ){
				$parent_list_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_id='$res_row[task_id]'");			
			}else{
				$parent_list_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_id='$res_row[task_parent]'");				
			}

			$parent_row = mysqli_fetch_array( $parent_list_result );
				
			if( ($res_row['task_parent'] != $project_number) OR  $parent_row['has_child'] == 0 )
			{
				
				//Appending parent task id of the parent of child resource task
				$a_key = $res_row['project_id']."*".$res_row['task_parent']."*".$parent_row['task_name']."*".$parent_row['task_hierarchy']."*".$res_row['resource_type']."*".$res_row['resource_category']."*".$res_row['resource_name']."*".$parent_row['task_parent']."*".$res_row['resource_unit'];					
				
				//creating array key for resource amount spent
				$amount_spent_key = $a_key.'*amount_spent';	
				
				//creating array key for resource utilized
				$resource_utilized_key = $a_key.'*res_utilized';
				
				if(!isset($task_resource_assigned[$a_key])){
					$task_resource_assigned[$a_key] = $res_row['resource_assigned'];
					$task_resource_utilized[$resource_utilized_key] = $res_row['resource_utilized'];
					$task_resource_spent[$amount_spent_key] = $res_row['amount_spent'];
				}
				else
				{
						$net_assigned = $task_resource_assigned[$a_key] + $res_row['resource_assigned'];
					$task_resource_assigned[$a_key] = $net_assigned;
						
						$net_utilized = $task_resource_utilized[$resource_utilized_key] + $res_row['resource_utilized'];
					$task_resource_utilized[$resource_utilized_key] = $net_utilized;
					
						$net_amount_spent = $task_resource_spent[$amount_spent_key] + $res_row['amount_spent'];
					$task_resource_spent[$amount_spent_key] = $net_amount_spent;				
				}					
			}		
		}
	}

	if (isset($task_resource_assigned))
	{
		$task_resource_keys = array_keys($task_resource_assigned);
		$j = 0;
		$res_key_ar = array();	
		foreach($task_resource_keys as $t_assigned)
		{
			$utilized_key = $t_assigned.'*res_utilized';
			$amount_apent_key = $t_assigned.'*amount_spent';
			
			$res_key_ar = explode("*", $t_assigned );
			$result = mysqli_query($conn, "SELECT * FROM $reports_resource WHERE project_id='$res_key_ar[0]' AND task_id='$res_key_ar[1]' AND task_name='$res_key_ar[2]' AND task_hierarchy='$res_key_ar[3]' AND resource_type='$res_key_ar[4]' AND resource_category='$res_key_ar[5]' AND resource_name='$res_key_ar[6]' AND task_parent='$res_key_ar[7]'");
			$row = mysqli_fetch_array($result);
			
			if(!isset($row['task_id']))
			{
				$rpt_sql = "INSERT INTO $reports_resource ( project_id, task_id, task_name, task_hierarchy, resource_type, resource_category, resource_name, task_parent, resource_unit , resource_assigned, resource_utilized , amount_spent ) VALUES ( '$res_key_ar[0]' , '$res_key_ar[1]' , '$res_key_ar[2]' , '$res_key_ar[3]' , '$res_key_ar[4]' , '$res_key_ar[5]' , '$res_key_ar[6]' , '$res_key_ar[7]' , '$res_key_ar[8]' , '$task_resource_assigned[$t_assigned]', '$task_resource_utilized[$utilized_key]' , '$task_resource_spent[$amount_apent_key]')"; 
				$conn->query($rpt_sql) or die('Error, task query failed - reports_resource INSERT statement');
			}
			else
			{
				$rpt_sql = "UPDATE $reports_resource SET resource_unit= '$res_key_ar[8]', resource_assigned = '$task_resource_assigned[$t_assigned]' , resource_utilized = '$task_resource_utilized[$utilized_key]' , amount_spent='$task_resource_spent[$amount_apent_key]'  WHERE project_id='$res_key_ar[0]' AND task_id='$res_key_ar[1]' AND task_name='$res_key_ar[2]' AND task_hierarchy='$res_key_ar[3]' AND resource_type='$res_key_ar[4]' AND resource_category='$res_key_ar[5]' AND resource_name='$res_key_ar[6]' AND task_parent='$res_key_ar[7]'";
				
				$conn->query($rpt_sql) or die('Error, task query failed - reports_resource UPDATE statement');
			}
			
			$j = $j+1;
		}
	}
}
	
?>