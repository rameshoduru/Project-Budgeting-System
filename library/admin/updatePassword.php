<?php

			
?>

  <script>
  var userids = "ramesh,kumar,singh";
  userids = userids.split(",");
  $(function() {
    var availableNames = userids;
    $( "#username" ).autocomplete({
      source: availableNames
    });
  });
   
  </script>
  
<div class="panel panel-primary">
      <div class="panel-heading">Update Password</div> 
<br>
	<div class="panel-body">  
	
	<div class="form-group">
		<div class="col-sm-6 adminPage">
			<b>Email ID</b>
		</div>
		<div class="col-sm-3 adminPage">
			<b>Password</b>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-6">		
			<div class="ui-widget">
				<input class="form-control input-sm" name="username" id="username" required>
			</div>		
		</div>

		<div class="col-sm-3" >
			<input class="form-control input-sm" name="userpwd" id="userpwd" required>
		</div>
		<div class="col-sm-2">
			<input type="submit" class="btn btn-info sm-2" name="upload" value="Update" onClick="return updatePwd();"> 
		</div>
	</div>	
	</div>	
</div><!-- Panel ends here -->