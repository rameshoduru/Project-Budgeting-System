<?php

// Establishing Connection with Server by passing server_name, user_id and password as a parameter
include('../dbconnect.php');

	$fileName = basename($_SERVER['PHP_SELF']);
	
	if( $fileName == "adminpage.php"){
		$pageTitle = "Administration Console";
	}

?>	

	<div class="navbar navbar-default navbar-top">
		<label class="col-sm-9 control-label menuClass">&nbsp;Welcome : <i class="glyphicon glyphicon-user"></i> 
			<b>&nbsp;<?php echo ucwords(strtolower($login_firstname)); ?>&nbsp;<?php echo ucwords(strtolower($login_lastname)); ?></b>
		</label>
		<div class="col-sm-3 pull-right text-right">
			<label class="control-label"><a href="../pbstracker/pbsHome.php" class="btn btn-primary btn-xs menuClass">
						<span class="glyphicon glyphicon-home"></span>&nbsp;Home</a></label>
			<label class="control-label"><a href="http://localhost/xampp/mobpublishing/library/logout.php" class="btn btn-primary btn-xs menuClass">
				<span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a>
			</label>
		</div>
	</div>
	                
	<div class="panel panel-primary" style="margin-bottom: 1px;">
	    <div class="panel-body">
			<div class="row header">		
				<div class="col-sm-3">	
					<div align="left"> 
					<img src="./App Listings - Unilever_files/blank.gif"><br>
						<img src="./App Listings - Unilever_files/unilever-logo.svg" alt="Unilever logo" class="logo__img">
					</div>	
				</div>
				<div class="col-sm-6">	<br>
					<h3 class="pageTitle"><?php //echo $pageTitle; ?></h3>
				</div>
				
				<div class="col-sm-3" align="right">					
											
				</div>
			</div>
			
			<div id="adminlink" class="row header pull-right text-right">
				<div class="col-sm-12"></div>
			</div>			
		</div>
	</div>	