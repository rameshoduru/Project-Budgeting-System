<?php


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"resource_rollup_report.xlsx\"");
header("Cache-Control: max-age=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: Binary");

include('../dbconnect.php');
include('../appconfig.php');
include('functions_reports.php');

/** Include PHPExcel */
require_once ('../PHPExcel/Classes/PHPExcel.php');
$objPHPExcel = new PHPExcel();
$objWorkSheet = $objPHPExcel->getActiveSheet()->setTitle("Resource Distribution");

//------- resource rolling-up starts here ------

	if(isset($_REQUEST['pid']))
	{		
		$project_id = 	$_REQUEST['pid'];	
		$project_no_result = mysqli_query($conn, "SELECT project_number FROM $project_master");
		$row = mysqli_fetch_array( $project_no_result );
		$project_number = $row['project_number'];
	}
	
	$tasks_primary_result = mysqli_query($conn, "SELECT * FROM $task_master WHERE task_parent='$project_number'");
		$task_parent = array();		
	while( $row = mysqli_fetch_array( $tasks_primary_result ))
	{
		$her = $row['task_hierarchy'].'%';
		$task_list_result = mysqli_query($conn, "SELECT task_parent FROM $task_master WHERE task_hierarchy LIKE '$her'");
		$task_count = 0;

		while( $row = mysqli_fetch_array( $task_list_result )) 
		{	
			$task_parent[] = $row['task_parent'];
		}		
	}

	
//Fetching resource parameters from respective tables and roll-up to thier immediate parent tasks. These records are stored in reports_resource table
//This function is written in functions_report.php
	process_reports( $task_parent , $task_work_consumed );
	process_reports( $task_parent , $task_machinery_consumed );
	process_reports( $task_parent , $task_manpower_consumed );
	process_reports( $task_parent , $task_material_consumed );

	$task_list_result = mysqli_query($conn, "SELECT task_parent FROM $reports_resource WHERE project_id='$project_id'");
	$task_count = 0;
	$task_parent = array();

//Roll-up of resource parameters within the reports_resource table. New parent tasks will be added 	
	while( $row = mysqli_fetch_array( $task_list_result )) 
	{	
		if($project_number != $row['task_parent']){
			$task_parent[] = $row['task_parent'];
		}						
	}
	process_reports( $task_parent , $reports_resource );
	
//roll-up ends here -----------------------------------------------


$res_type = "Resource Rollup Summary";
$objWorkSheet = $objPHPExcel->getActiveSheet()->setTitle($res_type);	

$rw=2;
	//Setting column headers									
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Task');		
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Category');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Resource Type');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Resource Name');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'Resource Unit');	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, 'Resource Assigned');	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 1, 'Resource Utilized');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 1, 'Amount Spent');
	//formating column header
	$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
	
	$query = "SELECT * FROM reports_resource ORDER BY task_hierarchy ASC";
	$conn->query($query) or die('Error, resource query failed');
	$result = $conn->query($query);
	
	while( $rsrow = $result->fetch_array(MYSQLI_ASSOC))
	{
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 0, $rw, $rsrow['task_name']);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 1, $rw, $rsrow['resource_category']);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 2, $rw, $rsrow['resource_type']);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 3, $rw, $rsrow['resource_name']);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 4, $rw, $rsrow['resource_unit']);
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 4, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 4, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 5, $rw, $rsrow['resource_assigned']);
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 5, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 5, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 6, $rw, $rsrow['resource_utilized']);
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 6, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 6, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow( 7, $rw, $rsrow['amount_spent']);
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 7, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
		$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 7, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$rw++;				
	}
    
	//----- formatting work sheet
	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);

	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getFont()->setSize(9);

	foreach(range('A','G') as $columnID)
	{
		//re-sizing column widths according to values
		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
	}
	
	
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

// Write the Excel file to filename resource_plan_matrix_task.xlsx 
$objWriter->save('php://output');

?>