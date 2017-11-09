<?php
include('../session.php');

	$manpower = "";
	$machinery = "";
	$material = "";
	$work = "";
	
	$resType = "";
	$resName = "";
	$resUnit = "";
	$resQnty = "";
	$resUsed = "";
	$resCP = "";
	
	$resourceMNP = "";
	$resourceMAC = "";
	$resourceMAT = "";
	$resourceWRK = "";
	$task_name = "";
	
	if( isset($_REQUEST['pid']) and isset($_REQUEST['txTaskID']) ){
		
			if (isset($_REQUEST['pid'])){
				$project_id = $_REQUEST['pid'];
			}
			
			if (isset($_REQUEST['txTaskID'])){
				$task_id = $_REQUEST['txTaskID'];
			}
			if (isset($_REQUEST['taskName'])){
				$task_name = $_REQUEST['taskName'];
			}			
		//Fetching resource details from task_master table
		$resourceWRK =  resDetails("task_work_consumed", $project_id, $task_id);	
		$resourceMNP =  resDetails("task_manpower_consumed", $project_id, $task_id);
		$resourceMAT =  resDetails("task_material_consumed", $project_id, $task_id);
		$resourceMAC =  resDetails("task_machinery_consumed", $project_id, $task_id);
	}	
	
	
	function resDetails($tbl, $project_id, $task_id){

		global $conn;

		$taskSql = "SELECT * FROM $tbl where project_id='$project_id' AND task_id='$task_id'"; 
		$conn->query($taskSql) or die('Error, query failed');
		$result = $conn->query($taskSql);
		
		if ($result->num_rows > 0) {
			
			$i=0;

			while($row_res = $result->fetch_array(MYSQLI_ASSOC)) 
			{
				if($i == 0){
					$resource_category = $row_res['resource_category'];
					$resType = $row_res['resource_type'];
					$resName = $row_res['resource_name'];
					$resUnit = $row_res['resource_unit'];
					$resQnty = $row_res['resource_assigned'];
					$resUsed = $row_res['resource_utilized'];
					$resCP = $row_res['resourceCP'];
				}else{
					$resource_category = $resource_category ."~$~". $row_res['resource_category'];
					$resType = $resType ."~$~". $row_res['resource_type'];
					$resName = $resName ."~$~". $row_res['resource_name'];
					$resUnit = $resUnit ."~$~". $row_res['resource_unit'];
					$resQnty = $resQnty ."~$~". $row_res['resource_assigned'];
					$resUsed = $resUsed ."~$~". $row_res['resource_utilized'];
					$resCP =  $resCP ."~$~". $row_res['resourceCP'];				
				}
				$i++;
			}
		return $resType ."@^@". $resName ."@^@". $resUnit ."@^@". $resQnty ."@^@". $resUsed ."@^@". $resCP ."@^@".$resource_category;				
		}
	}	


//Fetching list of tasks from task_master table
//If a task has sibling, only siblings will be allowed for actuals entry

	if(isset($_REQUEST['pid']) ){
		if ($_REQUEST['pid'] != ""){
			
			$project_id = $_REQUEST['pid'];
		$projSql = "SELECT * FROM task_master where project_id='$project_id'"; 
		$conn->query($projSql) or die('Error, task query failed');
		$tasks_list = $conn->query($projSql);
		$tasksList = "";
		
		$parentTasks = Array();		
		$ptasks_list = $conn->query($projSql);
		while (	$trow = $ptasks_list->fetch_array(MYSQLI_ASSOC) ) {
			$parentTasks[] = $trow['task_parent'];
		}
		
		while (	$row = $tasks_list->fetch_array(MYSQLI_ASSOC) ) {
			if( array_search( $row['task_id'], $parentTasks ) == NULL ){
			$tasksList = $tasksList."<li><a href='#' onClick=\"fnSelectTask('".$row['task_id']."' , '".$row['project_id']."', '".$row['task_name']."')\">".$row['task_name']."</a></li>";				
			}
		}
		}
	}

?>
<html>
<head>

<title>Progress Update</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">


	<?php 	include ("../uilinks.php"); ?>
	

<script language="JavaScript" type="text/javascript">



	function loadResources(){
	
		var docCur = document.forms[0];
		
		document.getElementById("projUpdateMP").innerHTML = "";
		document.getElementById("projUpdateMC").innerHTML = "";
		document.getElementById("projUpdateWT").innerHTML = "";
		document.getElementById("projUpdateMT").innerHTML = "";
			
		var WorkData = '<?php echo $resourceWRK; ?>'.split("@^@");
		var ManPowerData = '<?php echo $resourceMNP; ?>'.split("@^@");
		var MaterialData = '<?php echo $resourceMAT; ?>'.split("@^@");
		var MachineryData = '<?php echo $resourceMAC; ?>'.split("@^@");		


		var indType = WorkData[0].split("~$~");
		var indName = WorkData[1].split("~$~");
		var indUnit = WorkData[2].split("~$~");
		var indPlan = WorkData[3].split("~$~");
		var indActual = WorkData[4].split("~$~");
		var indComp = WorkData[5].split("~$~");
		var indCat = WorkData[6].split("~$~");
		
		var indComp1 = 0; 
		
		if (indComp == "" ){
			indComp = 0;
		}else{
			for(i=0 ; i<indComp.length ; i++){	
				indComp1 = indComp1 + parseFloat(indComp[i])	
			}
		}
					
		if( indType[0] != "")
		{
			var projUpdateMPData = "<table  class='pgUpdTable'><tr><th colspan = 3 align='left'>Work</th><th colspan = 3>" + indComp1 + "% Complete</th></tr>"
			projUpdateMPData = projUpdateMPData + "<tr style='font-weight: bold;'><td width='20%'>Category</td><td width='20%'>Name</td><td width='15%'>Unit</td><td width='15%'>Plan</td><td width='15%'>Actual</td><td width='15%'>Current</td></tr>";
	
			for(i=0 ; i<indType.length ; i++)
			{
				if (indActual[i] == undefined || indActual[i] == "")
					var actVal = 0;
				else
					var actVal = indActual[i]	
			
				if (i != 0)
				projUpdateMPData = projUpdateMPData + "<tr><td colspan = 6> <div style='height:1px;background-color:#69A94E;'></div></td></tr>"
				
				projUpdateMPData = projUpdateMPData + "<tr><td>" + indCat[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + indName[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + indUnit[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + indPlan[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + actVal + "</td>"
				projUpdateMPData = projUpdateMPData + "<td><input type='number' size='10' value='0'></td></tr>"
			}
			projUpdateMPData = projUpdateMPData + "</table>"
			document.getElementById("projUpdateWT").innerHTML = projUpdateMPData
		}
		else
		{
			if(document.getElementById("projUpdateWT").style.display == "none"){ 
				document.getElementById("projUpdateWTTB").style.display = "none" 
			}
		}		

		
		var indType = MachineryData[0].split("~$~");
		var indName = MachineryData[1].split("~$~");
		var indUnit = MachineryData[2].split("~$~");
		var indPlan = MachineryData[3].split("~$~");
		var indActual = MachineryData[4].split("~$~");
		var indComp = MachineryData[5].split("~$~");
		var indCat = WorkData[6].split("~$~");
		
		var indComp1 = 0; 
		
		if (indComp == "" ){
			indComp = 0;
		}else{
			for(i=0 ; i<indComp.length ; i++)
			{	indComp1 = indComp1 + parseFloat(indComp[i])		}
		}
					
		if( indType[0] != "")
		{
			var projUpdateMPData = "<table  class='pgUpdTable'><tr><th colspan = 3 align='left'>Machinery</th><th colspan = 3>" + indComp1 + "% Complete</th></tr>"
			projUpdateMPData = projUpdateMPData + "<tr style='font-weight: bold;'><td width='20%'>Category</td><td width='20%'>Name</td><td width='15%'>Unit</td><td width='15%'>Plan</td><td width='15%'>Actual</td><td width='15%'>Current</td></tr>";
			
			for(i=0 ; i<indType.length ; i++)
			{
				if (indActual[i] == undefined || indActual[i] == "")
					var actVal = "0"
				else
					var actVal = indActual[i]	
			
				if (i != 0)
					projUpdateMPData = projUpdateMPData + "<tr><td colspan = 6><div style='height:1px;background-color:#69A94E;'></div></td></tr>"
				
					projUpdateMPData = projUpdateMPData + "<tr><td>" + indCat[i] + "</td>"
					projUpdateMPData = projUpdateMPData + "<td>" + indName[i] + "</td>"
					projUpdateMPData = projUpdateMPData + "<td>" + indUnit[i] + "</td>"
					projUpdateMPData = projUpdateMPData + "<td>" + indPlan[i] + "</td>"
					projUpdateMPData = projUpdateMPData + "<td>" + actVal + "</td>"
					projUpdateMPData = projUpdateMPData + "<td><input type='number' size='10' value='0'></td></tr>"
			}
			projUpdateMPData = projUpdateMPData + "</table>"
			document.getElementById("projUpdateMC").innerHTML = projUpdateMPData
		}
		else
		{
			if(document.getElementById("projUpdateMC").style.display == "none"){ document.getElementById("projUpdateMCTB").style.display = "none" }
		}
	

		var indType = ManPowerData[0].split("~$~");
		var indName = ManPowerData[1].split("~$~");
		var indUnit = ManPowerData[2].split("~$~");
		var indPlan = ManPowerData[3].split("~$~");
		var indActual = ManPowerData[4].split("~$~");
		var indComp = ManPowerData[5].split("~$~");
		var indCat = WorkData[6].split("~$~");
		
		var indComp1 = 0; 
		
		if (indComp == "" ){
			indComp = 0;
		}else{
			for(i=0 ; i<indComp.length ; i++)
			{	indComp1 = indComp1 + parseFloat(indComp[i]) }
		}
					
		if( indType[0] != "")
		{
			var projUpdateMPData = "<table class='pgUpdTable'><tr ><th colspan = 3 align='left'>Manpower</th><th colspan = 3>" + indComp1 + "% Complete</th></tr>"
			projUpdateMPData = projUpdateMPData + "<tr style='font-weight: bold;'><td width='20%'>Category</td><td width='20%'>Name</td><td width='15%'>Unit</td><td width='15%'>Plan</td><td width='15%'>Actual</td><td width='15%'>Current</td></tr>";

			for(i=0 ; i<indType.length ; i++)
			{
				if (indActual[i] == undefined || indActual[i] == "")
					var actVal = 0;
				else
					var actVal = indActual[i]	
			
				if (i != 0)
					projUpdateMPData = projUpdateMPData + "<tr><td colspan = 6 ><div style='height:1px;background-color:#69A94E;'></div></td></tr>";
				projUpdateMPData = projUpdateMPData + "<tr><td>" + indCat[i] + "</td>"				
				projUpdateMPData = projUpdateMPData + "<td>" + indName[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + indUnit[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + indPlan[i] + "</td>"
				projUpdateMPData = projUpdateMPData + "<td>" + actVal + "</td>"
				projUpdateMPData = projUpdateMPData + "<td><input type='number' size='10' value='0'></td></tr>"
			}
			projUpdateMPData = projUpdateMPData + "</table>"
			document.getElementById("projUpdateMP").innerHTML = projUpdateMPData
		}
		else
		{
			if(document.getElementById("projUpdateMP").style.display == "none"){ document.getElementById("projUpdateMPTB").style.display = "none" }
		}

	
		var indType = MaterialData[0].split("~$~");
		var indName = MaterialData[1].split("~$~");
		var indUnit = MaterialData[2].split("~$~");
		var indPlan = MaterialData[3].split("~$~");
		var indActual = MaterialData[4].split("~$~");
		var indComp = MaterialData[5].split("~$~");
		var indCat = WorkData[6].split("~$~");
		
		var indComp1 = 0; 
		
		if (indComp == "" ){
			indComp = 0;
		}else{
			for(i=0 ; i<indComp.length ; i++)
			{	indComp1 = indComp1 + parseInt(indComp[i])		}
		}
		
		if( indType[0] != "")
		{
			var projUpdateMPData = "<table class='pgUpdTable'><tr><th colspan = 3 align='left'>Material</th><th colspan = 3>" + indComp1 + "% Complete</th></tr>"
			projUpdateMPData = projUpdateMPData + "<tr style='font-weight: bold;'><td width='20%'>Category</td><td width='20%'>Name</td><td width='15%'>Unit</td><td width='15%'>Plan</td><td width='15%'>Actual</td><td width='15%'>Current</td></tr>";
	
			for(i=0 ; i<indType.length ; i++)
			{
				if (indActual[i] == undefined || indActual[i] == "")
					var actVal = 0;
				else
					var actVal = indActual[i];	
			
				if (i != 0)
					projUpdateMPData = projUpdateMPData + "<tr><td colspan = 6><div style='height:1px;background-color:#69A94E;'></div></td></tr>";
				projUpdateMPData = projUpdateMPData + "<tr><td>" + indCat[i] + "</td>"	
				projUpdateMPData = projUpdateMPData + "<td>" + indName[i] + "</td>";
				projUpdateMPData = projUpdateMPData + "<td>" + indUnit[i] + "</td>";
				projUpdateMPData = projUpdateMPData + "<td>" + indPlan[i] + "</td>";
				projUpdateMPData = projUpdateMPData + "<td>" + actVal + "</td>";
				projUpdateMPData = projUpdateMPData + "<td><input type='number' size='5' value='0'></td></tr>";
			}
			projUpdateMPData = projUpdateMPData + "</table>";
			document.getElementById("projUpdateMT").innerHTML = projUpdateMPData;
		}
		else
		{
			if(document.getElementById("projUpdateMT").style.display == "none"){ document.getElementById("projUpdateMTTB").style.display = "none"; }
		}

	}		



	function fnUpdateProgress()
	{
		
//fetching task name$(function(){
	var task_Name ="";
    //Listen for a click on any of the dropdown items
		$(".taskName li").click(function(){
			//Get the value
			var task_Name = $(this).attr("value");
			//Put the retrieved value into the hidden input
			//$("input[name='thenumbers']").val(value);
		});
	
		var docCur = document.forms[0];
		
		var ManPowerData = docCur.ManPowerData.value.split("@^@");
		var MachineryData = docCur.MachineryData.value.split("@^@");
		var MaterialData = docCur.MaterialData.value.split("@^@");
		var WorkData = docCur.WorkData.value.split("@^@");

//Manpower consumed		
		var divData = document.getElementById("projUpdateMP").getElementsByTagName("input");
		var updateVal0 = "task_manpower_consumed" + "^@$@^" + ManPowerData[0]+ "^@$@^" + ManPowerData[1] + "^@$@^" + ManPowerData[2] + "^@$@^"+ ManPowerData[6]+"^@$@^";
		var updateVal = "";
		for( i=0 ; i < divData.length ; i++) 
		{
			if(updateVal != "")
				updateVal = updateVal + "*$$*"
			updateVal = updateVal + divData[i].value
		}
		if( ManPowerData[1] != undefined ){
			var manpUpdData = updateVal0 + updateVal + "@^@";
		}else{var manpUpdData = "";}
		
//Machinery consumed
		var divData = document.getElementById("projUpdateMC").getElementsByTagName("input")		
		var updateVal0 = "task_machinery_consumed" + "^@$@^" + MachineryData[0] + "^@$@^" + MachineryData[1] + "^@$@^" + MachineryData[2]  + "^@$@^"+ MachineryData[6]+"^@$@^";
		var updateVal = "";
		for( i=0 ; i < divData.length ; i++)
		{
			if(updateVal != "")
				updateVal = updateVal + "*$$*"
			updateVal = updateVal + divData[i].value
		}		
		if( MachineryData[1] != undefined ){
			var macUpdData = updateVal0 + updateVal+ "@^@";
		}else{var macUpdData = "";}

	
//Material consumed		
		var divData = document.getElementById("projUpdateMT").getElementsByTagName("input")
		var updateVal0 = "task_material_consumed" + "^@$@^" + MaterialData[0]+ "^@$@^" + MaterialData[1] + "^@$@^" + MaterialData[2] + "^@$@^"+ MaterialData[6]+"^@$@^";
		var updateVal = "";
		for( i=0 ; i < divData.length ; i++)
		{
			if(updateVal != "")
				updateVal = updateVal + "*$$*"
			updateVal = updateVal + divData[i].value
		}
	
		if( MaterialData[1] != undefined ){
			var matUpdData = updateVal0 + updateVal+ "@^@";
		}else{var matUpdData = "";}

//Work consumed		
		var divData = document.getElementById("projUpdateWT").getElementsByTagName("input")
		var updateVal0 = "task_work_consumed" + "^@$@^" + WorkData[0] + "^@$@^" + WorkData[1]+ "^@$@^" + WorkData[2] + "^@$@^"+ WorkData[6]+"^@$@^";
		var updateVal = "";
		for( i=0 ; i < divData.length ; i++)
		{
			if(updateVal != "")
				updateVal = updateVal + "*$$*"
			updateVal = updateVal + divData[i].value
		}
		
		if( WorkData[1] != undefined ){
			var workUpdData = updateVal0 + updateVal;
		}else{var workUpdData = "";}
		
		var task_Name = '<?php echo $_REQUEST["taskName"]; ?>';
		var updateData = manpUpdData + macUpdData + matUpdData + workUpdData;
		var updateData = updateData + "*@$@*" + document.forms[0].txProjectID.value + "*@$@*" + document.forms[0].txTaskID.value+"*@$@*"+task_Name;
		var strAgtPath = "../update_actuals/updateTaskProgress.php?updateData=" + updateData;
		
		$.ajax({url:strAgtPath, type:"POST", async:false, processData:false, data:updateData, dataType:'text', success:function(result){
			result = result.split("*@$@*")
			if (result[0] != "")
			{

			}
			else
			{
				var txTaskID = '<?php echo $_REQUEST["txTaskID"]; ?>';
				var page = "/pbtsystem/library/update_actuals/progress_update.php?openform&pid=" + window.opener.document.forms[0].txProjectID.value + "&txTaskID=" + txTaskID;
				location.href=page;
			}
		}});
		
	}


</script>

<style>
	body {margin:0;}

	.navbar {
	  overflow: hidden;
	  background-color: #333;
	  position: fixed;
	  top: 0;
	  width: 100%;
	}
	.navbar a {
		float: left;
		display: block;
		color: #f2f2f2;
		text-align: center;
		padding: 14px 16px;
		text-decoration: none;
	}
	.main {
	  padding: 16px;
	  margin-top: 50px;
	  height: 1500px; 
	}


</style>

</head>

<body onload='loadResources();'>

<form method="post" action="" id="frmProgressUpdate">

<script>
	function submitValidation(){
		//Input validation
		var inputValues = document.getElementById("updtActuals").getElementsByTagName("input")
		var m = 0;
		
		for ( i=0 ; i<inputValues.length ; i++ )
		{
			if ( inputValues[i].type == "number" && inputValues[i].value != 0 )
			{
				m = m+1;
			}
		}
		
		if( m != 0 ){			
			fnUpdateProgress();
		}	
	}

</script>

<div class="navbar">
	<a href="#">Update Actuals</a>				
	<div class="nav navbar-nav navbar-right">
	    <button class="btn btn-success navbar-btn" onClick="submitValidation()"><span class="glyphicon glyphicon-ok-circle"></span> Update</button>
    </div>
</div>


<script>
	function fnSelectTask( strTaskID , strProjID, strTaskName )
	{
		location.href = "./progress_update.php?openform&cacCont=<?php echo $cacCont; ?>&pid=" + strProjID + "&txTaskID=" + strTaskID +"&taskName="+strTaskName;
	}
</script>


<div class="container main" >
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="row">				
				<div class="col-sm-2" >						
				</div>
				<div class="col-sm-8" >	
					<div style="text-align: center"><b><?php echo $task_name; ?></b></div>
				</div>
				<div class="col-sm-2" >	
					<div class='dropdown pull-right'>
						<div class='collapse navbar-collapse' id='navbar-collapse-1'>
							<ul class='nav navbar-nav'>                               
								<li class='dropdown'>
								  <button class='btn btn-success dropdown-toggle btn-sm' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-import'></span> Select Task<span class='caret'></span></button>
									<ul class='dropdown-menu' name="taskName"><?php echo $tasksList; ?></ul>
								</li>
							</ul>
						</div>									
					</div>					
				</div>	
			</div>	
		</div> 
		
		<div class="panel-body" id="updtActuals">  	
			<div id="projUpdateWTTB"> <div id='projUpdateWT' > </div> <br> </div>
			<div id="projUpdateMPTB"> <div id='projUpdateMP' > </div> <br> </div>
			<div id="projUpdateMTTB"> <div id='projUpdateMT'> </div> <br> </div>
			<div id="projUpdateMCTB"> <div id='projUpdateMC' > </div> <br> </div>
		</div>	
	</div><!-- Panel ends here -->

	<input name="txProjectID" type="hidden" value='<?php echo $_REQUEST["pid"]; ?>' >
	<input name="txTaskID" type="hidden" value='<?php echo $_REQUEST["txTaskID"]; ?>' >
	<input name="txCurDtTim" type="hidden" value="2016910134633">
	<input name="txDBPath" type="hidden" value="PMTool/projMgmtPortal.nsf">

	<input name="ManPowerData" type="hidden" value='<?php echo $resourceMNP; ?>'>
	<input name="MachineryData" type="hidden" value='<?php echo $resourceMAC; ?>'>
	<input name="MaterialData" type="hidden" value='<?php echo $resourceMAT; ?>'>
	<input name="WorkData" type="hidden" value='<?php echo $resourceWRK; ?>'>
</div>

</form>

</body>
</html>

