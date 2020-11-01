<?php
class Search extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!
		/* REQUIRES SOMETHING LIKE THIS IN VIEW'S CONTROLLER METHOD TO SET searchtable1 FOR DISPLAY FOR EACH LINK ...
		$this->arr['jquery'] .= "\$('#{LINK-ID}').click(function(){ 
			\$('#searchtable1popup').modal();
			\$('#searchtable1popup').html('Loading...');
			var p = '".WEB_ROOT.INDEX_PAGE."/search/{METHOD}/{PARAMS}';
			\$.get(p,function(data){ 
				\$('#searchtable1popup').html(data);
				\$('#searchtable1').DataTable({ responsive: true, order: [[ 1, 'asc' ]] });\n
			});
			return false;
		}); \n";

		... and a Search Link: <a href="#{A-NAME}" id="{LINK-ID}" class="searchLink">Search</a>
		*/

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.

		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}
		else{ echo "You are not currently logged in!"; exit(); }
	}

	public function index($id=0){
		echo "Ooops. You didn't go anywhere!";
	}
	
	public function indust($domId=0){
		// General Industry Search Table
		// $domID = document.getElementById() element identifier, eg, document.getElementById('indust_origin').
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		$sql = "SELECT CONCAT('[',`ichange_indust`.`id`,'] ',`indust_name`) AS `key`, `report_mark` AS `RR`, 
			`indust_name` AS `Name`, `op_info` AS `Op Info`, `desc` AS `Description`, `town` AS `Town`, 
			`freight_in` AS `Receives`, `freight_out` AS `Sends` 
			FROM `ichange_indust` 
			LEFT JOIN `ichange_rr` ON `ichange_indust`.`rr` = `ichange_rr`.`id` 
			WHERE LENGTH(`ichange_rr`.`report_mark`) > 0";
		$this->res = (array)$this->Generic_model->qry($sql);
		$this->copyFld = "key";
		$this->domId = $domId;
		$this->generateTable();
		//echo "This is a pop up without table";
	}

	public function car($domId=0,$rr=0,$comm=0){
		// General Car Search Table
		// $domId = document.getElementById() element identifier, eg, document.getElementById('indust_origin').
		// $rr = Railroad ID to get cars for.
		// $comm = function to run after populating the $domId
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		$sql = "SELECT CONCAT(`ichange_cars`.`car_num`,',',`aar_type`,',',`report_mark`) AS `key`, `ichange_cars`.`car_num` AS `Car Number`, `aar_type` AS `AAR Type`, `desc` AS `Desc`, `location` AS `Location`, 
			IF(`ichange_cars`.`lading` = 'MT', '[EMPTY]', `ichange_cars`.`lading`) AS `Lading`, 
			IF(`ichange_waybill`.`waybill_num`,`ichange_waybill`.`waybill_num`,'') AS `In Waybill`
			FROM `ichange_cars` 
			LEFT JOIN `ichange_rr` ON `ichange_cars`.`rr` = `ichange_rr`.`id` 
			LEFT JOIN `ichange_waybill` ON `ichange_waybill`.`cars` LIKE CONCAT('%',`ichange_cars`.`car_num`,'%')
			WHERE `ichange_cars`.`rr` = '".$rr."'";
		$this->res = (array)$this->Generic_model->qry($sql);
		$this->copyFld = "key";
		$this->domId = $domId;
		if(strlen($comm) > 0){$this->comm = $comm; }
		$this->generateTable();
		//echo "This is a pop up without table";
	}
	
	public function waybillSummarySW($domId=0,$rr=0,$swID=0,$comm=0,$pass=0){
		// Waybill Summary table for searching and adding to switchlist
		// $domId = document.getElementById() element identifier, eg, document.getElementById('indust_origin').
		// $rr = Railroad ID to get waybills for.
		// $swID = switchlist ID (ie, trains.id field value the switchlist is for, for excluding WBs on that switchlist).
		// $comm = function to run after populating the $domId
		// $pass = whether to pass the value of the radio buttonm clicked to the command / function indicated in $comm. 0 = no, 1 = yes.
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		$sql = "SELECT `train_id` FROM `ichange_trains` WHERE `id` = '".$swID."'";
		$train = (array)$this->Generic_model->qry($sql);
		
		$sql = "SELECT `id` AS `key`, `waybill_num` AS `WB Number`, `status` AS `Status`, `indust_origin_name` AS `Origin`, 
			`indust_dest_name` AS `Destination`, `return_to` AS `Return To`, `train_id` AS `In Train`, 
			`lading` AS `Lading`, `routing` AS `Routing` 
			FROM `ichange_waybill` WHERE `rr_id_handling` = '".$rr."' AND `train_id` != '".@$train[0]->train_id."'";
		$this->res = (array)$this->Generic_model->qry($sql);
		$this->copyFld = "key";
		$this->domId = $domId;
		$this->comm = $comm; //echo "<br />comm = ".$this->comm;
		$this->pass = $pass;
		$this->mess = "<p>Waybill has been added to the switchlist for ".$train[0]->train_id.".</p><p>Close this dialog to continue.</p>";
		$this->generateTable();
	}

	public function carSummarySW($domId=0,$rr=0,$swID=0,$comm=0,$pass=0){
		// Car Summary table for searching and adding to switchlist
		// $domId = document.getElementById() element identifier, eg, document.getElementById('indust_origin').
		// $rr = Railroad ID to get waybills for.
		// $swID = switchlist ID (ie, trains.id field value the switchlist is for, for excluding WBs on that switchlist).
		// $comm = function to run after populating the $domId
		// $pass = whether to pass the value of the radio buttonm clicked to the command / function indicated in $comm. 0 = no, 1 = yes.
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		$sql = "SELECT `train_id` FROM `ichange_trains` WHERE `id` = '".$swID."'";
		$train = (array)$this->Generic_model->qry($sql);
		
		$sql = "SELECT `ichange_cars`.`id` AS `key`, `car_num` AS `Car Num`, `aar_type` AS `AAR Type`, `desc` AS `Description`, 
			`ichange_cars`.`location` AS `Location`, IF(LENGTH(`ichange_trains`.`train_id`) > 0,`ichange_trains`.`train_id`,'[Not Allocated') AS `In Train`, `lading` AS `Lading` 
			FROM `ichange_cars` 
			LEFT JOIN `ichange_tr_cars` ON `ichange_cars`.`id` = `ichange_tr_cars`.`cars_id` 
			LEFT JOIN `ichange_trains` ON `ichange_tr_cars`.`trains_id` = `ichange_trains`.`id` 
			WHERE `ichange_cars`.`rr` = '".$rr."' AND (`ichange_trains`.`id` IS NULL OR `ichange_trains`.`id` != '".$swID."')";
		$this->res = (array)$this->Generic_model->qry($sql);
		$this->copyFld = "key";
		$this->domId = $domId;
		$this->comm = $comm; //echo "<br />comm = ".$this->comm;
		$this->pass = $pass;
		$this->mess = "<p>Car has been added to the switchlist for ".$train[0]->train_id.".</p><p>Close this dialog to continue.</p>";
		$this->generateTable();
	}
	
	function generateTable(){
		$this->res_kys = array_keys((array)$this->res[0]);
		//echo "<pre>"; print_r($this->res_kys); print_r($this->res); echo "</pre>";
		$this->htm['thead'] = "";
		$this->htm['tbody'] = "";
		$this->htm['thead'] .= "<td class=\"td_title\">Select</td>";
		for($i=0;$i<count($this->res_kys);$i++){
			if($this->res_kys[$i] != $this->copyFld){
				$this->htm['thead'] .= "<td class=\"td_title\">".$this->res_kys[$i]."</td>";
			}
		}
		$comm = "";
		$pass = "";
		$mess = "<p>'+\$(this).val()+' has been copied to the form.</p><p>You can now close this dialog</p>";
		if(isset($this->domId) && $this->domId != '0'){ $comm .= "\$('#".$this->domId."').val(\$(this).val());"; }
		if(isset($this->pass) && $this->pass == 1){ $pass = "this.value"; }
		if(isset($this->comm) && $this->comm != '0'){ $comm .= $this->comm."(".$pass.");"; }
		if(isset($this->mess)){ $mess = $this->mess; }
		for($r=0;$r<count($this->res);$r++){
			$this->res[$r] = (array)$this->res[$r];
			$this->htm['tbody'] .= "<tr>";
			$this->htm['tbody'] .= "<td>
				<input type=\"radio\" name=\"selectItem\" class=\"selectItem\" value=\"".$this->res[$r][$this->copyFld]."\" onclick=\"".$comm."\$('.modal').html('".$mess."<a href=\'#\' rel=\'modal:close\'>Close</a>.');\" />
				</td>";
			for($t=0;$t<count($this->res_kys);$t++){
				if($this->res_kys[$t] != $this->copyFld){
					$this->htm['tbody'] .= "<td>".str_replace(",",", ",$this->res[$r][$this->res_kys[$t]])."</td>";
				}
			}
			$this->htm['tbody'] .= "</tr>";
		}
		echo "<div><p>To exit this Search view, press the ESC key on your keyboard, click the round 'X' button on the top right hand corner of this Search view, or click outside the white Search area.</p>
		<table id=\"searchtable1\" class=\"hover order-column\" style=\"font-size: 9pt; width: 90%;\">
			<thead>
				<tr>
					".$this->htm['thead']."
				</tr>
			</thead>
			<tbody>".$this->htm['tbody']."</tbody>
		</table>
		</div>";
		//echo "This is a pop up";
	}
}
