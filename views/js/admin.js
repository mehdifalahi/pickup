/*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

$(document).ready(function() {
	
	$('.timepicker').timepicker({
		'timeFormat': 'g:i A',
		'step': 15
	});

	var businessHoursManager = $('.perdayofweek').businessHours({
		//operationTime: [{"isActive":false,"timeFrom":null,"timeTill":null},{"isActive":false,"timeFrom":null,"timeTill":null},{"isActive":true,"timeFrom":"09:00","timeTill":"18:00"},{"isActive":true,"timeFrom":"09:00","timeTill":"18:00"},{"isActive":true,"timeFrom":"09:00","timeTill":"18:00"},{"isActive":true,"timeFrom":"09:00","timeTill":"18:00"},{"isActive":true,"timeFrom":"09:00","timeTill":"18:00"}],
		operationTime: (typeof perdayofweek !== 'undefined' && perdayofweek)? JSON.parse(perdayofweek): [{"isActive":true,"timeFrom":null,"timeTill":null},{"isActive":true,"timeFrom":null,"timeTill":null},{"isActive":true,"timeFrom":null,"timeTill":null},{"isActive":true,"timeFrom":null,"timeTill":null},{"isActive":true,"timeFrom":null,"timeTill":null},{"isActive":false,"timeFrom":null,"timeTill":null},{"isActive":false,"timeFrom":null,"timeTill":null}],
		postInit:function(){
			$('.operationTimeFrom, .operationTimeTill').timepicker({
				'timeFormat': 'g:i A',
				'step': 15
			});
		},
		dayTmpl:'<div class="dayContainer" style="width: 80px;">' +
			'<div data-original-title="" class="colorBox"><input type="checkbox" class="invisible operationState"></div>' +
			'<div class="weekday"></div>' +
			'<div class="operationDayTimeContainer">' +
			'<div class="operationTime input-group"><span class="input-group-addon"><i class="fa fa-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
			'<div class="operationTime input-group"><span class="input-group-addon"><i class="fa fa-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
			'</div></div>'
    });	


	$('#pickup_store_form').submit(function(event) {
		event.preventDefault(); 
		$("#perdayofweek_input").val(JSON.stringify(businessHoursManager.serialize()));
		$(this).unbind('submit').submit();
	})


	$("#working_hours").on('change', function(e) {
		var id = $(this).val();
		if(id == 1){
			$('.everyday').parent().parent().show();
			$('.perdayofweek').parent().parent().hide();
		} else if(id == 2){
			$('.everyday').parent().parent().hide();
			$('.perdayofweek').parent().parent().show();			
		}
	}).change();	


	$("#country_id").on('change', function(e) {
		var country_id = $(this).val();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax-tab.php',
			data: {
				ajax: true,
				controller: 'AdminPStores',
				action: 'GetStates', 
				token: token_static, 
				country_id: country_id
			},
		}).success(function (data) {
			if(data){
				var option = '';
				var selected = '';
				for(var i=0; i < data.length; i++){
					if(state_id == data[i].id_state){
						selected = 'SELECTED';
					} else {
						selected = '';
					}
					option += '<option value="'+ data[i].id_state +'" '+ selected +'>'+ data[i].name +'</option>';
				}
				$("#state_id").html(option);
				$('#state_id').trigger("chosen:updated");
			}
		});
	}).change();
	
	
	$('button[name="submitAddpickup_storeAndStay_btn"]').click(function(){
		$(this).closest('form').append('<input type="hidden" name="submitAddpickup_storeAndStay" value="1" />');
		$(this).closest('form').submit();
	});
	
	
});	