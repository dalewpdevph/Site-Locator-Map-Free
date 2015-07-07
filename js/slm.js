var markersarr = [];
var markerCluster;
var map;
jQuery(document).ready(function($) {

	function SLM(slm_opt){
		
		this.map_el = slm_opt.map_el;
		
		this.data = $.extend(true, {}, this.defaults, slm_opt);
		
	}; 
	
	SLM.prototype = {
		markers : [],
		windows : [],
		defaults : {
			markers 		: {},
			zoom			: 10,
			type			: 'ROADMAP',
			mousewheel 		: true,
			scalecontrol 	: true,
			zoompancontrol 	: true,
			typecontrol 	: true,
			display			: 'map',
			highlight		: true,
			highlight_class : 'slm_highlight',
			num				: 1
		},
		
		_empty : function (v){
				return (!v || /^\s*$/.test(v));
			},
			
		_get_latlng : function(i){
			var me = this,
				g  = new google.maps.Geocoder(),
				m  = me.data.markers,
				a  = m[i]['address'];
		
			g.geocode({address:a}, function(result, status){
				me.counter--;
				if(status && status == "OK"){
					m[i]['latlng'] = new google.maps.LatLng(result[0]['geometry']['location'].lat(), result[0]['geometry']['location'].lng())
				}else{
					m[i]['invalid'] = true;
				}
				
				// Load map after markers are checked
				if(me.counter == 0){
					me._load_map();
				}
			});
		},
		
		_load_map : function() {
			
			var me = this,
				m  = me.data.markers,
				h  = m.length,
				c  = 0;
				v  = 0; // Number of valid points
				map_el = this.map_el;
			
			while(c < h && m[c]['invalid']) c++;
			
			if(c < h){
				me.map = new google.maps.Map($(map_el)[0], {
						zoom: me.data.zoom,
						center: m[c].latlng,
						mapTypeId: google.maps.MapTypeId[me.data.type],							
						// Map controls
						panControl: me.data.zoompancontrol,
						scaleControl: me.data.scalecontrol,
						zoomControl: me.data.zoompancontrol,
						mapTypeControl: me.data.typecontrol,
						scrollwheel: me.data.mousewheel
				});
				me.markers = [];
				var map = me.map,
					bounds = new google.maps.LatLngBounds(),
					open_by_default;
				
				google.maps.event.addListenerOnce(map, 'idle', function(){
					setTimeout(function(){
						if(open_by_default)
									google.maps.event.trigger(me.markers[open_by_default - 1], 'click');
					}, 1000);				
				});
				
				me.infowindow = new google.maps.InfoWindow({maxWidth:340});
				
				for (var i = c; i < h; i++){		
					if(!m[i]['invalid']){
						bounds.extend(m[i].latlng);
						var marker = new google.maps.Marker({
													  position: m[i].latlng,
													  map: map,
													  icon: m[i].map_icon,
													  title:((m[i].address) ? m[i].address : '')
													 });

													 
						var push = [];
						markersarr[m[i].post_id] = marker;
						//markersarr[i] = marker;
						marker.id = i;
						me.markers.push(marker);
					
						if(m[i]['open'] == "true"){
							open_by_default = me.markers.length;
						}
				
						me.bindInfoWindow(marker, map, me.infowindow, m[i].info);
						me.add_post_class(marker, m[i].post_id);
						me.remove_post_class(marker, m[i].post_id);
					}
				}	

				if (h > 1) {
				  map.fitBounds(bounds);
				}
				else if (h == 1) {
				  map.setCenter(bounds.getCenter());
				  map.setZoom(me.data.zoom);
				}

				
				if(me.data.num < 2) { // Disable clustere on second object
					var markerCluster = new MarkerClusterer(map, me.markers);
				}
			}
			
			
					
		},
		// Open the marker popup
		bindInfoWindow: function(marker, map, infowindow, strDescription) {
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent("<div style='min-height: 100px'>" + strDescription + "</div>");
				infowindow.open(map, marker);
			});
		},
		
		
		// Add class to post area			
		add_post_class: function(marker, pid) {
			google.maps.event.addListener(marker, 'mouseover', function(){
				$('.post-'+pid).addClass("marker_on_hover");
			});
		},
		
		remove_post_class: function(marker, pid){
			google.maps.event.addListener(marker, 'mouseout', function(){
				$('.post-'+pid).removeClass("marker_on_hover");
			});
		},
		
		
		set_map: function(){
			if(this.data.markers.length){
			
				var m = this.data.markers,
					h = m.length;
					
				this.counter = h; // Counter for long and lat
				
 				for(var i=0; i < h; i++){
			
					if( (this._empty(m[i].latitude) || this._empty(m[i].longitude)) && !this._empty(m[i].address)){
						this._get_latlng(i);
						
					}else if(this._empty(m[i].latitude) && this._empty(m[i].longitude)){
						// Invalid address remove from list
						m[i]['invalid'] = true;
						this.counter--;
					}else{
						m[i]['latlng'] = new google.maps.LatLng(m[i].latitude, m[i].longitude);
						this.counter--;
					}
					
				} 
				
				// Load map after markers checked
				if(this.counter == 0){
					this._load_map();
				}
			}

			
		},
		

			
	};
	
			// Onload function
		window['slm_init'] = function(){
			
			$('.slm-map').each(function(){
				
				var rel = $(this).attr("rel");
				if(rel != null) {
					if(slm_meta.length) {
						
						slm_map_data[rel].num = rel;
						
						var slm = new SLM(slm_map_data[rel]);
						slm.set_map();
					
					}
				}
				
			});
			
			var map_form = $('.slm-map-form');
			if(map_form.length > 0) {
				slm_search_address();
			}
						
		};
		

		var map = $('.slm-map');
		var map_form = $('.slm-map-form');
		if(map.length > 0 || map_form.length > 0){
			slm_append_clusterer()
			// Include google map script and load the maps api
			$('<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false&libraries=places&callback=slm_init"></script>').appendTo('body');	
			
		}



	if(slm_ajax_search == true) {
	 
		jQuery("input[name='submit_slm']").click(function(e) {
			
			var ser = jQuery(this).parents("form").serializeObject();
			var click_this = $(this);
			var form_el = click_this.parents().find(".slm-map-form");
			
			if(form_el.data("ext") == null) {
				e.preventDefault();
			
				var methodObj = {action: "slm_ajax_result_map"};
				var data = jQuery.extend({}, ser, methodObj);
				form_el.block({ message: "Loading..." }); 
				click_this.parents().find(".slm-map").block({ message: "Loading..." }); 
				click_this.parents().find(".slm-lists").block({ message: "Loading..." }); 
				click_this.parents().find(".slm-pagination").block({ message: "Loading..." }); 
				jQuery.get( slm_ajax_url, data, function( response ) {
					$(form_el).unblock(); 
					var arrx = new Array();
					var ix = 0;
					jQuery.each(response.markers, function(idx, obj) {
						arrx[ix] = obj;
						ix++;
					}); 

					var rel = click_this.parents().find(".slm-map").attr("rel");
					var count = response.count;
					if(rel != null) {
						if(slm_meta.length) {				
							slm_remove_markers();
							slm_map_data[rel].num = rel;
							
							if(arrx[0].address == "World") {
								slm_map_data[rel].zoom = 1;
							}
							if($(".slm-count").length > 0) {
								$(".slm-count").text(count);
							}
							slm_map_data[rel].markers = arrx;

							var slm = new SLM(slm_map_data[rel]);
						

							slm.set_map();				
							
							
						}
					}

				}, "json");		
				
				var methodObj = {action: "slm_ajax_result_lists"};
				var data = jQuery.extend({}, ser, methodObj);
				
				jQuery.get( slm_ajax_url, data, function( response ) {
					
					$(".slm-lists").replaceWith(response);

				});		
				var url = form_el.attr("action");
				var queryvar = form_el.data("queryvar");
				var front = form_el.data("front");
				var methodObj = {action: "slm_ajax_result_pagination", url: url, queryvar: queryvar, front: front};
				var data = jQuery.extend({}, ser, methodObj);
				
				jQuery.get( slm_ajax_url, data, function( response ) {
					if(response != false) {
						
						$(".slm-pagination").replaceWith(response);
					}

				});
			}
			
		})
	}
})
jQuery.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function slm_remove_markers() {
			
	for(i=0; i< markersarr.length; i++) {
	
		if(markersarr[i] != null) {
			markersarr[i].setMap(null);
			
		}
		
	}
}

function slm_search_address() {
	
	var options = {};
	if(slm_ct_restrict != "ALL") {
		options = { componentRestrictions: {'country': slm_ct_restrict} };
	}
	if(jQuery("#slm_address").length > 0 ) {
		var autocomplete = new google.maps.places.Autocomplete(jQuery("#slm_address")[0], options);

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			
		}); 
	}
	
	jQuery("#slm_address").focusin(function () {
        jQuery(document).keypress(function (e) {
            if (e.which == 13) {
               
                var firstResult = jQuery(".pac-container .pac-item:first").text();
              
				jQuery("#slm_address").val(firstResult);              
            }
        });
    });	
	
}




function slm_append_clusterer() {
	jQuery('<script type="text/javascript" src="'+ 	slm_url +'js/markerclusterer.js"></script>').appendTo('body');	
}
		
function slm_open_window(num) {

 google.maps.event.trigger(markersarr[num], 'click');
 var scrollTarget = jQuery(".slm-map").offset().top;
 jQuery('html,body').animate({scrollTop:scrollTarget}, 1000, "swing")
 
 return false;
}