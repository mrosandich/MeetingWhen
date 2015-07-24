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
if( $PageState == "savenew" && GetFormValue("sub","","Alpha") == "yes") //pagestate is savenew and submitted is yes
{
	
	//meeting_main will need guid
	//meeting_person each user will need GUID and the new meeting_id
	
	
	if( $_POST['form_token'] != $_SESSION['form_token']){
		$UserMessageResponse = 'Invalid form submission';
		$PageState = "new";
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
		$EmailSent 		= GetFormValue("doemail","no","Alpha",1);
		$EmailUsed 		= GetFormValue("senderemail","","AlphaNumericPunctuation",1);
		$EmailSig 		= GetFormValue("emailsig","","AlphaNumericPunctuation",1);
		$EMailFullName 	= GetFormValue("sendername","","AlphaNumeric",1);
		
		$GUID			= uniqid() . uniqid(); 
		
		//Do main meeting and times/dates
		$SqlDates = array();
		$FormDate = $_POST['t_date'];
		$FormTime = $_POST['t_time'];
		for($x=0;$x<count($FormDate);$x++){
			
			//Clean this post.
			$TempDate = RegClean($FormDate[$x],"Date");
			$TempTime = RegClean($FormTime[$x],"Time");
			if( $TempDate != "" && $TempTime != "" && substr_count($TempDate,"/") == 2 && substr_count($TempTime,":") == 1 ){
				$SqlDates[] = GetSQLTime($TempDate,$TempTime);
			}
		}
		
		$InsertMeetingMain = "
		INSERT INTO `meetingwhen_main`
		(`title`,`description`,`is_active`,`user_id`,`GUID`,email_sent,email_fullname,email_email,email_signature)
		VALUES
		('$Title','$Description','1','".$_SESSION['user_id']."','$GUID','$EmailSent ','$EMailFullName','$EmailUsed','$EmailSig');";
		
		mysql_query($InsertMeetingMain);
		$meeting_id = mysql_insert_id($link);
		
		for($x=0;$x<count($SqlDates);$x++){
			
			$InsertMeetingTimes = "Insert into meetingwhen_main_dates 
			(meeting_id,user_id,date_and_time)
			values
			('$meeting_id','".$_SESSION['user_id']."','".$SqlDates[$x]."' );";
			mysql_query($InsertMeetingTimes);
		}
		
		
		
		//Do users for meeting
		$FormNames = $_POST['n_'];
		$FormEmails = $_POST['ae_'];
		for($x=0;$x<count($FormNames);$x++){
			
			//Clean this post.
			$TempName = RegClean($FormNames[$x],"AlphaNumeric");
			$TempEmail = RegClean($FormEmails[$x],"Email");
			if( $TempName != "" && $TempEmail != "" && substr_count($TempEmail,"@") == 1){
				//Insert recipient if the email and name are not blank
				$InsertUserSQL = GenUserSQL($TempName,$TempEmail,$meeting_id,$_SESSION['user_id']);
				if( $InsertUserSQL != "" ){
					mysql_query($InsertUserSQL);
				}
			}
		}
		
		$UserMessageResponse = " Your link is: <a href=\"" . $MeetingWhenIndexPath . "?m=$GUID&p=0&s=genDetails\">" . $MeetingWhenIndexPath . "?m=$GUID&p=0&s=genDetails</a>";
		$PageState = "my";
		
		if( GetFormValue("doemail","","Alpha")=='yes') 
		{
			SendEmailClient( GetFormValue("sendername","","AlphaNumeric"), GetFormValue("senderemail","","Email"), $meeting_id, GetFormValue("emailsig","","AlphaNumeric"),GetFormValue("title","","AlphaNumeric"),$GUID );
		}
	}
}// end save new

?>