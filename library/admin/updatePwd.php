<?php
include('./library/dbconnect.php');
						 
if (($_SERVER["REQUEST_METHOD"] == "POST") AND (isset($_POST['upload']))) {
	
	if(trim_input($_POST["userpwd"]) != ""){
		$usermail = trim_input($_POST["username"]);
		$userpwd = trim_input($_POST["userpwd"]);
	}	

	$check = "SELECT * from con_login WHERE usermail='$usermail'";
	$conn->query($check) or die('Error, query failed');
	$reqResult = $conn->query($check);
	$row = $reqResult->fetch_array(MYSQLI_ASSOC); 
	
	$returnmsg ="";	

	if($row['usermail'] != "" ){
		$sql = "UPDATE con_login SET userpwd='$userpwd' WHERE usermail='$usermail'";
		$conn->query($sql);
		$returnmsg = "<div class='alert alert-success'>
			<strong>Success!</strong>&nbsp;Password updated for &nbsp;<b> $usermail </b>
				</div>";
	}else{
		$returnmsg = "<div class='alert alert-danger'>
			<strong>Failed!</strong>&nbsp;Selected user name not found in the records </div>";
	}

}
	function trim_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
	
$conn->close();

include 'adminpage.php';

?>