<?php
class CWP_Jobs_Feed {
	
	protected $atts;
	
	protected $feed_all_url = 'https://www.wsujobs.com/all_jobs.atom';
	
	public function __construct( $atts ){
		
		$this->atts = $atts;
		
	} // end __construct
	
	public function get_feed_all_url(){ return $this->feed_all_url; }
	
	
	public function get_feed(){
		
		$feed = array();
		
		$temp_feed = fetch_feed( $this->get_feed_all_url() );
		
		if ( ! is_wp_error( $temp_feed ) ){
			
			$feed = $this->parse_feed( $temp_feed );
			
		} else {
			
			$feed = array();
			
		} // end if
		
		return $feed;
		
	} // end get_feed
	
	protected function parse_feed( $temp_feed ){
		
		$feed = array();
		
		$feed_data = $temp_feed->data['child']['http://www.w3.org/2005/Atom']['feed'][0]['child']['http://www.w3.org/2005/Atom']['entry'];
		
		foreach( $feed_data as $index => $feed_item ){
				
			$feed_item_data = $this->parse_feed_item( $feed_item );
				
			$feed[ $feed_item_data[0] ] = $feed_item_data[1]; 
			
		} // end foreach
		
		return $feed;
		
	} // end parse_feed
	
	protected function parse_feed_item( $feed_item ){
		
		$feed_item = $feed_item['child']['http://www.w3.org/2005/Atom'];
		
		$data = array();
		
		$id = $feed_item['id'][0]['data'];
		
		$data['published'] = $feed_item['published'][0]['data'];
		
		$data['title'] = $feed_item['title'][0]['data']; 
		
		//var_dump( $feed_item['link'][0]['attribs']['']['href'] );
		//$data['link'] = $feed_item['link'][0]['attribs'][0]['href'];
		
		$data['link'] = 'http://www.wsujobs.com/postings/' . $id;
		
		$data['content'] = $feed_item['content'][0]['data'];
		
		return array( $id , $data );
		
	} // end parse_feed_item
	
}