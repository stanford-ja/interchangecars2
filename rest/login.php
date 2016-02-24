<?php

class login {
	// Properties
	var $dbh;
	var $id;
	var $db_array;
	var $database;
	var $tbl = "ichange_rr";
	var $greeting;
	var $details=array();

	/* Constructor */
	function __construct(){
		/* Get DB Connection */
		// Should be try catch throw rest style 510 etc when completed
		$this->dbconnect();
	}

	function dbconnect(){
		include("db_connect.php");
		$this->dbh = mysql_connect($host,$user,$pass); //or throw new RestException(501, 'MySQL: Shat');
	}

	function basicsetup($id,$pword){
		$this->setdb();
		$this->id = $id;
		$this->pword = $pword;
	}

	function setdb(){
		$this->database = "jstan_general"; //$this->sites[$this->site]; // or throw...for now
		mysql_select_db($this->database) or die( "Unable to select database");
	}
	
	function index($id="",$pword=""){
		$this->basicsetup($id,$pword);
		$this->basicinfo();
		return $this->details;
	}
	
	function basicinfo(){	
		/*
		$sql = "SELECT `report_mark`,`id`,`owner_name`,`pw` FROM `".$this->tbl."` ORDER BY `report_mark`";
		$qry = mysql_query($sql);
		while($res = mysql_fetch_assoc($qry)){
			$this->details['list'][] = array('value'=>$res['id'],'label'=>$res['report_mark']." (".$res['owner_name'].")", 'pword' => $res['pw']);
		}
		*/
		$this->details['accessed'] = "Yes";

		$sql = "SELECT `pw` FROM `".$this->tbl."` WHERE `report_mark` = '".$this->id."' AND `pw` = '".$this->pword."'";
		$qry = mysql_query($sql);
		while($res = mysql_fetch_assoc($qry)){
			//$this->details['list'][0] = array('pword' => $res['pw']);
			$this->details['ok'] = 1;
		}
	}

	function __destruct(){
		@mysql_close($this->dbh);
	}

}

?>
