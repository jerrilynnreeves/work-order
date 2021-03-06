<?php
###################################################################
#               STATIC POPULATE FIELDS Functions                  #
###################################################################
function getWorkOrderPriority($wo_priority)
{
	echo "<p><label for='wo-priority' class='label'>Priority: </label>";
	echo "<select id='wo-priority' name='wo-priority' tabindex='5'>";
	echo "<option ".isSelected($wo_priority, 'Normal')." value='Normal'>Normal</option>";       
	echo "<option ".isSelected($wo_priority, 'Low')." value='Low'>Low</option>";
	echo "<option ".isSelected($wo_priority, 'High')." value='High'>High</option>";
	echo "<option ".isSelected($wo_priority, 'Emergancy')." value='Emergency'>Emergency</option>";
	echo "</select>";
}
function getWorkOrderStatus($wo_status)
{
	echo "<select id='wo-status' name='wo-status' tabindex='13
	'>";
	echo "<option ".isSelected($wo_status, 'Opened')." value='Opened'>Opened</option>";       
	echo "<option ".isSelected($wo_status, 'Pending')." value='Pending'>Pending</option>";
	echo "<option ".isSelected($wo_status, 'Closed')." value='Closed'>Closed</option>";
	echo "<option ".isSelected($wo_status, 'Re-opened')." value='Re-opened'>Re-opened</option>";
	echo "</select>";
}
function getOnCallTechs($oc_tech)
{
	echo "<select id='oc-tech' name='oc-tech' tabindex='1'>";
	echo "<option ".isSelected($oc_tech, 'michaels')." value='michaels'>Michael Schrader</option>";       
	echo "<option ".isSelected($oc_tech, 'thaon')." value='thaon'>Thao Ngo</option>";
	echo "</select>";
}
###################################################################
#                       WORK ORDER Forms                          #
###################################################################
function getWOFunctions()
{
	echo "<div id='app-functions'>";
	echo "<form id= 'workorder-functions' name='workorder-functions' action='workorders.php' method='post'>";
	echo "<p><input type='submit' name='app-submit-workorders' id='app-submit-workorders' class='button' value='Submit Workorder' tabindex='1' /></p>";
	//echo "<p><input type='submit' name='app-update-workorders' id='app-update-workorders' class='button' value='Update Workorder' tabindex='2' /></p>";
	echo "<p><input type='submit' name='app-opened-workorders' id='app-opened-workorders' class='button' value='Opened Workorders' tabindex='2' /></p>";
    echo "<p><input type='submit' name='app-closed-workorders' id='app-closed-workorders' class='button' value='Closed Workorders' tabindex='3' /></p>";
    echo "<p><input type='submit' name='app-oncall' id='app-oncall' class='button' value='On Call' tabindex='4' /></p>";
    echo "</form>";
	echo "</div>";
}
function editViewWorkoderForm($view, $wonumber, $database, $status_error)
{
	//View Readonly
	if ($view == 1)
	{
		$read = 'readonly';
	}
	//View Edit
	else
	{
		$read = '';
	}

	//Get Variables for the form
	echo "<div class='active-container' id='listWorkOrders'>";
	echo "<div id = 'workorder-edit-container'>";
	echo "<form id='workorder-edit-form' action='workorders.php' method='post'>";
    echo "<fieldset>";
    echo "<legend>&nbsp;General Information&nbsp;</legend>";
	echo "<input type='hidden' name='wo-number' id ='wo-number' value='".$wonumber."'>";
	echo "<p><label for='wo-date-requested' class='label'>Date Requested: </label>";
    echo "<input type='text' name='wo-date-requested' id='wo-date-requested' class='field-small' tabindex='1' value='".getWOValue($parameter = 'wo_daterequested', $wonumber, $database)."' readonly/>";
    echo "<input type='text' name='wo-current-status' id='wo-current-status' class='field-small-text' tabindex='2' value='".getWOValue($parameter = 'wo_currentstatus', $wonumber, $database)."' readonly/>";
    echo "</p>";
    echo "<p><label for='wo-store' class='label'>Store: </label>";
	$storenumber = getWOValue($parameter = 'wo_storenumber', $wonumber, $database);
    echo "<input type='text' name='wo-store' id='wo-storenum' class='field-small' tabindex='3' value='".$storenumber."' readonly/>";
    echo "<input type='text' name='wo-storename' id='wo-storename' class='field' tabindex='4' value='".getStoreName($storenumber, $database)."' readonly/>";
    echo "<input type='text' name='wo-wandnum' id='wo-wandnum' class='field-small' tabindex='5' value='".getWandNumber($storenumber, $database)."' readonly/>";
    echo "</p>";
    echo "<p><label for='wo-category' class='label'>Req/Sub By: </label>";
	echo "<input type='text' name='wo-requestedby' id='wo-requestedby' class='field' tabindex='6' value='".getWOValue($parameter = 'wo_requestedby', $wonumber, $database)."' readonly/>";
	$username = getWOValue($parameter = 'wo_submittedby', $wonumber, $database); 
	echo "<input type='text' name='wo-submittedby' id='wo-submittedby' class='field' tabindex='7' value='".getUserName($username, $database)."' readonly/>";
    echo "</p>";
    echo "</fieldset>";
    echo "<fieldset>";
    echo "<legend>&nbsp;Problem&nbsp;</legend>";
	//CATEGORY-EQUIPMENT
    echo "<p><label for='wo-category' class='label'>Category/Equip: </label>";
	echo "<input type='text' name='wo-category' id='wo-category' class='cat-eq' tabindex='8' value='".getWOValue($parameter = 'wo_category', $wonumber, $database)."' ".$read."/>";
	echo "<input type='text' name='wo-equipment' id='wo-equipment' class='cat-eq' tabindex='9' value='".getWOValue($parameter = 'wo_equipment', $wonumber, $database)."' ".$read."/>";
	if ($view == 0)
	{
		echo "<input type='submit' name='wo-ce-change' id='wo-ce-change' class='button' value='Change' tabindex='10' />";
	}
    echo "</p>";
    echo "<p><label for='wo-problem' class='label'>Problem: </label>";
    echo "<textarea id='wo-problem' name='wo-problem' rows='5' cols='20'";
	if ($view == 0)
	{
		echo " class ='addnote' ";
	}
	echo "tabindex='11' ".$read.">".getWOValue($parameter = 'wo_problem', $wonumber, $database)."</textarea>";
	if($view ==0)
	{
	echo "<input type='submit' name='update-problem' id='update-problem' class='button' style='margin-left: 3px; margin-bottom: 2px;' value='Update' tabindex='4' />";
	}
    echo "</p>";
    echo "<p><label for='wo-assignedto' class='label'>Assigned To: </label>";
	$a_username = getWOValue($parameter = 'wo_assignedto', $wonumber, $database); 
	if ($view == 0)
	{
		//Find out if the logged in user is allowed to update assigned to 
		$update_allowed = canChangeAssignedTo($_SESSION['username'], $database);
		//echo "<p>Update Allowed: ".$update_allowed."</p>";
		if ($update_allowed == 1)
		{
			getAssignNames ($wonumber, $a_username, $database);
			echo "<input type='submit' name='update-assignedto' id='update-assignedto' class='button' style='margin-left: 3px;' value='Update' tabindex='4' />";
		}
		else
		{
			echo "<input type='text' name='wo-assignedto' id='wo-assignedto' class='field' tabindex='12' value='".getUserName($a_username, $database)."' readonly/>";
		}	
		
		$wo_priority = getWOValue($parameter = 'wo_priority', $wonumber, $database);
		getWorkOrderPriority($wo_priority);
		echo "<input type='submit' name='update-priority' id='update-priority' class='button' style='margin-left: 3px;' value='Update' tabindex='4' />";
	}
	if($view == 1)
	{
		echo "<input type='text' name='wo-assignedto' id='wo-assignedto' class='field' tabindex='12' value='".getUserName($a_username, $database)."' ".$read."/>";
		echo "<input type='text' name='wo-priorty' id='wo-priority' class='field-small-text' tabindex='13' value='".getWOValue($parameter = 'wo_priority', $wonumber, $database)."' ".$read."/>";
    echo "</p>";
	}
    echo "</fieldset>";
    echo "<fieldset>";
    echo "<legend>&nbsp;Status Updates&nbsp;</legend>";
    //Get status updates for the work order FUNCTION
	workOrderStatus($wonumber, $database);
	echo "<p>";
    if ($view == 0)
	{
		echo "<p><label for='wo-status' class='label'>Update Status: </label>";
		$current_status = $wo_priority = getWOValue($parameter = 'wo_currentstatus', $wonumber, $database);
		getWorkOrderStatus($wo_status=$current_status, $database);
		echo "<textarea id='wo-statusAddNote' name='wo-statusAddNote' rows='5' cols='20' class='addnote' style='margin-left:125px;' tabindex='19'></textarea>";
		echo "<input type='submit' name='status-update-submit' id='status-update-submit' class='button' style='margin-left: 3px;' value='Update' tabindex='14' />";
		if($status_error == 1)
		{
			echo "<p style='color: red; font-weight:bold; margin-top:5px; margin-left:125px; font-size:small;'>Your status update was not applied because you did not enter a note.</p>";
		}
		elseif($status_error == 2)
		{
			echo "<p style='color:red; font-weight:bold; margin-top:5px; margin-left:125px;font-size:small;'>Your status update was not applied because the status selected is the work order's current status.</p>";
		}
		else
		{
			echo "<p style='color: red; font-weight:bold; margin-top:5px; margin-left:125px;font-size:small;'>If you are updating the status, you must also enter a note</p>";
		}
    }
	echo "</p>";
	echo "</fieldset>";
    echo "<fieldset>";
    echo "<legend>&nbsp;Work Order Notes&nbsp;</legend>";
    //GET work order notes FUNCTION
	getWONotes($wonumber, $database);
	if ($view == 0)
	{
		echo "<p><label for='wo-addNote' class='label'> </label>";
		echo "<textarea id='wo-addNote' name='wo-addNote' rows='5' cols='20' class='addnote' tabindex='20'></textarea>";
		echo "<input type='submit' name='status-addNote-submit' id='status-addNote-submit' class='button' style='margin-left: 3px;'value='Add Note' tabindex='4' />";
		echo "</p>";
	}
    echo "</fieldset>";
    echo "<input type='submit' name='wo-close-window' id='wo-close-window' class='button' style='margin:10px;float:right; ' value='Close' tabindex='4' />";
	echo "</form>";
    echo "</div>";
	echo "</div>";
}
function submitWorkorderForm($wo_storenumber, $wo_daterequested, $wo_category, $wo_equipment, $wo_problem, $wo_requestedby, $wo_priorty, $wo_submittedby, $submit_status, $database)
{
	if ($submit_status == -1)
	{
		echo "<div id='addWorkorderForm' class='hidden-container'>";
	}
	else
	{
		echo "<div id='addWorkorderForm' class='active-container'>";
	}	
	echo "<div id = 'workorder-new-container'>";
	//echo "<p class='error'>Submit Status: ".$submit_status."</p>";
	echo "<form id='workorder-new-form' action='workorders.php' method='post'>";
    echo "<fieldset>";
	echo "<legend>&nbsp;Location & Problem&nbsp;</legend>";
	echo "<p><label for='wo-date-requested' class='label'>Date Requested: </label>";
	echo "<input type='text' name='wo-date-requested' id='wo-date-requested' class='field' tabindex='1' value='".$wo_daterequested."' readonly/>";
	echo "</p>";
	//GET STORES FUNCTION
	getStoreList($_SESSION['username'], $selected_store=$wo_storenumber, $database);
    //GET CATEGORIES FUNCTION

	getCategoryDropDown($wo_category, $submit_status, $database);
	//If the user has choosen a category
	if($submit_status == 1 or $submit_status == 2 or $submit_status==3)
	{
		//GET EQUIPMENT FUNCTION
		getEquipmentDropDown($wo_category, $wo_equipment, $submit_status, $database);
        echo "</p>";
     }
	echo "</fieldset>";
    if ($submit_status == 2 or $submit_status == 3 or $submit_status == 4 or $submit_status == 5)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Problem Details&nbsp;</legend>";
		echo "<p><label for='wo-requestedby' class='label'".formatMissing($submit_status, $wo_requestedby).">Requested By: </label>";
		echo "<input type='text' name='wo-requestedby' id='wo-requestedby' class='field' ".formatMissingWO($submit_status, $wo_requestedby)." tabindex='2' value='".$wo_requestedby."'/></p>";
		getWorkOrderPriority($wo_priority, $database);
		echo "<textarea id='wo-problem' name='wo-problem' rows='5' cols='20'".formatMissingWO($submit_status, $wo_problem).">".$wo_problem."</textarea>";
		echo "</fieldset>";
		echo "<p style='text-align:center'>";
		if ($submit_status == 5)
		{
			echo "<p class='error'>You must select a category and equipment type before you can continue.  Please choose a category</p>";
		}
		else
		{
			echo "<input type='submit' name='wo-reset-submit' id='wo-reset-submit' class='button' value='Reset' tabindex='1' />";
			echo "<input type='submit' name='wo-request-submit' id='wo-request-submit' class='button' value='Submit' tabindex='2' />";
			echo "<input type='submit' name='wo-cancel-submit' id='wo-cancel-submit' class='button' value='Cancel' tabindex='4' />";
			echo "</p>";
			
		}
	}
	echo "</form>";
    echo "</div>";
}
function getOnCall($database)
{
	echo "<div class='active-container' id='listWorkOrders'>";
	echo "<div id = 'workorder-edit-container'>";
	echo "<form id='workorder-edit-form' action='workorders.php' method='post'>";
    echo "<fieldset>";
	echo "<legend>On Call</legend>";
	echo "<p>If you are experiencing an emergency you may contact this  week's on-call maintenance technician.  If you receive his or her voice mail, please leave a message.  If you have not heard back from the maintenance technician within an hour, you may contact the maintenance supervisor, Jason McCammon at 828-691-8202.</p>";
	echo "<h2 style='text-align: center'><strong>This Week's On-Call Technician</strong></h2>" ;
	echo "<h2 style='text-align: center'><strong>Thao Ngo | 828-775-5098</strong></h2>" ;
	echo "</fieldset>";
	echo "</form>";
	echo "</div>";
}
?>