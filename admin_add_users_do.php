<?php
/*
	Author	: Mell Rosandich
	Date	: 6/29/2015
	email	: mell@ourace.com
	website : www.ourace.com
	sample	: http://www.ourace.com/code_examples/meetwhen/index.php?m=559d86231973e559d86231977a
	
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
defined('SYSLOADEDADMIN') OR die('No direct access allowed.');
if($PageState == "donewuser" && IsLoggedInAndAdmin() == true)
{

	if(!isset( $_POST['username'], $_POST['password'], $_POST['form_token'], $_POST['fullname'], $_POST['email'])){
		$UserMessageResponse = 'Please enter a valid full name, email, username, password';
		$PageState = "newuser";
	}

	elseif( $_POST['form_token'] != $_SESSION['form_token']){
		$UserMessageResponse = 'Invalid form submission';
		$PageState = "newuser";
	}

	elseif (strlen( $_POST['username']) > $UserNameMaxLength || strlen($_POST['username']) < $UserNameMinLength){
		$UserMessageResponse = 'Incorrect Length for Username must be between $UserNameMinLength and $UserNameMaxLength characters';
		$PageState = "newuser";
	}

	elseif( $_POST['password'] != "" && ( strlen($_POST['password']) < $PasswordMinLength || strlen($_POST['password']) > $PasswordMaxLength )){
		$UserMessageResponse = 'Incorrect Length for Password must be between $PasswordMinLength and $PasswordMaxLength characters';
		$PageState = "newuser";
	}
	else
	{
		//Clear token to prevent reposts.
		$_SESSION['form_token'] = md5( uniqid('auth', true) );
		
		$FullName 	= GetFormValue("fullname", "","AlphaNumeric",1);
		$Email 		= GetFormValue("email", "","Email",1);
		$UserName 	= GetFormValue("username", "","AlphaNumeric",1);
		$IsAdmin 	= GetFormValue("is_admin", "0","Numeric");
		$Password 	= GetFormValue("password", "","Password");
		$Password 	= DataBaseCleanEscapeValue( md5( $Password ));

		$InsertSQL = "INSERT INTO meetingwhen_users (fullname,email,username,password,is_admin,is_activated ) VALUES ('$FullName','$Email','$UserName', '$Password','$IsAdmin','1' )";
		mysql_query($InsertSQL);
		$InsertId = mysql_insert_id($link);
		if( $InsertId == 0 || $InsertId == false ){
			$PageState = "newuser";
			$UserMessageResponse = 'There was an error processing your request. Please try again.';
		}
		else{
			$PageState = "users";
			$UserMessageResponse = 'The new user was created:' . GetFormValue("fullname", "","AlphaNumeric");
		}
	}
}
?>