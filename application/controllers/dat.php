<?php
class Dat extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('file');
		$this->load->library('mricf');
		$this->load->model("Generic_model",'',TRUE);
		
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Data Interface";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

		$this->move_to_opts = array(
			'0' => "Select a table",
			'ichange_waybill' => "Waybills / Purchase Orders", 
			'ichange_trains' =>  "Trains",
			'ichange_cars' => "Cars"
		);
		$this->add_opts = array(
			'0' => "Add to Existing Records, do not delete existing records", 
			'1' =>  "Replace existing records in table with contents from file",
			'2' => "Delete all existing records and add contents from file"
		);

	}

	public function index(){
		$this->csv_file();
	}
	
	public function csv_file(){
		$this->arr['pgTitle'] .= " - File Upload / Import";
		
		$this->set_form_flds();
		$this->display_views();
	}

	function do_upload(){
		$config['upload_path'] = './uploaded_files/';
		$config['allowed_types'] = 'csv|txt';
		$config['overwrite'] = TRUE;
		$config['file_name'] = "file-".$this->arr['rr_sess'].".csv";

		$this->load->library('upload', $config);
		
		if(!$this->upload->do_upload())	{
			$this->set_form_flds();
			$this->flddat['error'] = $this->upload->display_errors();
			$this->display_views();
			/*
			//$this->load->view('upload_form', $error);
			echo "<pre>"; print_r($error); echo "</pre>";
			echo "<pre>"; print_r($this->upload); echo "</pre>";
			$this->load->view();
			*/
		}else	{
			$d = $_POST;
			$dte = date('YmdHis');
			$data = array('upload_data' => $this->upload->data());
			$f_str = read_file($data['upload_data']['full_path']);
			$f_dat = explode(chr(10),$f_str); // chr(10) is line feed, new line!
			//echo "<pre>"; print_r($data); echo "</pre>";
			//echo "<pre>"; print_r($f_dat); echo "</pre>";
			//echo "<pre>"; print_r($d); echo "</pre>";
			$dsql = "";
			if($d['repl'] == 2){
				if($d['tbl'] == "ichange_cars"){$dsql = "DELETE FROM `".$d['tbl']."` WHERE `rr` = '".$d['rr_id']."'";}
				if($d['tbl'] == "ichange_trains"){$dsql = "DELETE FROM `".$d['tbl']."` WHERE `railroad_id` = '".$d['rr_id']."'";}
				//if($d['tbl'] == "ichange_waybill"){$dsql = "DELETE FROM `".$d['tbl']."` WHERE `rr` = '".$d['rr_id']."'";}
			}
			if(strlen($dsql) > 0){$this->Generic_model->change($dsql);}
			$fld_names = explode("|",$f_dat[0]);
			for($i=1;$i<count($f_dat);$i++){
				if(strlen($f_dat[$i]) > 0){
					$f_dat2 = explode("|",$f_dat[$i]);
					$sql = "INSERT INTO `".$d['tbl']."` SET ";
					$wot=0;
					if($d['repl'] == 1 && $d['tbl'] != "ichange_waybill"){
						$wot=1;
						$sql = "UPDATE `".$d['tbl']."` SET ";
					}
					$cntr=0;
					for($f=0;$f<count($f_dat2);$f++){
						if(strlen($f_dat2[$f]) > 0){
							if($cntr > 0){$sql .= ", ";}
							$f_dat_form = strtoupper($f_dat2[$f]);
							$f_dat_form = str_replace("\"\"","\"",$f_dat_form);
							$f_dat_form = str_replace("\"[","[",$f_dat_form);
							$f_dat_form = str_replace("]\"","]",$f_dat_form);
							$sql .= "`".$fld_names[$f]."` = '".$f_dat_form."'";
							$cntr++;
						}
					}
					if($d['tbl'] == "ichange_waybill"){$sql .= ", `waybill_num` = '".$dte."R".$this->arr['rr_sess']."U".$i."'";}
					if($wot == 1){
						$sql .= " WHERE `".$fld_names[0]."` = '".$f_dat2[0]."'";
						if($d['tbl'] == "ichange_cars"){$sql .= " AND `rr` = '".$d['rr_id']."'";}
						if($d['tbl'] == "ichange_trains"){$sql .= " AND `railroad_id` = '".$d['rr_id']."'";}
					}
					//echo $sql."<br />";
					$this->Generic_model->change($sql);
					header("Location:../home");
				}
				
			}
			//$this->load->view('upload_success', $data);
		}
	}
	
	function set_form_flds(){
		if($this->arr['rr_sess'] > 0){
			$this->flddat = array('fields' => array());
			// Selector to move car to wherever.
			$this->flddat['fields'][] = "<div style=\"background-color: moccasin; padding: 4px; border: 1px solid red; font-size: 10pt;\">
				The Form below will upload and either insert or update the records for the Table selected with the data in the file selected. 
				The CSV file needs to be pipe (|) delimited, and have the column names in the first row of the file. 
				The application uses the column names to work out what piece of data goes in what field in the data table. 
				</div><br />";
			$this->flddat['fields'][] = "<div id=\"wb_info\" style=\"display:none; background-color: yellow; padding: 6px; border: 1px solid red; font-size: 10pt; width: 50%; margin-left: 25%; text-align: left;\">
				<strong>Waybills / Purchase Orders table fields:</strong><br />
				<ul>
				<li>date - Date of waybill / purchase order,</li>
				<li>rr_id_from - ID of railroad the shipment originates on,</li>
				<li>rr_id_to - ID of railroad shipment is destined for,</li>
				<li>rr_id_handling - ID of railroad the shipment is currently on,</li>
				<li>lading - The commodity being shipped,</li>
				<li>indust_origin_name - Originating industry,</li>
				<li>indust_dest_name - Destination industry,</li>
				<li>return_to - Where to return the car to once unloaded,</li>
				<li>routing - Route the shipment will take,</li>
				<li>status - Current Status (normally P_ORDER or WAYBILL),</li>
				<li>avail_due_date - Date the shipment becomes available at the originating industry or when the shipment needs to be at nearest RR interchange point to destination industry,</li>
				<li>po - Purchase Order number (if applicable).</li>
				<li>cars - Cars to attach to waybill, in JSON format (Syntax: [{\"AAR_REQD\":\"<u>AAR Code</u>\",\"NUM\":\"<u>Car report mark & number</u>\",\"AAR\":\"<u>AAR Code</u>\",\"RR\":\"<u>Your RR ID</u>\"},{<u>extra cars with same JSON elements (AAR_REQD, NUM, AAR, RR)</u>}] . eg, <em>[{\"AAR_REQD\":\"LO\", \"NUM\":\"ABC123\", \"AAR\":\"LO\", \"RR\":\"1\"}]</em>). If you are not sure how to format the JSON array it is best to not include this column in the file.</li>
				<li>notes - Notes to include on the waybill that are not part of other fields.</li>
				</ul>
				</div>";
			$this->flddat['fields'][] = "<div id=\"tr_info\" style=\"display:none; background-color: yellow; padding: 6px; border: 1px solid red; font-size: 10pt; width: 50%; margin-left: 25%; text-align: left;\">
				<strong>Trains table fields:</strong><br />
				<ul>
				<li>train_id - Date of waybill / purchase order (must be the first column in the file!),</li>
				<li>train_desc - ID of railroad the shipment originates on,</li>
				<li>no_cars - ID of railroad shipment is destined for,</li>
				<li>sun - ID of railroad the shipment is currently on,</li>
				<li>mon - The commodity being shipped,</li>
				<li>tues - Originating industry,</li>
				<li>web - Destination industry,</li>
				<li>thu - Where to return the car to once unloaded,</li>
				<li>fri - Route the shipment will take,</li>
				<li>sat - Current Status (normally P_ORDER or WAYBILL),</li>
				<li>op_notes - Date the shipment becomes available at the originating industry or when the shipment needs to be at nearest RR interchange point to destination industry,</li>
				<li>direction - Purchase Order number (if applicable),</li>
				<li>origin - Origin of train,</li>
				<li>destination - Destination of train,</li>
				<li>tr_sheet_ord - Train sheet order / train departure time.</li>
				</ul>
				</div>";
			$this->flddat['fields'][] = "<div id=\"ca_info\" style=\"display:none; background-color: yellow; padding: 6px; border: 1px solid red; font-size: 10pt; width: 50%; margin-left: 25%; text-align: left;\">
				<strong>Cars table fields:</strong><br />
				<ul>
				<li>car_num - Car numb incl. reporting mark (must be the first column in the file!),</li>
				<li>aar_type - ID of railroad the shipment originates on,</li>
				<li>desc - ID of railroad shipment is destined for,</li>
				<li>special_instruct - ID of railroad the shipment is currently on,</li>
				</ul>
				</div>";
			$this->flddat['fields'][] = form_open_multipart('../dat/do_upload');
			$this->flddat['fields'][] = form_hidden('rr_id',$this->arr['rr_sess']);
			$this->flddat['fields'][] = "<br /><span style=\"font-size: 10pt;\">".form_label('Table to Upload To', 'tbl')."&nbsp;</span>".form_dropdown('tbl',$this->move_to_opts,'0','id="tbl" onchange="disp_info(); showEle(\'dao\'); if(this.value == \'ichange_waybill\'){hideEle(\'dao\');}"')."<br />";
			$this->flddat['fields'][] = "<div id=\"dao\"><span style=\"font-size: 10pt;\">".form_label('Data Add Options', 'repl')."&nbsp;</span>".form_dropdown('repl',$this->add_opts,'0')."</div>";
			$this->flddat['fields'][] = "<span style=\"font-size: 10pt;\">".form_label('Filename', 'userfile')."&nbsp;</span>".form_upload('userfile')."<br />";
			$this->flddat['fields'][] = form_submit('submit', 'Upload');
			$this->flddat['fields'][] = form_close();
		}
	}
	
	
	function display_views(){
		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('fields', @$this->flddat);}
		else{	$this->load->view('not_allowed');}
		$this->load->view('footer');
	}

}
?>