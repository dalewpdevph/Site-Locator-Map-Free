<?php
	
class SiteLocatorMapType {
	
	private $query_var;
	private $paginate;
	private $url;
	private $obj_query;
	private $post_type;
	private $db_config;
	private $result_args;
	private $loc_query;
	private $map_meta = false;
	private $slm_number = 1;
	
	function __construct( $post_type, $query_var, $paginate, $url, $db_config, $slm_number = 1) {
		$this->post_type = $post_type;
		$this->query_var = $query_var;
		$this->paginate = $paginate;
		$this->url = $url;
		$this->db_config = $db_config;
		$this->obj_query = new SiteLocatorMapQuery($this);		
		$this->slm_number = $slm_number;
		add_action("wp_footer", array($this, "map_footer")); // Print script if global variable is set to true
	}
	
	function set_search($radius, $address) {
		$this->obj_query->set_search($radius, $address);
	}
	
	function set_category($cat) {
		$this->cat = $cat;
		$this->obj_query->set_category($cat);
	}
	

	
	function set_single($pid) {
		$this->obj_query->set_single($pid);
	}	
	
	function set_add_args($args) {
		$this->obj_query->set_add_args($args);
	}
	
	function set_requests_search($req) {
	
		if(isset($req['slm_radius']) && isset($req['slm_address']) && !empty($req['slm_address'])) {		
			$this->set_search($req['slm_radius'], $req['slm_address']);
		}
		
		if(isset($req['slm_category'])) {
			
			$this->set_category($req['slm_category']);
		}
		

			
	}
	
	function get_query_var() {
		return $this->query_var;
	}
	
	function get_paginate() {
		return $this->paginate;
	}
	
	function get_url() {
		return $this->url;
	}
	
	function get_post_type() {
		return $this->post_type;
	}
	
	function get_db_config() {
		return $this->db_config;
	}
	
	function get_calc_info() {
		return $this->obj_query->get_calc_info();
	}
	
	function get_result_args() {
		$this->result_args = $this->obj_query->get_result_args();
		
		return $this->result_args;
	}
	
	function get_location_category() {
		$loc_cat = $this->obj_query->get_location_category();
		
		return $loc_cat;
	}
	
	function search_form($atts = array()) {
		global $sh_atts;

		$sh_atts = $atts;
		$form_url = $this->url;
		$form_url = trailingslashit($form_url);
		$rel = "";
		if(!empty($atts['ext_url'])) {
			$form_url = site_url() . "/" . $atts['ext_url'];
			$rel = " data-ext='true' ";
		}

		if(is_front_page()) {
			$front = "yes";
		} else {
			$front = "no";
		}
	?>
				<div id="store-locator-form">
					<form class="slm-map-form" data-front="<?php echo $front; ?>" data-queryvar="<?php echo $this->query_var; ?>" id='search-location-form' <?php echo $rel; ?> method="get" action="<?php echo $form_url;  ?>">
						<?php
							site_locator_map_get_template_part("location","search_form"); // Template search form
						?>
					</form>
				</div>
				
	<?php

	}
	
	function display_lists() {

			
		$args = $this->obj_query->get_result_args();
		//dumpit($args);
		$loc_query = new WP_Query($args);
		$this->loc_query = $loc_query;
		
		if($loc_query->have_posts()) { 
			echo "<div class='slm-lists'>";
			while ( $loc_query->have_posts() ) { $loc_query->the_post();
			
				site_locator_map_get_template_part("location","content");
			}
			echo "</div>";
		}

	}
	
	function result_message() {	


		echo "<span class='result-msg'><span class='slm-count'>" . $this->result_count() . "</span>.</span>";
	}
	
	function result_count() {
		$args = array_merge( array("fields" => "ids"), $this->obj_query->get_result_args()); // Only get post id

		$all = new WP_Query( $args );
		$plural = ($all->found_posts > 1 ) ? __("s", "site_locator_map") : "";
		$text = $all->found_posts . " " . __("Location", "site_locator_map") . $plural;
		return $text;
	}

	
	function pagination() {
		global $wp_query, $wp_rewrite;

		//$wpobj = $wp_query->queried_object;
		//if(!isset($wpobj)) return false;
		
		
		$loc_query = $this->loc_query;
		if(empty($loc_query)) {
			$args = $this->obj_query->get_result_args();
			$loc_query = new WP_Query($args);
		}
		
		if(is_front_page()) {
			$this->query_var = "page";
			$this->url = $this->url . "location/";
		}
		$args = array(
			'base'         => @add_query_arg($this->query_var,'%#%'),
			'format'       => '',
			'total'        => $loc_query->max_num_pages,
			'current'      => max( 1, $this->obj_query->get_paged() ),
			'prev_next'    => True,
			'prev_text'    => __('&laquo; Previous'),
			'next_text'    => __('Next &raquo;'),
		); 
 		$sar = array();
		if(isset($_GET['slm_radius'])) {
			$sar['slm_radius'] = $_GET['slm_radius'];			
		}
		
		if(isset($_GET['slm_address'])) {
			$sar['slm_address'] = urlencode($_GET['slm_address']);
		}
		
		if(isset($this->cat)) {
			$sar['slm_category'] = $this->cat;
		}
		

		
		if(!empty($sar)) {
			$args['add_args'] = $sar;
		}
		
		if ( $wp_rewrite->using_permalinks() )
			$args['base'] = trailingslashit($this->url) . $this->query_var . "/%#%/";
			
		
		echo paginate_links( $args );
	}
	
	
	// Display locations meta in json format
	function display_map_meta() {		
		
		
		$markers = $this->get_markers();
		echo "<script id='slm_meta_script' type='text/javascript'>";
			
		$slm_name = "slm_meta";
		// Convert php array to js
		$markers_display = slm_php_to_js_array($markers, $slm_name);
		echo $markers_display;
		
		// Map configuration
		$config = $this->db_config;
		
		$zoom = $config["zoom"];
		$pancontrol = ($config["pancontrol"])? "true": "false";
		$scalecontrol = ($config["scalecontrol"])? "true": "false";
		$typecontrol = ($config["typecontrol"])? "true": "false";
		$type = strtoupper($config["type"]);
		$mousewheel = ($config["mousewheel"])? "true": "false";
		
		$slm_opt= "var slm_opt = {};\n";
		$slm_opt.= "slm_opt['zoom'] = $zoom; ";
		$slm_opt.= "slm_opt['zoompancontrol'] = $pancontrol; "; // boolean
		$slm_opt.= "slm_opt['scalecontrol'] = $scalecontrol; "; // boolean
		$slm_opt.= "slm_opt['typecontrol'] = $typecontrol; "; // boolean
		$slm_opt.= "slm_opt['type'] = '$type'; "; // ROADMAP, SATELLITE, TERRAIN
		$slm_opt.= "slm_opt['mousewheel'] = $mousewheel; "; // boolean
		$slm_opt.= "slm_opt['markers'] = $slm_name; ";
		$slm_opt.= "slm_opt['map_el'] = '.slm-map-" . $this->slm_number . "'; \n";
		echo $slm_opt;
		
		echo " slm_map_data[" . $this->slm_number . "] = slm_opt;";
		echo "</script>";
		

	}
	
	function get_markers() {
		
		$args = array_merge( array("fields" => "ids"), $this->obj_query->get_result_args()); // Only get post id

		$locations = get_posts($args);
	
		$slm_meta = array();
		if(!empty($locations)) {
			$markers = array();
			foreach($locations as $location) {
				
				$post_id = $location;
				
				$slm_meta = get_post_meta($post_id, "slm_meta", true);
			
				$location_information = get_post_meta($post_id, "location_information", true);
				if(!empty($slm_meta)) {
					 global $post;
					 
					 $entry = get_posts(array("post_type" => $this->post_type,  "p" => $post_id, "post_status" => "publish"));
					 $cats = get_the_terms($post_id, "location_category");
					 if(!empty($cats)) {
						 $cats = array_values($cats);
						
						 // Get first term
						 $tid = $cats[0]->term_id;
						 $icon_obj = slm_get_map_icon_src($tid);
						 $slm_meta['map_icon'] = $icon_obj['src'];
					 } else {
						$slm_meta['map_icon'] = SLM_URL . '/images/icons/marker.png';
					 }
					 // Set location info
					 $info = "<h4 class='slm-info-title'></h4>";
					 $info .= "<h5 class='slm-info-address'>" .  $slm_meta['address'] . "</h5>";
					
					 ob_start();
					 if(!empty($entry)) {
						foreach($entry as $post) {
							setup_postdata($post);
							site_locator_map_get_template_part("location","infowindow");
						}
					 }
					 $content = ob_get_contents();
					 
					 ob_end_clean();
					 $slm_meta["info"] = $content;
					 $markers[$post_id] = $slm_meta;
				}
			}
		} else {
 			$slm_meta = array();
			$slm_meta['address'] = "World";
			$slm_meta['latitude'] = "0";
			$slm_meta['longitude'] = "0";
			$slm_meta['post_id'] = 0;
			$slm_meta["info"] = "Locations Not Found";
			$slm_meta['map_icon'] = " - ";
			$this->db_config['zoom'] = 1;
			$markers[0] = $slm_meta;
		}
		
		return $markers;
	}
	
	function the_map() {
		$this->set_map_meta(true); // Display js meta
		$width = $this->db_config['width'];
		$height = $this->db_config['height'];
		$align = "";
		if($this->db_config['align'] == "right") {
			$align = "float: right;";
		} elseif($this->db_config['align'] == "center") {
			$align = "margin: 0 auto;";
		}
		
		echo "<div class='slm-map slm-map-" . $this->slm_number . "' rel='" . $this->slm_number . "' id='slm-map' style='$align max-width: " . $width . "px; height: " . $height . "px; width: 100%;'></div><div style='clear: both'></div>";
	}
	
	function map_footer() {
	
 		
		$map_meta = $this->map_meta;
		if(!$map_meta) {
		//	return false;
		}
		
		$this->display_map_meta();
		

		wp_print_scripts("slm_script");
		
		wp_print_styles("slm_style");
		
	}
	
	function set_map_meta($bol) {
		$this->map_meta = $bol; // display js when true
	}
	
}

function slm_get_location_category() {
	global $sitelocatormaptype;
	$loc_cat = $sitelocatormaptype->get_location_category();
	return $loc_cat;
}

?>