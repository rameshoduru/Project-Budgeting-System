<?php

//This program is called within the Progress_update.php file
// Inside JS function 'fnUpdateProgress()' at line# 153 this program is called thru AJAX call_user_func
include('../session.php');
include('../globalClasses.php');


$reqContent = $_REQUEST["updateData"]; 

//$reqContent ="task_work_consumed^@$@^Work~$~Work~$~Work~$~Work~$~Work^@$@^road~$~Cabin~$~Restaurant~$~Bridge~$~Walkway^@$@^M~$~SFT~$~SFT~$~M~$~M^@$@^HCN01~$~HCN02~$~CDE01~$~CDE02~$~CDE02^@$@^5*$$*9*$$*7*$$*0*$$*0*@$@*7234B54C*@$@*150587745916*@$@*North Gate"; 



$reqContent = explode("*@$@*", $reqContent);

		$project_id = $reqContent[1];
		$task_id = $reqContent[2];
		$taskName = $reqContent[3];
		$user_id = $login_session;
		
//Splitting data according to type 		
$updateData = explode( "@^@", $reqContent[0]);

		$msg1 = "ALERT: You have entered completed resource usage more than the planned for these resources!!";
		$msg = "";
		
//Updating resource utilization details
	For($intLoop=0; $intLoop < count($updateData); $intLoop++) {
		If ($updateData[$intLoop] != ""){
			
			$resData = explode( "^@$@^", $updateData[$intLoop] );
			$resTypes = explode( "~$~", $resData[1] );
			$resNames = explode( "~$~", $resData[2] );
			$resUnits = explode( "~$~", $resData[3] );
			$resCategories = explode( "~$~", $resData[4] );
			$varEntered = explode( "*$$*", $resData[5] );
			
		//Validating the values entered against planned
			For($intDataLoop=0; $intDataLoop < count($varEntered); $intDataLoop++) 
			{				
				$ressql = "SELECT * FROM $resData[0] WHERE task_id='$task_id' AND resource_name ='$resNames[$intDataLoop]'"; 
				$conn->query($ressql) or die('Error, query failed - line# 47 - UpdateTaskProgress.php');
				$result = $conn->query($ressql);
				$resrow = $result->fetch_array(MYSQLI_ASSOC);
				
				$plannedValue = $resrow['resource_assigned'];
				$varActuals = $resrow['resource_utilized'];
				if((count($varActuals) >= $intDataLoop) )
				{
					$consumed = $varEntered[$intDataLoop] + $varActuals;
				}
				
				if( $consumed > $plannedValue )
				{
					$msg = $msg."\n".$resNames[$intDataLoop];	
					goto myFunction;
				}
			}
			

		//Updating actuals 	
			For($intDataLoop=0; $intDataLoop < count($varEntered); $intDataLoop++) 
			{	
				$proj_res_sql = "SELECT * FROM $project_resources WHERE project_id='$project_id' AND resource_name ='$resNames[$intDataLoop]'"; 
				$conn->query($proj_res_sql) or die('Error, query failed  - line# 59 - UpdateTaskProgress.php');
				$pro_result = $conn->query($proj_res_sql);
				$projresrow = $pro_result->fetch_array(MYSQLI_ASSOC);
				$projUtilized = $projresrow['resource_utilized_master'];
				$amount_spent_master = $projresrow['amount_spent'];
				$resourceQuantity = $projresrow['resource_quantity'];
				$projResourceRate  = $projresrow['resource_rate'];

				$ressql = "SELECT * FROM $resData[0] WHERE task_id='$task_id' AND resource_category='$resCategories[$intDataLoop]' AND resource_name ='$resNames[$intDataLoop]'"; 
				$conn->query($ressql) or die('Error, query failed');
				$result = $conn->query($ressql);
				$resrow = $result->fetch_array(MYSQLI_ASSOC);
				$amount_spent = $resrow['amount_spent'];				
				$plannedValue = $resrow['resource_assigned'];
				$varActuals = $resrow['resource_utilized'];
				
				//updating resource consumed at project and task level
				$consumed = $varEntered[$intDataLoop] + $varActuals;
				
				//Updating amount spent at task level				
				if( $consumed <= $plannedValue ){
					$dblnumCompPrsnt = ( $consumed / $plannedValue ) * 100;
					$dblnumCompPrsnt = number_format( $dblnumCompPrsnt, 2 );
					
					//adding amount spent with latest resource rate*utilized quantity
					$amountSpent = $projResourceRate * $varEntered[$intDataLoop];
					$amount_cumulative = $amount_spent + $amountSpent;
					$amount_spent_master = $amount_spent_master + $amountSpent; //updating resource master table

					if( $varEntered[$intDataLoop] != 0 )
					{
						$value_entered = $varEntered[$intDataLoop];
						echo $value_entered."<br>";
						echo $consumed."<br><br>";
						$ressql = "UPDATE $resData[0] SET resource_utilized = '$consumed', resourceCP = '$dblnumCompPrsnt', amount_spent = '$amount_cumulative' WHERE task_id='$task_id' AND resource_name ='$resNames[$intDataLoop]'";
						$conn->query($ressql) or die('Error, update query failed  - line# 80 - UpdateTaskProgress.php');	
						
						$projUtilized = $projUtilized + $varEntered[$intDataLoop];	
						$projResAvailable = $resourceQuantity - $projUtilized;
						
						$proj_res_sql = "UPDATE $project_resources SET resource_utilized_master = '$projUtilized', projResAvailable = '$projResAvailable' , amount_spent = '$amount_spent_master' WHERE project_id='$project_id' AND resource_name ='$resNames[$intDataLoop]' AND resource_category ='$resCategories[$intDataLoop]'";
						$conn->query($proj_res_sql) or die('Error, update proj_res_sql query failed - line#97');
				
						
						$logmsg = $user_id.": Updated actuals against ".strtoupper($resNames[$intDataLoop]).": Value entered : ".$value_entered;
	
						$resource_category = $resCategories[$intDataLoop];
						$resource_name = $resNames[$intDataLoop];
						$resource_type = $resTypes[$intDataLoop];
						$resource_unit = $resUnits[$intDataLoop];
						
						resource_utilizaton( $project_id, $task_id, $taskName, $resource_category, $resource_name, $resource_type, $resource_unit, $value_entered, $amountSpent, $user_id );
					}
				}
			}					
		}
	}
	
if($msg != ""){
echo $msg1."\n".$msg;	
}
	
//Rollingup resource utilization to task level
	$txWorkCP = resourceCP("task_work_consumed", $task_id);
	$txManPowerCP = resourceCP("task_manpower_consumed", $task_id);
	$txMaterialCP = resourceCP("task_material_consumed", $task_id);
	$txMachineryCP = resourceCP("task_machinery_consumed", $task_id);
		
	function resourceCP($tbl, $task_id){
		global $conn;
		
		$ressql = "SELECT AVG(resourceCP) as total FROM $tbl WHERE task_id = '$task_id'"; 
		$conn->query($ressql) or die('Error, task parent query failed  - line# 109 - UpdateTaskProgress.php');
		$result = $conn->query($ressql);
		
		$row = $result->fetch_array(MYSQLI_ASSOC);		
		$varCompletion = $row['total'];
		return $varCompletion;		
	}	

	$tasksql = "SELECT * FROM task_master WHERE task_id='$task_id'"; 
	$conn->query($tasksql) or die('Error, query failed  - line# 118 - UpdateTaskProgress.php');
	$result = $conn->query($tasksql);
	$task_row = $result->fetch_array(MYSQLI_ASSOC);
	
		If ($task_row['numManPowerWght'] != "0" And $task_row['numMachinaryWght'] != "0" And $task_row['numMaterialWght'] <> "0"){
			$overAllCompletionMP = ( $txManPowerCP/100 ) * $task_row['numManPowerWght'];
			$overAllCompletionMC = ( $txMachineryCP/100 ) * $task_row['numMachinaryWght'];
			$overAllCompletionMT = ( $txMaterialCP/100 ) * $task_row['numMaterialWght'];
			$dblnumCompPrsnt = $overAllCompletionMP + $overAllCompletionMC + $overAllCompletionMT;
			$numCompPrsnt = number_format($dblnumCompPrsnt,2);
			
		}else{
			$overAllCompletionMP = ( $txManPowerCP * 33.4 );
			$overAllCompletionMC = ( $txMachineryCP * 33.3 );
			$overAllCompletionMT = ( $txMaterialCP * 33.3 );
			$dblnumCompPrsnt = $overAllCompletionMP + $overAllCompletionMC + $overAllCompletionMT;
			$numCompPrsnt = number_format($dblnumCompPrsnt,2);					
		}	
	
	$sql = "UPDATE task_master SET txManPowerCP = '$txManPowerCP', txMachineryCP = '$txMachineryCP',".
	" txWorkCP = '$txWorkCP', txMaterialCP = '$txMaterialCP', numCompPrsnt = '$numCompPrsnt' WHERE task_id='$task_id'";	
	$conn->query($sql)or die('Error, update task_master query failed  - line# 139 - UpdateTaskProgress.php');;

myFunction:
 echo "Exited";
