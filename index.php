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
define('SYSLOADED', "Yes Loaded");
include("common.php");
include("class_meeting.php");

$HTTPPrefix = "http://";
if(isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] == "on") {
        $HTTPPrefix = "https://";
    }
}

?>
<!DOCTYPE html>
<head>
<title>Meet When?</title>
<meta name="viewport" content="width=device-width">
<script src="<?php echo $HTTPPrefix;?>code.jquery.com/jquery-1.10.2.js"></script>
<link rel="stylesheet" href="meetwhen_client.css">
<script>
function SendMsg(m,pguid,ts,cntrl)
{
	var pullurl = "<?php echo $MeetingWhenCallBackPagePath; ?>?s=q&p=" + pguid + "&m=" + m + "&t=" + ts + "&v=" + cntrl.checked  + "&rn=" + Math.floor(Math.random() * 100000); 
	try {
		if (pullurl) {
			jQuery.get(pullurl, {},
			function (data) {
				ConfirmChange(data);
			}
			, 'html');
		}
	}
	catch (e) { alert("ops: " + e.Message); }
}
function SendCant(m,pguid,cntrl)
{
	var pullurl = "<?php echo $MeetingWhenCallBackPagePath; ?>?s=w&p=" + pguid + "&m=" + m +  "&v=" + cntrl.checked  + "&rn=" + Math.floor(Math.random() * 100000); 
	try {
		if (pullurl) {
			jQuery.get(pullurl, {},
			function (data) {
				ConfirmChangeCant(data);
			}
			, 'html');
		}
	}
	catch (e) { alert("ops: " + e.Message); }
}

function SaveReason(m,pguid,cntrl)
{
	var Reason=encodeURIComponent($("#rcanttext").val());
	var pullurl = "<?php echo $MeetingWhenCallBackPagePath; ?>?s=re&p=" + pguid + "&m=" + m +  "&v=" + Reason  + "&rn=" + Math.floor(Math.random() * 100000); 
	try {
		if (pullurl) {
			jQuery.get(pullurl, {},
			function (data) {
				ConfirmReason(data);
			}
			, 'html');
		}
	}
	catch (e) { alert("ops: " + e.Message); }
}

function ConfirmReason(data)
{
	$("#trcan").css("background-color", "green");
}

function ConfirmChangeCant(DataIn)
{
	var MyData = DataIn.split("|");
	if( MyData[0] == 1 ){
		if(  MyData[2] == "1" ){
			$("#" + MyData[1] + "_name").css("background-color", "#F33");
			for(var x=1;x<16;x++){
				$("#T_" + MyData[1] + "_" + x).attr('checked', false);
				$("#T_" + MyData[1] + "_" + x).attr('disabled', true);
				$("#" + MyData[1] + "_" + x).css("background-color", "#999");
			}
			$("#trcan").show();
		}
		else{
			$("#" + MyData[1] + "_name").css("background-color", "#fff");
			for(var x=1;x<16;x++){
				$("#T_" + MyData[1] + "_" + x).attr('disabled', false);
				$("#" + MyData[1] + "_" + x).css("background-color", "#fff");
			}
			$("#trcan").hide();
		}
	}
	else{
		alert("Error Saving changes");
	}
}
function ConfirmChange(DataIn)
{
	var MyData = DataIn.split("|");
	if( MyData[0] == 1 ){
		if(  MyData[2] == "1" ){
			$("#" + MyData[1]).css("background-color", "#d1f3d1");
		}
		else{
			$("#" + MyData[1]).css("background-color", "yellow");
		}
	}
	else{
		alert("Error Saving changes");
	}
}
</script>
</head>
<body>
<br /><br />
<center>
<?php


//These 2 GUIDs are generated in the admin tool and represent the meeting and the person.
$MeetingGUID = GetQueryValue("m","","AlphaNumeric");
$MeetingGUIDEsaped = GetQueryValue("m","","AlphaNumeric",1);
$PersonGUID  = GetQueryValue("p","","AlphaNumeric"); 


$MeetingMain = array();
$MeetingPeople = array();
$MeetingPeopleDates = array();
$CantGoUser = "0";
$CantGoReason = "";

$SelectMeetingData = "select * from meetingwhen_main where GUID='$MeetingGUIDEsaped' and is_active='1'";
$result = mysql_query($SelectMeetingData);
while ($hash = mysql_fetch_assoc( $result )) 
{
	$MeetingMain[] = $hash;
}
if( count($MeetingMain) > 0 )
{


	$Meeting_ID = $MeetingMain[0]['meeting_id'];
	$SelectMeetingData = "select * from meetingwhen_people where meeting_id='$Meeting_ID' and inactive='0'";
	$result = mysql_query($SelectMeetingData);
	while ($hash = mysql_fetch_assoc( $result )) 
	{
		$MeetingPeople[] = $hash;
	}

	
	//get all meetings joined to dates
	$MeetingMainDatesSet = array();
	$SelectMeetDatesJoin = "SELECT * FROM `meetingwhen_main` MM
		join `meetingwhen_main_dates` MD on MM.meeting_id = MD.meeting_id
		where MM.meeting_id='$Meeting_ID'
		order by MM.meeting_id, MD.date_and_time";
	$result = mysql_query($SelectMeetDatesJoin);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MeetingMainDatesSet[] = $hash;
	}
	
	
	
	$SelectMeetingDates = "select  MP.*, MD.slot_using_id from meetingwhen_people MP
							left join meetingwhen_people_dates MD on MP.people_id = MD.people_id and  MP.meeting_id = MD.meeting_id
							where MP.meeting_id='$Meeting_ID' and MP.inactive='0'";
	$result = mysql_query($SelectMeetingDates);
	while ($hash = mysql_fetch_assoc( $result )) 
	{
		$MeetingPeopleDates[] = $hash;
	}
	
	

	echo "<h2>" . $MeetingMain[0]['title']. "</h2>";
	echo "<b>" . $MeetingMain[0]['description']. "</b><br />". "<br />";

	
	echo "<table id=\"maintbl\">";
	echo "<tr>";
		echo "<td>Date: ".date("Y",strtotime($MeetingMainDatesSet[0]['date_and_time']))."</td>";
		
		
		for($il=0;$il < count($MeetingMainDatesSet);$il++)
		{
			$currentspan = CalcCols($MeetingMainDatesSet,$il);
			if( $MeetingMainDatesSet[$il]['date_and_time'] != "" && $MeetingMainDatesSet[$il]['date_and_time'] != "0000-00-00 00:00:00")
			{
				echo "<td align=\"center\" colspan=\"$currentspan\"><b>" . date("D M j\<\s\u\p\>S\<\/\s\u\p\>",strtotime($MeetingMainDatesSet[$il]['date_and_time'])) . "</b></td>"; 
			}
			if( $currentspan >0 )
			{
				$il = $il + ($currentspan-1);
			}
		}
		
	echo "</tr>";
	
	echo "<tr>";
		echo "<td>" ; 
		echo "&nbsp;";
		echo "</td>" ; 
		for($il=0;$il < count($MeetingMainDatesSet);$il++)
		{
			if( $MeetingMainDatesSet[$il]['date_and_time'] != "" && $MeetingMainDatesSet[$il]['date_and_time'] != "0000-00-00 00:00:00")
			{
				echo "<td align=\"center\" >" . date("g:iA",strtotime($MeetingMainDatesSet[$il]['date_and_time'])) . "</td>"; 
			}
		}
	echo "</tr>";
	
	
	$Isparticapant = 0;
	for($x=0;$x<count($MeetingPeople);$x++)
	{
		echo "<tr>";
		if($MeetingPeople[$x]['cant_go'] == "1")
		{
			$TempTitle = htmlentities($MeetingPeople[$x]['cant_go_reason']);
			echo "<td title=\"$TempTitle\" id=\"" . $MeetingPeople[$x]['GUID'] . "_name\" class=\"cantgo\">" ; 
		}
		else
		{
			echo "<td id=\"" . $MeetingPeople[$x]['GUID'] . "_name\" >" ; 
		}
		echo $MeetingPeople[$x]['name'];
		echo "</td>" ; 
		for($il=0;$il < count($MeetingMainDatesSet);$il++)
		{
			if( $MeetingMainDatesSet[$il]['date_and_time'] != "" && $MeetingMainDatesSet[$il]['date_and_time'] != "0000-00-00 00:00:00")
			{
				$uid = $MeetingPeople[$x]['GUID'] . "_" . $MeetingMainDatesSet[$il]['dates_id'];
				
				
				if($MeetingPeople[$x]['GUID'] != $PersonGUID)
				{
					
					if($MeetingPeople[$x]['cant_go']=="1")
					{
						echo "<td class=\"cantgorow\" align=\"center\">-</td>";
					}
					else
					{
						if( PersonHasSlotId($MeetingPeopleDates,$MeetingMainDatesSet[$il]['dates_id'],$MeetingPeople[$x]['people_id'] ) == false )
						{
							echo "<td align=\"center\">-</td>";
							//echo "<td align=\"center\">" . $MeetingMainDatesSet[$il]['dates_id'] . "," . $MeetingPeople[$x]['people_id'] . "</td>";
						}
						else
						{
							echo "<td align=\"center\" class=\"tdg\"><img src=\"" . $MeetingWhenImagePath . "greencheck.png\" /></td>";
							//echo "<td align=\"center\">" . $MeetingMainDatesSet[$il]['dates_id'] . "," . $MeetingPeople[$x]['people_id'] . "</td>";
						}
					}
				}
				else
				{
					$Isparticapant = 1;
					$DatesId = $MeetingMainDatesSet[$il]['dates_id'];
					if( PersonHasSlotId($MeetingPeopleDates,$MeetingMainDatesSet[$il]['dates_id'],$MeetingPeople[$x]['people_id'] ) == false )
					{
						if($MeetingPeople[$x]['cant_go'] == "1")
						{
							$CantGoUser = "1";
							$CantGoReason = $MeetingPeople[$x]['cant_go_reason'];
							echo "<td class=\"cantgosta\" align=\"center\" id=\"$uid\"><input id=\"T_$uid\" type=\"checkbox\"  onclick=\"SendMsg('$MeetingGUID','$PersonGUID','$DatesId',this)\" disabled/></td>";
						}
						else
						{
							echo "<td align=\"center\" id=\"$uid\"><input id=\"T_$uid\" type=\"checkbox\"  onclick=\"SendMsg('$MeetingGUID','$PersonGUID','$DatesId',this)\" /></td>";
						}
					}
					else
					{
						echo "<td align=\"center\" id=\"$uid\"><input   id=\"T_$uid\" type=\"checkbox\" onclick=\"SendMsg('$MeetingGUID','$PersonGUID','$DatesId',this)\" checked/></td>";
					}
										
				}
			}
		}
		echo "</tr>";
	}
	echo "<table>";
	?>
	<table><tr><td>
	<?php if( $Isparticapant == 1)
	{?>
		<div id="ck-button">
		<label>
			<input type="checkbox" name="cantgo" value="yes" id="cantgockbk" onclick="SendCant('<?=$MeetingGUID;?>','<?=$PersonGUID;?>',this)"><span>I can't Go</span>
		</label>
		</div>
	<?php } ?>
	</td></tr>
	<tr id="trcan"><td>Optional <b>Public</b> Reason Why you cant:<br />
	<textarea cols="40" rows"4" name="rcant" id="rcanttext"><?=strip_tags($CantGoReason);?></textarea><br />
	<input type="button" value="Save Reason" onclick="SaveReason('<?=$MeetingGUID;?>','<?=$PersonGUID;?>',this)" /></td></tr>
	</table>
	<?php if($CantGoUser == "1"){ ?>
	<script>
		$("#cantgockbk").attr('checked', true);
		$("#trcan").show();
	</script>
	<?php 
	} 
	echo "<br /><b>A green check means the person to the left is available for that time.</b>";
	echo "<br />To enter your time click the check box. It automatically saves.";
	echo "<br />A name in red means they can't make it. You can hover your mouse to see the reason, if they have left one.";
	echo "<br />If you make a mistake you can un-check the box. It automatically saves.";
	echo "<br />If you can't go then click \"I can't go\", you can leave an optional public reason as to why.";
	
}
else
{
	echo "There was an issue getting that meeting request.";
}

mysql_close($link);




function CalcCols($MeetingMain,$RefForward){
	$tspan = 1;
	for($x=0;$x<count($MeetingMain);$x++){
		if( date("Ymd",strtotime($MeetingMain[$RefForward]['date_and_time'])) == date("Ymd",strtotime($MeetingMain[$x]['date_and_time'])) && $RefForward != $x){
			$tspan++;
		}
	}
	return $tspan;
}

function PersonHasSlotId($MeetingPeopleDates,$dates_id,$people_id){
	for($x=0;$x<count($MeetingPeopleDates);$x++){
		if( $MeetingPeopleDates[$x]['slot_using_id'] == $dates_id && $MeetingPeopleDates[$x]['people_id'] == $people_id ){
			return true;
		}
	}
	return false;
}
?>
</center>
</body>
</html>