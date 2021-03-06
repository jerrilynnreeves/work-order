// JavaScript Document
$(document).ready(function()
{
	//Hide all containers with the hidden class
	$('div.hidden-container').hide();
	//User Admin Functions
	$('#add-user.app-title').click(function() {
	$('#addUserForm.hidden-container').toggle('slow')
	.siblings('div.hidden-container:visible').toggle('slow')
	});
	//Change Password
	$('#change-password.app-title').click(function() {
	$('#changePwdForm.hidden-container').toggle('slow')
	$('#addUserForm.hidden-container').hide('slow');		//Hide Add
															//OPENED Change Password
	$('#editUserForm.hidden-container').hide('slow');		//Hide Edit
	$('#editUserRoleForm.hidden-container').hide('slow');	//Hide Add/Edit User Role
	$('#deleteUserForm.hidden-container').hide('slow');		//Hide Delete
	});
	//Edit User
	$('#edit-user.app-title').click(function() {
	$('#editUserForm.hidden-container').toggle('slow')
	$('#addUserForm.hidden-container').hide('slow')			//Hide Add
	$('#changePwdForm.hidden-container').hide('slow');		//Hide Password
															//OPENED Edit not Done
	$('#editUserRoleForm.hidden-container').hide('slow');	//Hide Add/Edit User Role
	$('#deleteUserForm.hidden-container').hide('slow');		//Hide Delete
	});
	//Add/Edit User Role
	$('#editrole-user.app-title').click(function() {
	$('#editUserRoleForm.hidden-container').toggle('slow')
	$('#addUserForm.hidden-container').hide('slow')			//Hide Add
	$('#changePwdForm.hidden-container').hide('slow');		//Hide Password
	$('#editUserForm.hidden-container').hide('slow');		//Hide Edit
															//OPENED Add/Edit Role
	$('#deleteUserForm.hidden-container').hide('slow');		//Hide Delete
	});
	//Delete User
	$('#delete-user.app-title').click(function() {
	$('#deleteUserForm..hidden-container').toggle('slow')
	$('#addUserForm.hidden-container').hide('slow');		//Hide Add
	$('#changePwdForm.hidden-container').hide('slow');		//Hide Password
	$('#editUserForm.hidden-container').hide('slow');		//Hide Edit
	$('#editUserRoleForm.hidden-container').hide('slow');	//Hide Add/Edit User Role
															//OPENED Delete
	});
	//Work Order List
	$('tr.wo-detail-even').hide();
	$('tr.wo-detail-odd').hide();
	$('.view').click(function() {
	var i = $('span').index(this)+1;
	var i = i * 3-1;
	var test = 'tr:eq('+i+')';
	$(test).toggle();
	});
	
});
