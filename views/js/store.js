/*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

$(document).ready(function() {

	var DateTime = new Date();
    var strDay = DateTime.getDay();
    var strHours = DateTime.getHours();	
	
	if(store){
		if(store.working_hours == 1){

			var timing = JSON.parse(store.everyday);
			if((strHours > getHourAtTime(timing[0]) || strHours == getHourAtTime(timing[0])) && (strHours < getHourAtTime(timing[1]) || strHours == getHourAtTime(timing[1]))){
				$('.workingtime_now').html('<span class="openstore">Open now | closes: '+ timing[1] +'</span>');
			} else {
				$('.workingtime_now').html('<span>Closed</span>');
			}
		} else if(store.working_hours == 2){
			
			var timing_obg = JSON.parse(store.perdayofweek);
			var timing = timing_obg[strDay - 1];
			if(timing.isActive){
				if((strHours > getHourAtTime(timing.timeFrom) || strHours == getHourAtTime(timing.timeFrom)) && (strHours < getHourAtTime(timing.timeTill) || strHours == getHourAtTime(timing.timeTill))){
					$('.workingtime_now').html('<span class="openstore">Open now | closes: '+ timing.timeTill +'</span>');
				} else {
					$('.workingtime_now').html('<span>Closed</span>');
				}
			} else {
				$('.workingtime_now').html('<span>Closed</span>');
			}
		}
	}
	
	
	function getHourAtTime(time) {
		var s = time.split(" ");
		var format = s[1];
		var time = s[0].split(":");
		time = time[0];
		if(format == 'PM'){
			time = parseInt(time) + 12;
		}

		return parseInt(time);
	}	
});	