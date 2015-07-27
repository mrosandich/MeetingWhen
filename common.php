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
defined('SYSLOADED') OR die('No direct access allowed.');

//set up the database here
$link = mysql_connect('localhost', 'UserName', 'Password');
$db_selected = mysql_select_db('Database', $link);


//The full path to the client page,something like this: /meeting/index.php
$MeetingWhenCallBackPagePath = "/meeting/callback_jquery.php";

//this will be used in the email for the person to click and accept times
$MeetingWhenIndexPath = "http://YourWebsitenameHere.com/meeting/index.php";

//if you have a path please specify
//some thing like: /images/     or   /meet/images/
$MeetingWhenImagePath = "/images/";

//user name and password configure
$UserNameMinLength = 4;
$UserNameMaxLength = 100;
$PasswordMinLength = 6;
$PasswordMaxLength = 100;

//Use LDAP for authentication
$UseLDAPSystem = 0;
$LDAPConfig['app_ldap_ip'] 				= "192.168.1.100";
$LDAPConfig['app_ldap_port'] 			= 389;
$LDAPConfig['app_user'] 				= 'CN=MyADSearchAccount,OU=Austin,DC=ourace,DC=com';
$LDAPConfig['app_pass'] 				= 'Com!plex%Passw0rd';
$LDAPConfig['app_search_user_base'] 	= "OU=Austin,DC=ourace,DC=com";
$LDAPConfig['app_use_activedirectory'] 	= 1;//set to 0 if you want to use a a normal search expression

//these are used for another way to auth when you don't want your LDAP access on the same server or for something custom.
//An example of this can be seen in the file class_ldap validateUserRemote($username,$password) where it calls a curl to get the credentials
$LDAPConfig['app_use_alt_method'] 		= 0; //set to 1 to use the validateUserRemote($username,$password) function
$LDAPConfig['app_use_alt_method_url'] 	= "http://YourWebsitenameHere.com/meeting/service_ldap_remote.php"; //some remote curl page with auth

function GetFormValue($Key,$DefaultValue,$RegCleanType,$EscapeMySql = 0){
	$ReturnValue = $DefaultValue;

	if(array_key_exists($Key,$_POST)){
		$ReturnValue = $_POST[$Key];
	}
	
	$ReturnValue = RegClean($ReturnValue,$RegCleanType);
	
	if($EscapeMySql == 1 ){
		$ReturnValue = DataBaseCleanEscapeValue($ReturnValue);
	}
	
	return $ReturnValue;
}


function GetQueryValue($Key,$DefaultValue,$RegCleanType,$EscapeMySql = 0){
	$ReturnValue = $DefaultValue;

	if(array_key_exists($Key,$_GET))
	{
		$ReturnValue = $_GET[$Key];
	}
	
	$ReturnValue = RegClean($ReturnValue,$RegCleanType);
	
	if($EscapeMySql == 1 ){
		$ReturnValue = DataBaseCleanEscapeValue($ReturnValue);
	}
	return $ReturnValue;
}

function DataBaseCleanEscapeValue($InValue){
	return mysql_real_escape_string($InValue);
}


function RegClean($InValue,$RegCleanType){
	
	if( $RegCleanType == "AlphaNumeric" ){
		$InValue = preg_replace("/[^a-zA-Z0-9\s\.\-]/", "", $InValue);
	}
	
	if( $RegCleanType == "UserName" ){
		$InValue = preg_replace("/[^a-zA-Z0-9]/", "", $InValue);
	}
	
	if( $RegCleanType == "Email" ){
		$InValue = preg_replace("/[^a-zA-Z0-9\s\.\_\@]/", "", $InValue);
	}
	if( $RegCleanType == "Password" ){
		
	}
	
	if( $RegCleanType == "Numeric" ){
		$InValue = preg_replace("/[^0-9\s\.\-]/", "", $InValue);
	}
	
	if( $RegCleanType == "Alpha" ){
		$InValue = preg_replace("/[^a-zA-Z\s]/", "", $InValue);
	}
	
	if( $RegCleanType == "AlphaNumericPunctuation" ){
		$InValue = preg_replace("/[^a-zA-Z0-9\s\'\.\"\?\!\@\*]/", "", $InValue);
	}
	
	if( $RegCleanType == "NumericDate" ){
		$InValue = preg_replace("/[^0-9\/]/", "", $InValue);
	}
	if( $RegCleanType == "Time" ){
		$InValue = preg_replace("/[^0-9a-z\:]/", "", $InValue);
	}
	return $InValue;
}

function IsLoggedIn(){
	if(isset( $_SESSION['user_id'] )){
		return true;
	}
	return false;
}

function IsLoggedInAndAdmin(){
	if(isset( $_SESSION['user_id'] )){
		if($_SESSION['is_admin'] == 1){
			return true;
		}
	}
	return false;
}


function SendEmailClient($FromName,$FromEmail,$MeetingId,$EmailSig,$MeetingTitle,$MeetingGuid){
	
	$EmailsSQL = "select * from meeting_people where meeting_id='$MeetingId' and inactive='0'";
	$result = mysql_query($EmailsSQL);
	$MeetingPeople = array();
	while ($hash = mysql_fetch_assoc( $result )) 
	{
		$MeetingPeople[] = $hash;
	}
	
	for($x=0;$x<count($MeetingPeople);$x++){
		$ToAddress = $MeetingPeople[$x]['email']; 
		$ToName = $MeetingPeople[$x]['name']; 
		$ToGUID = $MeetingPeople[$x]['GUID']; 
		$Subject = $MeetingTitle;
		$message = "Hello $ToName, <br />";
		$message .= "To help facilitate our meeting please go the below URL and select time(s) you are available. Please don't forward this email because each invite is unique and the below URL represents your response.<br /><br />";
		$message .= "<a href=\"" . $MeetingWhenIndexPath . "?m=$MeetingGuid&p=$ToGUID\">" . $MeetingWhenIndexPath . "?m=$MeetingGuid&p=$ToGUID</a><br />";
		
		if( $EmailSig != "" )
		{
			$message .= "<br /><br />";
			$message .= str_replace("\r\n","<br />",$EmailSig);
		}
		
		$headers = 'From: ' . $FromEmail . "\r\n" .'Reply-To: ' . $FromEmail  . "\r\n" .'X-Mailer: PHP/' . phpversion() . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		mail($ToAddress, $Subject, $message, $headers);
		sleep(1);
	}
}

function GenUserSQL($name,$email,$meeting_id,$user_id){
	if( $name != "" && $email != "" && $meeting_id != "" ){
		$NewGUID 	=  uniqid() . uniqid();
		$Tempname	=	mysql_real_escape_string($name);
		$TempEmail	=	mysql_real_escape_string($email);
		return "INSERT INTO `meetingwhen_people`(`meeting_id`,`GUID`,`name`,`email`,`is_done`,`inactive`,user_id)VALUES('$meeting_id','$NewGUID','$Tempname','$TempEmail','0','0','$user_id');";
	}
	return "";
}

function GetSQLTime($Datepart,$TimePart){
		if( $Datepart == "" || $TimePart == "" ){return "";}
		$DateParts = split("/",$Datepart);
		$RetVal = $DateParts[2] . "-" . $DateParts[0] . "-" . $DateParts[1];
		
		$TempTime = str_replace("am","",$TimePart);
		$TempTime = str_replace("pm","",$TempTime);
		$TimeParts = split(":",$TempTime);
		if( strpos($TimePart,"pm") > 0 && substr($TimePart,0,2) !="12")
		{
			$TimeParts[0] = ($TimeParts[0]*1) +12;
		}
		$RetVal = $RetVal . " " . $TimeParts[0] . ":" . $TimeParts[1] . ":00";
		return mysql_real_escape_string($RetVal);
}
?>