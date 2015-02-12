// Set map coordinate and address
function slm_set_latlng() {

	var f 			= jQuery('.slm_map_container').parents('.map_form'),
		a 			= jQuery('#slm_address').val(),
		longitude 	= jQuery('#slm_longitude').val(),
		latitude 	= jQuery('#slm_latitude').val(),
		request		= {};
	
	// Remove spaces
	longitude = longitude.replace(/^\s+/, '').replace(/\s+$/, '');
	latitude  = latitude.replace(/^\s+/, '').replace(/\s+$/, '');
	a = a.replace(/^\s+/, '').replace(/\s+$/, '');
	
	if(longitude.length && latitude.length){
		request['location'] = new google.maps.LatLng(latitude, longitude);
	}else if(a.length){
		request['address'] = a.replace(/[\n\r]/g, '');
	}else{
		return false;
	}	
	
	_get_latlng(request, function(result, status){
		if(status && status == "OK"){
			// Update meta
			var address   = result[0]['formatted_address'],
				latitude  = result[0]['geometry']['location'].lat(),
				longitude = result[0]['geometry']['location'].lng();
			
			if(address && latitude && longitude){
				jQuery('#slm_address').val(address);
				jQuery('#slm_longitude').val(longitude);
				jQuery('#slm_latitude').val(latitude);
				
				// Load map
				slm_load_map(f.find('.slm_map_container'),latitude, longitude);
			}
		}else{
			alert('The point is not located');
		}
		
	});
};



// Load Google map form
function slm_load_map(container, latitude, longitude){
	var c = container,
		f = c.parents('.map_form'),
		p = new google.maps.LatLng(latitude, longitude),
		m = new google.maps.Map(c[0], {
							zoom: 9,
							center: p,
							mapTypeId: google.maps.MapTypeId['ROADMAP'],
							
							// Show / Hide controls
							panControl: true,
							scaleControl: true,
							zoomControl: true,
							mapTypeControl: true,
							scrollWheel: true
					}),
		mk = new google.maps.Marker({
						  position: p,
						  map: m,
						  icon: new google.maps.MarkerImage(slm_default_marker),
						  draggable: true
					 });
			
		google.maps.event.addListener(mk, 'position_changed', function(){
			f.find('#slm_latitude').val(mk.getPosition().lat());
			f.find('#slm_longitude').val(mk.getPosition().lng());
	
			var latitude = mk.getPosition().lat();
			var longitude = mk.getPosition().lng();
			var request = {};
			request['location'] = new google.maps.LatLng(latitude, longitude);
			_get_latlng(request, function(result, status){
				if(status && status == "OK"){
			
					var address   = result[0]['formatted_address'];
					
					if(address){
						
						f.find('#slm_address').val(address);
					}
				}
			});
		});
		
		var autocomplete = new google.maps.places.Autocomplete(jQuery("#slm_address")[0], {});

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			
		}); 
};
// Set Map based on form input
window['slm_set_map'] = function(){
	var request = {};

	if(slm_meta['longitude'] && slm_meta['latitude']){
		request['location'] = new google.maps.LatLng(slm_meta['latitude'], slm_meta['longitude']);
	}else if(slm_meta['address']){
		request['address'] = slm_meta['address'].replace(/[\n\r]/g, '');
	}

	_get_latlng(request, function(result, status){
		if(status && status == "OK"){
	
			var address   = result[0]['formatted_address'],
				latitude  = result[0]['geometry']['location'].lat(),
				longitude = result[0]['geometry']['location'].lng();
			
			if(address && latitude && longitude){
				
				slm_load_map(jQuery('.slm_map_container'),latitude, longitude);
			}
		}
	});
}
// Call Google map Class
function _get_latlng(request, callback){
	var g = new google.maps.Geocoder();
	g.geocode(request, callback);
};


jQuery(document).ready(function($){
	// Include Google Map API library
	if(jQuery('.slm_map_container').length){
		jQuery('<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false&libraries=places&callback=slm_set_map"></script>').appendTo('body');
	}
	
})

// Empty form values
function slm_form_reset(el) {
	jQuery('#slm_address').val("");
	jQuery('#slm_longitude').val("");
	jQuery('#slm_latitude').val("");
}
