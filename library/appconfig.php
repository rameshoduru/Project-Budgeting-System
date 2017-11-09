<?php
//This file is included in session.php

//======================= Table references =======
	$con_login = "con_login";
	$log_records = "log_records";
	$project_master = "project_master";
	$project_resources = "project_resources";
	$project_task_links = "project_task_links";
	$project_templates = "project_templates";
	$resource_link_data = "resource_link_data";
	$task_master = "task_master";
	$task_machinery_consumed = "task_machinery_consumed";
	$task_manpower_consumed = "task_manpower_consumed";
	$task_material_consumed = "task_material_consumed";
	$task_work_consumed = "task_work_consumed";
	$resource_utilization = "resource_utilization";
	$reports_resource = "reports_resource";
	
//>====Base paths for documents and assets====
	function url(){
	  return sprintf(
		"%s://%s%s",
		isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
		$_SERVER['SERVER_NAME'],"" );
	}
	$doc_baseurl = url();
	$doc_basepath = url()."/pbtsystem";
	$res_filepath = "./templates/projectFiles";
	$templates_filepath = "./templates";
//=================================================<

$resource_types=array("Work", "Manpower", "Machinery", "Material");

$resource_table_array = array("Work"=>$task_work_consumed, "Machinery"=>$task_machinery_consumed, "Manpower"=>$task_manpower_consumed, "Material"=>$task_material_consumed);

	if(isset($_REQUEST['pid']) )
	{
		$project_id = $_REQUEST['pid'];	
			$sql = "SELECT * FROM $project_master WHERE project_id='$project_id'";
			$conn->query($sql) or die('Error, project query failed');
			$row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
		$project_number = $row['project_number'];	
	}
			

//List of projects
	function list_of_projects()
	{
		global $conn;
		global $proj_list;
		global $project_master;
		
		$proj_list = array();
		//List of projects based on user access
		if(isset($GLOBALS["login_session"]) ){
			$login_session = $GLOBALS["login_session"];
			
			$sql = "SELECT * FROM $project_master WHERE proj_manager='$login_session'";
			$conn->query($sql) or die('Error, project query failed');
			$result = $conn->query($sql);	
			if ($result->num_rows > 0)
			{				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) 
				{									
					$proj_list[$row['project_id']] = $row['project_name'];
				}			
			}		
		}
		return $proj_list;
	}
//------------------ LOP ends here ---------


?>
