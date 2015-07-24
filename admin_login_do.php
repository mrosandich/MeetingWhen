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
defined('SYSLOADEDADMIN') OR die('No direct access allowed.');
if($PageState == "dologin")
{

	if(isset( $_SESSION['user_id'] ))
	{
		$UserMessageResponse = 'You are already logged in';
		$PageState = "";
	}
	elseif(!isset( $_POST['username'], $_POST['password'], $_POST['form_token'])){
		$UserMessageResponse = 'Please enter a valid username and password';
		$PageState = "login";
	}
	elseif( $_POST['form_token'] != $_SESSION['form_token']){
		$UserMessageResponse = 'Invalid form submission';
		$PageState = "login";
	}
	else
	{

		$UserName 	= GetFormValue("username", "","UserName",1);
		$Password 	= GetFormValue("password", "","Password");
		$Password 	= DataBaseCleanEscapeValue(md5( $Password ));
		$SelectSQL = "select * from meetingwhen_users where username='$UserName' and password='$Password' and is_activated='1' and is_ldap='0' limit 1";//this SQL will change if ldap is used
		
		if($UseLDAPSystem == 1){
			$LdapCheck = new cLDAP($LDAPConfig);
			$LdapCheck->initLDAPMySQL($link);
			$isLoggedIn = $LdapCheck->validateUser($UserName,GetFormValue("password", "","Password"));
			if( $isLoggedIn == 1 ){
				$SelectSQL = "select * from meetingwhen_users where username='$UserName' and is_ldap='1' and is_activated='1' limit 1";
			}
		}
		
		
		
		
		$MySqlSet = array();
		$result = mysql_query($SelectSQL);
		while ($hash = mysql_fetch_assoc( $result )) {
			$MySqlSet[] = $hash;
		}
		
		$UserId = false;
		$IsAdmin = false;
		if(count($MySqlSet) > 0 ){
			if( $MySqlSet[0]['username'] == GetFormValue("username", "","AlphaNumeric") ){
				$UserId  = $MySqlSet[0]['user_id'];
				$IsAdmin = $MySqlSet[0]['is_admin'];
			}
		}
		
		
		if($UserId == false){
                $UserMessageResponse = 'Login Failed';
				$PageState = "login";
        }
		else{
			$_SESSION['user_id'] = $UserId;
			$_SESSION['is_admin'] = $IsAdmin;
			$_SESSION['email_signature'] 	= $MySqlSet[0]['email_signature'];
			$_SESSION['email'] 				= $MySqlSet[0]['email'];
			$_SESSION['fullname'] 			= $MySqlSet[0]['fullname'];
			
			$UserMessageResponse = 'You are now logged in';
			$PageState = "";
        }
		
		
	}
}
?>