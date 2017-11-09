<?php
/*This function is called in by below JS files in retrieving the resource details
	addWork
	addMachinery
	addMaterial
	addManPower
*/
include('../dbconnect.php');
include('../appconfig.php');

$project_id = $_REQUEST["projid"];

//$project_id = "EF39E0D3";
//$tskcat = "CDE02~CDE01";
// library/ajax/getResDetails.php?tskcat=CDE02&projid=EF39E0D3

$resDetails = "";

//Getting project resources from table
	$resourceSql = "SELECT * FROM $project_resources WHERE project_id='$project_id' ";
				
	$conn->query($resourceSql) or die('Error, query failed');
	$result = $conn->query($resourceSql);
	
	$resType = Array();
	$resName = Array();
	$resCat = Array();
	$resQnty = Array();
	$resUnit = Array();	

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $resType[] = $row['resource_type'];  
	$resName[] = $row['resource_name']; 
	$resCat[] = $row['resource_category']; 
	$resQnty[] = $row['resource_quantity']; 
	$resUnit[] = $row['resource_unit']; 
}

$resType = join("@^@", $resType);
$resName = join("@^@", $resName);
$resCat = join("@^@", $resCat);
$resQnty = join("@^@", $resQnty);
$resUnit = join("@^@", $resUnit);

echo $resType."@~@".$resName."@~@".$resCat."@~@".$resQnty."@~@".$resUnit;

?>