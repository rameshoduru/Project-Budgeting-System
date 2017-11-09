<?php

include('../dbconnect.php');
include('../appconfig.php');
include('../globalClasses.php');
include('../global_functions.php');

ini_set("display_errors",1);
date_default_timezone_set("Asia/Kolkata"); 

/** Include PHPExcel */
require_once ('../PHPExcel/Classes/PHPExcel.php');

$user_id = "";
$resFilePath = "";
$file_name = "";
$task_count = 1;

	if(isset($_REQUEST['pid']) )
	{
		$user_id = $_REQUEST['uid'];
	}
	if(isset($_REQUEST['pid']) ){	
				
		$project_id = 	$_REQUEST['pid'];	
	//setting download link for resource template uploaded recently
			$resQuery = "SELECT * FROM $project_master where project_id='$project_id'";
			$conn->query($resQuery) or die('Error, query failed');
			$result = $conn->query($resQuery);
			$proj_row = $result->fetch_array(MYSQLI_ASSOC);
			$resFilePath_recent = $proj_row['resFilePath'];
			$file_name_recent = $proj_row['resFileName'];

	//setting download link for resource template (blank)
			$query = "SELECT * FROM $project_templates";
			$conn->query($query) or die('Error, resource template query failed');
			$result = $conn->query($query);
			$sqlrow = $result->fetch_array(MYSQLI_ASSOC);
			$resFilePath = $sqlrow['location'];
			$file_name = $sqlrow['name'];			
	}	
	
	if( $_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST['upload']) AND (!isset($proj_row)))
	{

		if( $_FILES['excelImport']['size'] > 0) {
			$file_name = $_FILES['excelImport']['name'];
			$file_tmp  = $_FILES['excelImport']['tmp_name'];
			$fileSize = $_FILES['excelImport']['size'];
			$fileType = $_FILES['excelImport']['type'];
		}
		
		
		try {
			$fileType = PHPExcel_IOFactory::identify($file_tmp);
			$objReader = PHPExcel_IOFactory::createReader($fileType);
			$objPHPExcel = $objReader->load($file_tmp);
			$inserts = 0;
			
			foreach ($objPHPExcel->getAllSheets() as $sheet) {
			   // $sheets[$sheet->getTitle()] = $sheet->toArray();
				$highestRow = $sheet->getHighestRow(); // e.g. 10
				$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
				
				//creating project 
				$project_name = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 0 , 2)->getValue()));
				create_project( $project_id, $project_name, $user_id );
				
			   for ($excel_row = 2; $excel_row <= $highestRow; ++$excel_row) {
						
					$resType = checkValue(mysqli_real_escape_string($conn, $sheet->getTitle()));
					$task_name = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 1 , $excel_row)->getValue()));
					$task_start_date = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 2 , $excel_row)->getValue()));				
					$task_start_date = PHPExcel_Style_NumberFormat::toFormattedString($task_start_date, 'YYYY-MM-DD');
					
					$task_end_date = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 3 , $excel_row)->getValue()));
					$task_end_date = PHPExcel_Style_NumberFormat::toFormattedString($task_end_date, 'YYYY-MM-DD');
					
					$resCat = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 4 , $excel_row)->getValue()));
					$resName = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 5 , $excel_row)->getValue()));
					$resQnty = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 6 , $excel_row)->getValue()));
					$resUnit = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 7 , $excel_row)->getValue()));
					$resRate = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 8 , $excel_row)->getValue()));
					
				//create task if not exist
					//Fetching project_number to assign parent task number
					$resQuery = "SELECT * FROM $project_master where project_id='$project_id'";
					$proj_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC);
					$project_number = $proj_row['project_number'];
					$task_parent = $proj_row['project_number'];
					
					//creating task id from random number
					$num1 = time(); 
					$num2 = (rand(10,100));
					//$num3 = strtoupper( substr( md5(uniqid(rand(), true)), 0, 5 ));
					$randnum = $num1.$num2.$excel_row;
					
					$task_count = create_task( $task_count, $task_parent, $project_id, $task_name, $user_id, $task_start_date, $task_end_date, $resType, $resCat, $resName, $resUnit, $resQnty, $randnum );	
				//----
					
					if($resType!="" and $resName !="" and $resQnty !="" and $resUnit != "")
					{
						$proj_res_sql = "SELECT * FROM $project_resources WHERE project_id='$project_id' AND resource_type ='$resType' AND resource_category='$resCat' AND resource_name ='$resName'"; 
						$conn->query($proj_res_sql) or die('Error, project resource query failed # line 84');
						$result = $conn->query($proj_res_sql);
						$projresrow = $result->fetch_array(MYSQLI_ASSOC);
						
						$resourceAmount = $resQnty * $resRate;
						
						if(!isset($projresrow))
						{							
							$query = "INSERT into $project_resources(project_id, resource_type, resource_category, resource_name, resource_quantity,resource_unit, resource_assigned_master, resource_rate, resource_value) VALUES( '$project_id','$resType','$resCat','$resName','$resQnty','$resUnit', '$resQnty', '$resRate','$resourceAmount')";							
							mysqli_query($conn,$query);	
							$inserts++;										
						}
						else
						{
							$new_quantity = $projresrow['resource_quantity']+ $resQnty;
							$new_amount = $projresrow['resource_value']+ $resourceAmount;
							
							$query = "UPDATE project_resources SET resource_quantity = '$new_quantity', resource_value = '$new_amount', resource_assigned_master='$new_quantity' WHERE project_id='$project_id' AND resource_type = '$resType' AND resource_name = '$resName' AND resource_category = '$resCat'";	
							
							mysqli_query($conn,$query);	
						}
																		
					}				
				} 
			}
			
			resUpload( $res_filepath, $project_id, $file_name, $file_tmp, $fileType );
					
		} catch (Exception $e) {
		 die($logmsg = $e->getMessage());
		}
				
		//Log updates
		/*
				$task_id = "";
				if( $updates == 1 ){
					$logmsg = $user_id.": Project resource details ".$updates." - record updated";
				}else{
					$logmsg = $user_id.": Project resource details ".$updates." - records updated";
				}
				log_activities( $user_id, $project_id, $task_id, $logmsg );	
				
				if( $inserts == 1){
					$logmsg = $user_id.": Project resource details ".$inserts.": record added";
				}else{
					$logmsg = $user_id.": Project resource details ".$inserts.": records added";
				}		
				log_activities( $user_id, $project_id, $task_id, $logmsg );

		task_members
				
		*/
	}
	
//Project creation starts 
	function create_project( $project_id, $project_name, $user_id )
	{
		global $conn;
		global $project_master;
		
		//creating projno. from random
		$num1 = date("Ymd");
		$num2 = time();
		$randnum = $num1.$num2;
				
		$projQuery = "SELECT * FROM $project_master where project_name='$project_name'";
		$conn->query($projQuery) or die('Error, project creation query failed');
		$proj_row = $conn->query($projQuery)->fetch_array(MYSQLI_ASSOC);
		$prsdt = date('Y-m-d');
		$predt = date('Y-m-d', strtotime($prsdt. ' + 1 days'));
		$proj_duration = date_diff(date_create($prsdt), date_create($predt))->format("%a");
		
		if (!isset($proj_row)){
			$query = "INSERT into $project_master(project_id, project_number, project_name, activity_type, proj_base_line_number, proj_plan_start_date, proj_plan_end_date, proj_actual_start_date, proj_actual_end_date, proj_duration, proj_manager, proj_users, proj_admin ) VALUES( '$project_id','$randnum','$project_name','project','1','".$prsdt."','".$predt."', '".$prsdt."','".$predt."', '$proj_duration','$user_id','$user_id','ADM' )";
			
			$conn->query($query) or die('Error, project creation query failed - line#165');
		}
	}
//--- Project creation ends here
	
	
//--- Create task strats here
	function create_task( $task_count, $task_parent, $project_id, $task_name, $user_id, $task_start_date, $task_end_date, $resType, $resCat, $resName, $resUnit, $resQnty, $randnum )
	{
		global $conn;
		global $task_master;
				 
		if(!isset($task_start_date))
		{
			$task_start_date = date('Y-m-d');
			$task_end_date = date('Y-m-d', strtotime($task_start_date. ' + 1 days'));
			$task_duration = date_diff(date_create($task_start_date), date_create($task_end_date))->format("%a");		
		}
		else
		{
			$task_duration = date_diff(date_create($task_start_date), date_create($task_end_date))->format("%a");	
		}

		$task_query = "SELECT * FROM $task_master where project_id='$project_id' AND task_name='$task_name'";
		$task_row = $conn->query($task_query)->fetch_array(MYSQLI_ASSOC);
		
		if (!isset($task_row))
		{
			$query = "INSERT into $task_master( task_id, project_id, task_name, activity_type, task_parent, task_hierarchy, proj_base_line_number, task_plan_start_date, task_plan_end_date, task_actual_start_date, task_actual_end_date, numTaskDuration, proj_manager, task_members, task_admin ) VALUES( '$randnum', '$project_id','$task_name','task','$task_parent', '$task_count', '1','$task_start_date','$task_end_date','$task_start_date','$task_end_date','$task_duration','$user_id','$user_id','ADM' )";
			mysqli_query($conn, $query);
			
			$task_count++; //this is treated as task_hierarchy for main tasks
		}						
	
	//task table look-up for task id
		$resQuery = "SELECT * FROM $task_master where project_id='$project_id' AND task_name='$task_name'";
		$task_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC); 
		$task_id = $task_row['task_id'];
		
	//Creating records in resource consumtion tables	
		if( $resType == "Work")
		{
			//duplication check in resource consumtion table
			$resQuery = "SELECT * FROM task_work_consumed where project_id='$project_id' AND task_id='$task_id' AND resource_type='$resType' AND resource_category='$resCat' AND resource_name='$resName'";
			$resource_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC); 
			
			if(!isset($resource_row))
			{
				$query = "INSERT into task_work_consumed(project_id, task_id, task_name, txTaskParent, resource_type, resource_category, resource_name, resource_unit, resource_assigned) VALUES( '$project_id','$task_id','$task_name','$task_parent','$resType','$resCat', '$resName', '$resUnit', '$resQnty')";				
				mysqli_query($conn, $query);				
			}							
		}
		
		if( $resType == "Manpower")
		{
			//duplication check in resource consumtion table
			$resQuery = "SELECT * FROM task_manpower_consumed where project_id='$project_id' AND task_id='$task_id' AND resource_type='$resType' AND resource_category='$resCat' AND resource_name='$resName'";
			$resource_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC); 
			
			if(!isset($resource_row))
			{
				$query = "INSERT into task_manpower_consumed(project_id, task_id, task_name, txTaskParent, resource_type, resource_category, resource_name, resource_unit, resource_assigned) VALUES( '$project_id','$task_id','$task_name','$task_parent','$resType','$resCat', '$resName', '$resUnit', '$resQnty')";				
				mysqli_query($conn, $query);
			}			
		}						
		if( $resType == "Machinery")
		{
			//duplication check in resource consumtion table
			$resQuery = "SELECT * FROM task_machinery_consumed where project_id='$project_id' AND task_id='$task_id' AND resource_type='$resType' AND resource_category='$resCat' AND resource_name='$resName'";
			$resource_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC); 
			
			if(!isset($resource_row))
			{
				$query = "INSERT into task_machinery_consumed(project_id, task_id, task_name, txTaskParent, resource_type, resource_category, resource_name, resource_unit, resource_assigned) VALUES( '$project_id','$task_id','$task_name','$task_parent','$resType','$resCat', '$resName', '$resUnit', '$resQnty')";				
				mysqli_query($conn, $query);
			}			
		}						
		if( $resType == "Material")
		{
			//duplication check in resource consumtion table
			$resQuery = "SELECT * FROM task_material_consumed where project_id='$project_id' AND task_id='$task_id' AND resource_type='$resType' AND resource_category='$resCat' AND resource_name='$resName'";
			$resource_row = $conn->query($resQuery)->fetch_array(MYSQLI_ASSOC); 
			
			if(!isset($resource_row))
			{
				$query = "INSERT into task_material_consumed(project_id, task_id, task_name, txTaskParent, resource_type, resource_category, resource_name, resource_unit, resource_assigned) VALUES( '$project_id','$task_id','$task_name','$task_parent','$resType','$resCat', '$resName', '$resUnit', '$resQnty')";				
				mysqli_query($conn, $query);
			}			
		}
			return $task_count;
	}
	

//---- Create task ends here	
	function checkValue($value){
		if(isset($value)){
			$value = $value;
		}else{$value = "";}
		return $value;
	}

//>------------------ Setting heirarchy levels and rolling up of start and end dates to primary/parent tasks--------------

	set_heirarchy( $project_id, $project_number, $task_master ); //this functions is defined in global_functions.php
	rollup_dates( $project_id, $project_number, $task_master ); //this functions is defined in global_functions.php
	
//<------------------------ Ends here ------------------------------------------------------
	
	

//Uploading Excel form
	function resUpload( $res_filepath, $project_id, $file_name, $file_tmp, $fileType ){
		global $conn;
		$filePath  = $res_filepath."/".$project_id;

		if (!file_exists( $filePath )) {
		//	mkdir( $filePath , 0777, true);
		}

		$file_name = preg_replace("/[^-\w]+/", ".", $file_name);
		$filePath = $filePath."/".$file_name;

		if(!get_magic_quotes_gpc())
		{
			$file_name = addslashes($file_name);
		}

		$query = "UPDATE project_master SET resFileName='$file_name', resFilePath='$filePath' WHERE project_id='$project_id'";
		//	move_uploaded_file( $file_tmp, $filePath );
		  
		$conn->query($query) or die('Error, query failed-resource upload-line# 240');
		unset($_POST);
	}
	
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>File Upload</title>
	<?php
	include ("../uilinks.php");
	?>
</head>

<body>
<form method="post" class="form-horizontal" enctype="multipart/form-data" >

<div class="container">

	<div class="panel panel-primary" style="margin-bottom: 1.5px; border:0px">
		<div class="panel-body" style="font-weight: bold; font-family: verdana,helvetica;color: #03B0D7">
		Admin Console
		</div>
	</div>
	
	<div class="panel panel-primary" style="margin-bottom: 1.5px; font-size: 12px;">
		<div class="panel-body">
		
			<div class="form-group">	
				<div class="col-sm-11">
				</div>
				<div class="col-sm-1">
					<button type='submit' class='btn btn-default btn-xs btn-action' name='upload'>
						<span class='glyphicon glyphicon-saved'></span>&nbsp;Submit</button>
				</div>
			</div>			
			<div class="form-group">
				<div class="col-sm-3" style="font-weight: bold; font-family: verdana,helvetica;">
				Upload project resource details
				</div>
				<div class="col-sm-9">
					<input name="excelImport" type="file" id="excelImport" > 		
				</div>
			</div>
					
			<div class="form-group">
				<div class="col-sm-6" id="piidiv"> 
					<label class="control-label fontBold" id="piadiv">Template (blank): </label>
					<label class="control-label fontLight" ><a href= <?php echo $resFilePath; ?> target='_blank'> <?php echo $file_name; ?> </a></label><br>
				</div>
			</div> 
			
		</div>
	</div>
</div>

</form>
<?php include("../closedb.php") ?>

</body>
</html>