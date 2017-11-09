<?php
include('./dbconnect.php');
include('./appconfig.php');

session_start(); // Starting Session
$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
if (empty($_POST['username']) || empty($_POST['password'])) {
$error = "Username or Password is invalid";
}
else
{
// Define $username and $password
$username=$_POST['username'];
$password=$_POST['password'];

/* check connection */
if ($conn->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// To protect MySQL injection for Security purpose
$username = stripslashes($username);
$password = stripslashes($password);
$username = mysqli_real_escape_string($conn, $username);
$password = mysqli_real_escape_string($conn, $password);

// SQL query to fetch information of registerd users and finds user match.
$query = "SELECT * from $con_login where userpwd='$password' AND username='$username'";
$result = $conn->query($query);

if ($result->num_rows == 1) {
$_SESSION['login_user']= $username; // Initializing Session
header("location: profile.php"); // Redirecting To Other Page
echo "success";
} else {
$error = "Username or Password is invalid";
}
mysqli_close($conn); // Closing Connection
}
}
?>