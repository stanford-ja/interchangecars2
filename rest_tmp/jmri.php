<?php

class jmri {
	// Properties
	var $dbh;
	var $id;
	var $db_array;
	var $database;
	var $tbl = "ichange_waybill";
	var $greeting;
	var $details=array();

	/* Constructor */
	function __construct(){
		/* Get DB Connection */
		// Should be try catch throw rest style 510 etc when completed
		$this->dbconnect();
		
		// dummy data until validation chain built...
		//$this->siite = '1';
		//$this->cma_id = '205';
		//$this->details[cma_id]='205';
		
		// obvious wrapping required... subclassing or outclassing is the obvious choice...later
	//	$this->setdb();
		// dummy sql testing
		//$this->sqltest();

		// Setup Basic Client Info
		//$this->basicinfo();
	}

	function dbconnect(){
		include("db_connect.php");
		$this->dbh = mysql_connect($host,$user,$pass); //or throw new RestException(501, 'MySQL: Shat');
	}

	function basicsetup($id){
		$this->setdb();
		$this->id = $id;
	}

	function setdb(){
		$this->database = "jstan_general"; //$this->sites[$this->site]; // or throw...for now
		mysql_select_db($this->database) or die( "Unable to select database");
	}
	
	function index($id=0){
		//if($id!=0){
			$this->basicsetup($id);
			$this->basicinfo();
			return $this->details;
		//}else{ return false; }
	}
	
	function basicinfo(){
		// Do query for waybill data
		$sql="SELECT `waybill_num`, `cars`, `progress`  
			FROM `ichange_waybill` WHERE `rr_id_handling`='".$this->id."' AND `status` != 'CLOSED'";
		$qry=mysql_query($sql,$this->dbh);
		$this->details = array();
		$cntr=0;
		while($row=mysql_fetch_assoc($qry)){ //mysql_fetch_array($res);
			$this->details['waybills'][$cntr] = $row;
			$this->details['waybills'][$cntr]["id"] = $this->id;
		
			// Convert JSON arrays to PHP arrays
			$this->details['waybills'][$cntr]['progress'] = @json_decode($this->details['waybills'][$cntr]['progress'],true);
			$this->details['waybills'][$cntr]['progress'] = $this->details['waybills'][$cntr]['progress'][count($this->details['waybills'][$cntr]['progress'])-1];
			$this->details['waybills'][$cntr]['map_location'] = $this->details['waybills'][$cntr]['progress']['map_location'];
			$this->details['waybills'][$cntr]['cars'] = @json_decode($this->details['waybills'][$cntr]['cars'],true);
		
			// Put all cars for railroad into an array caller mycars
			$this->details['waybills'][$cntr]['mycars'] = array();
			$this->details['waybills'][$cntr]['no_cars'] = 0;
			for($i=0;$i<count($this->details['waybills'][$cntr]['cars']);$i++){
				//$this->details['mycars'][] = $this->details['cars'][$i]['RR'];
				if($this->details['waybills'][$cntr]['cars'][$i]['RR'] == $this->id && $this->details['waybills'][$cntr]['cars'][$i]['NUM'] != ''){
					$this->details['waybills'][$cntr]['mycars'][] = $this->details['waybills'][$cntr]['cars'][$i];
					$this->details['waybills'][$cntr]['no_cars']++;
				}
			}
			unset($this->details['waybills'][$cntr]['cars']);
			$cntr++;
		}
	}

	function __destruct(){
		@mysql_close($this->dbh);
	}

	function post($request_data=NULL){
		// new record
		/* NOT NEEDED AS ALWAYS USES EXISTING WAYBILLS
		$this->setdb();
		$fld_names = $this->fld_names($request_data);
		$fld_vals = $this->fld_vals($request_data);
		$sql = "INSERT INTO `".$this->tbl."` (".$fld_names.") VALUES (".$fld_vals.")";
		mysql_query($sql,$this->dbh) or die("Error: ".mysql_error());
		//return $this->dbh->insert($this->_validate($request_data),$this->tbl);
		return $request_data; //$request_data; //mysql_query($sql,$this->dbh);
		*/
	}
	
	function put($id=NULL, $request_data=NULL){
		// update record
		$this->setdb();
		$sq = "SELECT `rr_id_handling`,`progress` FROM `ichange_waybill` WHERE `waybill_num` = '".$id."'";
		$qr = mysql_query($sq);
		$re = mysql_fetch_assoc($qr);
		$json_prog = @json_decode($re['progress'],true);
		//$fldValPairs = $this->fldValPairs($request_data);
		if(isset($request_data['map_location'])){
			$json_prog[] = array(
				'date' => date('Y-m-d'), 
				'time' => date('H:i'), 
				'text' => strtoupper($request_data['text'])." (JMRI)", 
				'waybill_num' => $id, 
				'map_location' => strtoupper($request_data['map_location']), 
				'status' => strtoupper($request_data['status']), 
				'train' => strtoupper($request_data['train']), 
				'rr' => $re['rr_id_handling'], 
				'exit_location' => ""
			);
		}
		$sql = "UPDATE `".$this->tbl."` SET `progress` = '".$json_prog."' WHERE `waybill_num` = '".$id."'";
		mysql_query($sql,$this->dbh) or die("Error: ".mysql_error());
		//return $this->dbh->update($id, $this->_validate($request_data),$this->tbl);
		return $request_data;
	}

	function delete($id=NULL) {
		// delete record
		return $this->dbh->delete($id,$this->tbl);
	}

	function fld_names($rec){
		//$rec = json_decode($recJSON,true);
		$fld_names = "";
		$fld_kys = array_keys($rec);
		for($i=0;$i<count($fld_kys);$i++){
			if($i>0){$fld_names .= ", ";}
			$fld_names .= "`".$fld_kys[$i]."`";
		}
		return $fld_names;
	}
	
	function fld_vals($rec){
		//$rec = json_decode($recJSON,true);
		$fld_vals = "";
		$fld_kys = array_keys($rec);
		for($i=0;$i<count($fld_kys);$i++){
			if($i>0){$fld_vals .= ", ";}
			$fld_vals .= "'".$this->escape($rec[$fld_kys[$i]])."'";
		}
		return $fld_vals;
	}
	
	function fldValPairs($rec){
		$fld_pairs = "";
		$fld_kys = array_keys($rec);
		for($i=0;$i<count($fld_kys);$i++){
			if($i>0){$fld_pairs .= ", ";}
			$fld_pairs .= "`".$fld_kys[$i]."` = '".$this->escape($rec[$fld_kys[$i]])."'";
		}
		return $fld_pairs;
	}
	
	function escape($str){
		return mysql_escape_string($str);
	}
}

?>
