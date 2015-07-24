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
if( IsLoggedInAndAdmin() == false ){
	
	//Fill the arays with allowed page states
	$PublicPages 	= array("logout","login","dologin","");
	$UserPages		= array("profile","editprofiledo","new","savenew","my","myedit","saveedit","dologin");
	
	
	//restrict Logged in users pages to public and UserPages
	if( IsLoggedIn() == true && ( in_array($PageState,$UserPages) == false && in_array($PageState,$PublicPages) == false ) ){
		$UserMessageResponse = "The page your are trying to access requires an administrative account.";
		$PageState="";
	}
	
	if( IsLoggedIn() == false && in_array($PageState,$PublicPages) == false ){
		$UserMessageResponse = "The page your are trying to access requires you to be logged in.";
		$PageState="";
	}
}
?>