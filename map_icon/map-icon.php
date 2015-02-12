<?php

 
 
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'map-icon', 40, 40, true ); //(cropped)
}



class slm_map_icon {
	
	private $post_type;
	private $admin_url;
	
    function __construct() {
		global $sitelocatormap;
        // Add base actions
        add_action('admin_head', array($this, 'init'));
        add_action('edit_term', array($this, 'save_fields'), 10, 3);
        add_action('create_term', array($this, 'save_fields'), 10, 3);
		$this->post_type = $sitelocatormap->get_post_type();

    }

    /**
     * Adds our form fields to the WP add/edit term forms
     * @since 0.8.0
     */
    function init() {       
		global $sitelocatormap;
		$this->post_type = $sitelocatormap->get_post_type();
            $tax = $this->post_type . "_category";
			add_action($tax.'_add_form_fields', array($this, 'add_fields'));
			add_action($tax.'_edit_form_fields', array($this, 'edit_fields'));

			add_filter("manage_edit-{$tax}_columns", array($this, 'add_taxonomy_column'));
			add_filter("manage_{$tax}_custom_column", array($this, 'edit_taxonomy_columns'), 10, 3);
            
        
    }

    /**
     * Adds our custom fields to the WP add term form
     * @since 0.8.0
     */
    function add_fields() {
        $this->setup_field_scripts();
        global $wp_version;
        // Make it look better if it is 3.5 or later
        $before_3_5 = true;
        if(preg_match('/^[3-9]\.[5-9]/', $wp_version)) $before_3_5 = false;
		?>
    <div class="form-field" style="overflow: hidden;">
        <label>Image</label>
        <input type="hidden" name="slm_map_icon" id="slm_map_icon" value="" />
        <input type="hidden" name="slm_map_icon_classes" id="slm_map_icon_classes" value="" />
        <br/>
        <img src="" id="slm_map_icon_preview" style="max-width:300px;max-height:300px;float:left;display:none;padding:0 5px 5px 0;" />
        <a href="#" class="<?php echo($before_3_5)?'':'button'; ?>" id="slm_map_icon_add_image">Add Image</a>
        <a href="#" class="<?php echo($before_3_5)?'':'button'; ?>" id="slm_map_icon_remove_image" style="display: none;">Remove Image</a>
    </div>
    <?php
    }

    /**
     * Adds our custom fields to the WP edit term form
     * @param $taxonomy Object A WP Taxonomy term object
     * @since 0.8.0
     */
    function edit_fields($taxonomy) {
        $this->setup_field_scripts();
		$tid = $taxonomy->term_id;
 ?>
    <tr class="form-field">
        <th><label for="slm_map_icon">Image</label></th>
        <td>
            <?php $image = slm_get_map_icon_src($tid); ?>
            <input type="hidden" name="slm_map_icon" id="slm_map_icon" value="<?php echo ($image)?$image['src']:''; ?>" />
            <input type="hidden" name="slm_map_icon_classes" id="slm_map_icon_classes" value="" />
            <?php $image = slm_get_map_icon_src($tid);  ?>
            <img src="<?php echo ($image)?$image['src']:''; ?>" id="slm_map_icon_preview" style="max-width: 300px;max-height: 300px;float:left;display: <?php echo($image['src'])?'block':'none'; ?>;padding: 0 5px 5px 0;" />
            <a href="#" class="button" id="slm_map_icon_add_image" style="display: <?php echo($image['src'])?'none':'inline-block'; ?>;">Add Image</a>
            <a href="#" class="button" id="slm_map_icon_remove_image" style="display: <?php echo($image['src'])?'inline-block':'none'; ?>;">Remove Image</a>
        </td>
    </tr>
    <?php
    }

    function setup_field_scripts() {

		wp_enqueue_media();
		wp_enqueue_script('slm-map-icon', plugin_dir_url(__FILE__) . "js/map-icon.js", array('jquery', 'thickbox'));
    }

    /**
     * Saves the data from our custom fields on the WP add/edit term form
     * @param $term_id
     * @param null $tt_id
     * @param null $taxonomy
     * @since 0.8.0
     */
    function save_fields($term_id, $tt_id = null, $taxonomy = null) {

        // Save our info
        $option = "slm_map_icon_{$taxonomy}_{$term_id}";
        if(isset($_POST['slm_map_icon']) && ($src = $_POST['slm_map_icon'])) {
            if($src != '') {
                if(isset($_POST['slm_map_icon_classes']) && preg_match('/wp-image-([0-9]{1,99})/', $_POST['slm_map_icon_classes'], $matches)) {
                    // We have the ID from the class, use it.
                    update_option($option, $matches[1]);
                }
                else {
                    global $wpdb;
                    $prefix = $wpdb->prefix;
                    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $src));
                    // See if we found the attachment ID, otherwise save URL instead.
                    if(is_numeric($attachment[0]))
                        update_option($option, $attachment[0]);
                    else
                        update_option($option, $src);
                }
				
            }
            else {
                $test = get_option($option);
                if($test)
                    delete_option($option);
            }
        }
        else {
            $test = get_option($option);
            if($test)
                delete_option($option);
        }

    }

    /**
     * Adds the new column to all taxonomy management screens
     * @param $columns
     * @return mixed
     * @since 0.8.3
     */
    function add_taxonomy_column($columns) {
        $columns['slm_map_icon_thumb'] = 'Map Icon';
        return $columns;
    }

    /**
     * Adds the thumbnail to all terms in the taxonomy management screens (if they have a thumbnail we can get).
     * @param $out
     * @param $column_name
     * @param $term_id
     * @return bool|String
     * @since 0.8.3
     */
    function edit_taxonomy_columns($out, $column_name, $term_id) {
        if($column_name != 'slm_map_icon_thumb') return $out;
        $term = get_term($term_id, $_GET['taxonomy']);
		
        $image = slm_get_map_icon($term_id);
        if($image)
            $out = $image;
        return $out;
    }
	
	function get_post_type() {
		return $this->post_type;
	}
	
}
$slm_map_icon = new slm_map_icon;


function slm_get_map_icon_src($tid) {
	global $slm_map_icon;
	 $src = get_option('slm_map_icon_' .  $slm_map_icon->get_post_type() . '_category_'.$tid);
	
 	$tmp = false;
    if(is_numeric($src)) {
        $tmp = wp_get_attachment_image_src($src, "map-icon");
        if($tmp && !is_wp_error($tmp) && is_array($tmp) && count($tmp) >= 3)
            $tmp = array('ID' => $src, 'src' => $tmp[0], 'width' => $tmp[1], 'height' => $tmp[2]);
        else return false;
    }
    elseif(!empty($src))
        $tmp = array('src' => $src);
    if($tmp && !is_wp_error($tmp) && is_array($tmp) && isset($tmp['src']))
        return $tmp;
    else return false;
}

function slm_get_map_icon($tid) {
	global $slm_map_icon;
	$tax_term = get_term($tid, $slm_map_icon->get_post_type() . "_category");

    $image = slm_get_map_icon_src($tid, "map-icon");

    if(!$image) return false;
    return '<img src="'.$image['src'].'" alt="'.$tax_term->name.'" class="taxonomy-term-image" width="'.(($image['width'])?$image['width']:'').'" height="'.(($image['height'])?$image['height']:'').'" />';
}

function slm_map_icon($tid) {
    echo slm_get_map_icon($tid);
}

function slm_options_setup() {
	global $pagenow;

	if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
		// Now we'll replace the 'Insert into Post Button' inside Thickbox
		add_filter( 'gettext', 'slm_replace_thickbox_text'  , 1, 3 );
	}
}
add_action( 'admin_init', 'slm_options_setup' );

function slm_replace_thickbox_text($translated_text, $text, $domain) {
	if ('Insert into Post' == $text) {
		$referer = strpos( wp_get_referer(), 'slm-map-icon' );
		if ( $referer != '' ) {
			return __('Use this as icon', 'slm' );
		}
	}
	return $translated_text;
}