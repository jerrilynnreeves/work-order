<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  ua_admin.php
#	Version:  November 20, 2009
#	Purpose:  User Adminstration Functions

session_start();  //Start the session
ob_start();
include("functions.php");
include("ua_functions.php");
include("header.php");
$db = db_connect();

//If user is not logged in go to the login page
if (!isset($_SESSION['username']))
{
		$url = 'login.php';
		header("Location: $url"); 
		ob_end_flush();
		exit();
}
//If user is logged in 
else
{
	//Check to see if the user has the right permissions to be on this page
	$allowed_access = confirmUserRole($_SESSION['username'], 'ADMIN', $db);
	if ($allowed_access == 1)
	{
		getAdminFunctions();
		#############################################
		#           START ACTION HANDLING           #
		#############################################
		#*# ADD USER BUTTON #*#
		if(isset($_POST['add-user-submit']))
		{
			//if the form contains any empty fields
			if((empty($_POST['user-name'])) or (empty($_POST['user-email'])) or (empty($_POST['user-firstname'])) or (empty($_POST['user-lastname']))or (empty($_POST['user-password']))or (empty($_POST['user-role']))or (empty($_POST['user-status'])))
			{
				//Get the form again show missing data
				getAddUserForm($_POST['user-name'], $_POST['user-email'], $_POST['user-firstname'], $_POST['user-lastname'], $_POST['user-password'], $_POST['user-role'], $_POST['user-status'], $submit_status=-1, $db);
			}
			else
			{
				//Add the user
				addUser ($_POST['user-name'], $_POST['user-email'], $_POST['user-firstname'], $_POST['user-lastname'], $_POST['user-password'], $_POST['user-role'], $_POST['user-status'], $db);
			}
		}
		#*# SEND USER EMAIL BUTTON #*#
		elseif(isset($_POST['uemail-submit']))
		{
			//echo "<h2>send email</h2>";
			
			//load forms
			loadForms();
		}
		#*# SELECT USER FOR USER ROLE CHANGE #*#
		elseif(isset($_POST['ur-selectUser-submit']))
		{
			getUserRoleForm($_POST['user-select'], $submit_status=1, $db);
		}
		#*# DELETE USER ROLE LINK CLICKED #*#
		elseif(isset($_GET['d-ur']))
		{
			deleteUserRole($_GET['d-ur'], $db);
			getUserRoleForm($_GET['un'], $submit_status=3, $db);
		}
		#*# CHANGE USER ROLE STATUS LINK CLICKED #*#
		elseif(isset($_GET['c-ur']))
		{
			setRoleStatus($_GET['un'], $_GET['c-ur'], $db);
			getUserRoleForm($_GET['un'], $submit_status=4, $db);
		}
		#*# ADD USER ROLE CLICKED
		elseif(isset($_POST['ur-addRole-submit']))
		{
			addUserRole ($_POST['username'], $_POST['user-role'], $_POST['user-status'], $db);
			getUserRoleForm($_POST['username'], $submit_status=2, $db);
		}
		#*#	SELECT USER FOR PASSWORD RESET #*#
		elseif(isset($_POST['pwd-user-submit']))
		{
			getPasswordForm($_POST['user-select'], $submit_status=1, $db);
		}
		#*# CHANGE PASSWORD CLICKED #*#
		elseif(isset($_POST['change-pwd-submit']))
		{
			if(empty($_POST['pwd']))
			{
				getPasswordForm($_POST['username'], $submit_status=-1, $db);
			}
			else
			{
				$submit_status = updatePassword($_POST['username'], $_POST['pwd'], $db);
				getPasswordForm($_POST['username'], $submit_status, $db);
			}
		}
		#*# DELETED USER SELECTED #*#
		elseif(isset($_POST['delete-user-submit']))
		{
			getDeleteUserForm($_POST['user-select'], $submit_status=1, $db);
		}
		#*# CONFIRM DELETION CLICKED #*#
		elseif(isset($_POST['confirm-delete-submit']))
		{
			$submit_status = deleteUser($_POST['username'], $db);
			getDeleteUserForm($_POST['username'], $submit_status, $db);
		}
		#*# SELECT USER TO EDIT #*#
		elseif($_POST['edit-user-submit'])
		{
			//get data for the form and passit.
			$brg_username = $_POST['user-name'];
			$brg_email = $_POST['user-email'];
			$brg_firstname = $_POST['user-firstname'];
			$brg_lastname = $_POST['user-lastname'];
			
			updateUser($brg_username, $brg_email, $brg_firstname, $brg_lastname, $db);
			
			$brg_email = getUserValue ($brg_username, $parameter = 'brg_email', $db);
			$brg_firstname = getUserValue ($brg_username, $parameter = 'brg_firstname', $db);
			$brg_lastname = getUserValue($brg_username, $parameter = 'brg_lastname', $db);
			getEditUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $submit_status=1, $db);
		}
		#*# EDIT USER DATA #*#
		elseif($_POST['userEdit-submit'])
		{
			//get data for the form and passit.
			$brg_username = getUserValue ($_POST['user-select'], $parameter = 'brg_username', $db);
			$brg_email = getUserValue ($_POST['user-select'], $parameter = 'brg_email', $db);
			$brg_firstname = getUserValue ($_POST['user-select'], $parameter = 'brg_firstname', $db);
			$brg_lastname = getUserValue($_POST['user-select'], $parameter = 'brg_lastname', $db);
			
			getEditUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $submit_status=1, $db);
		}			
		#*# EDIT FORM CHANGE ROLE STATUS #*#
		elseif(isset($_GET['c-ed']))
		{
			setRoleStatus($_GET['un'], $_GET['c-ed'], $db);
			
			//get data for the form and pass it to the Edit User Form based on the username selected
			$brg_username = getUserValue ($_GET['un'], $parameter = 'brg_username', $db);
			$brg_email = getUserValue ($_GET['un'], $parameter = 'brg_email', $db);
			$brg_firstname = getUserValue ($_GET['un'], $parameter = 'brg_firstname', $db);
			$brg_lastname = getUserValue($_GET['un'], $parameter = 'brg_lastname', $db);
			
			getEditUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $submit_status=1, $db);
		}
		#*# EDIT FORM DELETE ROLE STATUS #*#
		elseif(isset($_GET['d-ed']))
		{
			deleteUserRole($_GET['d-ed'], $db);
			
			//get data for the form and pass it to the Edit User Form based on username choosen
			$brg_username = getUserValue ($_GET['un'], $parameter = 'brg_username', $db);
			$brg_email = getUserValue ($_GET['un'], $parameter = 'brg_email', $db);
			$brg_firstname = getUserValue ($_GET['un'], $parameter = 'brg_firstname', $db);
			$brg_lastname = getUserValue($_GET['un'], $parameter = 'brg_lastname', $db);
			
			getEditUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $submit_status=1, $db);	
		}
		else
		{
			loadForms($db);
		}
	}
	else
	{
		echo "<h2 class='error'>Access Denied</h2>";
	}
}
function loadForms($db)
{
	getAddUserForm('', '', '', '', '', '', '', $submit_status=0, $db);
	getPasswordForm('', $submit_status=0, $db);
	getEditUserForm('', '', '', '', $submit_status=0, $db);
	getUserRoleForm('', $submit_status=0, $db);
	getDeleteUserForm('', $submit_status=0, $db);
}
	
echo "<div style='height:200px;'></div>";
include("footer.html");
?>