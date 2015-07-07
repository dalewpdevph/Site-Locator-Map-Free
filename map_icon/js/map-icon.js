jQuery(document).ready(function($) {

	// Uploading files
	var file_frame;
	 
	jQuery('#slm_map_icon_add_image').live('click', function( event ){
	 
		event.preventDefault();
		 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		file_frame.open();
		return;
		}
		 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		title: jQuery( this ).data( 'uploader_title' ),
		button: {
		text: jQuery( this ).data( 'uploader_button_text' ),
		},
		multiple: false // Set to true to allow multiple files to be selected
		});
		 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			
			$('#slm_map_icon').val(attachment.url);
			
			$('#slm_map_icon_preview').attr('src', attachment.url).css('display', 'block');
			$('#slm_map_icon_add_image').css('display', 'none');
			$('#slm_map_icon_remove_image').css('display', 'inline-block');
		// Do something with attachment.id and/or attachment.url here
		});
		 
		// Finally, open the modal
		file_frame.open();
	});


    $('#slm_map_icon_remove_image').click(function() {
        $('#slm_map_icon').val('');
        $('#slm_map_icon_classes').val('');
        $('#slm_map_icon_preview').css('display', 'none');
        $('#slm_map_icon_add_image').css('display', 'inline-block');
        $('#slm_map_icon_remove_image').css('display', 'none');
    });

});