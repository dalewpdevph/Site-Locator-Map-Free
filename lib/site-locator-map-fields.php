<?php

class SiteLocatorMapFields {
	
	protected $option_name = "site-map-fields";
	protected $loc_meta;
	protected $post_id;
	protected $meta_prefix = "slm_map_field_";
	protected $option_fields;
	protected $window = false;
	protected $slm_meta;

	
	function __construct() {
		global $site_locator_map_admin;

		$this->option_fields = $this->get_option_fields();
		$this->slm_meta = $site_locator_map_admin->get_slm_meta();
	}
	
	
	
	function set_loc_meta($post_id) {
		$loc_meta = get_post_meta($post_id, "location_information", true);
		$this->loc_meta = $loc_meta;
		$this->post_id = $post_id;
	}
	
	function get_option_fields() {
		$option_name = $this->option_name;
		$of_loc_info = slm_of_get_option($option_name);
		if(!empty($of_loc_info)) {
			return $of_loc_info;
		} else {
			return false;
		}
	}
	
	function convert_field_name($name) {
		$oli_name = str_replace(" ", "-",strtolower($name));
		return $oli_name;
	}
	
	function get_meta_value($oli_name) {
		
		$oli_name = $this->convert_field_name($oli_name);
		$slm_metas = $this->slm_meta;
		
		if(in_array($oli_name, $slm_metas)) {
			$slm_meta = get_post_meta($this->post_id, "slm_meta", true);			
			$loc_meta = $slm_meta[$oli_name];
		} else {
			$loc_meta = get_post_meta($this->post_id, $this->meta_prefix . $oli_name, true);
		}
		
		if(!empty($loc_meta)) {
			return $loc_meta;
		} else {
			return false;
		}
	}
	
	function set_meta_fields() {
	
		$loc_meta = $this->loc_meta;
		
		if(!isset($loc_meta)) return false;
		
		$of_loc_info = $this->option_fields;
		if($of_loc_info) {
			foreach($of_loc_info as $oli) {
				echo "<div class='loc-field'";
					echo "<label for='location_website'>" . $oli['value'] . ": </label>";
	
					$oli_name = $this->convert_field_name($oli['value']);
					$loc_value = $this->get_meta_value($oli['value']);
					if($oli['type'] == "multiple") {
						echo "<br /><textarea rows=10 cols=60 name='location_" . $oli_name . "'>" . $loc_value . "</textarea>";
					} else {
						echo "<input size=60 type='text' name='location_" . $oli_name . "' value='" . $loc_value . "' />";
					}
				echo "</div>";
			}
		}
	}
	
	function save_meta_fields($request) {
	
		$loc_meta = $this->loc_meta;
		
		if(empty($loc_meta)) {
			$loc_meta = array();
		}
		
		$of_loc_info = $this->option_fields;

		if(!empty($of_loc_info)) {
			foreach($of_loc_info as $oli) {
				$oli_name = $this->convert_field_name($oli['value']);

				$loc_value = $request['location_' . $oli_name];;
				
					$post_id = $this->post_id;
					update_post_meta($post_id, $this->meta_prefix . $oli_name, $loc_value);
			}
		}
	}
	
	function set_info_window() {
		$this->window = true;
	}
	
	function get_fields_infowindow() {
		$of_loc_info = $this->option_fields;
		$fields = array();
		if(!empty($of_loc_info)) {
			foreach($of_loc_info as $ok => $ov) {
				
				if($ov['showinfo'] == "no") {
					unset($of_loc_info[$ok]);
				}
			}
		}
		if(!empty($of_loc_info)) {
			return $of_loc_info;
		} else {
			return false;
		}
	}
	
	function display_fields($label = true) {

		
		$window = $this->window;
		$fields = array();
 		if($window) {
			$fields = $this->get_fields_infowindow();
		} else {
			$fields = $this->option_fields;
		
		}

		
		if($fields) {
			foreach($fields as $fv) {
				$mv = $this->get_meta_value($fv['value']);
				if($mv) {
					$label_el =  "<label class='slm-label'>" . $fv['value'] . ":</label>";

					$label_str = ($label)? $label_el : "";
					echo "<p>$label_str <span>" . $mv . "</span></p>";
				}
			}
		}
		
	}
}

function get_slm_field($field) {
	global $post;

	$slmObj = new SiteLocatorMapFields();
	$slmObj->set_loc_meta($post->ID);
	
	$value = $slmObj->get_meta_value($field);
	if(!empty($value)) {
		return $value;
	} else {
		return false;
	}
}

function the_slm_field($field) {
	if(get_slm_field($field)) {
		echo get_slm_field($field);
	}
}

function slm_display_fields($label = "xx", $window = false) {
	global $post, $sitelocatormap;
	$db = $sitelocatormap->get_db_config();

	if(isset($db['field_label'])) {
		$label_bool = ($db['field_label'])? true : false;				
	}
	
	if(is_bool($label)) {
		$label_bool = $label;
	}
	$slmObj = new SiteLocatorMapFields();
	$slmObj->set_loc_meta($post->ID);
	if($window) {
		$slmObj->set_info_window();
	}
	$slmObj->display_fields($label_bool);
}

?>