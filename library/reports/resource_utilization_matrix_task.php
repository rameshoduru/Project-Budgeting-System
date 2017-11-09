<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"resource_utilization_matrix_task.xlsx\"");
header("Cache-Control: max-age=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: Binary");

include('../dbconnect.php');
include('../appconfig.php');

/** Include PHPExcel */
require_once ('../PHPExcel/Classes/PHPExcel.php');

$project_id = $_REQUEST["pid"];

	//getting project start date and end date to create chart headers
	$query = "SELECT * FROM $resource_utilization WHERE project_id='$project_id'";
	$conn->query($query) or die('Error, query failed');
	$result = $conn->query($query);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	
	
$objPHPExcel = new PHPExcel();
	
	//Setting column headers									
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Task');		
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Category');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Resource Type');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Resource Name');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'Resource Quantity');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, 'Resource Unit');		
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 1, 'Amount Spent');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 1, 'Transaction Date');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, 1, 'User ID');
	//formating column header
	$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);

	//getting list of tasks from resource utilization table	
	$query = "SELECT task_name FROM $resource_utilization WHERE project_id='$project_id'";
	$conn->query($query) or die('Error, query failed-Line# 49');
	$result = $conn->query($query);	
	
	//Grouping under tasks
	$task_heads = Array();		
	while ($resrow = $result->fetch_array(MYSQLI_ASSOC))
	{
		$task_heads[] = $resrow['task_name'];
	}	
	$task_heads = array_unique($task_heads);
		
	$rw=2;
	
	foreach($task_heads as $task_name)
	{
		//Adding task name in the first column
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rw, $task_name);
				 
		//Sub-grouping under resource categories 
		$query = "SELECT resource_category FROM $resource_utilization WHERE project_id='$project_id'";
		$conn->query($query) or die('Error, query failed-Line# 64');
		$result = $conn->query($query);
		$resource_heads = Array();		
		
		while ($resrow = $result->fetch_array(MYSQLI_ASSOC)){
			$resource_heads[] = $resrow['resource_category'];
		}	
		$resource_heads = array_unique($resource_heads);
		
		foreach($resource_heads as $res_category)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rw, $res_category);
			
			//fetching other column values											
			$rw = resourcePlan( $project_id, $task_name, $res_category, $rw );			
		}			
			//adding blank row
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rw++, "");
	}

	//----- formatting work sheet
	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);

	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getFont()->setSize(9);

	foreach(range('A','I') as $columnID)
	{
		//re-sizing column widths according to values
		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
	}
	//----------
	
	function resourcePlan( $project_id, $task_name, $res_category, $rw )
	{
		global $conn;
		global $objPHPExcel;
		$utl_table = $GLOBALS["resource_utilization"];
		
		$query = "SELECT * FROM $utl_table WHERE project_id ='$project_id' AND task_name='$task_name' AND resource_category='$res_category'";
		$conn->query($query) or die('Error, resource query failed - line#102');
		$result = $conn->query($query);	
				
		while( $rsrow = $result->fetch_array(MYSQLI_ASSOC))
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rw, $rsrow['resource_type']);		
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rw, $rsrow['resource_name']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rw, $rsrow['resource_utilized']);
			$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 4, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
			$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 4, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rw, $rsrow['resource_unit']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rw, $rsrow['amount_spent']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rw, $rsrow['transaction_date']);		
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rw, $rsrow['user_id']);

		$rw++;				
		}
		return $rw;
	}

	
$objPHPExcel->getActiveSheet()->setTitle('Resource Utilization');

//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

// Write the Excel file to filename resource_plan_matrix_task.xlsx 
$objWriter->save('php://output');

?>
