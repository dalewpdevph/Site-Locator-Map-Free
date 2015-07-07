<?php
if(!function_exists("slm_optionsframework_option_name")) {
	function slm_optionsframework_option_name() {

		// This gets the theme name from the stylesheet
		$optname = "site_locator_map_option";
		$optname = preg_replace("/\W/", "_", strtolower($optname) );

		$slm_optionsframework_settings = get_option( 'slm_optionsframework' );
		$slm_optionsframework_settings['id'] = $optname;
		update_option( 'slm_optionsframework', $slm_optionsframework_settings );
	}
}
if ( ! function_exists( 'slm_optionsframework_init' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'lib/options-framework/options-framework.php';
}


function slm_optionsframework_menu($menu) {
	global $sitelocatormap;
	$pt = $sitelocatormap->get_post_type();
	$menu['menu_title'] = __( 'Options', 'slm_optionsframework');
	$menu['page_title'] = __( 'Site Locator Map Options', 'slm_optionsframework');
	$menu['menu_slug'] = 'slm-options-framework';
	$menu['parent_slug'] = 'edit.php?post_type=' . $pt;
	$menu['capability'] = 'edit_plugins';
	$menu['position'] = '73.3';
		
	return $menu;
}
add_filter("slm_optionsframework_menu","slm_optionsframework_menu");



add_filter('slm_of_options', 'slm_of_options'); 

function slm_of_options($options) {
	$options[] = array(
		'name' => __('More Features', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Purchase Site Locator Map paid version for more features', 'options_check'),
		'desc' => __(
		'
		<div>
		<a href="http://codecanyon.net/item/site-locator-map/7354406"><img src="' . SLM_URL . 'admin/images/site-locator-map-preview.jpg" alt="site locator map"/></a>
		<br />
		<a style="font-size: 30px" href="http://codecanyon.net/item/site-locator-map/7354406">Site Locator Map in Codecanyon</a>
		</div>
		<ul style="font-size: 25px">
		<li>- Search by location attributes</li>
		<li>- Support ajax search (page does not load when search for locations).</li>
		</ul>', 'options_check'),
		'type' => 'info');

	

	$on_ff = array("on" => "On", "off" => "Off");	
	$zoom_array = array();
	for($i = 1; $i < 22; $i++) {
		$zoom_array[$i] = $i;
	}
	
	$options[] = array(
		'name' => __('Maps', 'options_framework_theme'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('Show map', 'options_framework_theme'),
		'desc' => __('Select off to hide map.', 'options_framework_theme'),
		'id' => 'showmap',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);
	

		
		$options[] = array(
		'name' => __('Map Zoom', 'options_framework_theme'),
		'desc' => __('Map Zoom Level.', 'options_framework_theme'),
		'id' => 'map_zoom',
		'std' => '10',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $zoom_array);
	
	$options[] = array(
		'name' => __('Width', 'options_framework_theme'),
		'desc' => __('Input map\'s width. The number is in px.', 'options_framework_theme'),
		'id' => 'map_width',
		'std' => '1000',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Height', 'options_framework_theme'),
		'desc' => __('Input map\'s height. The number is in px.', 'options_framework_theme'),
		'id' => 'map_height',
		'std' => '600',
		'class' => 'mini',
		'type' => 'text');
	
	$align_array = array("left" => "Left", "center" => "Center", "right" => "Right");
	
	$options[] = array(
		'name' => __('Align', 'options_framework_theme'),
		'desc' => __('Choose map\'s alignment.', 'options_framework_theme'),
		'id' => 'map_align',
		'std' => 'center',
		'class' => 'mini',
		'type' => 'select',
		'options' => $align_array);	
	
	$type_array = array("ROADMAP" => "ROADMAP", "SATELLITE" => "SATELLITE", "TERRAIN" => "TERRAIN");
	
	$options[] = array(
		'name' => __('Type', 'options_framework_theme'),
		'desc' => __('Choose map type.', 'options_framework_theme'),
		'id' => 'map_type',
		'std' => 'ROADMAP',
		'class' => 'mini',
		'type' => 'select',
		'options' => $type_array);	
	
	
	
	$options[] = array(
		'name' => __('Mousewheel', 'options_framework_theme'),
		'desc' => __('Activate mousewheel.', 'options_framework_theme'),
		'id' => 'mousewheel',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);	
		
	$options[] = array(
		'name' => __('Pan control', 'options_framework_theme'),
		'desc' => __('Pan control option.', 'options_framework_theme'),
		'id' => 'pancontrol',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);	
		
	$options[] = array(
		'name' => __('Scale control', 'options_framework_theme'),
		'desc' => __('Scale control option.', 'options_framework_theme'),
		'id' => 'scalecontrol',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);
			
	$options[] = array(
		'name' => __('Map type control', 'options_framework_theme'),
		'desc' => __('Map type control option.', 'options_framework_theme'),
		'id' => 'typecontrol',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);	
	$options[] = array(
		'name' => __('Purchase Site Locator Map paid version for more features', 'options_check'),
		'desc' => __(
		'
		<div>

		<a style="font-size: 30px" href="http://codecanyon.net/item/site-locator-map/7354406">Site Locator Map in Codecanyon</a>
		</div>
		<ul style="font-size: 25px">
		<li>- Search by location attributes</li>
		<li>- Support ajax search (page does not load when search for locations).</li>
		</ul>', 'options_check'),
		'type' => 'info');
	$options[] = array(
		'name' => __('Lists', 'options_framework_theme'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Show List', 'options_framework_theme'),
		'desc' => __('Show list option.', 'options_framework_theme'),
		'id' => 'map_list',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);


	
	$options[] = array(
		'name' => __('Per Page', 'options_framework_theme'),
		'desc' => __('Input a number for number of locations per page.', 'options_framework_theme'),
		'id' => 'per_page',
		'std' => '25',
		'class' => 'mini',
		'type' => 'text');		

	$options[] = array(
		'name' => __('Purchase Site Locator Map paid version for more features', 'options_check'),
		'desc' => __(
		'
		<div>

		<a style="font-size: 30px" href="http://codecanyon.net/item/site-locator-map/7354406">Site Locator Map in Codecanyon</a>
		</div>
		<ul style="font-size: 25px">
		<li>- Search by location attributes</li>
		<li>- Support ajax search (page does not load when search for locations).</li>
		</ul>', 'options_check'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Search Form', 'options_framework_theme'),
		'type' => 'heading');

		
	$ct_list = slm_get_countries();
	$options[] = array(
		'name' => __('Country Restriction', 'options_framework_theme'),
		'desc' => __('Limit your autocomplete address to a country.', 'options_framework_theme'),
		'id' => 'slm_country_restrict',
		'std' => 'all',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $ct_list);	

	$options[] = array(
		'name' => __('Show search form', 'options_framework_theme'),
		'desc' => __('Select off to hide searcg form.', 'options_framework_theme'),
		'id' => 'showsearch',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);	
		
	$options[] = array(
		'name' => __('Search Measurement', 'options_framework_theme'),
		'desc' => __('Set search measurement miles or kilometers.', 'options_framework_theme'),
		'id' => 'search_measurement',
		'std' => 'mi',
		'class' => 'mini',
		'type' => 'select',
		'options' => array("mi" => "Miles", "km" => "kilometers"));

	$options[] = array(
		'name' => __('Purchase Site Locator Map paid version for more features', 'options_check'),
		'desc' => __(
		'
		<div>

		<a style="font-size: 30px" href="http://codecanyon.net/item/site-locator-map/7354406">Site Locator Map in Codecanyon</a>
		</div>
		<ul style="font-size: 25px">
		<li>- Search by location attributes</li>
		<li>- Support ajax search (page does not load when search for locations).</li>
		</ul>', 'options_check'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Fields', 'options_framework_theme'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('Field label', 'options_framework_theme'),
		'desc' => __('Select off to hide label. Does not override template set.', 'options_framework_theme'),
		'id' => 'field_label',
		'std' => 'on',
		'class' => 'mini',
		'type' => 'select',
		'options' => $on_ff);
	
	$repeater_array = array();
	$repeater_array[0] = array("value" => "Description", "type" => "multiple", "showinfo" => "no");
	
	$options[] = array(
		'name' => __('Sit Map Fields', 'options_framework_theme'),
		'desc' => __('Add some site map fields. You can drag each field to order them in the info window and in the list', 'options_framework_theme'),
		'id' => 'site-map-fields',
		'std' => $repeater_array,
		'type' => 'repeat_text'
	);
	$options[] = array(
		'name' => __('View', 'options_framework_theme'),
		'type' => 'heading');	
	$options[] = array(
		'name' => __('Section order', 'options_framework_theme'),
		'desc' => __('Drag itmes to sort sections on front view.', 'options_framework_theme'),
		'id' => 'sortable-map',
		'std' => 'Map,Search,Lists',
		'type' => 'sortable'
	);
	$options[] = array(
		'name' => __('Purchase Site Locator Map paid version for more features', 'options_check'),
		'desc' => __(
		'
		<div>

		<a style="font-size: 30px" href="http://codecanyon.net/item/site-locator-map/7354406">Site Locator Map in Codecanyon</a>
		</div>
		<ul style="font-size: 25px">
		<li>- Search by location attributes</li>
		<li>- Support ajax search (page does not load when search for locations).</li>
		</ul>', 'options_check'),
		'type' => 'info');	

	return $options;
};

function slm_repeat_text_option_type( $option_name, $option, $values ){

	$counter = 0;
	
	$output = '<ul class="of-repeat-loop of-sortable">';
	
	$bg = " transparent";
	if( is_array( $values ) ) foreach ( (array)$values as $value ){
		$bg = ($bg == "#dddddd") ? " transparent" : "#dddddd";
		$output .= '<li style="padding: 10px; background-color: ' . $bg . '" class="of-repeat-group of-sortable-items">';
		$output .= '<input class="of-input" data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$counter.'][value]' ) . '" type="text" value="' . $value['value'] . '" />';
		if($value['value'] != "Description") {
			$output .= '<button class="dodelete button icon delete">'. __('Remove') .'</button>';
		}
		$ch_s = ($value['type'] == "single")? "checked" : "";
		$ch_m = ($value['type'] == "multiple")? "checked" : "";
		$output .= '<div class="of-type-choices">
		<h3>Input Type: </h3><br/>
		<input ' . $ch_s . ' class="of-input-radio" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$counter.'][type]' ) . '" type="radio" value="single" /> <label>Single</label><br /><br />';
		$output .= '<input class="of-input-radio" ' . $ch_m . ' name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$counter.'][type]' ) . '" type="radio" value="multiple" /> <label>Multiple</label></div>';
		
		$ch_y = ($value['showinfo'] == "yes")? "checked" : "";
		$ch_n = ($value['showinfo'] == "no")? "checked" : "";
		$output .= '<br/><div class="of-type-choices">
		<h3>Show on Map Info Window: </h3><br/><input class="of-input-info-radio" ' . $ch_y . ' name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$counter.'][showinfo]' ) . '" type="radio" value="yes" /> <label>Yes</label><br /><br />';
		$output .= '<input class="of-input-info-radio" ' . $ch_n . ' name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$counter.'][showinfo]' ) . '" type="radio" value="no" /> <label>No</label></div>';
		
		

		$output .= '</li><!--.of-repeat-group-->';

		$counter++;
	}

	$output .= '<li class="of-repeat-group of-sortable-items to-copy">';
	$output .= '<input class="of-input" data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" type="text" value="Default" />';	
	$output .= '<button class="dodelete button icon delete">'. __('Remove') .'</button>';
	
	$output .= '<div class="of-type-choices">
	<h3>Input Type: </h3><br/>
	<input class="of-input-radio" name="' . esc_attr( $option_name . '[' . $option['id'] . '][type]' ) . '" type="radio" value="single" /> <label>Single</label><br /><br />';
	$output .= '<input class="of-input-radio" name="' . esc_attr( $option_name . '[' . $option['id'] . '][type]' ) . '" type="radio" value="multiple" /> <label>Multiple</label></div>';	
	
	$output .= '<br /><div class="of-type-choices">
	<h3>Show on Map Info Window: </h3><br/>
	<input class="of-input-info-radio" name="' . esc_attr( $option_name . '[' . $option['id'] . '][showinfo]' ) . '" type="radio" value="yes" /> <label>Yes</label><br /><br />';
	$output .= '<input class="of-input-info-radio" name="' . esc_attr( $option_name . '[' . $option['id'] . '][showinfo]' ) . '" type="radio" value="no" /> <label>No</label></div>';
		
	$output .= '</li><!--.of-repeat-group-->';


	$output .= '<button class="docopy button icon add">Add</button>';

	$output .= '</ul><!--.of-repeat-loop-->';

	return $output;
}
add_filter( 'slm_optionsframework_repeat_text', 'slm_repeat_text_option_type', 10, 3 );

function slm_sortable( $option_name, $option, $values ){

	$counter = 0;
	
	$output = '<ul class="of-sortable">';
	if(!is_array($values)) {
		$values = explode(",",$values);
	}
	if(!empty($values)) {
		foreach($values as $valk => $val) {
			$output.= '<li class="ui-state-default of-sortable-items"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span> ' . ucwords($val); 
			$output.= "<input type='hidden' class='slm_of_sortable_position' name='" . esc_attr( $option_name . '[' . $option['id'] . '][' . $counter . ']' ) . "' value='" .strtolower($val). "' />";
			$output.='</li>';
			$counter++;
		}
		
	}
	
	$output .= "</ul>";

	return $output;
}
add_filter( 'slm_optionsframework_sortable', 'slm_sortable', 11, 3 );


/*
 * Sanitize Repeat Fields
 */
function slm_sanitize_repeat_field( $input, $option ){

	if(is_array($input)) {

		$output = array_values($input);

		foreach($output as $ik => $in) {
			$in['value'] = sanitize_text_field($in['value']);
			$in['type'] = sanitize_text_field($in['type']);
			$in['showinfo'] = sanitize_text_field($in['showinfo']);
		}
		
		return $output;
	}

	return $input;
	
}
add_filter( 'slm_of_sanitize_repeat_text', 'slm_sanitize_repeat_field', 10, 2 );

function slm_sanitize_sortable( $input, $option ){

	
	if(is_array($input)) {
		$input = array_values($input);
	}
	return $input;
}
add_filter( 'slm_of_sanitize_sortable', 'slm_sanitize_sortable', 10, 2 );


/*
 * Custom repeating field scripts
 * Add and Delete buttons
 */
function slm_of_repeat_script() {	?>

	<style>
		#slm_optionsframework .to-copy {display: none;}

		#slm_optionsframework .of-repeat-group {
			overflow: hidden;
			margin-bottom: 1.4em;
		}
		#slm_optionsframework .of-repeat-group .of-input {
			width: 80%;
		}

		.of-repeat-group .dodelete {
			float: right;
		}
		
		.of-sortable li {
			cursor: move;
			padding-right: 30px
		}
		
		.of-sortable-placeholder {
			border: 3px dashed #d3d3d3;
	
		}
		
		#slm_optionsframework h3 {
			display: inline-block;
			text-align: left
		}
		
		
		.of-sortable-items {
			display: block;
			padding: 10px 5px;
			
		}
		
		.of-sortable-items .ui-icon {
			float: left;
			background-position: -128px -50px;
		}
	</style>

	<script type="text/javascript">
	jQuery(function($){

		delete_repeat_group();
		$(".docopy").on("click", function(e){
		
			// the loop object
			$loop = $(this).parent();

			// the group to copy
			$group = $loop.find('.to-copy').clone().insertBefore($(this)).removeClass('to-copy');
			
			set_attr_init($group);
			return false;
		});

		$( ".of-sortable" ).sortable({
			items: '.of-sortable-items',
			cursor:'move', 	
						
			forcePlaceholderSize: true, 
			placeholder: "of-sortable-placeholder"
		});
		
		function delete_repeat_group() {
			$(".of-repeat-group").on("click", ".dodelete", function(e){
			  $(this).parent().remove();
			  e.preventDefault();			
			});
		}
		
		function set_attr_init(group) {

			$group = $(group);
			$loop = $(group).parent();
			// the new input
			$input = $group.find('.of-input');
			$radio = $group.find('.of-input-radio');
			$showinfo = $group.find('.of-input-info-radio');
			
			input_name = $input.attr('data-rel');
			count = $loop.children('.of-repeat-group').not('.to-copy').length;

			$input.attr('name', input_name + '[' + ( count - 1 ) + '][value]');
			$radio.attr('name', input_name + '[' + ( count - 1 ) + '][type]');
			$showinfo.attr('name', input_name + '[' + ( count - 1 ) + '][showinfo]');
			
			
			
				$radio.eq(0).attr("checked", "checked");
				$showinfo.eq(1).attr("checked", "checked");
					
			
			delete_repeat_group();
		}
	});
	
	</script>
<?php
}
add_action( 'slm_optionsframework_custom_scripts', 'slm_of_repeat_script' );

function slm_get_countries() {
$countryList = array(
	"ALL" => "All",
	"AF" => "Afghanistan",
	"AL" => "Albania",
	"DZ" => "Algeria",
	"AS" => "American Samoa",
	"AD" => "Andorra",
	"AO" => "Angola",
	"AI" => "Anguilla",
	"AQ" => "Antarctica",
	"AG" => "Antigua and Barbuda",
	"AR" => "Argentina",
	"AM" => "Armenia",
	"AW" => "Aruba",
	"AU" => "Australia",
	"AT" => "Austria",
	"AZ" => "Azerbaijan",
	"BS" => "Bahamas",
	"BH" => "Bahrain",
	"BD" => "Bangladesh",
	"BB" => "Barbados",
	"BY" => "Belarus",
	"BE" => "Belgium",
	"BZ" => "Belize",
	"BJ" => "Benin",
	"BM" => "Bermuda",
	"BT" => "Bhutan",
	"BO" => "Bolivia",
	"BA" => "Bosnia and Herzegovina",
	"BW" => "Botswana",
	"BV" => "Bouvet Island",
	"BR" => "Brazil",
	"BQ" => "British Antarctic Territory",
	"IO" => "British Indian Ocean Territory",
	"VG" => "British Virgin Islands",
	"BN" => "Brunei",
	"BG" => "Bulgaria",
	"BF" => "Burkina Faso",
	"BI" => "Burundi",
	"KH" => "Cambodia",
	"CM" => "Cameroon",
	"CA" => "Canada",
	"CT" => "Canton and Enderbury Islands",
	"CV" => "Cape Verde",
	"KY" => "Cayman Islands",
	"CF" => "Central African Republic",
	"TD" => "Chad",
	"CL" => "Chile",
	"CN" => "China",
	"CX" => "Christmas Island",
	"CC" => "Cocos [Keeling] Islands",
	"CO" => "Colombia",
	"KM" => "Comoros",
	"CG" => "Congo - Brazzaville",
	"CD" => "Congo - Kinshasa",
	"CK" => "Cook Islands",
	"CR" => "Costa Rica",
	"HR" => "Croatia",
	"CU" => "Cuba",
	"CY" => "Cyprus",
	"CZ" => "Czech Republic",
	"CI" => "Côte d’Ivoire",
	"DK" => "Denmark",
	"DJ" => "Djibouti",
	"DM" => "Dominica",
	"DO" => "Dominican Republic",
	"NQ" => "Dronning Maud Land",
	"DD" => "East Germany",
	"EC" => "Ecuador",
	"EG" => "Egypt",
	"SV" => "El Salvador",
	"GQ" => "Equatorial Guinea",
	"ER" => "Eritrea",
	"EE" => "Estonia",
	"ET" => "Ethiopia",
	"FK" => "Falkland Islands",
	"FO" => "Faroe Islands",
	"FJ" => "Fiji",
	"FI" => "Finland",
	"FR" => "France",
	"GF" => "French Guiana",
	"PF" => "French Polynesia",
	"TF" => "French Southern Territories",
	"FQ" => "French Southern and Antarctic Territories",
	"GA" => "Gabon",
	"GM" => "Gambia",
	"GE" => "Georgia",
	"DE" => "Germany",
	"GH" => "Ghana",
	"GI" => "Gibraltar",
	"GR" => "Greece",
	"GL" => "Greenland",
	"GD" => "Grenada",
	"GP" => "Guadeloupe",
	"GU" => "Guam",
	"GT" => "Guatemala",
	"GG" => "Guernsey",
	"GN" => "Guinea",
	"GW" => "Guinea-Bissau",
	"GY" => "Guyana",
	"HT" => "Haiti",
	"HM" => "Heard Island and McDonald Islands",
	"HN" => "Honduras",
	"HK" => "Hong Kong SAR China",
	"HU" => "Hungary",
	"IS" => "Iceland",
	"IN" => "India",
	"ID" => "Indonesia",
	"IR" => "Iran",
	"IQ" => "Iraq",
	"IE" => "Ireland",
	"IM" => "Isle of Man",
	"IL" => "Israel",
	"IT" => "Italy",
	"JM" => "Jamaica",
	"JP" => "Japan",
	"JE" => "Jersey",
	"JT" => "Johnston Island",
	"JO" => "Jordan",
	"KZ" => "Kazakhstan",
	"KE" => "Kenya",
	"KI" => "Kiribati",
	"KW" => "Kuwait",
	"KG" => "Kyrgyzstan",
	"LA" => "Laos",
	"LV" => "Latvia",
	"LB" => "Lebanon",
	"LS" => "Lesotho",
	"LR" => "Liberia",
	"LY" => "Libya",
	"LI" => "Liechtenstein",
	"LT" => "Lithuania",
	"LU" => "Luxembourg",
	"MO" => "Macau SAR China",
	"MK" => "Macedonia",
	"MG" => "Madagascar",
	"MW" => "Malawi",
	"MY" => "Malaysia",
	"MV" => "Maldives",
	"ML" => "Mali",
	"MT" => "Malta",
	"MH" => "Marshall Islands",
	"MQ" => "Martinique",
	"MR" => "Mauritania",
	"MU" => "Mauritius",
	"YT" => "Mayotte",
	"FX" => "Metropolitan France",
	"MX" => "Mexico",
	"FM" => "Micronesia",
	"MI" => "Midway Islands",
	"MD" => "Moldova",
	"MC" => "Monaco",
	"MN" => "Mongolia",
	"ME" => "Montenegro",
	"MS" => "Montserrat",
	"MA" => "Morocco",
	"MZ" => "Mozambique",
	"MM" => "Myanmar [Burma]",
	"NA" => "Namibia",
	"NR" => "Nauru",
	"NP" => "Nepal",
	"NL" => "Netherlands",
	"AN" => "Netherlands Antilles",
	"NT" => "Neutral Zone",
	"NC" => "New Caledonia",
	"NZ" => "New Zealand",
	"NI" => "Nicaragua",
	"NE" => "Niger",
	"NG" => "Nigeria",
	"NU" => "Niue",
	"NF" => "Norfolk Island",
	"KP" => "North Korea",
	"VD" => "North Vietnam",
	"MP" => "Northern Mariana Islands",
	"NO" => "Norway",
	"OM" => "Oman",
	"PC" => "Pacific Islands Trust Territory",
	"PK" => "Pakistan",
	"PW" => "Palau",
	"PS" => "Palestinian Territories",
	"PA" => "Panama",
	"PZ" => "Panama Canal Zone",
	"PG" => "Papua New Guinea",
	"PY" => "Paraguay",
	"YD" => "People's Democratic Republic of Yemen",
	"PE" => "Peru",
	"PH" => "Philippines",
	"PN" => "Pitcairn Islands",
	"PL" => "Poland",
	"PT" => "Portugal",
	"PR" => "Puerto Rico",
	"QA" => "Qatar",
	"RO" => "Romania",
	"RU" => "Russia",
	"RW" => "Rwanda",
	"RE" => "Réunion",
	"BL" => "Saint Barthélemy",
	"SH" => "Saint Helena",
	"KN" => "Saint Kitts and Nevis",
	"LC" => "Saint Lucia",
	"MF" => "Saint Martin",
	"PM" => "Saint Pierre and Miquelon",
	"VC" => "Saint Vincent and the Grenadines",
	"WS" => "Samoa",
	"SM" => "San Marino",
	"SA" => "Saudi Arabia",
	"SN" => "Senegal",
	"RS" => "Serbia",
	"CS" => "Serbia and Montenegro",
	"SC" => "Seychelles",
	"SL" => "Sierra Leone",
	"SG" => "Singapore",
	"SK" => "Slovakia",
	"SI" => "Slovenia",
	"SB" => "Solomon Islands",
	"SO" => "Somalia",
	"ZA" => "South Africa",
	"GS" => "South Georgia and the South Sandwich Islands",
	"KR" => "South Korea",
	"ES" => "Spain",
	"LK" => "Sri Lanka",
	"SD" => "Sudan",
	"SR" => "Suriname",
	"SJ" => "Svalbard and Jan Mayen",
	"SZ" => "Swaziland",
	"SE" => "Sweden",
	"CH" => "Switzerland",
	"SY" => "Syria",
	"ST" => "São Tomé and Príncipe",
	"TW" => "Taiwan",
	"TJ" => "Tajikistan",
	"TZ" => "Tanzania",
	"TH" => "Thailand",
	"TL" => "Timor-Leste",
	"TG" => "Togo",
	"TK" => "Tokelau",
	"TO" => "Tonga",
	"TT" => "Trinidad and Tobago",
	"TN" => "Tunisia",
	"TR" => "Turkey",
	"TM" => "Turkmenistan",
	"TC" => "Turks and Caicos Islands",
	"TV" => "Tuvalu",
	"UM" => "U.S. Minor Outlying Islands",
	"PU" => "U.S. Miscellaneous Pacific Islands",
	"VI" => "U.S. Virgin Islands",
	"UG" => "Uganda",
	"UA" => "Ukraine",
	"SU" => "Union of Soviet Socialist Republics",
	"AE" => "United Arab Emirates",
	"GB" => "United Kingdom",
	"US" => "United States",
	"ZZ" => "Unknown or Invalid Region",
	"UY" => "Uruguay",
	"UZ" => "Uzbekistan",
	"VU" => "Vanuatu",
	"VA" => "Vatican City",
	"VE" => "Venezuela",
	"VN" => "Vietnam",
	"WK" => "Wake Island",
	"WF" => "Wallis and Futuna",
	"EH" => "Western Sahara",
	"YE" => "Yemen",
	"ZM" => "Zambia",
	"ZW" => "Zimbabwe",
	"AX" => "Åland Islands",
);
return $countryList;
}
?>