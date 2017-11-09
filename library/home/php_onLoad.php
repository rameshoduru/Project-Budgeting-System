<?php

function php_onLoad($progtype){
		global $project_id;
		global $conn;
$resDetails = "";
	
	$proj_master_table = $GLOBALS["project_master"];
	$task_master_table = $GLOBALS["task_master"];
	$task_work_table = $GLOBALS["task_work_consumed"];
	$task_manpower_table = $GLOBALS["task_manpower_consumed"];
	$task_machinery_table = $GLOBALS["task_machinery_consumed"];
	$task_material_table = $GLOBALS["task_material_consumed"];	
		
	//Fetching values from project_master table  echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
	$projSql = "SELECT * FROM $proj_master_table where project_id='$project_id'"; 
	$conn->query($projSql) or die('Error, project query failed line# 35');
	$projResult = $conn->query($projSql);
	$row = $projResult->fetch_array(MYSQLI_ASSOC); 

	$projName = $row['project_name'];
	
	$id = $row['project_number'];
	
	$numBaseLineNumber = $row['proj_base_line_number'];
	$manpowerw = '"'.$row['numManPowerWght'].'"';
	$machineryw = '"'.$row['numMachinaryWght'].'"';
	$materialw = '"'.$row['numMaterialWght'].'"';
	$manpowerrused = '"'.$row['txManPowerUsed'].'"';
	$manpowerrcp = '"'.$row['txManPowerCP'].'"';
	$worktyperused = '"'.$row['txWorkDone'].'"';
	$worktypercp = '"'.$row['txWorkCP'].'"';
	$machineryrused = '"'.$row['txMachineryUsed'].'"';
	$machineryrcp = '"'.$row['txMachineryCP'].'"';
	$materialrused = '"'.$row['txMaterialUsed'].'"';
	$materialrcp = '"'.$row['txMaterialCP'].'"';
	$overallCP = '"'.$row['task_overallCP'].'"';
	$text = '"'.$row['project_name'].'"';
	$type = '"'.$row['activity_type'].'"';
	$start_date = date_format(date_create($row['proj_actual_start_date']),"d-m-Y");	
	$start_date = '"'.$start_date.'"';
	$end_date = date_format(date_create($row['proj_actual_end_date']),"d-m-Y");	
	$end_date = '"'.$end_date.'"';
	$duration = $row['proj_duration'];
	$progress = 0;
	$open = 'true';
	$users = '"'.$row['proj_users'].'"';
	$parent = $row['txTaskParent'];
	
	$projArray = array(); 
	$projArray = array('"id": '.$id, '"manpowerw": '. $manpowerw, '"machineryw": '. $machineryw, '"materialw": '. $materialw, '"manpowerrused": '.  
						$manpowerrused, '"manpowerrcp": '. $manpowerrcp, '"worktyperused": '. $worktyperused, '"worktypercp": '. $worktypercp, '"machineryrused": '. 
						$machineryrused, '"machineryrcp": '. $machineryrcp, '"materialrused": '. $materialrused, '"materialrcp": '. $materialrcp, '"overallCP": '. 
						$overallCP, '"manpowerr":[]', '"worktyper":[]',	'"machineryr":[]', '"materialr":[]','"text": '. $text, '"type": '.$type , '"start_date": '
						.$start_date, '"end_date": '.$end_date, '"duration": '.$duration, '"progress": '.'0', '"open": '.'true', '"users": '.$users, '"parent": '.$parent, '"has_child": '.'0'. " }" );

	$projdata = '"data":[ {';
		foreach($projArray as $proj) {
			$projdata = $projdata.$proj.' , ';
		}
	//project details ends here -------------------------------------

	//Fetching task details	
	$taskSql = "SELECT * FROM $task_master_table where project_id='$project_id'"; 
	$conn->query($taskSql) or die('Error, task query failed line#61 - function - php_onload');
	$result = $conn->query($taskSql);

	$taskArray = array();
	$taskList = array();
	
	if ($result->num_rows > 0) {		
		$i=0;
		while( $row = $result->fetch_array(MYSQLI_ASSOC)) 
		{
			$id = $row['task_id'];
			$task_id = $row['task_id'];
			$has_child = $row['has_child'];
			$manpowerw = '"'.$row['numManPowerWght'].'"';
			$machineryw = '"'.$row['numMachinaryWght'].'"';
			$materialw = '"'.$row['numMaterialWght'].'"';
			$manpowerrused = '"'.$row['txManPowerUsed'].'"';
			$manpowerrcp = '"'.$row['txManPowerCP'].'"';
			$worktyperused = '"'.$row['txWorkDone'].'"';
			$worktypercp = '"'.$row['txWorkCP'].'"';
			$machineryrused = '"'.$row['txMachineryUsed'].'"';
			$machineryrcp = '"'.$row['task_mach_machineryrcp'].'"';
			$materialrused = '"'.$row['task_mat_materialrused'].'"';
			$materialrcp = '"'.$row['task_mat_materialrcp'].'"';
			$overallCP = '"'.$row['task_overallCP'].'"';
			$categories = '"'.$row['assigned_categories'].'"';
			$text = $row['task_name'];
			$type = '"'.$row['activity_type'].'"';
			$start_date = date_format(date_create($row['task_actual_start_date']),"d-m-Y");
			$start_date = '"'.$start_date.'"'; 
			$end_date = date_format(date_create($row['task_actual_end_date']),"d-m-Y");
			$end_date = '"'.$end_date.'"';
			$duration = $row['numTaskDuration'];
			$parent = $row['task_parent'];
			$progress = 0;
			$open = 'true';
			$users = '"'.$row['task_members'].'"';
			
	
		if( $progtype == "mcp" ){
			$progress = $row['txMachineryCP']/100;
		}elseif($progtype == "mtp"){
			$progress = $row['txMaterialCP']/100;
		}elseif($progtype == "wtp"){
			$progress = $row['txWorkCP']/100;
		}elseif($progtype == "mpp"){
			$progress = $row['txManPowerCP']/100;
		}else{
			$progress = 0;
		}

		
		$resDetailsWRK = resource_consumed( $task_work_table, $task_id, $resDetails, "typewt", "namewt", "catwt", "unitwt", "qntywt", "avqntywt" ); 
		$resDetailsMNP = resource_consumed( $task_manpower_table, $task_id, $resDetails, "typemp", "namemp", "catmp", "unitmp", "qntymp", "avqntymp"); 
		$resDetailsMAC = resource_consumed( $task_machinery_table, $task_id, $resDetails, "typemc", "namemc", "catmc", "unitmc", "qntymc", "avqntymc"); 
		$resDetailsMAT = resource_consumed( $task_material_table, $task_id, $resDetails, "typemt", "namemt", "catmt", "unitmt", "qntymt", "avqntymt"); 
		
			$taskList = array("{ ".'"id": '. $id,
				'"manpowerw": '. $manpowerw, 
				'"machineryw": '. $machineryw, 
				'"materialw": '. $materialw, 
				'"manpowerrused": '. $manpowerrused, 
				'"manpowerrcp": '. $manpowerrcp, 				
				'"worktyperused": '. $worktyperused, 
				'"worktypercp": '. $worktypercp, 
				'"machineryrused": '. $machineryrused, 
				'"machineryrcp": '. $machineryrcp, 
				'"materialrused": '. $materialrused, 
				'"materialrcp": '. $materialrcp, 
				'"overallCP": '. $overallCP,	
				'"categories": '. $categories,	
				'"manpowerr": ['.$resDetailsMNP.']', 
				'"worktyper":['.$resDetailsWRK.']',	
				'"machineryr":['.$resDetailsMAC.']', 
				'"materialr":['.$resDetailsMAT.']',		
				'"text": '.'"'.$text.'"', 
				'"type": '.$type ,
				'"start_date": '.$start_date, 
				'"end_date": '.$end_date, 
				'"duration": '.$duration, 
				'"parent": '.$parent, 
				'"progress": '.$progress, 
				'"open": '.'true', 
				'"users": '.$users,
				'"has_child": '.$has_child." }");
			$taskArray[$i] = $taskList;			
				$i++;
		}			
	}
	$taskdata = "";
	

	foreach($taskArray as $results) {
		foreach($results as $res) {
			$taskdata = $taskdata.$res.' , ';				
		}
	}			
 		
	$projdata = $projdata.$taskdata." ]";

	return $projdata;
}

//********************** Fetching task links **********************
	function task_links(){
		global $conn;
		global $project_id;
		
		$task_links_table = $GLOBALS["project_task_links"];
	
		$tasklink = "SELECT * FROM $task_links_table where project_id='$project_id'"; 
		$conn->query($tasklink) or die('Error, task links - query failed');
		$linkres = $conn->query($tasklink);

		$linkArray = array();
		$linkList = array();
			if ($linkres->num_rows > 0) {
				
			$i=0;
				while( $row = $linkres->fetch_array(MYSQLI_ASSOC)) 
				{
					$id = $row['link_no'];
					$source = $row['txSourceID'];			
					$target = $row['txTargetID'];
					$type = '"'.$row['txLinkType'].'"';
					$linkList = array("{ ".'"id": '.$id,  '"source": '.'"'.$source.'"', '"target": '.$target , '"type": '.$type." }");				
					$linkArray[$i] = $linkList;			
						$i++;
				}			
			}
		
		$data = '"links":[';
		foreach($linkArray as $results) {
			foreach($results as $res) {
			$data = $data.$res.' , ';
			}
		}
		$linkdata = $data." ]";	
		return $linkdata;
	}
//**********************


//********************** Building resource utilization details **********************

	function resource_consumed($tableName, $task_id, $resDetails, $typer, $namer, $catr, $unitr, $qntyr, $avqntyr){
		global $conn;
		global $project_id;
		global $project_resources;
		
		$resArray = array();
		$resList = "";
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
								
					$projQuery = "SELECT * FROM $project_resources WHERE project_id = '$project_id' AND resource_type = '$res_type' AND resource_name = '$res_name' AND resource_category = '$res_cat'";
					$conn->query($projQuery) or die('Error, work allocation - query failed');
					$result = $conn->query($projQuery);	
					$resRow = $result->fetch_array(MYSQLI_ASSOC);
					
					$res_avail = $resRow['resource_quantity'] - $resRow['resource_assigned_master'];
					
				$resList = "{ ". '"'.$typer.'": "'.$res_type.'"'.", ". '"'.$namer.'": "'.$res_name.'"'.
				", ".'"'.$catr.'": "'.$res_cat.'"'.",".'"'.$unitr.'": "'.$res_unit.'"'.", ".'"'.$qntyr.'": "'.$res_quantity.'"'.", ".'"'.$avqntyr.'": "'.$res_avail.'"'." }"; 
	
				$resArray[$i] = $resList;	
					$i++;					
			}						
		}
		if( $resList != ""){
			foreach($resArray as $resource){
			if( $resDetails == "" ){
				$resDetails = $resource;
			}else{
				$resDetails = $resDetails.", ".$resource;				
			}
		}
		return $resDetails;	
		}
	}
	
?>