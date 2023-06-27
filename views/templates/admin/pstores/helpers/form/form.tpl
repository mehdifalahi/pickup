{*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'everyday'}
        <div class="everyday col-md-6">
			<div class="operationTime input-group">
				<span class="input-group-addon"><i class="fa fa-sun"></i></span>
				<input type="text" name="everyday_start" class="mini-time form-control timepicker" value="{if $fields_value['everyday']}{$fields_value['everyday'][0]|escape:'htmlall':'UTF-8'}{/if}">
			</div>
			<div class="operationTime input-group">
				<span class="input-group-addon"><i class="fa fa-moon"></i></span>
				<input type="text" name="everyday_end" class="mini-time form-control timepicker" value="{if $fields_value['everyday']}{$fields_value['everyday'][1]|escape:'htmlall':'UTF-8'}{/if}">
			</div>
		</div>
    {elseif $input.type == 'perdayofweek'}
		<div class="perdayofweek"></div>
		<input type="hidden" name="perdayofweek" id="perdayofweek_input" value="{$fields_value['perdayofweek']|escape:'htmlall':'UTF-8'}"/>
    {elseif $input.type == 'map'}
		<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key={$setting.google_api|escape:'htmlall':'UTF-8'}&callback=initMap&libraries=geometry,places&v=weekly" defer></script>
	
		<div id="map" style="height:400px;width:100%;"></div>
		
		<script>
		let map;
		var geocoder;
		var marker;
		var markersArray = [];

		var lat = {if $fields_value['lat']}{$fields_value['lat']|escape:'htmlall':'UTF-8'}{else}-34.397{/if};
		var lng = {if $fields_value['lng']}{$fields_value['lng']|escape:'htmlall':'UTF-8'}{else}150.644{/if};
		function initMap() {
			const myLatlng = { lat: lat, lng: lng };
			
			map = new google.maps.Map(document.getElementById("map"), {
				center: myLatlng,
				zoom: 8,
			});
			geocoder = new google.maps.Geocoder();
			
			marker = new google.maps.Marker({
				position: myLatlng,
				map,
				draggable: true,
			});	
			marker.setMap(map);
			markersArray.push(marker);

			google.maps.event.addListener(marker, 'dragend', function(evt){
				document.getElementById("lat").value = evt.latLng.lat().toFixed(6);
				document.getElementById("lng").value = evt.latLng.lng().toFixed(6);
			});
			
			
			const locationButton = document.createElement("button");
			locationButton.textContent = "{l s='Pan to Current Location' mod='pickup'}";
			locationButton.classList.add("custom-map-control-button");
			locationButton.type = 'button';
			map.controls[google.maps.ControlPosition.TOP_CENTER].push(
				locationButton
			);
			locationButton.addEventListener("click", () => {
			  // Try HTML5 geolocation.
			  if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
				  (position) => {
					const pos = {
					  lat: position.coords.latitude,
					  lng: position.coords.longitude,
					};

					document.getElementById("lat").value = position.coords.latitude.toFixed(6);
					document.getElementById("lng").value = position.coords.longitude.toFixed(6);
					
					marker.setPosition(pos);
					map.setCenter(pos);
				  },
				  () => {
					//handleLocationError(true, infoWindow, map.getCenter());
				  }
				);
			  } else {
				// Browser doesn't support Geolocation
				handleLocationError(false, infoWindow, map.getCenter());
			  }
			});
			
		}
		
		function setLocationAddress(){
			var address = document.getElementById("address").value;
			var city = document.getElementById("city").value;
			var zipcode = document.getElementById("zipcode").value;
			var adrs = address + ', '+ zipcode + ', ' + city;

			geocoder.geocode({ 'address': adrs }, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					clearOverlays();
					marker = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location,
						draggable: true,
					});
					markersArray.push(marker);
					
					document.getElementById("lat").value = results[0].geometry.location.lat().toFixed(6);
					document.getElementById("lng").value = results[0].geometry.location.lng().toFixed(6);	

					google.maps.event.addListener(marker, 'dragend', function(evt){
						document.getElementById("lat").value = evt.latLng.lat().toFixed(6);
						document.getElementById("lng").value = evt.latLng.lng().toFixed(6);
					});	
					
				} else {
					alert("{l s='Geocode unsuccessful' mod='pickup'}");
				}				
			});	
		}
		
		function handleLocationError(browserHasGeolocation, infoWindow, pos) {
			infoWindow.setPosition(pos);
			infoWindow.setContent(
			browserHasGeolocation
			? "{l s='Error: The Geolocation service failed.' mod='pickup'}"
			: "{l s='Error: Your browser does not support geolocation.' mod='pickup'}"
			);
			infoWindow.open(map);
		}
		
		function clearOverlays() {
		  for (var i = 0; i < markersArray.length; i++ ) {
			markersArray[i].setMap(null);
		  }
		  markersArray.length = 0;
		}		
		
		</script>
		
		<style>
			.custom-map-control-button {
				appearance: button;
				background-color: #fff;
				border: 0;
				border-radius: 2px;
				box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
				cursor: pointer;
				margin: 10px;
				padding: 0 0.5em;
				height: 40px;
				font: 400 18px Roboto, Arial, sans-serif;
				overflow: hidden;
			}
			.custom-map-control-button:hover {
				background: #ebebeb;
			}
		</style>		
	{else}
        {$smarty.block.parent}
    {/if}
{/block}