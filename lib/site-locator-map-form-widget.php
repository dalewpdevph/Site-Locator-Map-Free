<?php

class SlmWidgetForm extends WP_Widget {


	function __construct() {
		parent::__construct(
			'slm_widget_form', // Base ID
			__('Site Locator Map Form', 'site_locator_map'), // Name
			array( 'description' => __( 'Displays Site Locator Map Form', 'site_locator_map' ), ) // Args
		);
	}


	public function widget( $args, $instance ) {
		global $sitelocatormaptype;
     	echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		$content = "";
		$atts['showmap'] = false;
		$atts['showlist'] = false;
		$ext_url = $instance['slm_ext_url'];
		$ext_url = trim($ext_url);
		if(!empty($ext_url)) {
			$atts['ext_url'] = $ext_url;
		}
			
		if(is_object($sitelocatormaptype)) {
			
			$content = slm_search_form($atts);
		} else {
			$content = "Form will not show if no site locator map being displayed.";
		}
		echo  $content;
		
		echo $args['after_widget'];
	}


	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Search', 'site_locator_map' );
		}		
		
		if ( isset( $instance[ 'slm_ext_url' ] ) ) {
			$slm_ext_url = $instance[ 'slm_ext_url' ];
		}
		else {
			$slm_ext_url ="";
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>		
		
		<p>
		<label for="<?php echo $this->get_field_id( 'slm_ext_url' ); ?>"><?php _e( 'Redirect URL:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'slm_ext_url' ); ?>" name="<?php echo $this->get_field_name( 'slm_ext_url' ); ?>" type="text" value="<?php echo esc_attr( $slm_ext_url ); ?>"><br />
		<span>Empty box if you want to use ajax.</span>
		</p>
		<?php 
	}


	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['slm_ext_url'] = ( ! empty( $new_instance['slm_ext_url'] ) ) ? strip_tags( $new_instance['slm_ext_url'] ) : '';

		return $instance;
	}
}

add_action('widgets_init',
     create_function('', 'return register_widget("SlmWidgetForm");')
);
?>