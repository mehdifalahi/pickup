/*
* 	Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

$(document).ready(function() {

	function loadStoreLocator(lat, lng){
		$('#bh-sl-map-container').storeLocator({
			'infowindowTemplatePath'     : url + 'modules/pickup/views/js/templates/infowindow-description.html',
			'listTemplatePath'           : url + 'modules/pickup/views/js/templates/location-list-description.html',
			//'dataLocation'           	 : url + 'modules/pickup/views/data/locations.json',
			maxDistanceID:'maxdistance',
			autoGeocode: true,
			defaultLoc: true,
			defaultLat: lat,
			defaultLng : lng,
			dataType: 'json',
			dataRaw: stores,
			geocodeID: 'currentLoc',
			locationsPerPage: 20,
			storeLimit: 500,
			pagination: true,
			taxonomyFilters : {
				'country'	: 'country-filter',
				'state'		: 'state-filter',
				'city' 		: 'city-filter',
			},
			lengthUnit: (distance_unit == 'km')?'km':'m',
			'mapSettings'   : {
				zoom : parseInt(map_zoom_level),
			},		
			//dataLocation: 'data/locations.json'	
			sortBy: {
				method: 'numeric',
				order: 'asc',
                prop: 'ordering'
            },	
		});
	}
	

	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(successLoc, errorLoc);
	} else {
		console.log('geolocation not supported');
		loadStoreLocator(default_lat, default_lng);
	}	
	
	
	function successLoc(position) {
		loadStoreLocator(position.coords.latitude, position.coords.longitude);
	}

	function errorLoc(msg) {
		loadStoreLocator(default_lat, default_lng);
		console.log('error: ' + msg.message);
	}	
	
	
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
		$('#city').change();
	});
	
	$('#city option').hide();
	
});	