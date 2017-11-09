<?php
//Function:list_of_projects()
//Purpose: Generates list of projects associted with user id
//Source file: appconfig.php
	list_of_projects();

// $proj_list is generated in appconfig.php 
//Calling function: list_of_projects()
//Variable name: $proj_list	

$open_project ="";
	$project_ids = array_keys($proj_list);
	if(isset($project_ids))
	{
		$proj_list_global = "<ul class='dropdown-menu'>";
		for($i=0; $i<sizeof($proj_list); $i++)
		{
			$proj_list_global = $proj_list_global . "<li><a href='#' onClick=\"projectSelection('".$project_ids[$i]."')\">".$proj_list[$project_ids[$i]]."</a></li>";					
		}
		$proj_list_global = $proj_list_global. "</ul>";
		
		$open_project ="<ul class='nav navbar-nav'>  <li class='dropdown'> <button class='btn btn-info dropdown-toggle btn-sm' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-folder-open'></span> Open<span class='caret'></span></button> $proj_list_global</li></ul>";		
	}
	
	
//Fetching list of tasks from task_master table
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
			$tasksList = $tasksList."<li><a href='#' onClick=\"fnSelectTask('".$row['task_id']."' , '".$row['project_id']."')\">".$row['task_name']."</a></li>";				
			}
		}

//List of projects based on user access
	if(isset($login_session) ){
		$sql = "SELECT * FROM project_master WHERE proj_users='$login_session'";
		$conn->query($sql) or die('Error, task query failed');
		$proj_list = $conn->query($sql);			
	}
	
//Generating action buttons 

$resUploadAction = "";
$frmSubmit = "";
$periodicVus = "";
$progressVus = "";
$progressUpdates = "";
$reports_disp = "";
$reports_download = "";

$frmSubmit ="<button type='submit' id='btnSubmitEnable' class='btn btn-success btn-sm' title='Save Project' onclick='fnSubmit()'><span class='glyphicon glyphicon-save-file'></span> Save</button><button id='btnSubmitDisabled' class='btn btn-success btn-sm disabled'><span class='glyphicon glyphicon-save-file'></span> Save</button>";
			
	if(isset($_REQUEST['pid']) ){
		if ($_REQUEST['pid'] != ""){
			
			$project_id = $_REQUEST['pid'];
						
			$periodicVus = "<div class='collapse navbar-collapse' id='navbar-collapse-1'>
					<ul class='nav navbar-nav'>                               
						<li class='dropdown'>
						  <button class='btn btn-info dropdown-toggle btn-sm' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-calendar'></span> Views<span class='caret'></span></button>
							<ul class='dropdown-menu'>
								<li><a href='./pbsHome.php?pid=$project_id&amp;progtp=#' onclick='dailyGanttView()' >Daily</a></li>
								<li><a href='./pbsHome.php?pid=$project_id&amp;progtp=#' onclick='weeklyGanttView()' >Weekly</a></li>
								<li><a href='./pbsHome.php?pid=$project_id&amp;progtp=#' onclick='monthlyGanttView()' >Monthly</a></li>
							</ul>
						</li>
					</ul>
				</div>";
			
			$progressVus = "<div class='collapse navbar-collapse' id='navbar-collapse-1'>
					<ul class='nav navbar-nav'>                               
						<li class='dropdown'>
						  <button class='btn btn-info dropdown-toggle btn-sm' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-tasks'></span> Progress<span class='caret'></span></button>
							<ul class='dropdown-menu'>
								<li><a href='./pbsHome.php?pid=$project_id&amp;cacCont=$cacCont&progtp=mpp#'>Manpower</a></li>
								<li><a href='./pbsHome.php?pid=$project_id&amp;cacCont=$cacCont&progtp=mcp#'>Machinery</a></li>
								<li><a href='./pbsHome.php?pid=$project_id&amp;cacCont=$cacCont&progtp=mtp#'>Material</a></li>
								<li><a href='./pbsHome.php?pid=$project_id&amp;cacCont=$cacCont&progtp=wtp#'>Work</a></li>
							</ul>
						</li>
					</ul>
				</div>";
			
			$reports_disp = "<div class='collapse navbar-collapse' id='navbar-collapse-1'>
				<ul class='nav navbar-nav'>                               
					<li class='dropdown'>
					  <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-modal-window'></span> Reports	<span class='caret'></span></button>
						<ul class='dropdown-menu'>                          
							<li><a href='#' data-toggle='modal' data-target='#taskList'>Tasks Starting Today</a></li>
							<li><a href='#' data-toggle='modal' data-target='#evModal'>Earned Value</a></li>
							<li><a href='#' data-toggle='modal' data-target='#resModal'>Resource Plan </a></li>                     							
						</ul>
					</li>
				</ul>
			</div>";
			
			$reports_download = "<div class='collapse navbar-collapse' id='navbar-collapse-1'>
				<ul class='nav navbar-nav'> 
					<li class='dropdown'>
					  <button class='btn btn-primary btn-sm dropdown-toggle' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-download-alt'></span> Reports	<span class='caret'></span></button>
						<ul class='dropdown-menu'> 
							<li><a  href='../reports/resource_allocation_download.php?pid=$project_id&amp;progtp=#'>Resource Allocation</a></li>							
							<li class='dropdown dropdown-submenu'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Resource Matrix</a>
								<ul class='dropdown-menu'>
									<li><a tabindex='-1' href='../reports/resource_plan_matrix_category.php?pid=$project_id&amp;progtp=#'>By Category</a></li>
									<li><a tabindex='-1' href='../reports/resource_plan_matrix_task.php?pid=$project_id&amp;progtp=#'>By Task</a></li>                                 
								</ul>
							</li> 
							<li class='dropdown dropdown-submenu'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Resource Gantt</a>
								<ul class='dropdown-menu'>
									<li><a tabindex='-1' href='../reports/resource_plan_gantt_category.php?pid=$project_id&amp;progtp=#'>By Category</a></li>
									<li><a tabindex='-1' href='../reports/resource_plan_gantt_task.php?pid=$project_id&amp;progtp=#'>By Task</a></li>                                
								</ul>
							</li>
							<li class='dropdown dropdown-submenu'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Resource Utilization</a>
								<ul class='dropdown-menu'>
									<li><a tabindex='-1' href='../reports/resource_utilization_matrix_task.php?pid=$project_id&amp;progtp=#'>By Category</a></li>
									<li><a tabindex='-1' href='../reports/resource_utilization_matrix_task.php?pid=$project_id&amp;progtp=#'>By Task</a></li>                                
								</ul>
							</li>
							<li><a  href='../reports/resource_rollup.php?pid=$project_id&amp;progtp=#'>Resource Distribution</a></li>							
						</ul>
					</li>
				</ul>
			</div>";
			
			$progressUpdates = "<a href='../update_actuals/progress_update.php?pid=$project_id&amp;cacCont=$cacCont&progtp=' class='btn btn-success btn-sm' title='Update actuals' target='_blank'><span class='glyphicon glyphicon-upload'></span> Actuals</a>";
						
		}else{
			$frmSubmit = "";
			$periodicVus = "";
			$reports_disp = ""; //Reports drop down
			$progressVus = "";
		}
	}
	$Administration ="<a href='../admin/adminpage.php?pid=$project_id&amp;cacCont=$cacCont&progtp=' class='btn btn-danger btn-sm' title='Administration' target='_blank'><span class='glyphicon glyphicon-upload'></span> Admin</a>";;
?>

<script>
// function to choose project
function projectSelection(project_id) {
	location.href = "";	
	location.href = "./pbsHome.php?pid=" + project_id + "&cacCont=<?php echo $cacCont; ?>&progtp=";			   
}
</script>
<script>
//This is for reports dropdown submenu
$(document).ready(function(){
  $('.dropdown-submenu a.test').on("click", function(e){
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });
});
</script>
  
	<div class="row header">
		<div class="navbar-top navbar-custom">
			<div class="col-sm-2" style="font-family: verdana,helvetica; font-weight: bold;font-size: 11px;">
				<label class="control-label" ><i class="glyphicon glyphicon-user"></i> <b>
					&nbsp;<?php echo ucwords(strtolower($login_firstname)); ?>&nbsp;<?php echo ucwords(strtolower($login_lastname)); ?></b><!-- from session.php -->
				</label>			
			</div>		
			<div class="col-sm-8 text-center">
				<label class="control-label" >
					<h6>&nbsp;&nbsp;<?php echo $projName;?></h6><!-- from session.php -->
				</label>		
			</div>
						
			<div class="col-sm-2 pull-right text-right">
				<label><a href="/pbtsystem/library/logout.php" class="btn btn-warning btn-xs" title="Logout">
					<b><i class="glyphicon glyphicon-log-out"></i> </b></a>&nbsp;
				</label>
			</div>
		</div>
	</div>

			<div class="row header">				
				<div class="col-sm-1" >				
				</div>				
				<div class="col-sm-1" >
					<button type='button' class='btn btn-primary btn-sm' title='New Project' onclick='fnNew()'><span class='glyphicon glyphicon-new-window'></span> New</button>
				</div>
				<div class="col-sm-1" >	
					<div class='collapse navbar-collapse' id='navbar-collapse-1'>
						<?php echo $open_project; ?>
					</div>
				</div>
				<div class="col-sm-1" >
					<?php echo $progressVus; ?>
				</div>
				<div class="col-sm-1" >
					<?php echo $periodicVus; ?>
				</div>
				<div class="col-sm-1" >
					<?php echo $reports_disp; ?>	
				</div>
				<div class="col-sm-1" >
					<?php echo $reports_download; ?>
				</div>
				<div class="col-sm-1" >
					
				</div>
				<div class="col-sm-1" >
					<div class='collapse navbar-collapse' id='navbar-collapse-1'>
						<ul class='nav navbar-nav'>                               
							<li class='dropdown'>
							  <button class='btn btn-warning dropdown-toggle btn-sm' type='button' data-toggle='dropdown'><span class='glyphicon glyphicon-upload'></span> Upload<span class='caret'></span></button>
								<ul class='dropdown-menu'>
									<li><a href='/pbtsystem/library/upload/fileUpload_resource.php?pid=<?php echo $project_id;?>&uid=<?php echo $login_session; ?>&cacCont=<?php echo $cacCont; ?>&progtp=#'  target='_blank'>Resource</a></li>
									<li><a href='/pbtsystem/library/upload/fileUpload_project.php?pid=<?php echo $project_id;?>&uid=<?php echo $login_session; ?>&cacCont=<?php echo $cacCont; ?>&progtp=#' target='_blank'>Project</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-1" >					
					<?php echo $progressUpdates; ?>
				</div>
				<div class="col-sm-1" >					
					<?php echo $frmSubmit; ?>
				</div>	
				<div class="col-sm-1" >
					<?php echo $Administration; ?>	
				</div>				
			</div>