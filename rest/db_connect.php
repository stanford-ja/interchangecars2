<?php
		$host = "db72d.pair.com";
		$user = "jstan_6_w";
		$pass = "Js120767";
		$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
		if(strpos($LocTst,"/www/html") > 0){
			$host = "localhost";
			$user = "admin";
			$pass = "admin";
		}
?>