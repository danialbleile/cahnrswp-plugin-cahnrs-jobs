<?php
/**
 * CAHNRS Job object
 * @author Danial Bleile
 * @version 0.0.1
 */
 
require_once 'class-cwp-post-type.php';
 
class CWP_Jobs extends CWP_Post_Type {
	
	// @var string $slug Post type slug
	protected $slug = 'job';
	
	// @var string|array $label String for single label or array for all labels
	protected $label = 'Jobs';
	
	// @var array $meta_fields Meta data for the post type
	protected $fields = array(
		'_redirect' => array('text',''),
		'_dept' => array('text',''),
	);
	
	// @var bool $do_save Add save action
	protected $do_save = true;
	
	// @var bool | string $admin_enqueue 
	protected $admin_enqueue = array(
		
		array(
			'type'      => 'script',
			'handle'    => 'cwp-jobs-admin-js',
			'url'       => 'js/admin-script.js',
			'deps'      => array(),
			'ver'       => '0.0.1',
			'in_footer' => true,
		),
		
	);
	
	// @var array $jobs 
	protected $jobs;
	
	
	/**
	 * Get method for property
	 * @return array - CAHNRS Jobs
	 */
	public function get_jobs(){ return $this->jobs; } 
	
	/**
	 * Add edit form after title
	 * @param object $post WP Post object
	 * @param array $settings Key => values for defined fields
	 * @return html for the edit page
	 */
	protected function edit_form( $post , $settings ){
		
		$html = '<input type="text" name="_redirect" value="' . $settings['_redirect'] . '" placeholder="URL: http://example.com" />';
		
		$html .= '<input type="text" name="_dept" value="' . $settings['_dept'] . '" placeholder="Department" />';
		
		$html .= '<p><a href="http://devsite.wpdev.cahnrs.wsu.edu/jobs/?update-jobs-cache=1">Update Jobs Cache</a></p>';
		
		return $html;
		
	} // end edit_form
	
	
	public function do_jobs_request(){
		
		
		$jobs_posts = array();
		
		$the_query = new WP_Query( array( 'post_type' => 'job' , 'posts_per_page' => '-1' , 'post_status' => 'any' ) );
		
		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				
				$the_query->the_post();
				
				if ( ! empty( $_GET['clear'] ) ){
					
					wp_delete_post( $the_query->post->ID, true );
					
				} else {
					
					$id = explode( '-' , $the_query->post->post_name );
					
					$jobs_posts[ $id[0] ] = $the_query->post;
					
				}
				
			} // end while

		} // end if
		
		if ( empty( $_GET['clear'] ) ){
		
			$html = $this->get_html();
			
			$listings_html = $this->get_listings_html_array( $html );
			
			$jobs = $this->get_jobs_array( $listings_html[0] );
			
			wp_reset_postdata();
			
			$i = 0;
			
			foreach( $jobs as $job_id => $job ){
				
				if ( $i  > 10 ) continue;
				
				$i++;
				
				if ( array_key_exists( $job_id , $jobs_posts ) ){
					
					unset( $jobs_posts[ $job_id ] );
					
				} else {
					
					$post = array(
						'post_name'     => $job_id,
						'post_title'    => $job['title'],
						'post_excerpt'  => $job['desc'],
						'post_type'     => 'job',
					);
					
					if( get_term_by( 'name' , trim( $job['dept'] ) , 'category' ) ){
						
						$post['post_status'] = 'publish';
						
					} // end if
					
					$post_id = wp_insert_post ( $post );
					
					update_post_meta( $post_id , '_redirect' , 'https://www.wsujobs.com/postings/' . $job_id );
					
					update_post_meta( $post_id , '_dept' , trim( $job['dept'] ) );
					
					echo '<li>Added: ' . $job['title'] . '</li>';
					
				}// end if
				
			} // end foreach
			
			echo '<hr />';
			
			foreach( $jobs_posts as $j_post ){
				
				wp_delete_post( $j_post->ID, true );
				
				echo '<li>Removed: ' . $j_post->post_title . '</li>';
				
			} // end foreach
		
		} // end if
		
		//var_dump( $jobs );
		
	} // end do_jobs_request
	
	/**
	 * Get HTML from www.wsujobs.com
	 * @return HTML
	 */
	private function get_html(){
		
		$html = '';
		
		for ( $i = 1; $i < 8; $i++ ){
			
			$html .= file_get_contents( 'https://www.wsujobs.com/postings/search?page=' . $i );
			
		}
		
		//$html .= file_get_contents( plugins_url( 'temp.html' , dirname(__FILE__) ) );
		
		return $html;
		
	} // end get_html
	
	/**
	 * Split individual listings from html
	 * @param string $html from wsujobs
	 * @return array Individaul job html
	 */ 
	public function get_listings_html_array( $html ){
		
		$listing = array();
		
		//$pattern = '/<div.*class="job-item.*>[\S\s]<table>([\S\s]+?)<\/table>/';
		
		$pattern = '/<div.*?job-item.*?>[\S\s]+?<table>([\S\s]+?)<\/table>[\S\s]+?<\/div>/';
		
		preg_match_all( $pattern, $html, $listing );
		
		return $listing;
		
	} // get_listings_html_array
	
	/**
	 * Get jobs from html
	 * @param array $list HTML for jobs
	 * @return array
	 */
	private function get_jobs_array( $list ){
		
		$jobs = array();
		
		foreach( $list as $job ){
			
			$id = $this->get_job_id( $job );
			
			$title = 
			
			$jobs[ $id ] = array( 
				'title' => $this->get_job_title( $job ),
				'dept' => $this->get_job_dept( $job ),
				'desc' => $this->get_job_desc( $job ),
				);
			
		} // end foreach
		
		return $jobs;	
		
	} // end get_jobs
	
	/**
	 * Get job id from html
	 * @param string $job HTML for job
	 * @return string 
	 */
	private function get_job_id( $job ){
		
		$match = array();
		
		$pattern = '/\/postings\/(.*)"/';
		
		preg_match( $pattern , $job , $match );
		
		return $match[1];
		
	} // end get_job_id
	
	/**
	 * Get job title from html
	 * @param string $job HTML for job
	 * @return string 
	 */
	private function get_job_title( $job ){
		
		$match = array();
		
		$pattern = '/job-title[\S\s]+?<a[\S\s]+?>(.*?)<\/a>/';
		
		preg_match( $pattern , $job , $match );
		
		return $match[1];
		
	} // end get_job_title
	
	/**
	 * Get job dept from html
	 * @param string $job HTML for job
	 * @return string 
	 */
	private function get_job_dept( $job ){
		
		$match = array();
		
		$pattern = '/<tr.*?>[\S\s]+?<td.*?job-title[\S\s]+?<td>[\S\s]+?<td>[\S\s]+?<td>([\S\s]+?)<\/td>/';
		
		preg_match( $pattern , $job , $match );
		
		return $match[1];
		
	} // end get_job_title
	
	/**
	 * Get job description from html
	 * @param string $job HTML for job
	 * @return string 
	 */
	private function get_job_desc( $job ){
		
		$match = array();
		
		$pattern = '/<tr.*?>[\S\s]+?<td.*?job-title[\S\s]+?<td>[\S\s]+?<td>[\S\s]+?<td>([\S\s]+?)<\/td>/';
		
		$pattern = '/job-description.*?>([\S\s]+?)<\/span>/';
		
		preg_match( $pattern , $job , $match );
		
		return $match[1];
		
	} // end get_job_title
	
	
	
} // end CWP_Jobs