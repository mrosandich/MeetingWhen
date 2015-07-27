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

/*-----------------------------------------------------------------------------------------------------
This page is used if you don't want your LDAP directly exposed on your web server.
You will need a web server exposed to the internet, a web server not exposed but has access to the your LDAP server.

Your live web server will send curl messages with user/pass over https to the none public web server for authentication.
Using this is method is very rare. 
-----------------------------------------------------------------------------------------------------*/
error_reporting(0);
@ini_set('display_errors', 0);

define('SYSLOADED', "Yes Loaded");
include("common.php");
include("class_ldap.php");

$UserName = urldecode( GetFormValue("username","","") );
$Password = urldecode(GetFormValue("password","","") );

$LdapCheck = new cLDAP($LDAPConfig);
$isLoggedIn = $LdapCheck->validateUser($UserName,$Password);

echo "[
	{\"username\":\"" . $LdapCheck->user_username .  "\",
	\"email\":\"" . $LdapCheck->user_email .  "\",
	\"fullname\":\"" . $LdapCheck->user_namefull .  "\",
	\"namelast\":\"" . $LdapCheck->user_namelast .  "\",
	\"namefirst\":\"" . $LdapCheck->user_namefirst .  "\",
	\"userdn\":\"" . $LdapCheck->user_userdn .  "\",
	\"loggedin\":\"" . $LdapCheck->user_loggedin .  "\",
	\"errormessage\":\"" . $LdapCheck->app_error_message  . "\"}
	]";

?>