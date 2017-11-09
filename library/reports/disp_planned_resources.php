<?php

//***************************************Display resource details********************************
	$workTab = "";
	$manpowerTab = "";
	$materialTab = "";
	$machineryTab = "";
	$tasksList = "";
	$todaysTasks="";
	
	if(isset($_REQUEST['pid']) ){
		
		if ($_REQUEST['pid'] != ""){
			$project_id = $_REQUEST['pid'];
		}
		
//Display resource details 
		$proj_res_sql = "SELECT * FROM $project_resources WHERE project_id='$project_id'"; 
		$conn->query($proj_res_sql) or die('Error, query failed');
		$result = $conn->query($proj_res_sql);
		
		if ( $result->num_rows > 0 ) {		
			$workTab="<div class='table-responsive'><table class='table table-hover table-striped'> <tr><th>Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th> Utilized</th></tr>";
			$manpowerTab = "<div class='table-responsive'><table class='table table-hover table-striped'><tr><th>Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Utilized</th></tr>";
			$materialTab = "<div class='table-responsive'><table class='table table-hover table-striped'><tr><th>Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Utilized</th></tr>";
			$machineryTab = "<div class='table-responsive'><table class='table table-hover table-striped'><tr><th>Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Utilized</th></tr>";
					
			while( $projresrow = $result->fetch_array(MYSQLI_ASSOC)) 
			{
				if($projresrow['resource_type'] == "Work"){
					$workTab = $workTab."<tr>".
					"<td>".htmlspecialchars($projresrow['resource_category'])."</td>".
					"<td>".htmlspecialchars($projresrow['resource_name'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_quantity'])."</td>".
					"<td align='center'>".htmlspecialchars($projresrow['resource_unit'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_rate'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_value'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_utilized_master'])."</td></tr>";
				}
				if($projresrow['resource_type'] == "Manpower"){
					$manpowerTab = $manpowerTab."<tr>".
					"<td>".htmlspecialchars($projresrow['resource_category'])."</td>".
					"<td>".htmlspecialchars($projresrow['resource_name'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_quantity'])."</td>".
					"<td align='center'>".htmlspecialchars($projresrow['resource_unit'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_rate'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_value'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_utilized_master'])."</td></tr>";
				}
				if($projresrow['resource_type'] == "Material"){
					$materialTab = $materialTab."<tr>".
					"<td>".htmlspecialchars($projresrow['resource_category'])."</td>".
					"<td>".htmlspecialchars($projresrow['resource_name'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_quantity'])."</td>".
					"<td align='center'>".htmlspecialchars($projresrow['resource_unit'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_rate'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_value'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_utilized_master'])."</td></tr>";
				}
				if($projresrow['resource_type'] == "Machinery"){
					$machineryTab = $machineryTab."<tr>".
					"<td>".htmlspecialchars($projresrow['resource_category'])."</td>".
					"<td>".htmlspecialchars($projresrow['resource_name'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_quantity'])."</td>".
					"<td align='center'>".htmlspecialchars($projresrow['resource_unit'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_rate'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_value'])."</td>".
					"<td align='right'>".htmlspecialchars($projresrow['resource_utilized_master'])."</td></tr>";
				}				
			}
			$workTab = $workTab."</table></div>";
			$manpowerTab = $manpowerTab."</table></div>";
			$materialTab = $materialTab."</table></div>";
			$machineryTab = $machineryTab."</table></div>";
		}
	}
//***************************************Display resource details - ends ********************************

	
?>