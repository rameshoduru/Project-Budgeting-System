<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"resource_allocation_report.xlsx\"");
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

	if(isset($_REQUEST['pid']))
	{		
		$project_id = 	$_REQUEST['pid'];	
	}

$objPHPExcel = new PHPExcel();
		
	$rw=2;

	foreach($resource_types as $res_type)
	{
		if($res_type == "Work"){
			$objWorkSheet = $objPHPExcel->getActiveSheet()->setTitle($res_type);		
		}
		else
		{
			$objWorkSheet = $objPHPExcel->createSheet()->setTitle($res_type);			
		}
				
		resourcePlan( $project_id, $res_type, $rw );					
	}			

	
	function resourcePlan( $project_id, $res_type, $rw )
	{
		global $conn;
		global $objPHPExcel;
		$proj_resource_table = $GLOBALS["project_resources"];
		
		//Setting column headers 	
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(0, 1, 'Category');		
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(1, 1, 'Name');
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(2, 1, 'Quantity');
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(3, 1, 'Unit');		
		$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(4, 1, 'Rate');
		
		//formating column header
		$objPHPExcel->getSheetByName($res_type)->getStyle("A1:I1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getSheetByName($res_type)->getStyle("A1:I1")->getFont()->setBold(true);		
		
		$query = "SELECT * FROM $proj_resource_table WHERE project_id='$project_id' AND resource_type='$res_type'";
		$conn->query($query) or die('Error, resource query failed');
		$result = $conn->query($query);
		
		while( $rsrow = $result->fetch_array(MYSQLI_ASSOC))
		{   
			$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(0, $rw, $rsrow['resource_category']);		
			$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(1, $rw, $rsrow['resource_name']);
			$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(2, $rw, $rsrow['resource_quantity']);
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 2, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 2, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(3, $rw, $rsrow['resource_unit']);
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 3, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 3, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getSheetByName($res_type)->setCellValueByColumnAndRow(4, $rw, $rsrow['resource_rate']);
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 4, $rw )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
			$objPHPExcel->getSheetByName($res_type)->getCellByColumnAndRow( 4, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$rw++;				
		}
			//----- formatting work sheet
			$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);

			$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getFont()->setSize(9);

			foreach(range('A','E') as $columnID)
			{
				//re-sizing column widths according to values
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
			//----------
	}


//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

// Write the Excel file to filename resource_allocation_download.xlsx 
$objWriter->save('php://output');

?>
