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
define('SYSLOADEDADMIN', "Yes Loaded");
include("common.php");
include("class_meeting.php");
include("class_ldap.php");

$PageState = GetQueryValue("ps","","Alpha");
$UserMessageResponse = "";
session_start();

$HTTPPrefix = "http://";
if(isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] == "on") {
        $HTTPPrefix = "https://";
    }
}

include( "admin_login_do.php");
include( "admin_logout_do.php");
if(!isset( $_SESSION['user_id'] )){
	if( $PageState != "login" && $PageState != "logout" ){
		$PageState = "login";
	}
}
include( "admin_security.php");
include( "admin_new_meeting_do.php");
include( "admin_edit_meeting_do.php");
include( "admin_add_users_do.php");
include( "admin_edit_users_do.php");
include( "admin_edit_profile_do.php");
?>

<html>
<head>
<script src="<?php echo $HTTPPrefix;?>code.jquery.com/jquery-1.10.2.js"></script>
<script src="<?php echo $HTTPPrefix;?>code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo $HTTPPrefix;?>code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="meetwhen_admin.css">
<link rel="stylesheet" href="slick_dtp.css">
<script src="slick_dtp.js"></script>
<script>
	var CurrentTimeSlotCount = 1;
	var CurrentPersonSlotCount = 1;
	var slickDTP = '';
  $(function() {

	slickDTP = new SlickDTP();
	$( "#t1_date" ).click(function() {slickDTP.pickDate('#t1_date','#t1_time');});
	$( "#t1_time" ).click(function() {slickDTP.pickDate('#t1_date','#t1_time');});

  });
  
  function DropTime(Inid){
	  $('#ts'+Inid).show();
  }
  function DropRep(Inid){
	  $('#pr_' + Inid).show();
  }
  function ShowEmail(inctrl){
	if( inctrl.checked == true ){
		$('#emblock').show();
		$('#saveform').val('Save and Send Email');
	}
	else{
		$('#emblock').hide();
		$('#saveform').val('Save');
	}
  }
  function AppendTimeSlots(CallingID){
	if( $('#t'+CallingID+'_date').val() != ""  && $('#t'+CallingID+'_time').val() != "" && CallingID == CurrentTimeSlotCount){
		CurrentTimeSlotCount++;
		$('#timeslots').append('<tr id="ts'+CurrentTimeSlotCount+'" ><td>Time slot '+CurrentTimeSlotCount+' <input type="hidden" name="datesid[]" value="0" /></td><td>Date: <input type="text"  id="t'+CurrentTimeSlotCount+'_date" name="t_date[]" class="t_date" value="" onclick="slickDTP.pickDate(\'#t'+CurrentTimeSlotCount+'_date\',\'#t'+CurrentTimeSlotCount+'_time\')"/>&nbsp;&nbsp; Start Time: <input type="text"  id="t'+CurrentTimeSlotCount+'_time" name="t_time[]" class="t_time" value="" onclick="slickDTP.pickDate(\'#t'+CurrentTimeSlotCount+'_date\',\'#t'+CurrentTimeSlotCount+'_time\')" /></td><td><input type="button" value="+" onclick="AppendTimeSlots('+CurrentTimeSlotCount+');" /></td></tr>');  
	}
  }
  function AppendPersonSlots(CallingID){
	  if( $('#n_'+CallingID).val() != ""  && $('#ae_'+CallingID).val() != "" && CallingID == CurrentPersonSlotCount){
		CurrentPersonSlotCount++;
		$('#peopleslots').append('<tr id="pr_'+CurrentPersonSlotCount+'"><td>Name: <input type="text" class="person_add" id="n_'+CurrentPersonSlotCount+'" name="n_[]" value="" /><input type="hidden" name="peopleid[]" value="0" /></td><td>Email: <input type="text" id="ae_'+CurrentPersonSlotCount+'" name="ae_[]" value="" /></td><td><input type="button" value="+" onclick="AppendPersonSlots('+CurrentPersonSlotCount+')" /></td></tr>');  
		$('#n_' + CurrentPersonSlotCount).on("keydown", function (event){if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {event.preventDefault();}}).autocomplete({source: function (request, response) {$.getJSON("admin_callback_jquery.php?s=s", {term: extractLast(request.term)}, response);},search: function () { var term = extractLast(this.value);if (term.length < 2) {return false;}},focus: function () {return false;},select: function (event, ui) {var terms = split(this.value);terms.pop();terms.push(ui.item.label); AddPersonEmail(ui.item.id, ui.item.value,$(this).attr('id') );terms.push("");return false;} });
	}
  }
  
   //autocomplete for DX
    $(function () {
       

      $('#n_1')
      .on("keydown", function (event) {
          if (event.keyCode === $.ui.keyCode.TAB &&
            $(this).autocomplete("instance").menu.active) {
              event.preventDefault();
          }
      })
      .autocomplete({
          source: function (request, response) {
              $.getJSON("admin_callback_jquery.php?s=s", {
                  term: extractLast(request.term)
              }, response);
          },
          search: function () {
              var term = extractLast(this.value);
              if (term.length < 2) {
                  return false;
              }
          },
          focus: function () {
              return false;
          },
          select: function (event, ui) {
              var terms = split(this.value);
              terms.pop();
              terms.push(ui.item.label);
              AddPersonEmail(ui.item.id, ui.item.value,$(this).attr('id') );
              terms.push("");
              return false;
          }
      });
    });
	 function split(val) {
            return val.split(/,\s*/);
        }
	function extractLast(term) {
		return split(term).pop();
	}
	function AddPersonEmail(inName,inEmail,inId){
		//alert( inName + "," + inEmail + "," + inId);
		$('#' + inId).val(inName);
		inId = inId.replace('n_','ae_');
		$('#' + inId).val(inEmail);
	}
  
</script>
<body>
<?php
include("admin_menu.php");

if( $UserMessageResponse != "" ){
	echo $UserMessageResponse . "<br /><br />";
}


include( "admin_new_meeting_display.php");
include( "admin_edit_meeting_display.php");
include( "admin_add_users_display.php");
include( "admin_edit_users_display.php");
include( "admin_edit_profile_display.php");
include( "admin_users.php");
include( "admin_mymeetings.php");
include( "admin_login_display.php");
include( "admin_logout_display.php");
include( "admin_home_display.php");
?>

</body>
</html>
<?php


mysql_close($link);
?>