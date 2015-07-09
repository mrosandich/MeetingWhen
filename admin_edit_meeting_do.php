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
if( $PageState == "saveedit" && GetFormValue("sub","","Alpha") == "yes") //pagestate is savenew and submitted is yes
{
	
	//meeting_main will need guid
	//meeting_person each user will need GUID and the new meeting_id
	
	
	if( $_POST['form_token'] != $_SESSION['form_token']){
		$UserMessageResponse = 'Invalid form submission';
		$PageState = "myedit";
	}
	elseif(1==2){
		
	}
	else
	{
	
	
		//Clear token to prevent reposts.
		$_SESSION['form_token'] = md5( uniqid('auth', true) );
		
		//make the time parts
		$Description 	= GetFormValue("description","","AlphaNumericPunctuation",1);
		$Title 			= GetFormValue("title","","AlphaNumericPunctuation",1);
		$MeetingId		= GetFormValue("meeting_id","","Numeric",1);
		$IsActive		= GetFormValue("is_active","0","Numeric",1);
		$UserId			= $_SESSION['user_id'];
		$GUID			= GetFormValue("guid","0","AlphaNumeric");
		
		$UpdateMeetingMain = "update `meetingwhen_main`
		set `title`='$Title' ,`description`='$Description' ,`is_active`='$IsActive'
		where meeting_id='$MeetingId' and user_id='$UserId'";
		
		mysql_query($UpdateMeetingMain);
		
		
		
		//Do main meeting and times/dates
		$FormDate = $_POST['t_date'];
		$FormTime = $_POST['t_time'];
		$FormDBId = $_POST['datesid'];
		for($x=0;$x<count($FormDate);$x++){
			
			//Clean this post.
			$TempDate = RegClean($FormDate[$x],"Date");
			$TempTime = RegClean($FormTime[$x],"Time");
			$TempID	  = RegClean($FormDBId[$x],"Numeric");
			
			if( $TempID == "0" ){
				if( $TempDate != "" && $TempTime != "" && substr_count($TempDate,"/") == 2 && substr_count($TempTime,":") == 1 ){
					$TempSQLDate = GetSQLTime($TempDate,$TempTime);
					$InsertMeetingTimes = "Insert into meetingwhen_main_dates 
					(meeting_id,user_id,date_and_time)
					values
					('$MeetingId','$UserId','".$TempSQLDate."' );";
					mysql_query($InsertMeetingTimes);
				}
			}
			else
			{
				if( $TempDate != "" && $TempTime != "" && substr_count($TempDate,"/") == 2 && substr_count($TempTime,":") == 1 ){
					$TempSQLDate = GetSQLTime($TempDate,$TempTime);
					$UpdateMeetingTimes = "update meetingwhen_main_dates set date_and_time = '$TempSQLDate'
					where meeting_id='$MeetingId' and user_id='$UserId' and dates_id='$TempID'";
					mysql_query($UpdateMeetingTimes);
				}
			}
		}
		
		
		
		//Do users for meeting
		$FormNames = $_POST['n_'];
		$FormEmails = $_POST['ae_'];
		$FormDBId = $_POST['peopleid'];
		for($x=0;$x<count($FormNames);$x++){
			
			//Clean this post.
			$TempName 	= RegClean($FormNames[$x],"AlphaNumeric");
			$TempEmail 	= RegClean($FormEmails[$x],"Email");
			$TempID	  	= RegClean($FormDBId[$x],"Numeric");
			
			if( $TempName != "" && $TempEmail != "" && substr_count($TempEmail,"@") == 1){
				
				//Insert recipient if the email and name are not blank
				if( $TempID == "0" ){
					$InsertUserSQL = GenUserSQL($TempName,$TempEmail,$MeetingId,$UserId);
					if( $InsertUserSQL != "" ){
						mysql_query($InsertUserSQL);
					}
				}else{
					$UpdatePerson = "update meetingwhen_people set name='$TempName', email='$TempEmail' where people_id='$TempID' and user_id='$UserId' and meeting_id='$MeetingId'";
					mysql_query($UpdatePerson);
				}
			}
		}
		
		$UserMessageResponse = " Your link is: <a target=\"new_\" href=\"" . $MeetingWhenIndexPath . "?m=$GUID&p=0&s=genDetails\">" . $MeetingWhenIndexPath . "?m=$GUID&p=0&s=genDetails</a>";
		$PageState = "myedit";
		
		if( GetFormValue("doemail","","Alpha")=='yes') 
		{
			SendEmailClient( GetFormValue("sendername","","AlphaNumeric"), GetFormValue("senderemail","","Email"), $meeting_id, GetFormValue("emailsig","","AlphaNumeric"),GetFormValue("title","","AlphaNumeric"),$GUID );
		}
	}
}// end save new

?>