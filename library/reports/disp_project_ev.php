<?php
//include('../dbconnect.php');
//include('../appconfig.php');

//This file is called in pbsHome.php @ line#645
$progress = "";
$earnedValue = "";
global $project_id;

//	if ($_REQUEST['pid'] != ""){
//		$project_id = $_REQUEST['pid'];		
	//}
$project_id = $project_id;

//Fetching list of tasks from task_master table

		$earnedValue = "<div class='table-responsive'><table class='table table-hover table-striped'> <tr><th>Name</th><th>Work</th><th>Machinery</th><th>Material</th><th>Manpower</th></tr>";
		
	//Fetching project number
		$project_number_sql = "SELECT * FROM $project_master where project_id='$project_id'"; 
		$pno_row = $conn->query($project_number_sql)->fetch_array(MYSQLI_ASSOC) or die('Error, task query failed');
		$pno = $pno_row['project_number'];
		
	//Fetching list of tasks	
		$task_list_sql = "SELECT * FROM $task_master where project_id='$project_id'"; 
		$conn->query($task_list_sql) or die('Error, task query failed');
		
		$parentTasks = Array();		
		$taskNames = Array();
		
		$tasks_list = $conn->query($task_list_sql);
		while (	$trow = $tasks_list->fetch_array(MYSQLI_ASSOC) ) 
		{
			if($pno == $trow['task_parent'])
			{
				$parentTasks[] = $trow['task_id'];
			}
			else
			{
				$parentTasks[] = $trow['task_parent'];
			}			
			$taskNames[$trow['task_id']] = $trow['task_name'];			
		}
		
		$parentTasks = array_unique($parentTasks);
		$arrlength = count($parentTasks);
				
		$workTotal = 0;
		$macTotal = 0;
		$matTotal = 0;
		$manTotal = 0;
		
		for($x = 0; $x < $arrlength; $x++){
			if(isset($parentTasks[$x]))
			{
				$wrksql = "SELECT SUM(amount_spent) as wrkTotal FROM $task_work_consumed WHERE task_id = '$parentTasks[$x]'"; 
				$macsql = "SELECT SUM(amount_spent) as macTotal FROM $task_machinery_consumed WHERE task_id = '$parentTasks[$x]'"; 
				$matsql = "SELECT SUM(amount_spent) as matTotal FROM $task_material_consumed WHERE task_id = '$parentTasks[$x]'"; 
				$mnpsql = "SELECT SUM(amount_spent) as mnpTotal FROM $task_manpower_consumed WHERE task_id = '$parentTasks[$x]'"; 			

				$conn->query($wrksql) or die('Error, wrk task parent query failed');
				$result = $conn->query($wrksql);			
				$wrkrow = $result->fetch_array(MYSQLI_ASSOC);	

				$conn->query($macsql) or die('Error, mac task parent query failed');
				$result = $conn->query($macsql);			
				$macrow = $result->fetch_array(MYSQLI_ASSOC);

				$conn->query($matsql) or die('Error, mat task parent query failed');
				$result = $conn->query($matsql);			
				$matrow = $result->fetch_array(MYSQLI_ASSOC);

				$conn->query($mnpsql) or die('Error, man task parent query failed');
				$result = $conn->query($mnpsql);			
				$mnprow = $result->fetch_array(MYSQLI_ASSOC);			

				
				$workTotal = $workTotal + $wrkrow['wrkTotal'];
				$macTotal = $macTotal + $macrow['macTotal'];
				$matTotal = $matTotal + $matrow['matTotal'];
				$manTotal = $manTotal + $mnprow['mnpTotal'];
				
				if( isset($taskNames[$parentTasks[$x]])){
					$earnedValue = $earnedValue."<tr>".
					"<td><b>".$taskNames[$parentTasks[$x]]."</b></td>".
					"<td align='right'>".$wrkrow['wrkTotal']."</td>".
					"<td align='right'>".$macrow['macTotal']."</td>".
					"<td align='right'>".$matrow['matTotal']."</td>".
					"<td align='right'>".$mnprow['mnpTotal']."</td>";
				}				
			}
 
		}
		
		
		$GT = $workTotal+$macTotal+$matTotal+$manTotal;
	echo $earnedValue."</tr>".
					"<tr><td><b>Total</b></td><td align='right'><b>$workTotal</b></td><td align='right'><b>$macTotal</b></td>".
					"<td align='right'><b>$matTotal</b></td><td align='right'><b>$manTotal</b></td></tr>".
					"<tr><td><b>Grand Total</b></td><td align='right'><b>$GT</b></td><td></td><td></td><td></td></tr></table>";

	
?>
