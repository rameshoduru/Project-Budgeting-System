<?php
include('./dbconnect.php');

$project_id= "1CB05728";

		$projsql = "SELECT * FROM task_master WHERE project_id='$project_id'"; 
		$conn->query($projsql) or die('Error, query failed');
		$result = $conn->query($projsql);

		$original_records_array = Array();
		
			$i=0;
			while( $row = $result->fetch_array(MYSQLI_ASSOC)) 
			{
				$start_date = date_format(date_create($row['task_actual_start_date']),"d-m-Y");
				$end_date = date_format(date_create($row['task_actual_end_date']),"d-m-Y");
			
				$original_record = 'txTaskType~!^'.$row['activity_type'].'@$@txProjectID~!^'.$project_id.'@$@numBaseLineNumber~!^'.'1'.'@$@txTaskID~!^'.$row['task_id'].'@$@txTaskName~!^'.$row['task_name'].'@$@txTaskParent~!^'.$row['task_parent'].'@$@dtTPSD~!^'.$start_date.'@$@dtTPED~!^'.$end_date.'@$@numTaskDuration~!^'.$row['numTaskDuration'].'@$@proj_manager~!^'.$row['proj_manager'].'@$@numManPowerWght~!^'.$row['numManPowerWght'].'@$@numMachinaryWght~!^'.$row['numMachinaryWght'].'@$@numMaterialWght~!^'.$row['numMaterialWght'].'@$@*~*@$@';
				
				$task_id = $row['task_id'];
				$task_name = $row['task_name'];
				$parent_task = $row['task_parent'];
				
				$resDetailsWRK = resource_consumed( 'task_work_consumed', $project_id, $task_id, $task_name, $parent_task ); 
				$resDetailsMNP = resource_consumed( 'task_manpower_consumed', $project_id, $task_id, $task_name, $parent_task ); 
				$resDetailsMAC = resource_consumed( 'task_machinery_consumed', $project_id, $task_id, $task_name, $parent_task ); 
				$resDetailsMAT = resource_consumed( 'task_material_consumed', $project_id, $task_id, $task_name, $parent_task );
				
				//$original_record = $original_record.'*$~$*'.$resDetailsWRK.'*$~$*'.$resDetailsMNP.'*$~$*'.$resDetailsMAC.'*$~$*'.$resDetailsMAT.'*$~$*';
				
				$original_records_array[$i] = $original_record;
			
				$i++;
			}

			echo $original_records_array[0].'</br></br>';	
			
		function resource_consumed($tableName, $project_id, $task_id, $task_name, $parent_task ){
			global $conn;
			
			$resArray = array();
			$resDetails = "";
			
			$qry = "SELECT * FROM $tableName WHERE task_id='$task_id'"; 
			$conn->query($qry) or die('Error, work allocation - query failed');
			$resresult = $conn->query($qry);
			
			if ($resresult->num_rows > 0) {
				$i=0;
				while( $row = $resresult->fetch_array(MYSQLI_ASSOC)) 
				{				
					$res_name = $row['resource_name'];
					$res_cat = $row['resource_category'];
					$res_quantity = $row['resource_assigned'];
					$res_type = $row['resource_type'];				
					$res_unit = $row['resource_unit'];
					
					$resDetails = 'txResTable~!^'.$tableName.'@$@txTaskID~!^'.$task_id.'@$@txTaskName~!^'.$task_name.'@$@txProjectID~!^'.$project_id.'@$@numBaseLineNumber~!^1@$@txTaskParent~!^'.$parent_task.'@$@txResType~!^'.$row['resource_type'].'@$@txResName~!^'.$row['resource_name'].'@$@txResCat~!^'.$row['resource_category'].'@$@txResUnit~!^'.$row['resource_unit'].'@$@txResQnty~!^'.$row['resource_assigned'];
								
					$i++;					
				}						
			}
		return $resDetails;	
		}
			
?>