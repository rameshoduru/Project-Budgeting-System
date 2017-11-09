<?php
include('../dbconnect.php');
/*
	$sql = "SELECT * FROM con_territories";	
	$result = $conn->query($sql);	
	*/
	
?>

<div class="panel panel-primary">
      <div class="panel-heading">User Registration</div> 
<br>
	<div class="panel-body">  
	<div class="form-group">
		<div class="col-sm-3 adminPage">
			<b>First Name</b>
		</div>
		<div class="col-sm-3 adminPage">
			<b>Last Name</b>
		</div>		
		<div class="col-sm-3 adminPage">
			<b>Agency Name</b>
		</div>
		<div class="col-sm-3 adminPage">
			<b>Country</b>
		</div>		
	</div>

	<div class="form-group">
		<div class="col-sm-3">
			<input class="form-control input-sm" name="firstname" id="firstname" required>     
		</div>
		<div class="col-sm-3" >
			<input class="form-control input-sm" name="lastname" id="lastname" required>     
		</div>	
		<div class="col-sm-3" >
			<input class="form-control input-sm" name="agencyname" id="agencyname" required>     
		</div>
		<div class="col-sm-3" >
			<select class="form-control" name="agencycountry" required>
				<?php
				echo "<option value=''>-Select-</option>";
					
				?>
			</select>	    
		</div>		
	</div>

	<div class="form-group">
		<div class="col-sm-3 adminPage">
			<b>City</b>
		</div>
		<div class="col-sm-3 adminPage">
			<b>Email ID</b>
		</div>
		<div class="col-sm-3 adminPage" >
			<b>Password</b>
		</div>
	</div>	
	<div class="form-group">
		<div class="col-sm-3" >
			<input class="form-control input-sm" name="agencycity" id="agencycity" required> 
		</div>		
		<div class="col-sm-3">
			<input class="form-control input-sm" name="usermail" id="usermail" type="Email" placeholder="Email" required>			
		</div>
		<div class="col-sm-3" >
			<input class="form-control input-sm" name="userpwd" id="userpwd" required>
		</div>		
		<div class="col-sm-1">
			<input type="submit" class="btn btn-info sm-2" name="upload" value="Submit" onClick="return userReg();"> 
		</div>
	</div>	
	</div>
	
</div><!-- Panel ends here -->