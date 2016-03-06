<?php
// MySQLi Wrapper CLass
class sqli {
	var $mysqli;
	var $qry = array();
	var $sql_line;
	var $obj; // Object name to put fetch_object into (eg, fetch_object("test")
	var $h; // Host name
	var $u; // Username
	var $p; // Password
	var $post; // $_POST array
	var $engine; // Database engine - 0 = mysqli, 1= mysql
	
	// Variables for qry_gen method set.
	var $pkey; // Is the field the primary key?
	var $skip; // Fields to ignore / skip in array.
	var $qrytype; // Query type: insert, update, delete
	var $condition; // Select condition
	var $id; // Record ID (primary key)
	var $tbl; // Data Table
	var $field_sql; // SQL fields portion of query
	
	// Error handling
	var $error;
	
	function setHost($host,$user,$pass){
		//if($_SERVER['SERVER_NAME'] == "testing"){$host = "localhost";}

		$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		// Test for path, act accordingly.
		if(strpos($LocTst,"www/Applications/") > 0){
			$this->h = 'localhost';
			$this->u = 'admin';
			$this->p = 'admin';
		}

		if(strlen($this->h) < 1){$this->h = $host;}
		if(strlen($this->u) < 1){$this->u = $user;}
		if(strlen($this->p) < 1){$this->p = $pass;}
	}
	
	//function sqli($host="db72d.pair.com",$user="jstan_6_w",$pass="Js120767"){
	function sqli($host="db150c.pair.com",$user="jstan2_2_w",$pass="Js120767"){
		//$this->errors();
		$this->setHost($host,$user,$pass);
		if(class_exists('mysqli')){$this->mysqli = new mysqli($this->h,$this->u,$this->p); $this->engine = 0;}
		else{$this->errors("The mysqli class does not exist on the server <strong>".$_SERVER['SERVER_NAME']."<br />Mysqli needs to be installed for this application to function."); echo $this->error; exit();}
		//else{$this->mysqli = new mysql($this->h,$this->u,$this->p); $this->engine = 1;}

	}
			
	function resultSet(){
		while($r = $this->qry->fetch_object($this->obj)){
			echo $r->display();
			echo "<hr />";
		}
	}
	
	function select_db($d){
		$this->resetError();
		$this->mysqli->select_db($d);
		if(strlen($this->mysqli->error) > 0){$this->errors("Error selecting database [".$d."]: ".$this->mysqli->error."<br />"); echo $this->error;}
	}
	
	function query($sql, $stor=0){
		$this->resetError();
		$this->qry = $this->mysqli->query($sql);
		if(strlen($this->mysqli->error) > 0){$this->errors("Error executing query: ".$this->mysqli->error."<br />Query: ".$sql."<br />"); echo $this->error;}
		//$this->qry = $this->mysqli->query($sql) or die("The specified query cannot be completed: ".$sql);
		//if($stor > 0){$this->mysqli->store_result();}
		return $this->qry;
	}
	
	function close(){
		$this->mysqli->close();
	}
	
	function resetError(){$this->error = "";}
	
	function fetch_assoc(){
		return $this->qry->fetch_assoc();
	}

	function fetch_array(){
		return $this->qry->fetch_array();
	}

	function fetch_row(){
		//return $this->mysqli->fetch_row($this->qry);
		return $this->qry->fetch_row();
	}
	
	function num_rows(){
		return $this->qry->num_rows;
	}
	
	function insert_id(){
		return $this->mysqli->insert_id;
	}
	
	function fetch_column_names($tbl){
		if(count($this->skip) < 1){$this->skip = array();}
		$sql = "SHOW COLUMNS FROM `".$tbl."`";
		$this->query($sql);
		$col_names = array();
		while($res = $this->fetch_assoc()){
			if(!in_array($res['Field'],$this->skip)){$col_names[] = $res['Field'];}
		}
		return $col_names;
	}

	function safe($value){
	   // no escaped strings needed for any server that uses magic_quotes! Like WDTC!
	   if(strpos($_SERVER['SERVER_NAME'],"wdtc") === true){
		   return stripslashes($value); //WDTC!
		   exit;
	   }	   
	   return $value;
	   
	} 
	
	function bld_arr($form_array){
		// loop thru $form_array adding POST value to multi array until arraysize
		$array_size = count($form_array);
		$actual_cnt = 0;
		for($i=0;$i<$array_size;$i++){
			$fieldname=$form_array[$i];
			$data=$this->safe($this->post[$fieldname]);
			if($fieldname != $this->pkey){
				$actual_cnt++;
				if($actual_cnt > 1){ $sql .= ", ";}
				$sql.=" `".$form_array[$i]."` = '".$data."'";
			}
		}	
		return $sql;
	}

	function qry_gen(){
		// primary use of method is to update fields from post array in $this->post
		$field_names = $this->fetch_column_names($this->tbl);
		$this->field_sql=$this->bld_arr($field_names);
		$query = $this->qry_type();
		return $query;
	}
	
	function qry_type(){
		switch($this->qrytype){
			case "update":
				$query = $this->update();
				break;
			case "insert":
				$query = $this->insert();
				break;
			case "select":
				$query = $this->select();
				break;
			case "delete":
				$query="DELETE FROM `".$tbl."` WHERE `".$this->pkey."`='".$this->id."' LIMIT 1";
				break;
		}
		return $query;
	}
	
	function insert(){
		$base="INSERT INTO `".$this->tbl."` SET ";
		$query=$base.$this->field_sql;
		return $query;
	}
	
	function update(){
		$base="UPDATE `".$this->tbl."` SET ";
		$where=" WHERE `".$this->pkey."`='".$this->id."'";
		$query=$base.$this->field_sql.$where;
		return $query;
	}

	function select(){
		$base="SELECT * FROM `".$this->tbl."` WHERE ";
		if($this->condition != ""){ $where = $this->condition; }
		$query=$base.$where;
		return $query;
	}
	
	function delete(){
		$query="DELETE FROM `".$this->tbl."` WHERE `".$this->pkey."`='".$this->id."' LIMIT 1";
		return $query;
	}
	
	// Error handling method
	function errors($e){
		$this->error = "<html><head></head>";
		$this->error .= "<body style=\"font-size: 15pt; font-weight: bold; padding: 15%; background-color: black; color: yellow; font-family: Tahoma;\">";
		$this->error .= "<h2 style=\"color: red;\">An error has occured in: sqliWrapper</h2>";
		$this->error .= $e."</body>";
		$this->error .= "</html>";
		//if($_SESSION['userlevel'] == 99){echo $this->error; exit();}
	}
}

class dataInstance {
	
	function __construct(){
		//$this->setVar($this->allocated);
		//$this->setVars($this->flds);
		//$this->calcHold();
	}
	
	function display(){
		$output="field1: ".$this->c_field1."<br />";
		$output.="field2: ".$this->c_field2."<br />";
		//for($i=0:$i<count($this->flds);$i++){$output .= ", ".$this->flds[$i];}
		return $output;
	}
	
	/*
	function calcHold(){
		$hold=$this->duration-$this->billsec;
		if($hold < 0){ $hold=0; }
		$this->hold=$hold;
	}
	
	function setVar($cma_id){
		$this->cma_id=$cma_id;
	}
	*/
	
}
?>
