<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  ua_admin.php
#	Version:  November 20, 2009
#	Purpose:  User Adminstration Functions

session_start();  //Start the session
ob_start();
include("functions.php");
include("forms.php");
include("header.php");
    
	echo "<div id='welcome'>";
	echo "<h1 style='text-align:center; color:#F20017;'>Website Coming Soon</h1>";
    echo "</div>";
	
echo "<div style='height:200px;'></div>";
include("footer.html");
?>