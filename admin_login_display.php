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
if($PageState == "login")
{
	$form_token = md5( uniqid('auth', true) );
	$_SESSION['form_token'] = $form_token;
?>
<h2>Login</h2>
<form action="admin.php?ps=dologin" method="post">
<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
<fieldset>
<p>
<label for="username">Username</label>
<input type="text" id="username" name="username" value="<?php echo GetFormValue("username", "","AlphaNumeric"); ?>" maxlength="60" />
</p>
<p>
<label for="password">Password</label>
<input type="password" id="password" name="password" value="" maxlength="60" />
</p>
<p>
<input type="submit" value="Login" />
</p>
</fieldset>
<?php
}
?>