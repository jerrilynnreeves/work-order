<?php
###################################################################
#               STATIC POPULATE FIELDS Functions                  #
###################################################################
function getUserRoleCMB()
{
	echo "<p><label for='user-role' class='label'>Role: </label>";
	echo "<select id='user-role' name='user-role' tabindex='4'>";
	echo "<option ".isSelected($brg_role, 'EMP')." value='EMP'>Employee</option>";
	echo "<option ".isSelected($brg_role, 'ADMIN')." value='ADMIN'>Administrator</option>";
	echo "<option ".isSelected($brg_role, 'DM')." value='DM'>District Manager</option>";
	echo "<option ".isSelected($brg_role, 'GM')." value='GM'>General Manager</option>";
	echo "<option ".isSelected($brg_role, 'TECH')." value='TECH'>Technician</option>";
	echo "</select>";
}
function getUserStatusCMB()
{
	echo "<p><label for='user-status' class='label'>Status: </label>";
	echo "<select id='user-status' name='user-status' tabindex='5'>";
	echo "<option ".isSelected($brg_status, 1)." value='1'>Active User</option>";       
	echo "<option ".isSelected($brg_status, -1)." value='-1'>Inactive User</option>";
	echo "</select>";
}
###################################################################
#                          FORM Functions                         #
###################################################################
function getAdminFunctions()
{
	echo "<div id='app-functions'>";
	echo "<h2 id='add-user' class='app-title'>Add User</h2>";
	echo "<h2 id='change-password' class='app-title'>Change Password</h2>";
	echo "<h2 id='edit-user' class='app-title'>Edit User</h2>";
	echo "<h2 id='editrole-user' class='app-title'>Add/Edit User Role</h2>";
	echo "<h2 id='delete-user' class='app-title'>Delete User</h2>";
	echo "</div>";
}
function getConfirmation($brg_username, $brg_email, $brg_firstname, $brg_lastname, $brg_role, $brg_status)
{
	echo "<div id='confirm-user-container'>";
	echo "<form id='confirm-user-form' action='ua_admin.php' method='post'>";
	echo "<fieldset>";
	echo "<legend>&nbsp;User Confirmation Form&nbsp;</legend>";
	echo "<p>".$brg_firstname." ".$brg_lastname." was added to the database with the following properties: </p>";
	echo "<p>Username: ".$brg_username."</p>";
	echo "<p>Email: ".$brg_email."</p>";
	echo "<p>Role: ".$brg_role."</p>";
	if ($brg_status == 1)
	{
		echo "<p>Status: Active</p>";
	}
	else
	{
		echo "<p>Status: In-Active</p>";
	}
	echo "</fieldset>";
	echo "<p style='text-align:center'>";
	echo "<input type='submit' name='uemail-submit' id='uemail-submit' class='button' value='Email User' tabindex='1' />";
	echo "<input type='submit' name='edit-user-submit' id='edit-user-submit' class='button' value='Edit User' tabindex='2' />";
	echo "<input type='submit' name='add-userrole-submit' id='add-userrole-submit' class='button' value='Add/Edit Role' tabindex='4' />";
	echo "</p>";
	echo "<p style='text-align:center'>";
	echo "<input type='submit' name='close' id='close' class='button' value='Close' tabindex='6' />";
	echo "</p>";
	echo "</form>";
	echo "</div>";
}
function getAddUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $brg_password, $brg_role, $brg_status, $submit_status, $db)
{
	if ($submit_status ==0)
	{
		echo "<div id='addUserForm' class='hidden-container'>";
	}
	elseif($submit_status==-1)
	{
		echo "<div id='addUserForm' class='active-container'>";
		echo "<p class='error'>The form contained missing data.  Please fill out the fields labeled in red and re-submit</p>";
	}
	else
	{
		echo "<div id='addUserForm' class='active-container'>";
	}
	
	echo "<div id='add-user-container'>";
	echo "<form id='add-user-form' action='ua_admin.php' method='post'>";
	echo "<fieldset>";
	echo "<legend>&nbsp;Add User&nbsp;</legend>";
	echo "<p><label for='user-name' class='label' ".formatMissing($submit_status, $brg_username).">Username: </label>";
	echo "<input type='text' name='user-name' id='user-name' class='field' value='".$brg_username."' tabindex='1'/></p>";
	echo "<p><label for='user-email' class='label' ".formatMissing($submit_status, $brg_email).">Email Address: </label>";
	echo "<input type='text' name='user-email' id='user-email' class='field' tabindex='2' value='".$brg_email."'/></p>";
	echo "<p><label for='user-firstname' class='label' ".formatMissing($submit_status, $brg_firstname).">First Name: </label>";
	echo "<input type='text' name='user-firstname' id='user-firstname' class='field' tabindex='2' value='".$brg_firstname."'/></p>";
	echo "<p><label for='user-lastname' class='label' ".formatMissing($submit_status, $brg_lastname).">Last Name: </label>";
	echo "<input type='text' name='user-lastname' id='user-lastname' class='field' tabindex='3' value='".$brg_lastname."'/></p>";
	echo "<p><label for='user-password' class='label' ".formatMissing($submit_status, $brg_password).">Password: </label>";
	echo "<input type='text' name='user-password' id='user-password' class='field' tabindex='3' value='".$brg_password."'/></p>";
	echo "</fieldset>";
	echo "<fieldset>";
	echo "<legend>&nbsp;Add User Role&nbsp;</legend>";
	getUserRoleCMB();
	getUserStatusCMB();
	echo "</fieldset>";
	echo "<p style='text-align:center'>";
	echo "<input type='submit' name='add-user-submit' id='add-user-submit' class='button' value='Add User' tabindex='4' />";
	echo "<input type='submit' name='cancel' id='cancel' class='button' value='Cancel' tabindex='5' />";
	echo "</p>";
	echo "</form>";
	echo "</div>";
	echo "</div>";
}
function getEditUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $submit_status, $db)
{
	if ($submit_status ==0)
	{
		echo "<div id='editUserForm' class='hidden-container'>";
	}
	elseif($submit_status==1)
	{
		echo "<div id='editUserForm' class='active-container'>";
	}
	else
	{
		echo "<div id='editUserForm' class='active-container'>";
	}
	
	echo "<div id='edit-user-container'>";
	echo "<form id='edit-user-form' action='ua_admin.php' method='post'>";

	if ($submit_status == 0)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Select User&nbsp;</legend>";
		getRegisteredUsers($db);
		echo "<input type='submit' name='userEdit-submit' id='userEdit-submit' class='button' value='Select User' tabindex='1' />";
		echo "</fieldset>";
	}
	else
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Edit User&nbsp;</legend>";
		echo "<p><label for='user-name' class='label' ".formatMissing($submit_status, $brg_username).">Username: </label>";
		echo "<input type='text' name='user-name' id='user-name' class='field' value='".$brg_username." ' style='background-color: #F1F1F1; border: solid 1px #CCCCCC;' readonly/></p>";
		echo "<p><label for='user-email' class='label' ".formatMissing($submit_status, $brg_email).">Email Address: </label>";
		echo "<input type='text' name='user-email' id='user-email' class='field' tabindex='1' value='".$brg_email."'/></p>";
		echo "<p><label for='user-firstname' class='label' ".formatMissing($submit_status, $brg_firstname).">First Name: </label>";
		echo "<input type='text' name='user-firstname' id='user-firstname' class='field' tabindex='2' value='".$brg_firstname."'/></p>";
		echo "<p><label for='user-lastname' class='label' ".formatMissing($submit_status, $brg_lastname).">Last Name: </label>";
		echo "<input type='text' name='user-lastname' id='user-lastname' class='field' tabindex='3' value='".$brg_lastname."'/></p>";
		//echo "<p><label for='user-password' class='label' ".formatMissing($submit_status, $brg_password).">Password: </label>";
		//echo "<input type='text' name='user-password' id='user-password' class='field' tabindex='3' value='".$brg_password."'/></p>";
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='edit-user-submit' id='edit-user-submit' class='button' value='Edit User' tabindex='4' />";
		echo "<input type='submit' name='cancel' id='cancel' class='button' value='Cancel' tabindex='5' />";
		echo "</p>";
		echo "</fieldset>";
		echo "<fieldset>";
		echo "<legend>&nbsp;Edit User Role&nbsp;</legend>";
		getUserRolesTable($brg_username,$type='ed', $db);
		echo "</fieldset>";
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='close' id='close' class='button' value='Close' tabindex='6' />";
		echo "</p>";
		if ($submit_status >0)
		{
			echo "<p style='color:red; font-size: small;'>You must click <strong>Close</strong> before you can perform any other administrative functions listed left not related to editing user roles (such as adding, deleting, or changing passwords.</p>";
		}
	}
	echo "</form>";
	echo "</div>";
	echo "</div>";
}
function getUserRoleForm($username, $submit_status, $db)
{
	if ($submit_status == 0)
	{
		echo "<div id='editUserRoleForm' class='hidden-container'>";
	}
	else
	{
		echo "<div id='editUserRoleForm' class='active-container'>";
	}
	echo "<div id='user-role-container'>";
	echo "<form id='user-role-form' action='ua_admin.php' method='post'>";
	echo "<input type='hidden' name='username' id = 'username' value='".$username."'>";
	if ($submit_status == 0)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Select User&nbsp;</legend>";
		getRegisteredUsers($db);
		echo "<input type='submit' name='ur-selectUser-submit' id='ur-selectUser-submit' class='button' value='Select User' tabindex='1' />";
		echo "</fieldset>";
	}
	else
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Edit ".getUserName($username, $db)."'s Role(s)&nbsp;</legend>";
		getUserRolesTable($username, $type='ur', $db);
		echo "</fieldset>";
		
		echo "<fieldset>";
		echo "<legend>&nbsp;Add Role&nbsp;</legend>";

		getUserRoleCMB();
		getUserStatusCMB();
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='ur-addRole-submit' id='ur-addRole-submit' class='button' value='Add Role' tabindex='1' />";
		echo "</p>";
		echo "</fieldset>";
	}
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='close' id='close' class='button' value='Close' tabindex='1' />";
		echo "</p>";
		if ($submit_status >0)
		{
			echo "<p style='color:red; font-size: small;'>You must click <strong>Close</strong> before you can perform any other administrative functions listed left not related to editing user roles (such as adding, deleting, or changing passwords.</p>";
		}
		echo "</form>";
		echo "</div>";
		echo "</div>";
}
function getPasswordForm($username, $submit_status, $db)
{
	//If the form has not been submitted, hide it
	if ($submit_status == 0)
	{
		echo "<div id='changePwdForm' class='hidden-container'>";
	}
	//If the password form was submitted with a blank field, show error
	elseif($submit_status==-1)
	{
		echo "<div id='changePwdForm' class='active-container'>";
		echo "<p class='error'>The password field was blank.  Please try again.</p>";
	}
	//Else display the container opened
	else
	{
		echo "<div id='changePwdForm' class='active-container'>";
	}
	echo "<div id='change-pwd-container'>";
	echo "<form id='change-pwd-form' action='ua_admin.php' method='post'>";
	echo "<input type='hidden' name='username' id = 'username' value='".$username."'>";
	//If a request has never been submitted, show user names
	if ($submit_status == 0)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Change Password&nbsp;</legend>";
		getRegisteredUsers($db);
		echo "<input type='submit' name='pwd-user-submit' id='pwd-user-submit' class='button' value='Select User' tabindex='1' />";
		echo "</fieldset>";
	}
	//If password was sucessfully changed
	elseif ($submit_status ==2)
	{
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='email-userpwd' id='email-userpwd' class='button' value='Email It' tabindex='1' />";
		echo "<input type='submit' name='cancel' id='cancel' class='button' value='Cancel' tabindex='5' />";
		echo "</p>";		
	}
	else
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Change ".getUserName($username, $db)."'s Password&nbsp;</legend>";
		echo "<p><label for='pwd' class='label'>New Password: </label>";
		echo "<input type='text' name='pwd' id='pwd' class='field' value='' tabindex='1'/></p>";
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='change-pwd-submit' id='change-pwd-submit' class='button' value='Change Password' tabindex='3' />";
		echo "<input type='submit' name='cancel' id='cancel' class='button' value='Cancel' tabindex='4' />";
		echo "</p>";
		echo "</fieldset>";
	}
		if ($submit_status >0 or $submit_status == -1)
		{
			echo "<p style='color:red; font-size: small;'>You must click <strong>Cancel</strong> if you want to exit out of this function and do something else listed left.</p>";
		}
		echo "</form>";
		echo "</div>";
		echo "</div>";
}
function getDeleteUserForm($username, $submit_status, $db)
{
	if ($submit_status == 0)
	{
		echo "<div id='deleteUserForm' class='hidden-container'>";
	}
	else
	{
		echo "<div id='deleteUserForm' class='active-container'>";
	}
	echo "<div id='delete-user-container'>";
	echo "<form id='delete-user-form' action='ua_admin.php' method='post'>";
	echo "<input type='hidden' name='username' id = 'username' value='".$username."'>";
	
	if (($submit_status == 0) or ($submit_status == 3))
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Delete User&nbsp;</legend>";
		if($submit_status == 3)
		{
			echo "<p style='color:red'>There was a problem deleting the selected user</p>";
		}
		getRegisteredUsers($db);
		echo "<input type='submit' name='delete-user-submit' id='delete-user-submit' class='button' value='Select User' tabindex='1' />";
		echo "</fieldset>";
	}
	elseif ($submit_status ==1)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Confirm Deletion&nbsp;</legend>";
		echo "<p>Please cofirm or cancel the deletion of <br/><strong>".getUserName($username, $db)."</strong></p>";
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='confirm-delete-submit' id='confirm-delete-submit' class='button' value='Confirm' tabindex='1' />";
		echo "<input type='submit' name='cancel' id='cancel' class='button' value='Cancel' tabindex='2' />";
		echo "</p>";		
	}
	elseif ($submit_status == 2)
	{
		echo "<fieldset>";
		echo "<legend>&nbsp;Deleted User&nbsp;</legend>";
		
		echo "<p style='color:green;'>The user was deleted sucessfully</p>";
		echo "<p style='text-align:center'>";
		echo "<input type='submit' name='close' id='close' class='button' value='Close' tabindex='5' />";
		echo "</p>";		
	}
	if ($submit_status >0)
	{
		echo "<p style='color:red; font-size: small;'>You must click <strong>Cancel</strong> or <strong>Close</strong> button if you want to exit out of this function and do something else listed left.</p>";
	}
	echo "</form>";
	echo "</div>";
	echo "</div>";
}
function getUserRolesTable($username, $type, $db)
{
	if ($db)
	{
		$qry_userRoles = "SELECT brg_id, brg_role, brg_active FROM tbl_userassign WHERE brg_userid='$username'";
		$run_qry = @mysqli_query ($db, $qry_userRoles);
		
		echo "<table width='100%' border='0'>";
			while ($userRole_row = mysqli_fetch_array($run_qry))
			{
				echo "<tr>";
				echo "<td><a href='ua_admin.php?d-".$type."=".$userRole_row[0]."&un=".$username."'>Delete</a></td>";
				if ($userRole_row[2] == 1)
				{
					echo "<td><a href='ua_admin.php?c-".$type."=".$userRole_row[0]."&un=".$username."'>Make Inactive</a></td>";
				}
				else
				{
					echo "<td><a href='ua_admin.php?c-".$type."=".$userRole_row[0]."&un=".$username."'>Make Active</a></td>";
				}
				echo "<td>".$userRole_row[1]."</td>";
				if ($userRole_row[2] == 1)
				{
					echo "<td>Active</td>";
				}
				else
				{
					echo "<td>Inactive</td>";
				}
				echo "</tr>";
			}
		echo "</table>";	
	}
	else
	{
		echo "<p>There was a problem retrieving the user's role assignements</p>";
	}
}
function getRegisteredUsers($db)
{	
	if ($db)
	{
		$qry_registeredUsers = "SELECT brg_username, brg_firstname, brg_lastname FROM tbl_users WHERE brg_isDeleted=0 ORDER BY brg_firstname";
		$run_qry = @mysqli_query ($db, $qry_registeredUsers);
		
		echo "<select id='user-select' name='user-select' tabindex='4'>";
			while ($user_row = mysqli_fetch_array($run_qry))
			{
				echo "<option value='".$user_row[0]."'>".$user_row[1]." ".$user_row[2]."</option>";
			}
		echo "</select>";
	}
	else
	{
		echo "<p>There was an error retrieving the user list</p>";
	}
}
###################################################################
#                          DATA Functions                         #
###################################################################
function addUser ($brg_username, $brg_email, $brg_firstname, $brg_lastname, $brg_password, $brg_role, $brg_status, $db)
{	
	if ($db)
	{
		//search for username
		$qry_searchExisting = "SELECT brg_username FROM tbl_users WHERE brg_username='$brg_username'";
		$run = @mysqli_query ($db, $qry_searchExisting);
		$num_rows = mysqli_num_rows($run);
		
		//If the user's name does not exist
		if ($num_rows == 0)
		{
			//Add user to the user table
			$qry_addUser = "INSERT INTO tbl_users (brg_username, brg_email, brg_firstname, brg_lastname, brg_password) VALUES ('$brg_username', '$brg_email', '$brg_firstname', '$brg_lastname', SHA1('$brg_password'))";	
			$add_user = @mysqli_query ($db, $qry_addUser);
		
			//Add user role
			addUserRole($brg_username, $brg_role, $brg_status, $db);
		
			//Return One for sucessful user additon
			getConfirmation($brg_username, $brg_email, $brg_firstname, $brg_lastname, $brg_role, $brg_status);
		}
		else
		{
			//Return a the form with error message
			echo "<p class='error'>This username already exists!</p>";
			getAddUserForm($_POST['user-name'], $_POST['user-email'], $_POST['user-firstname'], $_POST['user-lastname'], $_POST['user-password'], $_POST['user-role'], $_POST['user-status'], $submit_status=2, $db);
		}
	}
	else
	{	
		echo "<p class='error'>There was a problem adding the user</p>";
		getAddUserForm($brg_username, $brg_email, $brg_firstname, $brg_lastname, $brg_password, $brg_role, $brg_status, $submit_status=1, $db);
	}
}
function addUserRole ($brg_username, $brg_role, $brg_status, $db)
{
	if ($db)
	{	
		//Search to see if the user already has that role.
		$qry_searchrole = "SELECT brg_role, brg_active from tbl_userassign WHERE brg_role = '$brg_role' AND brg_userid='$brg_username'";
		$run = @mysqli_query ($db, $qry_searchrole);
		$num_rows = mysqli_num_rows($run);

		//if the query returned a result
		if($num_rows >0)
		{
			echo "<p class='error'>The user already has this role.<br/>Please edit the role from the Edit User Role(s) form</p>";
		}
		else
		{
			//Add user role
			$qry_addRole = "INSERT INTO tbl_userassign (brg_userid, brg_role, brg_active) VALUES('$brg_username', '$brg_role', '$brg_status')";
			$add_role = @mysqli_query ($db, $qry_addRole);
		}
	}
	else
	{	
		echo "<p class='error'>There was a problem adding the user role</p>";
	}
}
function setRoleStatus($brg_username, $brg_roleid, $db)
{
	if($db)
	{
		//find userrole status
		$qry_getStatus = "SELECT brg_active FROM tbl_userassign WHERE brg_id=$brg_roleid";
		$run_qry = @mysqli_query ($db, $qry_getStatus); 
		//switch the status
		while ($status_row = mysqli_fetch_array($run_qry))
		{
			if ($status_row[0] == 1)
			{
				$new_status = -1;
			}
			else
			{
				$new_status = 1;
			}
		}
	}	
	//update the record
	$qry_updateStatus = "UPDATE tbl_userassign SET brg_active='$new_status' WHERE brg_id=$brg_roleid LIMIT 1";
	$run_update = @mysqli_query ($db, $qry_updateStatus);
}
function deleteUser($username, $db)
{	
	if ($db)
	{
		//Flag User as Deleted in the database
		//Prevents the user being edited
		$qry_deleteUser = "UPDATE tbl_users SET brg_isDeleted=1 WHERE brg_username='$username'";
		//Run Query
		$run_qry = @mysqli_query ($db, $qry_deleteUser);
		if($run_qry)
		{
			//Delete User Role by username
			$qry_deleteUserRoles = "DELETE FROM tbl_userassign WHERE brg_userid='$username'";
			//Run Query
			$run = @mysqli_query ($db, $qry_deleteUserRoles);
			if ($run)
			{
				return 2;
			}
		}
		else
		{
			return 3;
		}
	}
	else
	{
		return 3;
	}
}
function deleteUserRole($brg_roleid, $db)
{
	//If the database is opened
	if($db)
	{
		//set-up query
		$deleteUserRole_query = "DELETE FROM tbl_userassign WHERE brg_id=".$brg_roleid;
		//Run Query
		$runDelete = @mysqli_query ($db, $deleteUserRole_query); // Run the query.
	}
	else
	{
		echo "<p>Ther was an error deleting user role</p>";
	}
}
function updatePassword($username, $password, $db)
{	
	if ($db)
	{
		//set-up update query
		$qry_updatePassword = "UPDATE tbl_users SET brg_password = SHA1('$password') WHERE brg_username='$username'";
		//Run Query
		//echo "<p class='error'>".$qry_updatePassword."</p>";
		$run_qry = @mysqli_query ($db, $qry_updatePassword);
		
		//If the query ran 
		if ($run_qry) 
		{ 
			echo "<p class='error' style='color:green'>".getUserName($username)."'s password has been changed</p>";
			return 2;
		}
		else
		{
			echo "<p class='error'>There was a problem modifying this data.</p>";
			return -1;
		}
	}
	else
	{
		echo "<p class='error'>There was a problem with your request</p>";
		return -1;
	}
}
function updateUser($brg_username, $brg_email, $brg_firstname, $brg_lastname, $db)
{
	if ($db)
	{
		//set-up update query
		$qry_updateUser = "UPDATE tbl_users SET brg_email = '$brg_email', brg_firstname = '$brg_firstname', brg_lastname = '$brg_lastname' WHERE brg_username='$brg_username'";
		//Run Query
		//echo "<p class='error'>".$qry_updateUser."</p>";
		$run_qry = @mysqli_query ($db, $qry_updateUser);
	}
}
?>