<?php
include('./library/dbconnect.php');
						 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if(trim_input($_POST["userpwd"]) != ""){
		$firstname = trim_input($_POST["firstname"]);
		$lastname = trim_input($_POST["lastname"]);
		$agencyname = trim_input($_POST["agencyname"]);
		$agencycity = trim_input($_POST["agencycity"]);
		$agencycountry = trim_input($_POST["agencycountry"]);
		$usermail = trim_input($_POST["usermail"]);
		$userpwd = trim_input($_POST["userpwd"]);
	}	
}

function trim_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

	$check = "SELECT * from con_login WHERE usermail='$usermail'";
	$conn->query($check) or die('Error, query failed');
	$reqResult = $conn->query($check);
	$row = $reqResult->fetch_array(MYSQLI_ASSOC); 
	
	if($row['usermail'] == "" ){
		$sql = "INSERT INTO con_login (  firstname, lastname, agencyname, username, agencycity, agencycountry, usermail, userpwd )
		VALUES ( '$firstname', '$lastname', '$agencyname', '$usermail' ,'$agencycity' , '$agencycountry' , '$usermail', '$userpwd')";

		$conn->query($sql);		
		$returnmsg = "<div class='alert alert-success'><strong>Success!</strong>&nbsp;User added to the records &nbsp;<b> $usermail </b></div>";
	}else if( $row['usermail'] != "" ){
		$returnmsg = "<div class='alert alert-danger'><strong>Warning!</strong>&nbsp;User details already available in the records &nbsp;<b> $usermail </b></div>";
	}
	


$conn->close();

include 'adminpage.php';
?>