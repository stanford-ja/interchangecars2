<?php
class Example_rssfeed extends CI_Controller {  
	function __construct(){
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!! 
		$this->load->helper('xml');
		$this->load->helper('text');
		$this->load->model('example_rss_model', '', TRUE);
		$this->load->library('xml_writer');
		$this->load->library('rss_writer');
	}

	function index(){ 
		echo "You got to the RSS feeder, but have not specified a method! Add a {method name} to the end of the URL.";
	}
	
	function waybills(){
	
		$this->rss_writer->specification='1.0';
		$this->rss_writer->about='http://localhost/Applications/interchangecars/rss_xml.php'; //'http://www.stanfordhosting.net/xml.xml';
		$this->rss_writer->stylesheet='css/rss1html.xsl';
		$this->rss_writer->rssnamespaces['dc']='http://purl.org/dc/elements/1.1/';
	
		//Define the properties of the channel.
		$properties=array();
		$properties['description']='Example RSS Feed';
		$properties['link']='http://localhost/zip-tar-files/CodeIgniter/index.php';
		$properties['title']='MRICF - Current Waybills';
		//$properties['dc:date']='2002-06-30T00:00:00Z';
		$properties['dc:date']=date('Y-m-d')."T".date('H:i:s')."Z"; //'2002-06-30T00:00:00Z';
		$this->rss_writer->addchannel($properties);
	
		// Add items
		$posts = $this->example_rss_model->getWaybills(10);
		foreach($posts->result() as $post):
		
			$cars = @json_decode($post->cars,TRUE);
			$car_lst = "";
			for($i=0;$i<count($cars);$i++){
				if(strlen($cars[$i]['NUM']) > 0){$car_lst .= $cars[$i]['NUM']."(".$cars[$i]['AAR'].") ";}
			}
			$properties=array();
			$properties['description'] = "NO CARS SET.";
			if(strlen($car_lst) > 0){$properties['description'] = 'Cars: '.$car_lst;}
			$properties['link']='http://localhost/Applications/interchangecars/edit.php?type=WAYBILL&action=EDIT&id='.$post->waybill_num;
			$properties['title']=$post->status.' - '.$post->waybill_num.' to '.$post->indust_dest_name.' - via: '.$post->routing;
			$properties['dc:date']=$post->date."T00:00:00"; //date('Y-m-d').'T'.date('H:i:s').'Z';
			$this->rss_writer->additem($properties);

		endforeach;
		$this->load->view('example_rss'); // Render the RSS feed in a view rather than in the controller!
	}
}
?>