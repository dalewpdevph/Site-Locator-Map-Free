<?php

class SiteLocatorMapLocation {
	
	protected $post_id;
	protected $field_obj;
	
	function __construct($post_id) {

		
		$this->post_id = $post_id;
		$site_locator_map_fields = new SiteLocatorMapFields();
		$site_locator_map_fields->set_loc_meta($post_id);
		$this->field_obj = $site_locator_map_fields;
	}
	
	
	
}

?>