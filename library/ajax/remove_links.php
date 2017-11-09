<?php
include('../dbconnect.php');
include('../appconfig.php');


//This code is called in function 'fnSubmit()' in the appFunctions.js file to reset task links

$projid = $_REQUEST["projid"];

	$sql = "SELECT * FROM project_task_links WHERE project_id='$projid'"; 
	$conn->query($sql) or die('Error, query failed');
	$Result = $conn->query($sql);
	$row = $Result->fetch_array(MYSQLI_ASSOC);
		
	if( $row['project_id'] != "" ){
		$sql = "DELETE FROM project_task_links WHERE project_id='$projid'";	
		$conn->query($sql) or die('Error, query failed');		
	}



?>