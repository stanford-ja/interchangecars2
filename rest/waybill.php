<?php

class waybill {
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

	function basicsetup($id,$rr){
		$this->setdb();
		$this->id = $id;
		$this->rr = $rr;
	}

	function setdb(){
		$this->database = "jstan_general"; //$this->sites[$this->site]; // or throw...for now
		mysql_select_db($this->database) or die( "Unable to select database");
	}
	
	function index($id=0,$rr=""){
		//if($id!=0){
			/* Basic CMAID / SITE / DB SELECTION */
			$this->basicsetup($id,$rr);
			/* Basic Client Information Details Array */
			$this->basicinfo();
			/* return base client details */
			return $this->details;
			//return $this->greeting;
		//}else{ return false; }
	}
	
	function basicinfo(){
		$sql="SELECT `date`,`rr_id_from`,`rr_id_to`,`rr_id_handling`,`indust_origin_name`,
			`indust_dest_name`,`return_to`,`routing`,`status`,`waybill_num`,`lading`,
			`train_id`,`waybill_type`,`notes`, `progress`  
			FROM `".$this->tbl."` WHERE `waybill_num`='".$this->id."'";
		$qry=mysql_query($sql,$this->dbh);
		$row=mysql_fetch_assoc($qry);//mysql_fetch_array($res);
		$this->details = $row;
		$this->details['id'] = $this->id;
		$this->details['progress'] = "";
		$prog_arr = @json_decode($row['progress'],TRUE);
		for($ja=0;$ja<count($prog_arr);$ja++){
			$this->details['progress'] .= $prog_arr[$ja]['date']." ".$prog_arr[$ja]['time']." - <strong>".$prog_arr[$ja]['status']." - ".$prog_arr[$ja]['map_location']."</strong> - ".$prog_arr[$ja]['text']."<hr />";
		}
		
		$sql = "SELECT * FROM `".$this->tbl."` WHERE `status` != 'CLOSED' AND `routing` LIKE '%".$this->rr."%' ORDER BY `date`";
		$qry = mysql_query($sql);
		while($res = mysql_fetch_assoc($qry)){
			$this->details['list'][] = $res; //array('value'=>$res['waybill_num'],'label'=>$res['waybill_num']);
		}
		$this->details['list_fields'] = array("date","waybill_num","status","lading","indust_origin_name","indust_dest_name","routing");
		$this->details['list_headings'] = array("Date","Waybill No.","Status","Lading","Origin","Destination","Routing");
		$this->details['id_key'] = "waybill_num";
		$this->details['title'] = "Waybills for ".@$this->rr;
		if($this->id > 0){$this->details['title'] = "Waybill Info: ".@$this->id;}
	}

	function __destruct(){
		@mysql_close($this->dbh);
	}

	/*
	function get($id) {
		return $this->dbh->get($id,$this->tbl);
	}
	*/

	function post($request_data=NULL){
		// new record
		$this->setdb();
		$fld_names = $this->fld_names($request_data);
		$fld_vals = $this->fld_vals($request_data);
		$sql = "INSERT INTO `".$this->tbl."` (".$fld_names.") VALUES (".$fld_vals.")";
		mysql_query($sql,$this->dbh) or die("Error: ".mysql_error());
		//return $this->dbh->insert($this->_validate($request_data),$this->tbl);
		return $request_data; //$request_data; //mysql_query($sql,$this->dbh);
	}
	
	function put($id=NULL, $request_data=NULL){
		// update record
		$this->setdb();
		$fldValPairs = $this->fldValPairs($request_data);
		$sql = "UPDATE `".$this->tbl."` SET ".$fldValPairs." WHERE `waybill_num` = '".$id."'";
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
	
	function prog($p){
		
	}
}

?>
