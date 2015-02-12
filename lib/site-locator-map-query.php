<?php
	
class SiteLocatorMapQuery {
	
	protected $initial_args;
	protected $add_args;
	private $search_url;
	private $db_config;
	private $query_var;
	private $paginate;
	private $post_type;
	private $paged;
	private $addr_info;
	
	function __construct($sitelocatormaptype) {


		$this->post_type =  $sitelocatormaptype->get_post_type();
		$this->search_url =  $sitelocatormaptype->get_url();
		$this->db_config =  $sitelocatormaptype->get_db_config();
		$this->query_var = $sitelocatormaptype->get_query_var();
		$this->paginate = $sitelocatormaptype->get_paginate();
		
		
		$this->initial_args =  array(
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'posts_per_page' => $this->db_config['perpage']
		);
		
		$paged = get_query_var($this->query_var);
		if(empty($paged)) {
			$paged = 1;
		}
		$this->paged = $paged;
		$this->add_args['paged'] = $this->paged;
			
	}
	
	function get_paged() {
		return $this->paged;
	}
	
	function get_location_category() {
		$loc_cat = get_terms( $this->post_type . "_category");
		if(!empty($loc_cat)) {
			return $loc_cat;
		} else {
			return false;
		}
	}
	
	function set_single($pid) {
		$this->initial_args['posts_per_page'] = 1;
		$this->initial_args['p'] = $pid;
	}
	
	
	
	function set_category($cat) {
		
		if(!empty($cat) && is_numeric($cat)) {
			
			$cat_array = array('posts_per_page'=>-1,'tax_query' => array( array('taxonomy' => $this->post_type . '_category', 'field' => 'id', 'terms' => $cat)));
			$args = $this->initial_args;			
			$args['fields'] = "ids";
			
			$cat_args = array_merge($args, $cat_array);
			
			$cat_ids = get_posts($cat_args);			
			$add_args = $this->add_args;
			if(!empty($cat_ids)) {
				if(isset($add_args['post__in'])) {
					$merge_ids = array_intersect($add_args['post__in'], $cat_ids);					
					$add_args['post__in'] = $merge_ids;
					
					if(empty($merge_ids)) {
						$add_args['post__in'] = array(0);
					}
					
				} else {
				
					$add_args['post__in'] = $cat_ids;
				}
				
				$this->add_args = $add_args;			
			}
		
		}
	}
	
	function get_calc_info() {
		$addr_info = $this->addr_info;

		$info = $this->locate_stores($addr_info['lat'], $addr_info['lng'], $addr_info['radius']);
		
		if(!empty($info)) {
			return $info;
		}
	}
	
	function set_search($radius, $address) {
		
		if(!empty($address)) {
			$address = urlencode($address);
			$api = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false");
			$pt_json = json_decode($api);
			$lat = $pt_json->results[0]->geometry->location->lat;
			$lng = $pt_json->results[0]->geometry->location->lng;
			$this->addr_info['lat'] = $lat;
			$this->addr_info['lng'] = $lng;
			$this->addr_info['radius'] = $radius;			
			
			$this->locate_stores($lat, $lng, $radius);
			$locations = $this->location_info;




			uasort($locations, "slm_cmp");
			
			
			if(!empty($locations)) {
				$loc_keys = array_keys($locations);
				$this->add_args['post__in'] = $loc_keys;		
				$this->add_args['orderby'] = 'post__in';		
			} else {
				$this->add_args['post__in'] = array(0);
			}
			
		}	
	}
	
	function locate_stores($center_lat, $center_lng, $radius) {
	
		$args = $this->initial_args;
		$args['fields'] = "ids";
		$args['posts_per_page'] = -1;
		$locations = get_posts($args);
		
		$stores = array();
		
		if(!empty($locations)) {	

			foreach ($locations as $loc) {
				$pid = $loc;
				$stores[$pid]['post_id'] = $pid;
				
				$slm_meta = get_post_meta($pid, "slm_meta", true);
	
				
				$stores[$pid]['latitude'] = $slm_meta['latitude'];
				$stores[$pid]['longitude'] = $slm_meta['longitude'];
				
				$stores[$pid]['radius'] = $radius;
				
				$sm = $this->db_config['search_measurement'];
				$miles = true;
				$stores[$pid]['measure'] = "miles";
				if($sm == "km") {
					$miles = false;
					$stores[$pid]['measure'] = "km";
				}
				
				$stores[$pid]['distance'] = slm_distance($slm_meta['latitude'], $slm_meta['longitude'], $center_lat,$center_lng, $miles);
				
				
			}
			
			if(!empty($stores)) {
				foreach ($stores as $key => $store) {
					
					$dst = $store['distance'];
					if ($dst > $radius) {
						unset($stores[$key]);						
					} else { 
						$stores[$key]['distance'] = $dst; 
					}	
				}
				
				$this->location_info = $stores;
				return $stores;
			}
		}
	
	
	}
	
	function set_add_args($args) {
		$this->add_args = $args;
	}
	
	function get_result_args() {
		if(isset($this->add_args)) {
			$result = array_merge($this->initial_args, $this->add_args);
		} else {
			$result = $this->initial_args;
		}
		
		return $result;
	}
	
}

function slm_cmp($a, $b)
{
	if ($a['distance'] == $b['distance']) {
		return 0;
	}
	return ($a['distance'] < $b['distance']) ? -1 : 1;
}
	
?>