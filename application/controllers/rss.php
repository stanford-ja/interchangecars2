<?php
class Rss extends CI_Controller {  
	function __construct(){
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!! 
		$this->load->helper('xml');
		//$this->load->helper('text');
		$this->load->model('Generic_model', '', TRUE);
		//$this->load->library('xml_writer');
		$this->load->library('rss_writer');
	}

	function index(){ 
		echo "You got to the RSS feeder, but have not specified a method! Add a {method name} to the end of the URL.";
	}
	
	function waybills(){
		$sql = "SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND `status` != 'P_ORDER' ORDER BY `date` DESC";
		$this->set_params("Waybills","waybills");
		$tmp = (array)$this->Generic_model->qry($sql);
		$this->data['posts'] = array();
		$cntr = 0;
		$oldest_ts = date('U') - (86400 * 10); // n days ago
		$oldest_date = date('Y-m-d',$oldest_ts)."T00:00:00";
		for($t=0;$t<count($tmp);$t++){
			$cars = @json_decode($tmp[$t]->cars,TRUE);
			$car_lst = "";
			$dt = $tmp[$t]->date."T00:00:00"; //Z"; Z on the end make it 'zulu' (GMT+0) time
			for($i=0;$i<count($cars);$i++){
				if(strlen($cars[$i]['NUM']) > 0 && $cars[$i]['NUM'] != "UNDEFINED"){$car_lst .= $cars[$i]['NUM']."(".$cars[$i]['AAR'].")<br />";}
			}

			$prog = @json_decode($tmp[$t]->progress,TRUE);
			$prog_lst = "";
			//for($p=count($prog)-1;$p>=0;$p=$p-1){
				//$prog_lst .= $prog[$p]['date']." ".$prog[$p]['time']." - ".$prog[$p]['text']."<br />";
			//}
			if(count($prog) > 0){
				$prog_lst .= $prog[count($prog)-1]['date']." ".$prog[count($prog)-1]['time']." - ".$prog[count($prog)-1]['text']."<br />";
				$dt = $prog[count($prog)-1]['date']."T".$prog[count($prog)-1]['time'].":00";
			}
			//echo $dt." - ".$oldest_date."<br />";
			if($dt > $oldest_date){
				$this->data['posts']["wb".$cntr]['description'] = $car_lst.$prog_lst;
				$this->data['posts']["wb".$cntr]['link'] = WEB_ROOT."/waybill/edit/".$tmp[$t]->id;
				$this->data['posts']["wb".$cntr]['title'] = $tmp[$t]->status.' - '.$tmp[$t]->waybill_num.' to '.$tmp[$t]->indust_dest_name.' - via: '.$tmp[$t]->routing;
				$this->data['posts']["wb".$cntr]['dc:date'] = $dt; //$tmp[$t]->date."T00:00:00Z";
				$cntr++;
			}
		}
		$this->create_channel();
		$this->add_items_2_rss();
		$this->output_rss();

		exit();

	}

	function porders(){
		$sql = "SELECT * FROM `ichange_waybill` WHERE `status` = 'P_ORDER' ORDER BY `date` DESC";
		$this->set_params("Purchase Orders","porders");
		$tmp = (array)$this->Generic_model->qry($sql);
		$this->data['posts'] = array();
		$cntr = 0;
		for($t=0;$t<count($tmp);$t++){
			$cars = @json_decode($tmp[$t]->cars,TRUE);
			$car_lst = "";
			for($i=0;$i<count($cars);$i++){
				if(strlen($cars[$i]['NUM']) > 0){$car_lst .= $cars[$i]['NUM']."(".$cars[$i]['AAR'].") ";}
			}

			$prog = @json_decode($tmp[$t]->progress,TRUE);
			$prog_lst = "";
			//for($p=count($prog)-1;$p>=0;$p=$p-1){
				//$prog_lst .= $prog[$p]['date']." ".$prog[$p]['time']." - ".$prog[$p]['text']."<br />";
			//}
			if(count($prog) > 0){
				$prog_lst .= $prog[count($prog)-1]['date']." ".$prog[count($prog)-1]['time']." - ".$prog[count($prog)-1]['text']."<br />";
			}
			$this->data['posts']["wb".$cntr]['description'] = "<strong>".$tmp[$t]->indust_origin_name.'</strong> to <strong>'.$tmp[$t]->indust_dest_name.'</strong> - via: '.$tmp[$t]->routing; //'Cars: '.$car_lst."<br />".$prog_lst;
			$this->data['posts']["wb".$cntr]['link'] = WEB_ROOT."/waybill/edit/".$tmp[$t]->id;
			$this->data['posts']["wb".$cntr]['title'] = $tmp[$t]->lading.' - '.$tmp[$t]->waybill_num;
			$this->data['posts']["wb".$cntr]['dc:date'] = $tmp[$t]->date."T00:00:00Z";
			$cntr++;
		}
		$this->create_channel();
		$this->add_items_2_rss();
		$this->output_rss();

		exit();

	}

	// Supporting methods
	function set_params($fn,$url){
		$this->chann = array();
		$this->chann['title'] = $fn;
		//$this->chann['outputencoding'] = 'utf-8';
		$this->chann['link'] = WEB_ROOT."rss/".$url;
		$this->chann['description'] = $fn;
		$this->chann['dc:date']=date('Y-m-d')."T".date('H:i:s')."Z"; //'2002-06-30T00:00:00Z';

	}

	function create_channel(){
		$this->rss_writer->specification='1.0';
//		$this->rss_writer->stylesheet='css/rss1html.xsl'; // Optional
		$this->rss_writer->rssnamespaces['dc'] = 'http://purl.org/dc/elements/1.1/';
		$this->rss_writer->about = $this->chann['link'];
		$this->rss_writer->addchannel($this->chann);
	}

	function add_items_2_rss(){
		if(count($this->data['posts']) < 1){
			$this->data['posts'][0]['description'] = "No feed items found";
			$this->data['posts'][0]['link'] = "";
			$this->data['posts'][0]['title'] = "No feed items found";
			$this->data['posts'][0]['dc:date'] = date('Y-m-d')."T00:00:00Z";			
		}
		asort($this->data['posts']);
		$rss_kys = array_keys($this->data['posts']);
		for($t=0;$t<count($rss_kys);$t++){
			$this->rss_writer->additem($this->data['posts'][$rss_kys[$t]]);
		}
	}

	function output_rss(){
		if($this->rss_writer->writerss($output)){
			/*
			 *  If the document was generated successfully, you may now output it.
			 */
			header('Content-Type: text/xml; charset="'.$this->rss_writer->outputencoding.'"');
			//header('Content-Type: text/xml; charset="'.$this->data['encoding'].'"');
			header('Content-Length: '.strval(strlen($output)));
			echo $output;
		}else{
			/*
			 *  If there was an error, output it as well.
			 */
			header('Content-Type: text/plain');
			echo ('Error: '.$this->rss_writer->error);
		}
	}


}
?>
