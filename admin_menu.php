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
?>
<a href="admin.php" class="<?php echo ($PageState=="" || $PageState=="home" ? 'menu_selected':'menu_unselected')?>">Home</a> | 
<a href="admin.php?ps=new" class="<?php echo($PageState=="new" ? 'menu_selected':'menu_unselected')?>">New Meeting</a> | 
<a href="admin.php?ps=my" class="<?php echo ($PageState=="my"  ? 'menu_selected':'menu_unselected')?>">My Meetings</a> |
<a href="admin.php?ps=profile" class="<?php echo ($PageState=="profile"  ? 'menu_selected':'menu_unselected')?>">My Profile</a> |
<?php 
if(IsLoggedIn() == true ){
	if(IsLoggedInAndAdmin() == true){
		echo '<a href="admin.php?ps=users" class="'. ($PageState=="users"|| $PageState=="edituser" || $PageState=="newuser" ? 'menu_selected':'menu_unselected') . '">Manage Users</a> | ';
	}
	echo '<a href="admin.php?ps=logout" class="'. ($PageState=="logout"  ? 'menu_selected':'menu_unselected') . '">Logout</a>';
}
else
{
	echo '<a href="admin.php?ps=login" class="'. ($PageState=="login"  ? 'menu_selected':'menu_unselected') . '">Login</a>';
}
?>
<hr />