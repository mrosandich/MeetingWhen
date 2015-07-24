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
if($PageState == "users")
{
	echo "<a href=\"admin.php?ps=newuser\">New User</a>\n";
	
	$SelectSQL = "select * from meetingwhen_users order by fullname";

	$MySqlSet = array();
	$result = mysql_query($SelectSQL);
	while ($hash = mysql_fetch_assoc( $result )) {
		$MySqlSet[] = $hash;
	}
	
	echo "<table id=\"UserTableList\"><tr><th>Full Name</th><th>User Name</th><th>Email</th><th>Is Active</th><th>Is Admin</th><th>-</th></tr>\n";
	for($x=0;$x<count($MySqlSet);$x++){
		echo "<tr>\n";
		echo "<td>" . $MySqlSet[$x]['fullname'] . "</td>";
		echo "<td>" . $MySqlSet[$x]['username'] . "</td>";
		echo "<td>" . $MySqlSet[$x]['email'] . "</td>";
		echo "<td>" . ($MySqlSet[$x]['is_activated']==1 ?'Yes':'No') . "</td>";
		echo "<td>" . ($MySqlSet[$x]['is_admin']==1 ?'Yes':'No') . "</td>";
		echo "<td>[<a href=\"admin.php?ps=edituser&id=" . $MySqlSet[$x]['user_id'] . "\">edit</a>]</td>";
		echo "</tr>\n";
	}
	
	echo "</table>";
}
?>