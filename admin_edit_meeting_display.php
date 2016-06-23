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
if($PageState == "myedit")
{
	
	$form_token = md5( uniqid('auth', true) );
	$_SESSION['form_token'] = $form_token;
	
	$SelectedMeeting = GetQueryValue("id", "","Numeric");
	$SelectSQL = "select * from meetingwhen_main where user_id='" . $_SESSION['user_id'] . "' and meeting_id='$SelectedMeeting' limit 1";
	$MySqlSet = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MySqlSet[] = $hash;
	}
	
	if( count($MySqlSet) >0 ){
	$SelectSQL = "select * from meetingwhen_main_dates where user_id='" . $_SESSION['user_id'] . "' and meeting_id='$SelectedMeeting' order by date_and_time";
	$MainDates = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MainDates[] = $hash;
	}
	
	$SelectSQL = "select * from meetingwhen_people where user_id='" . $_SESSION['user_id'] . "' and meeting_id='$SelectedMeeting'";
	$People = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$People[] = $hash;
	}
?>
<form method="post" action="admin.php?ps=saveedit&id=<?php echo $MySqlSet[0]['meeting_id']; ?>">
<input type="hidden" name="sub" value="yes" />
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<input type="hidden" name="meeting_id" value="<?php echo $MySqlSet[0]['meeting_id']; ?>" />
<input type="hidden" name="guid" value="<?php echo $MySqlSet[0]['GUID']; ?>" />
<table>
<tr><td>Is Active</td><td><input style="width:20px;" type="checkbox" name="is_active" value="1" <?php if($MySqlSet[0]['is_active'] == 1){echo "checked";} ?>/> check to allow access to this meeting</td><td></td></tr>
<tr><td>Title</td><td><input style="width:441px;" type="text" name="title" value="<?php echo $MySqlSet[0]['title']; ?>" /></td><td></td></tr>
<tr><td>Description</td><td><textarea name="description" cols="60" rows="8"><?php echo htmlentities($MySqlSet[0]['description']); ?></textarea></td><td></td></tr>
</table>

<table id="timeslots">
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td colspan="2">Use only the slots you need and enter them sequentially</td></tr>

<?php
	for($x=0;$x<count($MainDates);$x++){
		$DatePart = date("m/d/Y",strtotime($MainDates[$x]['date_and_time']));
		$TimePart = date("h:ia",strtotime($MainDates[$x]['date_and_time']));
	?>
		<tr id="ts<?php echo $x+1;?>" ><td>Time slot <?php echo $x;?>
		<input type="hidden" name="datesid[]" value="<?php echo $MainDates[$x]['dates_id'];?>" /></td>
		<td>Date: <input type="text"  id="t<?php echo $x+1;?>_date" name="t_date[]"  class="t_date" value="<?php echo $DatePart;?>" onclick="slickDTP.pickDate('#t<?php echo $x+1;?>_date','#t<?php echo $x+1;?>_time')"/>&nbsp;&nbsp; 
		Start Time: <input type="text"  id="t<?php echo $x+1;?>_time" name="t_time[]"  value="<?php echo $TimePart;?>" class="t_time" onclick="slickDTP.pickDate('#t<?php echo $x+1;?>_date','#t<?php echo $x+1;?>_time')"/></td>
		<td><input type="button" value="+" onclick="AppendTimeSlots(<?php echo $x+1;?>);" /></td>
		</tr>
	<?php
	}
	if(count($MainDates) == 0){
	?>
	<tr id="ts1" ><td>Time slot 1</td>
	<td>Date: <input type="text"  id="t1_date" name="t_date[]"  class="t_date" value="" />
	&nbsp;&nbsp; Start Time: <input type="text"  id="t1_time" name="t_time[]"  value="" class="t_time" /></td>
	<td><input type="button" value="+" onclick="AppendTimeSlots(1);" /></td></tr>

	<?php
	}
?>
</table>
<table id="peopleslots">
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td colspan="2">Enter names and email address of the people invited</td></tr>
	<?php
	for($x=0;$x<count($People);$x++){
	?>
	<tr id="pr_<?php echo $x+1;?>">
		<td>Name: <input type="text" name="n_[]" value="<?php echo $People[$x]['name'];?>" id="n_<?php echo $x+1;?>"/>
										<input type="hidden" name="peopleid[]" value="<?php echo $People[$x]['people_id'];?>" /></td>
		<td>Email: <input type="text" name="ae_[]" value="<?php echo $People[$x]['email'];?>" id="ae_<?php echo $x+1;?>"/></td>
		<td><input type="button" value="+" onclick="AppendPersonSlots(<?php echo $x+1;?>)" /></td>
	</tr>
	<?php
	}
	if(count($People) == 0){
	?>
	<tr id="pr_1">
		<td>Name: <input type="text" class="person_add" name="n_[]" value="" id="n_1"/>
		<input type="hidden" name="peopleid[]" value="0" />
		</td><td>Email: <input type="text" name="ae_[]" value="" id="ae_1"/></td>
		<td><input type="button" value="+" onclick="AppendPersonSlots(1)" /></td>
	</tr>
	<?php
	}
	?>

</table>
<br />
Send Emails <input type="checkbox" name="doemail" value="yes" onclick="ShowEmail(this);" unchecked/><br />
<table id="emblock">
<tr><td>Your Name:</td><td><input type="text" value="<?php echo $_SESSION['fullname'];?>" name="sendername" /></td></tr>
<tr><td>Your Email:</td><td><input type="text" value="<?php echo $_SESSION['email'];?>" name="senderemail" /></td></tr>
<tr><td>Email Signature</td><td><textarea cols="60" rows="8" name="emailsig" /><?php echo htmlentities($_SESSION['email_signature']);?></textarea></td></tr>
</table>
<input type="submit" value="Save" id="saveform" />
</form>
<script>
	for(var x=0; x<<?php echo count($MainDates)?>;x++ ){
		
	}
	CurrentTimeSlotCount = <?php echo count($MainDates);?>;
	CurrentPersonSlotCount = <?php echo count($People);?>;

</script>
<?php
	}//end count >0
	else{
		echo "There was an error requesting the meeting details";
	}
}
?>