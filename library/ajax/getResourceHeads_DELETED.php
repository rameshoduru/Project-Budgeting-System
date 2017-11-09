<?php
//This AJAX call is called in appOnLoad.js file to populate task category/s

include('../dbconnect.php');

if(isset($_REQUEST['pid']) ){

	if ($_REQUEST['pid'] != ""){
		$project_id = $_REQUEST['pid'];		
	}
	
	//Fetching list of resource categories in a project
		$query = "SELECT resource_category FROM project_resources WHERE project_id='$project_id'";
		$conn->query($query) or die('Error, query failed');
		$result = $conn->query($query);
			$resource_heads = Array();
			
		while ($resrow = $result->fetch_array(MYSQLI_ASSOC)){
			$resource_heads[] = $resrow['resource_category'];
		}
		$resource_heads = array_unique($resource_heads);
//		$resource_heads = join("~",$resource_heads);
//		$resource_heads = split("~", $resource_heads);	
	$resCat = "";
	
	foreach($resource_heads as $cat) {
		$resCat = $resCat.'<option value='.$cat.'>'.$cat.'</option>';
	}		
	echo $resCat;	
}

?>