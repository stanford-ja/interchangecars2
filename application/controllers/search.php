<?php
class Search extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!
		/* REQUIRES SOMETHING LIKE THIS IN VIEW'S CONTROLLER METHOD TO SET searchtable1 FOR DISPLAY FOR EACH LINK ...
		$this->arr['jquery'] .= "\$('#{LINK-ID}').click(function(){ 
			var p1 = '".WEB_ROOT.INDEX_PAGE."/search/{METHOD}/{PARAMS}';
			\$.get(p1,function(data1){ 
				\$('#searchtable1popup').html(data1);
				\$('#searchtable1popup').modal();
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
	
	public function indust($domId=""){
		// $domID = document.getElementById() element name, eg, document.getElementById('indust_origin').
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		$sql = "SELECT CONCAT('[',`ichange_indust`.`id`,'] ',`indust_name`) AS `key`, `report_mark` AS `RR`, `indust_name` AS `Name`, `desc` AS `Description`, `town` AS `Town`, `freight_in` AS `Receives`, `freight_out` AS `Sends` 
			FROM `ichange_indust` 
			LEFT JOIN `ichange_rr` ON `ichange_indust`.`rr` = `ichange_rr`.`id` 
			WHERE LENGTH(`ichange_rr`.`report_mark`) > 0";
		$this->res = (array)$this->Generic_model->qry($sql);
		$this->copyFld = "key";
		$this->domId = $domId;
		$this->generateTable();
		//echo "This is a pop up without table";
	}

	public function car($domId="",$rr=0,$comm=""){
		// $domId = document.getElementById() element name, eg, document.getElementById('indust_origin').
		// `key` = field is the value to return when Select link is clicked (is ignored when creating table columns).
		// $comm = function to run after populating the $domId
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
		//$this->modalId = $modalId;
		$this->generateTable();
		//echo "This is a pop up without table";
	}
	
	function generateTable(){
		$this->res_kys = array_keys((array)$this->res[0]);
		//echo "<pre>"; print_r($this->res_kys); print_r($this->res); echo "</pre>";
		$this->htm['thead'] = "";
		$this->htm['tbody'] = "";
		$this->htm['thead'] .= "<td class=\"td_title\">Actions</td>";
		for($i=0;$i<count($this->res_kys);$i++){
			if($this->res_kys[$i] != $this->copyFld){
				$this->htm['thead'] .= "<td class=\"td_title\">".$this->res_kys[$i]."</td>";
			}
		}
		$comm = "\$('#".$this->domId."').val(\$(this).val());";
		if(isset($this->comm)){ $comm .= $this->comm."();"; }
		for($r=0;$r<count($this->res);$r++){
			$this->res[$r] = (array)$this->res[$r];
			$this->htm['tbody'] .= "<tr>";
			$this->htm['tbody'] .= "<td>
				<input type=\"radio\" name=\"selectItem\" class=\"selectItem\" value=\"".$this->res[$r][$this->copyFld]."\" onclick=\"".$comm." \$('.modal').html('<p>'+\$(this).val()+' has been copied to the form.</p><p>You can now close this dialog</p><a href=\'#\' rel=\'modal:close\'>Close</a>.');\" />
				</td>";
			for($t=0;$t<count($this->res_kys);$t++){
				if($this->res_kys[$t] != $this->copyFld){
					$this->htm['tbody'] .= "<td>".str_replace(",",", ",$this->res[$r][$this->res_kys[$t]])."</td>";
				}
			}
			$this->htm['tbody'] .= "</tr>";
		}
		echo "<div>
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
