<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  login.php
#	Version:  November 20, 2009
#	Purpose:  Logon and Logout functions for BRG web-tools

session_start();  //Start the session
ob_start();
include("forms.php");
include("functions.php");
include("header.php");
$database = db_connect();
//If URL requested is not login.php
//Store reqested url in Session variable
//
$workorderrequested = 0;
$requesting_url = $_SESSION['page-requested'];
//echo "<p>Request: ".$_SESSION['page-requested']."</p>";
if (strrpos($requesting_url, '?email=') >0)
{
	$workorderrequested = 1;
}

if (isset($_POST['logon-submit']))
{
	$validationPass = validateLogon($_POST['login-name'], $_POST['login-password'], $database);
	if ($validationPass == 1)
	{
		if ($workorderrequested == 1)
		{
			$url = $requesting_url;
			header("Location: $url"); 
		}
		else
		{
			$url = 'workorders.php';
			header("Location: $url"); 
		}	
		ob_end_flush();
		exit();
	}
	else
	{
		echo "<h2 style='color:red;text-align:center;'>Your login failed</h2>";
		getLoginForm();
	}
}
elseif ($_GET['logout'])
{
	$url = 'login.php';
	header("Location: $url"); 
	session_unset();
	session_destroy();
	ob_end_flush();
	exit();
}
else
{
	getLoginForm();
	
}
include("footer.html");
mysqli_close($database);
?>