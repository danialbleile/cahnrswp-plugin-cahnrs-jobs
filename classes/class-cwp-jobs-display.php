<?php
class CWP_Jobs_Display {
	
	protected $atts;
	
	public function __construct( $atts = array() ){
	} // end __construct
	
	public function the_jobs( $feed ){
		
		$html = '<div class="cahnrs-job-list">';
		
			$html .= $this->the_nav( $feed );
			
			$html .= $this->the_list( $feed , array() , 'cahnrs-faculty-jobs active' );
			
			$html .= $this->the_list( $feed , array() , 'cahnrs-staff-jobs' );
			
			$html .= $this->the_list( $feed , array() , 'cahnrs-student-jobs' );
			
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
	
	protected function the_list( $feed , $filters = array() , $class = '' ){
		
		$feed = $this->apply_filters( $feed , $filters );
		
		$html = '<ul class="cahnrs-jobs-feed ' . $class . '">';
		
			foreach( $feed as $id => $feed_item  ){
				
				//var_dump( $feed_item );
				
				$link = '<a href="' . $feed_item['link'] . '" target="_blank" >';
		
				$html .= '<li class="cahnrs-job">';
				
					$html .= '<h3>' . $link . $feed_item['title'] . '</a></h3>';
					
					$html .= '<h4>Department Here, Location Here</h4>';
					
					$html .= wp_trim_words( $feed_item['content'], $num_words = 45, '...' );
					
					$html .= '<a class="cahnrs-job-details" href="' . $feed_item['link'] . '" target="_blank" >' . 'View Details</a>';
				
				$html .= '</li>';
			
			} // end foreach
		
		$html .= '</ul>';
		
		return $html;
		
	} // end list
	
	protected function apply_filters( $feed , $filters ){
		
		shuffle( $feed );
		
		return array_slice( $feed , 0 , 10 );
		
	} // end apply filters
	
	
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