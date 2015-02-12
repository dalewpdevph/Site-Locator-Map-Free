<?php
// Converts php meta to js array
function slm_php_to_js_array($array, $name) {
	
	$i = 0;

	$con = "var $name = new Array(); \n";
	$length = count($array);
	foreach($array as $ark => $arv) {

 		$con.= $name . "[$i] = " . json_encode($arv) . "; \n";
		
		$i++;
	}
	$con.="";
	return $con;
}



function slm_distance($lat1, $lng1, $lat2, $lng2, $miles = true) {
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;

	$r = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;
	$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;

	return ($miles ? ($km * 0.621371192) : $km);
}



function site_locator_map_get_template_part( $slug, $name = '' ) {
	
	$template = '';
	
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php" ) );

	
	if ( !$template && $name && file_exists( SLM_DIR . "/templates/{$slug}-{$name}.php" ) )
		$template = SLM_DIR . "/templates/{$slug}-{$name}.php";	
	
	if ( !$template && $name && file_exists( SLM_DIR . "/templates/{$slug}.php" ) )
		$template = SLM_DIR . "/templates/{$slug}.php";

	
	if ( !$template )
		$template = locate_template( array ( "{$slug}.php" ) );


		
	if ( $template )
		load_template( $template, false );
}

function slm_paginate() {
	global $sitelocatormaptype, $wp_query;
	// Paginate wordpress codex documentation
	
	$pt = get_post_type();
	echo "<div class='slm-pagination'>";
	if(is_post_type_archive($pt) || is_tax()  ) {
		$big = 999999999; // need an unlikely integer
		
		echo paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '/page/%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages
		) );

	} else {
		
		$sitelocatormaptype->pagination();
	}
	echo "</div>";
}

function slm_search_form($atts = array()) {
	global $sitelocatormaptype;
	$sitelocatormaptype->search_form($atts);
}

function slm_form_address() {
?>
<input type="text" id="slm_address" name="slm_address" placeholder="<?php _e("Enter Address...", "site_locator_map"); ?> value="<?php echo (!empty($_GET['slm_address']))? $_GET['slm_address'] : ""; ?>" />
<?php
}

function slm_form_radius() {
	global $sitelocatormap;
	
	?>
	<label class="slm-radius-label"><?php _e("Search Radius","site_locator_map"); ?></label>:
	<select id="slm_radius" name="slm_radius">
	 <?php
		$rad = range(25, 800, 25);
		$rad = apply_filters("slm_radius", $rad);
		$rad_s = (isset($_GET['slm_radius']))? $_GET['slm_radius']: "";
		$db_config = $sitelocatormap->get_db_config();
		$sm = $db_config['search_measurement'];
		foreach($rad as $ra) {
			$selected = ($rad_s == $ra)? "selected" : "";
			echo "<option value='$ra' $selected>" . $ra . $sm . "</option>";
		}
	 ?>
	</select>
	<?php
}

function slm_form_category() {
	global $sh_atts;
	
$loc_cat = slm_get_location_category();
if(!empty($loc_cat)) { ?>
<label class="slm-category-label"><?php _e("Category","site_locator_map"); ?></label>: <select id="slm_category" name="slm_category">
	<option value="all"><?php _e('All', "site_locator_map"); ?></option>
	<?php
	
	if(!empty($sh_atts['category'])) {
		$loc_cat_s = $sh_atts['category'];
	} elseif(isset($_GET['slm_category'])) {
		$loc_cat_s = $_GET['slm_category'];
	} else {
		$loc_cat_s = -1;
	}
	foreach($loc_cat as $cat) {
		$selected = ($loc_cat_s == $cat->term_id)? "selected" : "";
		
		echo "<option value=" . $cat->term_id . " $selected>" . $cat->name . "</option>";
	}
	?>
</select>
<?php }

}

function slm_form_submit() {
?>
	<input type="submit" name="submit_slm" value="<?php _e("Search","site_locator_map") ?>" />
<?php
}

function slm_result_message() {
	global $sitelocatormaptype;
	$sitelocatormaptype->result_message();
}

function slm_map() {
	global $sitelocatormaptype;
	$sitelocatormaptype->the_map();
}

function slm_lists() {
	global $sitelocatormaptype;
		
	$sitelocatormaptype->display_lists();
}

function slm_get_calc_info() {
	global $sitelocatormaptype;
	
	$info = $sitelocatormaptype->get_calc_info();
	return $info;
}

function slm_get_distance() {
	global $post;
	$id = $post->ID;

	$info = slm_get_calc_info();
	$info = $info[$id];
	if(!empty($info)) {
	return round($info['distance']) . " " . $info['measure'];
	} else {
		return false;
	}
}


// converts string to boolean
function slm_str_to_bool($atts) {
	
	if(!empty($atts)) {
		foreach($atts as $attk => $att) {
			if($att == 'true') {
				$atts[$attk] = (bool) 1;
			}
			if($att == 'false') {
				$atts[$attk] = (bool) 0;
			}
		}
		return $atts;
	}
	
}

// Debugging
if(!function_exists("dumpit")) {
	function dumpit($arr, $return = false) {
		$out = "<pre>" . print_r($arr, true) . "</pre>";
		if(!$return) {
			echo $out;
		} else {
			return $out;
		}
	}
}
?>