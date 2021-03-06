<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  login.php
#	Version:  November 20, 2009
#	Purpose:  Logon and Logout functions for BRG web-tools

session_start();  //Start the session
ob_start();
include("forms.php");
include("functions.php");
include("header.html");

//Check to see if the user is logged in
if (!isset($_SESSION['user_id']))
{
	echo "<p>Logged into the dashboard</p>";
	echo "<p>View latest active work orders</p>";
	echo "<p>View Store's active work orders</p>";
	echo "<p>View BRG News</p>";
}
//If user is not logged-in redirect to the login page
else
{
		$url = 'login.php';
		header("Location: $url"); 
		ob_end_flush();
		exit();
}
include("footer.html");
?>