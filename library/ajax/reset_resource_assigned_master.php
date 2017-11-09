<?php
//This called in appFunctions.js at line#154
// This will reset the assigned resources in the project_resources table. 'resource_assigned_master' --> 0

include('../dbconnect.php');

if(isset($_REQUEST['pid']) ){

	if ($_REQUEST['pid'] != ""){
		$project_id = $_REQUEST['pid'];		
	}

	$resSql = "UPDATE project_resources SET resource_assigned_master = 0 WHERE project_id='$project_id'"; 
	$conn->query($resSql) or die('Error, query failed');		
}

?>