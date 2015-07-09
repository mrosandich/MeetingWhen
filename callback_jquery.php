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

define('SYSLOADED', "Yes Loaded");
include("common.php");

//this sets the choosen slot for a meeting for a person
if( GetQueryValue("s","","Alpha") == "q" ){
	$pguid 	= GetQueryValue("p","","AlphaNumeric",1); 
	$ts 	= GetQueryValue("t","","AlphaNumeric",1); 
	$tval 	= GetQueryValue("v","","AlphaNumeric",1); 
	
	$FoundSlot = 0;
	$MeetingId = 0;
	$PeopleId = 0;
	$PeopleGUID = 0;
	$ActionSQL = "";
	$CountRows = 0;
	
	if( $tval == "true" || $tval == 1 ){
		$tval = 1;
	}
	else{
		$tval = 0;
	}
	
	$MeetingPeople = array();
	$CheckSql = "select  MP.*, MD.slot_using_id from meetingwhen_people MP
							left join meetingwhen_people_dates MD on MP.people_id = MD.people_id and  MP.meeting_id = MD.meeting_id
							where MP.GUID='$pguid' and MP.inactive='0'";
	$result = mysql_query($CheckSql);
	while ($hash = mysql_fetch_assoc( $result )) 
	{
		$MeetingPeople[] = $hash;
	}
	
	for($x=0;$x<count($MeetingPeople);$x++){
		//copy some vars for the insert
		$MeetingId 	= $MeetingPeople[$x]['meeting_id'];
		$PeopleId 	= $MeetingPeople[$x]['people_id'];
		$PeopleGUID = $MeetingPeople[$x]['GUID'];
		
		if($MeetingPeople[$x]['slot_using_id'] == $ts){
			$FoundSlot = 1;
		}
	}
	
	if( $FoundSlot == 0 ){
		if($tval == 1 ){
			$ActionSQL = "insert into meetingwhen_people_dates 
			(meeting_id,people_id,slot_using_id,people_guid)
			values
			('$MeetingId','$PeopleId','$ts','$PeopleGUID')";
		}
	}else{
		if($tval == 0 ){
			$ActionSQL ="delete from meetingwhen_people_dates 
			where meeting_id='$MeetingId' and people_id='$PeopleId' and slot_using_id='$ts' and people_guid='$PeopleGUID'";
			
		}
	}
	if( $ActionSQL != "" ){
		mysql_query($ActionSQL);
		$CountRows = mysql_affected_rows($link);
	}
	echo $CountRows . "|" . $pguid . "_" . $ts . "|" . $tval ;
	mysql_close($link);
	exit();	
}

//This sets the optional can't go text for the meeting for a person
if( GetQueryValue("s","","Alpha") == "re" ){
	$pguid 	= GetQueryValue("p","","AlphaNumeric",1); 
	$ts 	= GetQueryValue("t","","AlphaNumeric",1);
	$tval 	= GetQueryValue("v","","AlphaNumericPunctuation",1); 
	
	$UpdateSQL  = "update meetingwhen_people set cant_go_reason='$tval' where GUID='$pguid'";
	mysql_query($UpdateSQL);
	
	$CountRows = mysql_affected_rows($link);
	echo $CountRows . "|" . $pguid . "_" . $ts . "|" . $tval ;
	
	mysql_close($link);
	exit();	
}


//This toggles all the values for the meeting for a person for the can't go option.
if( GetQueryValue("s","","Alpha") == "w" ){
	$pguid = GetQueryValue("p","","AlphaNumeric",1); 
	$ts = GetQueryValue("t","","AlphaNumeric",1);
	$tval = GetQueryValue("v","","AlphaNumeric",1); 
	if( $tval == "true" || $tval == 1 )
	{
		$tval = 1;
		$UpdateSQL  = "update meetingwhen_people set cant_go='1' where GUID='$pguid'";
	}
	else
	{
		$tval = 0;
		$UpdateSQL  = "update meetingwhen_people set cant_go='0' where GUID='$pguid'";
	}
	
	mysql_query($UpdateSQL);
	$CountRows = mysql_affected_rows($link);
	echo $CountRows . "|" . $pguid . "|" . $tval ;
	
	mysql_close($link);
	exit();	
}
?>