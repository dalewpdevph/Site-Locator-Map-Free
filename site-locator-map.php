<?php
/*
	Plugin Name: Site locator Map Free
	Description: A location management system that allows your users to locate places, stores, spots and other areas through search filters. Please checkout the Site Locator Map Paid Version for more features in <a href='http://codecanyon.net/item/site-locator-map/7354406'>Codecanyon</a>
	Version: 1.2.5
	Author: Precious Dale Ramirez
	Author URI: http://codecanyon.net/item/site-locator-map/7354406
	Copyright 2013-2014  Precious Dale Ramirez, Demo http://codecanyon.net/item/site-locator-map/7354406
*/

define('SLM_DIR', plugin_dir_path(__FILE__));
define('SLM_URL', plugin_dir_url(__FILE__));

// Admin codes
require("admin/site-locator-map-options.php");
require("admin/site-locator-map-admin.php");

class SiteLocatorMap {
	private $db_config;
	protected $post_type;
	private $atts_config;
	private $obj_type;
	function __construct() {
		
		$post_type = "location";
		$this->post_type = $post_type;
		add_action("init", array($this, "slm_init"));  // Set SLM map class global
		add_action("wp", array($this, "wp"));  // Set SLM objects
		add_action("pre_get_posts", array($this, "pre_get_posts"));  // Set result args for default post type templates
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); // Include admin map js script
		add_action('wp_head', array($this, 'wp_head')); // Include admin map js script

		
		add_action("template_include", array($this, "template_loader")); // Template file default or fallbacks
		$this->load_db_config();
	}

	function SiteLocatorMap() {
		$this->__construct();
	}
	
	function get_post_type() {
		return $this->post_type;
	}
	
	
	function wp() {
		global $slm_number;
		// For single location
		if(is_singular($this->post_type)) {
			global $sitelocatormaptype;
			$post_id = get_the_ID();
			$sitelocatormaptype = new SiteLocatorMapType($this->post_type, "paged", "page", get_permalink(), $this->db_config);
			$sitelocatormaptype->set_single($post_id);
			$slm_number++;
		}
		


	
	}
	
	function pre_get_posts($query) {
		global $sitelocatormaptype, $wp_query, $slm_number;
		// Initialize slm obj for location defualt template
		if($query->is_main_query() && $query->is_post_type_archive($this->post_type) && !is_admin()) {
			$sitelocatormaptype = new SiteLocatorMapType($this->post_type, "paged", "page", get_post_type_archive_link( $this->post_type ), $this->db_config, $slm_number);
			if(isset($_GET)) {
				$sitelocatormaptype->set_requests_search($_GET);
			}
			$res = $sitelocatormaptype->get_result_args();
			$query->query_vars = $res;
			$slm_number++;

		}
		
		if($query->is_main_query() && $query->is_tax($this->post_type . "_category") && !is_admin()) {
		
			$term_id = $wp_query->queried_object->term_id;
			$sitelocatormaptype = new SiteLocatorMapType($this->post_type, "paged", "page", get_term_link( $term_id ), $this->db_config, $slm_number);
			$sitelocatormaptype->set_category($term_id);
			$res = $sitelocatormaptype->get_result_args();
			$query->query_vars = $res;
			$slm_number++;
		}

	}
	
		// Set map default configuration
	function default_configuration() {
		return array(
					'zoom' => '10',
					'width' => '750',
					'height' => '550',
					'align' => 'left',									
					'language' => 'en',
					'icons' => array(),
					'default_icon' => SLM_URL . '/images/icons/marker.png',
					'type' => 'ROADMAP',
					'mousewheel' => true,
					'pancontrol' => true,
					'scalecontrol' => true,
					'typecontrol' => true,
					'highlight'	=> true,
					'list' => true,
					'search' => true,
					'map' => true,
					'field_label' => true,
					'perpage' => 25, // Set locations per page
				
				);
	}
	
	function set_configuration($args = array()) {
		$defaults = $this->default_configuration();
		$config = wp_parse_args($args, $defaults);
		$this->db_config = $config;
		$this->atts_config = $config;
	}
	
	function set_db_config($args) {
		$cur_db = $this->db_config;	
		
		$config = wp_parse_args($args, $cur_db);
		$this->db_config = $config;	
	}	
	
	function set_atts_config($args) {
		$cur_db = $this->db_config;	
		
		$config = wp_parse_args($args, $cur_db);
		$this->atts_config = $config;	
	}
	
	function load_db_config() {
		$zoom = slm_of_get_option("map_zoom", 10);
		$width = slm_of_get_option("map_width", 1000);
		$height = slm_of_get_option("map_height", 600);
		$align = slm_of_get_option("map_align", "left");
		$type = slm_of_get_option("map_type", "ROADMAP");
		$search_measurement = slm_of_get_option("search_measurement", "mi");
		
		$mousewheel = true;
		if(slm_of_get_option("mousewheel", "on") == "off") {
			$mousewheel = false;
		}
		
		$pancontrol = true;
		if(slm_of_get_option("pancontrol", "on") == "off") {
			$pancontrol = false;
		}		
		
		$scalecontrol = true;
		if(slm_of_get_option("scalecontrol", "on") == "off") {
			$scalecontrol = false;
		}		
		
		$typecontrol = true;
		if(slm_of_get_option("typecontrol", "on") == "off") {
			$typecontrol = false;
		}		
		
		$map_list = true;
		if(slm_of_get_option("map_list", "on") == "off") {
			$map_list = false;
		}
		
		$showmap = true;
		if(slm_of_get_option("showmap", "on") == "off") {
			$showmap = false;
		}		
		
		$showsearch = true;
		if(slm_of_get_option("showsearch", "on") == "off") {
			$showsearch = false;
		}		
		
		$field_label = true;
		if(slm_of_get_option("field_label", "on") == "off") {
			$field_label = false;
		}
		
		$per_page = slm_of_get_option("per_page", 25);
		

		
		$db_config = array(
				'zoom' => $zoom,
				'width' => $width,
				'height' => $height,
				'align' => $align,									
				'type' => $type,
				'mousewheel' => $mousewheel,
				'pancontrol' => $pancontrol,
				'scalecontrol' => $scalecontrol,
				'typecontrol' => $typecontrol,
				'list'	=> $map_list,
				'search'	=> $showsearch,
				'map'	=> $showmap,
				'field_label'	=> $field_label,
				'perpage' => $per_page, // Set posts per page

				'search_measurement' => $search_measurement // Set search measurement relations
		);

		$this->set_configuration($db_config);
	}
	
	function get_db_config() {
		return $this->db_config;
	}
	
	function get_atts_config() {
		return $this->atts_config;
	}

	function slm_init() {
		global $slm_number;
		$slm_number = 1;
		$this->tax_type_init();
		$db_config = $this->db_config;
		
		// Add query var and rewrite rule for page, post and location post type only.
		add_rewrite_rule('(.?.+?)/slmp/?([0-9]{1,})/?$','index.php?pagename=$matches[1]&slmp=$matches[2]','top');	
		add_rewrite_rule('(.?.+?)/slmpd/?([0-9]{1,})/?$','index.php?name=$matches[1]&slmpd=$matches[2]','top');
		
		add_rewrite_tag('%slmp%','([^&]+)');
		add_rewrite_tag('%slmpd%','([^&]+)');
		
	}

	function tax_type_init() {
	
		$cur_post_type = $this->post_type;
		$post_type = apply_filters("slm_post_type", $cur_post_type);
		$this->post_type = $post_type;
		
		if ( !post_type_exists( $post_type ) ) {
			$labels = array(
			'name' => __('Location', 'site_locator_map'),
			'singular_name' => __('Location', 'site_locator_map'),
			'add_new' => __('Add New Location', 'site_locator_map'),
			'add_new_item' => __('Add New Location', 'site_locator_map'),
			'edit_item' => __('Edit Location', 'site_locator_map'),
			'new_item' => __('New Location', 'site_locator_map'),
			'all_items' => __('All Locations', 'site_locator_map'),
			'view_item' => __('View Location', 'site_locator_map'),
			'search_items' => __('Search locations', 'site_locator_map'),
			'not_found' =>  __('No Location found', 'site_locator_map'),
			'not_found_in_trash' => __('No Location found in Trash', 'site_locator_map'), 
			'parent_item_colon' => '',
			'menu_name' => 'Site Locator Map Free'
			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array('slug' => apply_filters("slm_slug", $this->post_type, $this->post_type) ), // Change locaiton slug through filter
			'capability_type' => 'post',
			'has_archive' => true, 
			"taxonomies" => array($this->post_type . "_category"),
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon' => SLM_URL . "/images/icons/slm-icon.png",
			'supports' => array( 'title', 'thumbnail', 'editor', 'comments' )
			); 
			register_post_type($post_type,$args);

			register_taxonomy(
			$this->post_type . '_category',
			$this->post_type,
			array(
				'label' => __('Location Categories', 'site_locator_map'),
				'rewrite' => array('slug' => $this->post_type . '_category'),
				'capabilities' => 
					array(
						'assign_terms',
						'edit_terms',
						'manage_terms',
						'delete_terms',
					),
					'hierarchical' => true,
					'show_ui'                 => true,
					'show_admin_column'       => true,
					'query_var'               => true,
				)
			);
		}
		
		// Include front end map js script
		wp_register_style(
			'slm_style',
			SLM_URL.'css/slm-styles.css'
		);
				
		
		wp_register_script(
			'slm_blockui',
			SLM_URL.'js/jquery.blockUI.js'
			,
			array("jquery"),
			true
		);		
		
		wp_register_script(
			'slm_script',
			SLM_URL.'js/slm.js'
			,
			array("jquery", "slm_blockui"),
			true
		);	
	
		wp_localize_script( 'slm_script', 'slm_url', SLM_URL );
		wp_localize_script( 'slm_script', 'slm_ajax_url', admin_url("admin-ajax.php") );
		$cts = slm_of_get_option("slm_country_restrict", "ALL");
		$ajax = slm_of_get_option("slm_ajax_search", false);
		

		wp_localize_script( 'slm_script', 'slm_ct_restrict', $cts);
		wp_localize_script( 'slm_script', 'slm_ajax_search', $ajax);
		
		
	}
	
	function admin_enqueue_scripts() {
		wp_enqueue_script("jquery");
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script("slm-options", SLM_URL . "js/slm-admin-options.js");
		wp_enqueue_style(
			'sortable-ui', SLM_URL . 'css/jquery-ui.css'
		);	

		if(get_post_type() != $this->post_type) return false;
		
		wp_enqueue_style(
			'admin_slm_style',
			SLM_URL.'css/slm-admin-styles.css'
		);		

		
		wp_enqueue_script(
			'admin_slm_script',
			SLM_URL.'js/slm-admin.js',
			array('jquery'),
			"1"
		);	
		

		
		wp_enqueue_script("jquery-ui-autocomplete");
	}
	
	function wp_head() {
		echo "<script type='text/javascript'>";
		echo "var slm_map_data = {};";
		echo "</script>";
	}
	
	function template_loader($template) {
		
		
		$find = array( 'site-locator-map.php' );
		$file = '';

		if ( is_single() && get_post_type() ==  $this->post_type ) {

			$file 	= 'single-' . $this->post_type . '.php';			
			
			$find[] = $file;			

		} elseif ( is_post_type_archive( $this->post_type ) ) {
		
			$file 	= 'archive-' . $this->post_type . '.php';			
			
			$find[] = $file;		
			
		}elseif(is_tax($this->post_type . "_category")) {
		
			$file 	= 'taxonomy-' . $this->post_type . '_category.php';			
			$find[] = $file;
		}
	

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) $template = SLM_DIR . 'templates/' . $file;
		}
		

		return $template;
	}

	
}


$GLOBALS['sitelocatormap'] = new SiteLocatorMap();

require("map_icon/map-icon.php");
require("site-locator-functions.php");
require("lib/site-locator-map-type.php");


require("lib/site-locator-map-fields.php");
require("lib/site-locator-map-location.php");

function slm_flush_rules() {
	global $sitelocatormap;
	$sitelocatormap->slm_init();
	flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, "slm_flush_rules");

require("lib/site-locator-map-query.php");
require("lib/site-locator-map-form-widget.php");

// Shortcode to display site locations
add_shortcode("site_locator_map_form", "site_locator_map_form");
function site_locator_map_form($atts) {
	$content = "";
	$atts['showmap'] = false;
	$atts['showlist'] = false;
	$content = get_site_locator_map($atts);
	return $content;
	
}

add_shortcode("site_locator_map", "site_locator_map_shortcode");
function site_locator_map_shortcode($atts) {
	$content = get_site_locator_map($atts);
	return $content;
}

function get_site_locator_map($atts = array()) {
	global $sitelocatormaptype, $sitelocatormap, $slm_number;
	
	if(!empty($atts)) {
		$atts = slm_str_to_bool($atts);
		
		$sitelocatormap->set_atts_config($atts);
	}
	if(is_single()) {
		$url = get_permalink();
		$query_var = "slmpd";
		$paginate = "slmpd";
	} else {
		$url = get_permalink();
		$query_var = "slmp";
		$paginate = "slmp";
	}
	
	$content = "";
	$db = $sitelocatormap->get_atts_config();

	$sitelocatormaptype = new SiteLocatorMapType($sitelocatormap->get_post_type(), $query_var, $paginate, $url, $db, $slm_number);	
	$slm_number++;
	if(isset($_GET)) {

		$sitelocatormaptype->set_requests_search($_GET);
	}
	

	
	if(!empty($atts)) {
		if(!empty($atts['category'])) {
			$cat = $atts['category'];
			$sitelocatormaptype->set_category($cat);
		}
	
	}

	if(!empty($atts['location_id'])) {
		$sitelocatormaptype->set_single($atts['location_id']);
	}
	
	$view_sort = slm_of_get_option("sortable-map", array("map", "search", "lists"));
	
 	$showmap = (slm_of_get_option("showmap", "on") == "off")? false : true;
	if(isset($atts['showmap'])) {
		if($atts['showmap'] == false ) {
			$showmap = false;
		}
		if($atts['showmap'] == true ) {
			$showmap = true;
		}
	} 	
	
	$showsearch = (slm_of_get_option("showsearch", "on") == "off")? false : true;
	if(isset($atts['showsearch'])) {
		if($atts['showsearch'] == false ) {
			$showsearch = false;
		}
		if($atts['showsearch'] == true ) {
			$showsearch = true;
		}
	}	
	
	$showlist = (slm_of_get_option("showlist", "on") == "off")? false : true;
	if(isset($atts['showlist'])) {
		if($atts['showlist'] == false ) {
			$showlist = false;
		}
		if($atts['showlist'] == true ) {
			$showlist = true;
		}
	}


	ob_start();
	echo "<div class='site-locator-map-wrap'>";
	if(!isset($atts['message']) || (isset($atts['message']) && $atts['message'] == true))
		slm_result_message();
	// Sort order option
	if(!empty($view_sort)) {		
		foreach($view_sort as $view) {
			
			switch($view) {
				case "map":
					if($showmap) {
						slm_map();
					}
				break;
				
				case "search":
					if($showsearch) {
						if(empty($atts['location_id'])) {
							slm_search_form($atts);
						}
					}
				break;
				
				case "lists":
					if($showlist) {
						slm_lists();
					}
				break;
			}
		}
	}
	if($showlist == true || $showmap == true) {
		slm_paginate();	
	}
	echo "</div>";
	$content.= ob_get_contents();
	ob_end_clean();
	
	return $content;
}

function site_locator_map() {
	echo get_site_locator_map();
}


?>