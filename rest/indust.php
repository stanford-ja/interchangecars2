<?php

class indust {
	// Properties
	var $dbh;
	var $id;
	var $db_array;
	var $database;
	var $tbl = "ichange_indust";
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
		// Get railroad id from rep mark
		$rr_id = 0;
		$rr_sql = "SELECT `id` FROM `ichange_rr` WHERE `report_mark` = '".strtoupper($this->rr)."'";
		$rr_qry = mysql_query($rr_sql);
		while($rr_res = mysql_fetch_array($rr_qry)){
			$rr_id = $rr_res['id'];
		}

		// Get industry info
		$sql="SELECT `indust_name`, `desc`, `rr`, `freight_in`, `freight_out`, `op_info` 
			FROM `".$this->tbl."` WHERE `id`='".$this->id."' AND `rr` = '".$rr_id."'";
		$qry=mysql_query($sql,$this->dbh);
		$row=mysql_fetch_assoc($qry);//mysql_fetch_array($res);
		$this->details = $row;
		$this->details["id"] = $this->id;

		$sql = "SELECT * FROM `".$this->tbl."` WHERE `rr` = '".$rr_id."'";
		$qry = mysql_query($sql);
		while($res = mysql_fetch_assoc($qry)){
			$this->details['list'][] = $res;
		}
		$this->details['list_fields'] = array("indust_name","desc","freight_in","freight_out");
		$this->details['list_headings'] = array("Name","Description","Freight In","Freight Out");
		$this->details['id_key'] = "id";
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
		$sql = "UPDATE `".$this->tbl."` SET ".$fldValPairs." WHERE `id` = '".$id."'";
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
