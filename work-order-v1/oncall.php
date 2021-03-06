<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  workorders.php
#	Version:  Jan 14, 2010
#	Purpose:  Work Order Action Handling

session_start();  //Start the session
ob_start();
include("functions.php");
include("forms.php");
include("header.php");
$database = db_connect();

//Query database for current on call tech and last time changed

	$qry_currentoncall = "SELECT wo_oncallid, wo_weekchanged FROM tbl_oncall";
	$run_qry = @mysqli_query ($database, $qry_currentoncall);
	$current_row = mysqli_fetch_array($run_qry);
	
	$current_tech = $current_row[0];
	$week_changed = $current_row[1];
	
	$today_day = date('D');
	$today_hour = date('g');
	$current_week = strftime('%U');
	
	if (($today_day == 'Tue') AND ($today_hour >=7) AND ($week_changed < $current_week))
	{
		//Change the usersname
		if ($current_tech == 'michaels')
		{
			$tech = 'thaon';
		}
		else
		{
			$tech = 'michaels';
		}
		//Update the database with the new tech's name
		$qry_updateoncall = "UPDATE tbl_oncall SET wo_oncallid = '$tech', wo_weekchanged = $current_week";
		$run_updateoncall = @mysqli_query ($database, $qry_updateoncall);
	}
	else
	{
		$tech =	$current_tech; 
	}
	
	return $tech;
	
	//echo "<p>PHP Day ".date('D')."</p>";
	//echo "<p>PHP Hour ".date('g')."</p>";
	//echo "<p>PHP Week ".$current_week."</p>";
	//echo "<p>Tech ".$tech."</p>";


echo "<div style='height:200px;'></div>";
include("footer.html");
mysqli_close($database);
?>
?>