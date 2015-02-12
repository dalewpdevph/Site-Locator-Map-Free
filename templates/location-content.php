<div class="slm-entry">
	<h2><?php the_title(); ?></h2>
	<h4><?php the_slm_field("address"); ?></h4>

	<div class="slm-entry-content">
		<?php echo get_the_content(); // avoid nested slm shortcode will cause infinite loop ?>
	</div>
	<?php
		slm_display_fields();
	?>
	
	<?php
	$address = get_slm_field("address");
	if($address):
	?>
	<?php if(slm_get_distance()) { ?>
	<strong>Distance:</strong> <?php echo slm_get_distance(); ?>
	<?php } ?>
	<div class="slm-more-info">
		<a href="javascript:;" onclick="return slm_open_window(<?php echo get_the_ID(); ?>)"><?php _e("Show Marker", "site-locator-map"); ?></a> - <a href="<?php the_permalink(); ?>"><?php _e("More Info", "site-locator-map"); ?></a>
	</div>
	<?php endif; ?>
</div>
