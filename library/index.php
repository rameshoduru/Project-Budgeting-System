<?php
include('./login.php'); // Includes Login Script
if(isset($_SESSION['login_user'])){
header("location: ./home/pbsHome.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php
include('./uilinks.php');
?>

   <style type="text/css">
            .modal-footer {   border-top: 0px; }
   </style>
	
</head>
<body>

<div class="container header">
	
 <form class="form col-md-12 center-block" action="" method="post">
 
 <div class="row">		
		<div class="col-sm-6" >	
		<div align="left";> 
		
		</div>	
		</div>
		
	</div>
	<br><br>
<div id="loginModal" class="modal show header" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
  <div class="modal-content">
      <div class="modal-header">
           <h1 class="text-center">Login</h1>
      </div>
      <div class="modal-body">         
            <div class="form-group">
              <input type="text" id="name" name="username" class="form-control input-lg" placeholder="Email">
            </div>
            <div class="form-group">
              <input type="password"  id="password" name="password" class="form-control input-lg" placeholder="Password">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">Sign In</button>
              <span class="pull-right"><a href="#">..</a></span><span><a href="#">..</a></span>
			 <span><?php echo $error; ?></span>
            </div>         
      </div>
      <div class="modal-footer">
          <div class="col-md-12">
         
		  </div>	
      </div>
  </div>
  </div>
   </form>
</div>

</div>


</body>
</html>