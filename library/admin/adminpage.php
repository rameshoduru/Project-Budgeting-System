<?php
include('../session.php'); 

/*
if( $login_userrole != 'ADM') {	
$redirection = "pbsHomepage.php";
	include 'redirection.php';
}
*/

?>

<html>
<head>
<title>App Listings - Unilever</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
<?php  
include('../uilinks.php');
?>
	
	<script type="text/javascript">

		var errmsg = "";
		var sEmail;
		var msgtxt;
		var stopSubmit;
		function updatePwd(){
			stopSubmit=false;			
			msgtxt="";

			sEmail = $('#username').val();
			errmsg = "\nUser name should be of email format";
	
			validateEmail(sEmail, errmsg);
			
			if(stopSubmit==true){
				alert(msgtxt);				
				return false;
			}			
			
			document.getElementById('appRequest').action = "<?php echo htmlspecialchars("updatePwd.php");?>";
		};

		
		function userReg(){	
			stopSubmit=false;			
			msgtxt="";

			sEmail = $('#usermail').val();
			errmsg = "\nWrong format of user mail id";
				validateEmail(sEmail, errmsg);			
			if(stopSubmit==true){
				alert(msgtxt);				
				return false;
			}			
		
			document.getElementById('appRequest').action = "<?php echo htmlspecialchars("userRegUpdate.php");?>";	
		};	
		
		
	
		var atype;
		function loadActivity(){
		var radioType = document.forms[0].elements["admin"];
			for(var i=0;i<radioType.length;i++){
				if(radioType[i].checked){
					atype = radioType[i].value;
				}
			}	
				
			if( atype == "updtPass" ){
					$("#pagecontent").load("updatePassword.php");
			}else if( atype == "userReg" ){
					$("#pagecontent").load("userReg.php");
			}else if( atype == "remProject" ){
					$("#pagecontent").load("remProject.php");
			};
					$("#info").html("");			
		}	
		
		function loadPage(){
			$("#info").html("");			
		}	

		
	</script>
</head>

<body>
<div class="container">

<?php include("subform_header_menu.php") ?>

<form id="appRequest" class="form-horizontal" method="post" enctype="multipart/form-data">


<div class="panel panel-primary" style="margin-bottom: 1px;">
    <div class="panel-body">
		<div class="form-group">
			<div class="col-sm-1"></div>
			<div class="col-sm-8">
				<label class="radio-inline radioClass"><input type="radio" name="admin" onClick="loadActivity();" value="userReg" required>New User Registration</label>			
				<label class="radio-inline radioClass"><input type="radio" name="admin" onClick="loadActivity();" value="updtPass" required>Update Password</label>	
				<label class="radio-inline radioClass"><input type="radio" name="admin" onClick="loadActivity();" value="remProject" required>Update Password</label>	
			</div>	
		</div>	
	</div>
	
	 <div class="panel-body">
		<div class="form-group">
			<div class="col-sm-1"></div>
			<div class="col-sm-8">
				<label class="radio-inline radioClass"><input type="radio" name="admin" onClick="loadActivity();" value="userReg" required>New User Registration</label>			
				<label class="radio-inline radioClass"><input type="radio" name="admin" onClick="loadActivity();" value="updtPass" required>Update Password</label>	
			</div>	
		</div>	
	</div>
	
</div>

<div id="info"><?php if(isset($returnmsg)){echo $returnmsg;}?></div>		
		
<div id="pagecontent"></div>

</div>
 </form>
 </body>
 </html>
 