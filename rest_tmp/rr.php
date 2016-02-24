<?php

class rr {
	// Properties
	var $dbh;
	var $id;
	var $db_array;
	var $database;
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
		$this->dbh = mysql_connect($host,$user,$pass) or die("Cannot connect to MySQL server"); //or throw new RestException(501, 'MySQL: Shat');
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
			/* Basic CMAID / SITE / DB SELECTION */
			$this->basicsetup($id);
			/* Basic Client Information Details Array */
			$this->basicinfo();
			/* return base client details */
			return $this->details;
			//return $this->greeting;
		//}else{ return false; }
	}
	
	function basicinfo(){
		$sql="SELECT `report_mark`,`rr_name`,`rr_desc`,`owner_name`,`interchanges`
			`affiliates`,`website`,`social` FROM `ichange_rr` WHERE `id`='".$this->id."'";
		$qry=mysql_query($sql,$this->dbh);
		$row= mysql_fetch_assoc($qry);//mysql_fetch_array($res);
		$this->details = $row;
		$this->details["id"] = $this->id;
		$this->details['pw'] = "[PRIVATE]";

		$sql = "SELECT * FROM `ichange_rr` ORDER BY `report_mark`";
		$qry = mysql_query($sql);
		while($res = mysql_fetch_assoc($qry)){
			$this->details['list'][] = array('value'=>$res['id'],'label'=>$res['report_mark']);
		}
	}

	function __destruct(){
		@mysql_close($this->dbh);
	}

}

?>
