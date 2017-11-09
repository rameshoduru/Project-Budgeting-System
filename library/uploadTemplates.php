<?php

require_once './dbconnect.php';
require_once './session.php';

ini_set("display_errors", 1);

$resFilePath = "";
$reference = "project_resources";

if( $_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST['upload']) ){


	if( $_FILES['excelImport']['size'] > 0) {
		$file_name = $_FILES['excelImport']['name'];
		$file_tmp  = $_FILES['excelImport']['tmp_name'];
		$fileSize = $_FILES['excelImport']['size'];
		$fileType = $_FILES['excelImport']['type'];
	}
	
//setting download link for resource template
		$resQuery = "SELECT * FROM project_templates WHERE reference = '$reference'";
		$conn->query($resQuery) or die('Error, query failed');
		$result = $conn->query($resQuery);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$location = $row['location'];	
		
		$filePath  = $templates_filepath; //assigned in sessions.php
		
		resourceUpload( $reference, $filePath, $file_name, $file_tmp, $fileType, $fileSize, $location );
		
		header('Location:'.$_SERVER['PHP_SELF']);
			
}


//Uploading PIA form
	function resourceUpload( $reference, $filePath, $file_name, $file_tmp, $fileType, $fileSize, $location ){
			global $conn;

		if (!file_exists( $filePath )) {
			mkdir( $filePath , 0777, true);
		}

		$file_name = preg_replace("/[^-\w]+/", ".", $file_name);
		$filePath = $filePath."/".$file_name;

		if(!get_magic_quotes_gpc())
		{
			$file_name = addslashes($file_name);
		}
		if (isset($_POST['upload'])){
			if($location != "" ){//check for existing template
				$query = "UPDATE project_templates SET type='$fileType', size ='$fileSize', name = '$file_name', location='$filePath' WHERE reference='$reference'";
			}else{
				$query = "INSERT INTO project_templates(reference, type, size, name, location) VALUES('$reference', '$fileType', '$fileSize', '$file_name', '$filePath')";
			}
		move_uploaded_file( $file_tmp, $filePath );
			  
			$conn->query($query) or die('Error, query failed-resource upload');			
		}

		unset($_POST);
	}

			
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>File Upload</title>
	<?php
	include ("./library/uilinks.php");
	?>
</head>

<body>
<form method="post" class="form-horizontal" enctype="multipart/form-data" >

<div class="container">

	<div class="panel panel-primary" style="margin-bottom: 1.5px; border:0px">
		<div class="panel-body" style="font-weight: bold; font-family: verdana,helvetica;color: #03B0D7">
		Admin Console (Master)
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
				Upload project resource template
				</div>
				<div class="col-sm-9">
					<input name="excelImport" type="file" id="excelImport" > 		
				</div>
			</div>
			
		
		</div>
	</div>
</div>

</form>
<?php include("./library/closedb.php") ?>

</body>
</html>