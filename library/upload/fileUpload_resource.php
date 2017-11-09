<?php

include('../dbconnect.php');
include('../appconfig.php');
include('../globalClasses.php');

ini_set("display_errors",1);
date_default_timezone_set("Asia/Kolkata"); 
		
/** Include PHPExcel */
require_once ('../PHPExcel/Classes/PHPExcel.php');

$resFilePath = "";
$file_name = "";

	if(isset($_REQUEST['pid']) ){
		if ($_REQUEST['pid'] != ""){
			$project_id = $_REQUEST['pid'];		
		}
		$user_id = $_REQUEST['uid'];	
	//setting download link for resource template uploaded recently
			$resQuery = "SELECT * FROM $project_master where project_id='$project_id'";
			$conn->query($resQuery) or die('Error, query failed');
			$result = $conn->query($resQuery);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$resFilePath_recent = $row['resFilePath'];
			$file_name_recent = $row['resFileName'];

	//setting download link for resource template (blank)
			$query = "SELECT * FROM $project_templates";
			$conn->query($query) or die('Error, resource template query failed');
			$result = $conn->query($query);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$resFilePath = $row['location'];
			$file_name = $row['name'];			
	}	
	
	$resource_tables = Array( "Machinery"=>$task_machinery_consumed, "Manpower"=>$task_manpower_consumed, "Material"=>$task_material_consumed, "Work"=>$task_work_consumed );
	
	if( $_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST['upload']) ){

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
			$updates = 0;
			foreach ($objPHPExcel->getAllSheets() as $sheet) {
			   // $sheets[$sheet->getTitle()] = $sheet->toArray();
				$highestRow = $sheet->getHighestRow(); // e.g. 10
				$highestColumn = $sheet->getHighestColumn(); // e.g 'F'
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
			  
			   for ($row = 2; $row <= $highestRow; ++$row) {
						
					$resType = checkValue(mysqli_real_escape_string($conn, $sheet->getTitle()));
					$resCat = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 0 , $row)->getValue()));
					$resName = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 1 , $row)->getValue()));
					$resQnty = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 2 , $row)->getValue()));
					$resUnit = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 3 , $row)->getValue()));
					$resRate = checkValue(mysqli_real_escape_string($conn, $sheet->getCellByColumnAndRow( 4 , $row)->getValue()));
					
					if($resType!="" and $resName !="" and $resQnty !="" and $resUnit != "")
					{
						$proj_res_sql = "SELECT * FROM $project_resources WHERE project_id='$project_id' AND resource_type ='$resType' AND resource_category='$resCat' AND resource_name ='$resName'"; 
						$conn->query($proj_res_sql) or die('Error, query failed');
						$result = $conn->query($proj_res_sql);
						$projresrow = $result->fetch_array(MYSQLI_ASSOC);
						$resourceAmount = $resQnty * $resRate;
						
						if( $projresrow['project_id'] == $project_id AND $projresrow['resource_type'] == $resType AND $projresrow['resource_name'] == $resName AND $projresrow['resource_category'] == $resCat )
						{
							if( $projresrow['resource_quantity'] != $resQnty OR $projresrow['resource_unit'] != $resUnit OR $projresrow['resource_rate'] != $resRate )
							{
								$res_utilized = $projresrow['projresource_utilized'];
								if( $resQnty == 0 AND $res_utilized != 0 )
								{   
									$resAmount = $res_utilized * $resRate;
									
									$query = "UPDATE $project_resources SET resource_assigned_master='$res_utilized', resource_quantity = '$res_utilized', resource_unit = '$resUnit', resource_rate = '$resRate', resource_value = '$resAmount' WHERE project_id='$project_id' AND resource_type = '$resType' AND resource_name = '$resName' AND resource_category = '$resCat'";
									
									//updating resource consumed table
									$resQuery = "SELECT * FROM $resource_tables[$resType] WHERE resource_type='$resType' AND resource_category = '$resCat' AND resource_name='$resName'";
									$conn->query($resQuery) or die('Error, query failed-resource upload');
									$result = $conn->query($resQuery);
																		
									$task_list = array();
									while($row = mysql_fetch_array($result))
										$task_list[] = $row['task_id'];
									
									foreach($task_list as $task_id)
									{ 
										$resource_assigned_master = $resRow['resource_utilized'];
										$resQuery = "UPDATE $resource_tables[$resType] SET resource_assigned_master='$resource_assigned_master' WHERE task_id='$task_id' AND resource_name = '$resName' AND resource_category = '$resCat'";
										mysqli_query($conn,$resQuery);										
									}
																		
									$updates++;	
									//	$objPHPExcel->getActiveSheet()->removeRow($row);
										
								}
								elseif( $resQnty != 0 )
								{
									$query = "UPDATE project_resources SET resource_quantity = '$resQnty', resource_unit = '$resUnit', resource_rate = '$resRate' WHERE project_id='$project_id' AND resource_type = '$resType' AND resource_name = '$resName' AND resource_category = '$resCat'";

									/*
									$query = "DELETE FROM project_resources WHERE project_id='$project_id' AND resource_type = '$resType' AND resource_name = '$resName' AND resource_category = '$resCat'";
									$updates++;	
									*/
								}
							}
						}
						else
						{
							$query = "INSERT into project_resources(project_id, resource_type, resource_category, resource_name,resource_quantity,resource_unit, resource_rate, resource_value ) VALUES( '".$project_id."','".$resType."','".$resCat."','".$resName."','".$resQnty."','".$resUnit."', '".$resRate."','".$resourceAmount."')";
							$inserts++;
						}
					}				
					mysqli_query($conn,$query);
				} 
			}
			
			resUpload( $res_filepath, $project_id, $file_name, $file_tmp, $fileType );
					
		} catch (Exception $e) {
		 die($e->getMessage());
		}
		
/*/Log updates
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
		*/			
	}

	function checkValue($value){
		if(isset($value)){
			$value = $value;
		}else{$value = "";}
		return $value;
	}


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
		  
		$conn->query($query) or die('Error, query failed-resource upload');
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