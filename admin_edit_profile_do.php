<?php
/*
	Author	: Mell Rosandich
	Date	: 6/29/2015
	email	: mell@ourace.com
	
	
	Copyright 2015 Mell Rosandich

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

*/

//user name and password configure
$UserNameMinLength = 4;
$UserNameMaxLength = 100;
$PasswordMinLength = 6;
$PasswordMaxLength = 100;


defined('SYSLOADEDADMIN') OR die('No direct access allowed.');
if($PageState == "editprofiledo" && IsLoggedIn() == true)
{

	if(!isset( $_POST['username'], $_POST['password'], $_POST['form_token'], $_POST['fullname'], $_POST['email'])){
		$UserMessageResponse = 'Please enter a valid full name, email, username, password';
		$PageState = "edituser";
	}

	elseif( $_POST['form_token'] != $_SESSION['form_token']){
		$UserMessageResponse = 'Invalid form submission';
		$PageState = "edituser";
	}

	elseif (strlen( $_POST['username']) > $UserNameMaxLength || strlen($_POST['username']) < $UserNameMinLength){
		$UserMessageResponse = 'Incorrect Length for Username must be between $UserNameMinLength and $UserNameMaxLength characters';
		$PageState = "edituser";
	}

	elseif( $_POST['password'] != "" && ( strlen($_POST['password']) < $PasswordMinLength || strlen($_POST['password']) > $PasswordMaxLength )){
		$UserMessageResponse = 'Incorrect Length for Password must be between $PasswordMinLength and $PasswordMaxLength characters';
		$PageState = "edituser";
	}
	else
	{
		//Clear token to prevent reposts.
		$_SESSION['form_token'] = md5( uniqid('auth', true) );
		
		$FullName 	= GetFormValue("fullname", "","AlphaNumeric",1);
		$Email 		= GetFormValue("email", "","Email",1);
		$Password 	= GetFormValue("password", "","Password");
		$Password 	= DataBaseCleanEscapeValue( md5( $Password ));
		$EmailSignature = GetFormValue("email_signature", "","AlphaNumericPunctuation",1);
		
		$SelectedUserid = $_SESSION['user_id'];
		
		$UpdateSQL = "update  meetingwhen_users set 
			fullname='$FullName',
			email='$Email',";
		if( GetFormValue("password", "","AlphaNumeric") != "" ){
			$UpdateSQL .= "password='$Password',";
		}
		$UpdateSQL .= "email_signature='$EmailSignature'
			where user_id='$SelectedUserid'";
			
		mysql_query($UpdateSQL);
		$RowsUpdatedCnt = mysql_affected_rows($link);
		if( $RowsUpdatedCnt == 0 || $RowsUpdatedCnt == false ){
			$PageState = "profile";
			$UserMessageResponse = 'There was an error processing your request. Please try again.';
		}
		else{
			$PageState = "profile";
			$UserMessageResponse = 'Your profile was updated:';
			
			//reset the session vars because they might have changed.
			$_SESSION['email_signature'] 	= GetFormValue("email_signature", "","AlphaNumericPunctuation");
			$_SESSION['email'] 				= GetFormValue("email", "","Email");
			$_SESSION['fullname'] 			= GetFormValue("fullname", "","AlphaNumeric");
		}
	}
}
?>