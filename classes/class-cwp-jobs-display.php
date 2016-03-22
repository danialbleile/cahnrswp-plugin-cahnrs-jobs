<?php
class CWP_Jobs_Display {
	
	protected $atts;
	
	public function __construct( $atts = array() ){
	} // end __construct
	
	public function the_jobs( $feed ){
		
		$html = '<div class="cahnrs-job-list">';
		
			$html .= $this->the_nav( $feed );
			
			$faculty_jobs = $this->get_cahnrs_jobs();
			
			$staff_jobs = $this->get_cahnrs_jobs( 'NOT IN' );
			
			$html .= $this->the_list( $faculty_jobs, 'cahnrs-faculty-jobs active' );
			
			$html .= $this->the_list( $staff_jobs , 'cahnrs-staff-jobs' );
			
			$html .= $this->get_students_section();
			
			$html .= $this->get_script();
		
		$html .= '</div>';
		
		return $html;
		
	} // end the_jobs
	
	protected function the_nav( $feed ){
		
		$html = '<ul class="cahnrs-jobs-nav">';
		
			$html .= '<li class="active">';
		
				$html .= '<a href="#" class="cahnrs-faculty-jobs"><img src="' . plugins_url( 'images/faculty-icon.jpg', dirname(__FILE__) ) . '" /><span>Faculty Positions</span></a>';
			
			$html .= '</li><li>';
			
				$html .= '<a href="#" class="cahnrs-staff-jobs"><img src="' . plugins_url( 'images/staff-icon.jpg', dirname(__FILE__) ) . '" /><span>Staff Positions</span></a>';
			
			$html .= '</li><li>';
			
				$html .= '<a href="#" class="cahnrs-student-jobs"><img src="' . plugins_url( 'images/student-icon.jpg', dirname(__FILE__) ) . '" /><span>Student Positions</span></a>';
				
			$html .= '</li>';
		
		return $html . '</ul>';
		
	} // end the_nav
	
	protected function get_cahnrs_jobs( $operator = 'IN'){
		
		$args = array(
			'post_type' => 'job',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'jobtype',
					'field'    => 'slug',
					'terms'    => 'faculty',
					'operator' => $operator,
				),
			),
		);
		
		$query = new WP_Query( $args );
		
		$jobs = array();
		
		if ( $query->have_posts() ) {
		
			while ( $query->have_posts() ) {
				
				$query->the_post();
				
				$jobs[ $query->post->post_name ]['title'] =  get_the_title();
				
				$jobs[ $query->post->post_name ]['link'] =  'http://www.wsujobs.com/postings/' . $query->post->post_name;
				
				$jobs[ $query->post->post_name ]['content'] = get_the_content();
				
				$jobs[ $query->post->post_name ]['excerpt'] =  get_the_excerpt();
				
				$jobs[ $query->post->post_name ]['dept'] = get_post_meta( $query->post->ID , '_dept' , true );
				
				$jobs[ $query->post->post_name ]['location'] = get_post_meta( $query->post->ID , '_location' , true );
				
			}
			
		} 
		/* Restore original Post Data */
		wp_reset_postdata();
		
		return $jobs;
		
	}
	
	protected function get_students_section(){
		
		$html = '<div class="cahnrs-jobs-feed cahnrs-student-jobs">';
		
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => 'student-jobs',
				),
			),
		);
		
		$query = new WP_Query( $args );
		
		$jobs = array();
		
		if ( $query->have_posts() ) {
		
			while ( $query->have_posts() ) {
				
				$query->the_post();
				
				$html .= '<h3>' . get_the_title() . '</h3>';
				
				$html .= do_shortcode( get_the_content() );
				
			} // end while
			
		} // end if
		
		/* Restore original Post Data */
		wp_reset_postdata();
		
		$html .= '</div>';
		
		return $html;
		
	} // end 
	
	protected function the_list( $feed , $class = '' ){
		
		$html = '<ul class="cahnrs-jobs-feed ' . $class . '">';
		
			foreach( $feed as $id => $feed_item  ){
				
				//var_dump( $feed_item );
				
				$link = '<a href="' . $feed_item['link'] . '" target="_blank" >';
		
				$html .= '<li class="cahnrs-job">';
				
					$html .= '<h3>' . $link . $feed_item['title'] . '</a></h3>';
					
					$html .= '<h4>'. $feed_item['dept'] . ', '. $feed_item['location'] . '</h4>';
					
					$html .= wp_trim_words( $feed_item['excerpt'], $num_words = 45, '...' );
					
					$html .= '<a class="cahnrs-job-details" href="' . $feed_item['link'] . '" target="_blank" >' . 'View Details</a>';
				
				$html .= '</li>';
			
			} // end foreach
		
		$html .= '</ul>';
		
		return $html;
		
	} // end list
	
	
	protected function get_script(){
		
		/*
		CWPJobs = {
	
			wrap: jQuery('.cahnrs-job-list'),
			
			init: function(){
				
				CWPJobs.wrap.on('click' , '.cahnrs-job-list > .cahnrs-jobs-nav li' , function( event ){
					
					event.preventDefault();
					
					CWPJobs.change_section( jQuery( this ) );
					
					})
				
			},
			
			change_section: function( ic ){
				
				var sec = jQuery('.cahnrs-jobs-feed').eq( ic.index() ).show().siblings().hide();
				
			}
			
		}
		CWPJobs.init();
		*/
		$script = '<script>CWPJobs={wrap:jQuery(".cahnrs-job-list"),init:function(){CWPJobs.wrap.on("click",".cahnrs-jobs-nav li",function(s){s.preventDefault(),CWPJobs.change_section(jQuery(this))})},change_section:function(s){s.addClass("active").siblings().removeClass("active"),jQuery(".cahnrs-jobs-feed").eq(s.index()).addClass("active").siblings(".cahnrs-jobs-feed").removeClass("active")}},CWPJobs.init();</script>';
		
		return $script;
		
	}
	
	
}