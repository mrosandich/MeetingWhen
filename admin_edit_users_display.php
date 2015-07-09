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
if($PageState == "edituser"&& IsLoggedInAndAdmin() == true)
{
	$form_token = md5( uniqid('auth', true) );
	$_SESSION['form_token'] = $form_token;
	$SelectedUserid = GetQueryValue("id", "","Numeric");
	$SelectSQL = "select * from meetingwhen_users where user_id='$SelectedUserid' limit 1";
	$MySqlSet = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MySqlSet[] = $hash;
	}
	
	
?>
<form action="admin.php?ps=edituserdo&id=<?php echo $MySqlSet[0]['user_id']; ?>" method="post">
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<input type="hidden" name="user_id" value="<?php echo $MySqlSet[0]['user_id']; ?>" />
<table>
	<tr>
		<td><label for="fullname">Full Name</label></td>
		<td><input type="text" id="fullname" name="fullname" value="<?php echo $MySqlSet[0]['fullname']; ?>" /></td>
	</tr>
	<tr>
		<td><label for="email">Email</label></td>
		<td><input type="text" id="email" name="email" value="<?php echo $MySqlSet[0]['email']; ?>" /></td>
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
		<td><label for="is_admin">Is Admin</label></td>
		<td><input type="checkbox" id="is_admin" name="is_admin" value="1" <?php if($MySqlSet[0]['is_admin'] == 1){echo "checked";} ?>/></td>
	</tr>
	<tr>
		<td><label for="is_admin">Account is enabled</label></td>
		<td><input type="checkbox" id="is_activated" name="is_activated" value="1" <?php if($MySqlSet[0]['is_activated'] == 1){echo "checked";} ?>/></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Update User" /></td>
	</tr>
</table>
</fieldset>
</form>
<?php
}
?>