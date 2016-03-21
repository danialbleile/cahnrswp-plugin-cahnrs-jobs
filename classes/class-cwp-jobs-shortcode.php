<?php
class CWP_Jobs_Shortcode {
	
	public function add_shortcode(){
		
		// Register shortcode
		add_shortcode( 'cahnrsjobs', array( $this , 'do_shortcode' ) );
		
	} // end add_shortcode
	
	public function do_shortcode( $atts , $content ){
	} // end do_shortcode
	
}