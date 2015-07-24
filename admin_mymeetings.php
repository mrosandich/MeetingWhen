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
if($PageState == "my" && IsLoggedIn() == true)
{

	$MySessionId = $_SESSION['user_id'];
	$MeetingSet = array();
	$DatesSet = array();
	$MeetingPeople = array();
	$MeetingIds = "";
	//get all the meetings for this user
	$SelectSQL = "select * from meetingwhen_main where user_id='$MySessionId' order by meeting_id desc";
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MeetingSet[] = $hash;
		$MeetingIds .= "'" . $hash['meeting_id'] . "',";
	}
	if(count($MeetingSet)> 0 ){
		$MeetingIds = substr($MeetingIds,0,-1);
		
		$SelectMeetingData = "select  MP.*, MD.slot_using_id from meetingwhen_people MP
								left join meetingwhen_people_dates MD on MP.people_id = MD.people_id and  MP.meeting_id = MD.meeting_id
								where MP.meeting_id in ($MeetingIds) and MP.inactive='0'";
		$result = mysql_query($SelectMeetingData);
		while ($hash = mysql_fetch_assoc( $result )) 
		{
			$MeetingPeople[] = $hash;
		}

		//get all meetings joined to dates
		$SelectMeetDatesJoin = "SELECT * FROM `meetingwhen_main` MM
			join `meetingwhen_main_dates` MD on MM.meeting_id = MD.meeting_id
			where MM.user_id='$MySessionId'
			order by MM.meeting_id, MD.date_and_time";
		$result = mysql_query($SelectMeetDatesJoin);
		while ($hash = mysql_fetch_assoc( $result )) {
			$DatesSet[] = $hash;
		}
		
		echo "<table id=\"MyMeetings\"><tr><th>Title</th><th>Earliest&nbsp;Time</th><th>Latest&nbsp;Time</th><th>Is&nbsp;Active</th><th>Invited</th><th>Can't&nbsp;Go</th><th>Meeting&nbsp;URL</th><th>-</th></tr>\n";
		for($x=0;$x<count($MeetingSet);$x++){
			
			$TempClass = new MeetingClass();
			$TempClass->Load($MeetingSet[$x]['meeting_id'],$MeetingSet,$DatesSet,$MeetingPeople);
			echo "<tr>\n";
			echo "<td class=\"td150\">" . $MeetingSet[$x]['title'] . "</td>";
			echo "<td class=\"td150\">" . $TempClass->GetMinDate() . "</td>";
			echo "<td class=\"td150\">" . $TempClass->GetMaxDate() . "</td>";
			echo "<td>" . ($MeetingSet[$x]['is_active']==1 ?'Yes':'No') . "</td>";
			echo "<td>" . $TempClass->GetPeopleInvited() . "</td>";
			echo "<td>" . $TempClass->GetCantGo() . "</td>";

			echo "<td>" . $MeetingWhenIndexPath . "?m=" . $MeetingSet[$x]['GUID'] . "</td>";
			echo "<td>[<a href=\"admin.php?ps=myedit&id=" . $MeetingSet[$x]['meeting_id'] . "\">edit</a>]</td>";
			echo "</tr>\n";
		}
		echo "</table>";
	}//end count($MeetingSet)> 0
	else{
		echo "There are no meetings listed.";
	}
}
?>