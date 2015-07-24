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
class MeetingClass {
	
	public $DateMin = "not set";
	public $DateMax = "not set";
	public $PeopleInvited = 0;
	public $PeopleCantGo = 0;
	
	function Load($MeetingID,$MeetingArray,$MeetingDatesArray,$MeetingPeople){
		
		$TempSmallDate = date("U")*2;
		$TempLargeDate = 0;
		for($x=0;$x<count($MeetingDatesArray);$x++){
			if($MeetingDatesArray[$x]['meeting_id'] == $MeetingID){
				if( (date("U",strtotime($MeetingDatesArray[$x]['date_and_time']))*1) > $TempLargeDate ){
					$TempLargeDate = date("U",strtotime($MeetingDatesArray[$x]['date_and_time']))*1;
					$this->DateMax = $MeetingDatesArray[$x]['date_and_time'];
				}
				
				if( (date("U",strtotime($MeetingDatesArray[$x]['date_and_time']))*1) < $TempSmallDate ){
					$TempSmallDate = date("U",strtotime($MeetingDatesArray[$x]['date_and_time'])) *1 ;
					$this->DateMin = $MeetingDatesArray[$x]['date_and_time'];
				}
			}
		}
		
		$TempList = array();
		for($x=0;$x<count($MeetingPeople);$x++){
			
			if($MeetingPeople[$x]['meeting_id'] == $MeetingID){
				if( in_array($MeetingPeople[$x]['people_id'],$TempList)==false ){
					$this->PeopleInvited++;
					if($MeetingPeople[$x]['cant_go'] == "1"){
						$this->PeopleCantGo++;
					}
					$TempList[] = $MeetingPeople[$x]['people_id'];
				}
			}
		}
		
	}


	function GetMinDate(){
		if(  $this->DateMin != "not set" ){
			return date("m/d/y H:i",strtotime($this->DateMin));
		}
		return $this->DateMin;
	}
	function GetMaxDate(){
		if(  $this->DateMax != "not set" ){
			return date("m/d/y H:i",strtotime($this->DateMax));
		}
		return $this->DateMax;
	}
	
	function GetCantGo(){
		return $this->PeopleCantGo;
	}	
	
	function GetPeopleInvited(){
		return $this->PeopleInvited;
	}
}//end class
?>