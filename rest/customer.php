<?php

class customer {
	// Properties
	var $dbh;
	var $cma_id;
	var $cust_id;
	var $site;
	var $sites=array('','cma_nowra','cma_international','cma_melbourne');
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
		$host = "10.19.21.88";
		$user = "root";
		$passw = "A110uRdat4";
		$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		if(strpos($LocTst,"/www/html/welldone/") > 0){
			$host = "localhost";
			$user = "admin";
			$pass = "admin";
		}
		$this->dbh = mysql_connect($host,$user,$pass); //or throw new RestException(501, 'MySQL: Shat');
	}

	function basicsetup($cmaid){
		$this->setsite($cmaid);
		$this->setdb();
		$this->cma_id = $cmaid;
	}

	function setsite($cmaid=0){
		if($cmaid!=0){
			if(strlen($cmaid)=='3'){ $this->site = '1'; }  // 3 digit cma id = nowra site
			else{ $this->site=substr($cmaid,0,1); }
		}
	}

	function setdb(){
		$this->database = $this->sites[$this->site]; // or throw...for now
		mysql_select_db($this->database) or die( "Unable to select database");
	}
	
	function index($cmaid=0){
		if($cmaid!=0){
			/* Basic CMAID / SITE / DB SELECTION */
			$this->basicsetup($cmaid);
			/* Basic Client Information Details Array */
			$this->basicinfo();
			$this->greeting();
			/* return base client details */
			return $this->details;
			//return $this->greeting;
		}else{ return false; }
	}
	
	
	function getfff(){
	
	}

	function siteselect($cma_id=0){
	// 2BC
	}

	function basicinfo(){
		$sql="SELECT `CustID`,`Company`,`DisplayName` FROM `customers` WHERE `Deleted`='0' AND `DIDExt`=".$this->cma_id." LIMIT 1";
		$res=mysql_query($sql,$this->dbh);
		$row= mysql_fetch_assoc($res);//mysql_fetch_array($res);
		$this->details = $row;
		$this->details["cma_id"] = $this->cma_id;
		$this->details["cust_id"] = $row['CustID'];
		$this->details["site"] = $this->site;
	}

	function sqltest(){
	//	mysql_select_db($this->sites['1']) or die( "Unable to select database");
		//$sql="SELECT `CustID`,`DIDExt`,`Company`,`DisplayName` FROM `customers` WHERE `Deleted`='0' AND `DIDExt`=".$this->cma_id." LIMIT 1";
		$sql="SELECT * FROM `customers` WHERE `Deleted`='0' AND `DIDExt`=".$this->cma_id." LIMIT 1";
		$result=mysql_query($sql,$this->dbh);	
		$this->db_array = mysql_fetch_assoc($result);
		echo json_encode($this->db_array)."<br />";
	}


	function __destruct(){
		@mysql_close($this->dbh);
	}

	/***** GREETING CAN BE F***KED --- LEAVE FOR NOW HOWEVER COOL *****/ 
	/** ROUGH AND READY GET GREETING / SET GREETING TEST no subclassing **/
	function greeting(){
		//$sql="SELECT `Greeting` FROM `customers` WHERE `CustID`='".$this->cust_id."' LIMIT 1";
		$sql="SELECT `Greeting` FROM `customers` WHERE `Deleted` = '0' AND `DIDExt`='".$this->cma_id."' LIMIT 1";
		//echo $sql."<br />";
		$result=mysql_query($sql,$this->dbh);
		$greeting = mysql_fetch_assoc($result);
		// wrapping required... or just return natural or object?
		//$this->greeting = json_encode($greeting['Greeting']);// json_encode(mysql_escape_string($greeting['Greeting']));
		$this->details['greeting'] = json_encode($greeting['Greeting']);
		return $this->greeting;
	}

}

?>
