<?php


class SiteLocatorMapAdmin {

	protected $slm_meta = array("address", "longitude", "latitude");
	
	function __construct() {
		
		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes')); // Adds location information.
		add_action( 'save_post', array($this, 'save_postdata'),99); //saves	
		add_action("admin_init", array($this, "admin_init"));
		
	}
	
	function get_slm_meta() {
		return $this->slm_meta;
	}
	
	function add_meta_boxes() {
		global $sitelocatormap;
		$post_type = $sitelocatormap->get_post_type();
		add_meta_box('site-locator-information', __('Location Information', 'site-locator-map'), array($this, 'location_information'), $post_type, "normal");
		add_meta_box("site-locator-map", __('Location Map', 'site-locator-map'), array($this, "location_map"), $post_type, "normal");
		
	}
	
	function location_information($post) {

		$post_id = $post->ID;		
		
	
		wp_nonce_field(__FILE__, "slm_location_info");
		// Add location form fields

		$site_locator_map_fields = new SiteLocatorMapFields();
		$site_locator_map_fields->set_loc_meta($post_id);
		
		$site_locator_map_fields->set_meta_fields();
		
		echo "<div class='clear'></div>";

	} 
	function save_postdata($post_id) {

		if ( empty($_POST['slm_location_info']) || !wp_verify_nonce($_POST['slm_location_info'],__FILE__) )
		{
		   return false;
		}
		
		$site_locator_map_fields = new SiteLocatorMapFields();
		$site_locator_map_fields->set_loc_meta($post_id);
		
		$site_locator_map_fields->save_meta_fields($_POST);
		
	}	
		

	//Display map form
	function location_map($post) {

		// Add security to the form
		
		echo '<input type="hidden" name="slm_map_form" value="' . wp_create_nonce(__FILE__) . '" />';
		?>
		<?php
		$post_id = $post->ID;
			// Get post slm meta
			
			
		?>
		<script type="text/javascript">
			var slm_default_marker = "<?php echo SLM_URL . '/images/icons/marker.png'; ?>";
			var slm_meta = {};
						
			<?php
			$slm_meta = get_post_meta($post_id, "slm_meta", true);
			$slm_names = $this->slm_meta;

			$slm_inputs = array();
			$slm_js = "";
			if(empty($slm_meta)) {
				
				$slm_js.=  'slm_meta["address"]="Washington, DC, USA";'; 
				$slm_js.=  'slm_meta["latitude"]="38.8951118";'; 
				$slm_js.=  'slm_meta["longitude"]="-77.0363658";'; 
								
				if($slm_names) {					
					foreach($slm_names as $slk => $slv) {
						$slm_inputs[$slv] = "";
					}
				}
			} else {
				$slm_js = "";
				
				if($slm_names) {					
					foreach($slm_names as $slk => $slv) {
						if(!empty($slm_meta[$slv])) {
							$val = $slm_meta[$slv];
						} else {
							$val = "";
						}
						$slm_js.=  'slm_meta["' . $slv . '"]="' . $val . '";'; 
						$slm_inputs[$slv] = $val;
						
					}
				}
			}
			echo $slm_js;


			?>
		</script>
		
		<div class="map_form">
			<div id="slm_left">
				<?php
					if(!empty($slm_inputs)) {
						foreach($slm_inputs as $slik => $sliv) {
						?>
						<p><label><?php echo ucwords($slik); ?>: </label><input type="text" id="slm_<?php echo $slik; ?>" name="slm_meta[<?php echo $slik; ?>]" size="60" value="<?php echo $sliv; ?>" /></p>
						<?php
						}
					}
				?>

				<p><input type="button" onclick="slm_set_latlng(this);" name="get_data" value="Get Data" /> <input type="button" onclick="slm_form_reset(this);" name="clear_form" value="Clear" /></p>
				<input type="hidden" name="slm_meta[post_id]" value="<?php echo $post_id; ?>" />		
			</div>
			<div id="slm_right">
				<div id="slm_map_container" class="slm_map_container" style="width:400px; height:250px; border:1px dotted #CCC;"></div>
			</div>
		</div>
		<?php
	}
	
	function admin_init() {
		
		// Set map form meta data
		add_action("save_post", array($this, "save_map_form"));
	}
	
	// Save map meta data
	function save_map_form($post_id) {
		global $current_user;
		
		if ( empty($_POST['slm_map_form']) || !wp_verify_nonce($_POST['slm_map_form'],__FILE__) ) return $post_id;
		$slm_meta = $_POST["slm_meta"];
		
		update_post_meta($post_id, "slm_meta", $slm_meta);
	}
	
}

$site_locator_map_admin = new SiteLocatorMapAdmin();

?>