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
defined('SYSLOADED') OR die('No direct access allowed.');


class cLDAP{

	var $app_ldap_ip 				= '';
	var $app_ldap_port 				= '';
	var $app_user 					= '';
	var $app_pass 					= '';
	var $app_error_message 			= '';
	var $app_search_user_base 		= '';
	var $app_use_activedirectory 	= ''; 
	
	//------------------------------------
	//	You don't need to anything below
	//------------------------------------
	
	var $user_username = '';
	var $user_password = '';
	var $user_email	='';
	var $user_namefull = '';
	var $user_namelast = '';
	var $user_namefirst = '';
	var $user_userdn = '';
	var $user_loggedin = 0;

	//mysql stuff
	var $SQLLink = "";
	var $SQLUseAutoUser = 0;
	var $SQLNewUserId = "";
	
	public function __construct($LDAPConfig){
		$this->app_ldap_ip 				= $LDAPConfig['app_ldap_ip'];
		$this->app_ldap_port 			= $LDAPConfig['app_ldap_port'];
		$this->app_user 				= $LDAPConfig['app_user'];
		$this->app_pass 				= $LDAPConfig['app_pass'];
		$this->app_error_message 		= "";
		$this->app_search_user_base 	= $LDAPConfig['app_search_user_base'];
		$this->app_use_activedirectory 	= $LDAPConfig['app_use_activedirectory']; 
	}
	
	//if you want to auto add user to the system via a valid login then you need to pass a valid MySQL link to this function.
	function initLDAPMySQL($SQLLink){
		$this->SQLLink = $SQLLink;
		$this->SQLUseAutoUser = 1;
	}
	
	
	//returns 0=failed, 1=valid username/password combination.
	function validateUser($username,$password){
		
		$this->user_username = $username;
		$this->user_password = $password;
		
		
		$conn_status = ldap_connect($this->app_ldap_ip,$this->app_ldap_port);
		ldap_set_option($conn_status, LDAP_OPT_TIMELIMIT, 2);
		ldap_set_option($conn_status, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($conn_status, LDAP_OPT_PROTOCOL_VERSION, 3);

		if ($conn_status === FALSE) {
			$this->app_error_message = "Couldn't connect to LDAP server";
			$this->user_loggedin = 0;
			return 0 ;
		}

		$bind_status = ldap_bind($conn_status, $this->app_user, $this->app_pass);
		if( $this->app_use_activedirectory == 0 ){
			$query = "(&(uid=" . $this->user_username . ")(objectClass=person))";
		}else{
			$query='(&(objectClass=User)(sAMAccountName=' . $this->user_username. '))';
		}
		
		$search_status = ldap_search($conn_status, $this->app_search_user_base, $query, array('dn','mail','givenname','sn') );
		if ($search_status === FALSE) {
		  $this->app_error_message = "Search on LDAP failed";
		  $this->user_loggedin = 0;
		  return 0 ;
		}
		
		
		try{
			$result = ldap_get_entries($conn_status, $search_status);
		}
		catch(Exception $e){
			$this->app_error_message = "No search results from LDAP. Exception:" .$e->getMessage();
			$this->user_loggedin = 0;
			return 0 ;
		}
		if ($result === FALSE) {
			$this->app_error_message = "No search results from LDAP";
			$this->user_loggedin = 0;
			return 0 ;
		}
		if ((int) @$result['count'] > 0) {
			$this->user_userdn = $result[0]['dn'];
			
			//check if we have an email
			if( array_key_exists('count',$result[0]['mail']) ){
				if($result[0]['mail']['count'] > 0 ){
					$this->user_email = $result[0]['mail'][0];
				}
			}
			
			//check if we have a first name
			if( array_key_exists('count',$result[0]['givenname']) ){
				if($result[0]['givenname']['count'] > 0 ){
					$this->user_namefirst = $result[0]['givenname'][0];
				}
			}
			//check if we have a first name
			if( array_key_exists('count',$result[0]['sn']) ){
				if($result[0]['sn']['count'] > 0 ){
					$this->user_namelast = $result[0]['sn'][0];
				}
			}
			$this->user_namefull = $this->user_namefirst . ' ' . $this->user_namelast;
		}
		if (trim((string) $this->user_userdn) == '') {
			$this->app_error_message = "The retrieved user dn was empty. Won't be able to bind for password check";
			$this->user_loggedin = 0;
			return 0 ;
		}

		
		// Authenticate with the newly found DN and user-provided password
		try{
			if( $this->user_userdn !="" ){
				$auth_status = ldap_bind($conn_status, $this->user_userdn, $this->user_password);
			}
		}
		catch(Exception $e){
			$this->app_error_message = "Couldn't bind to LDAP to user, user or password wrong. Exception:" .$e->getMessage();
			$this->user_loggedin = 0;
			return 0 ;
		}
		if ($auth_status === FALSE) {
			$this->app_error_message = "Couldn't bind to LDAP to user, user or password wrong";
			$this->user_loggedin = 0;
			return 0 ;
		}

		//if we made it this far then we are good to go.
		$this->app_error_message = "LDAP successful. Valid Username and Password provided.";
		$this->user_loggedin = 1;
		
		if( $this->SQLUseAutoUser == 1 ){
			//First check to see if the user in the user database
			$CheckUserSQL = "select * from meetingwhen_users where username='".DataBaseCleanEscapeValue($this->user_username)."'";
			$MySqlSet = array();
			$result = mysql_query($CheckUserSQL,$this->SQLLink);
			while ($hash = mysql_fetch_assoc( $result )) {
				$MySqlSet[] = $hash;
			}
			
			if(count($MySqlSet) < 1 ){ //There is nothing in the array set so there was no results
				$FullName 	= DataBaseCleanEscapeValue(RegClean($this->user_namefull,"AlphaNumeric"));
				$Email 		= DataBaseCleanEscapeValue(RegClean($this->user_email,"Email"));
				$UserName 	= DataBaseCleanEscapeValue(RegClean($this->user_username,"UserName"));
				$IsAdmin 	= '0';
				$Password 	= '';
				$is_ldap 	= '1';
				$InsertSQL = "INSERT INTO meetingwhen_users (fullname,email,username,password,is_admin,is_activated,is_ldap ) VALUES ('$FullName','$Email','$UserName', '$Password','$IsAdmin','1','$is_ldap' )";
				mysql_query($InsertSQL,$this->SQLLink);
				$this->SQLNewUserId = mysql_insert_id($this->SQLLink);
			}
			
		}
		return 1 ;
	}//end validateUser
}//end class
?>