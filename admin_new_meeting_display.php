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
if($PageState == "new")
{
	
	$form_token = md5( uniqid('auth', true) );
	$_SESSION['form_token'] = $form_token;
?>
<form method="post" action="admin.php?ps=savenew">
<input type="hidden" name="sub" value="yes" />
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<table>
<tr><td>Title</td><td><input style="width:441px;"type="text" name="title" value="" /></td><td></td></tr>
<tr><td>Description</td><td><textarea name="description" cols="60" rows="8"></textarea></td><td></td></tr>
</table>

<table id="timeslots">
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td colspan="2">Use only the slots you need and enter them sequentially</td></tr>

<tr id="ts1" ><td>Time slot 1</td><td>Date: <input type="text"  id="t1_date" name="t_date[]"  class="t_date" value="" />&nbsp;&nbsp; Start Time: <input type="text"  id="t1_time" name="t_time[]"  value="" class="t_time" /></td><td><input type="button" value="+" onclick="AppendTimeSlots(1);" /></td></tr>

</table>
<table id="peopleslots">
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td colspan="2">Enter names and email address of the people invited</td></tr>
	<tr id="pr_1">
		<td>Name: <input type="text" class="person_add" name="n_[]" value="" id="n_1"/></td><td>Email: <input type="text" name="ae_[]" value="" id="ae_1"/></td>
		<td><input type="button" value="+" onclick="AppendPersonSlots(1)" /></td>
	</tr>
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
<?php
}
?>