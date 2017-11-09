<?php  
include('../session.php');
include('../reports/disp_planned_resources.php');
include('../reports/disp_todays_tasks.php');
include('../home/php_onLoad.php');

//Random number for urls
//$_SESSION['cacCont'] = strtoupper( substr( md5(uniqid(rand(), true)), 0, 12 ) );
//$cacCont=session_id();
//$cacCont=strtoupper( substr( md5(uniqid(rand(), true)), 0, 12 ) );

$pageTitle = "Project $$$$$";
$projName ="";
$projdata="";
$linkdata="";
$projno = "";
$txResourceType = "";
$txResourceName = "";
$txResourceQuantity = "";
$txResourceUnit = "";	
$progtype = "";
$resDetails = "";

global $project_id;

	if(!isset($project_id) )
	{
		$project_id = strtoupper( substr( md5(uniqid(rand(), true)), 0, 8 ) );
		$_SESSION["project_id"] = $project_id;
	}
	else
	{		
		//if( ($login_userrole == 'ADM') or ($login_userrole == "PM") ){
			$projdata = php_onLoad($progtype); //Data fetched from ./library/home/php_onLoad.php
			$linkdata = task_links();   //Data fetched from ./library/home/php_onLoad.php
		//}
	} 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Project Management</title>


	<?php
	include ("../uilinks.php");
	?>

	<script language="JavaScript" type='text/javascript'>
	var docProj;
	var users_data = {<?php echo $projdata; ?>, <?php echo $linkdata; ?>};

	<!-- dialogbox -->
         $(function() {
            $( "#tasksList" ).dialog({
               autoOpen: false, 
               hide: "fade",
               show : "slide",
               height: "300"      
            });
            $( "#opener" ).click(function() {
			   ($("#tasksList").dialog("isOpen") == false) ? $("#tasksList").dialog("open") : $("#tasksList").dialog("close") ;
            });
         });
    </script>
	<!--
	<style>
	 .ui-widget-header,.ui-state-default, ui-button{
		background:#b9cd6d;
		border: 1px solid #b9cd6d;
		color: #FFFFFF;
		font-weight: bold;
	 }	 
    </style>
	-->
	
</head>

<body text="#000000" bgcolor="#FFFFFF" onload="loadProject()">

<form method="post" name="frmProject">


<?php 
include ("../home/subform_header_menu.php");
?>

<div style="display:none">
	<input name="txResourceType" value="<?php echo $txResourceType; ?>" >
	<input name="txResourceName" value="<?php echo $txResourceName; ?>" >
	<input name="txResourceQuantity" value="<?php echo $txResourceQuantity; ?>">
	<input name="txResourceUnit" value="<?php echo $txResourceUnit; ?>" >
	<input name="txCurDtTim" value='<?php echo(mt_rand()); ?>'>
	<input name="txDBPath" type="hidden" value="PMTool/projMgmtPortal.nsf">
	<input name="txProgView" type="hidden" value="vwTaskDocuments">
	<input name="numBaseLineNumber" value="0" >
	<input name="txProjectID" value="<?php echo $project_id;; ?>">
	<input name="proj_manager" value="<?php echo $login_session; ?>">	
	<input name="%%Surrogate_txProjectGanttSet" type="hidden" value="1">							
	<input name="txProjectGanttSet" type="hidden" value="1">
</div>

<!-- Here goes Gantt chart -->

<div id="chartContent" style="width: 100%; padding: 8px; background:#000000;" >
	<div id="gantt_here" style="width:100%; height:85%;">
		<div class="gantt_container">
		</div>
	</div>
	<div class='appFooter'>© All Rights Reserved - Imperio Tech Solutions - 2014</div>
</div>
<!--Gantt chart -->

						
<div id="addManPowerDIV" class="divAddMP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-body">
			<div class="panel panel-info">
				<div class="panel-heading">	Manpower Details
					<div class="pull-right">
						<a class="btn btn-success btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpManpowerUpdate()" title="Update"><span class="glyphicon glyphicon-save"></span></a>
						<a class="btn btn-danger btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpManpowerCancel()" title="Cancel"><span class="glyphicon glyphicon-remove-sign"></a>
					</div>
				</div>
			</div>
			<div class="panel-body" id="divAddMPCont"> </div>
		</div>
      </div>
    </div>
</div>


<div id="addMachineryDIV" class="divAddMC" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
       <div class="modal-body">
			<div class="panel panel-info">
				<div class="panel-heading">Machinery Details
					<div class="pull-right">
						<a class="btn btn-success btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpMachinaryUpdate()" title="Update"><span class="glyphicon glyphicon-save"></span></a>
						<a class="btn btn-danger btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpMachinaryCancel()" title="Cancel"><span class="glyphicon glyphicon-remove-sign"></a>
					</div>
				</div>
				<div class="panel-body" id="divAddMCCont"> </div>
			</div>
      </div>
    </div>
  </div>
</div>

<div id="addMaterialDIV" class="divAddMT" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
			<div class="panel panel-info">
				<div class="panel-heading">Maretial Details
					<div class="pull-right">
						<a class="btn btn-success btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpMaterialUpdate()" title="Update"><span class="glyphicon glyphicon-save"></span></a>
						<a class="btn btn-danger btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpMaterialCancel()" title="Cancel"><span class="glyphicon glyphicon-remove-sign"></span></a>
					</div>
				</div>
				<div class="panel-body" id="divAddMTCont"> </div>
			</div> 	
      </div>
    </div>
  </div>
</div>

<div id="addWorkDIV" class="divAddWT" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">      
      <div class="modal-body">
			<div class="panel panel-info">
				<div class="panel-heading">Work Details
					<div class="pull-right">
						<a class="btn btn-success btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpWorkUpdate()" title="Update"><span class="glyphicon glyphicon-save"></span></a>
						<a class="btn btn-danger btn-sm" href="<?php echo $doc_basepath; ?>/library/home/pbsHome.php?pid=<?php echo $project_id; ?>&amp;cacCont=<?php echo $cacCont; ?>#" onclick="mpWorkCancel()" title="Cancel"><span class="glyphicon glyphicon-remove-sign"></span></a>
					</div>
				</div>
				<div class="panel-body" id="divAddWTCont"> </div>
			</div>        
      </div>      
    </div>

  </div>
</div>
		
			
		
	<!-- Modal - Resource details -->
	<div class = "modal fade" id = "resModal" tabindex = "-1" role = "dialog" aria-labelledby = "resModalLabel" aria-hidden = "true">
	   
		<div class = "modal-dialog">
			<div class = "modal-content modal-lg">
			 
			<div class = "modal-header">
				<button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">
					  &times;
				</button>
				
				<h5 class = "modal-title" id = "resModalLabel">
				   <b>Project Resources </b>
				</h5>
			</div>

			<!-- values are populated from \library\reports\planned_resources.php	-->			
			<div class = "modal-body">				
				<ul id = "myTab" class = "nav nav-tabs">
				   <li class = "active"><a href = "#work" data-toggle = "tab">Work</a></li>   
				   <li><a href = "#manpower" data-toggle = "tab">Man Power</a></li>	
				   <li><a href = "#material" data-toggle = "tab">Material</a></li>
				   <li><a href = "#machinery" data-toggle = "tab">Machinery</a></li>	
				</ul>				

				<div class = "tab-content">
				   <div class = "tab-pane fade in active" id = "work">
						</br> <?php echo $workTab; ?>
				   </div>					   
				   <div class = "tab-pane fade" id = "manpower">
						</br> <?php echo $manpowerTab; ?>
				   </div>					   
				   <div class = "tab-pane fade" id = "material">
						</br> <?php echo $materialTab; ?>
				   </div>					   
				   <div class = "tab-pane fade" id = "machinery">
						</br> <?php echo $machineryTab; ?>
				   </div>
				</div>					
			</div>				
			<div class = "modal-footer">
				<button type = "button" class = "btn btn-default" data-dismiss = "modal"> Close	</button>
			</div>
			 
		  </div><!-- /.modal-content -->
	   </div><!-- /.modal-dialog -->	  
	</div><!-- /.modal - Resource details -->

<!-- Modal - Task lists - List of tasks those have planned start date as today -->
	<div class = "modal fade" id = "taskList" tabindex = "-1" role = "dialog" aria-labelledby = "resModalLabel" aria-hidden = "true">	   
		<div class = "modal-dialog">
			<div class = "modal-content">
			 
				<div class = "modal-header">
					<button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">
						  &times;
					</button>					
					<h4 class = "modal-title" id = "resModalLabel">
					   <b>Tasks planned to start today</b>
					</h5>
				</div>
				 
				<div class = "modal-body">												   
					<?php echo $todaysTasks; ?>		<!-- Values fetched from 'disp_todays_tasks.php' -->	
				</div>
				 
				<div class = "modal-footer">
					<button type = "button" class = "btn btn-default" data-dismiss = "modal">Close</button>
				</div>
			 
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->	  
	</div>
	
<!-- /.modal - List of tasks those have planned start date as today -->
	
	
<!-- Modal - Project Earned Value -->
	<div class = "modal fade" id = "evModal" tabindex = "-1" role = "dialog" aria-labelledby = "resModalLabel" aria-hidden = "true">
	   
		<div class = "modal-dialog">
			<div class = "modal-content">
			 
			<div class = "modal-header">
				<button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">
					  &times;
				</button>
				
				<h5 class = "modal-title" id = "resModalLabel">
				   <b>Earned Value</b>
				</h5>
			</div>
			 
			<div class = "modal-body">												   
				<?php include('../reports/disp_project_ev.php'); ?>			
			</div>
			 
			<div class = "modal-footer">
				<button type = "button" class = "btn btn-default" data-dismiss = "modal"> Close	</button>
			</div>
			 
		  </div><!-- /.modal-content -->
	   </div><!-- /.modal-dialog -->	  
	</div><!-- /.modal - Project Earned Value -->


    <div id="tasksList" title="Click Task to Enter Completion"><?php echo $tasksList; ?></div>

<br/>
	  
</form>

</body>
</html>