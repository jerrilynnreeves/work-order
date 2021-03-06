<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";

if (isset($_SESSION['username']))
{
	echo "<title>Brumit Restaurant Group | Webtools </title>";
}
else
{
	echo "<title>Arby's | Brumit Restaurant Group</title>";
}
echo "<link href=\"brg.css\" rel=\"stylesheet\" type=\"text/css\" />";
echo "<script type=\"text/javascript\" src=\"jquery.js\"></script>";
echo "<script type=\"text/javascript\" src=\"brg.js\"></script>";
echo "</head>";

echo "<body>";
echo "<div id='page-wrapper'>";
echo "<div id='content-wrapper'>";
echo "<div id='header'><div id='brg-logo'></div>";
echo "<div id='top-nav'>";
echo "<ul>";
echo "<li><a href='index.php'>Home</a></li>";
//echo "<li><a href='#'>Menu</a></li>";
//echo "<li><a href='#'>Nutrition</a></li>";
//echo "<li><a href='#'>Store Finder</a></li>";
//echo "<li><a href='#'>About BRG</a></li>";
if (isset($_SESSION['username']))
{
	echo "<li><a href='workorders.php'>Work Orders</a></li>";
	echo "<li><a href='login.php?logout=1'>Logout</a></li>";
}
else
{
	echo "<li><a href='login.php'>Login</a></li>";
}
echo "</ul>";
echo "</div>";
echo "<div id='second-nav'>";
echo "<ul>";
if (isset($_SESSION['username']))
{
	//echo "<li><a href='dashboard.php'>Dashboard</a></li>";   
	//echo "<li><a href='workorders.php'>Work Orders</a></li>";
	//echo "<li><a href='ua_admin.php'>User Admin</a></li>";
	//echo "<li><a href='db_admin.php'>Database Admin</a></li>";
}
echo "</ul>";
echo "</div>";
echo "</div>";
echo "<div id='content'>";
?>