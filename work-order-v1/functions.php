<?php
function db_connect()
{
	require ('./includes/db_functions.php');
	return $dbc;
}

//Mails email content 
function mail_it($content, $subject, $recipient, $email_type) 
{  
	if ($email_type == 1)
	{
		$cc = 'Cc: jasonm@ncarbys.com'. "\r\n";
	}
	
   $headers = 'From: administrator@ncarbys.com' . "\r\n" .
    'Reply-To: administrator@ncarbys.com' . "\r\n" .$cc.
    'X-Mailer: PHP/' . phpversion();
   $message .= $content."\n\n";
   
   mail($recipient, $subject, $message, $headers);
}
###################################################################
#                       FORMATTING Functions                      #
###################################################################
//Display the value selected in a form submitted 
function isSelected($picked, $totest)
{
	if ($picked == $totest)
	{
		return "selected=' '";
	}
}
//Label of a field that is required but is missing data
function formatMissing($submit, $value)
{
	//if the form has never been submitted display regular text
	if ($submit == 0)
	{
		return "";
	}
	//if the form submit has been thrown a negative 1, it indicates an additional validation requirement failed
	elseif($submit == -1)
	{
		return "style='color: #F20017'";
	}
	else
	{
		if (empty($value))
		{
			return "style='color: #F20017'";
		}
		else
		{
			return "";
		}
	}
}
function formatMissingWO($submit, $value)
{
	//if the form has never been submitted display regular text
	if ($submit == 0 or $submit==1)
	{
		return "";
	}
	//if the form submit has been thrown a negative 1, it indicates an additional validation requirement failed
	else
	{
		if (empty($value))
		{
			return "style='border:1px solid #F20017;'";
		}
		else
		{
			return "";
		}
	}
}
###################################################################
#                    WORK ORDER DATA FUNCTIONS                    #
###################################################################
function getWOValue($parameter, $wo_number, $database)
{	
	if ($database)
	{
		//query username and roles from role table
		if ($parameter == 'wo_daterequested')
		{
			$qry_findvalue = "SELECT DATE_FORMAT(tbl_workorder.wo_daterequested, '%c/%e/%Y') FROM tbl_workorder WHERE wo_number=$wo_number";
		}
		else
		{
			$qry_findvalue = "SELECT $parameter FROM tbl_workorder WHERE wo_number=$wo_number";
		}
		$run = @mysqli_query ($database, $qry_findvalue);

		//echo "<p>".$qry_findvalue."</p>";
		if($run)
		{
			while ($findvalue_row = mysqli_fetch_array($run))
			{
				return $findvalue_row[0];
			}
		}
	}
}
function getCurrentDate()
{
	$date = date('m/d/Y h:i A');
	return $date;
}
function getStoreName($storenumber, $database)
{
	//$database = db_connect();
	if ($database)
	{
		$qry_storename = "SELECT brg_storename FROM tbl_stores WHERE brg_storenum = $storenumber";
		$run_storename = @mysqli_query ($database, $qry_storename);
		
			while ($storename_row = mysqli_fetch_array($run_storename))
			{
				return $storename_row[0];
			}
	}
}
function getWandNumber($storenumber, $database)
{
	//$database = db_connect();
	if ($database)
	{
		$qry_wandnumber = "SELECT brg_wandnum FROM tbl_stores WHERE brg_storenum = $storenumber";
		$run_wandnumber = @mysqli_query ($database, $qry_wandnumber);
		
			while ($wandnumber_row = mysqli_fetch_array($run_wandnumber))
			{
				return $wandnumber_row[0];
			}
	}
}
function getStoreList($username, $selected_store, $database)
{	
	if ($database)
	{
		//query username and roles from role table
		$qry_findrole = "SELECT brg_userid, brg_role FROM tbl_userassign WHERE brg_userid='$username'";
		$run = @mysqli_query ($database, $qry_findrole);

		if($run)
		{
			while ($userrole_row = mysqli_fetch_array($run))
			{
				if ($userrole_row[1] == 'DM')
				{
					$qry_stores = "SELECT brg_storenum, brg_wandnum, brg_storename FROM tbl_stores WHERE brg_storedm = '$username' ORDER BY brg_storenum"; 
					getStoreDropDown($qry_stores, $database, $selected_store);
					break;
				}
				elseif ($userrole_row[1] == 'GM')
				{
					$qry_stores = "SELECT brg_storenum, brg_wandnum, brg_storename FROM tbl_stores WHERE brg_storegm = '$username' ORDER BY brg_storenum";
					getStoreDropDown($qry_stores, $database, $selected_store);
					break;
				}
				else
				{
					$qry_stores = "SELECT brg_storenum, brg_wandnum, brg_storename FROM tbl_stores ORDER BY brg_storenum";
					getStoreDropDown($qry_stores, $database, $selected_store);
					break;
				}
			}
			
		}
		else
		{
			$qry_stores = "SELECT brg_storenum, brg_wandnum, brg_storename FROM tbl_stores ORDER BY brg_storenum";
			getStoreDropDown($qry_stores, $database, $selected_store);
		}
	}
	else
	{
		$qry_stores = "SELECT brg_storenum, brg_wandnum, brg_storename FROM tbl_stores ORDER BY brg_storenum";
		getStoreDropDown($qry_stores, $database, $selected_store);
	}	
}
function updateCatEquip($wonumber, $category, $equipment, $database)
{
	if ($database)
	{
		//set-up update query
		$qry_updateCE = "UPDATE tbl_workorder SET wo_category = '$category', wo_equipment = '$equipment' WHERE wo_number='$wonumber'";
		//echo "<p>".$qry_updateCE."</p>";
		$run_updateCatEquip = @mysqli_query ($database, $qry_updateCE);
	}
}
function submitWorkOrder($wo_storenumber, $wo_daterequested, $wo_category, $wo_equipment, $wo_problem, $wo_priority, $wo_requestedby, $wo_submittedby, $database)
{
	//open database
	$database = db_connect();
	if ($database)
	{
		//Get the DM and GM and TECH for the Work order
		$qry_mgrs = "SELECT brg_storedm, brg_storegm, brg_tech FROM tbl_stores WHERE brg_storenum = $wo_storenumber"; 
		$run_mgrs = @mysqli_query ($database, $qry_mgrs);
			
			while ($mgrs_row = mysqli_fetch_array($run_mgrs))
			{
				$brg_dm = $mgrs_row[0];
				$brg_gm = $mgrs_row[1];
				$brg_tech = $mgrs_row[2];
			}
		
		//Get GM full name (does not store GM username instead stores full name)
		$qry_gmname = "SELECT brg_firstname, brg_lastname FROM tbl_users WHERE brg_username = '$brg_gm'";
		$run_gmname = @mysqli_query ($database, $qry_gmname);
			
			while ($gmname_row = mysqli_fetch_array($run_gmname))
			{
				$brg_gmname = $gmname_row[0]." ".$gmname_row[1];
			}		
		
		//Get Cat/Equip Routing Type
		$qry_routing = "SELECT wo_routing FROM tbl_woequipment WHERE wo_category ='$wo_category' AND wo_equipment = '$wo_equipment'";
		$run_routing = @mysqli_query ($database, $qry_routing);
			
			while ($routing_row = mysqli_fetch_array($run_routing))
			{
				$brg_route = $routing_row[0];
				############################
				#        ASSIGN TECH	   #
				############################
				if ($brg_route == 'DM')
				{
					$wo_assignedto = $brg_dm;
				}
				elseif ($brg_route == 'IT')
				{
					$wo_assignedto = 'jerrir';
				}
				elseif($brg_route == 'MSUP')
				{
					$wo_assignedto = 'jasonm';
				}
				elseif($brg_route == 'TECH')
				{
					$wo_assignedto = $brg_tech;
				}
				else
				{
					$wo_assignedto = $brg_route;
				}
			}

			//Format Requested by name
			$wo_requestedby = ucwords($wo_requestedby);
			
		//Add information to the database
		$wo_problem = addslashes($wo_problem);
		$wo_requestedby = addslashes($wo_requestedby);
		
		$qry_addWorkorder = "INSERT INTO tbl_workorder (wo_number, wo_storenumber, wo_dm, wo_gm, wo_daterequested, wo_category, wo_equipment, wo_problem, wo_priority, wo_requestedby, wo_submittedby, wo_assignedto, wo_currentstatus) VALUES (NULL, '$wo_storenumber', '$brg_dm', '$brg_gmname', NOW(), '$wo_category', '$wo_equipment', '$wo_problem', '$wo_priority', '$wo_requestedby', '$wo_submittedby', '$wo_assignedto', 'Opened')";
		$add_workorder = @mysqli_query ($database, $qry_addWorkorder);
		
		//Get WO number
		$wo_number = mysqli_insert_id($database);
		
		//Add WorkOrder Status
		$qry_addStatus = "INSERT INTO tbl_wostatus (wo_status, wo_changedate, wo_updatedby, wo_workordernum) VALUES ('Opened', NOW(), '$wo_submittedby', $wo_number)";
		$add_status = @mysqli_query ($database, $qry_addStatus);
		
		##################################
		#        EMAIL WORKORDER         #
		##################################
		
		$recipient = '';
		
		//See if assignedto is an email address
		if (substr_count($wo_assignedto, '@') >0)
		{
			$recipient = $wo_assignedto;
		}
		else
		{
			//Query Assigned to email
			$qry_assignedto = "SELECT brg_email FROM tbl_users WHERE brg_username = '$wo_assignedto'";
			$run_assignedto = @mysqli_query ($database, $qry_assignedto);
		
				while ($assignedto_row = mysqli_fetch_array($run_assignedto))
				{
					$recipient = $assignedto_row[0];
				}		
		}	
		//Query DM email address
		$qry_dmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_dm'";
		$run_dmemail = @mysqli_query ($database, $qry_dmemail);
		
			while ($dm_row = mysqli_fetch_array($run_dmemail))
			{
				if ($dm_row[0]!=$assignedto_row[0])
				{
					$recipient = $recipient.", ".$dm_row[0];
				}
			}
		
		//Query GM email address
		$qry_gmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_gm'";
		$run_gmemail = @mysqli_query ($database, $qry_gmemail);
		
			while ($gm_row = mysqli_fetch_array($run_gmemail))
			{
				if (($gm_row[0]!= $assignedto_row[0]) or ($gm_row[0]!= $dm_row[0]))
				{
					$recipient = $recipient.", ".$gm_row[0];
				}
			}		
		
		//Create Subject and content of the Message
			//Subject:  Site | Category/Equip | Priorty
			$subject = $wo_storenumber." | ".$wo_category."/".$wo_equipment." | ".$wo_priority;
		//Body: Problem | Requested By | Link to Workorder
			$content = "Problem: ".$wo_problem."\r\n"."Requested By: ".$wo_requestedby."\r\n"."Link: http://www.ncarbys.com/workorders.php?email=".$wo_number."\r\n";

			$emailed_to = $recipient;
			//call the mail function
			mail_it($content, $subject, $recipient, $email_type = 1); 
			
		//Return to List View
		echo "<div id='addWorkorderForm' class='active-container'>";
		echo "<div id = 'workorder-new-container'>";
			echo "<form id='workorder-new-form' action='workorders.php' method='post'>";
			echo "<fieldset>";
			echo "<legend>&nbsp;Added Successfully&nbsp;</legend>";
				echo "<p style='color:green; margin-right:10px;'>Sucessfully Added Workorder #".$wo_number."";
				echo "<input type='submit' name='wo-close-submit' id='wo-close-submit' class='button' style='margin-left:10px;' value='Close' tabindex='1' /></p>";
				echo "<p style='color:green'><strong>Email Sent</strong></p>";
				echo "<p><strong>Recipients: </strong>".$emailed_to."</p>";
				echo "<p><strong>Subject: </strong>".$subject."</p>";
				echo "<p><strong>Message: </strong>".$content."</p>";
			echo "</form>";
		echo "</div>";
		echo "</div>";
	}
}
function getWorkListQuery($sort, $order, $view, $user, $query_all, $database)
{
	//Get Database field name for Sort
	if($sort == 'date')
	{
		$sort = 'tbl_workorder.wo_daterequested';
	}
	elseif($sort == 'number')
	{
		$sort = 'tbl_workorder.wo_number';
	}
	elseif($sort == 'store')
	{
		$sort = 'tbl_workorder.wo_storenumber';
	}
	elseif($sort == 'category')
	{
		$sort = 'tbl_workorder.wo_category';
	}
	elseif($sort == 'equipment')
	{
		$sort = 'tbl_workorder.wo_equipment';
	}
	elseif($sort == 'assignedto')
	{
		$sort = 'tbl_workorder.wo_assignedto';
	}
	else
	{
		$sort = 'tbl_workorder.wo_daterequested';
	}
	
	//Get status filter
	if ($view == 'ACTIVE')
	{
		$view_filter = "AND (tbl_workorder.wo_currentstatus = 'Opened'  OR tbl_workorder.wo_currentstatus = 'Pending' OR tbl_workorder.wo_currentstatus = 'Re-opened')"; 
		//OR tbl_workorder.wo_currentstatus = 'Reopened'";
	}
	elseif ($view == 'CLOSED')
	{
		$view_filter = "AND tbl_workorder.wo_currentstatus = 'Closed'";
	}
	else
	{
		$view_filter ="";
	}
	
	//By USERNAME & TYPE
	if ($query_all == 0)
	{
		$qry_findrole = "SELECT brg_userid, brg_role FROM tbl_userassign WHERE brg_userid='$user'";
		$run_findrole = @mysqli_query ($database, $qry_findrole);
			while ($userrole_row = mysqli_fetch_array($run_findrole))
			{
				if (($userrole_row[1] == 'DM') or ($userrole_row[1] == 'GM'))
				{
					//Query List of Store numbers
					if ($userrole_row[1] == 'DM')
					{
						$qry_field = 'brg_storedm';
					}
					elseif($userrole_row[1] == 'GM')
					{
						$qry_field = 'brg_storegm';
					}

					$qry_stores = "SELECT brg_storenum FROM tbl_stores WHERE ".$qry_field ."= '$user'";
					$run_stores =  @mysqli_query ($database, $qry_stores);
					$user_filter = "AND (";
					$count = 0;
					while ($store_row = mysqli_fetch_array($run_stores))
					{
						if ($count > 0)
						{
						
							$user_filter = $user_filter." OR tbl_workorder.wo_storenumber = ".$store_row[0];
						}
						else
						{
							$user_filter = $user_filter." tbl_workorder.wo_storenumber = ".$store_row[0];
						}
						$count ++;
					}
					$user_filter = $user_filter.")";
					break;
				}
				elseif (($userrole_row[1] == 'TECH'))
				{
					if ($query_all == 0)
					{
						$user_filter = " AND tbl_workorder.wo_assignedto = '".$user."'";
						break;
					}
					else
					{
						$user_filter = '';
					}
					
				}					
				else
				{
					$user_filter = '';
					break;
				}
			}
	}
	else
	{
		$user_filter ='';
	}	
	####################################################		
	#					QUERY BUILT					   #
	####################################################
	$qry_workorders = "SELECT DATE_FORMAT(tbl_workorder.wo_daterequested, '%c/%e/%Y'), tbl_workorder.wo_number, tbl_workorder.wo_storenumber, tbl_stores.brg_storename, tbl_workorder.wo_category, tbl_workorder.wo_equipment, tbl_workorder.wo_requestedby, tbl_workorder.wo_problem, tbl_workorder.wo_currentstatus, tbl_workorder.wo_priority, tbl_workorder.wo_assignedto FROM tbl_workorder, tbl_stores WHERE tbl_stores.brg_storenum = tbl_workorder.wo_storenumber $view_filter $user_filter ORDER BY $sort $order";
			return $qry_workorders;
}
function updateStatus($wonumber, $user, $status, $note, $database)
{
	$run_query = 0;
	if ($database)
	{
		//Get the current status of the work order
		$current_status = getWOValue($parameter = 'wo_currentstatus', $wonumber, $database);

			if ($current_status == 'Closed')
			{
				if ($status == 'Re-opened')
				{
					$run_query = 1;
				}
			}
			elseif($current_status == 'Opened')
			{
				if (($status == 'Pending') or ($status == 'Closed'))
				{
					$run_query = 1;
				}
			}
			elseif ($current_status == 'Pending')
			{
				if ($status == 'Closed')
				{
					$run_query = 1;
				}
			}
			elseif ($current_status == 'Re-opened')
			{
				
				if (($status == 'Pending') or ($status == 'Closed'))
				{
					$run_query = 1;
				}
			}
		
		//If the criteria for updating the status is met run the query
		if ($run_query == 1)
		{
			//Update status in the work order status table
			$qry_addStatus = "INSERT INTO tbl_wostatus (wo_status, wo_changedate, wo_updatedby, wo_workordernum) VALUES ('$status', NOW(), '$user', $wonumber)";
			$run_update = @mysqli_query ($database, $qry_addStatus);
			
			//Update Static Status in Work Order Table
			$qry_updateStatus = "UPDATE tbl_workorder SET wo_currentstatus='$status' WHERE wo_number=$wonumber";
			$run_update = @mysqli_query ($database, $qry_updateStatus);
			
			//Add the work order note
			$note = "Status Update Note: ".addslashes($note); 
			$qry_addNote = "INSERT INTO tbl_wonotes (wo_workorder, wo_notedate, wo_madeby, wo_note) VALUES ($wonumber, NOW(), '$user', '$note')";
			$run_noteAddition = @mysqli_query ($database, $qry_addNote);
		}
		
		###########################################
		#		       EMAIL STATUS				  #
		###########################################
		
		//find out who the work order is assigned to
		$wo_assignedto = getWOValue($parameter = 'wo_assignedto', $wonumber, $database);
		
		$recipient = '';
		
		//See if assignedto is an email address
		if (substr_count($wo_assignedto, '@') >0)
		{
			$recipient = $wo_assignedto;
		}
		else
		{
			//Query Assigned to email
			
			$qry_assignedto = "SELECT brg_email FROM tbl_users WHERE brg_username = '$wo_assignedto'";
			$run_assignedto = @mysqli_query ($database, $qry_assignedto);
		
				while ($assignedto_row = mysqli_fetch_array($run_assignedto))
				{
					$recipient = $assignedto_row[0];
				}		
		}	
		//Query DM email address
		$brg_dm = getWOValue($parameter = 'wo_dm', $wonumber, $database);
		$qry_dmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_dm'";
		$run_dmemail = @mysqli_query ($database, $qry_dmemail);
		
		
			while ($dm_row = mysqli_fetch_array($run_dmemail))
			{
				if ($dm_row[0]!=$assignedto_row[0])
				{
					$recipient = $recipient.", ".$dm_row[0];
				}
			}
		
		//Query GM email address
		$store_num = getWOValue($parameter = 'wo_storenumber', $wonumber, $database);
		$brg_gm = 'gm'.$store_num;
		
		$qry_gmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_gm'";
		$run_gmemail = @mysqli_query ($database, $qry_gmemail);
		
			while ($gm_row = mysqli_fetch_array($run_gmemail))
			{
				if (($gm_row[0]!= $assignedto_row[0]) or ($gm_row[0]!= $dm_row[0]))
				{
					$recipient = $recipient.", ".$gm_row[0];
				}
			}		
		
		$wo_requestedby = getUserName($user, $database);
		
		//Create Subject and content of the Message
			//Subject:  Status Update | Work rder #
			$subject = " Status Update | Work Order ".$wonumber;
		//Body: Problem | Requested By | Link to Workorder
			$content = "Status Updated To: ".$status."\r\n"."Update By: ".$wo_requestedby."\r\n".$note."\r\n"."Link: http://www.ncarbys.com/workorders.php?email=".$wonumber."\r\n";

			//So I can get all status updates as well
			$recipient = $recipient.", jerrir@ncarbys.com";
			
			$emailed_to = $recipient;
			//call the mail function
			mail_it($content, $subject, $recipient, $email_type=0); 
	}
}
function updateProblem($wonumber, $woproblem, $database)
{	
	if ($database)
	{
		//set-up update query
		$qry_updateProblem = "UPDATE tbl_workorder SET wo_problem = '$woproblem' WHERE wo_number='$wonumber'";
		$run_updateProblem = @mysqli_query ($database, $qry_updateProblem);
	}
}
function updatePriority($wonumber, $wopriority, $database)
{	
	if ($database)
	{
		//set-up update query
		$qry_updatePriority = "UPDATE tbl_workorder SET wo_priority = '$wopriority' WHERE wo_number='$wonumber'";
		echo "<p>".$qry_updatePriority."</p>";
		$run_updatePriority = @mysqli_query ($database, $qry_updatePriority);
	}
}
function addNote($wonumber, $user, $note, $database)
{
	//$database = db_connect();
	if ($database)
	{
		$qry_addNote = "INSERT INTO tbl_wonotes (wo_workorder, wo_notedate, wo_madeby, wo_note) VALUES ('$wonumber', NOW(), '$user', '$note')";
		//echo "<p>".$qry_addNote."</p>";
		$run_addNote = @mysqli_query ($database, $qry_addNote);
	}

	###########################################
	#		       EMAIL NOTE				  #
	###########################################
		//find out who the work order is assigned to
		$wo_assignedto = getWOValue($parameter = 'wo_assignedto', $wonumber, $database);
		
		$recipient = '';
		
		//See if assignedto is an email address
		if (substr_count($wo_assignedto, '@') >0)
		{
			$recipient = $wo_assignedto;
		}
		else
		{
			//Query Assigned to email
			$qry_assignedto = "SELECT brg_email FROM tbl_users WHERE brg_username = '$wo_assignedto'";
			$run_assignedto = @mysqli_query ($database, $qry_assignedto);
		
				while ($assignedto_row = mysqli_fetch_array($run_assignedto))
				{
					$recipient = $assignedto_row[0];
				}		
		}
		
		//Get DM Name
		$brg_dm = getWOValue($parameter = 'wo_dm', $wonumber, $database);
		
		$qry_dmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_dm'";
		$run_dmemail = @mysqli_query ($database, $qry_dmemail);
		
			while ($dm_row = mysqli_fetch_array($run_dmemail))
			{
				if ($dm_row[0]!=$assignedto_row[0])
				{
					$recipient = $recipient.", ".$dm_row[0];
				}
			}
		
		//GET GM EMAIL
		$store_num = getWOValue($parameter = 'wo_storenumber', $wonumber, $database);
		$brg_gm = 'gm'.$store_num;
		
		$qry_gmemail = "SELECT brg_email FROM tbl_users WHERE brg_username = '$brg_gm'";
		$run_gmemail = @mysqli_query ($database, $qry_gmemail);
		
			while ($gm_row = mysqli_fetch_array($run_gmemail))
			{
				if (($gm_row[0]!= $assignedto_row[0]) or ($gm_row[0]!= $dm_row[0]))
				{
					$recipient = $recipient.", ".$gm_row[0];
				}
			}		
		
		//So I can get all
		$recipient = $recipient.", jerrir@ncarbys.com";
		
		$wo_requestedby = getUserName($user, $database);
		$wo_category = getWOValue($parameter = 'wo_category', $wonumber, $database);
		$wo_equipment = getWOValue($parameter = 'wo_equipment', $wonumber, $database);
		$wo_problem = getWOValue($parameter = 'wo_problem', $wonumber, $database);
		
		//Create Subject and content of the Message
			//Subject:  Problem Updated | Work Order #
			$subject = "Note Added | Work Order ".$wonumber;
			$content = "Note: ".$note."\r\n"."Added By: ".$wo_requestedby."\r\n"."Category/Equipment: ".$wo_category." | ".$wo_equipment."\r\n"."Problem: ".$wo_problem."\r\n"."Link: http://www.ncarbys.com/workorders.php?email=".$wonumber."\r\n";
			
			
			//call the mail function
			mail_it($content, $subject, $recipient, $email_type = 0); 		
	
}
###################################################################
#                    WO   HTML/MENU FUNCTIONS                     #
###################################################################
function getStoreDropDown($query, $database, $selected_store)
{	
	if ($database)
	{
		$qry_stores = $query;
		$run = @mysqli_query ($database, $qry_stores);
		echo "<p><label for='wo-store' class='label'>Store: </label>";
		echo "<select id='wo-store' name='wo-store' tabindex='1'>";
			while ($store_row = mysqli_fetch_array($run))
			{
				if($store_row[0] != 9999)
				{
					echo "<option ".isSelected($selected_store, $store_row[0])." value='".$store_row[0]."'>".$store_row[0]." ".$store_row[2]." (".$store_row[1].")</option>";
				}
			}
		echo "<option ".isSelected($selected_store, '9999')." value='9999'>9999 Brumit Restaurant Group</option>";	
		echo "</select></p>";
	}
	else
	{
		echo "<p>There was an error retrieving the user list</p>";
	}
}
function changeCatEquip($currentCategory, $currentEquipment, $wonumber, $submit_status, $database)
{
	//Used From Edit Update Form
	echo "<div class='active-container' id='listWorkOrders'>";
	echo "<div id = 'workorder-edit-container'>";
	echo "<form id='workorder-edit-form' action='workorders.php' method='post'>";
    echo "<fieldset>";
	echo "<legend>&nbsp;WorkOrder #".$wonumber." Category/Equipment Update&nbsp;</legend>";
	echo "<input type='hidden' name='wo-number' id ='wo-number' value='".$wonumber."'>";
	
	if ($database)
	{
		//Show Category in field list with reset button
		//Show Equipment in drop down with Select picke
		echo "<p><label for='wo-category' class='label'>Category/Equip: </label>";
		
		if($submit_status == 0)
		{
			//Show Category in no-edit field with reset button
			echo "<input type='text' name='wo-category' id='wo-category' class='cat-eq' tabindex='1' value='".$currentCategory."' readonly/>";
			echo "<input type='submit' name='wo-ce-reset' id='wo-ce-reset' class='button' value='Reset' style='color:grey;' tabindex='2' />";
			
			//Show  Equipment in drop down list with a Select Button
			$qry_equipment = "SELECT wo_equipment FROM tbl_woequipment WHERE wo_category = '".$currentCategory."' ORDER BY wo_equipment";
			$run = @mysqli_query ($database, $qry_equipment);
			echo "<select class='cat-eq' id='wo-equipment' name='wo-equipment' tabindex='4'>";
				while ($equipment_row = mysqli_fetch_array($run))
				{
					echo "<option ".isSelected($currentEquipment, $equipment_row[0])." value='".$equipment_row[0]."'>".$equipment_row[0]."</option>";
				}
			echo "</select>";
			echo "<input type='submit' name='wo-ce-update' id='wo-ce-update' class='button' value='Update' tabindex='5' />";
		}
		//Category Change
		elseif($submit_status == 1)
		{
			//Show Category dropdown only with select button
			$qry_categories = "SELECT wo_category FROM tbl_wocategories ORDER BY wo_category";
			$run = @mysqli_query ($database, $qry_categories);
			
			echo "<select class='cat-eq' id='wo-category' name='wo-category' tabindex='1'>";
			echo "<option value='0'</option>";
				while ($category_row = mysqli_fetch_array($run))
				{
					echo "<option ".isSelected($currentCategory, $category_row[0])." value='".$category_row[0]."'>".$category_row[0]."</option>";
				}
			echo "</select>";
			echo "<input type='submit' name='wo-ce-resetSubmit' id='wo-ce-resetSubmit' class='button' value='Select' tabindex='2' />";
		}
	}		
	echo "</form>";
	echo "</div>";
	echo "</div>";
}
function getCategoryDropDown($selected_category, $submit_status, $database)
{	
	//Used from the Submit Workorder Form
	if ($database)
	{
		echo "<p><label for='wo-category' class='label'>Category/Equip: </label>";
		//If no category has been selected before / or reset / display entire list
		if($submit_status == -1 or $submit_status == 0 or $submit_status == 4 or $submit_status == 5)
		{
			$qry_categories = "SELECT wo_category FROM tbl_wocategories ORDER BY wo_category";
			$run = @mysqli_query ($database, $qry_categories);
			
			echo "<select class='cat-eq' id='wo-category' name='wo-category' tabindex='2'>";
			echo "<option value='0'</option>";
				while ($category_row = mysqli_fetch_array($run))
				{
					echo "<option ".isSelected($selected_category, $category_row[0])." value='".$category_row[0]."'>".$category_row[0]."</option>";
				}
			echo "</select>";
			echo "<input type='submit' name='wo-category-submit' id='wo-category-submit' class='button' value='Select' tabindex='5' />";
		}
		//Else lock the field and only let the user choose to reset the data
		else
		{
			echo "<input type='text' name='wo-category' id='wo-category' class='cat-eq' tabindex='2' value='".$selected_category."' readonly/>";
			echo "<input type='submit' name='wo-category-reset' id='wo-category-reset' class='button' value='Reset' style='color:grey;' tabindex='5' />";
		}
	}
	else
	{
		echo "<p>There was an error retrieving the category list</p>";
	}
}
function getEquipmentDropDown($selected_category, $selected_equipment, $submit_status, $database)
{	
	//Used from the Submit Work Order Form
	if ($database)
	{
		//If no equipment has been selected before and a category has been choosen display drop down
		if($submit_status == 1 or $submit_status==3)
		{
			$qry_equipment = "SELECT wo_equipment FROM tbl_woequipment WHERE wo_category = '".$selected_category."' ORDER BY wo_equipment";
			$run = @mysqli_query ($database, $qry_equipment);
			echo "<select class='cat-eq' id='wo-equipment' name='wo-equipment' tabindex='4'>";
				while ($equipment_row = mysqli_fetch_array($run))
				{
					echo "<option ".isSelected($selected_equipment, $equipment_row[0])." value='".$equipment_row[0]."'>".$equipment_row[0]."</option>";
				}
			echo "</select>";
			echo "<input type='submit' name='wo-equipment-submit' id='wo-equipment-submit' class='button' value='Continue' tabindex='5' />";
		}
		else
		{
			echo "<input type='text' name='wo-equipment' id='wo-equipment' class='cat-eq' tabindex='2' value='".$selected_equipment."' readonly/>";
			echo "<input type='submit' name='wo-equipment-reset' id='wo-equipment-reset' class='button' value='Reset' style='color:grey;' tabindex='5' />";
		}
	}
	else
	{
		echo "<p>There was an error retrieving the category list</p>";
	}
}
function getWorkorderList($sort, $order, $view, $user, $query_all, $database)
{
	//Builds Work Order List/Table
	$row = 'odd';
	echo "<div id='listWorkOrders' class='active-container'>";
      	echo "<div id = 'workorder-list-container'>";
		
		$tech = confirmUserRole($_SESSION['username'], 'TECH', $database);
		if ($tech ==1)
		{
			if ($view == 'ACTIVE')
			{
				$v = 'Active';
				$button = 'active';
			}
			else
			{
				$v = 'Closed';
				$button = 'closed';
				
			}
			echo "<form id='wo-viewadd' action='workorders.php' method='post'>";
			echo "<input type='submit' name='wo-viewall-".$button."' id='wo-viewall-submit' class='button' value='View All ".$v."' tabindex='1' />";
			echo "<input type='hidden' name='wo-view' id ='wo-view' value='".$view."'>";
			echo "<input type='hidden' name='wo-viewAll' id ='wo-viewAll' value='".$query_all."'>";
			echo "</form>";
		}
//&$view=".$view."&queryall=".$query_all
		echo "<table width='590px' border='0' cellpadding='0' cellspacing='0'>";
			echo "<tr class='heading'>";
			echo "<th scope='col' width='12%'><a href='workorders.php?sort=date&order=".getSortDirection($sort, $order, $link='date')."&v=".$view."&qa=".$query_all."'>Date</a></th>";
			echo "<th scope='col' width='8%'><a href='workorders.php?sort=number&order=".getSortDirection($sort, $order, $link='number')."&v=".$view."&qa=".$query_all."'>WO#</a></th>";
            echo "<th scope='col' width='18%'><a href='workorders.php?sort=store&order=".getSortDirection($sort, $order, $link='store')."&v=".$view."&qa=".$query_all."'>BRG Store</a></th>";
            echo "<th scope='col' width='13%'><a href='workorders.php?sort=category&order=".getSortDirection($sort, $order, $link='category')."&v=".$view."&qa=".$query_all."'>Category</a></th>";
            echo "<th scope='col' width='28%'><a href='workorders.php?sort=equipment&order=".getSortDirection($sort, $order, $link='equipment')."&v=".$view."&qa=".$query_all."'>Equipment</a></th>";
			echo "<th scope='col' width='22%'><a href='workorders.php?sort=assignedto&order=".getSortDirection($sort, $order, $link='assignedto')."&v=".$view."&qa=".$query_all."'>Assigned</a></th>";
            echo "</tr>";
			
				$qry_workorders = getWorkListQuery($sort, $order, $view, $user, $query_all, $database);
			
			$run_workorders = @mysqli_query ($database, $qry_workorders);	
			//echo "<p>".$qry_workorders."</p>";
				while ($workorder_row = mysqli_fetch_array($run_workorders))
				{
					$assigned_to = getUserName($workorder_row[10], $database);
					//Display brief about workorders
					echo "<tr class='wo-brief-".$row."'>";
					echo "<td ".getPriorityColor($workorder_row[9]).">".$workorder_row[0]."</td>";
					echo "<td><a ".getStatusColor($workorder_row[8])." href='workorders.php?view=".$workorder_row[1]."' target='_blank'>".$workorder_row[1]."</a></td>";
					echo "<td>".$workorder_row[2]." ".substr($workorder_row[3], 0, 8)."</td>";
					echo "<td>".substr($workorder_row[4], 0, 9)."...</td>";
					echo "<td>".substr($workorder_row[5], 0, 21)."...</td>";
					echo "<td>".substr($assigned_to, 0, 30)."</td>";
					echo "</tr>";
					//More Information Section
					echo "<tr class='wo-detail-".$row."'>";
					echo "<td>&nbsp;</td>";
					echo "<td colspan='5'>";
					echo "<p><strong>Problem: </strong>".$workorder_row[6]." states,".$workorder_row[7]."</p>";
					
					//Get status of the work order
					//Find the latest status date
					$qry_latestDate = "SELECT MAX(wo_changedate) as highest_date FROM tbl_wostatus WHERE wo_workordernum = $workorder_row[1]";
					$run_lastestDate = @mysqli_query ($database, $qry_latestDate);
								
						while ($latestDate_row = mysqli_fetch_array($run_lastestDate))
						{
							$lastDateChanged = $latestDate_row[0];
						}
					$qry_status = "SELECT tbl_wostatus.wo_status, DATE_FORMAT(tbl_wostatus.wo_changedate, '%c/%e/%Y'), tbl_users.brg_firstname, tbl_users.brg_lastname FROM tbl_wostatus, tbl_users WHERE brg_username = wo_updatedby AND tbl_wostatus.wo_changedate = '$lastDateChanged' AND tbl_wostatus.wo_workordernum = $workorder_row[1]";
					$run_status = @mysqli_query ($database, $qry_status);	
						while ($status_row = mysqli_fetch_array($run_status))
						{
							echo "<p><strong>Status: </strong>".$status_row[0]." on ".$status_row[1]." by " .$status_row[2]." ".$status_row[3]."</p>";
						}
						
						$assigned_to = getUserName($workorder_row[10], $database);
						echo "<p><strong>Assigned To: </strong>".$assigned_to."</p>";
						
					echo "</td>";
					echo "</tr>";
					
					//Get more actions
					echo "<tr class='wo-brief-action-".$row."'>";
					echo "<td colspan='6'><a href='workorders.php?view=".$workorder_row[1]."' target='_blank'>View</a> | <a href='workorders.php?edit=".$workorder_row[1]."' target='_blank'>Edit/Update</a> | <span class='view'>More Information +/-</span></td>";
					//<a href='workorders.php?viewmore=".$workorder_row[1]."'>
					echo "</tr>";
				
					//Change Even/Odd status
					if ($row == 'odd')
					{
						$row = 'even';
					}
					else
					{
						$row = 'odd';
					}
				}
		echo "</table>";
		echo "</div>";
		echo "</div>";
}
###################################################################
#               FUNCTIONS FOR WORK ORDER LIST                     #
###################################################################
function getSortDirection($sort, $order, $link)
{
	if ($sort == $link)
	{
		if ($order == 'ASC')
		{
			$order = 'DESC';
		}
		else
		{
			$order = 'ASC';
		}
		return $order;
	}
	else
	{
		$order = 'ASC';
	}
	return $order;
}
function getStatusColor($status)
{
	if ($status == 'Opened')
	{
		return "style='color:green; font-weight:bold;'";
	}
	elseif($status == 'Pending')
	{
		return "style='color:blue; font-weight:bold;'";
	}
	elseif($status == 'Re-opened')
	{
		return "style='color:red; font-weight:bold;'";
	}
	else
	{
		return '';
	}
}
function getPriorityColor($priority)
{
	if ($priority == 'Normal')
	{
		return "style='color:green;'";
	}
	elseif($priority == 'Low')
	{
		return '';
	}
	elseif($priority == 'High')
	{
		return "style='color:orange; font-weight:bold;'";
	}
	elseif($priority == 'Emergency')
	{
		return "style='color:red; font-weight:bold;'";
	}
	else
	{
		return '';
	}
}
###################################################################
#                   FUNCTIONS EDIT/UPDATE FORM                    #
###################################################################
function workOrderStatus($wonumber, $database)
{
	//$database = db_connect();
	if ($database)
	{
		$qry_status = "SELECT wo_statusid, DATE_FORMAT(wo_changedate, '%c/%e/%Y'), wo_status, wo_updatedby FROM tbl_wostatus WHERE wo_workordernum = $wonumber ORDER BY wo_changedate DESC";
		
		$run_status = @mysqli_query ($database, $qry_status);
		
			while ($status_row = mysqli_fetch_array($run_status))
			{
				$id  = $status_row[0];
				$date = $status_row[1];
				$status = $status_row[2];
				$by = getUserName($status_row[3], $database);
				echo "<p><label for='wo-category' class='label'>Updated on </label>";
				echo "<input type='text' name='wo-statusDate-".$id."' id='wo-statusDate-".$id."' class='field-small-text' value='".$date."' readonly/><span class='desc'> to </span>"; 
			 echo "<input type='text' name='wo-status-".$id."' id='wo-status-".$id."' class='field-small-text' value='".$status."' readonly/> <span class='desc'> by </span>";	
			 echo "<input type='text' name='wo-statusBy-".$id."' id='wo-statusBy-".$id."' class='field-long-text' value='".$by."' readonly/>";
			}
	}
}
function getWONotes($wonumber, $database)
{
	//$database = db_connect();
	if ($database)
	{
		$qry_notes = "SELECT wo_notesid, DATE_FORMAT(wo_notedate, '%c/%e/%Y'), wo_madeby, wo_note FROM tbl_wonotes WHERE wo_workorder = $wonumber ORDER BY wo_notedate DESC";
		//echo "<p>".$qry_notes."</p>";
		$run_notes = @mysqli_query ($database, $qry_notes);
		
			while ($notes_row = mysqli_fetch_array($run_notes))
			{
				$id = $notes_row[0];
				$date = $notes_row[1];
				$by = getUserName($notes_row[2], $database);
				$note = $notes_row[3];
				
				echo "<p><label for='wo-noteDate-".$id."' class='label'>Note Made on </label>";
				echo "<input type='text' name='wo-noteDate-".$id."' id='wo-noteDate-".$id."' class='field-small-text' value='".$date."' readonly/><span class='desc'> by </span>"; 
				echo "<input type='text' name='wo-noteBy-".$id."' id='wo-notesBy-".$id."' class='field-long-text' value='".$by."' readonly/>";
				echo "</p>";
				echo "<p><label for='wo-note-".$id."' class='label'> </label>";
				echo "<textarea id='wo-note-".$id."' name='wo-note-".$id."' rows='3' cols='20' readonly>".$note."</textarea>";
				echo "</p>";
			}
	}
}
function getAssignNames ($wonumber, $current_assign, $database)
{	
	//Get DM and add to array
	$brg_dm = getWOValue($parameter='wo_dm', $wonumber, $database);
	$assign_to = array($brg_dm);
	
	//Get GM and add to array
	$store_num = getWOValue($parameter = 'wo_storenumber', $wonumber, $database);
	$brg_gm = 'gm'.$store_num;
	
	array_push($assign_to, $brg_gm);
	
	//Get Techs and Employees and add to array
	$qry_others = "SELECT brg_userid FROM tbl_userassign WHERE brg_role = 'ADMIN' OR brg_role = 'EMP' OR brg_role = 'TECH'";
	$run_others = @mysqli_query ($database, $qry_others);
	
	///echo $qry_others;
		while ($others_row = mysqli_fetch_array($run_others))
		{
			array_push($assign_to, $others_row[0]);
		}
	//Delete any duplicate values
	$assigned = array_unique($assign_to);
	
	//Sort array
	sort($assigned);
	
	//Get First and last name for array and display in drop down
	echo "<select id='user-assign' name='user-assign' tabindex='4'>";
	foreach ($assigned as $username)
	{
	
		//Query for Users first and last name
		if ($database)
		{
			$qry_user = "SELECT brg_username, brg_firstname, brg_lastname FROM tbl_users WHERE brg_username='$username'";
			$run_qry = @mysqli_query ($database, $qry_user);
			//echo "<p>".$qry_user."</p>";
			
			while ($user_row = mysqli_fetch_array($run_qry))
			{
				echo "<option ".isSelected($picked=$current_assign, $user_row[0])." value='".$user_row[0]."'>".$user_row[1]." ".$user_row[2]."</option>";
			}
		}
		else
		{
			echo "<p>There was a problem accessing the users' name</p>";
		}
	}
	echo "</select>";
}
function canChangeAssignedTo($username, $database)
{
	$tech = confirmUserRole($_SESSION['username'], 'TECH', $database);
	$admin = confirmUserRole($_SESSION['username'], 'ADMIN', $database);
	if (($admin == 1) OR ($tech == 1))
	{
		$allowed_access = 1;
	}
	else
	{
		$allowed_access = 0;
	}
	return $allowed_access;
}
function updateAssignedTo($wonumber, $new_assignedto, $database)
{
		$qry_updateAssignedTo = "UPDATE tbl_workorder SET wo_assignedto = '$new_assignedto' WHERE wo_number=$wonumber";
		$run_updateAssignedTo = @mysqli_query ($database, $qry_updateAssignedTo);
		//echo $qry_updateAssignedTo;
	//Send Email
}
function checkCurrentStatus ($wonumber, $new_status, $database)
{
	$current_status = getWOValue($parameter='wo_currentstatus', $wonumber, $database);

	if($current_status == $new_status)
	{
		return 2;
	}
	else
	{
		return 0;
	}
}
###################################################################
#                       AUTH Functions                            #
###################################################################
function getLoginForm()
{	
	echo "<div id='login-container'>";
	echo "<form id ='login-form' action='login.php' method='post'>";
	echo "<fieldset>";
	echo "<legend>&nbsp;Login to BRG&nbsp;</legend>";
	echo "<p><label for='login-name' class='label'>Username</label>";
	echo "<input type='text' name='login-name' id='login-name' class='field' value='' tabindex='1'/></p>";
	echo "<p><label for='login-password' class='label'>Password</label>";
	echo "<input type='password' name='login-password' id='login-name' class='field' tabindex='2' value=''/></p>";
	echo "<p><input type='submit' name='logon-submit' id='logon-submit' class='button' value='Login' tabindex='3' /></p>";
	//echo "<p class='forgot'><a href='#'>Forgot Password</a></p>";
	echo "</fieldset>";
	echo "</form>";
	echo "</div>";
}	
//logs in a user and sets the user's id 
function validateLogon($user, $password, $database)
{
	if ($database)
	{
		if (empty($user) or empty($password))
		{
			return 0;
		}	
		elseif ($database)
		{
			//set-up update query
			$qry_findUser = "SELECT brg_username, brg_password FROM tbl_users WHERE brg_username='$user' AND brg_isDeleted=0";
			//Run Query
			$run = @mysqli_query ($database, $qry_findUser);
			$num_rows = mysqli_num_rows($run);
			
			if ($num_rows > 0)
			{
				while ($user_row = mysqli_fetch_array($run))
				{
					$password = sha1($password);
					if ($password == $user_row[1])
					{
						$_SESSION['username'] = $user_row[0];
						return 1;
					}
					else
					{
						return 0;
					}
				}
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}
//confirms that a user has access to a module
function confirmUserRole($user, $requestedrole, $database)
{	
	if ($database)
	{
		$qry_userrole = "SELECT brg_role, brg_active FROM tbl_userassign WHERE brg_userid='$user'";
		//Run Query
		$run = @mysqli_query ($database, $qry_userrole);
		$num_rows = mysqli_num_rows($run);
		
		if($num_rows >0)
		{
			while ($userrole_row = mysqli_fetch_array($run))
			{
				if ($userrole_row[0] == $requestedrole)
				{
					if ($userrole_row[1] == 1)
					{
						return 1;
						//if access has been found exit while loop
						break;
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			return 0;
		}	
	}
	else
	{
		echo "<p>There was an error</p>";
	}
}
function getUserName($username, $database)
{
	//Set Database Connection
	//$database = db_connect();
	if ($database)
	{
		$qry_username = "SELECT brg_firstname, brg_lastname FROM tbl_users WHERE brg_username='$username'";
		$run_qry = @mysqli_query ($database, $qry_username);
		$username_row = mysqli_fetch_array($run_qry);
		
		if ($username_row)
		{
			$userFullName = $username_row[0]." ".$username_row[1];
		}
		else
		{
			$userFullName = $username;
		}
		
		return $userFullName;
	}
}
function getUserValue($username, $parameter, $database)
{
	if ($database)
	{
		$qry_parameter = "SELECT ".$parameter." FROM tbl_users WHERE brg_username='$username'";
		$run_qry = @mysqli_query ($database, $qry_parameter);
		$parameter_row = mysqli_fetch_array($run_qry);
		
		$value = $parameter_row[0];
		return $value;
	}
}

###################################################################
#                       ON CALL FUNCTIONS                         #
###################################################################
function checkTech($database)
{
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
}

function  canChangeOnCall($database)
{
	$tech = confirmUserRole($_SESSION['username'], 'TECH', $database);
	$admin = confirmUserRole($_SESSION['username'], 'ADMIN', $database);
	if (($admin == 1) OR ($tech == 1) )
	{
		return 1;
	}
	else
	{
		return 0;
	}
}
function changeOnCallTech($tech, $database)
{
		$qry_updateoncall = "UPDATE tbl_oncall SET wo_oncallid = '$tech'";
		$run_updateoncall = @mysqli_query ($database, $qry_updateoncall);
}
function getOnCallForm($database)
{
	//Get current tech
	$current_tech = checkTech($database);

	//Get user's name and phone number
	$qry_techInfo ="SELECT brg_firstname, brg_lastname, brg_othernumber FROM tbl_users WHERE brg_username = '$current_tech'";
	$run_techInfo = @mysqli_query ($database, $qry_techInfo);
	$techInfo_row = mysqli_fetch_array($run_techInfo);
	
	$tech_name = $techInfo_row[0]." ".$techInfo_row[1];
	$tech_number = $techInfo_row[2];
	
	echo "<div class='active-container' id='listWorkOrders'>";
	echo "<div id = 'workorder-edit-container'>";
	echo "<form id='workorder-edit-form' action='workorders.php' method='post'>";
    echo "<fieldset>";
	echo "<legend>On Call</legend>";
	echo "<p>If you are experiencing an emergency you may contact this  week's on-call maintenance technician.  If you receive his or her voice mail, please leave a message.  If you have not heard back from the maintenance technician within an hour, you may contact the maintenance supervisor, Jason McCammon at 828-691-8202.</p>";
	echo "<h2 style='text-align: center'><strong>This Week's On-Call Technician</strong></h2>" ;
	echo "<h2 style='text-align: center'><strong>".$tech_name." | ".$tech_number."</strong></h2>" ;
	echo "</fieldset>";
	$can_change = canChangeOnCall($database);
	if ($can_change == 1)
	{
		echo "<fieldset>";
		echo "<legend>Change On Call Tech</legend>";
		echo "<p>Replace current tech with ";
		getOnCallTechs($current_tech);
			echo "<input type='submit' name='co-change-current' id='co-change-current' class='button' value='Change' tabindex='2' />";
		echo "</p>";
		echo "</fieldset>";
	}
	echo "</form>";
	echo "</div>";
}
?>