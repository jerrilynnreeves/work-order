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

//If user is not logged in go to the login page
if (!isset($_SESSION['username']))
{
		$_SESSION['page-requested'] = $_SERVER["REQUEST_URI"];
		$url = 'login.php';
		header("Location: $url"); 
		ob_end_flush();
		exit();
}
//If user is logged in 
else
{
	//Check to see if the user has the right permissions to be on this page
	$admin = confirmUserRole($_SESSION['username'], 'ADMIN', $database);
	$dm = confirmUserRole($_SESSION['username'], 'DM', $database);
	$gm = confirmUserRole($_SESSION['username'], 'GM', $database);
	$tech = confirmUserRole($_SESSION['username'], 'TECH', $database);
	$emp = confirmUserRole($_SESSION['username'], 'EMP', $database);
	
	if (($admin == 1) OR ($dm == 1) OR ($gm == 1) OR ($tech == 1) OR ($emp == 1))
	{
		$allowed_access = 1;
	}
	else
	{
		$allowed_access = 0;
	}
	if ($allowed_access == 1)
	{
		getWOFunctions();
		#############################################
		#           START ACTION HANDLING			#
		#           APP-BUTTON FUNCTIONS            #
		#############################################
		if(isset($_POST['app-submit-workorders']))
		{
			$wo_daterequested = getCurrentDate();
			submitWorkorderForm('', $wo_daterequested, '', '', '', '', '', '', $submit_status=0, $database);			
		}
		elseif (isset($_POST['app-oncall']))
		{
			getOnCallForm($database);
		}
		elseif(isset($_POST['wo-reset-submit']))
		{
			$wo_daterequested = getCurrentDate();
			submitWorkorderForm('', $wo_daterequested, '', '', '', '', '', '', $submit_status=0, $database);			
		}	
		elseif(isset($_POST['app-update-workorders']))
		{
			echo "<p class='error'>Show a list of workordrs to update</p>";	
		}
		elseif(isset($_POST['app-opened-workorders']))
		{
			getWorkorderList($sort='date', $order='DESC', $view='ACTIVE', $_SESSION['username'], $query_all=0, $database);
		}		
		elseif(isset($_POST['app-closed-workorders']))
		{
			getWorkorderList($sort='date', $order='DESC', $view='CLOSED', $_SESSION['username'], $query_all=0, $database);
		}
		//View all active wo-viewall-".$button.
		elseif(isset($_POST['wo-viewall-active']))
		{
			getWorkorderList($sort='date', $order='DESC', $_POST['wo-view'], $_SESSION['username'], $query_all=1, $database);
		}
		elseif(isset($_POST['wo-viewall-closed']))
		{
			getWorkorderList($sort='date', $order='DESC', $_POST['wo-view'], $_SESSION['username'], $query_all=1, $database);
		}
		#############################################
		#           START ACTION HANDLING			#
		#            SUBMIT WORK ORDER              #
		#############################################
		#*# CATEGORY SELECT BUTTON CLICKED #*#
		elseif(isset($_POST['wo-category-submit']))
		{
			//Dont let the user select the blank category selection
			if(is_numeric($_POST['wo-category']))
			{
				if (isset($_POST['wo-problem']) or isset($_POST['wo-requestedby']) or isset($_POST['wo-priorty']))
				{
					setWorkorderForm('', 5, $database);
				}
				else
				{
					echo "<p class='error'>You must select a category before you can continue</p>";
					setWorkorderForm('', 0, $databasee);
				}
			}
			else
			{
				if (isset($_POST['wo-problem']) or isset($_POST['wo-requestedby']) or isset($_POST['wo-priorty']))
				{
					setWorkorderForm('', 3, $database);
				}
				else
				{
					setWorkorderForm('', 1, $database);
				}
			}
		}
		#*# CATEGORY RESET BUTTON CLICKED #*#
		elseif(isset($_POST['wo-category-reset']))
		{
			if (isset($_POST['wo-problem']) or isset($_POST['wo-requestedby']) or isset($_POST['wo-priorty']))
			{
				setWorkorderForm('',0, $database);
			}
			else
			{
				setWorkorderForm('', 0, $database);
			}
		}
		#*# EQUIPMENT SELECT BUTTON CLICKED #*#
		elseif(isset($_POST['wo-equipment-submit']))
		{
			setWorkorderForm('', 2, $database);
		}
		#*# EQUIPMENT RESET BUTTON CLICKED #*#
		elseif(isset($_POST['wo-equipment-reset']))
		{
			setWorkorderForm ('', 3, $database);
		}
		#*# SUBMIT WORK ORDER BUTTON CLICKED
		elseif(isset($_POST['wo-request-submit']))
		{
			if (empty($_POST['wo-priority']) or empty($_POST['wo-requestedby']) or empty($_POST['wo-problem']))
			{
				echo "<p class='error'>Please fill out the fields in highlighted in red</p>";
				setWorkorderForm('', 2, $database);
			}
			else
			{
				submitWorkOrder($_POST['wo-store'], $_POST['wo-date-requested'], $_POST['wo-category'], $_POST['wo-equipment'], $_POST['wo-problem'], $_POST['wo-priority'], $_POST['wo-requestedby'], $_SESSION['username'], $database);
			}
		}
		#############################################
		#           START ACTION HANDLING			#
		#           VIEW/EDIT WORK ORDER            #
		#############################################
		elseif(isset($_GET['view']))
		{
			editViewWorkoderForm($view=1, $wonumber=$_GET['view'], $database, $status_error=0);
		}
		elseif(isset($_GET['edit']))
		{
			editViewWorkoderForm($view=0, $wonumber=$_GET['edit'], $database, $status_error=0);
		}
		elseif(isset($_POST['status-update-submit']))
		{
			if (empty($_POST['wo-statusAddNote']))
			{
				editViewWorkoderForm($view=0, $wonumber=$_POST['wo-number'], $database, $status_error=1);
			}
			else
			{
				$status_error = checkCurrentStatus ($_POST['wo-number'], $_POST['wo-status'], $database);
				if ($status_error == 2)
				{
					editViewWorkoderForm($view=0, $wonumber=$_POST['wo-number'], $database, $status_error);
				}
				else
				{
					$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
					updateStatus($_POST['wo-number'], $_SESSION['username'], $_POST['wo-status'], $_POST['wo-statusAddNote'], $database);
					header("Location: $url");
				}
			}
		}
		elseif(isset($_POST['status-addNote-submit']))
		{

			$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
			addNote($_POST['wo-number'], $_SESSION['username'], $_POST['wo-addNote'], $database);
			header("Location: $url");
		}
		elseif(isset($_POST['wo-ce-change']))
		{
			//If edit to category/equipment request show that edit form
			changeCatEquip($_POST['wo-category'], $_POST['wo-equipment'], $_POST['wo-number'], $submit_status=0, $database);
		}
		elseif(isset($_POST['wo-ce-reset']))
		{
			//If reset category was picked
			changeCatEquip($_POST['wo-category'], $currentEquipment='', $_POST['wo-number'], $submit_status=1, $database);
		}
		elseif(isset($_POST['wo-ce-resetSubmit']))
		{
			//If reset category was picked
			changeCatEquip($_POST['wo-category'], $currentEquipment='', $_POST['wo-number'], $submit_status=0, $database);
		}
		elseif(isset($_POST['wo-ce-update']))
		{
			//If Update was clicked
			$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
			updateCatEquip($_POST['wo-number'], $_POST['wo-category'], $_POST['wo-equipment'], $database);
			header("Location: $url");
		}
		elseif(isset($_POST['update-problem']))
		{
			//If Update was clicked
			$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
			updateProblem($_POST['wo-number'], $_POST['wo-problem'], $database);
			header("Location: $url");
		}
		elseif(isset($_POST['update-priority']))
		{
			//If Update was clicked
			$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
			updatePriority($_POST['wo-number'], $_POST['wo-priority'], $database);
			header("Location: $url");
		}
		elseif(isset($_POST['update-assignedto']))
		{
			//If update assigned to is clicked
			$url = $_SERVER["REQUEST_URI"]."?edit=".$_POST['wo-number'];
			updateAssignedTo($_POST['wo-number'], $_POST['user-assign'], $database);
			header("Location: $url");
		}
		#############################################
		#           START ACTION HANDLING			#
		#            LIST SORT FUNCTIONS            #
		#############################################
		elseif(isset($_GET['sort']))
		{
			$sort = $_GET['sort'];
			$order = $_GET['order'];
			$view = $_GET['v'];
			$query_all = $_GET['qa'];
			getWorkorderList($sort, $order, $view, $_SESSION['username'], $query_all, $database);
		}
		elseif(isset($_POST['wo-close-window']))
		{
			echo "<script type=\"text/javascript\">window.close();</script>";
		}
		#############################################
		#           START ACTION HANDLING			#
		#               EMAIL SENT URL              #
		#############################################
		elseif(isset($_GET['email']))
		{
			editViewWorkoderForm($view=0, $wonumber=$_GET['email'], $database, $status_error=0);
		}
		#############################################
		#           START ACTION HANDLING			#
		#           MANUAL ON CALL CHANGE           #
		#############################################
		elseif(isset($_POST['co-change-current']))
		{
			changeOnCallTech($_POST['oc-tech'], $database);
			getOnCallForm($database);
		}		
		else
		{
			getWorkorderList($sort='date', $order='DESC', $view='ACTIVE', $_SESSION['username'], $query_all=0, $database);
		}
	}
	else
	{
		echo "<h2 class='error'>Access Denied</h2>";
	}
}

function setWorkOrderForm($wo_submittedby, $submit_status, $database)
{
	submitWorkorderForm($_POST['wo-store'], $_POST['wo-date-requested'], $_POST['wo-category'], $_POST['wo-equipment'], $_POST['wo-problem'], $_POST['wo-requestedby'], $_POST['priority'], $wo_submittedby, $submit_status, $database);
}
	
echo "<div style='height:200px;'></div>";
include("footer.html");
mysqli_close($database);
?>