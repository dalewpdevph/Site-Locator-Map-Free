<div class="slm-infowindow">
<span><?php the_title(); ?></span><br />
<span><?php the_slm_field("address"); ?></span>
<?php
	$label = ''; // set false to hide label
	$infowindow = true; // to allow use of radio hide of the options backend
	slm_display_fields($label, $infowindow);
?>
<br />

<a target="_blank" href="http://maps.google.com/?q=<?php $address = get_slm_field("address"); echo urlencode($address); ?>"><?php _e("View on Google maps","site-locator-map"); ?></a>
</div>