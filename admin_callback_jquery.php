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

define('SYSLOADED', "YES");
include("common.php");
session_start();

if( IsLoggedIn() == true ){
	
	//Auto Populate the participants names
	if( GetQueryValue("s","","Alpha") == "s" ){

		$term = GetQueryValue("term","","AlphaNumeric",1);
		$jsonString = "[";
		$CheckSql = "SELECT name,email FROM tachc_org.meetingwhen_people where user_id='" . $_SESSION['user_id'] . "' and name like '%$term%' group by name,email order by name;";
		$result = mysql_query($CheckSql);
		while ($row = mysql_fetch_assoc( $result )) 
		{
			 $jsonString .= "{\"id\":\"" . $row['name'] . "\",\"label\":\"" .  $row['name'] . "  (" .  $row['email'] . ")\",\"value\":\"" . $row['email'] . "\"},";
		}
		mysql_close($link);
		
		$jsonString = substr($jsonString,0,-1);
		$jsonString .= "]";
		echo $jsonString;
		exit();	
	}else{
		echo "[]";
	}
}//end logged==true
else{
	echo "[]";
}

?>