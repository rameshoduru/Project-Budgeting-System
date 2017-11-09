<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"resource_plan_matrix_category.xlsx\"");
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
	$query = "SELECT * FROM $project_master WHERE project_id='$project_id'";
	$conn->query($query) or die('Error, project query failed line#21');
	$result = $conn->query($query);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	
$objPHPExcel = new PHPExcel();
	
	$start_date = strtotime($row['proj_actual_start_date']);
	$end_date = strtotime($row['proj_actual_end_date']);
	$proj_date_range = array();
	$rw = 2;
	$col = 2;
	
	//Creating first row with project duration split by dates setRotation()setWrapText
	for($i = $start_date; $i<$end_date; $i+=86400){	
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rw, date('d-M-Y', $i));
		$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw )->getStyle()->getAlignment()->setTextRotation(90);
		$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getRowDimension($rw)->setRowHeight(-1);
		$proj_date_range[] = date('Y-m-d', $i);
		$col++;
	}
	
	//getting list of resource categories in a project
	$query = "SELECT resource_category FROM $project_resources WHERE project_id='$project_id'";
	$conn->query($query) or die('Error, project resource query failed - line#45');
	$result = $conn->query($query);
		$resource_heads = Array();
		
	while ($resrow = $result->fetch_array(MYSQLI_ASSOC)){
		$resource_heads[] = $resrow['resource_category'];
	}
	$resource_heads = array_unique($resource_heads);
	
	$rw=3;
	
	foreach($resource_heads as $res_category){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rw, $res_category);
			$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 0, $rw )->getStyle()->getAlignment()->setShrinkToFit(true);
			
		$rw = resourcePlan( "Work", $project_id, $res_category, $proj_date_range, $rw );
		$rw = resourcePlan( "Material", $project_id, $res_category, $proj_date_range, $rw );
		$rw = resourcePlan( "Manpower", $project_id, $res_category, $proj_date_range, $rw );
		$rw = resourcePlan( "Machinery", $project_id, $res_category, $proj_date_range, $rw );
		
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rw++, "");
	}

	//----- formatting work sheet
	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);

	$objPHPExcel->getActiveSheet()->getStyle( 'A0:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $objPHPExcel->getActiveSheet()->getHighestRow() )->getFont()->setSize(9);

	foreach(range('A','B') as $columnID)
	{
		//re-sizing column widths according to values
		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
	}
	//----------
	
	function resourcePlan( $res_type, $project_id, $res_category, $proj_date_range, $rw ){
		global $conn;
		global $objPHPExcel;
		
		$resource_tables = $GLOBALS["resource_table_array"]; 
			
		$query = "SELECT * FROM $resource_tables[$res_type] WHERE project_id ='$project_id' AND resource_category='$res_category'";
		$conn->query($query) or die('Error, resource query failed');
		$result = $conn->query($query);	
		
		$row_count = mysqli_num_rows($result);		
		if($row_count != 0){
			$rw_res_type = $rw; //This variable is used to assign cumulative values of each resource assigned
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rw_res_type, $res_type);
			$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 1, $rw )->getStyle()->getFont()->setBold(true);
			$rw++;			
		}
		
		while( $rsrow = $result->fetch_array(MYSQLI_ASSOC)){
				
			$start_date = strtotime($rsrow['res_plan_start_date']);
			$end_date = strtotime($rsrow['res_plan_end_date']);
			$task_duration = date_diff( date_create($rsrow['res_plan_start_date']), date_create($rsrow['res_plan_end_date']));

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rw, $rsrow['resource_name']);
			
			
			for($i = $start_date; $i<$end_date; $i+=86400){	
				$array_index = array_search( date('Y-m-d', $i), $proj_date_range);
				if(isset($array_index)){	
					$col = $array_index + 2;
					$res_distribution = $rsrow['resource_assigned']/$task_duration->format("%a");	
					$res_distribution = number_format($res_distribution, 0);					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rw, $res_distribution);
					
					//Cell formatting
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw )->getStyle()->getAlignment()->setShrinkToFit(true);
					
					//Calculating cummulative value of resources assigned
					$cumulative_val = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw_res_type )->getValue();
					if(isset($cumulative_val)){
						$cumulative_val = $cumulative_val + $res_distribution;
					}
					else
					{
						$cumulative_val = $res_distribution;
					}					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rw_res_type, $cumulative_val);
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw_res_type )->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCFC9B');
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw_res_type )->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw_res_type )->getStyle()->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $rw_res_type )->getStyle()->getAlignment()->setShrinkToFit(true);
				}								
			}
		$rw++;				
		}
		return $rw;
	}

	
$objPHPExcel->getActiveSheet()->setTitle('Resource Plan');

//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

// Write the Excel file to filename resource_plan_matrix_category.xlsx 
$objWriter->save('php://output');

?>
