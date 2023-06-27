/*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/


$(document).ready(function() {
	
	var	latitude = null;
	var longitude = null;
	var error = true;
	var setData = $('#setData').val();
	
	var radio = $('#delivery_option_' + pickup_carrier_id);
	var option = $('#delivery_option_' + pickup_carrier_id).closest('.delivery-option');
	var html_option = '<h4>Pickup station</h4>';
	html_option += '<a id="select_station">select pickup station</a>';
	$(option).find('label').replaceWith("<div class='col-sm-11 pickup-container'>"+ html_option +"</div>");

	
	$(document).on('click','#select_station, #change_station',function(event){
		event.preventDefault();
		// get location
		getLocation();
		$.magnificPopup.open({
			items: {
			  src: '#station-popup',
			}
		});									
	});


	$('#country').change(function(){
		//var id_country = $(this).val();
		var id_country = $(this).find(':selected').data('id');
		$('#state').val('');
		$('#state option').hide();
		$("#state option[data-fix='1']").show();
		$("#state option[data-country='" + id_country + "']").show();
		$('#state').change();
	}).change();

	
	$('#state').change(function(){
		//var id_state = $(this).val();
		var id_state = $(this).find(':selected').data('id');
		$('#city').val('');
		$('#city option').hide();
		$("#city option[data-fix='1']").show();
		$("#city option[data-state='" + id_state + "']").show();
	});
	
	$('#city option').hide();
	
	$('.filter select').change(function(){
		var id_country = $('#country').find(':selected').data('id');
		var id_state = $('#state').find(':selected').data('id');
		var city = $('#city').val();
		var radius = $('#radius').val();
		
		$('.store-list ul').html('<div class="loading"><img src="'+ url +'modules/pickup/views/img/ajax-loader.gif" /></div>');

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_link_pickup,
			data: { 
				id_country: id_country,
				id_state: id_state,
				city: city,
				radius: radius,
				task: 'getStores',
				latitude: latitude,
				longitude: longitude,
				token: token,
			},
		}).success(function (data) {
			$('.store-list ul').html(data.html);			
		});	
	});	
	
	
	$('#select_direction').click(function(){
		var store_id = $('input[name=store_id]:checked').val();
		if(store_id){
			var address = $('#store_id_' + store_id).data('address');
			var win = window.open('https://maps.google.com/maps?saddr=current+location&daddr=' + address , '_blank');
			if (win) {
				win.focus();
			}			
		} else {
			alert('Please select a station');
		}
	});


	$('#select_store').click(function(){
		var store_id = $('input[name=store_id]:checked').val();
		var cart_id = $('#cart_id').val();
		if(store_id){
			var address = $('#store_id_' + store_id).data('address');
			var name = $('#store_id_' + store_id).data('name');
			var deliverystart = $('#store_id_' + store_id).data('deliverystart');
			var deliveryend = $('#store_id_' + store_id).data('deliveryend');
			var shipping_cost = $('#store_id_' + store_id).data('cost');
			//var pickup_fee = $('#pickup_fee').val();
			$.magnificPopup.close();
			
			html = '<h4>Pickup station</h4>';
			html 	+= '<div class="delivery-time">Ready for pickup between '+ deliverystart +' to '+ deliveryend +' with cheaper shipping fees</div>';
			html 	+= '<div class="details">';
			html 	+= 	'<h5 class="name">'+ name +'</h5>';
			html 	+= 	'<div class="info">'+ address +'</div>';
			html 	+= 	'<div class="fee">shipping fee <span>'+ shipping_cost +'</span></div>';
			html 	+= '</div>';
			html 	+= '<a id="change_station">change pickup station</a>';
			$('.pickup-container').html(html);

			radio.click();

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_link_pickup,
				data: { 
					store_id: store_id,
					cart_id: cart_id,
					task: 'setOrder',
					token: token
				},
				success: function(){
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: 'order?ajax=1&action=selectDeliveryOption',
						success: function(data){
							console.log(data.preview)
							$("#js-checkout-summary").replaceWith(data.preview);
						}	
					});					
				},
				error: function(){
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: 'order?ajax=1&action=selectDeliveryOption',
						success: function(data){
							//console.log(data.preview)
							$("#js-checkout-summary").replaceWith(data.preview);
						}	
					});
				}
			});
			
			error = false;
			
		} else {
			alert('Please select a station');
		}
	});
	
	// updated data
	/*
	if(setData == 1){
		var data = $('#data').val();
		data = JSON.parse(data);
		console.log( $("#store_id_" + data.store_id).offset().top);
		html = '<h4>Pickup station</h4>';
		html 	+= '<div class="delivery-time">Ready for pickup between '+ data.deliverydate_start +' to '+ data.deliverydate_end +' with cheaper shipping fees</div>';
		html 	+= '<div class="details">';
		html 	+= 	'<h5 class="name">'+ data.name +'</h5>';
		html 	+= 	'<div class="info">'+ data.address +'</div>';
		html 	+= 	'<div class="fee">shipping fee <span>'+ data.shipping_cost +'</span></div>';
		html 	+= '</div>';
		html 	+= '<a id="change_station">change pickup station</a>';
		$('.pickup-container').html(html);
		error = false;
		
		$("#store_id_" + data.store_id).attr('checked', 'checked');
		$('.store-list').animate({
			scrollTop: $("#store_id_" + data.store_id).offset().top
		}, 2000);		
	}*/
	
	
	function showLocation(position) {
		latitude = position.coords.latitude;
		longitude = position.coords.longitude;
	}


	function getLocation(){
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(showLocation);
		} else{
		   //alert("Sorry, browser does not support geolocation!");
		}
	}

/*
	$(".store-list").mCustomScrollbar({
		scrollbarPosition:"outside",
		theme:"dark",
	});*/
	
	
	$('button[name ="confirmDeliveryOption"]').click(function(e){
		if(radio.is(':checked')){
			if(error == true){
				e.preventDefault();
				alert('Please select a station');
				//error = false;
				//$('button[name ="confirmDeliveryOption"]').click();
			}
		}
	});	
	
});
		 