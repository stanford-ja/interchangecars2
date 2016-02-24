<?php
class db {
	var $dbh = ""; // Database connection instance
	
	function __construct(){
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
		return $this->dbh;	
	}
}
?>
