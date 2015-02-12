<?php
/**
 * Contains public functions that are callable in themes.
 */

/**
 * Get the source URL of the requested taxonomy term image
 * @param Object $tax_term A WP taxonomy term object
 * @param string $size (OPTIONAL) A string specifying the image size to get (e.g. thumbnail, medium, full, or a custom size that has been created by you or a plugin). Does NOT work on images that weren't uploaded through the WP media uploader.
 * @return bool|Array Returns false on failure, array containing the src and attachment ID (if any).
 * @since 0.8.0
 */
 
function slm_get_map_icon_src($tid) {
	 $src = get_option('slm_map_icon_location_category_'.$tid);
	 
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
	$tax_term = get_term($tid, "location_category");

    $image = slm_get_map_icon_src($tid, "map-icon");

    if(!$image) return false;
    return '<img src="'.$image['src'].'" alt="'.$tax_term->name.'" class="taxonomy-term-image" width="'.(($image['width'])?$image['width']:'').'" height="'.(($image['height'])?$image['height']:'').'" />';
}

function slm_map_icon($tid) {
    echo slm_get_map_icon($tid);
}
 
function s8_get_taxonomy_image_src($tax_term, $size = 'thumbnail') {
    if(!is_object($tax_term)) return false;
    $src = get_option('s8_tax_image_'.$tax_term->taxonomy.'_'.$tax_term->term_id);
    $tmp = false;
    if(is_numeric($src)) {
        $tmp = wp_get_attachment_image_src($src, $size);
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

/**
 * Get the html needed to display the taxonomy term image
 * @param Object $tax_term A WP taxonomy term object
 * @param string $size (OPTIONAL) A string specifying the image size to get (e.g. thumbnail, medium, full, or a custom size that has been created by you or a plugin). Does NOT work on images that weren't uploaded through the WP media uploader.
 * @return bool|String returns false on failure, html img string on success
 * @since 0.8.0
 */
function s8_get_taxonomy_image($tax_term, $size = 'thumbnail') {
    $image = s8_get_taxonomy_image_src($tax_term, $size);
    if(!$image) return false;
    return '<img src="'.$image['src'].'" alt="'.$tax_term->name.'" class="taxonomy-term-image" width="'.(($image['width'])?$image['width']:'').'" height="'.(($image['height'])?$image['height']:'').'" />';
}

/**
 * Echo out the html needed to display the taxonomy term image
 * @param $tax_term Object A WP taxonomy term object
 * @param string $size (OPTIONAL) A string specifying the image size to get (e.g. thumbnail, medium, full, or a custom size that has been created by you or a plugin). Does NOT work on images that weren't uploaded through the WP media uploader.
 * @since 0.8.0
 */
function s8_taxonomy_image($tax_term, $size = 'thumbnail') {
    echo s8_get_taxonomy_image($tax_term, $size);
}

function wptuts_options_setup() {
	global $pagenow;

	if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
		// Now we'll replace the 'Insert into Post Button' inside Thickbox
		add_filter( 'gettext', 'replace_thickbox_text'  , 1, 3 );
	}
}
add_action( 'admin_init', 'wptuts_options_setup' );

function replace_thickbox_text($translated_text, $text, $domain) {
	if ('Insert into Post' == $text) {
		$referer = strpos( wp_get_referer(), 'slm-map-icon' );
		if ( $referer != '' ) {
			return __('Use this as icon', 'slm' );
		}
	}
	return $translated_text;
}