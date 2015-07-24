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
if($PageState == "profile" && IsLoggedIn() == true)
{
	$form_token = md5( uniqid('auth', true) );
	$_SESSION['form_token'] = $form_token;
	$SelectedUserid = $_SESSION['user_id'];
	$SelectSQL = "select * from meetingwhen_users where user_id='$SelectedUserid' limit 1";
	$MySqlSet = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MySqlSet[] = $hash;
	}
	
	
?>
<form action="admin.php?ps=editprofiledo" method="post">
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<input type="hidden" name="user_id" value="<?php echo $MySqlSet[0]['user_id']; ?>" />
<table>
	<tr>
		<td><label for="fullname">Full Name</label></td>
		<td><input type="text" id="fullname" name="fullname" value="<?php echo $MySqlSet[0]['fullname']; ?>" /></td>
	</tr>
	<tr>
		<td><label for="email">Email</label></td>
		<td><input type="text" id="email" name="email" value="<?php echo $MySqlSet[0]['email']; ?>"/></td>
	</tr>
	<tr>
		<td><label for="username">Username</label></td>
		<td><input type="text" id="username" name="username" value="<?php echo $MySqlSet[0]['username']; ?>"  /></td>
	</tr>
	<tr>
		<td><label for="password">Password</label></td>
		<td><input type="text" id="password" name="password" value="" /><br>
		<small>enter a password to change the current password</small></td>
	</tr>
	<tr>
		<td><label for="email_signature">Email Signature</label></td>
		<td><textarea name="email_signature"><?php echo $MySqlSet[0]['email_signature']; ?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Save profile" /></td>
	</tr>
</table>
</fieldset>
</form>
<?php
}
?>