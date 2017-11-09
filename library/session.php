<?php
// Establishing Connection with Server by passing server_name, user_id and password as a parameter

include('dbconnect.php');

session_start();// Starting Session
// Storing Session
$user_check=$_SESSION['login_user'];
// SQL Query To Fetch Complete Information Of User
$query = "select username, firstname, lastname, usermail, userrole from con_login where username='$user_check'";
$ses_sql=$conn->query($query);
$row = $ses_sql->fetch_assoc();
$login_session = $row['username'];
$login_firstname = $row['firstname'];
$login_lastname = $row['lastname'];
$login_userrole = $row['userrole'];
$_SESSION["project_id"] = "";
$_SESSION["project_name"] = "";

if( $login_userrole == "ADM"){
		$roleflag = "1";
	}else{
		$roleflag  = "0";
}	

//====User details for audit trail ====
	$mydate=getdate(date("U"));
	$updatedby = ucwords(strtolower($login_firstname)) .' '. ucwords(strtolower($login_lastname)); 

//=======================================
	
if(!isset($login_session)){
header('Location: ../index.php'); // Redirecting To Home Page
}


include('../appconfig.php');

$numBaseLineNumber = "0";

$_SESSION['cacCont'] = strtoupper( substr( md5(uniqid(rand(), true)), 0, 12 ) );
$cacCont=session_id();


//Checks access to projects
	if(isset($_REQUEST['pid']) ){
		$pid = $_REQUEST['pid'];
		$sql = "SELECT * FROM $project_master WHERE proj_manager='$login_session' AND project_id='$pid'"; 
		$conn->query($sql) or die('Error, task query failed');
		$proj_list = $conn->query($sql);
	
		if ($proj_list->num_rows == 0){
			header("location: ./pbsHome.php");
		}
	}
//====================


?>


