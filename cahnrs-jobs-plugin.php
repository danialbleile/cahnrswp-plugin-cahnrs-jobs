<?php
/*
Plugin Name: CAHNRS Jobs
Plugin URI: http://cahnrs.wsu.edu/communications
Description: Pull CAHNRS Jobs from WSU Jobs
Author: cahnrscommunications, Danial Bleile
Author URI: http://cahnrs.wsu.edu/communications
Version: 0.0.1
*/

class CAHNRS_Jobs {
	
	private static $instance;
	
	public $shortcode;
	
	public $jobs;
	
	public function __construct(){
		
	} // end __construct
	
	/**
	 * Singleton Pattern - only one instance of 
	 * class exists
	**/ 
	public static function get_instance(){
		
		if ( null == self::$instance ) {
            self::$instance = new self;
			self::$instance->init_plugin();
        } // end if
 
        return self::$instance;
		
	} // end get_instance
	
	/**
	 * Set up action and filter hooks for
	 * the plugin
	**/
	public function init_plugin(){
		
		require_once 'classes/class-cwp-jobs.php';
		
		$this->jobs = new CWP_Jobs();
		
		$this->jobs->init();
		
		// Register shortcode
		add_shortcode( 'cahnrsjobs', array( $this , 'do_jobs_shortcode' ) );
		
		// Add admin scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		
		if ( ! empty( $_GET['update-jobs-cache'] ) ){
			
			add_filter( 'template_include', array( $this , 'clear_cache' ) , 99 );
			
		} // end if
		
		add_action( 'init', array( $this , 'add_taxonomy' ), 20 );
		
	} // end set_actions
	
	
	public function do_jobs_shortcode( $atts ){
		
		
		
		/*require_once 'classes/class-cwp-jobs-feed.php';
		$cwp_feed = new CWP_Jobs_Feed( $atts );
		
		$feed = $cwp_feed->get_feed();*/
		
		require_once 'classes/class-cwp-jobs-display.php';
		$display = new CWP_Jobs_Display( $atts );
		
		$html = $display->the_jobs( $feed );
		
		return $html;
		
	} // end do_jobs_shortcode
	
	
	public function add_scripts(){
		
		wp_enqueue_style( 'cahnrs_jobs_css', plugin_dir_url( __FILE__ ) . 'cahnrs-jobs-css.css' , false , '0.0.1' );
		
	} // end add_scripts
	
	public function clear_cache( $template ){
		
		return plugin_dir_path(__FILE__) . 'update-jobs.php';
		
	} 
	
	
	//public function add_public_scripts(){
	//}
	
	
	/*public function do_jobs_shortcode( $atts ){
		
		$feed = fetch_feed( 'https://www.wsujobs.com/all_jobs.atom' );
		
		if ( ! is_wp_error( $feed ) ){
			
			$jobs = $this->parse_jobs( $feed );
			
		} else {
		}// end if
		
	}

	
	private function parse_jobs( $feed ){
		
		$jobs = array( 
			'faculty'   => array(), 
			'staff'     => array(), 
			'temporary' => array() 
			); 
		
		$items = $this->get_job_items( $feed );
		
		foreach( $items as $job ){
			
			var_dump( $job['child']['http://www.w3.org/2005/Atom']['title'][0]['data'] );
			
		} // end foreach
		
		return $jobs;
		
	}
	
	private function get_job_items( $feed ){
		
		
		if ( isset( $feed->data['child']['http://www.w3.org/2005/Atom']['feed'][0]['child']['http://www.w3.org/2005/Atom']['entry'] ) ){
		
			$items = $feed->data['child']['http://www.w3.org/2005/Atom']['feed'][0]['child']['http://www.w3.org/2005/Atom']['entry'];
		
			return $items;
		
		} else {
			
			return array();
			
		}// end if
		
	} // end get_jobs*/
	
	public function add_taxonomy(){
		
		register_taxonomy(
        'jobarea',
        'job',
			array(
				'label' => __( 'CAHNRS Areas' ),
				'public' => true,
				'show_ui' => true,
				'hierarchical' => true,
			)
		);
		
		register_taxonomy(
        'jobdept',
        'job',
			array(
				'label' => __( 'Job Depts' ),
				'public' => true,
				'show_ui' => true,
				'hierarchical' => true,
			)
		);
		
		register_taxonomy(  
        'jobtype',
        'job',
			array(
				'label' => __( 'Job Type' ),
				'public' => true,
				'show_ui' => true,
				'hierarchical' => true,
			)
		);  
		
	}
	
	
} // end class

CAHNRS_Jobs::get_instance();
