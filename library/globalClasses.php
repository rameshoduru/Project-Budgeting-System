<?php

	function log_activities( $user_id, $project_id, $task_id, $logmsg ){
		
		global $conn;
		
		date_default_timezone_set("Asia/Kolkata"); 
		$log_date = date('Y-m-d H:i:s');
		
		$logsql = "INSERT INTO activities_log( log_date, user_id, project_id, task_id, log_message)".
				" VALUES ( '$log_date', '$user_id', '$project_id', '$task_id', '$logmsg')";			
	//	$conn->query($logsql) or die('Error, update activities_log - query failed  - line# 109 - globalClasses.php');
	}

	function resource_utilizaton( $project_id, $task_id, $taskName, $resource_category, $resource_name, $resource_type, $resource_unit, $value_entered, $amountSpent, $user_id ){
		
		global $conn;
		
		date_default_timezone_set("Asia/Kolkata"); 
		$transaction_date = date('Y-m-d H:i:s');

		$trans_sql = "INSERT INTO resource_utilization( transaction_date,  project_id, task_id, task_name, resource_type, resource_name, resource_category, resource_unit, resource_utilized, amount_spent, user_id)". 
		" VALUES ( '$transaction_date', '$project_id', '$task_id', '$taskName', '$resource_type', '$resource_name', '$resource_category', '$resource_unit', '$value_entered', '$amountSpent', '$user_id')";	
				
		$conn->query($trans_sql) or die('Error, resource_utilizaton - query failed  - line#24 - globalClasses.php');
	}

	
?>

